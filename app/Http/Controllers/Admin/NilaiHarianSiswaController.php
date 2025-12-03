<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SIAKAD\SCHOOL\NilaiTambahan;
use App\Models\SIAKAD\SCHOOL\NilaiTambahanSiswa;
use App\Models\SIAKAD\SCHOOL\SiswaKelas;
use App\Models\SIAKAD\SCHOOL\GuruMapel;
use App\Models\SIAKAD\SCHOOL\Kelas;
use App\Models\SIAKAD\SCHOOL\Mapel;
use App\Models\SIAKAD\SCHOOL\JadwalMapel;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class NilaiHarianSiswaController extends Controller
{
    /** Menampilkan halaman daftar / rekap nilai harian */
    public function index(Request $request)
    {
        $tahunAjaran = $request->input('tahun_ajaran', '2024/2025');
        $semester     = $request->input('semester', 'Ganjil');
        $kelasId      = $request->input('kelas_id');
        $mapelId      = $request->input('mapel_id');

        if ($request->ajax() && $kelasId) {
            $mapel = JadwalMapel::where('kelas_id', $kelasId)
                ->whereHas('penugasan', function ($q) use ($tahunAjaran, $semester) {
                    $q->where('tahun_ajaran', $tahunAjaran)
                        ->where('semester', $semester);
                })
                ->with('penugasan.mapel')
                ->get()
                ->pluck('penugasan.mapel')
                ->filter()
                ->unique('mapel_id')
                ->values()
                ->map(fn($m) => [
                    'mapel_id' => $m->mapel_id,
                    'nama_mapel' => $m->nama_mapel
                ]);

            return response()->json(['success' => true, 'mapel' => $mapel]);
        }

        // semua kelas untuk dropdown
        $kelas = Kelas::all()->map(function ($k) {
            $k->nama_kelas_lengkap = trim(($k->tingkat_kelas ?? '') . ' ' . ($k->nama_kelas ?? ''));
            return $k;
        });

        // jika kelas dipilih, ambil mapel yang ada di jadwal_mapel untuk kelas tsb pada TA+semester
        $mapel = Mapel::orderBy('nama_mapel')->get();
        if ($kelasId) {
            $mapelFromJadwal = JadwalMapel::where('kelas_id', $kelasId)
                ->whereHas('penugasan', function ($q) use ($tahunAjaran, $semester) {
                    $q->where('tahun_ajaran', $tahunAjaran)
                        ->where('semester', $semester);
                })
                ->with('penugasan.mapel')
                ->get()
                ->pluck('penugasan.mapel')
                ->filter()
                ->unique('mapel_id')
                ->values();

            if ($mapelFromJadwal->isNotEmpty()) {
                $mapel = $mapelFromJadwal;
            }
        }

        // LIST TUGAS (dipakai untuk monitoring ringkas per baris)
        $listTugas = collect();
        if ($kelasId && $mapelId) {
            $listTugas = NilaiTambahan::with(['kelas', 'guruMapel.guru', 'mapel'])
                ->where('kelas_id', $kelasId)
                ->where('mapel_id', $mapelId)
                ->where('tahun_ajaran', $tahunAjaran)
                ->where('semester', $semester)
                ->orderBy('tgl_entry', 'asc')
                ->get();

            // tentukan status pengisian tiap tugas (Selesai/Sebagian/Belum Diisi)
            $listTugas->transform(function ($t) use ($kelasId, $tahunAjaran) {
                $totalSiswa = SiswaKelas::where('kelas_id', $kelasId)
                    ->where('tahun_ajaran', $t->tahun_ajaran)
                    ->count();

                $nilaiMasuk = NilaiTambahanSiswa::where('nilai_tambahan_id', $t->id)
                    ->whereNotNull('nilai')
                    ->count();

                if ($totalSiswa == 0 || $nilaiMasuk == 0) $t->statusPengisian = 'Belum Diisi';
                elseif ($nilaiMasuk < $totalSiswa) $t->statusPengisian = 'Sebagian';
                else $t->statusPengisian = 'Selesai';

                return $t;
            });
        }

        // PREPARE REKAP TABLE (single table below header)
        $rekap = [];
        $tasks = collect();
        $noTasksForFilter = false;

        if ($kelasId && $mapelId && $tahunAjaran) {
            // Ambil tasks sesuai filter
            $tasks = NilaiTambahan::where('kelas_id', $kelasId)
                ->where('mapel_id', $mapelId)
                ->where('tahun_ajaran', $tahunAjaran)
                ->where('semester', $semester)
                ->orderBy('tgl_entry', 'asc')
                ->get();

            if ($tasks->isEmpty()) {
                // Tidak ada tugas sama sekali untuk filter ini
                $noTasksForFilter = true;
            } else {
                // buat array task ids
                $taskIds = $tasks->pluck('id')->toArray();

                // ambil semua nilai untuk taskIds -> groupBy id_siswa
                $nilaiAll = NilaiTambahanSiswa::whereIn('nilai_tambahan_id', $taskIds)
                    ->get()
                    ->groupBy('id_siswa');

                // ambil siswa list (tetapi hanya render rekap ketika ada tasks;
                // bila tasks kosong, kita tidak mau menampilkan daftar siswa kosong)
                $listSiswa = SiswaKelas::where('kelas_id', $kelasId)
                    ->where('tahun_ajaran', $tahunAjaran)
                    ->with('siswa:id_siswa,nis,nama')
                    ->get()
                    ->pluck('siswa')
                    ->filter()
                    ->values();

                // bangun rekap per siswa
                foreach ($listSiswa as $s) {
                    $nilaiPerTugas = [];
                    $total = 0;
                    $count = 0;

                    foreach ($tasks as $t) {
                        $nilaiObj = collect($nilaiAll->get($s->id_siswa, collect()))
                            ->firstWhere('nilai_tambahan_id', $t->id);

                        $nilai = $nilaiObj->nilai ?? null;
                        $nilaiPerTugas[$t->id] = $nilai;

                        if (!is_null($nilai)) {
                            $total += (float)$nilai;
                            $count++;
                        }
                    }

                    $rekap[] = [
                        'id_siswa' => $s->id_siswa,
                        'nis' => $s->nis,
                        'nama' => $s->nama,
                        'nilai_per_tugas' => $nilaiPerTugas,
                        'rata_rata' => $count ? round($total / $count, 2) : 0,
                    ];
                }
            }
        }

        // --- HITUNG STATUS KESELURUHAN TUGAS (header) ---
        $overallStatus = null; // Draft / Submitted / Mixed / No Tasks
        if ($tasks->isEmpty()) {
            $overallStatus = 'No Tasks';
        } else {
            $statuses = $tasks->pluck('status')->unique()->values()->all();
            if (count($statuses) === 1) {
                $overallStatus = $statuses[0];
            } else {
                $overallStatus = 'Mixed';
            }
        }

        // Beri nomor urut untuk PH (urutan tugas sesuai tgl)
        $i = 1;
        foreach ($tasks as $t) {
            $t->kode_ringkas = 'PH-' . $i++;
        }

        // kirim ke view
        return view('dashboard.admin.nilai-siswa.harian', compact(
            'tahunAjaran',
            'semester',
            'kelas',
            'mapel',
            'kelasId',
            'mapelId',
            'listTugas',
            'rekap',
            'tasks',
            'overallStatus',
            'noTasksForFilter'
        ));
    }
}
