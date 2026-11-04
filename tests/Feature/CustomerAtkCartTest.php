<?php

namespace Tests\Feature;

use App\Models\AntreanMesin;
use App\Models\InventarisStok;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerAtkCartTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;

    protected User $admin;

    protected Pelanggan $pelanggan;

    protected InventarisStok $stokKertas;

    protected InventarisStok $stokPulpen;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'nama' => 'Admin Matahari',
            'email' => 'admin@matahari.com',
            'password' => Hash::make('password123'),
            'nomor_telepon' => '081234567890',
            'role' => 'admin',
        ]);

        $this->customer = User::create([
            'nama' => 'Rina Maharani',
            'email' => 'rina@customer.com',
            'password' => Hash::make('password123'),
            'nomor_telepon' => '081234567891',
            'role' => 'user',
        ]);

        $this->pelanggan = Pelanggan::create([
            'user_id' => $this->customer->user_id,
            'saldo_poin' => 50,
        ]);

        $this->stokKertas = InventarisStok::create([
            'kode_bahan' => 'K-A4-70',
            'nama_bahan' => 'Kertas HVS A4 70gr PaperOne',
            'gramatur_detail' => '210 x 297 mm • Putih',
            'kategori' => 'Kertas & Amplop',
            'jumlah_stok' => 20,
            'batas_minimum' => 5,
            'kapasitas_maksimal' => 50,
            'satuan' => 'Rim',
        ]);

        $this->stokPulpen = InventarisStok::create([
            'kode_bahan' => 'P-FST-01',
            'nama_bahan' => 'Pulpen Faster C600 Hitam',
            'gramatur_detail' => '0.5 mm • Tinta Hitam',
            'kategori' => 'Alat Tulis & Koreksi',
            'jumlah_stok' => 50,
            'batas_minimum' => 10,
            'kapasitas_maksimal' => 100,
            'satuan' => 'Pcs',
        ]);
    }

    public function test_customer_portal_renders_atk_cart_elements(): void
    {
        $response = $this->actingAs($this->customer)->get(route('customer.home'));

        $response->assertStatus(200);
        $response->assertDontSee('navbarCartBtn');
        $response->assertSee('Keranjang Belanja ATK');
        $response->assertSee('modalCart');
        $response->assertSee('modalOrderSuccess');
        $response->assertSee('Pesanan ATK Berhasil Dibuat!');
        $response->assertSee('floatingCartContainer');
        $response->assertSee('Kertas HVS A4 70gr PaperOne');
        $response->assertSee('Pulpen Faster C600 Hitam');
    }

    public function test_customer_can_checkout_atk_cart_via_ajax_successfully(): void
    {
        $items = [
            [
                'stok_id' => $this->stokKertas->stok_id,
                'nama_item' => $this->stokKertas->nama_bahan,
                'kuantitas' => 2,
                'harga_satuan' => 45000,
                'sub_total' => 90000,
            ],
            [
                'stok_id' => $this->stokPulpen->stok_id,
                'nama_item' => $this->stokPulpen->nama_bahan,
                'kuantitas' => 5,
                'harga_satuan' => 4500,
                'sub_total' => 22500,
            ],
        ];

        $response = $this->actingAs($this->customer)
            ->postJson(route('customer.cart.checkout'), [
                'items' => $items,
                'total_harga' => 112500,
                'metode_pembayaran' => 'qris',
                'catatan' => 'Mohon disiapkan sebelum jam 2 siang',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'total_harga' => 112500,
            'metode_pembayaran' => 'QRIS',
        ]);
        $response->assertJsonStructure([
            'status',
            'message',
            'pesanan_id',
            'nomor_nota',
            'kode_antrean',
            'total_harga',
            'metode_pembayaran',
            'redirect_url',
        ]);

        // Assert stock decremented
        $this->assertEquals(18, $this->stokKertas->fresh()->jumlah_stok);
        $this->assertEquals(45, $this->stokPulpen->fresh()->jumlah_stok);

        // Assert pesanan created
        $pesanan = Pesanan::where('pelanggan_id', $this->pelanggan->pelanggan_id)->latest()->first();
        $this->assertNotNull($pesanan);
        $this->assertEquals('online', $pesanan->jenis_pesanan);
        $this->assertEquals('Retail ATK', $pesanan->kategori_layanan);
        $this->assertEquals(112500, (float) $pesanan->total_harga);
        $this->assertStringContainsString('Kertas HVS A4 70gr PaperOne (2 Rim)', $pesanan->spesifikasi);
        $this->assertStringContainsString('Pulpen Faster C600 Hitam (5 Pcs)', $pesanan->spesifikasi);
        $this->assertStringContainsString('Mohon disiapkan sebelum jam 2 siang', $pesanan->spesifikasi);

        // Assert detail items created
        $this->assertCount(2, $pesanan->detailPesanan);

        // Assert antrean ticket created
        $antrean = AntreanMesin::where('pesanan_id', $pesanan->pesanan_id)->first();
        $this->assertNotNull($antrean);
        $this->assertEquals('siap', $antrean->status_antrean);
        $this->assertEquals('Siap Diambil', $antrean->status_alur);

        // Assert loyalty points allocated for paid order
        $this->assertGreaterThan(50, $this->pelanggan->fresh()->saldo_poin);
    }

    public function test_customer_can_checkout_atk_cart_via_form_with_cart_data_json(): void
    {
        $items = [
            [
                'stok_id' => $this->stokKertas->stok_id,
                'nama_item' => $this->stokKertas->nama_bahan,
                'kuantitas' => 1,
                'harga_satuan' => 45000,
                'sub_total' => 45000,
            ],
        ];

        $response = $this->actingAs($this->customer)
            ->post(route('customer.cart.checkout'), [
                'cart_data' => json_encode($items),
                'total_harga' => 45000,
                'metode_pembayaran' => 'tunai',
                'catatan' => 'Bayar tunai di kasir gerai',
            ]);

        $response->assertRedirect(route('customer.home').'#pesanan-saya');
        $response->assertSessionHas('success');

        // Check stock decremented
        $this->assertEquals(19, $this->stokKertas->fresh()->jumlah_stok);

        $pesanan = Pesanan::where('pelanggan_id', $this->pelanggan->pelanggan_id)->latest()->first();
        $this->assertNotNull($pesanan);
        $this->assertEquals(45000, (float) $pesanan->total_harga);
        $this->assertEquals('TUNAI', $pesanan->metode_pembayaran);
    }

    public function test_checkout_fails_if_stock_is_insufficient(): void
    {
        $this->stokKertas->update(['jumlah_stok' => 2]);

        $items = [
            [
                'stok_id' => $this->stokKertas->stok_id,
                'nama_item' => $this->stokKertas->nama_bahan,
                'kuantitas' => 5, // exceeds available stock 2
                'harga_satuan' => 45000,
                'sub_total' => 225000,
            ],
        ];

        $response = $this->actingAs($this->customer)
            ->postJson(route('customer.cart.checkout'), [
                'items' => $items,
                'total_harga' => 225000,
                'metode_pembayaran' => 'qris',
            ]);

        $response->assertStatus(422);
        $response->assertJson([
            'status' => 'error',
        ]);
        $response->assertJsonPath('message', fn ($msg) => str_contains($msg, 'melebihi stok yang tersedia'));

        // Stock must not change
        $this->assertEquals(2, $this->stokKertas->fresh()->jumlah_stok);
    }

    public function test_checkout_fails_if_stock_is_zero(): void
    {
        $this->stokPulpen->update(['jumlah_stok' => 0]);

        $items = [
            [
                'stok_id' => $this->stokPulpen->stok_id,
                'nama_item' => $this->stokPulpen->nama_bahan,
                'kuantitas' => 1,
                'harga_satuan' => 4500,
                'sub_total' => 4500,
            ],
        ];

        $response = $this->actingAs($this->customer)
            ->postJson(route('customer.cart.checkout'), [
                'items' => $items,
                'total_harga' => 4500,
                'metode_pembayaran' => 'qris',
            ]);

        $response->assertStatus(422);
        $response->assertJson([
            'status' => 'error',
        ]);

        // Stock remains 0
        $this->assertEquals(0, $this->stokPulpen->fresh()->jumlah_stok);
    }

    public function test_guest_cannot_access_cart_checkout(): void
    {
        $response = $this->postJson(route('customer.cart.checkout'), [
            'items' => [
                [
                    'stok_id' => $this->stokKertas->stok_id,
                    'nama_item' => $this->stokKertas->nama_bahan,
                    'kuantitas' => 1,
                    'harga_satuan' => 45000,
                    'sub_total' => 45000,
                ],
            ],
            'total_harga' => 45000,
            'metode_pembayaran' => 'qris',
        ]);

        $response->assertStatus(401);
    }

    public function test_admin_pembelian_online_displays_both_printing_and_atk_tables(): void
    {
        // 1. Create a printing order
        $pesananPrint = Pesanan::create([
            'pelanggan_id' => $this->pelanggan->pelanggan_id,
            'nama_pemesan' => 'Rina Maharani',
            'judul_dokumen' => 'Modul Skripsi Bab 1-4',
            'kategori_layanan' => 'Jasa Cetak & Fotocopy',
            'spesifikasi' => 'A4 70gr • Hitam Putih • Jilid Biasa',
            'jenis_pesanan' => 'online',
            'total_harga' => 35000,
            'status_pesanan' => 'proses',
            'metode_pembayaran' => 'QRIS',
            'waktu_pesan' => now(),
        ]);

        $antreanPrint = AntreanMesin::create([
            'pesanan_id' => $pesananPrint->pesanan_id,
            'nomor_tiket' => 1,
            'nama_mesin' => 'Mesin Ricoh 2',
            'status_antrean' => 'menunggu',
            'status_alur' => 'Menunggu',
            'estimasi_waktu' => 15,
        ]);

        // 2. Create an ATK retail order
        $pesananAtk = Pesanan::create([
            'pelanggan_id' => $this->pelanggan->pelanggan_id,
            'nama_pemesan' => 'Rina Maharani',
            'judul_dokumen' => 'Beli ATK: Kertas HVS A4 70gr PaperOne (1 Rim)',
            'kategori_layanan' => 'Retail ATK',
            'spesifikasi' => 'Kertas HVS A4 70gr PaperOne (1 Rim)',
            'jenis_pesanan' => 'online',
            'total_harga' => 45000,
            'status_pesanan' => 'siap',
            'metode_pembayaran' => 'QRIS',
            'waktu_pesan' => now(),
        ]);

        $antreanAtk = AntreanMesin::create([
            'pesanan_id' => $pesananAtk->pesanan_id,
            'nomor_tiket' => 2,
            'nama_mesin' => 'Loket Kasir & Rak ATK',
            'status_antrean' => 'siap',
            'status_alur' => 'Siap Diambil',
            'estimasi_waktu' => 5,
        ]);

        // 3. Visit admin antrean index (Pembelian Online)
        $response = $this->actingAs($this->admin)->get(route('admin.antrean.index'));

        $response->assertStatus(200);
        $response->assertSee('Pembelian Online');
        $response->assertSee('Pembelian Khusus Printing (Antrean Workshop)');
        $response->assertSee('Pembelian Khusus ATK (Alat Tulis Kantor)');
        $response->assertSee('section-printing');
        $response->assertSee('section-atk');

        // Check printing item is rendered
        $response->assertSee('Modul Skripsi Bab 1-4');
        $response->assertSee('Mesin Ricoh 2');

        // Check ATK item is rendered in ATK table without action column
        $response->assertSee('Tabel Pembelian Retail ATK Online');
        $response->assertSee('Kertas HVS A4 70gr PaperOne (1 Rim)');
        $response->assertSee('Loket Kasir &amp; Rak ATK', false);
        $response->assertDontSee('AKSI KASIR');
        $response->assertDontSee('Serahkan ke Pelanggan');
    }

    public function test_admin_can_handover_atk_order_via_serahkan_action(): void
    {
        $pesananAtk = Pesanan::create([
            'pelanggan_id' => $this->pelanggan->pelanggan_id,
            'nama_pemesan' => 'Rina Maharani',
            'judul_dokumen' => 'Beli ATK: Pulpen Faster C600 Hitam (2 Pcs)',
            'kategori_layanan' => 'Retail ATK',
            'spesifikasi' => 'Pulpen Faster C600 Hitam (2 Pcs)',
            'jenis_pesanan' => 'online',
            'total_harga' => 9000,
            'status_pesanan' => 'siap',
            'metode_pembayaran' => 'TUNAI',
            'waktu_pesan' => now(),
        ]);

        $antreanAtk = AntreanMesin::create([
            'pesanan_id' => $pesananAtk->pesanan_id,
            'nomor_tiket' => 3,
            'nama_mesin' => 'Loket Kasir & Rak ATK',
            'status_antrean' => 'siap',
            'status_alur' => 'Siap Diambil',
            'status_pembayaran' => 'belum_lunas',
            'estimasi_waktu' => 5,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.antrean.action', $antreanAtk), [
            'action' => 'serahkan',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $antreanAtk->refresh();
        $pesananAtk->refresh();

        $this->assertEquals('Selesai', $antreanAtk->status_alur);
        $this->assertEquals('selesai', $antreanAtk->status_antrean);
        $this->assertEquals('lunas', $antreanAtk->status_pembayaran);
        $this->assertEquals('selesai', $pesananAtk->status_pesanan);
    }
}
