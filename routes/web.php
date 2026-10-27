<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Matahari Fotocopy
|--------------------------------------------------------------------------
*/

// Authentication Routes
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::get('/login', [AuthController::class, 'showLogin']);
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout']);

// Universal Dashboard Redirector
Route::get('/dashboard', function () {
    if (! Auth::check()) {
        return redirect()->route('login');
    }

    if (Auth::user()->isAdminOrStaff()) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('customer.home');
})->middleware('auth')->name('dashboard');

// Protected Admin & Cashier Routes
Route::middleware(['auth', 'admin.only'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
    Route::get('/dashboard/live-data', [DashboardController::class, 'liveData'])->name('dashboard.live-data');
    Route::post('/dashboard/walk-in', [DashboardController::class, 'storeWalkInOrder'])->name('dashboard.walk-in');
    Route::post('/dashboard/queue/{antrean}/status', [DashboardController::class, 'updateQueueStatus'])->name('dashboard.queue.status');
    Route::post('/dashboard/stock', [DashboardController::class, 'storeStock'])->name('dashboard.stock');
    Route::post('/dashboard/seed-demo', [DashboardController::class, 'seedDemoOrders'])->name('dashboard.seed-demo');
    Route::post('/dashboard/clear-demo', [DashboardController::class, 'clearDemoOrders'])->name('dashboard.clear-demo');

    // Antrean Mesin & Workshop Routes
    Route::get('/antrean', [DashboardController::class, 'antreanIndex'])->name('antrean.index');
    Route::get('/antrean/live-data', [DashboardController::class, 'antreanLiveData'])->name('antrean.live-data');
    Route::post('/antrean/{antrean}/action', [DashboardController::class, 'updateAntreanAlur'])->name('antrean.action');

    // Kasir POS Walk-in Routes
    Route::get('/walk-in', [DashboardController::class, 'walkInIndex'])->name('walkin.index');
    Route::get('/walk-in/live-data', [DashboardController::class, 'walkInLiveData'])->name('walkin.live-data');
    Route::post('/walk-in/checkout', [DashboardController::class, 'storeWalkInCheckout'])->name('walkin.checkout');
    Route::post('/walk-in/seed-demo', [DashboardController::class, 'seedWalkInDemo'])->name('walkin.seed-demo');
    Route::post('/walk-in/clear-demo', [DashboardController::class, 'clearWalkInDemo'])->name('walkin.clear-demo');

    // Manajemen Stok & Logistik Gerai Routes
    Route::get('/stok', [DashboardController::class, 'stokIndex'])->name('stok.index');
    Route::get('/stok/live-data', [DashboardController::class, 'stokLiveData'])->name('stok.live-data');
    Route::post('/stok/catat-masuk', [DashboardController::class, 'storeBahanMasuk'])->name('stok.catat-masuk');
    Route::post('/stok/tambah-item', [DashboardController::class, 'storeTambahItemBaru'])->name('stok.tambah-item');
    Route::post('/stok/{stok}/quick-add', [DashboardController::class, 'quickAddStok'])->name('stok.quick-add');

    // Data Member & Program Hadiah Kunjungan Routes
    Route::get('/member', [DashboardController::class, 'memberIndex'])->name('member.index');
    Route::post('/member/store', [DashboardController::class, 'storeMember'])->name('member.store');
    Route::post('/member/klaim-hadiah', [DashboardController::class, 'storeKlaimHadiah'])->name('member.klaim-hadiah');
    Route::get('/member/scan-ajax', [DashboardController::class, 'scanMemberAjax'])->name('member.scan-ajax');

    // Laporan Penjualan & Analitik Gerai Routes
    Route::get('/laporan', [DashboardController::class, 'laporanIndex'])->name('laporan.index');
    Route::get('/laporan/export', [DashboardController::class, 'laporanExport'])->name('laporan.export');
});

// Protected Customer Portal Routes
Route::middleware(['auth', 'customer.only'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/home', [DashboardController::class, 'customer'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'customer'])->name('dashboard');
    Route::post('/order', [DashboardController::class, 'storeCustomerOrder'])->name('order.store');
});
