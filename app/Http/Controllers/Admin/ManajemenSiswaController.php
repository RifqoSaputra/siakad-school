<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SIAKAD\SCHOOL\Siswa;
use Illuminate\Support\Facades\DB;

class ManajemenSiswaController extends Controller
{
    /**
     * Menampilkan daftar siswa dengan filtering dan ringkasan sederhana.
     */
    public function index(Request $request)
    {
        // 1. Ambil semua tahun ajaran unik dari tabel "kelas" untuk dropdown
        $allTahunAjaran = DB::table('kelas')
            ->select('tahun_ajaran')
            ->distinct()
            ->orderBy('tahun_ajaran', 'desc')
            ->pluck('tahun_ajaran')
            ->toArray();

        // Tentukan tahun ajaran aktif/terpilih (default ke yang terbaru jika ada)
        $defaultTahunAjaran = !empty($allTahunAjaran) ? $allTahunAjaran[0] : null;
        $tahunAjaranAktif = $request->get('tahun_ajaran', $defaultTahunAjaran);

        // 2. Data dropdown kelas (tanpa join ke siswa)
        //    Nama kelas: "tingkat_kelas nama_kelas"
        $allKelas = DB::table('kelas')
            ->when($tahunAjaranAktif, function ($q) use ($tahunAjaranAktif) {
                $q->where('tahun_ajaran', $tahunAjaranAktif);
            })
            ->select('kelas_id', DB::raw("CONCAT(tingkat_kelas, ' ', nama_kelas) AS nama_kelas_lengkap"))
            ->orderBy('tingkat_kelas', 'asc')
            ->orderBy('nama_kelas', 'asc')
            ->get();

        // 3. Query dasar siswa (tanpa join dulu, kita simplekan)
        $siswaQuery = Siswa::query();

        // FILTER 1: Search (NIS atau Nama)
        if ($search = $request->get('search')) {
            $siswaQuery->where(function ($query) use ($search) {
                $query->where('nama', 'like', '%' . $search . '%')
                    ->orWhere('nis', 'like', '%' . $search . '%');
            });
        }

        // FILTER 2: Jenis Kelamin (jika kolomnya ada)
        if ($jenisKelamin = $request->get('jenis_kelamin')) {
            $siswaQuery->where('jenis_kelamin', $jenisKelamin);
        }

        // NOTE:
        // Untuk saat ini kita BELUM menghubungkan siswa dengan kelas_aktif/siswa_kelas,
        // supaya tidak error karena struktur tabel belum pasti.
        // Nanti kalau kamu mau, kita bisa tambah join ke siswa_kelas + kelas.

        // 4. Hitung ringkasan sederhana
        $totalSiswaAktif    = $siswaQuery->count(); // anggap semua data = aktif
        $totalSiswaNonAktif = 0;                    // belum didefinisikan kolom status

        // 5. Ambil data siswa untuk tabel (paginate)
        $siswaData = $siswaQuery
            ->orderBy('nama', 'asc')
            ->paginate(15);

        // Kirim ke view
        return view('dashboard.admin.manajemen-siswa', compact(
            'siswaData',
            'tahunAjaranAktif',
            'totalSiswaAktif',
            'totalSiswaNonAktif',
            'allTahunAjaran',   // dropdown tahun ajaran
            'allKelas',         // dropdown kelas (walaupun belum dipakai di query)
            'request'           // supaya filter tetap keisi di form
        ));
    }
}
