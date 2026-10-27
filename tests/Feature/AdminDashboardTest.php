<?php

namespace Tests\Feature;

use App\Models\AntreanMesin;
use App\Models\BerkasDokumen;
use App\Models\DetailPesanan;
use App\Models\InventarisStok;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\ProdukLayanan;
use App\Models\ProgramLoyalitas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $customer;

    protected ProdukLayanan $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'nama' => 'Feri Anto (Admin)',
            'email' => 'admin@123',
            'password' => Hash::make('12345'),
            'nomor_telepon' => '081234567890',
            'role' => 'admin',
        ]);

        $this->customer = User::create([
            'nama' => 'Bagas Wicaksono',
            'email' => 'user@321',
            'password' => Hash::make('12345'),
            'nomor_telepon' => '081234567899',
            'role' => 'user',
        ]);

        Pelanggan::create([
            'user_id' => $this->customer->user_id,
            'saldo_poin' => 250,
        ]);

        $this->product = ProdukLayanan::create([
            'nama_item' => 'Cetak A4 Hitam Putih',
            'kategori' => 'Jasa Cetak',
            'harga_dasar' => 300,
            'status_aktif' => true,
        ]);

        InventarisStok::create([
            'nama_bahan' => 'Kertas HVS A4 70gr',
            'jumlah_stok' => 50,
            'batas_minimum' => 5,
            'satuan' => 'Rim',
        ]);
    }

    public function test_admin_dashboard_renders_in_empty_state_initially(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard Operasional Gerai');
        $response->assertSee('PESANAN HARI INI');
        $response->assertSee('PENDAPATAN SHIFT');
        $response->assertSee('ANTREAN AKTIF');
        $response->assertSee('BAHAN KRITIS');
        $response->assertSee('Tren Omzet 7 Hari Terakhir');
        $response->assertSee('Metode Pembayaran');
        $response->assertSee('KONSUMSI BAHAN HARI INI');
        $response->assertSee('Antrean Berjalan di Gerai');
        $response->assertSee('Belum Ada Antrean Pesanan Berjalan');
        $response->assertSee('0 Antrean Berjalan');
    }

    public function test_live_data_api_returns_json_structure(): void
    {
        $response = $this->actingAs($this->admin)->getJson(route('admin.dashboard.live-data'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'timestamp',
            'stats' => [
                'pesanan_hari_ini',
                'pesanan_selesai',
                'pesanan_proses',
                'pendapatan_shift',
                'target_shift',
                'persentase_pendapatan',
                'antrean_aktif',
                'est_tunggu',
                'bahan_kritis_count',
                'pembayaran' => ['qris', 'tunai', 'transfer', 'qris_percent'],
                'konsumsi' => ['kertas_hvs_rim', 'jilid_buku'],
                'tren_7_hari',
            ],
            'activeQueues',
        ]);
    }

    public function test_cashier_can_create_walk_in_order(): void
    {
        $payload = [
            'nama_pemesan' => 'Bagas Wicaksono',
            'tipe_pelanggan' => '⭐ Member Gold',
            'judul_dokumen' => 'Skripsi Bab 1-5 Final',
            'spesifikasi' => 'A4 80g • B/W Bolak-balik • Jilid Softcover',
            'produk_id' => $this->product->produk_id,
            'kuantitas' => 150,
            'total_harga' => 85000,
            'metode_pembayaran' => 'qris',
            'status_antrean' => 'cetak',
            'estimasi_waktu' => 15,
        ];

        $response = $this->actingAs($this->admin)->postJson(route('admin.dashboard.walk-in'), $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
        ]);

        $this->assertDatabaseHas('pesanan', [
            'nama_pemesan' => 'Bagas Wicaksono',
            'total_harga' => 85000,
            'metode_pembayaran' => 'qris',
        ]);

        $this->assertDatabaseHas('antrean_mesin', [
            'status_antrean' => 'cetak',
        ]);
    }

    public function test_cashier_can_update_queue_status(): void
    {
        $pesanan = Pesanan::create([
            'nama_pemesan' => 'Bagas Wicaksono',
            'judul_dokumen' => 'Skripsi Bab 1-5',
            'spesifikasi' => 'A4 80g',
            'jenis_pesanan' => 'walk-in',
            'total_harga' => 50000,
            'status_pesanan' => 'proses',
            'metode_pembayaran' => 'qris',
            'waktu_pesan' => now(),
        ]);

        $antrean = AntreanMesin::create([
            'pesanan_id' => $pesanan->pesanan_id,
            'nomor_tiket' => 1,
            'status_antrean' => 'cetak',
            'estimasi_waktu' => 15,
        ]);

        $response = $this->actingAs($this->admin)->postJson(route('admin.dashboard.queue.status', $antrean), [
            'status' => 'selesai',
        ]);

        $response->assertStatus(200);
        $this->assertSame('selesai', $antrean->fresh()->status_antrean);
        $this->assertSame('selesai', $pesanan->fresh()->status_pesanan);
    }

    public function test_customer_order_creates_queue_visible_to_admin(): void
    {
        $response = $this->actingAs($this->customer)->postJson(route('customer.order.store'), [
            'judul_dokumen' => 'Modul Praktikum Kimia',
            'spesifikasi' => 'A4 70g • Full Color',
            'produk_id' => $this->product->produk_id,
            'kuantitas' => 50,
            'metode_pembayaran' => 'qris',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('pesanan', [
            'judul_dokumen' => 'Modul Praktikum Kimia',
            'jenis_pesanan' => 'online',
        ]);

        // Check admin live data sees the newly created order
        $adminResponse = $this->actingAs($this->admin)->getJson(route('admin.dashboard.live-data'));
        $adminResponse->assertStatus(200);
        $adminResponse->assertJsonFragment([
            'judul_dokumen' => 'Modul Praktikum Kimia',
        ]);
    }

    public function test_demo_seed_and_clear_cycle(): void
    {
        // Seed demo
        $seedResponse = $this->actingAs($this->admin)->post(route('admin.dashboard.seed-demo'));
        $seedResponse->assertRedirect();

        $this->assertGreaterThan(0, Pesanan::count());
        $this->assertGreaterThan(0, AntreanMesin::count());

        // Clear demo
        $clearResponse = $this->actingAs($this->admin)->post(route('admin.dashboard.clear-demo'));
        $clearResponse->assertRedirect();

        $this->assertSame(0, Pesanan::count());
        $this->assertSame(0, AntreanMesin::count());
    }

    public function test_antrean_mesin_page_renders_in_empty_state_initially(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.antrean.index'));

        $response->assertStatus(200);
        $response->assertSee('Manajemen Antrean Mesin', false);
        $response->assertSee('MESIN KONICA 1 • HEAVY');
        $response->assertSee('MESIN RICOH 2 • DIGITAL');
        $response->assertSee('CANON 3 / KASIR A');
        $response->assertSee('Standby • Siap Order');
        $response->assertSee('Belum Ada Antrean Mesin Berjalan');
    }

    public function test_antrean_live_data_returns_workshop_metrics(): void
    {
        $response = $this->actingAs($this->admin)->getJson(route('admin.antrean.live-data'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'timestamp',
            'workshopData' => [
                'total',
                'diterima',
                'proses',
                'siap',
                'machines' => ['konica', 'ricoh', 'canon'],
            ],
            'queues',
        ]);
    }

    public function test_action_updates_antrean_alur_status(): void
    {
        $pesanan = Pesanan::create([
            'nama_pemesan' => 'Kevin Sanjaya',
            'judul_dokumen' => 'Diktat Kuliah',
            'spesifikasi' => 'A4 B/W',
            'jenis_pesanan' => 'walk-in',
            'total_harga' => 45000,
            'status_pesanan' => 'proses',
            'metode_pembayaran' => 'qris',
            'waktu_pesan' => now(),
        ]);

        $antrean = AntreanMesin::create([
            'pesanan_id' => $pesanan->pesanan_id,
            'nomor_tiket' => 5,
            'status_antrean' => 'menunggu',
            'estimasi_waktu' => 30,
            'status_alur' => 'Diterima',
        ]);

        $response = $this->actingAs($this->admin)->postJson(route('admin.antrean.action', $antrean), [
            'action' => 'proses_cetak',
        ]);

        $response->assertStatus(200);
        $this->assertSame('Sedang Cetak', $antrean->fresh()->status_alur);
        $this->assertSame('cetak', $antrean->fresh()->status_antrean);
    }

    public function test_customer_order_parses_pdf_pages_and_recalculates_total_harga(): void
    {
        Storage::fake('local');

        $pdfContent = "%PDF-1.4\n"
            ."1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj\n"
            ."2 0 obj << /Type /Pages /Kids [3 0 R 4 0 R 5 0 R 6 0 R] /Count 4 >> endobj\n"
            ."3 0 obj << /Type /Page /Parent 2 0 R >> endobj\n"
            ."4 0 obj << /Type /Page /Parent 2 0 R >> endobj\n"
            ."5 0 obj << /Type /Page /Parent 2 0 R >> endobj\n"
            ."6 0 obj << /Type /Page /Parent 2 0 R >> endobj\n"
            ."%%EOF";

        $pdfFile = UploadedFile::fake()->createWithContent('skripsi_4_halaman.pdf', $pdfContent);

        // 4 halaman * Rp 300 * 10 rangkap = Rp 12.000
        $response = $this->actingAs($this->customer)->postJson(route('customer.order.store'), [
            'judul_dokumen' => 'Skripsi 4 Halaman',
            'spesifikasi' => 'A4 70g • Hitam Putih',
            'produk_id' => $this->product->produk_id,
            'kuantitas' => 10,
            'metode_pembayaran' => 'qris',
            'berkas' => $pdfFile,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('berkas_dokumen', [
            'nama_file' => 'skripsi_4_halaman.pdf',
            'jumlah_halaman' => 4,
        ]);

        $this->assertDatabaseHas('pesanan', [
            'judul_dokumen' => 'Skripsi 4 Halaman',
            'volume_halaman' => 4,
            'total_harga' => 12000,
        ]);

        // Rp 12.000 => 10 loyalty points added to initial 250 = 260
        $this->assertSame(260, $this->customer->pelanggan->fresh()->saldo_poin);
        $this->assertDatabaseHas('program_loyalitas', [
            'pelanggan_id' => $this->customer->pelanggan->pelanggan_id,
            'jumlah_poin_masuk' => 10,
        ]);
    }

    public function test_completing_queue_deducts_inventory_stock_and_awards_loyalty_points(): void
    {
        $pelanggan = $this->customer->pelanggan;

        $pesanan = Pesanan::create([
            'pelanggan_id' => $pelanggan->pelanggan_id,
            'nama_pemesan' => $this->customer->nama,
            'judul_dokumen' => 'Modul Pelatihan Besar',
            'spesifikasi' => 'Kertas HVS A4 70gr • B/W',
            'jenis_pesanan' => 'online',
            'volume_halaman' => 250,
            'total_harga' => 35000,
            'status_pesanan' => 'proses',
            'metode_pembayaran' => 'qris',
            'waktu_pesan' => now(),
        ]);

        DetailPesanan::create([
            'pesanan_id' => $pesanan->pesanan_id,
            'produk_id' => $this->product->produk_id,
            'kuantitas' => 4, // 250 * 4 = 1000 lembar = 2 Rim
            'sub_total' => 35000,
        ]);

        $antrean = AntreanMesin::create([
            'pesanan_id' => $pesanan->pesanan_id,
            'nomor_tiket' => 9,
            'status_antrean' => 'cetak',
            'estimasi_waktu' => 20,
            'status_alur' => 'Sedang Cetak',
        ]);

        $response = $this->actingAs($this->admin)->postJson(route('admin.antrean.action', $antrean), [
            'action' => 'selesai',
        ]);

        $response->assertStatus(200);

        $stokA4 = InventarisStok::where('nama_bahan', 'Kertas HVS A4 70gr')->first();
        $this->assertSame(48, $stokA4->jumlah_stok); // 50 Rim - 2 Rim = 48 Rim

        // Rp 35.000 => 30 loyalty points added to initial 250 = 280
        $this->assertSame(280, $pelanggan->fresh()->saldo_poin);
        $this->assertDatabaseHas('program_loyalitas', [
            'pelanggan_id' => $pelanggan->pelanggan_id,
            'jumlah_poin_masuk' => 30,
        ]);
    }

    public function test_admin_can_download_customer_uploaded_berkas_dokumen(): void
    {
        Storage::fake('local');

        $file = UploadedFile::fake()->createWithContent('dokumen_cetak.pdf', '%PDF-1.4 dummy content');
        $storedPath = $file->store('berkas_pelanggan', 'local');

        $berkas = BerkasDokumen::create([
            'pelanggan_id' => $this->customer->pelanggan->pelanggan_id,
            'nama_file' => 'dokumen_cetak.pdf',
            'file_path' => $storedPath,
            'ukuran_file' => 22,
            'jumlah_halaman' => 1,
            'status_validasi' => 'valid (300 DPI)',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.berkas.download', $berkas->berkas_id));

        $response->assertStatus(200);
        $response->assertDownload('dokumen_cetak.pdf');
    }

    public function test_handover_serahkan_action_auto_purges_uploaded_berkas_for_uu_pdp_compliance(): void
    {
        Storage::fake('local');

        $file = UploadedFile::fake()->createWithContent('rahasia_skripsi.pdf', '%PDF-1.4 confidential content');
        $storedPath = $file->store('berkas_pelanggan', 'local');

        $berkas = BerkasDokumen::create([
            'pelanggan_id' => $this->customer->pelanggan->pelanggan_id,
            'nama_file' => 'rahasia_skripsi.pdf',
            'file_path' => $storedPath,
            'ukuran_file' => 28,
            'jumlah_halaman' => 1,
            'status_validasi' => 'valid (300 DPI)',
        ]);

        $pesanan = Pesanan::create([
            'pelanggan_id' => $this->customer->pelanggan->pelanggan_id,
            'nama_pemesan' => $this->customer->nama,
            'judul_dokumen' => 'Skripsi Rahasia',
            'spesifikasi' => 'A4 70g',
            'jenis_pesanan' => 'online',
            'volume_halaman' => 1,
            'total_harga' => 3000,
            'status_pesanan' => 'proses',
            'metode_pembayaran' => 'qris',
            'waktu_pesan' => now(),
        ]);

        DetailPesanan::create([
            'pesanan_id' => $pesanan->pesanan_id,
            'produk_id' => $this->product->produk_id,
            'berkas_id' => $berkas->berkas_id,
            'kuantitas' => 10,
            'sub_total' => 3000,
        ]);

        $antrean = AntreanMesin::create([
            'pesanan_id' => $pesanan->pesanan_id,
            'nomor_tiket' => 11,
            'status_antrean' => 'selesai',
            'estimasi_waktu' => 5,
            'status_alur' => 'Siap Diambil',
        ]);

        Storage::disk('local')->assertExists($storedPath);

        $response = $this->actingAs($this->admin)->postJson(route('admin.antrean.action', $antrean), [
            'action' => 'serahkan',
        ]);

        $response->assertStatus(200);
        Storage::disk('local')->assertMissing($storedPath);
        $this->assertSame('terhapus otomatis (UU PDP)', $berkas->fresh()->status_validasi);
    }

    public function test_admin_can_store_and_update_tarif_layanan(): void
    {
        $storeResponse = $this->actingAs($this->admin)->postJson(route('admin.tarif.store'), [
            'nama_item' => 'Cetak Poster A3+ Art Carton',
            'kategori' => 'Jasa Cetak & Reproduksi',
            'harga_dasar' => 7500,
            'satuan_barang' => 'Lembar',
        ]);

        $storeResponse->assertStatus(200);
        $this->assertDatabaseHas('produk_layanan', [
            'nama_item' => 'Cetak Poster A3+ Art Carton',
            'harga_dasar' => 7500,
        ]);

        $produk = ProdukLayanan::where('nama_item', 'Cetak Poster A3+ Art Carton')->firstOrFail();

        $updateResponse = $this->actingAs($this->admin)->postJson(route('admin.tarif.update', $produk), [
            'harga_dasar' => 8000,
        ]);

        $updateResponse->assertStatus(200);
        $this->assertSame('8000.00', (string) $produk->fresh()->harga_dasar);
    }

    public function test_class_diagram_domain_model_methods(): void
    {
        $berkas = new BerkasDokumen(['nama_file' => 'dokumen.pdf', 'jumlah_halaman' => 5]);
        $this->assertTrue($berkas->validasiFormat());
        $this->assertSame(5, $berkas->parseJumlahHalaman());

        $pesanan = new Pesanan();
        $this->assertSame(15000.0, $pesanan->hitungTotalBiaya(5, 300.0, 10));

        $antrean = new AntreanMesin(['nomor_tiket' => 7]);
        $this->assertSame('A-07', $antrean->generateTiket());

        $stok = InventarisStok::where('nama_bahan', 'Kertas HVS A4 70gr')->firstOrFail();
        $this->assertFalse($stok->cekLowStockAlert());
        $stok->potongStok(500); // 1 Rim
        $this->assertSame(49, $stok->fresh()->jumlah_stok);
    }
}
