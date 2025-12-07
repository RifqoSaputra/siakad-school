<?php

use Illuminate\Support\Facades\Route;

// ===============================
// AUTH CONTROLLER
// ===============================
use App\Http\Controllers\Auth\LoginController;

// ===============================
// DASHBOARD CONTROLLER (GLOBAL)
// ===============================
use App\Http\Controllers\DashboardController;

// ===============================
// ADMIN CONTROLLERS
// ===============================
use App\Http\Controllers\Admin\ManajemenGuruController;
use App\Http\Controllers\Admin\ManajemenSiswaController;
use App\Http\Controllers\Admin\MapelController;
use App\Http\Controllers\Admin\KelasController;


// ===============================
// RUTE LOGIN & LOGOUT
// ===============================

// Halaman login (root URL)
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');

// Proses login
Route::post('/login', [LoginController::class, 'login'])->name('login.process');

// Logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


// ===============================
// ROUTE YANG HARUS LOGIN
// ===============================
Route::middleware('auth')->group(function () {

    // ---------------------------
    // DASHBOARD UTAMA
    // ---------------------------
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // ---------------------------
    // ADMIN: MASTER DATA
    // ---------------------------
    Route::prefix('admin')->group(function () {

        Route::get('/guru', [ManajemenGuruController::class, 'index'])
            ->name('admin.guru');

        Route::get('/siswa', [ManajemenSiswaController::class, 'index'])
            ->name('admin.siswa');

        Route::get('/mapel', [MapelController::class, 'index'])
            ->name('admin.mapel');

        Route::get('/kelas', [KelasController::class, 'index'])
            ->name('admin.kelas');
    });
});