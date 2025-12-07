<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\Admin\ManajemenSiswaController;
use App\Http\Controllers\Admin\ManajemenGuruController;
use App\Http\Controllers\Admin\NilaiHarianSiswaController;

use App\Http\Controllers\Guru\AbsensiController;
use App\Http\Controllers\Ortu\SiswaController;
use App\Http\Controllers\Guru\InputNilaiHarianController;
use App\Http\Controllers\Guru\InputNilaiUjianController;

use App\Http\Controllers\Ortu\JadwalController;
use App\Http\Controllers\Ortu\CekAbsenController; // <-- BARU
use App\Http\Controllers\Ortu\NilaiSiswaController;


// --- 1. Rute Halaman Login & Proses Login ---
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login.page');
Route::post('/login', [LoginController::class, 'login'])->name('login.action');

// Rute yang memerlukan autentikasi
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard & Profil (Akses Universal)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/profile', function () {
        return view('profile.index');
    })->name('profile');

    // --- RUTE ADMIN ---
    Route::prefix('admin')->middleware('role:Admin')->group(function () {
        // Rute Manajemen Guru dan Siswa
        Route::get('guru', [ManajemenGuruController::class, 'index'])->name('admin.guru');
        Route::get('siswa', [ManajemenSiswaController::class, 'index'])->name('admin.siswa');

        Route::group(['prefix' => 'nilai', 'as' => 'admin.nilai.'], function () {
            Route::get('harian', [NilaiHarianSiswaController::class, 'index'])->name('harian');

            Route::get('ujian', function () {
                return view('dashboard.admin.nilai-siswa.nilai-ujian-siswa');
            })->name('ujian');
        });
    });

    // --- RUTE GURU ---
    Route::prefix('guru')->middleware('role:Guru')->group(function () {
        Route::group(['prefix' => 'nilai/harian', 'as' => 'nilai.harian.'], function () {
            Route::get('/', [InputNilaiHarianController::class, 'index'])->name('index'); // Untuk filter
            Route::get('/rekap', [InputNilaiHarianController::class, 'rekap'])->name('rekap'); // BARU: Untuk API data rekap
            Route::get('/{nilaiTambahan}/data-input', [InputNilaiHarianController::class, 'getSiswaForInput'])->name('data.input');
            Route::post('/', [InputNilaiHarianController::class, 'store'])->name('store'); // Untuk menyimpan tugas baru
            Route::post('/submit', [InputNilaiHarianController::class, 'submitNilaiHarian'])->name('submit');
            Route::post('/save-nilai', [InputNilaiHarianController::class, 'saveNilai'])->name('save.nilai');
            Route::delete('/{nilaiTambahan}', [InputNilaiHarianController::class, 'destroy'])->name('destroy');
        });

        Route::group(['prefix' => 'nilai/ujian', 'as' => 'nilai.ujian.'], function () {
            Route::get('/', [InputNilaiUjianController::class, 'index'])->name('index');
            Route::get('/api/list', [InputNilaiUjianController::class, 'getUjian'])->name('api');
            Route::get('/rekap', [InputNilaiUjianController::class, 'rekap'])->name('rekap');
            Route::get('/{nilaiUjian}/data-input', [InputNilaiUjianController::class, 'getSiswaForInput'])->name('data');
            Route::post('/save-nilai', [InputNilaiUjianController::class, 'saveNilai'])->name('save');
            Route::post('/submit/{id}', [InputNilaiUjianController::class, 'submitFinal'])->name('submitFinal');
        });

        // Rute jadwal guru URL menjadi /guru/jadwal
        Route::get('jadwal', function () {
            return view('guru.jadwal.index');
        })->name('guru.jadwal');

        Route::get('absensi/kelas', [AbsensiController::class, 'index'])->name('guru.absensi.kelas');
        Route::get('absensi/kelas/detail/{id_jadwal}', [AbsensiController::class, 'detail'])->name('guru.absensi.detail');
        Route::post('absensi/kelas/store/{id_jadwal}', [AbsensiController::class, 'store'])->name('guru.absensi.store');
    });

    // --- RUTE ORANG TUA (Grup dengan Prefix 'ortu') ---
    // URL akan menjadi /ortu/info-anak, /ortu/rapor, dsb.
    Route::prefix('ortu')->middleware('role:Orang Tua')->group(function () {
        // Route untuk memilih siswa aktif (yang dipanggil dari dropdown)
        Route::get('select/{id_siswa}', [SiswaController::class, 'selectSiswa'])->name('siswa.select');
        Route::get('jadwal', [JadwalController::class, 'index'])->name('ortu.jadwal');
        Route::get('absensi', [CekAbsenController::class, 'index'])->name('ortu.absensi'); // <-- BARU
        Route::get('info-anak', function () {
            return view('ortu.info-anak.index');
        })->name('ortu.info-anak');

        // ROUTE RAPOR TELAH DIMODIFIKASI AGAR LANGSUNG MERETURN VIEW BLADE
        Route::get('rapor', function () {
            // Mengarahkan langsung ke views/dashboard/ortu/rapor-siswa.blade.php
            return view('dashboard.ortu.rapor-siswa');
        })->name('ortu.rapor');

        Route::group(['prefix' => 'nilai-siswa', 'as' => 'ortu.nilai-siswa.'], function () {
            Route::get('/harian', [NilaiSiswaController::class, 'nilaiHarian'])->name('harian');
            Route::get('/harian/detail/{mapelId}', [NilaiSiswaController::class, 'nilaiHarianDetail'])->name('harian.detail');
            Route::get('/ujian', [NilaiSiswaController::class, 'nilaiUjian'])->name('ujian');
        });
    });
});
