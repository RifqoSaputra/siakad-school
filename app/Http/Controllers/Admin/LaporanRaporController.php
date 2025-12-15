<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use App\Models\SIAKAD\SCHOOL\Kelas;
use App\Models\SIAKAD\SCHOOL\SiswaKelas;
use App\Models\SIAKAD\SCHOOL\Mapel;
use App\Models\SIAKAD\SCHOOL\JadwalMapel;
use App\Models\SIAKAD\SCHOOL\CatatanRaporSemester;
use App\Models\SIAKAD\SCHOOL\Absensi;
use App\Models\SIAKAD\SCHOOL\NilaiAkhirSemester;
use App\Models\SIAKAD\SCHOOL\NilaiTambahanSiswa;
use App\Models\SIAKAD\SCHOOL\NilaiUjianSiswa;

class LaporanRaporController extends Controller
{
    public function index(Request $request)
    {
        // 1. Inisialisasi Filter
        $tahunAjaran = $request->input('tahun_ajaran', '2024/2025');
        $semester    = $request->input('semester', 'Ganjil');
        $kelasId     = $request->input('kelas_id');

        // 2. Load List Kelas untuk Dropdown
        $kelas = Kelas::where('status', 1)->get()->map(function ($item) {
            $item->nama_kelas_lengkap = $item->tingkat_kelas . ' ' . $item->nama_kelas;
            return $item;
        });

        $dataRapor = [];
        $isFilterApplied = false;
        $isRaporSubmitted = false;
        $kelasInfo = null;
        $allMapel = collect();
        $statusPublikasi = 'Draft';

        if ($kelasId) {
            $isFilterApplied = true;
            $kelasInfo = $kelas->where('kelas_id', $kelasId)->first();

            if ($kelasInfo) {
                try {
                    // Ambil Mapel relevan untuk kelas ini (dipakai di banyak fungsi)
                    $allMapel = $this->getRelevantMapelForClass($kelasId, $semester, $tahunAjaran);

                    // Hitung status publikasi kelas (Draft | Terkunci | Diterbitkan)
                    $statusPublikasi = $this->getPublishStatus($kelasId, $semester, $tahunAjaran);
                    $isRaporSubmitted = $statusPublikasi === 'Diterbitkan';

                    // Ambil Siswa
                    $siswaList = SiswaKelas::where('kelas_id', $kelasId)
                        ->where('tahun_ajaran', $tahunAjaran)
                        ->where('status', 1)
                        ->with(['siswa', 'kelas'])
                        ->get();

                    // Proses data per siswa
                    foreach ($siswaList as $siswaKelas) {
                        $siswa = $siswaKelas->siswa;
                        if (!$siswa) continue;
                        $idSiswa = $siswa->id_siswa;

                        // Hitung nilai (realtime jika belum diterbitkan, atau ambil dari nilai_akhir_semester jika sudah)
                        $mapelScores = $this->getStudentGrades($idSiswa, $kelasId, $semester, $tahunAjaran, $allMapel, $isRaporSubmitted);

                        // Ambil catatan walikelas (status_publikasi_siswa, is_filled, dsb)
                        $catatanData = $this->getCatatanRapor($idSiswa, $kelasId, $semester, $tahunAjaran, $isRaporSubmitted);

                        // Ambil absensi
                        $absensi = $this->getStudentAttendance($idSiswa, $kelasId, $semester, $tahunAjaran);

                        // Tentukan status_final per siswa menggunakan helper yang konsisten
                        $finalStatus = $this->getStudentStatusFinal($idSiswa, $kelasId, $semester, $tahunAjaran, $allMapel);

                        // Siapkan teks tampilan
                        $rataRataNilaiTeks = $mapelScores['rata_rata_total'] > 0 ? number_format($mapelScores['rata_rata_total'], 2) : 'Belum Lengkap';

                        // Predikat sikap hanya tunjukkan jika walas Terkunci/Diterbitkan
                        $isFinalizedForDisplay = in_array($catatanData['status_publikasi_siswa'] ?? 'Draft', ['Terkunci', 'Diterbitkan']);
                        $predikatSikapTeks = $isFinalizedForDisplay ? ($catatanData['predikat_sikap'] ?? 'Belum Diisi') : 'Belum Lengkap';

                        $catatanOrtu = [
                            'Sikap'       => $catatanData['predikat_sikap'],
                            'Kepribadian' => $catatanData['catatan_walikelas'],
                            'Kehadiran'   => $absensi
                        ];

                        $raporData = [
                            'rapor_id'        => $idSiswa,
                            'nis'             => $siswa->nis,
                            'nama'            => $siswa->nama,
                            'kelas_id'        => $kelasId,
                            'kelas_full'      => $siswaKelas->kelas->nama_kelas_lengkap ?? '-',
                            'semester'        => $semester,
                            'tahun_ajaran'    => $tahunAjaran,
                            'rata_rata_nilai' => $rataRataNilaiTeks,
                            'predikat_sikap'  => $predikatSikapTeks,
                            'detail_rapor' => [
                                'kelas_full'   => $siswaKelas->kelas->nama_kelas_lengkap ?? '-',
                                'semester'     => $semester,
                                'tahun_ajaran' => $tahunAjaran,
                                'nilai_mapel'  => $mapelScores['mapel_scores'],
                                'catatan_rapor' => [
                                    'Sikap'   => $catatanData['predikat_sikap'] ?? null,
                                    'Kepribadian' => $catatanData['catatan_walikelas'] ?? null,
                                    'status_publikasi' => $catatanData['status_publikasi_siswa'] ?? 'Draft',
                                ],

                                'absensi'      => $absensi
                            ],

                            'status_final'    => $finalStatus,
                        ];

                        $dataRapor[] = $raporData;
                    }
                } catch (\Exception $e) {
                    Log::error("Error LaporanRapor: " . $e->getMessage());
                    $request->session()->flash('error', "Terjadi kesalahan data: " . $e->getMessage());
                    $dataRapor = [];
                }
            }
        }

        return view('dashboard.admin.laporan-rapor-siswa', [
            'dataRapor'         => $dataRapor,
            'kelas'             => $kelas,
            'kelasInfo'         => $kelasInfo,
            'tahunAjaran'       => $tahunAjaran,
            'semester'          => $semester,
            'kelasId'           => $kelasId,
            'isFilterApplied'   => $isFilterApplied,
            'isRaporSubmitted'  => $isRaporSubmitted,
            'statusPublikasi'   => $statusPublikasi,
            'error'             => session('error') ?? null
        ]);
    }

