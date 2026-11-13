<?php

namespace Tests\Feature;

use App\Models\DetailPesanan;
use App\Models\InventarisStok;
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
        $response->assertSee('Kasir Walk-in');
        $response->assertSee('Nota Walk-in #W-');
        $response->assertDontSee('POS KASIR');
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

    public function test_walkin_page_integrates_with_manajemen_stok_and_removes_kirim_workshop_toggle(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.walkin.index'));

        $response->assertStatus(200);
        $response->assertDontSee('Kirim ke Antrean Pengerjaan');
        $response->assertSee('Pilih Layanan & ATK Cepat (Klik untuk Masuk Nota)', false);
        $response->assertSee('Fotokopi &amp; Print', false);
        $response->assertSee('Alat Tulis &amp; Map', false);
        $response->assertSee('Jilid &amp; Finishing', false);
        $response->assertSee('Jasa &amp; Lainnya', false);
        $response->assertSee('Kertas HVS A4 70gr PaperOne');
        $response->assertSee('Kawat Spiral No. 10 (1/2 inch)');
        $response->assertSee('Stok:');
    }

    public function test_walkin_checkout_deducts_inventory_stock_and_updates_financial_reports(): void
    {
        $this->actingAs($this->admin)->get(route('admin.walkin.index'));

        $stokSpiral = InventarisStok::where('nama_bahan', 'Kawat Spiral No. 10 (1/2 inch)')->firstOrFail();
        $initialStock = $stokSpiral->jumlah_stok;

        $payload = [
            'nama_pemesan' => 'Citra Kirana',
            'tipe_pelanggan' => 'Walk-in Tamu',
            'items' => [
                [
                    'nama_item' => 'Kawat Spiral No. 10 (1/2 inch)',
                    'kuantitas' => 5,
                    'harga_satuan' => 10000,
                    'sub_total' => 50000,
                    'rincian' => '5 Batang',
                ],
            ],
            'subtotal' => 50000,
            'diskon' => 0,
            'total_harga' => 50000,
            'metode_pembayaran' => 'TUNAI',
            'uang_diterima' => 50000,
            'kembalian' => 0,
        ];

        $response = $this->actingAs($this->admin)->postJson(route('admin.walkin.checkout'), $payload);

        $response->assertStatus(200);
        $this->assertEquals($initialStock - 5, $stokSpiral->fresh()->jumlah_stok);

        // Check LaporanOmzet
        $periode = now()->format('Y-m');
        $this->assertDatabaseHas('laporan_omzet', [
            'periode' => $periode,
            'total_pendapatan_offline' => 50000,
        ]);

        // Check laporan index page displays the transaction and omzet
        $laporanResponse = $this->actingAs($this->admin)->get(route('admin.laporan.index'));
        $laporanResponse->assertStatus(200);
        $laporanResponse->assertSee('Citra Kirana');
        $laporanResponse->assertSee('Rp 50.000');
    }

    public function test_adding_new_inventory_item_automatically_syncs_to_walkin_catalog(): void
    {
        $payload = [
            'kode_bahan' => 'A-GNT-10',
            'nama_bahan' => 'Gunting Joyko Stainless Besar',
            'gramatur_detail' => 'Ukuran 8 Inch Tajam Presisi',
            'kategori' => 'Alat Tulis & Koreksi',
            'jumlah_stok' => 15,
            'batas_minimum' => 3,
            'kapasitas_maksimal' => 30,
            'satuan' => 'Pcs',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.stok.tambah-item'), $payload);
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('inventaris_stok', [
            'kode_bahan' => 'A-GNT-10',
            'nama_bahan' => 'Gunting Joyko Stainless Besar',
        ]);

        $this->assertDatabaseHas('produk_layanan', [
            'nama_item' => 'Gunting Joyko Stainless Besar',
            'kategori' => 'Alat Tulis & Map',
            'status_aktif' => true,
        ]);
    }

    public function test_walkin_page_does_not_contain_cash_tender_inputs_and_edc_payment_method(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.walkin.index'));

        $response->assertStatus(200);
        $response->assertDontSee('UANG DITERIMA (TUNAI)');
        $response->assertDontSee('Kembalian: Rp');
        $response->assertDontSee('promptCustomTender');
        $response->assertDontSee("selectPaymentMethod('EDC'", false);
        $response->assertSee("selectPaymentMethod('QRIS'", false);
        $response->assertSee("selectPaymentMethod('TUNAI'", false);
        $response->assertSee("selectPaymentMethod('TRANSFER'", false);
    }

    public function test_walkin_nota_supports_manual_quantity_typing_and_retains_stepper_buttons(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.walkin.index'));

        $response->assertStatus(200);
        $response->assertSee('handleQtyInput', false);
        $response->assertSee('handleQtyBlur', false);
        $response->assertSee('updateItemQty', false);
        $response->assertSee('type="number"', false);
    }

    public function test_walkin_search_and_catalog_search_elements_render_properly(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.walkin.index'));

        $response->assertStatus(200);
        // Requirement 2: Kasir Walk-in search placeholder and empty state
        $response->assertSee('placeholder="Cari nomor nota atau nama pelanggan..."', false);
        $response->assertSee('filterPosTransactions', false);
        $response->assertSee('id="searchNotFoundRow"', false);
        $response->assertSee('Data tidak tersedia');

        // Requirement 3: Catalog search input under Pilih Layanan & ATK Cepat
        $response->assertSee('Pilih Layanan & ATK Cepat (Klik untuk Masuk Nota)', false);
        $response->assertSee('id="catalogSearchInput"', false);
        $response->assertSee('placeholder="Cari barang atau layanan ATK..."', false);
        $response->assertSee('filterCatalogProducts', false);
        $response->assertSee('id="catalogEmptyState"', false);
        $response->assertSee('Barang atau layanan tidak tersedia');
        $response->assertSee('data-name=', false);
    }

    public function test_pembelian_online_search_elements_and_empty_states_render_properly(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.antrean.index'));

        $response->assertStatus(200);
        // Requirement 1: Online orders search
        $response->assertSee('id="header-search-input"', false);
        $response->assertSee('filterQueueClientSide', false);
        $response->assertSee('id="search-not-found-antrean"', false);
        $response->assertSee('id="search-not-found-atk"', false);
        $response->assertSee('Data tidak tersedia');
    }

    public function test_walkin_renders_stock_warning_banner_and_labels_out_of_stock_items(): void
    {
        InventarisStok::create([
            'nama_bahan' => 'Buku Tulis Sinar Dunia 38 Lembar',
            'kategori' => 'ATK',
            'jumlah_stok' => 0,
            'satuan' => 'pcs',
            'harga_beli' => 2500,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.walkin.index'));

        $response->assertStatus(200);
        $response->assertSee('id="walkinStockNotice"', false);
        $response->assertSee('Stok produk habis. Produk tidak dapat ditambahkan ke nota.');
        $response->assertSee('Stok Habis');
    }

    public function test_walkin_checkout_rejects_items_with_zero_stock(): void
    {
        InventarisStok::create([
            'nama_bahan' => 'Buku Tulis Sinar Dunia 38 Lembar',
            'kategori' => 'ATK',
            'jumlah_stok' => 0,
            'satuan' => 'pcs',
            'harga_beli' => 2500,
        ]);

        $payload = [
            'nama_pemesan' => 'Budi Santoso',
            'tipe_pelanggan' => 'Walk-in Tamu',
            'items' => [
                [
                    'nama_item' => 'Buku Tulis Sinar Dunia 38 Lembar',
                    'kuantitas' => 1,
                    'harga_satuan' => 4000,
                    'sub_total' => 4000,
                ],
            ],
            'subtotal' => 4000,
            'diskon' => 0,
            'total_harga' => 4000,
            'metode_pembayaran' => 'TUNAI',
        ];

        $response = $this->actingAs($this->admin)->postJson(route('admin.walkin.checkout'), $payload);

        $response->assertStatus(422);
        $response->assertJson([
            'status' => 'error',
            'message' => 'Stok produk habis. Produk tidak dapat ditambahkan ke nota.',
        ]);

        $this->assertEquals(0, Pesanan::count());
    }

    public function test_stock_synchronization_between_manajemen_stok_and_kasir_walkin(): void
    {
        // 1. Awal: Buat item di inventaris dengan stok habis (0)
        $stok = InventarisStok::create([
            'kode_bahan' => 'ATK-SPIDOL-01',
            'nama_bahan' => 'Spidol Marker Snowman Hitam',
            'kategori' => 'Alat Tulis & Map',
            'jumlah_stok' => 0,
            'satuan' => 'pcs',
            'batas_minimum' => 5,
            'kapasitas_maksimal' => 50,
            'harga_beli' => 7000,
        ]);

        // 2. Pada Kasir Walk-in, item berstatus habis dan ditolak saat checkout
        $walkinRes = $this->actingAs($this->admin)->get(route('admin.walkin.index'));
        $walkinRes->assertStatus(200);
        $walkinRes->assertSee('Spidol Marker Snowman Hitam');
        $walkinRes->assertSee('Stok Habis');

        $rejectedCheckout = $this->actingAs($this->admin)->postJson(route('admin.walkin.checkout'), [
            'nama_pemesan' => 'Pelanggan Walk-in',
            'tipe_pelanggan' => 'Walk-in Tamu',
            'items' => [
                [
                    'nama_item' => 'Spidol Marker Snowman Hitam',
                    'kuantitas' => 1,
                    'harga_satuan' => 9000,
                    'sub_total' => 9000,
                ],
            ],
            'subtotal' => 9000,
            'diskon' => 0,
            'total_harga' => 9000,
            'metode_pembayaran' => 'TUNAI',
        ]);
        $rejectedCheckout->assertStatus(422);
        $rejectedCheckout->assertJson([
            'status' => 'error',
            'message' => 'Stok produk habis. Produk tidak dapat ditambahkan ke nota.',
        ]);

        // 3. Admin menambah stok melalui fitur Manajemen Stok (catat bahan masuk / quick add)
        $addStockRes = $this->actingAs($this->admin)->postJson(route('admin.stok.catat-masuk'), [
            'stok_id' => $stok->stok_id,
            'no_surat_jalan' => 'SJ-2026-TEST-01',
            'jumlah_masuk' => 20,
            'satuan' => 'pcs',
            'mitra_supplier' => 'CV Sinar Abadi Bintaro',
        ]);
        $addStockRes->assertStatus(200);
        $this->assertEquals(20, $stok->fresh()->jumlah_stok);

        // 4. Kasir Walk-in secara otomatis mengikuti data terbaru dari database
        $walkinUpdatedRes = $this->actingAs($this->admin)->get(route('admin.walkin.index'));
        $walkinUpdatedRes->assertStatus(200);
        $walkinUpdatedRes->assertSee('Stok: 20 pcs');

        $liveDataRes = $this->actingAs($this->admin)->get(route('admin.walkin.live-data'));
        $liveDataRes->assertStatus(200);
        $liveDataRes->assertJsonFragment([
            'nama_item' => 'Spidol Marker Snowman Hitam',
            'jumlah_stok' => 20,
            'is_habis' => false,
        ]);

        // 5. Produk yang sebelumnya habis sekarang BISA ditambahkan dan dicheckout
        $successCheckout = $this->actingAs($this->admin)->postJson(route('admin.walkin.checkout'), [
            'nama_pemesan' => 'Pelanggan Sukses',
            'tipe_pelanggan' => 'Walk-in Tamu',
            'items' => [
                [
                    'nama_item' => 'Spidol Marker Snowman Hitam',
                    'kuantitas' => 4,
                    'harga_satuan' => 9000,
                    'sub_total' => 36000,
                ],
            ],
            'subtotal' => 36000,
            'diskon' => 0,
            'total_harga' => 36000,
            'metode_pembayaran' => 'TUNAI',
        ]);
        $successCheckout->assertStatus(200);

        // 6. Stok di database berkurang otomatis (20 - 4 = 16)
        $this->assertEquals(16, $stok->fresh()->jumlah_stok);

        // 7. Perubahan stok langsung tercermin di Manajemen Stok (menggunakan sumber database yang sama)
        $stokLiveRes = $this->actingAs($this->admin)->get(route('admin.stok.live-data'));
        $stokLiveRes->assertStatus(200);
        $stokLiveRes->assertJsonFragment([
            'nama_bahan' => 'Spidol Marker Snowman Hitam',
            'jumlah_stok' => 16,
        ]);
    }
}
