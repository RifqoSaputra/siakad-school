<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController; 

// --- 1. Rute Halaman Login & Proses Login (Mengatasi MethodNotAllowed) ---
// Rute GET / (root) dan /login akan menampilkan form login
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login.page'); // Tambahkan rute GET ini
 
// Rute POST Login
Route::post('/login', [LoginController::class, 'login'])->name('login.action');

// Rute yang memerlukan autentikasi
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // Dashboard universal
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index'); 
    
    // Rute profil
    Route::get('/profile', function () {
        // Ganti dengan view yang sesuai jika Anda sudah membuatnya
        return view('profile.index'); 
    })->name('profile');

    // --- 2. RUTE DINAMIS UNTUK PENGUJIAN ---
    // Pastikan Anda telah mengimpor middleware 'role' jika menggunakannya di sini
    
    // Rute ADMIN (Menu ID 1, 2, 3)
    Route::prefix('admin')->group(function () {
        Route::get('guru', function () {
            return view('admin.guru.index');
        })->name('admin.guru');
        
        Route::get('siswa', function () {
            return view('admin.siswa.index');
        })->name('admin.siswa');
    });

    // Rute GURU (Menu ID 4, 5, 6)
    Route::prefix('guru')->group(function () {
        Route::get('nilai', function () {
            return view('guru.nilai.index');
        })->name('guru.nilai');

        Route::get('jadwal', function () {
            return view('guru.jadwal.index');
        })->name('guru.jadwal');
    });

    // Rute ORANG TUA (Menu ID 7, 8)
    Route::prefix('ortu')->group(function () {
        Route::get('info-anak', function () {
            return view('ortu.info-anak.index');
        })->name('ortu.info-anak');

        Route::get('rapor', function () {
            return view('ortu.rapor.index');
        })->name('ortu.rapor');
    });
});