    /**
     * Ambil mapel yang relevan untuk kelas ini pada semester & tahun ajaran.
     */
    private function getRelevantMapelForClass($kelasId, $semester, $tahunAjaran)
    {
        $mapelIds = JadwalMapel::where('jadwal_mapel.kelas_id', $kelasId)
            ->join('guru_mapel', 'jadwal_mapel.guru_mapel_id', '=', 'guru_mapel.guru_mapel_id')
            ->where('guru_mapel.semester', $semester)
            ->where('guru_mapel.tahun_ajaran', $tahunAjaran)
            ->select('guru_mapel.mapel_id')
            ->distinct()
            ->pluck('mapel_id');

        if ($mapelIds->isEmpty()) return collect();

        return Mapel::whereIn('mapel_id', $mapelIds)
            ->where('status', 1)
            ->get();
    }

    /**
     * Hitung nilai per siswa. Jika $isRaporSubmitted true -> ambil dari NilaiAkhirSemester.
     * Jika false -> hitung realtime tetapi HANYA jika semua komponen master berstatus 'Submitted'.
     */
    private function getStudentGrades($idSiswa, $kelasId, $semester, $tahun, $allMapel, $isRaporSubmitted)
    {
        $mapelScores = [];
        $totalNilai = 0;
        $countMapel = 0;

        foreach ($allMapel as $mapel) {
            $nilaiRapor = 0;
            $deskripsi = '-';
            $kkm = 75;

            $harian = 0;
            $pts = 0;
            $pas = 0;

            if ($isRaporSubmitted) {
                $fixedNilai = NilaiAkhirSemester::where('id_siswa', $idSiswa)
                    ->where('mapel_id', $mapel->mapel_id)
                    ->where('semester', $semester)
                    ->where('tahun_ajaran', $tahun)
                    ->first();
                if ($fixedNilai) {
                    $nilaiRapor = (float) $fixedNilai->nilai_rapor;
                    $deskripsi = $fixedNilai->deskripsi ?? '-';
                    $kkm = $fixedNilai->kkm ?? 75;
                }
            } else {
                // Cek apakah semua master untuk mapel ini sudah Submitted
                $isHarianSubmitted = DB::table('nilai_tambahan')
                    ->where('mapel_id', $mapel->mapel_id)
                    ->where('kelas_id', $kelasId)
                    ->where('semester', $semester)
                    ->where('tahun_ajaran', $tahun)
                    ->exists() ?
                    DB::table('nilai_tambahan')
                    ->where('mapel_id', $mapel->mapel_id)
                    ->where('kelas_id', $kelasId)
                    ->where('semester', $semester)
                    ->where('tahun_ajaran', $tahun)
                    ->where('status', 'Submitted')
                    ->exists()
                    : false; // jika tidak ada master, dianggap belum submitted

                $masterPts = DB::table('nilai_ujian')
                    ->where('mapel_id', $mapel->mapel_id)
                    ->where('kelas_id', $kelasId)
                    ->where('tipe_ujian', 'PTS')
                    ->where('semester', $semester)
                    ->where('tahun_ajaran', $tahun);

                $isPtsSubmitted = $masterPts->exists() ? $masterPts->where('status', 'Submitted')->exists() : false;

                $masterPas = DB::table('nilai_ujian')
                    ->where('mapel_id', $mapel->mapel_id)
                    ->where('kelas_id', $kelasId)
                    ->whereIn('tipe_ujian', ['PAS', 'UAS'])
                    ->where('semester', $semester)
                    ->where('tahun_ajaran', $tahun);

                $isPasSubmitted = $masterPas->exists() ? $masterPas->where('status', 'Submitted')->exists() : false;

                // Hanya hitung jika semua komponen ada dan Submitted
                if ($isHarianSubmitted && $isPtsSubmitted && $isPasSubmitted) {

                    // rata-rata harian (nilai_tambahan_siswa yang berelasi dengan master)
                    $harian = NilaiTambahanSiswa::whereHas('masterTambahan', function ($q) use ($mapel, $semester, $tahun) {
                        $q->where('mapel_id', $mapel->mapel_id)
                            ->where('semester', $semester)
                            ->where('tahun_ajaran', $tahun)
                            ->where('status', 'Submitted');
                    })->where('id_siswa', $idSiswa)->avg('nilai') ?? 0;

                    // PTS
                    $pts = NilaiUjianSiswa::whereHas('masterUjian', function ($q) use ($mapel, $semester, $tahun) {
                        $q->where('mapel_id', $mapel->mapel_id)
                            ->where('tipe_ujian', 'PTS')
                            ->where('semester', $semester)
                            ->where('tahun_ajaran', $tahun)
                            ->where('status', 'Submitted');
                    })->where('id_siswa', $idSiswa)->value('nilai') ?? 0;

                    // PAS / UAS
                    $pas = NilaiUjianSiswa::whereHas('masterUjian', function ($q) use ($mapel, $semester, $tahun) {
                        $q->where('mapel_id', $mapel->mapel_id)
                            ->whereIn('tipe_ujian', ['PAS', 'UAS'])
                            ->where('semester', $semester)
                            ->where('tahun_ajaran', $tahun)
                            ->where('status', 'Submitted');
                    })->where('id_siswa', $idSiswa)->value('nilai') ?? 0;

                    if ($harian > 0 || $pts > 0 || $pas > 0) {
                        $nilaiRapor = ($harian * 0.50) + ($pts * 0.20) + ($pas * 0.30);
                    }

                    if ($nilaiRapor > 0) {
                        if ($nilaiRapor >= 90) $deskripsi = "Sangat Baik dalam memahami materi.";
                        elseif ($nilaiRapor >= 75) $deskripsi = "Baik dalam memahami materi.";
                        else $deskripsi = "Perlu peningkatan pemahaman.";
                    }
                }
            }

            if (is_numeric($nilaiRapor) && $nilaiRapor > 0) {
                $totalNilai += $nilaiRapor;
                $countMapel++;
            }

            $mapelScores[] = [
                'mapel'         => $mapel->nama_mapel,
                'mapel_id'      => $mapel->mapel_id,
                'kkm'           => $kkm,
                'nilai_rapor'   => $nilaiRapor > 0 ? number_format((float)$nilaiRapor, 1) : '-',
                'nilai_akhir'   => $nilaiRapor > 0 ? number_format((float)$nilaiRapor, 1) : '-',
                'rata_harian'   => $harian > 0 ? number_format((float)$harian, 1) : '-',
                'pts'           => $pts > 0 ? $pts : '-',
                'pas'           => $pas > 0 ? $pas : '-',
                'deskripsi'     => $deskripsi
            ];
        }

        $totalMapel = $allMapel->count();
        $avg = ($countMapel > 0 && $countMapel === $totalMapel) ? ($totalNilai / $countMapel) : 0;

        return [
            'mapel_scores'    => $mapelScores,
            'rata_rata_total' => $avg
        ];
    }

