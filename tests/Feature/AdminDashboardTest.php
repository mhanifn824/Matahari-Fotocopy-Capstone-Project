<?php

namespace Tests\Feature;

use App\Models\AntreanMesin;
use App\Models\InventarisStok;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\ProdukLayanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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
}
