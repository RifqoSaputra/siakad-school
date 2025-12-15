<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SIAKAD\SCHOOL\NilaiUjian; // Ganti NilaiTambahan (PH) ke NilaiUjian (PTS/PAS)
use App\Models\SIAKAD\SCHOOL\SiswaKelas;
use App\Models\SIAKAD\SCHOOL\Kelas;
use App\Models\SIAKAD\SCHOOL\Mapel;
use App\Models\SIAKAD\SCHOOL\JadwalMapel;
use App\Models\SIAKAD\SCHOOL\NilaiUjianSiswa; // NilaiUjianSiswa
use Illuminate\Support\Collection;

class NilaiUjianSiswaController extends Controller
{
    /** Menampilkan halaman daftar / rekap nilai Ujian (PTS/PAS) */
    public function index(Request $request)
    {
        $tahunAjaran = $request->input('tahun_ajaran', '2024/2025');
        $semester    = $request->input('semester', 'Ganjil');
        $kelasId     = $request->input('kelas_id');
        $mapelId     = $request->input('mapel_id');
        $tipeUjian   = $request->input('tipe_ujian'); // Filter baru: PTS / PAS

        // --- Handle AJAX Request untuk dropdown Mapel ---
        if ($request->ajax() && $kelasId) {
            // Logika untuk mengambil mapel yang diajarkan di kelas tsb pada TA+Semester
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

        // --- Load Data Filter Normal ---

        // Semua kelas untuk dropdown
        $kelas = Kelas::all()->map(function ($k) {
            $k->nama_kelas_lengkap = trim(($k->tingkat_kelas ?? '') . ' ' . ($k->nama_kelas ?? ''));
            return $k;
        });

        // Semua mapel (atau filter berdasarkan kelas jika dipilih)
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

        // --- PREPARE REKAP DATA UJIAN ---
        $rekap = [];
        $ujian = null;
        $noUjianForFilter = false;

        if ($kelasId && $mapelId && $tipeUjian) {
            // Ambil data ujian PTS/PAS yang sesuai filter (Diasumsikan hanya ada SATU PTS dan SATU PAS per Kelas/Mapel/Semester)
            $ujian = NilaiUjian::with(['kelas', 'guruMapel.guru', 'mapel'])
                ->where('kelas_id', $kelasId)
                ->where('mapel_id', $mapelId)
                ->where('tahun_ajaran', $tahunAjaran)
                ->where('semester', $semester)
                ->where('tipe_ujian', $tipeUjian) // Filter Tipe Ujian (PTS/PAS)
                ->first();

            if (!$ujian) {
                $noUjianForFilter = true;
            } else {
                // Ambil daftar siswa di kelas tersebut
                $listSiswa = SiswaKelas::where('kelas_id', $kelasId)
                    ->where('tahun_ajaran', $tahunAjaran)
                    ->with('siswa:id_siswa,nis,nama')
                    ->get()
                    ->pluck('siswa')
                    ->filter()
                    ->values();

                // Ambil semua nilai ujian untuk ujian ini (PTS/PAS)
                $nilaiUjianAll = NilaiUjianSiswa::where('nilai_ujian_id', $ujian->id)
                    ->pluck('nilai', 'id_siswa'); // pluck('nilai', 'id_siswa') -> [id_siswa => nilai]

                // Bangun rekap per siswa
                foreach ($listSiswa as $s) {
                    $nilai = $nilaiUjianAll->get($s->id_siswa, null); // Ambil nilai atau null jika belum diinput

                    $rekap[] = [
                        'id_siswa' => $s->id_siswa,
                        'nis' => $s->nis,
                        'nama' => $s->nama,
                        'nilai' => $nilai, // Hanya satu kolom nilai
                    ];
                }
            }
        }

        // --- Status Ujian (Header) ---
        $ujianStatus = $ujian ? $ujian->status : null; // Misal: Draft / Selesai

        // kirim ke view
        return view('dashboard.admin.nilai-siswa.ujian', compact(
            'tahunAjaran',
            'semester',
            'kelas',
            'mapel',
            'kelasId',
            'mapelId',
            'tipeUjian',
            'ujian', // Data Ujian (untuk header ringkas)
            'rekap',
            'ujianStatus',
            'noUjianForFilter'
        ));
    }
}
