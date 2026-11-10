<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PanenController;
use App\Http\Controllers\StokBenihController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BenihController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\SystemSettingController;
use App\Http\Controllers\Admin\Reports\AdminRekapReportController;
use App\Http\Controllers\Petugas\Reports\PetugasRekapReportController;

Route::get('/', fn() => redirect()->route('login'));

// Route Guest
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Route Authenticated
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/panen', [PanenController::class, 'index'])->name('panen.index');
    Route::get('/stok-benih', [StokBenihController::class, 'index'])->name('stok.index');
    Route::get('/stok-benih/riwayat', [StokBenihController::class, 'riwayatMutasi'])->name('stok.riwayat');

    // Akses Manajemen Wilayah & Gudang BPSIP (Admin Pusat & Petugas Provinsi)
    Route::middleware(['role:admin_pusat|petugas_provinsi'])->group(function () {
        Route::resource('wilayah', WilayahController::class)->except(['create', 'edit', 'show']);
    });

    // Admin Pusat Management & Laporan Rekap Nasional
    Route::middleware(['role:admin_pusat'])->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('benih', BenihController::class)->except(['show']);
        Route::get('/logs', [ActivityLogController::class, 'index'])->name('logs.index');
        Route::get('/settings', [SystemSettingController::class, 'index'])->name('settings.index');

        // MANAJEMEN RIWAYAT MUTASI (KHUSUS ADMIN PUSAT)
        Route::get('/stok-benih/riwayat/{id}/edit', [StokBenihController::class, 'editRiwayat'])->name('stok.riwayat.edit');
        Route::put('/stok-benih/riwayat/{id}', [StokBenihController::class, 'updateRiwayat'])->name('stok.riwayat.update');
        Route::delete('/stok-benih/riwayat/{id}', [StokBenihController::class, 'destroyRiwayat'])->name('stok.riwayat.destroy');

        // LAPORAN ADMIN PUSAT
        Route::get('/admin/rekap-bbpsip', [AdminRekapReportController::class, 'index'])->name('admin.report.rekap');
        Route::get('/admin/rekap-bbpsip/pdf', [AdminRekapReportController::class, 'exportPdf'])->name('admin.report.rekap.pdf');
    });

    // Petugas Provinsi Laporan Rekap Wilayah
    Route::middleware(['role:petugas_provinsi'])->group(function () {
        Route::get('/petugas/rekap-bbpsip', [PetugasRekapReportController::class, 'index'])->name('petugas.report.rekap');
        Route::get('/petugas/rekap-bbpsip/pdf', [PetugasRekapReportController::class, 'exportPdf'])->name('petugas.report.rekap.pdf');
    });

    // Transaksi (Admin & Petugas)
    Route::middleware(['role:admin_pusat|petugas_provinsi'])->group(function () {
        Route::post('/panen', [PanenController::class, 'store'])->name('panen.store');
        Route::put('/panen/{panen}', [PanenController::class, 'update'])->name('panen.update');
        Route::delete('/panen/{panen}', [PanenController::class, 'destroy'])->name('panen.destroy');
            
        Route::post('/stok-benih/keluar', [StokBenihController::class, 'storeKeluar'])->name('stok.keluar.store');
        Route::post('/stok-benih/masuk', [StokBenihController::class, 'storeMasuk'])->name('stok.masuk.store');

        Route::get('/stok-benih/{id}/edit', [StokBenihController::class, 'edit'])->name('stok.edit');
        Route::put('/stok-benih/{id}', [StokBenihController::class, 'update'])->name('stok.update');
        Route::delete('/stok-benih/{id}', [StokBenihController::class, 'destroy'])->name('stok.destroy');
    });
    
});