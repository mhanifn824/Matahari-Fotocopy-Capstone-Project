<?php

namespace Tests\Feature;

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
        $response->assertSee('CABANG BINTARO SEKTOR 3');
        $response->assertSee('TOTAL OMZET 7 HARI');
        $response->assertSee('TOTAL NOTA PESANAN');
        $response->assertSee('VOLUME KERTAS TERCETAK');
        $response->assertSee('RATA-RATA TRANSAKSI');
        $response->assertSee('Tren Pendapatan 7 Hari');
        $response->assertSee('Komposisi Layanan');
        $response->assertSee('Analitik Waktu: Jam Sibuk Operasional Gerai');
        $response->assertSee('Transaksi Terakhir Gerai');
        $response->assertSee('Ekspor Laporan Excel (.xlsx)');
        $response->assertSee('Unduh Rekap PDF');
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
}
