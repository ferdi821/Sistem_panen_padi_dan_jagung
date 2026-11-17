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

    // Halaman Monitoring Utama (Admin Pusat, Pimpinan & Petugas Provinsi)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/panen', [PanenController::class, 'index'])->name('panen.index');
    Route::get('/stok-benih', [StokBenihController::class, 'index'])->name('stok.index');
    Route::get('/stok-benih/riwayat', [StokBenihController::class, 'riwayatMutasi'])->name('stok.riwayat');

    // View-only Master Varietas & Sebaran Wilayah Gudang (Bisa dipantau Pimpinan juga)
    Route::middleware(['role:admin_pusat|petugas_provinsi|pimpinan'])->group(function () {
        Route::get('/benih', [BenihController::class, 'index'])->name('benih.index');
        Route::get('/wilayah', [WilayahController::class, 'index'])->name('wilayah.index');
    });

    // Moderasi Edit & Hapus Master Wilayah (Khusus Admin Pusat & Petugas Provinsi)
    Route::middleware(['role:admin_pusat|petugas_provinsi'])->group(function () {
        Route::put('/wilayah/{wilayah}', [WilayahController::class, 'update'])->name('wilayah.update');
        Route::delete('/wilayah/{wilayah}', [WilayahController::class, 'destroy'])->name('wilayah.destroy');
    });

    // Laporan Rekap Nasional & Audit Log (Dapat diakses Admin Pusat & Pimpinan)
    Route::middleware(['role:admin_pusat|pimpinan'])->group(function () {
        Route::get('/admin/rekap-bbpsip', [AdminRekapReportController::class, 'index'])->name('admin.report.rekap');
        Route::get('/admin/rekap-bbpsip/pdf', [AdminRekapReportController::class, 'exportPdf'])->name('admin.report.rekap.pdf');
        Route::get('/admin/rekap-bbpsip/excel', [AdminRekapReportController::class, 'exportExcel'])->name('admin.report.rekap.excel');
        Route::get('/logs', [ActivityLogController::class, 'index'])->name('logs.index');
    });

    // Admin Pusat Management
    Route::middleware(['role:admin_pusat'])->group(function () {
        Route::resource('users', UserController::class);
        
        // PENGATURAN SISTEM (GET & POST)
        Route::get('/settings', [SystemSettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SystemSettingController::class, 'update'])->name('settings.update');

        // MANAJEMEN RIWAYAT MUTASI (KHUSUS ADMIN PUSAT)
        Route::get('/stok-benih/riwayat/{id}/edit', [StokBenihController::class, 'editRiwayat'])->name('stok.riwayat.edit');
        Route::put('/stok-benih/riwayat/{id}', [StokBenihController::class, 'updateRiwayat'])->name('stok.riwayat.update');
        Route::delete('/stok-benih/riwayat/{id}', [StokBenihController::class, 'destroyRiwayat'])->name('stok.riwayat.destroy');
    });

    // Petugas Provinsi: Laporan Rekap Wilayah & SEMUA OPERASIONAL INPUT/EDIT/HAPUS
    Route::middleware(['role:petugas_provinsi'])->group(function () {
        // Laporan Rekap (PDF & EXCEL)
        Route::get('/petugas/rekap-bbpsip', [PetugasRekapReportController::class, 'index'])->name('petugas.report.rekap');
        Route::get('/petugas/rekap-bbpsip/pdf', [PetugasRekapReportController::class, 'exportPdf'])->name('petugas.report.rekap.pdf');
        Route::get('/petugas/rekap-bbpsip/excel', [PetugasRekapReportController::class, 'exportExcel'])->name('petugas.report.rekap.excel');

        // CRUD Master Benih (Khusus Petugas)
        Route::resource('benih', BenihController::class)->except(['index', 'show']);

        // Tambah Master Wilayah & Gudang (Khusus Petugas)
        Route::post('/wilayah', [WilayahController::class, 'store'])->name('wilayah.store');

        // CRUD Data Panen (Khusus Petugas)
        Route::post('/panen', [PanenController::class, 'store'])->name('panen.store');
        Route::put('/panen/{panen}', [PanenController::class, 'update'])->name('panen.update');
        Route::delete('/panen/{panen}', [PanenController::class, 'destroy'])->name('panen.destroy');

        // Transaksi & CRUD Stok Benih Wilayah (Khusus Petugas)
        Route::post('/stok-benih/target', [StokBenihController::class, 'storeTarget'])->name('stok.target.store');
        Route::post('/stok-benih/keluar', [StokBenihController::class, 'storeKeluar'])->name('stok.keluar.store');
        Route::post('/stok-benih/masuk', [StokBenihController::class, 'storeMasuk'])->name('stok.masuk.store');
        Route::get('/stok-benih/{id}/edit', [StokBenihController::class, 'edit'])->name('stok.edit');
        Route::put('/stok-benih/{id}', [StokBenihController::class, 'update'])->name('stok.update');
        Route::delete('/stok-benih/{id}', [StokBenihController::class, 'destroy'])->name('stok.destroy');
    });
    
});