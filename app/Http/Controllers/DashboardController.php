<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

// MODEL
use App\Models\SIAKAD\SCHOOL\Guru;
use App\Models\SIAKAD\SCHOOL\JadwalMapel;
use App\Models\SIAKAD\SCHOOL\NilaiTambahan; // nilai harian
use App\Models\SIAKAD\SCHOOL\NilaiUjian;     // nilai ujian

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\SIAKAD\SCHOOL\User $user */
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return $this->adminIndex();
        }

        if ($user->hasRole('guru')) {
            return $this->guruIndex();
        }

        if ($user->hasRole('Orang Tua')) {
            return $this->ortuIndex();
        }

        return redirect()->route('profile')
            ->with('error', 'Akses dashboard tidak terdefinisi.');
    }


    /* ============================================================
     |  DASHBOARD GURU
     ============================================================ */
    public function guruIndex()
    {
        $user = Auth::user();
        $guru  = $user->guru; // relasi hasOne

        if (!$guru) {
            return abort(403, 'Data guru tidak ditemukan.');
        }

        // 1. Wali kelas (kelas yang dipegang oleh guru)
        $waliKelas = $guru->kelasWali()->first();

        // 2. Total Mapel Diampu
        $totalMapel = $guru->penugasanMapel()->count();

        // 3. Jadwal mengajar hari ini
        $hariIni = strtolower(now()->format('l'));
        // misal: monday, tuesday, friday → pastikan DB isi: senin, selasa, jumat
        // kalau pakai bahasa Indonesia, sesuaikan!

        $jadwalHariIni = JadwalMapel::whereIn(
            'guru_mapel_id',
            $guru->penugasanMapel()->pluck('guru_mapel_id')
        )
            ->where('hari', $hariIni)
            ->with(['kelas', 'penugasan.mapel'])
            ->orderBy('jam_mulai')
            ->get();

        // 4. Penilaian Harian terbaru (nilai_tambahan)
        $nilaiHarian = NilaiTambahan::whereIn(
            'guru_mapel_id',
            $guru->penugasanMapel()->pluck('guru_mapel_id')
        )
            ->with(['mapel', 'kelas'])
            ->orderBy('tgl_entry', 'desc')
            ->limit(5)
            ->get();

        // 5. Nilai Ujian terbaru
        $nilaiUjian = NilaiUjian::whereIn(
            'guru_mapel_id',
            $guru->penugasanMapel()->pluck('guru_mapel_id')
        )
            ->with(['mapel', 'kelas'])
            ->orderBy('tanggal_ujian', 'desc')
            ->limit(5)
            ->get();

        $tanggalHariIni = Carbon::now()->translatedFormat('l, d F Y');
        
        return view('dashboard.guru.index', [
            'guru'         => $guru,
            'waliKelas'    => $waliKelas,
            'totalMapel'   => $totalMapel,
            'jadwalHariIni' => $jadwalHariIni,
            'nilaiHarian'  => $nilaiHarian,
            'nilaiUjian'   => $nilaiUjian,
            'tanggalHariIni'  => $tanggalHariIni,
        ]);
    }


    /* ============================================================
     |  DASHBOARD ADMIN & ORTU (placeholder)
     ============================================================ */

    public function adminIndex()
    {
        return view('dashboard.admin.index');
    }

    public function ortuIndex()
    {
        return view('dashboard.ortu.index');
    }
}
