<?php

namespace Tests\Feature;

use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure test admin exists
        User::updateOrCreate(
            ['email' => 'admin@123'],
            [
                'nama' => 'Admin Matahari (Uji)',
                'password' => Hash::make('12345'),
                'nomor_telepon' => '081234567890',
                'role' => 'admin',
            ]
        );

        // Ensure test user exists
        $user = User::updateOrCreate(
            ['email' => 'user@321'],
            [
                'nama' => 'Pelanggan Setia (Uji)',
                'password' => Hash::make('12345'),
                'nomor_telepon' => '081234567899',
                'role' => 'user',
            ]
        );

        Pelanggan::updateOrCreate(
            ['user_id' => $user->user_id],
            ['saldo_poin' => 250]
        );
    }

    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertSee('Matahari');
        $response->assertSee('Login User / Pelanggan');
        $response->assertSee('Email');
        $response->assertSee('Masukkan email');
        $response->assertSee('Daftar Akun Pelanggan Baru');
    }

    public function test_admin_can_login_via_admin_tab_with_test_credentials(): void
    {
        $response = $this->post(route('login.post'), [
            'login_type' => 'admin',
            'identifier' => 'admin@123',
            'password' => '12345',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();
        $this->assertTrue(auth()->user()->isAdminOrStaff());
    }

    public function test_customer_cannot_login_via_admin_tab(): void
    {
        $response = $this->post(route('login.post'), [
            'login_type' => 'admin',
            'identifier' => 'user@321',
            'password' => '12345',
        ]);

        $response->assertSessionHasErrors('role_error');
        $this->assertGuest();
    }

    public function test_user_can_login_via_user_tab_with_test_credentials(): void
    {
        $response = $this->post(route('login.post'), [
            'login_type' => 'user',
            'identifier' => 'user@321',
            'password' => '12345',
        ]);

        $response->assertRedirect(route('customer.dashboard'));
        $this->assertAuthenticated();
        $this->assertTrue(auth()->user()->isCustomer());
    }

    public function test_admin_cannot_login_via_user_tab(): void
    {
        $response = $this->post(route('login.post'), [
            'login_type' => 'user',
            'identifier' => 'admin@123',
            'password' => '12345',
        ]);

        $response->assertSessionHasErrors('role_error');
        $this->assertGuest();
    }

    public function test_new_customer_can_register_and_is_assigned_user_role_only(): void
    {
        $uniqueEmail = 'pelanggan_baru_'.time().'@example.com';
        $uniquePhone = '081'.rand(10000000, 99999999);

        $response = $this->post(route('register'), [
            'nama' => 'Pelanggan Baru',
            'email' => $uniqueEmail,
            'nomor_telepon' => $uniquePhone,
            'password' => '12345',
            'password_confirmation' => '12345',
        ]);

        $response->assertRedirect(route('customer.dashboard'));
        $this->assertAuthenticated();

        $newUser = User::where('email', $uniqueEmail)->first();
        $this->assertNotNull($newUser);
        $this->assertSame('user', $newUser->role);
        $this->assertFalse($newUser->isAdminOrStaff());
        $this->assertTrue($newUser->isCustomer());

        // Check linked pelanggan and welcome points
        $this->assertNotNull($newUser->pelanggan);
        $this->assertSame(50, $newUser->pelanggan->saldo_poin);
    }

    public function test_customer_cannot_access_admin_dashboard_directly(): void
    {
        $user = User::where('email', 'user@321')->first();

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertRedirect(route('customer.dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::where('email', 'admin@123')->first();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Admin / Kasir');
        $response->assertSee('Hak Akses Terverifikasi');
    }

    public function test_logout_terminates_session(): void
    {
        $admin = User::where('email', 'admin@123')->first();

        $response = $this->actingAs($admin)->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
