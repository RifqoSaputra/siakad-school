<?php
// app/Http/Controllers/Admin/ManajemenSiswaController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SIAKAD\SCHOOL\Siswa;
// Pastikan Anda memiliki model untuk Kelas Aktif/Kelas jika diperlukan untuk relasi,
// atau kita langsung pakai query builder/DB Facade.
use Illuminate\Support\Facades\DB;

class ManajemenSiswaController extends Controller
{
    /**
     * Menampilkan daftar siswa aktif dengan filtering dan ringkasan.
     */
    public function index(Request $request)
    {
        // 1. Dapatkan Tahun Ajaran Aktif (Default & Dropdown Data)
        // Kita ambil semua tahun ajaran unik dari kelas aktif untuk dropdown
        $allTahunAjaran = DB::table('kelas_aktif')
            ->select('tahun_ajaran')
            ->distinct()
            ->orderBy('tahun_ajaran', 'desc')
            ->pluck('tahun_ajaran')
            ->toArray();
        
        // Tentukan tahun ajaran aktif/terpilih (default ke yang terbaru jika ada)
        $defaultTahunAjaran = !empty($allTahunAjaran) ? $allTahunAjaran[0] : '2025/2026';
        $tahunAjaranAktif = $request->get('tahun_ajaran', $defaultTahunAjaran);

        // 2. Data Dropdown Kelas (Mengambil data kelas unit)
        // Gabungkan Tingkat, Nama, dan Bagian Kelas untuk nama yang jelas di filter
        $allKelas = DB::table('kelas')
            ->select('kelas_id', DB::raw("CONCAT(tingkat_kelas, ' ', nama_kelas, ' ', bagian_kelas) AS nama_kelas_lengkap"))
            ->orderBy('tingkat_kelas', 'asc')
            ->orderBy('nama_kelas', 'asc')
            ->orderBy('bagian_kelas', 'asc')
            ->get();
        
        // 3. Query Dasar Siswa (Aktif di Tahun Ajaran Terpilih)
        
        $siswaQuery = Siswa::select('siswa.nis', 'siswa.nama', 'siswa.tgl_lahir', 'siswa.tempat_lahir', 'siswa.jenis_kelamin')
            ->addSelect(DB::raw("CONCAT(k.tingkat_kelas, ' ', k.nama_kelas, ' ', k.bagian_kelas) AS kelas_sekarang"))
            
            // Join SiswaKelas untuk penempatan
            ->join('siswa_kelas as sk', 'siswa.id_siswa', '=', 'sk.id_siswa')
            
            // Join KelasAktif (untuk filter Tahun Ajaran)
            ->join('kelas_aktif as ka', function ($join) use ($tahunAjaranAktif) {
                $join->on('sk.id_kelas_aktif', '=', 'ka.id_kelas_aktif')
                    ->where('ka.tahun_ajaran', $tahunAjaranAktif); 
            })
            
            // Join Kelas (untuk mendapatkan nama kelas lengkap)
            ->join('kelas as k', 'ka.kelas_id', '=', 'k.kelas_id');
            
        // Catatan: Asumsi kolom 'id_siswa' adalah PK di tabel 'siswa'.
        // Jika PK adalah 'id', ganti 'siswa.id_siswa' menjadi 'siswa.id' di semua join.

        // 4. Hitung Ringkasan Card (Sebelum Filter Applied)
        // Total Siswa AKTIF (Status = 1 di tahun ajaran terpilih)
        $totalSiswaAktif = (clone $siswaQuery)->where('sk.status', 1)->count();

        // Total Siswa NON-AKTIF / Perlu Aksi (Status = 0 di tahun ajaran terpilih)
        // Ini adalah siswa yang mungkin sudah LULUS, PINDAH, atau BELUM ditempatkan.
        // Kita hitung jumlah siswa yang ADA entry di kelas_aktif TAHUN INI, tetapi statusnya BUKAN 1.
        $totalSiswaNonAktif = DB::table('siswa_kelas as sk')
            ->join('kelas_aktif as ka', 'sk.id_kelas_aktif', '=', 'ka.id_kelas_aktif')
            ->where('ka.tahun_ajaran', $tahunAjaranAktif)
            ->where('sk.status', 0) // Asumsi 0 = Non-Aktif/Perlu Aksi/Lulus
            ->distinct('sk.id_siswa') // Menghitung per siswa unik
            ->count('sk.id_siswa');


        // 5. Terapkan Filter Tambahan (Hanya untuk Tampilan Tabel)
        $siswaQuery->where('sk.status', 1); // Default: Hanya tampilkan siswa yang aktif statusnya (1)

        // FILTER 1: Search (NIS atau Nama)
        if ($search = $request->get('search')) {
            $siswaQuery->where(function($query) use ($search) {
                $query->where('siswa.nama', 'like', '%' . $search . '%')
                      ->orWhere('siswa.nis', 'like', '%' . $search . '%');
            });
        }

        // FILTER 2: Kelas
        if ($kelasId = $request->get('kelas')) {
            $siswaQuery->where('k.kelas_id', $kelasId);
        }

        // FILTER 3: Jenis Kelamin
        if ($jenisKelamin = $request->get('jenis_kelamin')) {
            $siswaQuery->where('siswa.jenis_kelamin', $jenisKelamin);
        }

        // 6. Ambil Data Tabel (Paginate)
        $siswaData = $siswaQuery
            ->orderBy('kelas_sekarang', 'asc') 
            ->orderBy('siswa.nama', 'asc')
            ->paginate(15);
            
        // Variabel yang dikirim ke view
        return view('dashboard.admin.manajemen-siswa', compact(
            'siswaData', 
            'tahunAjaranAktif', 
            'totalSiswaAktif', 
            'totalSiswaNonAktif', 
            'allTahunAjaran', // Untuk dropdown tahun ajaran
            'allKelas', // Untuk dropdown kelas
            'request' // untuk menyimpan status filter di view
        ));
    }
}