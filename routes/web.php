<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BenihController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PanenController;
use App\Http\Controllers\PengujianMutuController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StokBenihController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('login'));

// Route Guest
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Route Authenticated (Bisa diakses SEMUA role termasuk Pimpinan)
Route::middleware(['auth'])->group(function () {

    // Dashboard & PDF (Read-Only)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/rekap-bbpsip', [ReportController::class, 'rekapBbpsip'])->name('report.rekap');
    Route::get('/report/rekap/pdf', [ReportController::class, 'rekapPdf'])->name('report.rekap.pdf');
    Route::get('/report/panen/pdf', [ReportController::class, 'panenPdf'])->name('report.panen.pdf');
    Route::get('/report/stok/pdf', [ReportController::class, 'stokPdf'])->name('report.stok.pdf');

    // Read-Only Index & Riwayat
    Route::get('/panen', [PanenController::class, 'index'])->name('panen.index');
    Route::get('/pengujian-mutu', [PengujianMutuController::class, 'index'])->name('pengujian.index');
    Route::get('/stok-benih', [StokBenihController::class, 'index'])->name('stok.index');
    Route::get('/stok-benih/riwayat', [StokBenihController::class, 'riwayatMutasi'])->name('stok.riwayat');

    // Admin Pusat Management
    Route::middleware(['role:admin_pusat'])->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('roles', RoleController::class)->except(['show']);
        Route::resource('benih', BenihController::class)->except(['show']);

        // Kelola & Batal Transaksi Mutasi (Khusus Admin Pusat)
        Route::get('/stok-benih/riwayat/{id}/edit', [StokBenihController::class, 'editRiwayat'])->name('stok.riwayat.edit');
        Route::put('/stok-benih/riwayat/{id}', [StokBenihController::class, 'updateRiwayat'])->name('stok.riwayat.update');
        Route::delete('/stok-benih/riwayat/{id}', [StokBenihController::class, 'destroyRiwayat'])->name('stok.riwayat.destroy');
    });

    // Fitur Input/Create Data & Manajemen (Admin Pusat & Petugas Provinsi)
    Route::middleware(['role:admin_pusat|petugas_provinsi'])->group(function () {
        // Panen
        Route::get('/panen/create', [PanenController::class, 'create'])->name('panen.create');
        Route::post('/panen', [PanenController::class, 'store'])->name('panen.store');
        Route::delete('/panen/{panen}', [PanenController::class, 'destroy'])->name('panen.destroy');

        // Uji Mutu
        Route::get('/pengujian-mutu/create', [PengujianMutuController::class, 'create'])->name('pengujian.create');
        Route::post('/pengujian-mutu', [PengujianMutuController::class, 'store'])->name('pengujian.store');
        Route::get('/pengujian-mutu/{id}/edit', [PengujianMutuController::class, 'edit'])->name('pengujian.edit');
        Route::put('/pengujian-mutu/{id}', [PengujianMutuController::class, 'update'])->name('pengujian.update');
        Route::delete('/pengujian-mutu/{id}', [PengujianMutuController::class, 'destroy'])->name('pengujian.destroy');

        // Transaksi Stok Keluar
        Route::get('/stok-benih/keluar', [StokBenihController::class, 'createKeluar'])->name('stok.keluar.create');
        Route::post('/stok-benih/keluar', [StokBenihController::class, 'storeKeluar'])->name('stok.keluar.store');

        // Edit & Hapus Stok Benih Wilayah
        Route::get('/stok-benih/{id}/edit', [StokBenihController::class, 'edit'])->name('stok.edit');
        Route::put('/stok-benih/{id}', [StokBenihController::class, 'update'])->name('stok.update');
        Route::delete('/stok-benih/{id}', [StokBenihController::class, 'destroy'])->name('stok.destroy');
    });
    
});