<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PengumumanNotificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;

use App\Http\Controllers\Admin\ManajemenSiswaController;
use App\Http\Controllers\Admin\ManajemenGuruController;
use App\Http\Controllers\Admin\MapelController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\NilaiHarianSiswaController;
use App\Http\Controllers\Admin\NilaiUjianSiswaController;
use App\Http\Controllers\Admin\LaporanRaporController;
use App\Http\Controllers\Admin\AdminAnnouncementController;

use App\Http\Controllers\Guru\AbsensiController;
use App\Http\Controllers\Ortu\SiswaController;
use App\Http\Controllers\Guru\InputNilaiHarianController;
use App\Http\Controllers\Guru\InputNilaiUjianController;
use App\Http\Controllers\Guru\CatatanSiswaController;

use App\Http\Controllers\Ortu\JadwalController;
use App\Http\Controllers\Ortu\CekAbsenController;
use App\Http\Controllers\Ortu\NilaiSiswaController;
use App\Http\Controllers\Ortu\InfoAnakController;
use App\Http\Controllers\Ortu\RaporSiswaController;

// --- 1. Rute Halaman Login & Proses Login ---
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');

// (opsional) Kalau mau punya /login juga sebagai alias:
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login.page');

// Proses login
Route::post('/login', [LoginController::class, 'login'])->name('login.process');

Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.reset');

// Logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::prefix('notifikasi/pengumuman')->as('pengumuman.notif.')->group(function () {
    Route::get('/', [PengumumanNotificationController::class, 'index'])->name('index');
    Route::post('/{id}/read', [PengumumanNotificationController::class, 'markAsRead'])->name('read');
    Route::post('/read-all', [PengumumanNotificationController::class, 'markAllAsRead'])->name('readAll');
});

// Rute yang memerlukan autentikasi
Route::middleware('auth')->group(function () {
    // Dashboard & Profil (Akses Universal)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/profile', function () {
        return view('profile.index');
    })->name('profile');

    // --- RUTE ADMIN ---
    Route::prefix('admin')->middleware('role:Admin')->group(function () {
        // Rute Manajemen Guru dan Siswa
        Route::get('/guru', [ManajemenGuruController::class, 'index'])->name('admin.guru');
        Route::post('/guru/store', [ManajemenGuruController::class, 'store'])->name('admin.guru.store');
        Route::post('/guru/update', [ManajemenGuruController::class, 'update'])->name('admin.guru.update');

        Route::get('/siswa', [ManajemenSiswaController::class, 'index'])->name('admin.siswa');
        Route::post('/siswa', [ManajemenSiswaController::class, 'store'])->name('admin.siswa.store');
        Route::post('/siswa/update', [ManajemenSiswaController::class, 'update'])->name('admin.siswa.update');

        Route::get('/mapel', [MapelController::class, 'index'])->name('admin.mapel');
        Route::post('/mapel/store', [MapelController::class, 'store'])->name('admin.mapel.store');
        Route::post('/mapel/update', [MapelController::class, 'update'])->name('admin.mapel.update');

        Route::get('/kelas', [KelasController::class, 'index'])->name('admin.kelas');
        Route::post('/kelas/store', [KelasController::class, 'store'])->name('admin.kelas.store');
        Route::post('/kelas/update', [KelasController::class, 'update'])->name('admin.kelas.update');
        Route::get('/kelas/search-siswa', [KelasController::class, 'searchSiswa']);
        Route::get('/kelas/{id}/siswa', [KelasController::class, 'siswaKelas']);

        // Rute untuk Nilai Harian dan Ujian (TETAP DI SINI)
        Route::group(['prefix' => 'nilai', 'as' => 'admin.nilai.'], function () {
            Route::get('harian', [NilaiHarianSiswaController::class, 'index'])->name('harian');
            Route::get('ujian', [NilaiUjianSiswaController::class, 'index'])->name('ujian');
        });

        Route::prefix('laporan')->name('admin.laporan.')->group(function () {
            Route::get('rapor', [LaporanRaporController::class, 'index'])->name('rapor.index');
            Route::post('rapor/validate-submit', [LaporanRaporController::class, 'validateSubmit'])->name('rapor.validate-submit');
            Route::post('rapor/final-submit', [LaporanRaporController::class, 'finalSubmit'])->name('rapor.final-submit');
            Route::post('rapor/reset/{id}', [LaporanRaporController::class, 'reset'])->name('rapor.reset');
            Route::get('rapor/download', [LaporanRaporController::class, 'download'])->name('rapor.download');
        });

        Route::prefix('pengumuman')->group(function () {
            Route::get('/', [AdminAnnouncementController::class, 'index'])->name('admin.pengumuman.index');
            Route::post('/', [AdminAnnouncementController::class, 'store'])->name('admin.pengumuman.store');
            Route::get('/{pengumuman}', [AdminAnnouncementController::class, 'show'])->name('admin.pengumuman.show');
            Route::put('/{pengumuman}', [AdminAnnouncementController::class, 'update'])->name('admin.pengumuman.update');
            Route::delete('/{pengumuman}', [AdminAnnouncementController::class, 'destroy'])->name('admin.pengumuman.destroy');
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

        Route::group(['prefix' => 'rapor', 'as' => 'guru.rapor.'], function () {
            Route::get('walikelas', [CatatanSiswaController::class, 'index'])->name('walikelas');
            Route::post('walikelas/store', [CatatanSiswaController::class, 'store'])->name('walikelas.store'); // Simpan Catatan
            Route::post('walikelas/lock', [CatatanSiswaController::class, 'lockFinal'])->name('walikelas.lock'); // Kunci Final (AJAX)
        });
    });

    // --- RUTE ORANG TUA (Grup dengan Prefix 'ortu') ---
    Route::prefix('ortu')->middleware('role:Orang Tua')->group(function () {
        // Route untuk memilih siswa aktif (yang dipanggil dari dropdown)
        Route::get('select/{id_siswa}', [SiswaController::class, 'selectSiswa'])->name('siswa.select');
        Route::get('jadwal', [JadwalController::class, 'index'])->name('ortu.jadwal');
        Route::get('absensi', [CekAbsenController::class, 'index'])->name('ortu.absensi'); // <-- BARU
        Route::get('info-anak', [InfoAnakController::class, 'index'])->name('ortu.info-anak');
        Route::get('rapor', [RaporSiswaController::class, 'index'])->name('ortu.rapor');

        Route::group(['prefix' => 'nilai-siswa', 'as' => 'ortu.nilai-siswa.'], function () {
            Route::get('/harian', [NilaiSiswaController::class, 'nilaiHarian'])->name('harian');
            Route::get('/harian/detail/{mapelId}', [NilaiSiswaController::class, 'nilaiHarianDetail'])->name('harian.detail');
            Route::get('/ujian', [NilaiSiswaController::class, 'nilaiUjian'])->name('ujian');
        });
    });
});
