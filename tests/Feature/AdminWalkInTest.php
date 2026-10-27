<?php

namespace Tests\Feature;

use App\Models\DetailPesanan;
use App\Models\LaporanOmzet;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminWalkInTest extends TestCase
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

        Pelanggan::create([
            'user_id' => $this->customer->user_id,
            'saldo_poin' => 500,
        ]);
    }

    public function test_walkin_pos_page_renders_successfully_for_authenticated_admin(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.walkin.index'));

        $response->assertStatus(200);
        $response->assertSee('Kasir POS Walk-in');
        $response->assertSee('Loket A05 • Kasir Buka');
        $response->assertSee('No. Nota #W-');
        $response->assertSee('Transaksi Walk-In Kasir Hari Ini');
        $response->assertSee('Pilihan Cepat Metode Pembayaran', false);
    }

    public function test_walkin_pos_starts_in_pristine_empty_state_with_zero_omset(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.walkin.index'));

        $response->assertStatus(200);
        $response->assertSee('Belum Ada Transaksi Walk-In Kasir Hari Ini');
        $response->assertSee('Rp 0');
        $response->assertSee('(0 Transaksi)');
    }

    public function test_walkin_live_data_endpoint_returns_json_metrics(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.walkin.live-data'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'todayOmset',
            'todayOmsetFormatted',
            'todayCount',
            'nextNotaCode',
            'activeAntreanCount',
            'orders',
        ]);
        $response->assertJson([
            'status' => 'success',
            'todayCount' => 0,
            'todayOmset' => 0,
        ]);
    }

    public function test_walkin_checkout_creates_pesanan_details_and_synchronizes_laporan_omzet(): void
    {
        $payload = [
            'nama_pemesan' => 'Rian Pratama',
            'tipe_pelanggan' => 'Member Aktif: ID MTH-8821',
            'items' => [
                [
                    'nama_item' => 'Fotokopi A4 H/P (Dokumen 38 hlm × 2 set)',
                    'kuantitas' => 76,
                    'harga_satuan' => 250,
                    'sub_total' => 19000,
                    'rincian' => '76 lembar @ Rp 250',
                ],
                [
                    'nama_item' => 'Jilid Lakban Mika + Karton Navy',
                    'kuantitas' => 2,
                    'harga_satuan' => 5000,
                    'sub_total' => 10000,
                    'rincian' => 'Cover mika bening @ Rp 5.000',
                ],
                [
                    'nama_item' => 'Stopmap Folio Diamond (Biru)',
                    'kuantitas' => 1,
                    'harga_satuan' => 3500,
                    'sub_total' => 3500,
                    'rincian' => 'ATK Map @ Rp 3.500',
                ],
                [
                    'nama_item' => 'Materai Tempel 10.000 Fisik',
                    'kuantitas' => 1,
                    'harga_satuan' => 12000,
                    'sub_total' => 12000,
                    'rincian' => 'Resmi @ Rp 12.000',
                ],
            ],
            'subtotal' => 44500,
            'diskon' => 2500,
            'total_harga' => 42000,
            'metode_pembayaran' => 'QRIS',
            'uang_diterima' => 50000,
            'kembalian' => 8000,
        ];

        $response = $this->actingAs($this->admin)->postJson(route('admin.walkin.checkout'), $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'pesanan' => [
                'nama' => 'Rian Pratama',
                'total' => 42000,
                'metode' => 'QRIS',
            ],
        ]);

        $this->assertDatabaseHas('pesanan', [
            'nama_pemesan' => 'Rian Pratama',
            'jenis_pesanan' => 'walk-in',
            'total_harga' => 42000,
            'status_pesanan' => 'selesai',
            'metode_pembayaran' => 'QRIS',
        ]);

        $this->assertEquals(4, DetailPesanan::count());

        // Verify walk-in cashier transactions do not enter workshop antrean mesin
        $this->assertDatabaseCount('antrean_mesin', 0);

        // Verify monthly revenue synchronization
        $periode = now()->format('Y-m');
        $this->assertDatabaseHas('laporan_omzet', [
            'periode' => $periode,
            'total_pendapatan_offline' => 42000,
        ]);
    }

    public function test_seed_and_clear_walkin_demo_cycle(): void
    {
        // 1. Seed demo
        $seedResponse = $this->actingAs($this->admin)->post(route('admin.walkin.seed-demo'));
        $seedResponse->assertRedirect(route('admin.walkin.index'));

        $this->assertEquals(34, Pesanan::where('jenis_pesanan', 'walk-in')->count());
        $this->assertEquals(1482500, (float) Pesanan::where('jenis_pesanan', 'walk-in')->sum('total_harga'));

        // Check LaporanOmzet updated with the demo offline omset
        $periode = now()->format('Y-m');
        $laporan = LaporanOmzet::where('periode', $periode)->first();
        $this->assertNotNull($laporan);
        $this->assertEquals(1482500, (float) $laporan->total_pendapatan_offline);

        // 2. Clear demo back to empty state
        $clearResponse = $this->actingAs($this->admin)->post(route('admin.walkin.clear-demo'));
        $clearResponse->assertRedirect(route('admin.walkin.index'));

        $this->assertEquals(0, Pesanan::where('jenis_pesanan', 'walk-in')->count());
        $laporanAfter = LaporanOmzet::where('periode', $periode)->first();
        $this->assertEquals(0, (float) $laporanAfter->total_pendapatan_offline);
    }

    public function test_unauthenticated_or_customer_cannot_access_walkin_pos(): void
    {
        $guestResponse = $this->get(route('admin.walkin.index'));
        $guestResponse->assertRedirect(route('login'));

        $custResponse = $this->actingAs($this->customer)->get(route('admin.walkin.index'));
        $custResponse->assertRedirect(route('customer.dashboard'));
    }
}
