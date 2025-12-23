<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\SIAKAD\SCHOOL\JadwalMapel;
use App\Models\SIAKAD\SCHOOL\GuruMapel;
use App\Models\SIAKAD\SCHOOL\Mapel;
use App\Models\SIAKAD\SCHOOL\Kelas;
use App\Models\SIAKAD\SCHOOL\Ruangan;

class ManajemenJadwalController extends Controller
{
    public function index(Request $request)
    {
        $query = JadwalMapel::with(['kelas', 'penugasan.guru', 'penugasan.mapel', 'ruangan']);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('kelas', function ($q2) use ($request) {
                    $q2->where('nama_kelas', 'like', "%{$request->search}%")
                        ->orWhere('tingkat_kelas', 'like', "%{$request->search}%");
                })
                    ->orWhereHas('penugasan.mapel', function ($q2) use ($request) {
                        $q2->where('nama_mapel', 'like', "%{$request->search}%");
                    })
                    ->orWhereHas('penugasan.guru', function ($q2) use ($request) {
                        $q2->where('nama_guru', 'like', "%{$request->search}%");
                    });
            });
        }

        if ($request->kelas_id) {
            $query->where('kelas_id', $request->kelas_id);
        }

        if ($request->mapel_id) {
            $query->whereHas('penugasan', fn($q) =>
            $q->where('mapel_id', $request->mapel_id));
        }

        if ($request->status !== null && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $jadwal = $query
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.admin.manajemen-jadwal', [
            'jadwal' => $jadwal,
            'guruMapel' => GuruMapel::with(['guru', 'mapel'])->where('status', 1)->get(),
            'mapel' => Mapel::where('status', 1)->get(),
            'kelas' => Kelas::where('status', 1)->get(),
            'ruangan' => Ruangan::where('status', 1)->get(),
            'totalJadwal' => JadwalMapel::count(),
            'jadwalAktif' => JadwalMapel::where('status', 1)->count(),
            'jadwalNonaktif' => JadwalMapel::where('status', 0)->count(),
            'request' => $request
        ]);
    }

    public function store(Request $r)
    {
        JadwalMapel::create([
            'guru_mapel_id' => $r->guru_mapel_id,
            'kelas_id' => $r->kelas_id,
            'ruangan_id' => $r->ruangan_id,
            'tahun_ajaran' => date('Y') . '/' . (date('Y') + 1),
            'hari' => $r->hari,
            'jam_mulai' => $r->jam_mulai,
            'jam_selesai' => $r->jam_selesai,
            'status' => $r->status,
            'user_entry' => Auth::user()->users_id,
            'tgl_entry' => now()
        ]);

        return back()->with('success', 'Jadwal berhasil ditambahkan');
    }

    public function update(Request $r)
    {
        $jadwal = JadwalMapel::findOrFail($r->jadwal_mapel_id);

        $jadwal->update([
            'guru_mapel_id' => $r->guru_mapel_id,
            'kelas_id' => $r->kelas_id,
            'ruangan_id' => $r->ruangan_id,
            'hari' => $r->hari,
            'jam_mulai' => $r->jam_mulai,
            'jam_selesai' => $r->jam_selesai,
            'status' => $r->status,
            'user_update' => Auth::user()->users_id,
            'tgl_update' => now()
        ]);

        return back()->with('success', 'Jadwal berhasil diperbarui');
    }
}
