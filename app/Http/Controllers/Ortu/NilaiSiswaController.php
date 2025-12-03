<?php

namespace App\Http\Controllers\Ortu;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SIAKAD\SCHOOL\SiswaKelas;
use App\Models\SIAKAD\SCHOOL\JadwalMapel;
use App\Models\SIAKAD\SCHOOL\NilaiTambahan;
use App\Models\SIAKAD\SCHOOL\NilaiTambahanSiswa;
use App\Models\SIAKAD\SCHOOL\NilaiUjian; // Model Master Nilai Ujian
use App\Models\SIAKAD\SCHOOL\NilaiUjianSiswa;
use Carbon\Carbon;

class NilaiSiswaController extends Controller
{
    protected const TAHUN_AJARAN = '2024/2025';

    public function nilaiHarian(Request $request)
    {
        $semester = $request->input('semester', 'Ganjil');
        $idSiswa  = $request->session()->get('selected_siswa_id');

        if (!$idSiswa) {
            return back()->with('error', 'Siswa belum dipilih.');
        }

        $kelasList = SiswaKelas::with('kelas')
            ->where('id_siswa', $idSiswa)
            ->where('tahun_ajaran', self::TAHUN_AJARAN)
            ->get();

        if ($kelasList->isEmpty()) {
            return back()->with('error', 'Siswa tidak terdaftar di kelas manapun.');
        }

        $kelasIdDefault = $kelasList->first()->kelas_id;
        $kelasId        = $request->input('kelas_id', $kelasIdDefault);
        $kelasNow       = $kelasList->firstWhere('kelas_id', $kelasId);

        if (!$kelasNow) {
            return back()->with('error', 'Kelas tidak valid.');
        }

        $kelasFull = ($kelasNow->kelas->tingkat_kelas ?? '') . ' ' . ($kelasNow->kelas->nama_kelas ?? '');

        // Ambil Jadwal Mapel
        $jadwalMapel = JadwalMapel::with(['penugasan.mapel', 'penugasan.guru'])
            ->where('kelas_id', $kelasId)
            ->get()
            ->unique('penugasan.mapel_id');

        $rekap = [];
        $mapelIds = $jadwalMapel->pluck('penugasan.mapel_id')->filter()->unique();

        if ($mapelIds->isNotEmpty()) {
            $tasks = NilaiTambahan::where('kelas_id', $kelasId)
                ->whereIn('mapel_id', $mapelIds)
                ->where('tahun_ajaran', self::TAHUN_AJARAN)
                ->where('semester', $semester)
                ->with(['nilaiSiswa' => fn($q) => $q->where('id_siswa', $idSiswa)])
                ->get()
                ->groupBy('mapel_id');

            foreach ($jadwalMapel as $jadwal) {
                $penugasan = $jadwal->penugasan;
                $mapelId   = $penugasan->mapel_id;

                $total = 0;
                $count = 0;

                if (isset($tasks[$mapelId])) {
                    foreach ($tasks[$mapelId] as $task) {
                        $nilaiObj = $task->nilaiSiswa->first();
                        $nilai    = $nilaiObj ? $nilaiObj->nilai : null;

                        if (!is_null($nilai)) {
                            $total += $nilai;
                            $count++;
                        }
                    }
                }

                $rata = $count > 0 ? round($total / $count, 2) : null;

                $rekap[] = [
                    'mapel_id' => $mapelId,
                    'mapel'    => $penugasan->mapel->nama_mapel ?? '-',
                    'guru'     => $penugasan->guru->nama_guru ?? '-',
                    'rata'     => $rata,
                    'has_data' => $count > 0 // Flag untuk frontend jika diperlukan
                ];
            }
        }

        return view('dashboard.ortu.nilai-siswa.nilai-harian', [
            'rekap'     => $rekap,
            'kelasFull' => $kelasFull,
            'semester'  => $semester,
            'kelasList' => $kelasList,
            'kelasId'   => $kelasId,
        ]);
    }

    public function nilaiHarianDetail(Request $request, $mapelId)
    {
        $idSiswa = $request->session()->get('selected_siswa_id');
        $kelasId = $request->input('kelas_id');

        if (!$idSiswa || !$kelasId) {
            return response()->json(['success' => false, 'msg' => 'Parameter kurang.'], 400);
        }

        // Cek Jadwal untuk info header modal
        $jadwal = JadwalMapel::with(['penugasan.mapel', 'penugasan.guru'])
            ->where('kelas_id', $kelasId)
            ->whereHas('penugasan', fn($q) => $q->where('mapel_id', $mapelId))
            ->first();

        // Data default jika jadwal tidak ketemu (case rare)
        $guruNama = $jadwal->penugasan->guru->nama_guru ?? '-';
        $mapelNm  = $jadwal->penugasan->mapel->nama_mapel ?? '-';

        // Ambil Nama Kelas
        $kelasSiswa = SiswaKelas::with('kelas')->where('id_siswa', $idSiswa)->where('kelas_id', $kelasId)->first();
        $kelasFull = ($kelasSiswa->kelas->tingkat_kelas ?? '') . ' ' . ($kelasSiswa->kelas->nama_kelas ?? '');

        $semester = $request->input('semester', 'Ganjil');

        // Ambil Detail Nilai
        $tasks = NilaiTambahan::where('kelas_id', $kelasId)
            ->where('mapel_id', $mapelId)
            ->where('tahun_ajaran', self::TAHUN_AJARAN)
            ->where('semester', $semester)
            ->orderBy('tgl_entry', 'desc') // Biasanya user ingin lihat nilai terbaru paling atas
            ->with(['nilaiSiswa' => fn($q) => $q->where('id_siswa', $idSiswa)])
            ->get();

        $rows = $tasks->map(function ($tugas) {
            $nilaiObj = $tugas->nilaiSiswa->first();
            return [
                'tgl'   => $tugas->tgl_entry ? Carbon::parse($tugas->tgl_entry)->format('d/m/Y') : '-',
                'jenis' => $tugas->tipe_penunjang,
                'ket'   => $tugas->deskripsi,
                'nilai' => $nilaiObj ? $nilaiObj->nilai : null,
            ];
        });

        return response()->json([
            'success' => true,
            'meta' => [
                'kelas_full' => $kelasFull,
                'guru'       => $guruNama,
                'mapel_name' => $mapelNm,
                'semester'   => $semester,
            ],
            'data' => $rows
        ]);
    }

