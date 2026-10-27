<?php

namespace Tests\Feature;

use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminMemberTest extends TestCase
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

    public function test_admin_member_page_renders_successfully_for_authenticated_admin(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.member.index'));

        $response->assertStatus(200);
        $response->assertSee('Data Member & Program Hadiah Kunjungan', false);
        $response->assertSee('TOTAL MEMBER AKTIF');
        $response->assertSee('HADIAH TERKLAIM');
        $response->assertSee('MEMBER PALING LOYAL');
        $response->assertSee('TOTAL POIN BEREDAR');
        $response->assertSee('Program Hadiah Stamp Kunjungan');
        $response->assertSee('Log Klaim Hadiah Hari Ini');
        $response->assertSee('Scan Member Card / QR WA');
    }

    public function test_guest_and_non_admin_cannot_access_admin_member(): void
    {
        $guestResponse = $this->get(route('admin.member.index'));
        $guestResponse->assertRedirect(route('login'));

        $userResponse = $this->actingAs($this->customer)->get(route('admin.member.index'));
        $userResponse->assertRedirect(route('customer.dashboard'));
    }

    public function test_admin_can_register_new_member(): void
    {
        $payload = [
            'nama' => 'Bayu Wicaksono',
            'nomor_telepon' => '0819-8765-4321',
            'kategori_member' => 'Mahasiswa UPJ/STAN',
            'instansi_detail' => 'Mahasiswa S1 Informatika',
            'badge_label' => 'UPJ BINTARO',
            'saldo_poin' => 50,
        ];

        $response = $this->actingAs($this->admin)->postJson(route('admin.member.store'), $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
        ]);

        $this->assertDatabaseHas('users', [
            'nama' => 'Bayu Wicaksono',
            'nomor_telepon' => '0819-8765-4321',
        ]);

        $this->assertDatabaseHas('pelanggan', [
            'kategori_member' => 'Mahasiswa UPJ/STAN',
            'instansi_detail' => 'Mahasiswa S1 Informatika',
            'badge_label' => 'UPJ BINTARO',
        ]);
    }

    public function test_admin_can_claim_member_reward(): void
    {
        $user = User::create([
            'nama' => 'Agus Priyono',
            'email' => 'agus@example.com',
            'password' => Hash::make('12345'),
            'nomor_telepon' => '0812-7777-8888',
            'role' => 'user',
        ]);

        $pelanggan = Pelanggan::create([
            'user_id' => $user->user_id,
            'kode_member' => 'MTH-9901',
            'kategori_member' => 'Member Umum',
            'saldo_poin' => 500,
            'total_kunjungan' => 10,
            'hadiah_terklaim_count' => 0,
        ]);

        $payload = [
            'pelanggan_id' => $pelanggan->pelanggan_id,
            'nama_hadiah' => 'Voucher Cetak Rp 25.000',
            'tipe_klaim' => 'Milestone 10x',
            'poin_tukar' => 0,
        ];

        $response = $this->actingAs($this->admin)->postJson(route('admin.member.klaim-hadiah'), $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
        ]);

        $pelanggan->refresh();
        $this->assertEquals(1, $pelanggan->hadiah_terklaim_count);

        $this->assertDatabaseHas('log_klaim_hadiah', [
            'pelanggan_id' => $pelanggan->pelanggan_id,
            'nama_hadiah' => 'Voucher Cetak Rp 25.000',
            'tipe_klaim' => 'Milestone 10x',
        ]);
    }

    public function test_admin_can_scan_and_lookup_member(): void
    {
        $user = User::create([
            'nama' => 'Rina Melati',
            'email' => 'rina@example.com',
            'password' => Hash::make('12345'),
            'nomor_telepon' => '0813-5555-6666',
            'role' => 'user',
        ]);

        Pelanggan::create([
            'user_id' => $user->user_id,
            'kode_member' => 'MTH-7788',
            'kategori_member' => 'Instansi/Dosen',
            'instansi_detail' => 'Dosen Teknik',
            'saldo_poin' => 300,
            'total_kunjungan' => 5,
        ]);

        $response = $this->actingAs($this->admin)->getJson(route('admin.member.scan-ajax', ['code' => 'MTH-7788']));

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'member' => [
                'nama' => 'Rina Melati',
                'kode_member' => 'MTH-7788',
            ],
        ]);
    }
}
