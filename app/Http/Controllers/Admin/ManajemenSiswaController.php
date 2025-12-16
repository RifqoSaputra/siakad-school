<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManajemenSiswaController extends Controller
{
    public function index(Request $request)
    {
        /* =====================================================
         * 1. TAHUN AJARAN
         * ===================================================== */
        $allTahunAjaran = DB::table('kelas')
            ->select('tahun_ajaran')
            ->distinct()
            ->orderBy('tahun_ajaran', 'desc')
            ->pluck('tahun_ajaran')
            ->toArray();

        $tahunAjaranAktif = $request->get(
            'tahun_ajaran',
            $allTahunAjaran[0] ?? null
        );

        /* =====================================================
         * 2. DROPDOWN KELAS
         * ===================================================== */
        $allKelas = DB::table('kelas')
            ->where('tahun_ajaran', $tahunAjaranAktif)
            ->select(
                'kelas_id',
                DB::raw("CONCAT(tingkat_kelas, ' ', nama_kelas) AS nama_kelas_lengkap")
            )
            ->orderBy('tingkat_kelas')
            ->orderBy('nama_kelas')
            ->get();

        /* =====================================================
         * 3. QUERY UTAMA SISWA
         * ===================================================== */
        $query = DB::table('siswa')
            ->leftJoin('siswa_kelas', function ($join) use ($tahunAjaranAktif) {
                $join->on('siswa.id_siswa', '=', 'siswa_kelas.id_siswa')
                    ->where('siswa_kelas.tahun_ajaran', $tahunAjaranAktif)
                    ->where('siswa_kelas.status', 1);
            })
            ->leftJoin('kelas', 'kelas.kelas_id', '=', 'siswa_kelas.kelas_id')
            ->select(
                'siswa.id_siswa',
                'siswa.nis',
                'siswa.nama',
                'siswa.tgl_lahir',
                DB::raw("CONCAT(kelas.tingkat_kelas, ' ', kelas.nama_kelas) AS kelas_sekarang"),
                DB::raw("IF(siswa_kelas.status = 1, 'Aktif', 'Non Aktif') AS status_akademik")
            );

        /* =====================================================
         * 4. FILTER
         * ===================================================== */
        // Search
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('siswa.nama', 'like', "%{$search}%")
                    ->orWhere('siswa.nis', 'like', "%{$search}%");
            });
        }

        // Filter Kelas
        if ($kelasId = $request->get('kelas')) {
            $query->where('kelas.kelas_id', $kelasId);
        }

        /* =====================================================
         * 5. RINGKASAN
         * ===================================================== */
        $totalSiswaAktif = DB::table('siswa_kelas')
            ->where('tahun_ajaran', $tahunAjaranAktif)
            ->where('status', 1)
            ->count();

        $totalSiswaNonAktif = DB::table('siswa')
            ->whereNotIn('id_siswa', function ($q) use ($tahunAjaranAktif) {
                $q->select('id_siswa')
                    ->from('siswa_kelas')
                    ->where('tahun_ajaran', $tahunAjaranAktif)
                    ->where('status', 1);
            })
            ->count();

        /* =====================================================
         * 6. PAGINATION
         * ===================================================== */
        $siswaData = $query
            ->orderBy('siswa.nama')
            ->paginate(15)
            ->withQueryString();

        return view('dashboard.admin.manajemen-siswa', compact(
            'siswaData',
            'tahunAjaranAktif',
            'allTahunAjaran',
            'allKelas',
            'totalSiswaAktif',
            'totalSiswaNonAktif',
            'request'
        ));
    }
}
