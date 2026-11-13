<?php

namespace Tests\Feature;

use App\Models\Pesanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminLaporanTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $customer;

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
            'nama' => 'Rian Pratama',
            'email' => 'rian@example.com',
            'password' => Hash::make('12345'),
            'nomor_telepon' => '0812-9876-5432',
            'role' => 'user',
        ]);
    }

    public function test_admin_laporan_page_renders_successfully_for_authenticated_admin(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.index'));

        $response->assertStatus(200);
        $response->assertSee('Laporan Penjualan &amp; Analitik Gerai', false);
        $response->assertSee('Rekapitulasi pendapatan dan tren transaksi Bintaro');
        $response->assertDontSee('Bintaro harian');
        $response->assertDontSee('performa layanan, volume cetak');
        $response->assertDontSee('CABANG BINTARO SEKTOR 3');
        $response->assertSee('TOTAL PESANAN');
        $response->assertDontSee('TOTAL OMZET 7 HARI');
        $response->assertSee('TOTAL NOTA PESANAN');
        $response->assertDontSee('Komposisi Layanan');
        $response->assertDontSee('Log pesanan walk-in real-time loket Bintaro');
        $response->assertDontSee('Halaman 1 dari 37');
        $response->assertDontSee('Showing');
        $response->assertSee('Tren Pendapatan 7 Hari');
        $response->assertSee('pesanan');
        $response->assertSee('Transaksi Terakhir Gerai');
        $response->assertSee('Hari Ini');
        $response->assertSee('Rentang:');
        $response->assertSee('Ekspor Laporan Excel (.xlsx)');
        $response->assertSee('Unduh Rekap PDF');
    }

    public function test_admin_laporan_shows_empty_notice_when_no_orders_found(): void
    {
        // Query future date with zero orders
        $futureDate = now()->addYears(2)->format('Y-m-d');
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.index', [
            'tanggal_mulai' => $futureDate,
            'tanggal_akhir' => $futureDate,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Tidak ada transaksi atau pembelian pada tanggal tersebut.');
    }

    public function test_admin_laporan_filters_by_hari_ini_and_date_range(): void
    {
        // 1. Transaction today (walk-in)
        Pesanan::create([
            'nama_pemesan' => 'Budi Walk-In',
            'jenis_pesanan' => 'walk-in',
            'total_harga' => 50000,
            'status_pesanan' => 'selesai',
            'metode_pembayaran' => 'TUNAI',
            'waktu_pesan' => now(),
        ]);

        // 2. Transaction 10 days ago (online)
        Pesanan::create([
            'nama_pemesan' => 'Siti Online',
            'jenis_pesanan' => 'online',
            'total_harga' => 75000,
            'status_pesanan' => 'selesai',
            'metode_pembayaran' => 'QRIS',
            'waktu_pesan' => now()->subDays(10),
        ]);

        // Filter: Hari Ini
        $todayResponse = $this->actingAs($this->admin)->get(route('admin.laporan.index', ['rentang' => 'hari_ini']));
        $todayResponse->assertStatus(200);
        $todayResponse->assertSee('Rp 50.000');
        $todayResponse->assertDontSee('Siti Online');

        // Filter: Custom date range covering 10 days ago
        $rangeResponse = $this->actingAs($this->admin)->get(route('admin.laporan.index', [
            'tanggal_mulai' => now()->subDays(12)->format('Y-m-d'),
            'tanggal_akhir' => now()->subDays(8)->format('Y-m-d'),
        ]));
        $rangeResponse->assertStatus(200);
        $rangeResponse->assertSee('Rp 75.000');
        $rangeResponse->assertSee('Siti Online');
        $rangeResponse->assertDontSee('Budi Walk-In');

        // Default: All transactions accumulated without double count
        $allResponse = $this->actingAs($this->admin)->get(route('admin.laporan.index'));
        $allResponse->assertStatus(200);
        $allResponse->assertSee('Rp 125.000');
    }

    public function test_admin_can_access_dedicated_transaksi_terakhir_gerai_page(): void
    {
        Pesanan::create([
            'nomor_nota_kustom' => '#MTR-WALK-1001',
            'nama_pemesan' => 'Andi Walk-in Gerai',
            'jenis_pesanan' => 'walk-in',
            'total_harga' => 35000,
            'status_pesanan' => 'selesai',
            'metode_pembayaran' => 'TUNAI',
            'waktu_pesan' => now(),
        ]);

        Pesanan::create([
            'nomor_nota_kustom' => '#MTR-ONL-2002',
            'nama_pemesan' => 'Dewi Order Online',
            'jenis_pesanan' => 'online',
            'total_harga' => 60000,
            'status_pesanan' => 'selesai',
            'metode_pembayaran' => 'QRIS',
            'waktu_pesan' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.laporan.transaksi'));

        $response->assertStatus(200);
        $response->assertSee('Transaksi Terakhir Gerai');
        $response->assertSee('Cari nomor nota atau nama pelanggan...');
        $response->assertSee('Andi Walk-in Gerai');
        $response->assertSee('Dewi Order Online');
        $response->assertSee('WALK-IN');
        $response->assertSee('ONLINE');
    }

    public function test_admin_transaksi_gerai_search_by_nota_and_nama_and_empty_state(): void
    {
        Pesanan::create([
            'nomor_nota_kustom' => '#NOTA-A101',
            'nama_pemesan' => 'Bambang Sudirman',
            'jenis_pesanan' => 'walk-in',
            'total_harga' => 25000,
            'status_pesanan' => 'selesai',
            'metode_pembayaran' => 'TUNAI',
            'waktu_pesan' => now(),
        ]);

        Pesanan::create([
            'nomor_nota_kustom' => '#NOTA-B202',
            'nama_pemesan' => 'Citra Lestari',
            'jenis_pesanan' => 'online',
            'total_harga' => 80000,
            'status_pesanan' => 'selesai',
            'metode_pembayaran' => 'QRIS',
            'waktu_pesan' => now(),
        ]);

        // 1. Search by nomor nota
        $notaResponse = $this->actingAs($this->admin)->get(route('admin.laporan.transaksi', ['q' => '#NOTA-A101']));
        $notaResponse->assertStatus(200);
        $notaResponse->assertSee('#NOTA-A101');
        $notaResponse->assertSee('Bambang Sudirman');
        $notaResponse->assertDontSee('Citra Lestari');

        // 2. Search by nama pelanggan
        $namaResponse = $this->actingAs($this->admin)->get(route('admin.laporan.transaksi', ['q' => 'Citra']));
        $namaResponse->assertStatus(200);
        $namaResponse->assertSee('Citra Lestari');
        $namaResponse->assertSee('#NOTA-B202');
        $namaResponse->assertDontSee('Bambang Sudirman');

        // 3. Search combined with filter jenis
        $combinedResponse = $this->actingAs($this->admin)->get(route('admin.laporan.transaksi', [
            'q' => 'Citra',
            'jenis' => 'online',
        ]));
        $combinedResponse->assertStatus(200);
        $combinedResponse->assertSee('Citra Lestari');

        // 4. Data tidak ditemukan
        $notFoundResponse = $this->actingAs($this->admin)->get(route('admin.laporan.transaksi', ['q' => 'NamaTidakAdaXYZ']));
        $notFoundResponse->assertStatus(200);
        $notFoundResponse->assertSee('Tidak Ada Transaksi Ditemukan');
        $notFoundResponse->assertSee('Tidak ada transaksi yang cocok dengan kriteria pencarian atau filter yang dipilih.');
    }

    public function test_guest_and_non_admin_cannot_access_admin_laporan(): void
    {
        $guestResponse = $this->get(route('admin.laporan.index'));
        $guestResponse->assertRedirect(route('login'));

        $userResponse = $this->actingAs($this->customer)->get(route('admin.laporan.index'));
        $userResponse->assertRedirect(route('customer.dashboard'));
    }

    public function test_admin_can_export_laporan_csv(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.export'));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment; filename=Laporan_Omzet_Matahari_Fotocopy_', (string) $response->headers->get('Content-Disposition'));
    }

    public function test_admin_laporan_rejects_filter_and_shows_notice_when_start_date_is_greater_than_end_date(): void
    {
        Pesanan::create([
            'nama_pemesan' => 'Pesanan Hari Ini',
            'jenis_pesanan' => 'walk-in',
            'total_harga' => 50000,
            'status_pesanan' => 'selesai',
            'metode_pembayaran' => 'TUNAI',
            'waktu_pesan' => now(),
        ]);

        // Input tanggal mulai > tanggal akhir
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.index', [
            'tanggal_mulai' => now()->addDays(5)->format('Y-m-d'),
            'tanggal_akhir' => now()->format('Y-m-d'),
        ]));

        $response->assertStatus(200);
        $response->assertSee('Rentang tanggal tidak valid. Tanggal mulai tidak boleh lebih besar dari tanggal akhir. Silakan masukkan tanggal yang benar.');
        // Filter ditolak dan sistem tidak memfilter rentang tidak valid (tetap menampilkan total pesanan)
        $response->assertSee('Rp 50.000');
    }

    public function test_admin_transaksi_gerai_rejects_filter_and_shows_notice_when_start_date_is_greater_than_end_date(): void
    {
        Pesanan::create([
            'nama_pemesan' => 'Pesanan Gerai Valid',
            'jenis_pesanan' => 'walk-in',
            'total_harga' => 45000,
            'status_pesanan' => 'selesai',
            'metode_pembayaran' => 'TUNAI',
            'waktu_pesan' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.laporan.transaksi', [
            'tanggal_mulai' => now()->addDays(7)->format('Y-m-d'),
            'tanggal_akhir' => now()->format('Y-m-d'),
        ]));

        $response->assertStatus(200);
        $response->assertSee('Rentang tanggal tidak valid. Tanggal mulai tidak boleh lebih besar dari tanggal akhir. Silakan masukkan tanggal yang benar.');
        // Transaksi tidak difilter dengan rentang yang salah
        $response->assertSee('Pesanan Gerai Valid');
    }
}
