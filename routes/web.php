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
use App\Http\Controllers\Admin\NilaiHarianSiswaController;
use App\Http\Controllers\Admin\MapelController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\PengumumanController;

// ===============================
// GURU CONTROLLERS
// ===============================
use App\Http\Controllers\Guru\AbsensiController;
use App\Http\Controllers\Guru\InputNilaiHarianController;
use App\Http\Controllers\Guru\InputNilaiUjianController;

// ===============================
// ORANG TUA CONTROLLERS
// ===============================
use App\Http\Controllers\Ortu\SiswaController;
use App\Http\Controllers\Ortu\JadwalController;
use App\Http\Controllers\Ortu\CekAbsenController;
use App\Http\Controllers\Ortu\NilaiSiswaController;
use App\Http\Controllers\Ortu\InfoAnakController;
use App\Http\Controllers\PengumumanNotificationController;


// ===============================
// RUTE LOGIN & LOGOUT
// ===============================

// Halaman login (root URL)
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');

// (opsional) Kalau mau punya /login juga sebagai alias:
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login.page');

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
        ->name('dashboard');   // dipakai di error tadi

    // (opsional) kalau ada kode lama yang masih pakai 'dashboard.index':
    // Route::get('/dashboard', [DashboardController::class, 'index'])
    //     ->name('dashboard.index');


    // ---------------------------
    // ADMIN: MASTER DATA + NILAI
    // ---------------------------
    Route::prefix('admin')->middleware('role:Admin')->group(function () {

        // Master data
        Route::get('/guru', [ManajemenGuruController::class, 'index'])
            ->name('admin.guru');

        Route::get('/siswa', [ManajemenSiswaController::class, 'index'])
            ->name('admin.siswa');

        Route::get('/mapel', [MapelController::class, 'index'])
            ->name('admin.mapel');

        Route::get('/kelas', [KelasController::class, 'index'])
            ->name('admin.kelas');

        // Nilai (dari kode GitHub lama)
        Route::group(['prefix' => 'nilai', 'as' => 'admin.nilai.'], function () {
            Route::get('harian', [NilaiHarianSiswaController::class, 'index'])->name('harian');

            Route::get('ujian', function () {
                return view('dashboard.admin.nilai-siswa.nilai-ujian-siswa');
            })->name('ujian');
        });
    });


    // ---------------------------
    // ADMIN: PENGUMUMAN
    // ---------------------------
    Route::prefix('admin/pengumuman')->as('admin.pengumuman.')->middleware('role:Admin')->group(function () {
        Route::get('/', [PengumumanController::class, 'index'])->name('index');
        Route::get('/create', [PengumumanController::class, 'create'])->name('create');
        Route::post('/', [PengumumanController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [PengumumanController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PengumumanController::class, 'update'])->name('update');
        Route::delete('/{id}', [PengumumanController::class, 'destroy'])->name('destroy');
    });


    // ---------------------------
    // RUTE GURU
    // ---------------------------
    Route::prefix('guru')->middleware('role:Guru')->group(function () {
        // Nilai harian
        Route::group(['prefix' => 'nilai/harian', 'as' => 'nilai.harian.'], function () {
            Route::get('/', [InputNilaiHarianController::class, 'index'])->name('index'); // filter
            Route::get('/rekap', [InputNilaiHarianController::class, 'rekap'])->name('rekap'); // API data rekap
            Route::get('/{nilaiTambahan}/data-input', [InputNilaiHarianController::class, 'getSiswaForInput'])->name('data.input');
            Route::post('/', [InputNilaiHarianController::class, 'store'])->name('store'); // simpan tugas baru
            Route::post('/submit', [InputNilaiHarianController::class, 'submitNilaiHarian'])->name('submit');
            Route::post('/save-nilai', [InputNilaiHarianController::class, 'saveNilai'])->name('save.nilai');
            Route::delete('/{nilaiTambahan}', [InputNilaiHarianController::class, 'destroy'])->name('destroy');
        });

        // Nilai ujian
        Route::group(['prefix' => 'nilai/ujian', 'as' => 'nilai.ujian.'], function () {
            Route::get('/', [InputNilaiUjianController::class, 'index'])->name('index');
            Route::get('/api/list', [InputNilaiUjianController::class, 'getUjian'])->name('api');
            Route::get('/rekap', [InputNilaiUjianController::class, 'rekap'])->name('rekap');
            Route::get('/{nilaiUjian}/data-input', [InputNilaiUjianController::class, 'getSiswaForInput'])->name('data');
            Route::post('/save-nilai', [InputNilaiUjianController::class, 'saveNilai'])->name('save');
            Route::post('/submit/{id}', [InputNilaiUjianController::class, 'submitFinal'])->name('submitFinal');
        });

        // Jadwal guru
        Route::get('jadwal', function () {
            return view('dashboard.guru.jadwal.index');
        })->name('guru.jadwal');

        // Absensi guru
        Route::get('absensi/kelas', [AbsensiController::class, 'index'])->name('guru.absensi.kelas');
        Route::get('absensi/kelas/detail/{id_jadwal}', [AbsensiController::class, 'detail'])->name('guru.absensi.detail');
        Route::post('absensi/kelas/store/{id_jadwal}', [AbsensiController::class, 'store'])->name('guru.absensi.store');
    });


    // ---------------------------
    // RUTE ORANG TUA
    // ---------------------------
    Route::prefix('ortu')->middleware('role:Orang Tua')->group(function () {

        // Dipanggil dari dropdown untuk memilih siswa aktif
        Route::get('select/{id_siswa}', [SiswaController::class, 'selectSiswa'])->name('siswa.select');

        Route::get('jadwal', [JadwalController::class, 'index'])->name('ortu.jadwal');

        Route::get('absensi', [CekAbsenController::class, 'index'])->name('ortu.absensi');

        // Info anak
        Route::get('info-anak', [InfoAnakController::class, 'index'])->name('ortu.info-anak');

        // Rapor
        Route::get('rapor', function () {
            return view('dashboard.ortu.rapor-siswa');
        })->name('ortu.rapor');

        // Nilai siswa
        Route::group(['prefix' => 'nilai-siswa', 'as' => 'ortu.nilai-siswa.'], function () {
            Route::get('/harian', [NilaiSiswaController::class, 'nilaiHarian'])->name('harian');
            Route::get('/harian/detail/{mapelId}', [NilaiSiswaController::class, 'nilaiHarianDetail'])->name('harian.detail');
            Route::get('/ujian', [NilaiSiswaController::class, 'nilaiUjian'])->name('ujian');
        });
    });

    // ---------------------------
    // NOTIFIKASI PENGUMUMAN (SEMUA ROLE LOGIN)
    // ---------------------------
    Route::prefix('notifikasi/pengumuman')->as('pengumuman.notif.')->group(function () {
        Route::get('/', [PengumumanNotificationController::class, 'index'])->name('index');
        Route::post('/{id}/read', [PengumumanNotificationController::class, 'markAsRead'])->name('read');
        Route::post('/read-all', [PengumumanNotificationController::class, 'markAllAsRead'])->name('readAll');
    });
});