    public function nilaiUjian(Request $request)
    {
        $semester   = $request->input('semester', 'Ganjil');
        $jenisUjian = $request->input('jenis_ujian', 'PTS'); // Default PTS
        $idSiswa    = $request->session()->get('selected_siswa_id');

        // 1. Validasi Siswa
        if (!$idSiswa) {
            return back()->with('error', 'Siswa belum dipilih.');
        }

        // 2. Ambil Daftar Kelas Siswa
        $kelasList = SiswaKelas::with('kelas')
            ->where('id_siswa', $idSiswa)
            ->where('tahun_ajaran', self::TAHUN_AJARAN)
            ->get();

        if ($kelasList->isEmpty()) {
            return back()->with('error', 'Siswa tidak terdaftar di kelas manapun.');
        }

        // 3. Tentukan Kelas Aktif
        $kelasIdDefault = $kelasList->first()->kelas_id;
        $kelasId        = $request->input('kelas_id', $kelasIdDefault);
        $kelasNow       = $kelasList->firstWhere('kelas_id', $kelasId);

        if (!$kelasNow) {
            return back()->with('error', 'Kelas tidak valid.');
        }

        $kelasFull = ($kelasNow->kelas->tingkat_kelas ?? '') . ' ' . ($kelasNow->kelas->nama_kelas ?? '');

        // 4. Ambil Jadwal Mapel (Untuk list mata pelajaran)
        // Kita hanya butuh list mapel yang diajarkan di kelas tersebut
        $jadwalMapel = JadwalMapel::with(['penugasan.mapel', 'penugasan.guru'])
            ->where('kelas_id', $kelasId)
            ->get()
            ->unique('penugasan.mapel_id');

        $rekap = [];

        // 5. Ambil Data Nilai Ujian Siswa yang Relevan (Kunci: mapel_id)
        $nilaiUjianData = [];

        // A. Ambil semua NilaiUjian (Master) yang sesuai kriteria
        $masterUjian = NilaiUjian::where('kelas_id', $kelasId)
            ->where('tahun_ajaran', self::TAHUN_AJARAN)
            ->where('semester', $semester)
            ->where('tipe_ujian', $jenisUjian) // Menggunakan 'tipe_ujian' sesuai model
            ->pluck('id', 'mapel_id'); // Ambil id master ujian, di-key berdasarkan mapel_id

        // B. Jika ada Master Ujian, ambil Nilai Siswa yang terkait
        if ($masterUjian->isNotEmpty()) {
            $nilaiSiswaQuery = NilaiUjianSiswa::whereIn('nilai_ujian_id', $masterUjian->values())
                ->where('id_siswa', $idSiswa)
                ->get()
                ->keyBy('nilai_ujian_id'); // Key by nilai_ujian_id (Foreign Key ke NilaiUjian)

            // C. Petakan Nilai Siswa ke mapel_id-nya
            foreach ($masterUjian as $mapelId => $nilaiUjianId) {
                if (isset($nilaiSiswaQuery[$nilaiUjianId])) {
                    $nilaiUjianData[$mapelId] = $nilaiSiswaQuery[$nilaiUjianId]->nilai;
                } else {
                    $nilaiUjianData[$mapelId] = null; // Mapel ada, tapi nilai siswa belum diinput
                }
            }
        }

        // 6. Looping Mapel dan Gabungkan dengan Nilai
        foreach ($jadwalMapel as $jadwal) {
            $penugasan = $jadwal->penugasan;

            // Pastikan penugasan dan mapel/guru tidak null (jika relasi di JadwalMapel bermasalah)
            if (!$penugasan || !$penugasan->mapel || !$penugasan->guru) {
                continue;
            }

            $mapelId = $penugasan->mapel_id;

            // Ambil nilai dari data yang sudah dipetakan di langkah 5
            $nilai = $nilaiUjianData[$mapelId] ?? null;

            $rekap[] = [
                'mapel' => $penugasan->mapel->nama_mapel,
                'guru'  => $penugasan->guru->nama_guru,
                'nilai' => $nilai, // Nilai dari NilaiUjianSiswa
            ];
        }

        return view('dashboard.ortu.nilai-siswa.nilai-ujian', [
            'rekap'      => $rekap,
            'kelasFull'  => $kelasFull,
            'semester'   => $semester,
            'jenisUjian' => $jenisUjian,
            'kelasList'  => $kelasList,
            'kelasId'    => $kelasId,
        ]);
    }
}
