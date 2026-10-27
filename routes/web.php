<?php

use App\Http\Controllers\AuthController;
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
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    // Manajemen tenant hanya untuk admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/tenant', [TenantController::class, 'index'])->name('tenant.index');
        Route::get('/tenant/create', [TenantController::class, 'create'])->name('tenant.create');
        Route::post('/tenant', [TenantController::class, 'store'])->name('tenant.store');
        Route::get('/tenant/{id_tenant}/edit', [TenantController::class, 'edit'])->name('tenant.edit');
        Route::put('/tenant/{id_tenant}', [TenantController::class, 'update'])->name('tenant.update');
        Route::delete('/tenant/{id_tenant}', [TenantController::class, 'destroy'])->name('tenant.destroy');
    });
});