    /**
     * Ambil catatan rapor per siswa.
     */
    private function getCatatanRapor($idSiswa, $kelasId, $semester, $tahun, $isRaporSubmitted)
    {
        $catatan = CatatanRaporSemester::where('id_siswa', $idSiswa)
            ->where('kelas_id', $kelasId)
            ->where('semester', $semester)
            ->where('tahun_ajaran', $tahun)
            ->first();

        $statusPublikasiSiswa = $catatan->status_publikasi ?? 'Draft';

        return [
            'status_publikasi_siswa' => $statusPublikasiSiswa,
            'is_filled'   => $catatan && $catatan->predikat_sikap && $catatan->catatan_walikelas,
            'predikat_sikap' => $catatan->predikat_sikap ?? null,
            'catatan_walikelas' => $catatan->catatan_walikelas ?? null,
            'status_text' => $statusPublikasiSiswa,
        ];
    }

    /**
     * Hitung status final per siswa: Diterbitkan | Lengkap | Belum Lengkap (...)
     */
    private function getStudentStatusFinal($idSiswa, $kelasId, $semester, $tahunAjaran, $allMapel)
    {
        // 1) dapatkan data catatan
        $catatan = CatatanRaporSemester::where('id_siswa', $idSiswa)
            ->where('kelas_id', $kelasId)
            ->where('semester', $semester)
            ->where('tahun_ajaran', $tahunAjaran)
            ->first();

        $statusPublikasiSiswa = $catatan ? ($catatan->status_publikasi ?? 'Draft') : 'Draft';
        $isCatatanFilled = $catatan && !empty($catatan->predikat_sikap) && !empty($catatan->catatan_walikelas);

        // 2) jika sudah Diterbitkan di catatan -> pastikan nilai_akhir_semester juga ada untuk semua mapel
        if ($statusPublikasiSiswa === 'Diterbitkan') {
            // cek nilai akhir exist untuk semua mapel relevan
            $missing = 0;
            foreach ($allMapel as $m) {
                $exists = NilaiAkhirSemester::where('id_siswa', $idSiswa)
                    ->where('mapel_id', $m->mapel_id)
                    ->where('semester', $semester)
                    ->where('tahun_ajaran', $tahunAjaran)
                    ->exists();
                if (!$exists) $missing++;
            }
            return $missing === 0 ? 'Diterbitkan' : 'Lengkap';
        }

        // 3) jika belum Diterbitkan -> hitung realtime apakah semua mapel siap & catatan terisi / terkunci
        $grades = $this->getStudentGrades($idSiswa, $kelasId, $semester, $tahunAjaran, $allMapel, false);

        // Jika semua mapel berhasil dihitung (rata_rata_total > 0) dan catatan walikelas sudah Terkunci
        if ($grades['rata_rata_total'] > 0 && $statusPublikasiSiswa === 'Terkunci') {
            return 'Lengkap';
        }

        // Jika semua nilai siap dan catatan terisi (walaupun walas belum kunci), kita masih anggap 'Lengkap'
        if ($grades['rata_rata_total'] > 0 && $statusPublikasiSiswa === 'Terkunci') {
            return 'Lengkap';
        }

        // Jika belum lengkap, beri alasan
        $alasan = [];
        if ($grades['rata_rata_total'] <= 0) $alasan[] = 'Nilai';
        if (!$isCatatanFilled) $alasan[] = 'Catatan';
        return 'Belum Lengkap' . (count($alasan) ? ' (' . implode(' & ', $alasan) . ')' : '');
    }

