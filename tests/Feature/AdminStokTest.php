<?php

namespace Tests\Feature;

use App\Models\InventarisStok;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminStokTest extends TestCase
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

    public function test_admin_stok_page_renders_successfully_for_authenticated_admin(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.stok.index'));

        $response->assertStatus(200);
        $response->assertSee('Inventaris Bahan & Stok Gerai', false);
        $response->assertSee('GUDANG UTAMA BINTARO');
        $response->assertSee('Catat Bahan Masuk');
        $response->assertSee('Tambah Item Baru');
        $response->assertSee('TOTAL ITEM TERDAFTAR');
        $response->assertSee('STOK AMAN');
        $response->assertSee('STOK MENIPIS');
        $response->assertSee('STOK KRITIS / HABIS');
        $response->assertSee('Daftar Stok Bahan');
        $response->assertSee('Riwayat Bahan Masuk Terbaru');
    }

    public function test_guest_and_non_admin_cannot_access_admin_stok(): void
    {
        $guestResponse = $this->get(route('admin.stok.index'));
        $guestResponse->assertRedirect(route('login'));

        $userResponse = $this->actingAs($this->customer)->get(route('admin.stok.index'));
        $userResponse->assertRedirect(route('customer.dashboard'));
    }

    public function test_admin_stok_live_data_returns_accurate_json_payload(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.stok.live-data'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'stats' => [
                'total',
                'aman',
                'menipis',
                'kritis',
            ],
            'items',
        ]);
    }

    public function test_catat_bahan_masuk_records_delivery_and_increments_inventory(): void
    {
        $item = InventarisStok::create([
            'nama_bahan' => 'Kertas Glossy Premium',
            'kode_bahan' => 'K-GLS-01',
            'kategori' => 'Kertas Khusus',
            'jumlah_stok' => 5,
            'satuan' => 'Rim',
            'batas_minimum' => 3,
            'kapasitas_maksimal' => 20,
        ]);

        $payload = [
            'stok_id' => $item->stok_id,
            'no_surat_jalan' => 'SJ-2026-TEST-99',
            'mitra_supplier' => 'CV Sumber Kertas Nusantara',
            'jumlah_masuk' => 10,
            'satuan' => 'Rim',
        ];

        $response = $this->actingAs($this->admin)->postJson(route('admin.stok.catat-masuk'), $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
        ]);

        $item->refresh();
        $this->assertEquals(15, $item->jumlah_stok);

        $this->assertDatabaseHas('riwayat_bahan_masuk', [
            'stok_id' => $item->stok_id,
            'no_surat_jalan' => 'SJ-2026-TEST-99',
            'mitra_supplier' => 'CV Sumber Kertas Nusantara',
            'jumlah_masuk' => 10,
        ]);
    }

    public function test_quick_add_stok_increments_inventory_stock_by_one_unit(): void
    {
        $item = InventarisStok::create([
            'nama_bahan' => 'Toner TN-324 Black',
            'kode_bahan' => 'T-KON-BK',
            'kategori' => 'Tinta & Toner',
            'jumlah_stok' => 1,
            'satuan' => 'Tabung',
            'batas_minimum' => 2,
            'kapasitas_maksimal' => 10,
        ]);

        $response = $this->actingAs($this->admin)->postJson(route('admin.stok.quick-add', ['stok' => $item->stok_id]), [
            'jumlah' => 2,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
        ]);

        $item->refresh();
        $this->assertEquals(3, $item->jumlah_stok);
    }

    public function test_tambah_item_baru_creates_new_inventory_record(): void
    {
        $payload = [
            'nama_bahan' => 'Tinta Cyan Epson L-Series',
            'kode_bahan' => 'T-EP-CYAN',
            'kategori' => 'Tinta & Toner',
            'gramatur_detail' => '70ml • Botol Original 003',
            'jumlah_stok' => 8,
            'satuan' => 'Botol',
            'batas_minimum' => 3,
            'kapasitas_maksimal' => 25,
        ];

        $response = $this->actingAs($this->admin)->postJson(route('admin.stok.tambah-item'), $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
        ]);

        $this->assertDatabaseHas('inventaris_stok', [
            'kode_bahan' => 'T-EP-CYAN',
            'nama_bahan' => 'Tinta Cyan Epson L-Series',
            'jumlah_stok' => 8,
            'satuan' => 'Botol',
        ]);
    }

    public function test_tambah_item_baru_with_uploaded_image_saves_image_to_stok_folder(): void
    {
        $fakeImage = UploadedFile::fake()->image('test_product.png', 100, 100);

        $payload = [
            'nama_bahan' => 'Kertas Foto Glossy A4',
            'kode_bahan' => 'K-FOTO-A4',
            'kategori' => 'Kertas Khusus',
            'gramatur_detail' => '230gsm Glossy Print',
            'jumlah_stok' => 15,
            'satuan' => 'Pak',
            'batas_minimum' => 3,
            'kapasitas_maksimal' => 30,
            'gambar' => $fakeImage,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.stok.tambah-item'), $payload);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('inventaris_stok', [
            'kode_bahan' => 'K-FOTO-A4',
            'nama_bahan' => 'Kertas Foto Glossy A4',
        ]);

        $savedImagePath = public_path('images/stok/k-foto-a4.png');
        $this->assertFileExists($savedImagePath);

        // Clean up test file
        if (file_exists($savedImagePath)) {
            unlink($savedImagePath);
        }
    }

    public function test_customer_can_access_customer_home_page(): void
    {
        $response = $this->actingAs($this->customer)->get(route('customer.home'));

        $response->assertStatus(200);
        $response->assertSee('Home');
        $response->assertSee('Cetak Cepat, Hasil Presisi');
        $response->assertSee('Katalog Alat Tulis (ATK)');
    }
}
