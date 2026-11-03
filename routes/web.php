<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\MobilController;
use App\Http\Controllers\PemesananMitraController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\SupirController;
use App\Http\Controllers\TenantAkunController;
use App\Http\Controllers\TenantController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Autentikasi
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:6,1')
        ->name('login.attempt');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:6,1')
        ->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Area setelah login
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Pelanggan diarahkan ke beranda; mitra & admin ke dashboard masing-masing
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ---- Pelanggan
    Route::middleware('role:pelanggan')->group(function () {
        Route::get('/beranda', [BerandaController::class, 'index'])->name('beranda');
        Route::get('/mobil/{id}', [BerandaController::class, 'show'])->whereNumber('id')->name('mobil.detail');

        Route::get('/pesanan', [PesananController::class, 'index'])->name('pesanan.index');
        Route::get('/pesanan/{kode}', [PesananController::class, 'show'])->name('pesanan.show');
        Route::patch('/pesanan/{kode}/batal', [PesananController::class, 'batal'])->name('pesanan.batal');
    });

    // ---- Mitra (admin_rental & staff)
    Route::middleware('role:mitra')->prefix('mitra')->name('mitra.')->group(function () {
        Route::resource('mobil', MobilController::class)->except('show');
        Route::resource('kategori', KategoriController::class)->except('show');
        Route::resource('supir', SupirController::class)->except('show');

        Route::get('pemesanan', [PemesananMitraController::class, 'index'])->name('pemesanan.index');
        Route::get('pemesanan/{id}', [PemesananMitraController::class, 'show'])->whereNumber('id')->name('pemesanan.show');
        Route::patch('pemesanan/{id}/status', [PemesananMitraController::class, 'status'])->whereNumber('id')->name('pemesanan.status');
    });

    // ---- Super admin
    Route::middleware('role:admin')->group(function () {
        Route::resource('tenant', TenantController::class);
        Route::post('tenant/{tenant}/akun', [TenantAkunController::class, 'store'])->name('tenant.akun.store');
        Route::patch('tenant/{tenant}/akun/{user}', [TenantAkunController::class, 'toggle'])->name('tenant.akun.toggle');
    });
});