    /**
     * Ambil status publikasi kelas (Draft | Terkunci [= siap publish] | Diterbitkan)
     * RULE: Jika semua siswa Diterbitkan => Diterbitkan.
     * Jika semua siswa minimal 'Lengkap' (Lengkap atau Diterbitkan) => Terkunci (Siap Publish).
     * Else => Draft.
     */
    private function getPublishStatus($kelasId, $semester, $tahunAjaran)
    {
        $siswaIds = SiswaKelas::where('kelas_id', $kelasId)
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('status', 1)
            ->pluck('id_siswa');

        if ($siswaIds->isEmpty()) return 'Draft';

        $allMapel = $this->getRelevantMapelForClass($kelasId, $semester, $tahunAjaran);

        $total = $siswaIds->count();
        $countDiterbitkan = 0;
        $countLengkapOrDiterbitkan = 0;

        foreach ($siswaIds as $idSiswa) {
            $status = $this->getStudentStatusFinal($idSiswa, $kelasId, $semester, $tahunAjaran, $allMapel);
            if ($status === 'Diterbitkan') {
                $countDiterbitkan++;
                $countLengkapOrDiterbitkan++;
            } elseif ($status === 'Lengkap') {
                $countLengkapOrDiterbitkan++;
            }
        }

        if ($countDiterbitkan === $total) return 'Diterbitkan';
        if ($countLengkapOrDiterbitkan === $total) return 'Terkunci'; // interpretasi: siap publish
        return 'Draft';
    }

