<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ManajemenSiswaController extends Controller
{
    public function index(Request $request)
    {
        $allTahunAjaran = DB::table('kelas')
            ->select('tahun_ajaran')
            ->distinct()
            ->orderByDesc('tahun_ajaran')
            ->pluck('tahun_ajaran');

        $tahunAjaranAktif = $request->tahun_ajaran ?? $allTahunAjaran->first();

        $allKelas = DB::table('kelas')
            ->where('tahun_ajaran', $tahunAjaranAktif)
            ->select(
                'kelas_id',
                DB::raw("CONCAT(tingkat_kelas,' ',nama_kelas) AS nama_kelas_lengkap")
            )
            ->orderBy('tingkat_kelas')
            ->get();

        $query = DB::table('siswa')
            ->leftJoin('siswa_kelas', function ($join) use ($tahunAjaranAktif) {
                $join->on('siswa.id_siswa', '=', 'siswa_kelas.id_siswa')
                    ->where('siswa_kelas.tahun_ajaran', $tahunAjaranAktif)
                    ->where('siswa_kelas.status', 1);
            })
            ->leftJoin('kelas', 'kelas.kelas_id', '=', 'siswa_kelas.kelas_id')
            ->select(
                'siswa.*',
                DB::raw("CONCAT(kelas.tingkat_kelas,' ',kelas.nama_kelas) AS kelas_sekarang")
            );

        if ($request->search) {
            $query->where('siswa.nama', 'like', "%{$request->search}%")
                  ->orWhere('siswa.nis', 'like', "%{$request->search}%");
        }

        if ($request->kelas) {
            $query->where('kelas.kelas_id', $request->kelas);
        }

        return view('dashboard.admin.manajemen-siswa', [
            'siswaData' => $query->orderBy('siswa.nama')->paginate(10)->withQueryString(),
            'allTahunAjaran' => $allTahunAjaran,
            'tahunAjaranAktif' => $tahunAjaranAktif,
            'allKelas' => $allKelas,
            'totalSiswaAktif' => DB::table('siswa')->where('status_siswa', 'Aktif')->count(),
            'totalSiswaNonAktif' => DB::table('siswa')->where('status_siswa', '!=', 'Aktif')->count(),
            'request' => $request
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nis' => 'required|unique:siswa,nis',
            'nama' => 'required',
            'jenis_kelamin' => 'required',
            'tgl_lahir' => 'required',
            'agama' => 'required',
        ]);

        DB::table('siswa')->insert([
            'nis' => $request->nis,
            'nama' => $request->nama,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tgl_lahir' => $request->tgl_lahir,
            'agama' => $request->agama,
            'alamat_rmh' => $request->alamat_rmh,
            'kota_rmh' => $request->kota_rmh,
            'status_siswa' => 'Aktif',
            'user_entry' => 1,
            'tgl_entry' => Carbon::now(),
        ]);

        return back()->with('success', 'Siswa berhasil ditambahkan');
    }

    public function update(Request $request)
    {
        DB::table('siswa')
            ->where('id_siswa', $request->id_siswa)
            ->update([
                'nis' => $request->nis,
                'nama' => $request->nama,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tgl_lahir' => $request->tgl_lahir,
                'agama' => $request->agama,
                'alamat_rmh' => $request->alamat_rmh,
                'kota_rmh' => $request->kota_rmh,
                'status_siswa' => $request->status_siswa,
                'user_update' => 1,
                'tgl_update' => Carbon::now(),
            ]);

        return back()->with('success', 'Data siswa berhasil diperbarui');
    }
}
