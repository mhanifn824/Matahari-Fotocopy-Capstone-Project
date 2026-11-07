<?php

namespace Tests\Feature;

use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\ProdukLayanan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TimezoneTest extends TestCase
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
            'nama' => 'Rian Pratama',
            'email' => 'rian@example.com',
            'password' => Hash::make('12345'),
            'nomor_telepon' => '0812-9876-5432',
            'role' => 'user',
        ]);

        Pelanggan::create([
            'user_id' => $this->customer->user_id,
            'saldo_poin' => 500,
        ]);

        $this->product = ProdukLayanan::create([
            'nama_item' => 'Print Dokumen A4',
            'kategori' => 'Jasa Cetak',
            'harga_dasar' => 500,
            'status_aktif' => true,
        ]);
    }

    public function test_application_configured_with_asia_jakarta_timezone(): void
    {
        $this->assertSame('Asia/Jakarta', config('app.timezone'));
        $this->assertSame('Asia/Jakarta', date_default_timezone_get());
        $this->assertSame('+07:00', now()->format('P'));
    }

    public function test_walkin_checkout_records_waktu_pesan_in_wib(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-07 14:35:00', 'Asia/Jakarta'));

        $payload = [
            'nama_pemesan' => 'Budi Santoso',
            'tipe_pelanggan' => 'Umum',
            'items' => [
                [
                    'nama_item' => 'Print Dokumen A4',
                    'kuantitas' => 10,
                    'harga_satuan' => 500,
                    'sub_total' => 5000,
                ],
            ],
            'subtotal' => 5000,
            'diskon' => 0,
            'total_harga' => 5000,
            'metode_pembayaran' => 'TUNAI',
            'uang_diterima' => 5000,
            'kembalian' => 0,
        ];

        $response = $this->actingAs($this->admin)->postJson(route('admin.walkin.checkout'), $payload);
        $response->assertOk();

        $pesanan = Pesanan::where('nama_pemesan', 'Budi Santoso')->firstOrFail();
        $this->assertSame('14:35 WIB', $pesanan->waktu_transaksi_format);

        // Verify in Laporan Omzet
        $laporanResponse = $this->actingAs($this->admin)->get(route('admin.laporan.index'));
        $laporanResponse->assertOk();
        $laporanResponse->assertSee('14:35 WIB');

        Carbon::setTestNow();
    }

    public function test_customer_online_order_records_waktu_pesan_and_tenggat_in_wib(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-07 09:15:00', 'Asia/Jakarta'));

        $payload = [
            'judul_dokumen' => 'Laporan Akhir Magang',
            'spesifikasi' => 'A4 HVS 70gr Hitam Putih',
            'produk_id' => $this->product->produk_id,
            'kuantitas' => 1,
            'jumlah_halaman' => 20,
            'metode_pembayaran' => 'qris',
        ];

        $response = $this->actingAs($this->customer)->post(route('customer.order.store'), $payload);
        $response->assertRedirect();

        $pesanan = Pesanan::where('judul_dokumen', 'Laporan Akhir Magang')->firstOrFail();
        $this->assertSame('10:00 WIB', $pesanan->tenggat_waktu);

        // Verify in Customer Portal
        $customerHome = $this->actingAs($this->customer)->get(route('customer.home'));
        $customerHome->assertOk();
        $customerHome->assertSee('07 Nov 09:15 WIB');

        // Verify in Admin Pembelian Online
        $adminAntrean = $this->actingAs($this->admin)->get(route('admin.antrean.index'));
        $adminAntrean->assertOk();
        $adminAntrean->assertSee('09:15 WIB');

        Carbon::setTestNow();
    }
}
