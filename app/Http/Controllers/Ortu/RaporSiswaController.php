<?php

namespace App\Http\Controllers\Ortu;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\SIAKAD\SCHOOL\SiswaKelas;
use App\Models\SIAKAD\SCHOOL\NilaiAkhirSemester;
use App\Models\SIAKAD\SCHOOL\CatatanRaporSemester;
use App\Models\SIAKAD\SCHOOL\JadwalMapel;
use App\Models\SIAKAD\SCHOOL\Absensi;

class RaporSiswaController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $idSiswa = session('selected_siswa_id');

        if (!$idSiswa) {
            return back()->with('error', 'Silakan pilih siswa terlebih dahulu.');
        }

        $kelasId = $request->input('kelas_id');
        $semester = $request->input('semester', 'Ganjil');

        // Ambil daftar kelas yang pernah ditempati siswa
        $kelasList = SiswaKelas::where('id_siswa', $idSiswa)
            ->where('status', 1)
            ->orderByDesc('tahun_ajaran')
            ->with('kelas')
            ->get();

        if ($kelasList->isEmpty()) {
            return view('dashboard.ortu.rapor-siswa', [
                'kelasListDummy' => [],
                'dataRapor' => [],
                'catatanOrtu' => [],
                'kelasFull' => '-',
                'semester' => $semester,
                'tahunAjaran' => '-',
                'kelasId' => null,
            ])->with('error', 'Siswa belum memiliki data kelas.');
        }

        // Default ke kelas pertama jika belum memilih kelas
        if (!$kelasId) {
            $kelasId = $kelasList->first()->kelas_id;
        }

        // Ambil data kelas yang dipilih
        $kelasActive = $kelasList->where('kelas_id', $kelasId)->first();

        // Tahun ajaran HARUS ikut kelas yg dipilih
        $tahunAjaran = $kelasActive->tahun_ajaran;

        // Nama kelas
        $kelasFull = ($kelasActive->kelas->tingkat_kelas ?? '') . ' ' . ($kelasActive->kelas->nama_kelas ?? '');
        
        // =============== Ambil CATATAN RAPOR =============== //
        $catatan = CatatanRaporSemester::where('id_siswa', $idSiswa)
            ->where('kelas_id', $kelasId)
            ->where('semester', $semester)
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('status_publikasi', 'Diterbitkan')
            ->first();

        if (!$catatan) {
            return view('dashboard.ortu.rapor-siswa', [
                'kelasListDummy' => $kelasList,
                'dataRapor' => [],
                'catatanOrtu' => [
                    'Sikap' => '-',
                    'Kepribadian' => '-',
                    'Kehadiran' => [
                        'Sakit' => 0,
                        'Izin' => 0,
                        'Alpa' => 0,
                    ]
                ],
                'kelasFull' => $kelasFull,
                'semester' => $semester,
                'tahunAjaran' => $tahunAjaran,
                'kelasId' => $kelasId,
            ])->with('error', 'Rapor belum diterbitkan oleh sekolah.');
        }

        // =============== Ambil NILAI AKHIR RAPOR =============== //
        $nilaiAkhir = NilaiAkhirSemester::where('id_siswa', $idSiswa)
            ->where('kelas_id', $kelasId)
            ->where('semester', $semester)
            ->where('tahun_ajaran', $tahunAjaran)
            ->with('mapel')
            ->get();

        if ($nilaiAkhir->isEmpty()) {
            return view('dashboard.ortu.rapor-siswa', [
                'kelasListDummy' => $kelasList,
                'dataRapor' => [],
                'catatanOrtu' => [],
                'kelasFull' => $kelasFull,
                'semester' => $semester,
                'tahunAjaran' => $tahunAjaran,
                'kelasId' => $kelasId,
            ])->with('error', 'Data nilai rapor belum tersedia.');
        }

        // Format nilai
        $dataRapor = $nilaiAkhir->map(function ($n) {
            return [
                'mapel' => $n->mapel->nama_mapel ?? '-',
                'kkm' => $n->kkm,
                'nilai_rapor' => (float)$n->nilai_rapor,
                'deskripsi' => $n->deskripsi,
            ];
        })->values()->toArray();

        // =============== Rekap ABSENSI =============== //
        $jadwalIds = JadwalMapel::where('kelas_id', $kelasId)
            ->whereHas('penugasan', function ($q) use ($semester, $tahunAjaran) {
                $q->where('semester', $semester)
                    ->where('tahun_ajaran', $tahunAjaran);
            })
            ->pluck('jadwal_mapel_id');

        $absensi = [
            'Sakit' => Absensi::where('id_siswa', $idSiswa)->whereIn('jadwal_mapel_id', $jadwalIds)->where('status', 'Sakit')->count(),
            'Izin' => Absensi::where('id_siswa', $idSiswa)->whereIn('jadwal_mapel_id', $jadwalIds)->where('status', 'Izin')->count(),
            'Alpa' => Absensi::where('id_siswa', $idSiswa)->whereIn('jadwal_mapel_id', $jadwalIds)->whereIn('status', ['Alpha', 'Alfa', 'TIDAK HADIR'])->count(),
        ];

        // =============== Prepare Catatan =============== //
        $catatanOrtu = [
            'Sikap' => $catatan->predikat_sikap,
            'Kepribadian' => $catatan->catatan_walikelas,
            'Kehadiran' => $absensi,
        ];

        return view('dashboard.ortu.rapor-siswa', [
            'kelasListDummy' => $kelasList,
            'dataRapor' => $dataRapor,
            'catatanOrtu' => $catatanOrtu,
            'kelasFull' => $kelasFull,
            'semester' => $semester,
            'tahunAjaran' => $tahunAjaran,
            'kelasId' => $kelasId,
        ]);
    }


    public function cetak(Request $request)
    {
        return "Fitur PDF Cetak Rapor (Coming Soon)";
    }
}
