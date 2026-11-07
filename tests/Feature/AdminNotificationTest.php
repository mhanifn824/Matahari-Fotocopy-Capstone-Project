<?php

namespace Tests\Feature;

use App\Models\InventarisStok;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminNotificationTest extends TestCase
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
            'nama' => 'Bagas Pratama',
            'email' => 'bagas@example.com',
            'password' => Hash::make('12345'),
            'nomor_telepon' => '081234567899',
            'role' => 'user',
        ]);

        Pelanggan::create([
            'user_id' => $this->customer->user_id,
            'kategori' => 'Umum',
        ]);
    }

    public function test_guest_cannot_access_global_notifications(): void
    {
        $response = $this->get(route('admin.notifications'));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_fetch_global_notifications_with_orders_stocks_and_members(): void
    {
        // 1. Order notification
        Pesanan::create([
            'user_id' => $this->customer->user_id,
            'nomor_pesanan' => 'ORD-TEST-001',
            'status_pesanan' => 'pending',
            'total_harga' => 50000,
            'metode_pembayaran' => 'transfer',
            'status_pembayaran' => 'lunas',
            'jenis_pesanan' => 'online',
            'tipe_pesanan' => 'online',
            'judul_dokumen' => 'Modul Skripsi',
        ]);

        // 2. Low stock notification
        InventarisStok::create([
            'nama_bahan' => 'Kertas A4 PaperOne',
            'kategori_bahan' => 'Kertas',
            'jumlah_stok' => 2,
            'satuan' => 'Rim',
            'batas_minimum' => 5,
        ]);

        $response = $this->actingAs($this->admin)->getJson(route('admin.notifications'));

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
            ]);

        $data = $response->json();
        $this->assertArrayHasKey('notifications', $data);
        $this->assertNotEmpty($data['notifications']);

        $types = array_column($data['notifications'], 'type');
        $this->assertContains('pesanan', $types);
        $this->assertContains('stok', $types);
        $this->assertContains('member', $types);

        // Check target URLs
        $orderNotif = collect($data['notifications'])->firstWhere('type', 'pesanan');
        $this->assertSame(route('admin.antrean.index'), $orderNotif['url']);

        $stockNotif = collect($data['notifications'])->firstWhere('type', 'stok');
        $this->assertSame(route('admin.stok.index'), $stockNotif['url']);

        $memberNotif = collect($data['notifications'])->firstWhere('type', 'member');
        $this->assertSame(route('admin.member.index'), $memberNotif['url']);
    }

    public function test_admin_can_mark_single_notification_read(): void
    {
        $response = $this->actingAs($this->admin)->postJson(route('admin.notifications.mark-read'), [
            'id' => 'stock_99',
        ]);

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
                'read_ids' => ['stock_99'],
            ]);

        $this->assertContains('stock_99', session('admin_read_notification_ids'));
    }

    public function test_admin_can_mark_all_notifications_read(): void
    {
        $response = $this->actingAs($this->admin)->postJson(route('admin.notifications.mark-all-read'), [
            'ids' => ['order_1', 'stock_2', 'member_3'],
        ]);

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
            ]);

        $readIds = session('admin_read_notification_ids');
        $this->assertContains('order_1', $readIds);
        $this->assertContains('stock_2', $readIds);
        $this->assertContains('member_3', $readIds);
    }

    public function test_all_admin_views_render_consistent_topbar_elements(): void
    {
        $routes = [
            'admin.dashboard',
            'admin.stok.index',
            'admin.member.index',
            'admin.antrean.index',
            'admin.walkin.index',
            'admin.laporan.index',
            'admin.laporan.transaksi',
        ];

        foreach ($routes as $routeName) {
            $response = $this->actingAs($this->admin)->get(route($routeName));
            $response->assertOk();
            $response->assertSee('admin-live-clock-target');
            $response->assertSee('global-notif-btn');
            $response->assertSee('FA');
        }
    }
}
