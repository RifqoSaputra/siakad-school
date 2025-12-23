<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\SIAKAD\SCHOOL\Kelas;
use App\Models\SIAKAD\SCHOOL\Guru;
use App\Models\SIAKAD\SCHOOL\Siswa;
use App\Models\SIAKAD\SCHOOL\SiswaKelas;

class KelasController extends Controller
{
    public function index(Request $request)
    {
        $query = Kelas::with(['waliKelas', 'siswaTerdaftar.siswa']);

        // FILTER SEARCH (KELAS / WALIKELAS)
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_kelas', 'like', "%{$request->search}%")
                    ->orWhere('tingkat_kelas', 'like', "%{$request->search}%")
                    ->orWhereHas('waliKelas', function ($g) use ($request) {
                        $g->where('nama_guru', 'like', "%{$request->search}%");
                    });
            });
        }

        // FILTER STATUS
        if ($request->status !== null && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $kelas = $query
            ->orderBy('tingkat_kelas')
            ->orderBy('nama_kelas')
            ->get();

        // SUMMARY
        $totalKelas = Kelas::count();
        $kelasAktif = Kelas::where('status', 1)->count();
        $kelasNonaktif = Kelas::where('status', 0)->count();

        // DATA MASTER
        $guru = Guru::where('status_guru', '1')->orderBy('nama_guru')->get();

        // SISWA YANG BELUM MASUK KELAS
        $siswaBelumKelas = Siswa::whereNotIn('id_siswa', function ($q) {
            $q->select('id_siswa')->from('siswa_kelas')->where('status', 1);
        })->orderBy('nama')->get();

        return view('dashboard.admin.manajemen-kelas', [
            'kelas' => $kelas,
            'guru' => $guru,
            'siswaBelumKelas' => $siswaBelumKelas,
            'totalKelas' => $totalKelas,
            'kelasAktif' => $kelasAktif,
            'kelasNonaktif' => $kelasNonaktif,
            'request' => $request
        ]);
    }

    public function store(Request $r)
    {
        DB::transaction(function () use ($r) {

            $kelas = Kelas::create([
                'tingkat_kelas' => $r->tingkat_kelas,
                'nama_kelas' => $r->nama_kelas,
                'tahun_ajaran' => $r->tahun_ajaran,
                'semester' => $r->semester,
                'walikelas' => $r->walikelas,
                'status' => $r->status,
                'user_entry' => Auth::user()->users_id,
                'tgl_entry' => now()
            ]);

            if ($r->siswa_ids) {
                foreach ($r->siswa_ids as $idSiswa) {
                    SiswaKelas::create([
                        'kelas_id' => $kelas->kelas_id,
                        'id_siswa' => $idSiswa,
                        'tahun_ajaran' => $r->tahun_ajaran,
                        'status' => '1',
                        'user_entry' => Auth::user()->users_id,
                        'tgl_entry' => now()
                    ]);
                }
            }
        });

        return back()->with('success', 'Kelas berhasil ditambahkan');
    }

    public function update(Request $r)
    {
        DB::transaction(function () use ($r) {

            $kelas = Kelas::findOrFail($r->kelas_id);

            $kelas->update([
                'tingkat_kelas' => $r->tingkat_kelas,
                'nama_kelas' => $r->nama_kelas,
                'tahun_ajaran' => $r->tahun_ajaran,
                'semester' => $r->semester,
                'walikelas' => $r->walikelas,
                'status' => $r->status,
                'user_update' => Auth::user()->users_id,
                'tgl_update' => now()
            ]);

            // RESET SISWA
            SiswaKelas::where('kelas_id', $kelas->kelas_id)->delete();

            if ($r->siswa_ids) {
                foreach ($r->siswa_ids as $idSiswa) {
                    SiswaKelas::create([
                        'kelas_id' => $kelas->kelas_id,
                        'id_siswa' => $idSiswa,
                        'tahun_ajaran' => $r->tahun_ajaran,
                        'status' => '1',
                        'user_entry' => Auth::user()->users_id,
                        'tgl_entry' => now()
                    ]);
                }
            }
        });

        return back()->with('success', 'Kelas berhasil diperbarui');
    }

    public function siswaKelas($kelasId)
    {
        $data = Siswa::whereIn('id_siswa', function ($q) use ($kelasId) {
            $q->select('id_siswa')
                ->from('siswa_kelas')
                ->where('kelas_id', $kelasId)
                ->where('status', 1);
        })
            ->orderBy('nama')
            ->get(['id_siswa', 'nama']);

        return response()->json($data);
    }

    public function searchSiswa(Request $r)
    {
        $q = $r->q;
        $kelasId = $r->kelas_id;

        $query = Siswa::query();

        $query->whereNotIn('id_siswa', function ($sub) use ($kelasId) {
            $sub->select('id_siswa')
                ->from('siswa_kelas')
                ->where('status', 1);

            if ($kelasId) {
                $sub->where('kelas_id', '!=', $kelasId);
            }
        });

        if ($q) {
            $query->where('nama', 'like', "%{$q}%");
        }

        $data = $query
            ->orderBy('nama')
            ->limit(5)
            ->get(['id_siswa', 'nama']);

        if ($data->isEmpty()) {
            return response()->json([
                'empty' => true,
                'message' => 'Tidak ada siswa yang bisa ditambahkan'
            ]);
        }

        return response()->json($data);
    }
}