    private function getStudentAttendance($idSiswa, $kelasId, $semester, $tahun)
    {
        $jadwalIds = JadwalMapel::where('kelas_id', $kelasId)
            ->whereHas('penugasan', function ($q) use ($semester, $tahun) {
                $q->where('semester', $semester)
                    ->where('tahun_ajaran', $tahun);
            })
            ->pluck('jadwal_mapel_id');

        if ($jadwalIds->isEmpty()) {
            return ['Sakit' => 0, 'Izin'  => 0, 'Alpa'  => 0];
        }

        $sakit = Absensi::where('id_siswa', $idSiswa)
            ->whereIn('jadwal_mapel_id', $jadwalIds)
            ->where('status', 'Sakit')
            ->count();
        $izin = Absensi::where('id_siswa', $idSiswa)
            ->whereIn('jadwal_mapel_id', $jadwalIds)
            ->where('status', 'Izin')
            ->count();
        $alpa = Absensi::where('id_siswa', $idSiswa)
            ->whereIn('jadwal_mapel_id', $jadwalIds)
            ->whereIn('status', ['Alpha', 'Alfa', 'TIDAK HADIR'])
            ->count();
        return [
            'Sakit' => $sakit,
            'Izin'  => $izin,
            'Alpa'  => $alpa,
        ];
    }

