<?php

namespace App\Http\Controllers;

use App\Models\AntreanMesin;
use App\Models\BerkasDokumen;
use App\Models\DetailPesanan;
use App\Models\InventarisStok;
use App\Models\LaporanOmzet;
use App\Models\LogKlaimHadiah;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\ProdukLayanan;
use App\Models\ProgramLoyalitas;
use App\Models\RiwayatBahanMasuk;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    /**
     * Display the Admin & Cashier dashboard.
     */
    public function admin(): View
    {
        $user = Auth::user();
        $user->load('adminKasir');

        $stats = $this->calculateDashboardStats();

        $activeQueues = AntreanMesin::with(['pesanan.pelanggan.user', 'pesanan.detailPesanan.produkLayanan'])
            ->where('status_antrean', '!=', 'selesai')
            ->orderBy('nomor_tiket', 'asc')
            ->take(10)
            ->get();

        $inventoryAlerts = InventarisStok::whereColumn('jumlah_stok', '<=', 'batas_minimum')->get();
        $inventoryItems = InventarisStok::orderBy('nama_bahan', 'asc')->get();
        $products = ProdukLayanan::where('status_aktif', true)->get();

        return view('admin.dashboard', compact(
            'user',
            'stats',
            'activeQueues',
            'inventoryAlerts',
            'inventoryItems',
            'products'
        ));
    }

    /**
     * Display the Machine & Workshop Queue Management page.
     */
    public function antreanIndex(Request $request): View
    {
        $user = Auth::user();
        $user->load('adminKasir');

        $filter = $request->query('tab', 'semua');
        $search = $request->query('q', '');

        $workshopData = $this->calculateWorkshopStats();

        $query = AntreanMesin::with([
            'pesanan.pelanggan.user',
            'pesanan.detailPesanan.produkLayanan',
            'pesanan.detailPesanan.berkasDokumen',
        ])->where('status_antrean', '!=', 'selesai');

        if ($filter === 'diterima') {
            $query->whereIn('status_alur', ['Diterima', 'diterima', 'menunggu']);
        } elseif ($filter === 'proses') {
            $query->whereIn('status_alur', ['Sedang Cetak', 'Sedang Jilid', 'Menunggu Bahan', 'cetak', 'salin', 'finishing']);
        } elseif ($filter === 'siap') {
            $query->whereIn('status_alur', ['Siap Diambil', 'siap', 'siap_pickup']);
        }

        if (! empty($search)) {
            $query->whereHas('pesanan', function ($q) use ($search) {
                $q->where('nama_pemesan', 'like', "%{$search}%")
                    ->orWhere('judul_dokumen', 'like', "%{$search}%");
            });
        }

        $antreanList = $query->orderBy('nomor_tiket', 'asc')->paginate(5)->withQueryString();

        return view('admin.antrean', compact(
            'user',
            'workshopData',
            'antreanList',
            'filter',
            'search'
        ));
    }

    /**
     * Provide real-time live data for dashboard AJAX auto-refresh.
     */
    public function liveData(): JsonResponse
    {
        $stats = $this->calculateDashboardStats();

        $activeQueues = AntreanMesin::with(['pesanan.pelanggan.user', 'pesanan.detailPesanan.produkLayanan'])
            ->where('status_antrean', '!=', 'selesai')
            ->orderBy('nomor_tiket', 'asc')
            ->take(10)
            ->get()
            ->map(function ($queue) {
                return [
                    'antrean_id' => $queue->antrean_id,
                    'nomor_tiket' => $queue->nomor_tiket,
                    'kode_antrean' => $queue->kode_antrean,
                    'status_antrean' => $queue->status_antrean,
                    'status_label' => $queue->status_label,
                    'status_color' => $queue->status_color,
                    'status_alur' => $queue->status_alur ?? 'Diterima',
                    'nama_mesin' => $queue->nama_mesin ?? 'Mesin Konica 1',
                    'nama_operator' => $queue->nama_operator ?? 'Feri',
                    'estimasi_waktu' => $queue->estimasi_waktu,
                    'nomor_nota' => $queue->pesanan?->nomor_nota ?? '#MF-'.str_pad((string) $queue->pesanan_id, 3, '0', STR_PAD_LEFT),
                    'nama_pelanggan' => $queue->pesanan?->display_name ?? 'Pelanggan Walk-in',
                    'tipe_pelanggan' => $queue->pesanan?->display_tipe ?? 'Walk-in • Reguler',
                    'judul_dokumen' => $queue->pesanan?->display_judul ?? 'Dokumen Fotokopi',
                    'spesifikasi' => $queue->pesanan?->display_spesifikasi ?? 'A4 70g • Standard Print',
                    'tenggat' => $queue->pesanan?->display_tenggat ?? ($queue->created_at->addMinutes(30)->format('H:i').' WIB'),
                ];
            });

        $inventoryAlerts = InventarisStok::whereColumn('jumlah_stok', '<=', 'batas_minimum')->get();

        return response()->json([
            'status' => 'success',
            'timestamp' => now()->toIso8601String(),
            'stats' => $stats,
            'activeQueues' => $activeQueues,
            'inventoryAlerts' => $inventoryAlerts,
        ]);
    }

    /**
     * Provide real-time data for Antrean Mesin view.
     */
    public function antreanLiveData(): JsonResponse
    {
        $workshopData = $this->calculateWorkshopStats();

        $queues = AntreanMesin::with([
            'pesanan.pelanggan.user',
            'pesanan.detailPesanan.produkLayanan',
            'pesanan.detailPesanan.berkasDokumen',
        ])
            ->where('status_antrean', '!=', 'selesai')
            ->orderBy('nomor_tiket', 'asc')
            ->take(20)
            ->get()
            ->map(function ($queue) {
                $berkas = $queue->pesanan?->detailPesanan?->firstWhere('berkas_id', '!=', null)?->berkasDokumen;

                return [
                    'antrean_id' => $queue->antrean_id,
                    'pesanan_id' => $queue->pesanan_id,
                    'berkas_id' => $berkas?->berkas_id,
                    'download_url' => $berkas ? route('admin.berkas.download', $berkas->berkas_id) : null,
                    'nomor_tiket' => $queue->nomor_tiket,
                    'kode_antrean' => $queue->kode_antrean,
                    'nomor_nota' => $queue->pesanan?->nomor_nota ?? '#MF-'.str_pad((string) $queue->pesanan_id, 3, '0', STR_PAD_LEFT),
                    'nama_pelanggan' => $queue->pesanan?->display_name ?? 'Pelanggan',
                    'tipe_pelanggan' => $queue->pesanan?->display_tipe ?? 'Walk-in',
                    'judul_dokumen' => $queue->pesanan?->display_judul ?? 'Dokumen Cetak',
                    'spesifikasi' => $queue->pesanan?->display_spesifikasi ?? '',
                    'nama_mesin' => $queue->nama_mesin ?? 'Mesin Konica 1',
                    'nama_operator' => $queue->nama_operator ?? 'Feri',
                    'tenggat' => $queue->pesanan?->display_tenggat ?? '15:30 WIB',
                    'keterangan_tenggat' => $queue->keterangan_tenggat ?? 'Sesuai Jadwal',
                    'status_pembayaran' => $queue->status_pembayaran ?? 'lunas',
                    'metode_pembayaran' => strtoupper($queue->pesanan?->metode_pembayaran ?? 'QRIS'),
                    'total_harga' => $queue->pesanan?->total_harga ?? 0,
                    'status_alur' => $queue->status_alur ?? 'Diterima',
                    'badge_alur' => $queue->badge_alur_color,
                ];
            });

        return response()->json([
            'status' => 'success',
            'timestamp' => now()->toIso8601String(),
            'workshopData' => $workshopData,
            'queues' => $queues,
        ]);
    }

    /**
     * Store walk-in order created by cashier.
     */
    public function storeWalkInOrder(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'nama_pemesan' => 'required|string|max:150',
            'tipe_pelanggan' => 'nullable|string|max:50',
            'judul_dokumen' => 'required|string|max:200',
            'spesifikasi' => 'required|string',
            'produk_id' => 'nullable|exists:produk_layanan,produk_id',
            'kuantitas' => 'nullable|integer|min:1',
            'total_harga' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|string|in:qris,tunai,transfer',
            'status_antrean' => 'required|string|in:cetak,salin,menunggu,finishing,siap',
            'estimasi_waktu' => 'nullable|integer|min:0',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $pesanan = Pesanan::create([
                'nama_pemesan' => $validated['nama_pemesan'],
                'tipe_pelanggan' => $validated['tipe_pelanggan'] ?? 'Walk-in • Reguler',
                'judul_dokumen' => $validated['judul_dokumen'],
                'spesifikasi' => $validated['spesifikasi'],
                'tenggat_waktu' => now()->addMinutes($validated['estimasi_waktu'] ?? 30)->format('H:i').' WIB',
                'jenis_pesanan' => 'walk-in',
                'total_harga' => $validated['total_harga'],
                'status_pesanan' => 'proses',
                'metode_pembayaran' => $validated['metode_pembayaran'],
                'waktu_pesan' => now(),
            ]);

            $produkId = $validated['produk_id'] ?? ProdukLayanan::first()?->produk_id ?? 1;
            $kuantitas = $validated['kuantitas'] ?? 1;

            DetailPesanan::create([
                'pesanan_id' => $pesanan->pesanan_id,
                'produk_id' => $produkId,
                'kuantitas' => $kuantitas,
                'sub_total' => $validated['total_harga'],
            ]);

            $nextTiket = (AntreanMesin::whereDate('created_at', Carbon::today())->max('nomor_tiket') ?? 0) + 1;

            $statusAlurMap = [
                'cetak' => 'Sedang Cetak',
                'salin' => 'Sedang Cetak',
                'menunggu' => 'Diterima',
                'finishing' => 'Sedang Jilid',
                'siap' => 'Siap Diambil',
            ];

            $namaMesin = match ($validated['status_antrean']) {
                'cetak' => 'Mesin Konica 1',
                'salin' => 'Mesin Ricoh 2',
                'finishing' => 'Workshop Finis',
                'siap' => 'Meja Kasir A',
                default => 'Mesin Konica 1',
            };

            $antrean = AntreanMesin::create([
                'pesanan_id' => $pesanan->pesanan_id,
                'nomor_tiket' => $nextTiket,
                'status_antrean' => $validated['status_antrean'],
                'estimasi_waktu' => $validated['estimasi_waktu'] ?? 15,
                'nama_mesin' => $namaMesin,
                'nama_operator' => Auth::user()?->nama ?? 'Feri',
                'status_alur' => $statusAlurMap[$validated['status_antrean']] ?? 'Diterima',
                'status_pembayaran' => 'lunas',
                'keterangan_tenggat' => 'Sesuai Jadwal',
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Pesanan Walk-in berhasil dicatat dan masuk antrean.',
                    'pesanan' => $pesanan,
                    'antrean' => $antrean,
                ]);
            }

            return redirect()->back()
                ->with('success', 'Pesanan Walk-in #'.$antrean->kode_antrean.' berhasil ditambahkan ke antrean.');
        });
    }

    /**
     * Update active queue status (KF-13 & KF-15 integrated).
     */
    public function updateQueueStatus(Request $request, AntreanMesin $antrean): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|string|in:cetak,salin,menunggu,finishing,siap,selesai',
        ]);

        return DB::transaction(function () use ($validated, $request, $antrean) {
            $statusAlurMap = [
                'cetak' => 'Sedang Cetak',
                'salin' => 'Sedang Cetak',
                'menunggu' => 'Diterima',
                'finishing' => 'Sedang Jilid',
                'siap' => 'Siap Diambil',
                'selesai' => 'Selesai',
            ];

            $newStatusAlur = $statusAlurMap[$validated['status']] ?? 'Sedang Cetak';
            $pesanan = $antrean->pesanan;

            $wasAlreadyCompleted = in_array($antrean->status_alur, ['Siap Diambil', 'Selesai'], true)
                || in_array($antrean->status_antrean, ['siap', 'selesai'], true)
                || $pesanan?->status_pesanan === 'selesai';

            $updatePayload = [
                'status_antrean' => $validated['status'],
                'status_alur' => $newStatusAlur,
            ];

            if (in_array($validated['status'], ['siap', 'selesai'], true)) {
                $updatePayload['status_pembayaran'] = 'lunas';
            }

            $antrean->update($updatePayload);

            if ($validated['status'] === 'selesai' && $pesanan) {
                $pesanan->update([
                    'status_pesanan' => 'selesai',
                ]);
            }

            if (! $wasAlreadyCompleted && $pesanan && in_array($newStatusAlur, ['Siap Diambil', 'Selesai'], true)) {
                $this->deductInventoryStockForOrder($pesanan);
                $this->allocateLoyaltyPointsForOrder($pesanan);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Status antrean berhasil diperbarui menjadi '.$antrean->status_label,
                    'antrean' => $antrean,
                ]);
            }

            return redirect()->back()
                ->with('success', 'Status antrean '.$antrean->kode_antrean.' diperbarui.');
        });
    }

    /**
     * Update Antrean Alur from Antrean Mesin page action button (KF-13 & KF-15 integrated).
     */
    public function updateAntreanAlur(Request $request, AntreanMesin $antrean): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'action' => 'required|string|in:proses_cetak,selesai,serahkan,tunggu_bahan',
        ]);

        return DB::transaction(function () use ($validated, $request, $antrean) {
            $pesanan = $antrean->pesanan;
            $wasAlreadyCompleted = in_array($antrean->status_alur, ['Siap Diambil', 'Selesai'], true)
                || in_array($antrean->status_antrean, ['siap', 'selesai'], true)
                || $pesanan?->status_pesanan === 'selesai';

            switch ($validated['action']) {
                case 'proses_cetak':
                    $antrean->update([
                        'status_alur' => 'Sedang Cetak',
                        'status_antrean' => 'cetak',
                    ]);
                    $msg = 'Antrean '.$antrean->kode_antrean.' mulai diproses cetak.';
                    break;
                case 'selesai':
                    $antrean->update([
                        'status_alur' => 'Siap Diambil',
                        'status_antrean' => 'siap',
                        'status_pembayaran' => 'lunas',
                    ]);
                    if (! $wasAlreadyCompleted && $pesanan) {
                        $this->deductInventoryStockForOrder($pesanan);
                        $this->allocateLoyaltyPointsForOrder($pesanan);
                    }
                    $msg = 'Antrean '.$antrean->kode_antrean.' selesai proses dan siap diambil di loket.';
                    break;
                case 'serahkan':
                    $antrean->update([
                        'status_alur' => 'Selesai',
                        'status_antrean' => 'selesai',
                        'status_pembayaran' => 'lunas',
                    ]);
                    if ($pesanan) {
                        $pesanan->update(['status_pesanan' => 'selesai']);
                        if (! $wasAlreadyCompleted) {
                            $this->deductInventoryStockForOrder($pesanan);
                            $this->allocateLoyaltyPointsForOrder($pesanan);
                        }
                        $this->purgeOrderFiles($pesanan);
                    }
                    $msg = 'Pesanan '.$antrean->kode_antrean.' telah diserahkan ke pelanggan.';
                    break;
                case 'tunggu_bahan':
                    $antrean->update([
                        'status_alur' => 'Menunggu Bahan',
                        'status_antrean' => 'menunggu',
                    ]);
                    $msg = 'Status antrean diubah ke Menunggu Bahan.';
                    break;
                default:
                    $msg = 'Status diperbarui.';
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => $msg,
                    'antrean' => $antrean,
                ]);
            }

            return redirect()->back()->with('success', $msg);
        });
    }

    /**
     * Securely download customer's original document file from private storage
     * by berkas_id, pesanan_id, or antrean_id for cashier/operator printing.
     */
    public function downloadBerkas(int|string $id): StreamedResponse|RedirectResponse|JsonResponse
    {
        $berkas = BerkasDokumen::find($id);

        if (! $berkas) {
            $detail = DetailPesanan::where('pesanan_id', $id)
                ->whereNotNull('berkas_id')
                ->with('berkasDokumen')
                ->first();
            $berkas = $detail?->berkasDokumen;
        }

        if (! $berkas) {
            $antrean = AntreanMesin::with('pesanan.detailPesanan.berkasDokumen')->find($id);
            $berkas = $antrean?->pesanan?->detailPesanan?->firstWhere('berkas_id', '!=', null)?->berkasDokumen;
        }

        if (! $berkas || empty($berkas->file_path) || ! Storage::disk('local')->exists($berkas->file_path)) {
            if (request()->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Berkas dokumen tidak ditemukan di penyimpanan privat.',
                ], 404);
            }

            abort(404, 'Berkas dokumen tidak ditemukan di penyimpanan privat.');
        }

        return Storage::disk('local')->download($berkas->file_path, $berkas->nama_file);
    }

    /**
     * Auto-purge customer uploaded document files after order handover (UU PDP No. 27/2022 - NFR-S).
     */
    protected function purgeOrderFiles(Pesanan $pesanan): void
    {
        $pesanan->loadMissing('detailPesanan.berkasDokumen');

        foreach ($pesanan->detailPesanan as $detail) {
            if ($detail->berkasDokumen) {
                $detail->berkasDokumen->purgeBerkas();
            }
        }
    }

    /**
     * Store or update service tariff in master produk_layanan (UC-14 / KF-14: Kelola Tarif Layanan).
     */
    public function storeTarifLayanan(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'nama_item' => 'required|string|max:100',
            'kategori' => 'required|string|max:50',
            'harga_dasar' => 'required|numeric|min:0',
            'status_aktif' => 'nullable|boolean',
        ]);

        $produk = ProdukLayanan::updateOrCreate(
            ['nama_item' => $validated['nama_item']],
            [
                'kategori' => $validated['kategori'],
                'harga_dasar' => $validated['harga_dasar'],
                'status_aktif' => $validated['status_aktif'] ?? true,
            ]
        );

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => "Tarif layanan {$produk->nama_item} berhasil disimpan.",
                'produk' => $produk,
            ]);
        }

        return redirect()->back()
            ->with('success', "Tarif layanan {$produk->nama_item} berhasil diperbarui.");
    }

    /**
     * Update an existing ProdukLayanan tariff record (UC-14 / KF-14).
     */
    public function updateTarifLayanan(Request $request, ProdukLayanan $produk): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'nama_item' => 'sometimes|required|string|max:100',
            'kategori' => 'sometimes|required|string|max:50',
            'harga_dasar' => 'required|numeric|min:0',
            'status_aktif' => 'nullable|boolean',
        ]);

        $produk->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => "Tarif layanan {$produk->nama_item} berhasil diperbarui.",
                'produk' => $produk->fresh(),
            ]);
        }

        return redirect()->back()
            ->with('success', "Tarif layanan {$produk->nama_item} berhasil diperbarui.");
    }

    /**
     * Store or restock inventory item.
     */
    public function storeStock(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'nama_bahan' => 'required|string|max:100',
            'jumlah_stok' => 'required|integer|min:0',
            'batas_minimum' => 'required|integer|min:1',
            'satuan' => 'required|string|max:30',
        ]);

        $stok = InventarisStok::updateOrCreate(
            ['nama_bahan' => $validated['nama_bahan']],
            [
                'jumlah_stok' => $validated['jumlah_stok'],
                'batas_minimum' => $validated['batas_minimum'],
                'satuan' => $validated['satuan'],
            ]
        );

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Inventaris stok '.$stok->nama_bahan.' berhasil diperbarui.',
                'item' => $stok,
            ]);
        }

        return redirect()->back()
            ->with('success', 'Data stok inventaris berhasil diperbarui.');
    }

    /**
     * Seed realistic sample demo data matching the reference screenshot.
     */
    public function seedDemoOrders(): JsonResponse|RedirectResponse
    {
        DB::transaction(function () {
            // Clear current data first (use query()->delete() for MySQL foreign-key & transaction compatibility)
            AntreanMesin::query()->delete();
            DetailPesanan::query()->delete();
            Pesanan::query()->delete();

            $today = Carbon::today();

            // Set up inventory alerts to match screenshot: 2 items low
            InventarisStok::updateOrCreate(
                ['nama_bahan' => 'Kertas HVS A4 70gr'],
                ['jumlah_stok' => 2, 'batas_minimum' => 5, 'satuan' => 'Rim']
            );
            InventarisStok::updateOrCreate(
                ['nama_bahan' => 'Tinta Konica Hitam'],
                ['jumlah_stok' => 1, 'batas_minimum' => 2, 'satuan' => 'Btl']
            );
            InventarisStok::updateOrCreate(
                ['nama_bahan' => 'Kertas HVS F4 70gr'],
                ['jumlah_stok' => 45, 'batas_minimum' => 5, 'satuan' => 'Rim']
            );

            // Fetch product IDs
            $prodCetakA4 = ProdukLayanan::where('nama_item', 'like', '%Cetak A4 Hitam%')->first()
                ?? ProdukLayanan::create(['nama_item' => 'Cetak A4 Hitam Putih', 'kategori' => 'Jasa Cetak', 'harga_dasar' => 300, 'status_aktif' => true]);
            $prodCetakWarna = ProdukLayanan::where('nama_item', 'like', '%Cetak A4 Warna%')->first()
                ?? ProdukLayanan::create(['nama_item' => 'Cetak A4 Warna', 'kategori' => 'Jasa Cetak', 'harga_dasar' => 1000, 'status_aktif' => true]);
            $prodJilid = ProdukLayanan::where('nama_item', 'like', '%Jilid Spiral%')->first()
                ?? ProdukLayanan::create(['nama_item' => 'Jilid Spiral Kawat', 'kategori' => 'Jilid', 'harga_dasar' => 8000, 'status_aktif' => true]);

            // The 5 active priority orders exactly from reference image:
            $referenceOrders = [
                [
                    'tiket' => 1,
                    'nota' => '#MF-2401',
                    'nama' => 'Pak Hendra Wijaya',
                    'tipe' => '👑 Platinum',
                    'judul' => 'Skripsi Bab 1-6 (142 hlm)',
                    'spesifikasi' => 'A4 75gr HVS • 3 Rangkap • Hardcover...',
                    'mesin' => 'Mesin Konica 1',
                    'operator' => 'Operator: Budi',
                    'tenggat' => '15:30 WIB',
                    'ket_tenggat' => '⚡ Mendesak',
                    'bayar_status' => 'lunas',
                    'status_alur' => 'Sedang Cetak',
                    'status_antrean' => 'cetak',
                    'total' => 85000,
                    'metode' => 'qris',
                    'est' => 15,
                    'jenis' => 'walk-in',
                    'produk_id' => $prodCetakA4->produk_id,
                    'qty' => 142,
                ],
                [
                    'tiket' => 2,
                    'nota' => '#MF-2402',
                    'nama' => 'Dina Rahmawati',
                    'tipe' => 'Walk-in',
                    'judul' => 'Brosur Promosi UPJ (12 hlm)',
                    'spesifikasi' => 'A4 Art Paper 150gr Full Color • 100 Lembar',
                    'mesin' => 'Mesin Ricoh 2',
                    'operator' => 'Operator: Feri',
                    'tenggat' => '16:00 WIB',
                    'ket_tenggat' => 'Est. 45 mnt',
                    'bayar_status' => 'belum_bayar',
                    'status_alur' => 'Diterima',
                    'status_antrean' => 'menunggu',
                    'total' => 180000,
                    'metode' => 'tunai',
                    'est' => 45,
                    'jenis' => 'walk-in',
                    'produk_id' => $prodCetakWarna->produk_id,
                    'qty' => 100,
                ],
                [
                    'tiket' => 3,
                    'nota' => '#MF-2403',
                    'nama' => 'Bagas Prasetyo',
                    'tipe' => 'Reguler',
                    'judul' => 'Laporan Keuangan Tahunan (68 hlm)',
                    'spesifikasi' => 'F4 Folio HVS • Jilid Spiral Kawat Mika',
                    'mesin' => 'Mesin Canon 3',
                    'operator' => 'Workshop Finis',
                    'tenggat' => '16:15 WIB',
                    'ket_tenggat' => 'Sesuai Jadwal',
                    'bayar_status' => 'lunas',
                    'status_alur' => 'Sedang Jilid',
                    'status_antrean' => 'finishing',
                    'total' => 65000,
                    'metode' => 'transfer',
                    'est' => 20,
                    'jenis' => 'online',
                    'produk_id' => $prodJilid->produk_id,
                    'qty' => 68,
                ],
                [
                    'tiket' => 4,
                    'nota' => '#MF-2404',
                    'nama' => 'Ibu Siti Aminah',
                    'tipe' => 'Walk-in',
                    'judul' => 'Fotokopi KTP & KK',
                    'spesifikasi' => '4 lbr x 20 rangkap • HVS Bolak-balik',
                    'mesin' => 'Meja Kasir A',
                    'operator' => 'Operator: Feri',
                    'tenggat' => '14:40 WIB',
                    'ket_tenggat' => 'Telah Rampung',
                    'bayar_status' => 'lunas',
                    'status_alur' => 'Siap Diambil',
                    'status_antrean' => 'siap',
                    'total' => 45000,
                    'metode' => 'tunai',
                    'est' => 0,
                    'jenis' => 'walk-in',
                    'produk_id' => $prodCetakA4->produk_id,
                    'qty' => 80,
                ],
                [
                    'tiket' => 5,
                    'nota' => '#MF-2405',
                    'nama' => 'Kevin Sanjaya',
                    'tipe' => 'Mhs STAN',
                    'judul' => 'Diktat Kuliah Akuntansi (210 hlm)',
                    'spesifikasi' => 'A4 B/W Dupleks • Jilid Lakban Hitam',
                    'mesin' => 'Mesin Konica 2',
                    'operator' => 'Operator: Joko',
                    'tenggat' => '17:00 WIB',
                    'ket_tenggat' => 'Tunggu Bahan',
                    'bayar_status' => 'lunas',
                    'status_alur' => 'Menunggu Bahan',
                    'status_antrean' => 'menunggu',
                    'total' => 95000,
                    'metode' => 'qris',
                    'est' => 30,
                    'jenis' => 'walk-in',
                    'produk_id' => $prodCetakA4->produk_id,
                    'qty' => 210,
                ],
                // 3 more to reach 8 active queues
                [
                    'tiket' => 6,
                    'nota' => '#MF-2406',
                    'nama' => 'Anisa Rahma',
                    'tipe' => '🌐 Order Web (WA)',
                    'judul' => 'Cetak Sertifikat Seminar',
                    'spesifikasi' => 'Art Carton 260g • Full Color',
                    'mesin' => 'Mesin Konica 1',
                    'operator' => 'Operator: Budi',
                    'tenggat' => '17:15 WIB',
                    'ket_tenggat' => 'Sesuai Jadwal',
                    'bayar_status' => 'lunas',
                    'status_alur' => 'Sedang Cetak',
                    'status_antrean' => 'cetak',
                    'total' => 60000,
                    'metode' => 'qris',
                    'est' => 15,
                    'jenis' => 'online',
                    'produk_id' => $prodCetakWarna->produk_id,
                    'qty' => 30,
                ],
                [
                    'tiket' => 7,
                    'nota' => '#MF-2407',
                    'nama' => 'Toko Buku Al-Falah',
                    'tipe' => 'Walk-in',
                    'judul' => 'Cetak Pamflet Pengajian',
                    'spesifikasi' => 'F4 70g • HVS Cetak Blok',
                    'mesin' => 'Mesin Konica 1',
                    'operator' => 'Operator: Budi',
                    'tenggat' => '17:30 WIB',
                    'ket_tenggat' => 'Sesuai Jadwal',
                    'bayar_status' => 'lunas',
                    'status_alur' => 'Diterima',
                    'status_antrean' => 'menunggu',
                    'total' => 40000,
                    'metode' => 'qris',
                    'est' => 15,
                    'jenis' => 'walk-in',
                    'produk_id' => $prodCetakA4->produk_id,
                    'qty' => 100,
                ],
                [
                    'tiket' => 8,
                    'nota' => '#MF-2408',
                    'nama' => 'Rini Septiani',
                    'tipe' => '🌐 Order Web (WA)',
                    'judul' => 'Cetak E-Book Resep Masakan',
                    'spesifikasi' => 'A4 70g • Jilid Spiral',
                    'mesin' => 'Mesin Canon 3',
                    'operator' => 'Workshop Finis',
                    'tenggat' => '17:45 WIB',
                    'ket_tenggat' => 'Telah Rampung',
                    'bayar_status' => 'lunas',
                    'status_alur' => 'Siap Diambil',
                    'status_antrean' => 'siap',
                    'total' => 75000,
                    'metode' => 'qris',
                    'est' => 0,
                    'jenis' => 'online',
                    'produk_id' => $prodJilid->produk_id,
                    'qty' => 1,
                ],
            ];

            foreach ($referenceOrders as $item) {
                $p = Pesanan::create([
                    'nama_pemesan' => $item['nama'],
                    'tipe_pelanggan' => $item['tipe'],
                    'judul_dokumen' => $item['judul'],
                    'spesifikasi' => $item['spesifikasi'],
                    'tenggat_waktu' => $item['tenggat'],
                    'jenis_pesanan' => $item['jenis'],
                    'total_harga' => $item['total'],
                    'status_pesanan' => 'proses',
                    'metode_pembayaran' => $item['metode'],
                    'waktu_pesan' => $today->copy()->setTime(10, $item['tiket'] * 15),
                ]);

                DetailPesanan::create([
                    'pesanan_id' => $p->pesanan_id,
                    'produk_id' => $item['produk_id'],
                    'kuantitas' => $item['qty'],
                    'sub_total' => $item['total'],
                ]);

                AntreanMesin::create([
                    'pesanan_id' => $p->pesanan_id,
                    'nomor_tiket' => $item['tiket'],
                    'status_antrean' => $item['status_antrean'],
                    'estimasi_waktu' => $item['est'],
                    'nama_mesin' => $item['mesin'],
                    'nama_operator' => $item['operator'],
                    'status_alur' => $item['status_alur'],
                    'status_pembayaran' => $item['bayar_status'],
                    'keterangan_tenggat' => $item['ket_tenggat'],
                ]);
            }

            // Seed 16 completed orders earlier today to reach:
            // Total 24 Nota (18 Selesai, 6 Proses)
            // Total Revenue: Rp 1.250.000
            $completedOrders = [
                ['total' => 65000, 'metode' => 'qris'],
                ['total' => 75000, 'metode' => 'qris'],
                ['total' => 85000, 'metode' => 'qris'],
                ['total' => 50000, 'metode' => 'qris'],
                ['total' => 45000, 'metode' => 'qris'],
                ['total' => 60000, 'metode' => 'qris'],
                ['total' => 30000, 'metode' => 'tunai'],
                ['total' => 25000, 'metode' => 'tunai'],
                ['total' => 20000, 'metode' => 'tunai'],
                ['total' => 35000, 'metode' => 'transfer'],
                ['total' => 25000, 'metode' => 'qris'],
                ['total' => 20000, 'metode' => 'qris'],
                ['total' => 20000, 'metode' => 'qris'],
                ['total' => 25000, 'metode' => 'qris'],
                ['total' => 15000, 'metode' => 'qris'],
                ['total' => 15000, 'metode' => 'qris'],
            ];

            foreach ($completedOrders as $idx => $comp) {
                $p = Pesanan::create([
                    'nama_pemesan' => 'Pelanggan Gerai #'.($idx + 1),
                    'tipe_pelanggan' => 'Walk-in • Reguler',
                    'judul_dokumen' => 'Fotokopi Dokumen #'.($idx + 1),
                    'spesifikasi' => 'A4 70g • Hitam Putih',
                    'tenggat_waktu' => 'Selesai',
                    'jenis_pesanan' => 'walk-in',
                    'total_harga' => $comp['total'],
                    'status_pesanan' => 'selesai',
                    'metode_pembayaran' => $comp['metode'],
                    'waktu_pesan' => $today->copy()->setTime(8, $idx * 15),
                ]);

                DetailPesanan::create([
                    'pesanan_id' => $p->pesanan_id,
                    'produk_id' => $prodCetakA4->produk_id,
                    'kuantitas' => max(1, (int) round($comp['total'] / 300)),
                    'sub_total' => $comp['total'],
                ]);
            }

            // Historical 6 days
            $pastRevenues = [950000, 1100000, 800000, 1200000, 1150000, 1050000];
            for ($d = 6; $d >= 1; $d--) {
                $pastDate = $today->copy()->subDays($d);
                $rev = $pastRevenues[6 - $d] ?? 1000000;
                Pesanan::create([
                    'nama_pemesan' => 'Rekap Harian '.$pastDate->format('d M'),
                    'tipe_pelanggan' => 'Sistem Rekap',
                    'judul_dokumen' => 'Rekap Transaksi Harian',
                    'spesifikasi' => 'Omzet Operasional',
                    'jenis_pesanan' => 'walk-in',
                    'total_harga' => $rev,
                    'status_pesanan' => 'selesai',
                    'metode_pembayaran' => 'qris',
                    'waktu_pesan' => $pastDate->setTime(17, 0),
                ]);
            }
        });

        if (request()->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Data simulasi referensi berhasil dimuat.',
            ]);
        }

        return redirect()->back()
            ->with('success', 'Data referensi berhasil diaktifkan untuk simulasi.');
    }

    /**
     * Clear all orders back to pristine empty state.
     */
    public function clearDemoOrders(): JsonResponse|RedirectResponse
    {
        DB::transaction(function () {
            AntreanMesin::query()->delete();
            DetailPesanan::query()->delete();
            Pesanan::query()->delete();

            // Reset stocks to safe levels
            InventarisStok::updateOrCreate(
                ['nama_bahan' => 'Kertas HVS A4 70gr'],
                ['jumlah_stok' => 50, 'batas_minimum' => 5, 'satuan' => 'Rim']
            );
            InventarisStok::updateOrCreate(
                ['nama_bahan' => 'Tinta Konica Hitam'],
                ['jumlah_stok' => 10, 'batas_minimum' => 2, 'satuan' => 'Btl']
            );
            InventarisStok::updateOrCreate(
                ['nama_bahan' => 'Kertas HVS F4 70gr'],
                ['jumlah_stok' => 40, 'batas_minimum' => 5, 'satuan' => 'Rim']
            );
        });

        if (request()->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Dashboard berhasil dikembalikan ke Kondisi Kosong (Empty State).',
            ]);
        }

        return redirect()->back()
            ->with('success', 'Dashboard berhasil direset ke Kondisi Awal Kosong (Empty State).');
    }

    /**
     * Display the Customer portal dashboard.
     */
    public function customer(): View
    {
        $user = Auth::user();
        $user->load('pelanggan');

        $pelanggan = $user->pelanggan;
        $saldoPoin = $pelanggan ? $pelanggan->saldo_poin : 0;

        $loyaltyHistory = $pelanggan
            ? ProgramLoyalitas::where('pelanggan_id', $pelanggan->pelanggan_id)->latest()->take(5)->get()
            : collect();

        $customerOrders = $pelanggan
            ? Pesanan::where('pelanggan_id', $pelanggan->pelanggan_id)
                ->with(['detailPesanan.produkLayanan', 'antreanMesin'])
                ->latest()
                ->get()
            : collect();

        $availableProducts = ProdukLayanan::where('status_aktif', true)->take(8)->get();
        $stokItems = InventarisStok::orderBy('nama_bahan', 'asc')->get();

        return view('customer.home', compact(
            'user',
            'pelanggan',
            'saldoPoin',
            'loyaltyHistory',
            'customerOrders',
            'availableProducts',
            'stokItems'
        ));
    }

    /**
     * Store order placed by Customer from customer portal.
     * Automatically integrates and forwards into Antrean Mesin in real time!
     */
    public function storeCustomerOrder(Request $request): JsonResponse|RedirectResponse
    {
        $user = Auth::user();
        $user->load('pelanggan');

        $validated = $request->validate([
            'judul_dokumen' => 'required|string|max:200',
            'spesifikasi' => 'required|string',
            'produk_id' => 'required|exists:produk_layanan,produk_id',
            'kuantitas' => 'required|integer|min:1',
            'metode_pembayaran' => 'required|string|in:qris,transfer',
            'berkas' => 'nullable|file|max:20480', // 20MB max
        ]);

        return DB::transaction(function () use ($validated, $user, $request) {
            $pelanggan = $user->pelanggan;
            if (! $pelanggan) {
                $pelanggan = Pelanggan::create([
                    'user_id' => $user->user_id,
                    'saldo_poin' => 100,
                ]);
            }

            $produk = ProdukLayanan::findOrFail($validated['produk_id']);
            $kuantitas = (int) $validated['kuantitas'];
            $jumlahHalaman = 1;

            $berkasId = null;
            if ($request->hasFile('berkas')) {
                $file = $request->file('berkas');

                try {
                    $path = $file->store('berkas_pelanggan', 'local');
                } catch (\Throwable) {
                    $ext = strtolower($file->getClientOriginalExtension() ?: 'pdf');
                    $path = $file->storeAs('berkas_pelanggan', Str::random(40).'.'.$ext, 'local');
                }

                if ($this->isPdfFile($file, $path)) {
                    $jumlahHalaman = $this->parsePdfPageCount($file, $path);
                }

                $berkas = BerkasDokumen::create([
                    'pelanggan_id' => $pelanggan->pelanggan_id,
                    'nama_file' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'ukuran_file' => $file->getSize(),
                    'jumlah_halaman' => $jumlahHalaman,
                    'status_validasi' => 'valid (300 DPI)',
                ]);
                $berkasId = $berkas->berkas_id;
            }

            $totalHarga = $jumlahHalaman * (float) $produk->harga_dasar * $kuantitas;

            $pesanan = Pesanan::create([
                'pelanggan_id' => $pelanggan->pelanggan_id,
                'nama_pemesan' => $user->nama,
                'tipe_pelanggan' => '🌐 Order Web (WA)',
                'judul_dokumen' => $validated['judul_dokumen'],
                'spesifikasi' => $validated['spesifikasi'],
                'tenggat_waktu' => now()->addMinutes(45)->format('H:i').' WIB',
                'jenis_pesanan' => 'online',
                'total_harga' => $totalHarga,
                'volume_halaman' => $jumlahHalaman,
                'status_pesanan' => 'proses',
                'metode_pembayaran' => $validated['metode_pembayaran'],
                'waktu_pesan' => now(),
            ]);

            DetailPesanan::create([
                'pesanan_id' => $pesanan->pesanan_id,
                'produk_id' => $produk->produk_id,
                'berkas_id' => $berkasId,
                'kuantitas' => $kuantitas,
                'sub_total' => $totalHarga,
            ]);

            $nextTiket = (AntreanMesin::whereDate('created_at', Carbon::today())->max('nomor_tiket') ?? 0) + 1;

            // Route dynamically to appropriate workshop machine
            $namaMesin = str_contains(strtolower($validated['spesifikasi']), 'warna') ? 'Mesin Ricoh 2' : 'Mesin Konica 1';
            $operator = 'Operator: Budi';

            $antrean = AntreanMesin::create([
                'pesanan_id' => $pesanan->pesanan_id,
                'nomor_tiket' => $nextTiket,
                'status_antrean' => 'menunggu',
                'estimasi_waktu' => 25,
                'nama_mesin' => $namaMesin,
                'nama_operator' => $operator,
                'status_alur' => 'Diterima',
                'status_pembayaran' => 'lunas',
                'keterangan_tenggat' => 'Est. 45 mnt',
            ]);

            // Allocate loyalty points when customer order is paid (lunas)
            $this->allocateLoyaltyPointsForOrder($pesanan);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Pesanan berhasil dikirim ke antrean mesin kasir.',
                    'pesanan' => $pesanan,
                    'antrean' => $antrean,
                ]);
            }

            return redirect()->route('customer.home')
                ->with('success', 'Pesanan Anda berhasil dikirim ke antrean mesin workshop gerai!');
        });
    }

    /**
     * Check whether the uploaded file is a PDF document.
     */
    protected function isPdfFile(UploadedFile $file, ?string $storedPath = null): bool
    {
        $ext = strtolower($file->getClientOriginalExtension());
        $clientMime = strtolower((string) $file->getClientMimeType());

        if ($ext === 'pdf' || str_contains($clientMime, 'pdf')) {
            return true;
        }

        if ($storedPath && Storage::disk('local')->exists($storedPath)) {
            $head = (string) Storage::disk('local')->get($storedPath);
            if (str_starts_with(ltrim($head), '%PDF-')) {
                return true;
            }
        }

        try {
            $detectedMime = strtolower((string) $file->getMimeType());

            return str_contains($detectedMime, 'pdf');
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Native stream/regex parser to calculate real PDF page count (KF-04).
     */
    public function parsePdfPageCount(UploadedFile|string $file, ?string $storedPath = null): int
    {
        $content = '';

        if ($storedPath && Storage::disk('local')->exists($storedPath)) {
            $content = (string) Storage::disk('local')->get($storedPath);
        }

        if ($content === '') {
            $filePath = $file instanceof UploadedFile ? ($file->getRealPath() ?: $file->getPathname()) : $file;
            if ($filePath && is_file($filePath)) {
                $content = (string) @file_get_contents($filePath);
            }
        }

        if ($content === '') {
            return 1;
        }

        // 1. Match individual /Type /Page dictionary objects (excluding /Pages)
        $pageObjectCount = preg_match_all('/\/Type\s*\/Page(?!s)\b/i', $content, $pageMatches);

        // 2. Inspect /Count N entries in /Pages tree dictionaries
        $treeCount = 0;
        if (preg_match_all('/\/Type\s*\/Pages\b[^>]*?\/Count\s+(\d+)/is', $content, $treeMatches) && ! empty($treeMatches[1])) {
            $treeCount = max(array_map('intval', $treeMatches[1]));
        } elseif (preg_match_all('/\/Count\s+(\d+)/i', $content, $countMatches) && ! empty($countMatches[1])) {
            $treeCount = max(array_map('intval', $countMatches[1]));
        }

        $detectedPages = max((int) $pageObjectCount, (int) $treeCount);

        return max(1, $detectedPages);
    }

    /**
     * Automatically deduct raw paper stock from inventaris_stok based on order specification & volume (KF-13).
     */
    protected function deductInventoryStockForOrder(Pesanan $pesanan): ?InventarisStok
    {
        $pesanan->loadMissing('detailPesanan.produkLayanan');

        $specCombined = strtoupper(
            ($pesanan->spesifikasi ?? '').' '.
            ($pesanan->judul_dokumen ?? '').' '.
            ($pesanan->detailPesanan->map(fn ($d) => $d->produkLayanan?->nama_item ?? '')->implode(' '))
        );

        $isF4 = str_contains($specCombined, 'F4') || str_contains($specCombined, 'FOLIO');
        $is80gr = str_contains($specCombined, '80G') || str_contains($specCombined, '80 GR');

        $stok = null;
        if ($isF4) {
            $stok = InventarisStok::where('nama_bahan', 'like', '%Kertas%F4%')
                ->orWhere('nama_bahan', 'like', '%Folio%F4%')
                ->orWhere('nama_bahan', 'like', '%F4%')
                ->orWhere('kode_bahan', 'like', '%F4%')
                ->orderBy('stok_id', 'asc')
                ->first();
        } else {
            if ($is80gr) {
                $stok = InventarisStok::where('nama_bahan', 'like', '%A4%80%')
                    ->orWhere('kode_bahan', 'like', '%A480%')
                    ->first();
            }

            if (! $stok) {
                $stok = InventarisStok::where('nama_bahan', 'Kertas HVS A4 70gr')->first()
                    ?? InventarisStok::where('nama_bahan', 'like', '%Kertas HVS A4%')->orderBy('stok_id', 'asc')->first()
                    ?? InventarisStok::where('nama_bahan', 'like', '%A4%')->orderBy('stok_id', 'asc')->first();
            }
        }

        if (! $stok) {
            $stok = InventarisStok::where('kategori', 'like', '%Kertas%')
                ->orWhere('nama_bahan', 'like', '%Kertas%')
                ->orderBy('stok_id', 'asc')
                ->first();
        }

        if (! $stok) {
            return null;
        }

        $volumeHalaman = max(1, (int) ($pesanan->volume_halaman ?? 1));
        $kuantitas = max(1, (int) ($pesanan->detailPesanan->sum('kuantitas') ?: 1));
        $totalLembar = $volumeHalaman * $kuantitas;

        if (strtolower(trim((string) $stok->satuan)) === 'rim') {
            $potongan = $totalLembar > 0 ? (int) ceil($totalLembar / 500) : 0;
        } else {
            $potongan = $totalLembar;
        }

        $stokBaru = max(0, (int) $stok->jumlah_stok - $potongan);
        $stok->update([
            'jumlah_stok' => $stokBaru,
        ]);

        return $stok->fresh();
    }

    /**
     * Automatically allocate loyalty points when a registered customer's order is paid/completed (KF-15).
     * Grants 10 points for every Rp 10.000 of total_harga.
     */
    protected function allocateLoyaltyPointsForOrder(Pesanan $pesanan): int
    {
        if (! $pesanan->pelanggan_id) {
            return 0;
        }

        $pelanggan = Pelanggan::find($pesanan->pelanggan_id);
        if (! $pelanggan) {
            return 0;
        }

        $poinMasuk = (int) floor(((float) $pesanan->total_harga) / 10000) * 10;
        if ($poinMasuk <= 0) {
            return 0;
        }

        $pelanggan->increment('saldo_poin', $poinMasuk);

        ProgramLoyalitas::create([
            'pelanggan_id' => $pelanggan->pelanggan_id,
            'jumlah_poin_masuk' => $poinMasuk,
            'jumlah_poin_keluar' => 0,
            'tanggal_transaksi' => now(),
        ]);

        return $poinMasuk;
    }

    /**
     * Calculate key operational dashboard metrics and trends.
     */
    protected function calculateDashboardStats(): array
    {
        $today = Carbon::today();

        $pesananHariIni = Pesanan::whereDate('waktu_pesan', $today)->count();
        $pesananSelesai = Pesanan::whereDate('waktu_pesan', $today)->where('status_pesanan', 'selesai')->count();
        $pesananProses = Pesanan::whereDate('waktu_pesan', $today)->where('status_pesanan', '!=', 'selesai')->count();

        $pendapatanShift = (float) Pesanan::whereDate('waktu_pesan', $today)->sum('total_harga');
        $targetShift = 1500000;
        $persentasePendapatan = $targetShift > 0 ? min(100, round(($pendapatanShift / $targetShift) * 100)) : 0;

        $activeQueueQuery = AntreanMesin::where('status_antrean', '!=', 'selesai');
        $antreanAktif = $activeQueueQuery->count();
        $estTunggu = (int) $activeQueueQuery->sum('estimasi_waktu');

        $antreanWalkIn = AntreanMesin::where('status_antrean', '!=', 'selesai')
            ->whereHas('pesanan', fn ($q) => $q->where('jenis_pesanan', 'walk-in'))
            ->count();

        $antreanOnline = AntreanMesin::where('status_antrean', '!=', 'selesai')
            ->whereHas('pesanan', fn ($q) => $q->whereIn('jenis_pesanan', ['online', 'web', 'wa']))
            ->count();

        $bahanKritisList = InventarisStok::whereColumn('jumlah_stok', '<=', 'batas_minimum')->get();
        $bahanKritisCount = $bahanKritisList->count();

        // Payment method breakdown
        $qrisTotal = (float) Pesanan::whereDate('waktu_pesan', $today)->where('metode_pembayaran', 'qris')->sum('total_harga');
        $tunaiTotal = (float) Pesanan::whereDate('waktu_pesan', $today)->where('metode_pembayaran', 'tunai')->sum('total_harga');
        $transferTotal = (float) Pesanan::whereDate('waktu_pesan', $today)->whereIn('metode_pembayaran', ['transfer', 'edc'])->sum('total_harga');

        $totalMetode = $qrisTotal + $tunaiTotal + $transferTotal;
        $qrisPercent = $totalMetode > 0 ? round(($qrisTotal / $totalMetode) * 100) : 0;
        $tunaiPercent = $totalMetode > 0 ? round(($tunaiTotal / $totalMetode) * 100) : 0;
        $transferPercent = $totalMetode > 0 ? round(($transferTotal / $totalMetode) * 100) : 0;

        // Material consumption today
        $totalKuantitasCetak = DetailPesanan::whereHas('pesanan', fn ($q) => $q->whereDate('waktu_pesan', $today))
            ->whereHas('produkLayanan', fn ($q) => $q->where('kategori', 'Jasa Cetak'))
            ->sum('kuantitas');

        $totalJilid = DetailPesanan::whereHas('pesanan', fn ($q) => $q->whereDate('waktu_pesan', $today))
            ->whereHas('produkLayanan', fn ($q) => $q->where('kategori', 'Jilid'))
            ->sum('kuantitas');

        // 7-day trend
        $tren7Hari = [];
        $totalOmzet7Hari = 0;
        $startDate = Carbon::today()->subDays(6);

        for ($i = 0; $i < 7; $i++) {
            $date = $startDate->copy()->addDays($i);
            $dayLabel = match ($date->dayOfWeek) {
                0 => 'Min',
                1 => 'Sen',
                2 => 'Sel',
                3 => 'Rab',
                4 => 'Kamis',
                5 => 'Jum',
                6 => 'Sab',
            }.' ('.$date->format('j').')';

            $dailyRevenue = (float) Pesanan::whereDate('waktu_pesan', $date)->sum('total_harga');
            $totalOmzet7Hari += $dailyRevenue;

            $tren7Hari[] = [
                'date' => $date->format('Y-m-d'),
                'label' => $dayLabel,
                'revenue' => $dailyRevenue,
                'is_today' => $date->isToday(),
            ];
        }

        $rataRataOmzetHarian = round($totalOmzet7Hari / 7);

        return [
            'pesanan_hari_ini' => $pesananHariIni,
            'pesanan_selesai' => $pesananSelesai,
            'pesanan_proses' => $pesananProses,
            'pendapatan_shift' => $pendapatanShift,
            'target_shift' => $targetShift,
            'persentase_pendapatan' => $persentasePendapatan,
            'antrean_aktif' => $antreanAktif,
            'est_tunggu' => $estTunggu,
            'antrean_walk_in' => $antreanWalkIn,
            'antrean_online' => $antreanOnline,
            'bahan_kritis_count' => $bahanKritisCount,
            'bahan_kritis_items' => $bahanKritisList,
            'pembayaran' => [
                'qris' => $qrisTotal,
                'tunai' => $tunaiTotal,
                'transfer' => $transferTotal,
                'qris_percent' => $qrisPercent,
                'tunai_percent' => $tunaiPercent,
                'transfer_percent' => $transferPercent,
                'total' => $totalMetode,
            ],
            'konsumsi' => [
                'kertas_hvs_rim' => $totalKuantitasCetak > 0 ? round($totalKuantitasCetak / 500, 1) : 0,
                'jilid_buku' => (int) $totalJilid,
            ],
            'tren_7_hari' => $tren7Hari,
            'rata_rata_omzet_harian' => $rataRataOmzetHarian,
            'periode_7_hari' => $startDate->format('j').' - '.Carbon::today()->format('j M'),
        ];
    }

    /**
     * Calculate Workshop and Machine Loads stats.
     */
    protected function calculateWorkshopStats(): array
    {
        $activeQueues = AntreanMesin::with('pesanan')
            ->where('status_antrean', '!=', 'selesai')
            ->get();

        $totalCount = $activeQueues->count();

        $diterimaCount = $activeQueues->filter(fn ($q) => in_array(strtolower($q->status_alur ?? $q->status_antrean), ['diterima', 'menunggu']))->count();
        $prosesCount = $activeQueues->filter(fn ($q) => in_array(strtolower($q->status_alur ?? $q->status_antrean), ['sedang cetak', 'sedang jilid', 'menunggu bahan', 'cetak', 'salin', 'finishing']))->count();
        $siapCount = $activeQueues->filter(fn ($q) => in_array(strtolower($q->status_alur ?? $q->status_antrean), ['siap diambil', 'siap', 'siap_pickup']))->count();

        $konicaQueues = $activeQueues->filter(fn ($q) => str_contains(strtolower($q->nama_mesin ?? ''), 'konica 1') || str_contains(strtolower($q->pesanan?->judul_dokumen ?? ''), 'skripsi'))->count();
        $ricohQueues = $activeQueues->filter(fn ($q) => str_contains(strtolower($q->nama_mesin ?? ''), 'ricoh'))->count();
        $canonQueues = $activeQueues->filter(fn ($q) => str_contains(strtolower($q->nama_mesin ?? ''), 'canon') || str_contains(strtolower($q->nama_mesin ?? ''), 'kasir a'))->count();

        if ($totalCount === 0) {
            $konicaPct = 0;
            $konicaStatus = 'Standby • Siap Order';
            $ricohPct = 0;
            $ricohStatus = 'Standby • Siap Order';
            $canonPct = 0;
            $canonStatus = 'Standby • Siap Order';
        } else {
            if ($konicaQueues >= 3) {
                $konicaPct = 85;
                $konicaStatus = "Beban Sibuk (Queue: {$konicaQueues})";
            } elseif ($konicaQueues > 0) {
                $konicaPct = 60;
                $konicaStatus = "Stabil (Queue: {$konicaQueues})";
            } else {
                $konicaPct = 0;
                $konicaStatus = 'Standby • Siap Order';
            }

            $ricohPct = $ricohQueues > 0 ? 40 : 0;
            $ricohStatus = $ricohQueues > 0 ? "Stabil (Queue: {$ricohQueues})" : 'Standby • Siap Order';

            $canonPct = $canonQueues > 0 ? min(100, $canonQueues * 25) : 0;
            $canonStatus = $canonQueues > 0 ? "Beban Aktif (Queue: {$canonQueues})" : 'Standby • Siap Order';
        }

        return [
            'total' => $totalCount,
            'diterima' => $diterimaCount,
            'proses' => $prosesCount,
            'siap' => $siapCount,
            'machines' => [
                'konica' => [
                    'nama' => 'MESIN KONICA 1 • HEAVY',
                    'sub' => 'Cetak Skripsi',
                    'status' => $konicaStatus,
                    'pct' => $konicaPct,
                    'queue' => $konicaQueues,
                ],
                'ricoh' => [
                    'nama' => 'MESIN RICOH 2 • DIGITAL',
                    'sub' => 'Warna High-speed',
                    'status' => $ricohStatus,
                    'pct' => $ricohPct,
                    'queue' => $ricohQueues,
                ],
                'canon' => [
                    'nama' => 'CANON 3 / KASIR A',
                    'sub' => 'Fotokopi Dokumen',
                    'status' => $canonStatus,
                    'pct' => $canonPct,
                    'queue' => $canonQueues,
                ],
            ],
        ];
    }

    /**
     * Display the Kasir POS Walk-in page.
     */
    public function walkInIndex(Request $request): View
    {
        $user = Auth::user();
        $user->load('adminKasir');

        $this->ensureCatalogProductsExist();

        $todayWalkInOrders = Pesanan::with(['pelanggan.user', 'detailPesanan.produkLayanan'])
            ->where('jenis_pesanan', 'walk-in')
            ->whereDate('waktu_pesan', Carbon::today())
            ->orderBy('waktu_pesan', 'desc')
            ->get();

        $todayOmset = (float) $todayWalkInOrders->sum('total_harga');
        $todayCount = $todayWalkInOrders->count();

        // Calculate next invoice code dynamically
        $maxPesananId = Pesanan::max('pesanan_id') ?? 0;
        $nextNotaCode = '#W-'.(1000 + $maxPesananId + 1);

        $activeAntreanCount = AntreanMesin::where('status_antrean', '!=', 'selesai')->count();

        $categories = [
            'Semua Item',
            'Fotokopi & Print',
            'Alat Tulis & Map',
            'Jilid & Finishing',
            'Jasa & Lainnya',
        ];

        $catalogProducts = ProdukLayanan::where('status_aktif', true)
            ->orderBy('kategori', 'asc')
            ->orderBy('nama_item', 'asc')
            ->get();

        $members = Pelanggan::with('user')->get();

        // Sync monthly revenue report
        $this->syncMonthlyOmset();

        return view('admin.walkin', compact(
            'user',
            'todayWalkInOrders',
            'todayOmset',
            'todayCount',
            'nextNotaCode',
            'activeAntreanCount',
            'categories',
            'catalogProducts',
            'members'
        ));
    }

    /**
     * Return live data for Kasir Walk-in POS.
     */
    public function walkInLiveData(): JsonResponse
    {
        $todayWalkInOrders = Pesanan::with(['pelanggan.user', 'detailPesanan.produkLayanan'])
            ->where('jenis_pesanan', 'walk-in')
            ->whereDate('waktu_pesan', Carbon::today())
            ->orderBy('waktu_pesan', 'desc')
            ->get();

        $todayOmset = (float) $todayWalkInOrders->sum('total_harga');
        $todayCount = $todayWalkInOrders->count();

        $maxPesananId = Pesanan::max('pesanan_id') ?? 0;
        $nextNotaCode = '#W-'.(1000 + $maxPesananId + 1);
        $activeAntreanCount = AntreanMesin::where('status_antrean', '!=', 'selesai')->count();

        $orders = $todayWalkInOrders->map(function ($order) {
            return [
                'id' => $order->pesanan_id,
                'no_nota' => $order->kode_nota_walkin,
                'waktu' => $order->waktu_transaksi_format,
                'nama' => $order->display_name,
                'tipe' => $order->display_tipe,
                'items_summary' => $order->spesifikasi ?: $order->display_judul,
                'total' => (float) $order->total_harga,
                'total_formatted' => 'Rp '.number_format((float) $order->total_harga, 0, ',', '.'),
                'metode' => strtoupper($order->metode_pembayaran),
                'metode_badge_class' => $order->metode_badge_class,
            ];
        });

        return response()->json([
            'status' => 'success',
            'todayOmset' => $todayOmset,
            'todayOmsetFormatted' => 'Rp '.number_format($todayOmset, 0, ',', '.'),
            'todayCount' => $todayCount,
            'nextNotaCode' => $nextNotaCode,
            'activeAntreanCount' => $activeAntreanCount,
            'orders' => $orders,
        ]);
    }

    /**
     * Store POS walk-in transaction (checkout).
     */
    public function storeWalkInCheckout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama_pemesan' => 'required|string|max:150',
            'tipe_pelanggan' => 'nullable|string|max:100',
            'pelanggan_id' => 'nullable|exists:pelanggan,pelanggan_id',
            'items' => 'required|array|min:1',
            'items.*.nama_item' => 'required|string',
            'items.*.kuantitas' => 'required|integer|min:1',
            'items.*.harga_satuan' => 'required|numeric|min:0',
            'items.*.sub_total' => 'required|numeric|min:0',
            'items.*.rincian' => 'nullable|string',
            'items.*.produk_id' => 'nullable',
            'subtotal' => 'required|numeric|min:0',
            'diskon' => 'nullable|numeric|min:0',
            'total_harga' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|string',
            'uang_diterima' => 'nullable|numeric',
            'kembalian' => 'nullable|numeric',
            'kirim_workshop' => 'nullable|boolean',
        ]);

        $this->ensureCatalogProductsExist();

        return DB::transaction(function () use ($validated) {
            // Build item description summary
            $itemStrings = [];
            foreach ($validated['items'] as $item) {
                $sub = $item['nama_item'].' ('.$item['kuantitas'].'x)';
                if (! empty($item['rincian'])) {
                    $sub = $item['nama_item'].' '.$item['rincian'].' ('.$item['kuantitas'].'x)';
                }
                $itemStrings[] = $sub;
            }
            $spesifikasiSummary = implode(' + ', $itemStrings);
            $judulUtama = $validated['items'][0]['nama_item'] ?? 'Penjualan Walk-in Kasir';

            $pesanan = Pesanan::create([
                'pelanggan_id' => $validated['pelanggan_id'] ?? null,
                'nama_pemesan' => $validated['nama_pemesan'],
                'tipe_pelanggan' => $validated['tipe_pelanggan'] ?? 'Walk-in Tamu',
                'judul_dokumen' => $judulUtama,
                'spesifikasi' => $spesifikasiSummary,
                'tenggat_waktu' => now()->addMinutes(15)->format('H:i').' WIB',
                'jenis_pesanan' => 'walk-in',
                'total_harga' => $validated['total_harga'],
                'status_pesanan' => 'selesai',
                'metode_pembayaran' => strtoupper($validated['metode_pembayaran']),
                'waktu_pesan' => now(),
            ]);

            // Save details
            foreach ($validated['items'] as $item) {
                $produkId = $item['produk_id'] ?? null;
                if (! $produkId || ! ProdukLayanan::where('produk_id', $produkId)->exists()) {
                    $prod = ProdukLayanan::where('nama_item', 'like', '%'.$item['nama_item'].'%')->first();
                    $produkId = $prod?->produk_id ?? (ProdukLayanan::first()?->produk_id ?? 1);
                }

                DetailPesanan::create([
                    'pesanan_id' => $pesanan->pesanan_id,
                    'produk_id' => $produkId,
                    'kuantitas' => $item['kuantitas'],
                    'sub_total' => $item['sub_total'],
                ]);
            }

            // Transaksi Kasir Walk-in adalah transaksi kasir langsung (POS) dan tidak masuk ke antrean mesin workshop
            $antrean = null;

            // Allocate loyalty points if walk-in transaction belongs to a registered member (KF-15)
            $this->allocateLoyaltyPointsForOrder($pesanan);

            // Synchronize monthly revenue report immediately
            $this->syncMonthlyOmset();

            return response()->json([
                'status' => 'success',
                'message' => 'Transaksi POS Walk-in berhasil disimpan ke tabel transaksi.',
                'pesanan' => [
                    'id' => $pesanan->pesanan_id,
                    'no_nota' => $pesanan->kode_nota_walkin,
                    'nama' => $pesanan->display_name,
                    'total' => (float) $pesanan->total_harga,
                    'total_formatted' => 'Rp '.number_format((float) $pesanan->total_harga, 0, ',', '.'),
                    'metode' => $pesanan->metode_pembayaran,
                    'waktu' => $pesanan->waktu_transaksi_format,
                ],
                'antrean' => $antrean ? [
                    'kode' => $antrean->kode_antrean,
                    'mesin' => $antrean->nama_mesin,
                ] : null,
            ]);
        });
    }

    /**
     * Seed Walk-in Demo data matching reference screenshot.
     */
    public function seedWalkInDemo(): RedirectResponse
    {
        $this->ensureCatalogProductsExist();

        // Remove previous demo walk-in orders for a fresh reference state
        Pesanan::where('jenis_pesanan', 'walk-in')->delete();

        $demoItems = [
            [
                'nama' => 'CV Berkah Jaya',
                'tipe' => 'Member Bisnis',
                'judul' => 'Kertas HVS A4 70gr (2 Rim)',
                'spesifikasi' => 'Kertas HVS A4 70gr (2 Rim)',
                'total' => 96000,
                'metode' => 'Transfer',
                'waktu' => Carbon::today()->setHour(13)->setMinute(52),
            ],
            [
                'nama' => 'Dimas Prasetyo',
                'tipe' => 'Member MTH-7102',
                'judul' => 'Print Warna A4 + Jilid Lakban',
                'spesifikasi' => 'Print Warna A4 (24 lbr) + Jilid Lakban Mika',
                'total' => 29000,
                'metode' => 'BCA',
                'waktu' => Carbon::today()->setHour(14)->setMinute(5),
            ],
            [
                'nama' => 'Budi Santoso',
                'tipe' => 'Walk-in Tamu',
                'judul' => 'Materai 10.000 + Pulpen Faster',
                'spesifikasi' => 'Materai 10.000 (2 pcs) + Pulpen Faster (1 pcs)',
                'total' => 28500,
                'metode' => 'Tunai',
                'waktu' => Carbon::today()->setHour(14)->setMinute(18),
            ],
            [
                'nama' => 'Anisa Rahma',
                'tipe' => '0857-1122-3344',
                'judul' => 'Fotokopi A4 H/P + Map Stopmap Folio',
                'spesifikasi' => 'Fotokopi A4 H/P (12 lbr) + Map Stopmap Folio (2 pcs)',
                'total' => 10000,
                'metode' => 'QRIS',
                'waktu' => Carbon::today()->setHour(14)->setMinute(26),
            ],
        ];

        // Add 30 other completed transactions to reach exactly 34 transactions and Rp 1.482.500 omset
        $currentSum = 96000 + 29000 + 28500 + 10000; // 163,500
        $targetSum = 1482500;
        $remainder = $targetSum - $currentSum; // 1,319,000 across 30 transactions (~43,966 per tx)

        $firstProd = ProdukLayanan::first()?->produk_id ?? 1;

        // Seed the 4 screenshot transactions
        foreach ($demoItems as $d) {
            $pes = Pesanan::create([
                'nama_pemesan' => $d['nama'],
                'tipe_pelanggan' => $d['tipe'],
                'judul_dokumen' => $d['judul'],
                'spesifikasi' => $d['spesifikasi'],
                'jenis_pesanan' => 'walk-in',
                'total_harga' => $d['total'],
                'status_pesanan' => 'selesai',
                'metode_pembayaran' => $d['metode'],
                'waktu_pesan' => $d['waktu'],
            ]);

            DetailPesanan::create([
                'pesanan_id' => $pes->pesanan_id,
                'produk_id' => $firstProd,
                'kuantitas' => 1,
                'sub_total' => $d['total'],
            ]);
        }

        // Seed additional transactions up to 34
        $names = ['Rian Pratama', 'Siti Aminah', 'Ahmad Dani', 'Hendro Wijaya', 'Ratna Dewi', 'Doni Kusuma', 'Eka Saputra', 'Maya Indah', 'Lestari Store', 'Fajar Grafika'];
        $methods = ['QRIS', 'Tunai', 'BCA', 'Transfer'];
        $baseSlice = round($remainder / 30);
        for ($i = 1; $i <= 30; $i++) {
            $val = ($i === 30) ? ($targetSum - (Pesanan::where('jenis_pesanan', 'walk-in')->sum('total_harga'))) : $baseSlice;
            $pes = Pesanan::create([
                'nama_pemesan' => $names[$i % count($names)].' ('.($i + 10).')',
                'tipe_pelanggan' => 'Walk-in Pelanggan',
                'judul_dokumen' => 'Penjualan ATK & Jasa Cetak',
                'spesifikasi' => 'Fotokopi / ATK / Cetak Nota #W-'.(1000 + $i),
                'jenis_pesanan' => 'walk-in',
                'total_harga' => $val,
                'status_pesanan' => 'selesai',
                'metode_pembayaran' => $methods[$i % count($methods)],
                'waktu_pesan' => Carbon::today()->setHour(8)->setMinute(15)->addMinutes($i * 10),
            ]);

            DetailPesanan::create([
                'pesanan_id' => $pes->pesanan_id,
                'produk_id' => $firstProd,
                'kuantitas' => 1,
                'sub_total' => $val,
            ]);
        }

        // Synchronize monthly omset
        $this->syncMonthlyOmset();

        return redirect()->route('admin.walkin.index')
            ->with('success', 'Data Demo Kasir Walk-in berhasil dimuat: 34 Transaksi • Omset Rp 1.482.500 (Sesuai Referensi Gambar).');
    }

    /**
     * Clear Walk-in orders back to pristine Empty State.
     */
    public function clearWalkInDemo(): RedirectResponse
    {
        Pesanan::where('jenis_pesanan', 'walk-in')->delete();
        $this->syncMonthlyOmset();

        return redirect()->route('admin.walkin.index')
            ->with('success', 'Semua transaksi walk-in telah dikosongkan. Kondisi Empty State aktif (0 Transaksi, Rp 0).');
    }

    /**
     * Ensure standard categorized products exist in database for the POS catalog.
     */
    public function ensureCatalogProductsExist(): void
    {
        $standards = [
            // Fotokopi & Print
            ['nama_item' => 'Fotokopi A4 H/P (Dokumen)', 'kategori' => 'Fotokopi & Print', 'harga_dasar' => 250],
            ['nama_item' => 'Print Warna A4 (Tinta Pigment)', 'kategori' => 'Fotokopi & Print', 'harga_dasar' => 1000],
            ['nama_item' => 'Fotokopi F4 / Folio H/P', 'kategori' => 'Fotokopi & Print', 'harga_dasar' => 300],
            ['nama_item' => 'Print Dokumen A4 H/P', 'kategori' => 'Fotokopi & Print', 'harga_dasar' => 500],

            // Alat Tulis & Map
            ['nama_item' => 'Stopmap Folio Diamond (Biru)', 'kategori' => 'Alat Tulis & Map', 'harga_dasar' => 3500],
            ['nama_item' => 'Stopmap Folio Diamond (Merah/Kuning)', 'kategori' => 'Alat Tulis & Map', 'harga_dasar' => 3500],
            ['nama_item' => 'Materai Tempel 10.000 Fisik', 'kategori' => 'Alat Tulis & Map', 'harga_dasar' => 12000],
            ['nama_item' => 'Pulpen Faster C600 Hitam', 'kategori' => 'Alat Tulis & Map', 'harga_dasar' => 4500],
            ['nama_item' => 'Kertas HVS A4 70gr (1 Rim)', 'kategori' => 'Alat Tulis & Map', 'harga_dasar' => 48000],
            ['nama_item' => 'Amplop Coklat Tali Folio', 'kategori' => 'Alat Tulis & Map', 'harga_dasar' => 3000],

            // Jilid & Finishing
            ['nama_item' => 'Jilid Lakban Mika + Karton Navy', 'kategori' => 'Jilid & Finishing', 'harga_dasar' => 5000],
            ['nama_item' => 'Jilid Spiral Kawat + Mika', 'kategori' => 'Jilid & Finishing', 'harga_dasar' => 10000],
            ['nama_item' => 'Laminating Panas A4/F4 Presisi', 'kategori' => 'Jilid & Finishing', 'harga_dasar' => 5000],

            // Jasa & Lainnya
            ['nama_item' => 'Scan Dokumen / PDF (High-Res)', 'kategori' => 'Jasa & Lainnya', 'harga_dasar' => 1500],
            ['nama_item' => 'Ketik Dokumen / Edit Naskah', 'kategori' => 'Jasa & Lainnya', 'harga_dasar' => 10000],
        ];

        foreach ($standards as $item) {
            ProdukLayanan::firstOrCreate(
                ['nama_item' => $item['nama_item']],
                [
                    'kategori' => $item['kategori'],
                    'harga_dasar' => $item['harga_dasar'],
                    'status_aktif' => true,
                ]
            );
        }
    }

    /**
     * Synchronize monthly omset aggregation into laporan_omzet table.
     */
    public function syncMonthlyOmset(?string $periode = null): LaporanOmzet
    {
        $periode = $periode ?? now()->format('Y-m');
        $date = Carbon::createFromFormat('Y-m', $periode);

        $totalOffline = (float) Pesanan::where('jenis_pesanan', 'walk-in')
            ->where('status_pesanan', '!=', 'dibatalkan')
            ->whereYear('waktu_pesan', $date->year)
            ->whereMonth('waktu_pesan', $date->month)
            ->sum('total_harga');

        $totalOnline = (float) Pesanan::where('jenis_pesanan', 'online')
            ->where('status_pesanan', '!=', 'dibatalkan')
            ->whereYear('waktu_pesan', $date->year)
            ->whereMonth('waktu_pesan', $date->month)
            ->sum('total_harga');

        return LaporanOmzet::updateOrCreate(
            ['periode' => $periode],
            [
                'total_pendapatan_offline' => $totalOffline,
                'total_pendapatan_online' => $totalOnline,
                'tanggal_generate' => now(),
            ]
        );
    }

    /**
     * Display the Manajemen Bahan & Stok Gerai page.
     */
    public function stokIndex(Request $request): View
    {
        $user = Auth::user();
        $user->load('adminKasir');

        $this->ensureInventoryItemsExist();

        $activeAntreanCount = AntreanMesin::where('status_antrean', '!=', 'selesai')->count();

        // Query items
        $query = InventarisStok::query();

        // Filters
        if ($request->filled('kategori') && $request->kategori !== 'Semua Kategori') {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('status') && $request->status !== 'Semua Status') {
            if ($request->status === 'Aman') {
                $query->whereColumn('jumlah_stok', '>', 'batas_minimum');
            } elseif ($request->status === 'Menipis') {
                $query->whereColumn('jumlah_stok', '<=', 'batas_minimum')->where('jumlah_stok', '>', 0);
            } elseif ($request->status === 'Kritis' || $request->status === 'Habis') {
                $query->where('jumlah_stok', '<=', 0);
            }
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('nama_bahan', 'like', "%{$search}%")
                    ->orWhere('kode_bahan', 'like', "%{$search}%")
                    ->orWhere('kategori', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('stok_id', 'asc')->paginate(8);

        // Overall inventory stats
        $allItems = InventarisStok::all();
        $totalItems = $allItems->count();
        $stokAman = $allItems->filter(fn ($i) => $i->jumlah_stok > $i->batas_minimum)->count();
        $stokMenipis = $allItems->filter(fn ($i) => $i->jumlah_stok <= $i->batas_minimum && $i->jumlah_stok > 0)->count();
        $stokKritis = $allItems->filter(fn ($i) => $i->jumlah_stok <= 0)->count();

        $persenAman = $totalItems > 0 ? round(($stokAman / $totalItems) * 100, 1) : 0;

        // Names of menipis items for cards
        $menipisNames = $allItems->filter(fn ($i) => $i->jumlah_stok <= $i->batas_minimum && $i->jumlah_stok > 0)
            ->take(3)
            ->map(fn ($i) => explode(' ', $i->nama_bahan)[1] ?? $i->nama_bahan)
            ->implode(', ');
        if (! $menipisNames) {
            $menipisNames = 'A4 70gr, Konica BK, Lakban';
        }

        $kritisItem = $allItems->filter(fn ($i) => $i->jumlah_stok <= 0)->first();
        $kritisSummary = $kritisItem ? "{$kritisItem->nama_bahan} (0 {$kritisItem->satuan})" : 'Kertas Kalkir A3 (0 Lembar)';

        // Critical item for red banner
        $criticalBannerItem = $allItems->filter(fn ($i) => $i->jumlah_stok <= $i->batas_minimum)->sortBy('jumlah_stok')->first();

        // Recent incoming delivery mutasi
        $recentMutasi = RiwayatBahanMasuk::orderBy('waktu_masuk', 'desc')->take(5)->get();

        $categories = ['Semua Kategori', 'Kertas', 'Kertas Khusus', 'Tinta & Toner', 'Bahan Jilid'];
        $statuses = ['Semua Status', 'Aman', 'Menipis', 'Kritis / Habis'];

        return view('admin.stok', compact(
            'user',
            'items',
            'totalItems',
            'stokAman',
            'stokMenipis',
            'stokKritis',
            'persenAman',
            'menipisNames',
            'kritisSummary',
            'criticalBannerItem',
            'recentMutasi',
            'activeAntreanCount',
            'categories',
            'statuses'
        ));
    }

    /**
     * Return live JSON data for stock management.
     */
    public function stokLiveData(Request $request): JsonResponse
    {
        $query = InventarisStok::query();

        if ($request->filled('kategori') && $request->kategori !== 'Semua Kategori') {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('status') && $request->status !== 'Semua Status') {
            if ($request->status === 'Aman') {
                $query->whereColumn('jumlah_stok', '>', 'batas_minimum');
            } elseif ($request->status === 'Menipis') {
                $query->whereColumn('jumlah_stok', '<=', 'batas_minimum')->where('jumlah_stok', '>', 0);
            } elseif ($request->status === 'Kritis' || $request->status === 'Habis') {
                $query->where('jumlah_stok', '<=', 0);
            }
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('nama_bahan', 'like', "%{$search}%")
                    ->orWhere('kode_bahan', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('stok_id', 'asc')->get()->map(function ($it) {
            return [
                'stok_id' => $it->stok_id,
                'kode_bahan' => $it->display_kode,
                'nama_bahan' => $it->nama_bahan,
                'gramatur_detail' => $it->gramatur_detail,
                'kategori' => $it->kategori,
                'jumlah_stok' => $it->jumlah_stok,
                'satuan' => $it->satuan,
                'batas_minimum' => $it->batas_minimum,
                'persentase_kapasitas' => $it->persentase_kapasitas,
                'status_stok' => $it->status_stok,
                'status_badge_class' => $it->status_badge_class,
                'terakhir_restock' => $it->terakhir_restock_formatted,
                'gambar_url' => $it->gambar_url,
            ];
        });

        $allItems = InventarisStok::all();
        $totalItems = $allItems->count();
        $stokAman = $allItems->filter(fn ($i) => $i->jumlah_stok > $i->batas_minimum)->count();
        $stokMenipis = $allItems->filter(fn ($i) => $i->jumlah_stok <= $i->batas_minimum && $i->jumlah_stok > 0)->count();
        $stokKritis = $allItems->filter(fn ($i) => $i->jumlah_stok <= 0)->count();

        return response()->json([
            'status' => 'success',
            'stats' => [
                'total' => $totalItems,
                'aman' => $stokAman,
                'menipis' => $stokMenipis,
                'kritis' => $stokKritis,
            ],
            'items' => $items,
        ]);
    }

    /**
     * Store incoming stock delivery (+ Catat Bahan Masuk).
     */
    public function storeBahanMasuk(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'stok_id' => 'required|exists:inventaris_stok,stok_id',
            'no_surat_jalan' => 'required|string|max:50',
            'jumlah_masuk' => 'required|integer|min:1',
            'satuan' => 'nullable|string|max:30',
            'mitra_supplier' => 'nullable|string|max:150',
        ]);

        $stok = InventarisStok::findOrFail($validated['stok_id']);
        $stok->increment('jumlah_stok', $validated['jumlah_masuk']);
        $stok->update(['terakhir_restock' => now()]);

        $mutasi = RiwayatBahanMasuk::create([
            'no_surat_jalan' => $validated['no_surat_jalan'],
            'stok_id' => $stok->stok_id,
            'nama_item' => $stok->nama_bahan,
            'spesifikasi' => $stok->gramatur_detail ?: ($stok->satuan.' Bahan Baku'),
            'jumlah_masuk' => $validated['jumlah_masuk'],
            'satuan' => $validated['satuan'] ?? $stok->satuan,
            'mitra_supplier' => $validated['mitra_supplier'] ?? 'CV Sinar Abadi Bintaro',
            'status_penerimaan' => 'Diterima',
            'waktu_masuk' => now(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => "Stok bahan {$stok->nama_bahan} berhasil ditambahkan sebanyak {$validated['jumlah_masuk']} {$stok->satuan}.",
                'stok' => $stok,
                'mutasi' => $mutasi,
            ]);
        }

        return redirect()->back()->with('success', "Stok bahan {$stok->nama_bahan} berhasil ditambah sebanyak {$validated['jumlah_masuk']} {$stok->satuan}.");
    }

    /**
     * Store new inventory item (+ Tambah Item Baru).
     */
    public function storeTambahItemBaru(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'kode_bahan' => 'required|string|max:50|unique:inventaris_stok,kode_bahan',
            'nama_bahan' => 'required|string|max:100',
            'gramatur_detail' => 'nullable|string|max:150',
            'kategori' => 'required|string|max:50',
            'jumlah_stok' => 'required|integer|min:0',
            'batas_minimum' => 'required|integer|min:1',
            'kapasitas_maksimal' => 'nullable|integer|min:1',
            'satuan' => 'required|string|max:30',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:5120',
        ]);

        $item = InventarisStok::create([
            'kode_bahan' => strtoupper($validated['kode_bahan']),
            'nama_bahan' => $validated['nama_bahan'],
            'gramatur_detail' => $validated['gramatur_detail'],
            'kategori' => $validated['kategori'],
            'jumlah_stok' => $validated['jumlah_stok'],
            'batas_minimum' => $validated['batas_minimum'],
            'kapasitas_maksimal' => $validated['kapasitas_maksimal'] ?? ($validated['batas_minimum'] * 3),
            'satuan' => $validated['satuan'],
            'terakhir_restock' => now(),
        ]);

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $ext = strtolower($file->getClientOriginalExtension());
            $code = strtolower(trim($validated['kode_bahan']));
            $filename = "{$code}.{$ext}";
            $destination = public_path('images/stok');
            if (! file_exists($destination)) {
                mkdir($destination, 0755, true);
            }
            $file->move($destination, $filename);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => "Item inventaris {$item->nama_bahan} ({$item->kode_bahan}) berhasil didaftarkan.",
                'item' => $item,
            ]);
        }

        return redirect()->back()->with('success', "Item inventaris {$item->nama_bahan} ({$item->kode_bahan}) berhasil didaftarkan.");
    }

    /**
     * Quick Add Stock from row action button (+ Stok).
     */
    public function quickAddStok(Request $request, InventarisStok $stok): JsonResponse|RedirectResponse
    {
        $jumlah = (int) $request->input('jumlah', 5);
        $stok->increment('jumlah_stok', $jumlah);
        $stok->update(['terakhir_restock' => now()]);

        $nextSj = 'SJ-'.date('Y-m').'-'.rand(10, 99);
        RiwayatBahanMasuk::create([
            'no_surat_jalan' => $nextSj,
            'stok_id' => $stok->stok_id,
            'nama_item' => $stok->nama_bahan,
            'spesifikasi' => $stok->gramatur_detail ?: ($stok->satuan.' Tambahan'),
            'jumlah_masuk' => $jumlah,
            'satuan' => $stok->satuan,
            'mitra_supplier' => 'CV Sinar Abadi Bintaro',
            'status_penerimaan' => 'Diterima',
            'waktu_masuk' => now(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => "Stok {$stok->nama_bahan} bertambah {$jumlah} {$stok->satuan}. Total sisa: {$stok->jumlah_stok} {$stok->satuan}.",
                'stok' => $stok,
            ]);
        }

        return redirect()->back()->with('success', "Stok {$stok->nama_bahan} bertambah {$jumlah} {$stok->satuan}. Total sisa: {$stok->jumlah_stok} {$stok->satuan}.");
    }

    /**
     * Ensure standard 18 inventory items and recent mutasi exist matching screenshot.
     */
    public function ensureInventoryItemsExist(): void
    {
        $standards = [
            [
                'kode_bahan' => 'K-A4-70',
                'nama_bahan' => 'Kertas HVS A4 70gr PaperOne',
                'gramatur_detail' => '210 × 297 mm • Putih Cerah',
                'kategori' => 'Kertas',
                'jumlah_stok' => 2,
                'batas_minimum' => 5,
                'kapasitas_maksimal' => 50,
                'satuan' => 'Rim',
                'terakhir_restock' => Carbon::now()->subDays(2),
            ],
            [
                'kode_bahan' => 'K-F4-75',
                'nama_bahan' => 'Kertas Folio F4 75gr Sinar Dunia',
                'gramatur_detail' => '215 × 330 mm • Nota & Dokumen Resmi',
                'kategori' => 'Kertas',
                'jumlah_stok' => 12,
                'batas_minimum' => 4,
                'kapasitas_maksimal' => 40,
                'satuan' => 'Rim',
                'terakhir_restock' => Carbon::now()->subDay(),
            ],
            [
                'kode_bahan' => 'K-A3-150',
                'nama_bahan' => 'Art Paper A3 150gr Glossy',
                'gramatur_detail' => '297 × 420 mm • Pamflet & Cover Skripsi',
                'kategori' => 'Kertas Khusus',
                'jumlah_stok' => 8,
                'batas_minimum' => 2,
                'kapasitas_maksimal' => 20,
                'satuan' => 'Rim',
                'terakhir_restock' => Carbon::now()->subDays(4),
            ],
            [
                'kode_bahan' => 'T-KON-BK',
                'nama_bahan' => 'Toner Konica Minolta TN-324 Black',
                'gramatur_detail' => 'Kartrid Bubuk • Mesin 02 Bizhub',
                'kategori' => 'Tinta & Toner',
                'jumlah_stok' => 1,
                'batas_minimum' => 2,
                'kapasitas_maksimal' => 10,
                'satuan' => 'Tabung',
                'terakhir_restock' => Carbon::now()->subDays(9),
            ],
            [
                'kode_bahan' => 'J-SPR-10',
                'nama_bahan' => 'Kawat Spiral No. 10 (1/2 inch)',
                'gramatur_detail' => 'Panjang A4 • Kapasitas 60-100 lembar',
                'kategori' => 'Bahan Jilid',
                'jumlah_stok' => 45,
                'batas_minimum' => 15,
                'kapasitas_maksimal' => 100,
                'satuan' => 'Batang',
                'terakhir_restock' => Carbon::now()->subDays(6),
            ],
            [
                'kode_bahan' => 'J-LKB-BK',
                'nama_bahan' => 'Lakban Jilid Hitam 2 Inch Nachi',
                'gramatur_detail' => 'Kain Cloth Tape Tebal • 48mm x 12m',
                'kategori' => 'Bahan Jilid',
                'jumlah_stok' => 3,
                'batas_minimum' => 5,
                'kapasitas_maksimal' => 25,
                'satuan' => 'Roll',
                'terakhir_restock' => Carbon::now()->subDays(12),
            ],
            [
                'kode_bahan' => 'K-KLK-A3',
                'nama_bahan' => 'Kertas Kalkir A3 Transparan',
                'gramatur_detail' => 'Tracing Paper • 80gsm Drafting',
                'kategori' => 'Kertas Khusus',
                'jumlah_stok' => 0,
                'batas_minimum' => 2,
                'kapasitas_maksimal' => 15,
                'satuan' => 'Lembar',
                'terakhir_restock' => Carbon::now()->subDays(19),
            ],
            [
                'kode_bahan' => 'K-BC-PUT',
                'nama_bahan' => 'Kertas Brief Card / Manila Putih',
                'gramatur_detail' => '160gsm • Cover Jilid & Kartu',
                'kategori' => 'Kertas Khusus',
                'jumlah_stok' => 15,
                'batas_minimum' => 5,
                'kapasitas_maksimal' => 30,
                'satuan' => 'Pak',
                'terakhir_restock' => Carbon::now()->subDays(3),
            ],
            [
                'kode_bahan' => 'T-RIC-CY',
                'nama_bahan' => 'Toner Ricoh Pro C5300 Cyan',
                'gramatur_detail' => 'Toner Digital Warna Premium',
                'kategori' => 'Tinta & Toner',
                'jumlah_stok' => 3,
                'batas_minimum' => 1,
                'kapasitas_maksimal' => 5,
                'satuan' => 'Tabung',
                'terakhir_restock' => Carbon::now()->subDays(5),
            ],
            [
                'kode_bahan' => 'T-RIC-MG',
                'nama_bahan' => 'Toner Ricoh Pro C5300 Magenta',
                'gramatur_detail' => 'Toner Digital Warna Premium',
                'kategori' => 'Tinta & Toner',
                'jumlah_stok' => 3,
                'batas_minimum' => 1,
                'kapasitas_maksimal' => 5,
                'satuan' => 'Tabung',
                'terakhir_restock' => Carbon::now()->subDays(5),
            ],
            [
                'kode_bahan' => 'T-RIC-YL',
                'nama_bahan' => 'Toner Ricoh Pro C5300 Yellow',
                'gramatur_detail' => 'Toner Digital Warna Premium',
                'kategori' => 'Tinta & Toner',
                'jumlah_stok' => 4,
                'batas_minimum' => 1,
                'kapasitas_maksimal' => 5,
                'satuan' => 'Tabung',
                'terakhir_restock' => Carbon::now()->subDays(5),
            ],
            [
                'kode_bahan' => 'J-MIK-01',
                'nama_bahan' => 'Mika Transparan Bening Jilid',
                'gramatur_detail' => 'Tebal 0.10mm • Cover Depan A4',
                'kategori' => 'Bahan Jilid',
                'jumlah_stok' => 20,
                'batas_minimum' => 5,
                'kapasitas_maksimal' => 50,
                'satuan' => 'Pak',
                'terakhir_restock' => Carbon::now()->subDays(10),
            ],
            [
                'kode_bahan' => 'J-KTN-NV',
                'nama_bahan' => 'Karton Manila Buffalo Navy',
                'gramatur_detail' => 'Cover Belakang Jilid Skripsi',
                'kategori' => 'Bahan Jilid',
                'jumlah_stok' => 18,
                'batas_minimum' => 5,
                'kapasitas_maksimal' => 40,
                'satuan' => 'Pak',
                'terakhir_restock' => Carbon::now()->subDays(8),
            ],
            [
                'kode_bahan' => 'K-HVS-A480',
                'nama_bahan' => 'Kertas HVS A4 80gr PaperOne',
                'gramatur_detail' => 'Premium Print Skripsi',
                'kategori' => 'Kertas',
                'jumlah_stok' => 10,
                'batas_minimum' => 3,
                'kapasitas_maksimal' => 30,
                'satuan' => 'Rim',
                'terakhir_restock' => Carbon::now()->subDays(7),
            ],
            [
                'kode_bahan' => 'K-GLS-4R',
                'nama_bahan' => 'Photo Paper Glossy 4R 230gr',
                'gramatur_detail' => 'Kertas Foto Kilap Instan',
                'kategori' => 'Kertas Khusus',
                'jumlah_stok' => 8,
                'batas_minimum' => 2,
                'kapasitas_maksimal' => 20,
                'satuan' => 'Pak',
                'terakhir_restock' => Carbon::now()->subDays(13),
            ],
            [
                'kode_bahan' => 'T-CAN-BK',
                'nama_bahan' => 'Tinta Botol Canon GI-790 Black',
                'gramatur_detail' => 'Tinta Pigment Cetak Cepat',
                'kategori' => 'Tinta & Toner',
                'jumlah_stok' => 6,
                'batas_minimum' => 2,
                'kapasitas_maksimal' => 15,
                'satuan' => 'Botol',
                'terakhir_restock' => Carbon::now()->subDays(11),
            ],
            [
                'kode_bahan' => 'J-LMN-A4',
                'nama_bahan' => 'Plastik Laminating Panas A4 100mic',
                'gramatur_detail' => 'Laminasi Ijazah & Sertifikat',
                'kategori' => 'Bahan Jilid',
                'jumlah_stok' => 14,
                'batas_minimum' => 3,
                'kapasitas_maksimal' => 30,
                'satuan' => 'Pak',
                'terakhir_restock' => Carbon::now()->subDays(6),
            ],
            [
                'kode_bahan' => 'J-HDC-A4',
                'nama_bahan' => 'Papan Hardcover No. 30 Tebal',
                'gramatur_detail' => 'Jilid Skripsi Lux Emas',
                'kategori' => 'Bahan Jilid',
                'jumlah_stok' => 25,
                'batas_minimum' => 5,
                'kapasitas_maksimal' => 50,
                'satuan' => 'Lembar',
                'terakhir_restock' => Carbon::now()->subDays(16),
            ],
            [
                'kode_bahan' => 'O-BNT-F4',
                'nama_bahan' => 'Ordner Bantex F4 7cm Tebal Biru',
                'gramatur_detail' => 'Kapasitas 500 Lembar • Mekanik Kuat',
                'kategori' => 'Ordner & Filing Document',
                'jumlah_stok' => 16,
                'batas_minimum' => 4,
                'kapasitas_maksimal' => 40,
                'satuan' => 'Pcs',
                'terakhir_restock' => Carbon::now()->subDays(3),
            ],
            [
                'kode_bahan' => 'P-ZBR-KOK',
                'nama_bahan' => 'Zebra Kokoro Gel Pen 0.5mm (1 Pack)',
                'gramatur_detail' => 'Isi 12 Pcs • Tinta Gel Hitam Cepat Kering',
                'kategori' => 'Alat Tulis & Koreksi',
                'jumlah_stok' => 22,
                'batas_minimum' => 5,
                'kapasitas_maksimal' => 50,
                'satuan' => 'Pack',
                'terakhir_restock' => Carbon::now()->subDays(2),
            ],
            [
                'kode_bahan' => 'L-DMR-CKL',
                'nama_bahan' => 'Lakban Coklat Daimaru 48mm x 90y',
                'gramatur_detail' => 'Daya Rekat Ekstra Kuat untuk Packing',
                'kategori' => 'Perlengkapan Packing & Jilid',
                'jumlah_stok' => 35,
                'batas_minimum' => 8,
                'kapasitas_maksimal' => 60,
                'satuan' => 'Roll',
                'terakhir_restock' => Carbon::now()->subDays(5),
            ],
            [
                'kode_bahan' => 'J-SPR-04',
                'nama_bahan' => 'Ring Kawat Spiral No. 4 & Mika Biru',
                'gramatur_detail' => 'Jilid Kalender & Proposal A4',
                'kategori' => 'Perlengkapan Packing & Jilid',
                'jumlah_stok' => 40,
                'batas_minimum' => 10,
                'kapasitas_maksimal' => 80,
                'satuan' => 'Batang',
                'terakhir_restock' => Carbon::now()->subDays(4),
            ],
            [
                'kode_bahan' => 'A-STP-KNG',
                'nama_bahan' => 'Stapler Heavy Duty Kangaro HD-23S17',
                'gramatur_detail' => 'Staples Tebal Jilid hingga 160 Lembar',
                'kategori' => 'Alat Tulis & Koreksi',
                'jumlah_stok' => 5,
                'batas_minimum' => 2,
                'kapasitas_maksimal' => 15,
                'satuan' => 'Unit',
                'terakhir_restock' => Carbon::now()->subDays(7),
            ],
        ];

        foreach ($standards as $item) {
            InventarisStok::updateOrCreate(
                ['kode_bahan' => $item['kode_bahan']],
                $item
            );
        }

        // Clean up any placeholder stock rows with missing kode_bahan so total is cleanly 18
        InventarisStok::whereNull('kode_bahan')->delete();

        // Ensure recent delivery logs exist
        if (RiwayatBahanMasuk::count() < 5) {
            $deliveries = [
                [
                    'no_surat_jalan' => 'SJ-2024-10-88',
                    'nama_item' => 'HVS Folio F4 75gr',
                    'spesifikasi' => 'Sinar Dunia • 15 Rim',
                    'jumlah_masuk' => 15,
                    'satuan' => 'Rim',
                    'mitra_supplier' => 'CV Sinar Abadi Bintaro',
                    'status_penerimaan' => 'Diterima',
                    'waktu_masuk' => Carbon::now()->subDay()->setHour(10)->setMinute(15),
                ],
                [
                    'no_surat_jalan' => 'SJ-2024-10-82',
                    'nama_item' => 'Art Paper A3 150gr',
                    'spesifikasi' => 'Glossy • 10 Rim',
                    'jumlah_masuk' => 10,
                    'satuan' => 'Rim',
                    'mitra_supplier' => 'CV Sinar Abadi Bintaro',
                    'status_penerimaan' => 'Diterima',
                    'waktu_masuk' => Carbon::now()->subDays(4)->setHour(14)->setMinute(40),
                ],
                [
                    'no_surat_jalan' => 'SJ-2024-10-77',
                    'nama_item' => 'Spiral Kawat #10',
                    'spesifikasi' => 'A4 Box • 50 Batang',
                    'jumlah_masuk' => 50,
                    'satuan' => 'Batang',
                    'mitra_supplier' => 'CV Sinar Abadi Bintaro',
                    'status_penerimaan' => 'Diterima',
                    'waktu_masuk' => Carbon::now()->subDays(6)->setHour(9)->setMinute(12),
                ],
                [
                    'no_surat_jalan' => 'SJ-2024-10-65',
                    'nama_item' => 'Toner TN-324 BK',
                    'spesifikasi' => 'Konica • 2 Tabung',
                    'jumlah_masuk' => 2,
                    'satuan' => 'Tabung',
                    'mitra_supplier' => 'CV Sinar Abadi Bintaro',
                    'status_penerimaan' => 'Diterima',
                    'waktu_masuk' => Carbon::now()->subDays(9)->setHour(11)->setMinute(30),
                ],
                [
                    'no_surat_jalan' => 'SJ-2024-10-54',
                    'nama_item' => 'Mika Bening Jilid',
                    'spesifikasi' => 'Tebal 0.10mm • 5 Pak',
                    'jumlah_masuk' => 5,
                    'satuan' => 'Pak',
                    'mitra_supplier' => 'CV Sinar Abadi Bintaro',
                    'status_penerimaan' => 'Diterima',
                    'waktu_masuk' => Carbon::now()->subDays(10)->setHour(16)->setMinute(5),
                ],
            ];

            foreach ($deliveries as $deliv) {
                RiwayatBahanMasuk::firstOrCreate(
                    ['no_surat_jalan' => $deliv['no_surat_jalan']],
                    $deliv
                );
            }
        }
    }

    /**
     * Display Data Member & Program Hadiah Kunjungan.
     */
    public function memberIndex(Request $request): View
    {
        $user = Auth::user();
        $user->load('adminKasir');

        $activeAntreanCount = AntreanMesin::where('status_antrean', '!=', 'selesai')->count();

        $tab = $request->input('tab', 'semua');
        $search = $request->input('q');
        $sort = $request->input('sort', 'kunjungan_desc');

        $query = Pelanggan::with('user');

        // Tab category filtering
        if ($tab === 'mahasiswa') {
            $query->where('kategori_member', 'Mahasiswa UPJ/STAN');
        } elseif ($tab === 'instansi') {
            $query->where('kategori_member', 'Instansi/Dosen');
        } elseif ($tab === 'umum') {
            $query->where('kategori_member', 'Member Umum');
        } elseif ($tab === 'siap_klaim') {
            $query->where(function ($q) {
                $q->where(function ($sub) {
                    $sub->where('total_kunjungan', '>=', 3)->where('hadiah_terklaim_count', '<', 1);
                })->orWhere(function ($sub) {
                    $sub->where('total_kunjungan', '>=', 5)->where('hadiah_terklaim_count', '<', 2);
                })->orWhere(function ($sub) {
                    $sub->where('total_kunjungan', '>=', 10)->where('hadiah_terklaim_count', '<', 3);
                })->orWhere(function ($sub) {
                    $sub->where('total_kunjungan', '>=', 20)->where('hadiah_terklaim_count', '<', 4);
                });
            });
        }

        // Search filtering
        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_member', 'like', "%{$search}%")
                    ->orWhere('instansi_detail', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('nama', 'like', "%{$search}%")
                            ->orWhere('nomor_telepon', 'like', "%{$search}%");
                    });
            });
        }

        // Sorting
        if ($sort === 'kunjungan_desc') {
            $query->orderBy('total_kunjungan', 'desc');
        } elseif ($sort === 'poin_desc') {
            $query->orderBy('saldo_poin', 'desc');
        } elseif ($sort === 'belanja_desc') {
            $query->orderBy('total_belanja', 'desc');
        } else {
            $query->orderBy('pelanggan_id', 'asc');
        }

        $members = $query->paginate(7)->withQueryString();

        // Calculate KPI Metrics dynamically across real registered members
        $allMembers = Pelanggan::all();
        $totalMemberAktif = $allMembers->count();
        $hadiahTerklaim = LogKlaimHadiah::count() + (int) $allMembers->sum('hadiah_terklaim_count');
        $memberPalingLoyal = $allMembers->filter(fn ($m) => $m->total_kunjungan >= 10)->count();
        $totalPoinBeredar = (int) $allMembers->sum('saldo_poin');

        // Category counts
        $countSemua = $allMembers->count();
        $countMahasiswa = $allMembers->where('kategori_member', 'Mahasiswa UPJ/STAN')->count();
        $countInstansi = $allMembers->where('kategori_member', 'Instansi/Dosen')->count();
        $countUmum = $allMembers->where('kategori_member', 'Member Umum')->count();
        $countSiapKlaim = $allMembers->filter(function ($m) {
            $k = $m->total_kunjungan;
            $h = $m->hadiah_terklaim_count;

            return ($k >= 3 && $h < 1) || ($k >= 5 && $h < 2) || ($k >= 10 && $h < 3) || ($k >= 20 && $h < 4);
        })->count();

        // Today's claim logs
        $todayClaims = LogKlaimHadiah::orderBy('waktu_klaim', 'desc')->take(6)->get();

        return view('admin.member', compact(
            'user',
            'members',
            'tab',
            'search',
            'sort',
            'totalMemberAktif',
            'hadiahTerklaim',
            'memberPalingLoyal',
            'totalPoinBeredar',
            'countSemua',
            'countMahasiswa',
            'countInstansi',
            'countUmum',
            'countSiapKlaim',
            'todayClaims',
            'activeAntreanCount'
        ));
    }

    /**
     * Store a new registered member (+ Daftarkan Member Baru).
     */
    public function storeMember(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'nomor_telepon' => 'required|string|max:30',
            'kategori_member' => 'required|string|max:50',
            'instansi_detail' => 'nullable|string|max:150',
            'badge_label' => 'nullable|string|max:50',
            'saldo_poin' => 'nullable|integer|min:0',
        ]);

        $user = User::firstOrCreate(
            ['nomor_telepon' => $validated['nomor_telepon']],
            [
                'nama' => $validated['nama'],
                'email' => 'member_'.time().'@mataharifotocopy.com',
                'password' => Hash::make('12345'),
                'role' => 'user',
            ]
        );

        $nextCode = 'MTH-'.str_pad((string) (Pelanggan::count() + 1), 4, '0', STR_PAD_LEFT);

        $pelanggan = Pelanggan::create([
            'user_id' => $user->user_id,
            'kode_member' => $nextCode,
            'kategori_member' => $validated['kategori_member'],
            'instansi_detail' => $validated['instansi_detail'] ?? $validated['kategori_member'],
            'badge_label' => $validated['badge_label'] ?? strtoupper(substr($validated['kategori_member'], 0, 10)),
            'saldo_poin' => $validated['saldo_poin'] ?? 50,
            'total_kunjungan' => 1,
            'total_belanja' => 0,
            'loyalty_tier' => 'Pemula',
            'hadiah_terklaim_count' => 0,
            'terakhir_kunjungan' => now(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => "Member {$user->nama} ({$pelanggan->kode_member}) berhasil didaftarkan.",
                'member' => $pelanggan->load('user'),
            ]);
        }

        return redirect()->back()->with('success', "Member {$user->nama} ({$pelanggan->kode_member}) berhasil didaftarkan.");
    }

    /**
     * Claim milestone or point reward for member.
     */
    public function storeKlaimHadiah(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'pelanggan_id' => 'required|exists:pelanggan,pelanggan_id',
            'nama_hadiah' => 'required|string|max:150',
            'tipe_klaim' => 'required|string|max:100',
            'poin_tukar' => 'nullable|integer|min:0',
        ]);

        $pelanggan = Pelanggan::with('user')->findOrFail($validated['pelanggan_id']);

        if (! empty($validated['poin_tukar']) && $validated['poin_tukar'] > 0) {
            $deduct = min($pelanggan->saldo_poin, $validated['poin_tukar']);
            $pelanggan->decrement('saldo_poin', $deduct);
        }

        $pelanggan->increment('hadiah_terklaim_count');

        $notaRef = '#MTH-'.rand(8900, 9999);
        $log = LogKlaimHadiah::create([
            'pelanggan_id' => $pelanggan->pelanggan_id,
            'nama_member' => $pelanggan->user?->nama ?? 'Member',
            'nama_hadiah' => $validated['nama_hadiah'],
            'tipe_klaim' => $validated['tipe_klaim'],
            'nama_kasir' => Auth::user()->nama ?? 'Feri Anto',
            'no_nota' => $notaRef,
            'waktu_klaim' => now(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => "Klaim {$validated['nama_hadiah']} untuk {$pelanggan->user?->nama} berhasil dicatat (Nota {$notaRef}).",
                'log' => $log,
            ]);
        }

        return redirect()->back()->with('success', "Klaim {$validated['nama_hadiah']} untuk {$pelanggan->user?->nama} berhasil dicatat (Nota {$notaRef}).");
    }

    /**
     * Barcode & ID quick lookup via AJAX.
     */
    public function scanMemberAjax(Request $request): JsonResponse
    {
        $code = trim((string) $request->input('code'));
        if (empty($code)) {
            return response()->json(['status' => 'error', 'message' => 'Kode member / No. HP tidak boleh kosong.'], 400);
        }

        $member = Pelanggan::with('user')
            ->where('kode_member', 'like', "%{$code}%")
            ->orWhereHas('user', function ($q) use ($code) {
                $q->where('nomor_telepon', 'like', "%{$code}%")
                    ->orWhere('nama', 'like', "%{$code}%");
            })
            ->first();

        if (! $member) {
            return response()->json(['status' => 'not_found', 'message' => "Member dengan ID/HP \"{$code}\" tidak ditemukan."], 404);
        }

        return response()->json([
            'status' => 'success',
            'member' => [
                'pelanggan_id' => $member->pelanggan_id,
                'kode_member' => $member->display_kode_member,
                'nama' => $member->user?->nama,
                'telepon' => $member->user?->nomor_telepon,
                'kategori' => $member->kategori_member,
                'instansi' => $member->instansi_detail,
                'poin' => $member->saldo_poin,
                'kunjungan' => $member->total_kunjungan,
                'belanja' => 'Rp '.number_format($member->total_belanja, 0, ',', '.'),
                'progress' => $member->progress_info,
            ],
        ]);
    }

    /**
     * Ensure 7 standard reference members and today claim logs exist.
     */
    public function ensureMembersExist(): void
    {
        if (Pelanggan::count() < 7) {
            $referenceData = [
                [
                    'nama' => 'Pak Hendra Wijaya',
                    'telepon' => '0812-9844-3109',
                    'email' => 'hendra.upj@example.com',
                    'kode_member' => 'MTH-0142',
                    'kategori_member' => 'Instansi/Dosen',
                    'instansi_detail' => 'Dosen Arsitektur',
                    'badge_label' => 'DOSEN UPJ',
                    'total_kunjungan' => 14,
                    'total_belanja' => 845000,
                    'saldo_poin' => 620,
                    'loyalty_tier' => 'Emas',
                    'hadiah_terklaim_count' => 3,
                ],
                [
                    'nama' => 'Nadia Putri',
                    'telepon' => '0857-1120-9941',
                    'email' => 'nadia.putri@example.com',
                    'kode_member' => 'MTH-0782',
                    'kategori_member' => 'Mahasiswa UPJ/STAN',
                    'instansi_detail' => 'Mahasiswi S1',
                    'badge_label' => 'UPJ BINTARO',
                    'total_kunjungan' => 8,
                    'total_belanja' => 315000,
                    'saldo_poin' => 230,
                    'loyalty_tier' => 'Reguler',
                    'hadiah_terklaim_count' => 1,
                ],
                [
                    'nama' => 'Dimas Prasetyo',
                    'telepon' => '0813-8902-1277',
                    'email' => 'dimas.stan@example.com',
                    'kode_member' => 'MTH-0821',
                    'kategori_member' => 'Mahasiswa UPJ/STAN',
                    'instansi_detail' => 'Tingkat Akhir',
                    'badge_label' => 'PKN STAN',
                    'total_kunjungan' => 10,
                    'total_belanja' => 520000,
                    'saldo_poin' => 490,
                    'loyalty_tier' => 'Emas',
                    'hadiah_terklaim_count' => 2,
                ],
                [
                    'nama' => 'Budi Santoso',
                    'telepon' => '0811-923-441',
                    'email' => 'notaris.budi@example.com',
                    'kode_member' => 'MTH-0012',
                    'kategori_member' => 'Instansi/Dosen',
                    'instansi_detail' => 'Kantor Notaris Bintaro',
                    'badge_label' => 'VIP PLATINUM',
                    'total_kunjungan' => 22,
                    'total_belanja' => 2450000,
                    'saldo_poin' => 1820,
                    'loyalty_tier' => 'VIP Platinum',
                    'hadiah_terklaim_count' => 3,
                ],
                [
                    'nama' => 'Siti Nurhaliza',
                    'telepon' => '0878-4390-1922',
                    'email' => 'siti.upj@example.com',
                    'kode_member' => 'MTH-0904',
                    'kategori_member' => 'Mahasiswa UPJ/STAN',
                    'instansi_detail' => 'Mahasiswa UPJ',
                    'badge_label' => 'MAHASISWA UPJ',
                    'total_kunjungan' => 4,
                    'total_belanja' => 145000,
                    'saldo_poin' => 95,
                    'loyalty_tier' => 'Pemula',
                    'hadiah_terklaim_count' => 1,
                ],
                [
                    'nama' => 'Anisa Rahma',
                    'telepon' => '0813-2289-0144',
                    'email' => 'anisa.rahma@example.com',
                    'kode_member' => 'MTH-1102',
                    'kategori_member' => 'Member Umum',
                    'instansi_detail' => 'Umum Sektor 3',
                    'badge_label' => 'WARGA BINTARO',
                    'total_kunjungan' => 3,
                    'total_belanja' => 78000,
                    'saldo_poin' => 65,
                    'loyalty_tier' => 'Pemula',
                    'hadiah_terklaim_count' => 0,
                ],
                [
                    'nama' => 'Rizky Kurniawan',
                    'telepon' => '0895-3411-8890',
                    'email' => 'rizky.stan@example.com',
                    'kode_member' => 'MTH-0551',
                    'kategori_member' => 'Mahasiswa UPJ/STAN',
                    'instansi_detail' => 'Mahasiswa Akuntansi',
                    'badge_label' => 'PKN STAN',
                    'total_kunjungan' => 7,
                    'total_belanja' => 410000,
                    'saldo_poin' => 310,
                    'loyalty_tier' => 'Reguler',
                    'hadiah_terklaim_count' => 1,
                ],
            ];

            foreach ($referenceData as $m) {
                $user = User::firstOrCreate(
                    ['email' => $m['email']],
                    [
                        'nama' => $m['nama'],
                        'nomor_telepon' => $m['telepon'],
                        'password' => Hash::make('12345'),
                        'role' => 'user',
                    ]
                );

                Pelanggan::firstOrCreate(
                    ['kode_member' => $m['kode_member']],
                    [
                        'user_id' => $user->user_id,
                        'kategori_member' => $m['kategori_member'],
                        'instansi_detail' => $m['instansi_detail'],
                        'badge_label' => $m['badge_label'],
                        'total_kunjungan' => $m['total_kunjungan'],
                        'total_belanja' => $m['total_belanja'],
                        'saldo_poin' => $m['saldo_poin'],
                        'loyalty_tier' => $m['loyalty_tier'],
                        'hadiah_terklaim_count' => $m['hadiah_terklaim_count'],
                        'terakhir_kunjungan' => Carbon::now()->subHours(rand(1, 10)),
                    ]
                );
            }
        }

        // Preseed today's claim logs if empty
        if (LogKlaimHadiah::count() < 3) {
            $claims = [
                [
                    'nama_member' => 'Fauzan Ramadhan',
                    'nama_hadiah' => 'Voucher Cetak Rp 25.000',
                    'tipe_klaim' => 'Milestone 10x',
                    'nama_kasir' => 'Feri Anto',
                    'no_nota' => 'Nota #MTH-9021',
                    'waktu_klaim' => Carbon::today()->setHour(14)->setMinute(12),
                ],
                [
                    'nama_member' => 'Bu Retno Kusuma',
                    'nama_hadiah' => 'Gratis Jilid Lakban Mika (2 Eks)',
                    'tipe_klaim' => 'Milestone 5x',
                    'nama_kasir' => 'Feri Anto',
                    'no_nota' => 'Nota #MTH-9018',
                    'waktu_klaim' => Carbon::today()->setHour(13)->setMinute(45),
                ],
                [
                    'nama_member' => 'Gilang Permana (STAN)',
                    'nama_hadiah' => '1 Lusin Pulpen Pilot BP-1',
                    'tipe_klaim' => 'Tukar 250 Poin',
                    'nama_kasir' => 'Feri Anto',
                    'no_nota' => 'Nota #MTH-8994',
                    'waktu_klaim' => Carbon::today()->setHour(11)->setMinute(20),
                ],
            ];

            foreach ($claims as $c) {
                LogKlaimHadiah::firstOrCreate(
                    ['no_nota' => $c['no_nota']],
                    $c
                );
            }
        }
    }

    /**
     * Display Laporan Penjualan & Analitik Gerai.
     */
    public function laporanIndex(Request $request): View
    {
        $user = Auth::user();
        $user->load('adminKasir');

        $activeAntreanCount = AntreanMesin::where('status_antrean', '!=', 'selesai')->count();
        $rentang = $request->input('rentang', '7_hari');

        $startDate = Carbon::today()->subDays(6)->startOfDay();

        // Dynamic KPIs calculated from actual database
        $totalOmzet7Hari = (float) Pesanan::where('waktu_pesan', '>=', $startDate)->where('status_pesanan', '!=', 'dibatalkan')->sum('total_harga');
        $totalNotaPesanan = Pesanan::where('waktu_pesan', '>=', $startDate)->where('status_pesanan', '!=', 'dibatalkan')->count();
        $volumeKertas = (int) DetailPesanan::whereHas('pesanan', fn ($q) => $q->where('waktu_pesan', '>=', $startDate)->where('status_pesanan', '!=', 'dibatalkan'))->sum('kuantitas');
        $rataRataTransaksi = $totalNotaPesanan > 0 ? round($totalOmzet7Hari / $totalNotaPesanan) : 0;
        $highestNota = (float) (Pesanan::where('waktu_pesan', '>=', $startDate)->where('status_pesanan', '!=', 'dibatalkan')->max('total_harga') ?? 0);
        $totalShiftSaatIni = (float) Pesanan::whereDate('waktu_pesan', Carbon::today())->where('status_pesanan', '!=', 'dibatalkan')->sum('total_harga');

        // Dynamic 7-day revenue trend
        $trend7Hari = [];
        $maxDaily = 0;
        $dailyBuckets = [];

        for ($i = 0; $i < 7; $i++) {
            $dt = Carbon::today()->subDays(6 - $i);
            $dayTotal = (float) Pesanan::whereDate('waktu_pesan', $dt)->where('status_pesanan', '!=', 'dibatalkan')->sum('total_harga');
            if ($dayTotal > $maxDaily) {
                $maxDaily = $dayTotal;
            }
            $dailyBuckets[] = [
                'date' => $dt,
                'total' => $dayTotal,
            ];
        }

        foreach ($dailyBuckets as $b) {
            $dt = $b['date'];
            $total = $b['total'];
            $hariNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
            $hariLabel = $hariNames[$dt->dayOfWeek].' ('.$dt->format('d').')';
            $tglLabel = $dt->translatedFormat('d M');
            $height = $maxDaily > 0 ? max(10, (int) round(($total / $maxDaily) * 100)) : 0;

            if ($total >= 1000000) {
                $label = round($total / 1000000, 2).'Jt';
            } elseif ($total >= 1000) {
                $label = round($total / 1000).'Rb';
            } else {
                $label = $total > 0 ? 'Rp '.number_format($total, 0, ',', '.') : '0';
            }

            $trend7Hari[] = [
                'hari' => $hariLabel,
                'tanggal' => $tglLabel,
                'total' => $total,
                'label' => $label,
                'height' => $height,
                'is_peak' => ($maxDaily > 0 && $total === $maxDaily),
            ];
        }

        // Dynamic service breakdown
        $catsConfig = [
            ['nama' => 'Fotokopi & Print B/W', 'match' => 'Fotokopi', 'color' => '#F59E0B'],
            ['nama' => 'Print Warna & Brosur', 'match' => 'Warna', 'color' => '#3B82F6'],
            ['nama' => 'Penjilidan Skripsi/Spiral', 'match' => 'Jilid', 'color' => '#64748B'],
            ['nama' => 'ATK, Map & Scan Dokumen', 'match' => 'Alat Tulis', 'color' => '#D97706'],
        ];

        $serviceBreakdown = [];
        foreach ($catsConfig as $cat) {
            $val = (float) DetailPesanan::whereHas('pesanan', fn ($q) => $q->where('waktu_pesan', '>=', $startDate)->where('status_pesanan', '!=', 'dibatalkan'))
                ->whereHas('produkLayanan', fn ($q) => $q->where('kategori', 'like', '%'.$cat['match'].'%')->orWhere('nama_item', 'like', '%'.$cat['match'].'%'))
                ->sum('sub_total');

            $pct = $totalOmzet7Hari > 0 ? (int) round(($val / $totalOmzet7Hari) * 100) : 0;
            $serviceBreakdown[] = [
                'nama' => $cat['nama'],
                'persen' => $pct,
                'nilai' => $val,
                'label_nilai' => $val >= 1000000 ? 'Rp '.round($val / 1000000, 2).'Jt' : 'Rp '.number_format($val, 0, ',', '.'),
                'color' => $cat['color'],
            ];
        }

        // Hourly analytics distribution (database-agnostic for both MySQL and SQLite)
        $todayOrders = Pesanan::whereDate('waktu_pesan', Carbon::today())->get();
        $hours = ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '19:00', '20:00'];
        $hourlyDistribution = [];
        foreach ($hours as $h) {
            $hourNum = (int) substr($h, 0, 2);
            $hCount = $todayOrders->filter(fn ($o) => $o->waktu_pesan && $o->waktu_pesan->hour === $hourNum)->count();
            $load = $hCount >= 5 ? 'peak' : ($hCount >= 2 ? 'medium' : 'low');
            $hourlyDistribution[] = [
                'jam' => $h,
                'load' => $load,
                'is_peak' => ($load === 'peak'),
            ];
        }

        // Query orders for recent transaction logs (5 per page)
        $orders = Pesanan::orderBy('waktu_pesan', 'desc')->paginate(5)->withQueryString();

        return view('admin.laporan', compact(
            'user',
            'orders',
            'rentang',
            'totalOmzet7Hari',
            'totalNotaPesanan',
            'volumeKertas',
            'rataRataTransaksi',
            'highestNota',
            'totalShiftSaatIni',
            'trend7Hari',
            'serviceBreakdown',
            'hourlyDistribution',
            'activeAntreanCount'
        ));
    }

    /**
     * Export reports to CSV/Excel format.
     */
    public function laporanExport(Request $request): StreamedResponse
    {
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=Laporan_Omzet_Matahari_Fotocopy_'.date('Ymd_His').'.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No. Nota', 'Waktu & Tanggal', 'Nama Pelanggan', 'Layanan & Spesifikasi', 'Metode Bayar', 'Total (Rp)', 'Kasir']);

            $orders = Pesanan::orderBy('waktu_pesan', 'desc')->get();
            foreach ($orders as $o) {
                fputcsv($file, [
                    $o->nomor_nota,
                    $o->waktu_pesan ? $o->waktu_pesan->format('d M Y H:i') : '-',
                    $o->display_name,
                    $o->display_spesifikasi,
                    $o->metode_pembayaran ?? 'Tunai',
                    (int) $o->total_harga,
                    $o->kasir_nama ?? 'Feri Anto',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Ensure 7-day realistic order transactions exist matching screenshot.
     */
    public function ensureLaporanOrdersExist(): void
    {
        if (Pesanan::count() < 10) {
            $refOrders = [
                [
                    'nomor_nota_kustom' => '#MTR-2410-098',
                    'nama_pemesan' => 'Ananda Rizky (UPJ)',
                    'tipe_pelanggan' => 'Mahasiswa UPJ',
                    'judul_dokumen' => 'Print Skripsi + Jilid Hardcover',
                    'spesifikasi' => '180 Lembar A4 • Kertas 80gr • Tinta Hitam',
                    'jenis_pesanan' => 'walk-in',
                    'total_harga' => 84000,
                    'status_pesanan' => 'selesai',
                    'metode_pembayaran' => 'QRIS Statis',
                    'kasir_nama' => 'Feri Anto',
                    'volume_halaman' => 180,
                    'kategori_layanan' => 'Penjilidan Skripsi/Spiral',
                    'waktu_pesan' => Carbon::today()->setHour(14)->setMinute(28),
                ],
                [
                    'nomor_nota_kustom' => '#MTR-2410-097',
                    'nama_pemesan' => 'PT Tri Dharma Mandiri',
                    'tipe_pelanggan' => 'Instansi',
                    'judul_dokumen' => 'Brosur Promosi Full Color',
                    'spesifikasi' => '250 Lembar A4 • Art Paper 150gr • Lipat 3',
                    'jenis_pesanan' => 'walk-in',
                    'total_harga' => 375000,
                    'status_pesanan' => 'selesai',
                    'metode_pembayaran' => 'Debit BCA',
                    'kasir_nama' => 'Feri Anto',
                    'volume_halaman' => 250,
                    'kategori_layanan' => 'Print Warna & Brosur',
                    'waktu_pesan' => Carbon::today()->setHour(14)->setMinute(15),
                ],
                [
                    'nomor_nota_kustom' => '#MTR-2410-096',
                    'nama_pemesan' => 'Dimas Prakoso',
                    'tipe_pelanggan' => 'PKN STAN',
                    'judul_dokumen' => 'Fotokopi Modul Kuliah STAN',
                    'spesifikasi' => '45 Lembar F4 • Bolak-balik • Jilid Lakban',
                    'jenis_pesanan' => 'walk-in',
                    'total_harga' => 22500,
                    'status_pesanan' => 'selesai',
                    'metode_pembayaran' => 'Tunai Pas',
                    'kasir_nama' => 'Feri Anto',
                    'volume_halaman' => 45,
                    'kategori_layanan' => 'Fotokopi & Print B/W',
                    'waktu_pesan' => Carbon::today()->setHour(13)->setMinute(50),
                ],
                [
                    'nomor_nota_kustom' => '#MTR-2410-095',
                    'nama_pemesan' => 'Siti Nurhaliza',
                    'tipe_pelanggan' => 'Mahasiswa UPJ',
                    'judul_dokumen' => 'Scan Berkas CPNS + Flashdisk',
                    'spesifikasi' => '12 Dokumen • 600 DPI PDF • Burn Arsip',
                    'jenis_pesanan' => 'walk-in',
                    'total_harga' => 18000,
                    'status_pesanan' => 'selesai',
                    'metode_pembayaran' => 'QRIS Shopee',
                    'kasir_nama' => 'Feri Anto',
                    'volume_halaman' => 12,
                    'kategori_layanan' => 'ATK, Map & Scan Dokumen',
                    'waktu_pesan' => Carbon::today()->setHour(13)->setMinute(35),
                ],
                [
                    'nomor_nota_kustom' => '#MTR-2410-094',
                    'nama_pemesan' => 'Bpk. Hendro (Kelurahan)',
                    'tipe_pelanggan' => 'Instansi',
                    'judul_dokumen' => 'Print Gambar Denah A3 Poster',
                    'spesifikasi' => '8 Lembar A3+ • Matte Photo • High Resolution',
                    'jenis_pesanan' => 'walk-in',
                    'total_harga' => 64000,
                    'status_pesanan' => 'selesai',
                    'metode_pembayaran' => 'Tunai',
                    'kasir_nama' => 'Feri Anto',
                    'volume_halaman' => 8,
                    'kategori_layanan' => 'Print Warna & Brosur',
                    'waktu_pesan' => Carbon::today()->setHour(13)->setMinute(10),
                ],
            ];

            foreach ($refOrders as $ord) {
                Pesanan::firstOrCreate(
                    ['nomor_nota_kustom' => $ord['nomor_nota_kustom']],
                    $ord
                );
            }
        }
    }
}
