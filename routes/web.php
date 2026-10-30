<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PanenController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('login'));

// Route Guest (Hanya bisa diakses jika belum login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Khusus Admin Pusat saja
    Route::middleware(['role:admin_pusat'])->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('roles', RoleController::class)->except(['show']);
    });

// Route Authenticated (Sudah Login)
Route::middleware(['auth'])->group(function () {
    Route::middleware(['permission:view dashboard'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/panen', [PanenController::class, 'index'])->name('panen.index');
    });

    Route::middleware(['permission:create panen'])->group(function () {
        Route::get('/panen/create', [PanenController::class, 'create'])->name('panen.create');
        Route::post('/panen', [PanenController::class, 'store'])->name('panen.store');
    });

    Route::middleware(['permission:delete panen'])->group(function () {
        Route::delete('/panen/{panen}', [PanenController::class, 'destroy'])->name('panen.destroy');
    });

    Route::get('/panen/export/excel', [PanenController::class, 'exportExcel'])->name('panen.export.excel');
    Route::get('/panen/export/pdf', [PanenController::class, 'exportPdf'])->name('panen.export.pdf');
});