    /**
     * Validasi sebelum final submit (dipanggil saat admin klik "Submit Rapor Kelas" -> validasi)
     */
    public function validateSubmit(Request $request)
    {
        $kelasId = $request->kelas_id;
        $semester = $request->semester;
        $tahunAjaran = $request->tahun_ajaran;

        $siswaList = SiswaKelas::where('kelas_id', $kelasId)
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('status', 1)
            ->with('siswa')
            ->get();

        if ($siswaList->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'Tidak ada siswa di kelas ini.'], 400);
        }

        $allMapel = $this->getRelevantMapelForClass($kelasId, $semester, $tahunAjaran);
        if ($allMapel->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'Tidak ada mata pelajaran terjadwal.'], 400);
        }

        // Jika sudah Diterbitkan sebelumnya, stop
        $isAlreadyPublished = CatatanRaporSemester::where('kelas_id', $kelasId)
            ->where('semester', $semester)
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('status_publikasi', 'Diterbitkan')
            ->exists();
        if ($isAlreadyPublished) {
            return response()->json(['status' => 'error', 'message' => 'Rapor sudah Diterbitkan sebelumnya.'], 400);
        }

        $siswaBelumLengkap = [];
        foreach ($siswaList as $sk) {
            $siswa = $sk->siswa;
            if (!$siswa) continue;
            $status = $this->getStudentStatusFinal($siswa->id_siswa, $kelasId, $semester, $tahunAjaran, $allMapel);
            if (strpos($status, 'Belum Lengkap') === 0) {
                $siswaBelumLengkap[] = $siswa->nama;
            }
        }

        if (!empty($siswaBelumLengkap)) {
            return response()->json([
                'status' => 'warning',
                'message' => 'Terdapat rapor siswa yang BELUM LENGKAP (Nilai Belum Submit Guru atau Catatan Walikelas Belum Diisi):',
                'list' => $siswaBelumLengkap
            ]);
        }

        return response()->json(['status' => 'success', 'message' => 'Semua rapor siswa sudah lengkap dan siap di SUBMIT (Diterbitkan).']);
    }

    /**
     * Final submit => simpan nilai_akhir_semester & ubah status catatan => Diterbitkan
     */
    public function finalSubmit(Request $request)
    {
        $kelasId = $request->kelas_id;
        $semester = $request->semester;
        $tahunAjaran = $request->tahun_ajaran;
        $userId = Auth::id();

        $allMapel = $this->getRelevantMapelForClass($kelasId, $semester, $tahunAjaran);
        if ($allMapel->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'Tidak ada mata pelajaran terjadwal.'], 400);
        }

        DB::beginTransaction();
        try {
            $siswaList = SiswaKelas::where('kelas_id', $kelasId)
                ->where('tahun_ajaran', $tahunAjaran)
                ->where('status', 1)
                ->get();

            // Pastikan kelas siap publish
            $statusKelas = $this->getPublishStatus($kelasId, $semester, $tahunAjaran);
            if ($statusKelas !== 'Terkunci') {
                DB::rollBack();
                return response()->json(['status' => 'error', 'message' => 'Kelas belum siap dipublish (masih ada yang Belum Lengkap).'], 400);
            }

            foreach ($siswaList as $sk) {
                $idSiswa = $sk->id_siswa;
                $grades = $this->getStudentGrades($idSiswa, $kelasId, $semester, $tahunAjaran, $allMapel, false);

                // Simpan nilai_akhir_semester untuk setiap mapel (only nilai > 0)
                foreach ($grades['mapel_scores'] as $score) {
                    $nilai = is_numeric(str_replace(',', '', (string)$score['nilai_rapor'])) ? (float) str_replace(',', '', (string)$score['nilai_rapor']) : 0;
                    if ($nilai > 0) {
                        NilaiAkhirSemester::updateOrCreate(
                            [
                                'id_siswa'      => $idSiswa,
                                'mapel_id'      => $score['mapel_id'],
                                'kelas_id'      => $kelasId,
                                'semester'      => $semester,
                                'tahun_ajaran'  => $tahunAjaran
                            ],
                            [
                                'nilai_rapor'   => $nilai,
                                'kkm'           => $score['kkm'],
                                'deskripsi'     => $score['deskripsi'],
                                'user_entry'    => $userId,
                                'tgl_entry'     => now(),
                                'user_update'   => $userId,
                                'tgl_update'    => now()
                            ]
                        );
                    }
                }

                // Update status catatan_rapor_semester menjadi Diterbitkan (jika belum)
                $existing = CatatanRaporSemester::where('id_siswa', $idSiswa)
                    ->where('kelas_id', $kelasId)
                    ->where('semester', $semester)
                    ->where('tahun_ajaran', $tahunAjaran)
                    ->first();

                $finalStatusKenaikan = $existing && !empty($existing->status_kenaikan) && $existing->status_kenaikan !== 'Belum Final'
                    ? $existing->status_kenaikan
                    : ($existing ? ($existing->status_kenaikan ?? 'Belum Final') : 'Belum Final');

                CatatanRaporSemester::updateOrCreate(
                    [
                        'id_siswa'      => $idSiswa,
                        'kelas_id'      => $kelasId,
                        'semester'      => $semester,
                        'tahun_ajaran'  => $tahunAjaran
                    ],
                    [
                        'status_publikasi' => 'Diterbitkan',
                        'status_kenaikan'  => $finalStatusKenaikan,
                        'user_update'      => $userId,
                        'tgl_update'       => now()
                    ]
                );
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Rapor kelas berhasil di SUBMIT dan Diterbitkan!']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Final Submit Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan saat Submit: ' . $e->getMessage()], 500);
        }
    }

    public function download(Request $request)
    {
        return back()->with('error', 'Fitur download belum diimplementasikan.');
    }
}
