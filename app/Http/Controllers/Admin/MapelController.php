<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\SIAKAD\SCHOOL\Mapel;
use App\Models\SIAKAD\SCHOOL\Guru;
use App\Models\SIAKAD\SCHOOL\GuruMapel;

class MapelController extends Controller
{
    public function index(Request $request)
    {
        $query = Mapel::with('penugasanGuru.guru');

        if ($request->search) {
            $query->where('nama_mapel', 'like', "%{$request->search}%")
                ->orWhere('kode_mapel', 'like', "%{$request->search}%");
        }

        if ($request->status !== null && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $mapel = $query->orderBy('nama_mapel')->get();

        // ===== SUMMARY CARD (PERSIS KAYA GURU) =====
        $totalMapel       = Mapel::count();
        $totalMapelAktif  = Mapel::where('status', 1)->count();
        $totalMapelNonAktif = Mapel::where('status', 0)->count();

        $guru = Guru::where('status_guru', 'Aktif')
            ->orderBy('nama_guru')
            ->get();

        return view('dashboard.admin.manajemen-mapel', [
            'mapel' => $mapel,
            'guru'  => $guru,
            'totalMapel' => $totalMapel,
            'totalMapelAktif' => $totalMapelAktif,
            'totalMapelNonAktif' => $totalMapelNonAktif,
            'request' => $request
        ]);
    }

    public function store(Request $r)
    {
        DB::transaction(function () use ($r) {

            $mapel = Mapel::create([
                'kode_mapel' => $r->kode_mapel,
                'nama_mapel' => $r->nama_mapel,
                'kategori_mapel' => $r->kategori_mapel,
                'status' => $r->status,
                'user_entry' => Auth::user()->users_id,
                'tgl_entry' => now()
            ]);

            if ($r->guru_ids) {
                foreach ($r->guru_ids as $idGuru) {
                    GuruMapel::create([
                        'mapel_id' => $mapel->mapel_id,
                        'id_guru' => $idGuru,
                        'status' => 'Aktif',
                        'tgl_entry' => now()
                    ]);
                }
            }
        });

        return back()->with('success', 'Mapel berhasil ditambahkan');
    }

    public function update(Request $r)
    {
        DB::transaction(function () use ($r) {

            $mapel = Mapel::findOrFail($r->mapel_id);

            $mapel->update([
                'kode_mapel' => $r->kode_mapel,
                'nama_mapel' => $r->nama_mapel,
                'kategori_mapel' => $r->kategori_mapel,
                'status' => $r->status,
                'user_update' => Auth::user()->users_id,
                'tgl_update' => now()
            ]);

            GuruMapel::where('mapel_id', $mapel->mapel_id)->delete();

            if ($r->guru_ids) {
                foreach ($r->guru_ids as $idGuru) {
                    GuruMapel::create([
                        'mapel_id' => $mapel->mapel_id,
                        'id_guru' => $idGuru,
                        'status' => 'Aktif',
                        'tgl_entry' => now()
                    ]);
                }
            }
        });

        return back()->with('success', 'Mapel berhasil diperbarui');
    }
}
