<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

// MODEL
use App\Models\User;
use App\Models\SIAKAD\SCHOOL\Guru;
use App\Models\SIAKAD\SCHOOL\Ortu;
use App\Models\SIAKAD\SCHOOL\Siswa;
use App\Models\SIAKAD\SCHOOL\Kelas;
use App\Models\SIAKAD\SCHOOL\GuruMapel;
use App\Models\SIAKAD\SCHOOL\SiswaKelas;
use App\Models\SIAKAD\SCHOOL\Absensi;
use App\Models\SIAKAD\SCHOOL\NilaiAkhirSemester;
use App\Models\SIAKAD\SCHOOL\Pengumuman;
use App\Models\SIAKAD\SCHOOL\PengumumanUser;
use App\Models\SIAKAD\SCHOOL\JadwalMapel;
use App\Models\SIAKAD\SCHOOL\NilaiTambahan;
use App\Models\SIAKAD\SCHOOL\NilaiUjian;
use App\Models\SIAKAD\SCHOOL\CatatanRaporSemester;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\SIAKAD\SCHOOL\User $user */
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return $this->adminIndex();
        }

        if ($user->hasRole('guru')) {
            return $this->guruIndex();
        }

        if ($user->hasRole('Orang Tua')) {
            return $this->ortuIndex();
        }

        return redirect()->route('profile')
            ->with('error', 'Akses dashboard tidak terdefinisi.');
    }


    /* ============================================================
     |  DASHBOARD GURU
     ============================================================ */
    public function guruIndex()
    {
        $user = Auth::user();
        $guru  = $user->guru; // relasi hasOne

        if (!$guru) {
            return abort(403, 'Data guru tidak ditemukan.');
        }

        // 1. Wali kelas (kelas yang dipegang oleh guru)
        $waliKelas = $guru->kelasWali()->first();

        // 2. Total Mapel Diampu
        $totalMapel = $guru->penugasanMapel()->count();

        // 3. Jadwal mengajar hari ini
        $hariIni = Carbon::now()->translatedFormat('l');

        $jadwalHariIni = JadwalMapel::whereIn(
            'guru_mapel_id',
            $guru->penugasanMapel()->pluck('guru_mapel_id')
        )
            ->where('hari', $hariIni)
            ->with(['kelas', 'penugasan.mapel'])
            ->orderBy('jam_mulai')
            ->get();

        // 4. Penilaian Harian terbaru (nilai_tambahan)
        $nilaiHarian = NilaiTambahan::whereIn(
            'guru_mapel_id',
            $guru->penugasanMapel()->pluck('guru_mapel_id')
        )
            ->with(['mapel', 'kelas'])
            ->orderBy('tgl_entry', 'desc')
            ->limit(5)
            ->get();

        // 5. Nilai Ujian terbaru
        $nilaiUjian = NilaiUjian::whereIn(
            'guru_mapel_id',
            $guru->penugasanMapel()->pluck('guru_mapel_id')
        )
            ->with(['mapel', 'kelas'])
            ->orderBy('tanggal_ujian', 'desc')
            ->limit(5)
            ->get();

        $tanggalHariIni = Carbon::now()->translatedFormat('l, d F Y');

        return view('dashboard.guru.index', [
            'guru'         => $guru,
            'waliKelas'    => $waliKelas,
            'totalMapel'   => $totalMapel,
            'jadwalHariIni' => $jadwalHariIni,
            'nilaiHarian'  => $nilaiHarian,
            'nilaiUjian'   => $nilaiUjian,
            'tanggalHariIni'  => $tanggalHariIni,
        ]);
    }


    /* ============================================================
     |  DASHBOARD ADMIN & ORTU (placeholder)
     ============================================================ */

    public function adminIndex()
    {
        // =========================
        // SUMMARY
        // =========================
        $totalSiswa = Siswa::count();
        $totalGuru  = Guru::count();
        $totalKelas = Kelas::where('status', 1)->count();

        $tahunAjaran = Kelas::where('status', 1)
            ->distinct()
            ->pluck('tahun_ajaran')
            ->first() ?? '-';

        // =========================
        // AKTIVITAS TERBARU SISTEM (FULL DATA)
        // =========================
        $aktivitas = collect();

        /* ========= ADMIN - PENGUMUMAN ========= */
        $pengumumanAktivitas = Pengumuman::latest()
            ->limit(10)
            ->get()
            ->map(function ($p) {
                return [
                    'aktor' => 'Admin',
                    'pesan' => "Pengumuman: {$p->judul}",
                    'waktu_raw' => $p->created_at->timestamp,
                    'waktu' => $p->created_at->format('d F Y, H:i'),
                    'warna' => 'border-yellow-400 text-yellow-700',

                    'group_key' => "pengumuman|{$p->id}",

                    'allow_expand' => false,
                ];
            });

        /* ========= GURU - NILAI HARIAN ========= */
        $nilaiHarianAktivitas = NilaiTambahan::where('status', 'Submitted')
            ->with(['kelas', 'mapel', 'guruMapel.guru'])
            ->orderByDesc('tgl_update')
            ->limit(20)
            ->get()
            ->map(function ($n) {

                $kelas = "{$n->kelas->tingkat_kelas} {$n->kelas->nama_kelas}";
                $guru  = optional($n->guruMapel->guru)->nama_guru ?? 'Guru';
                $mapel = $n->mapel->nama_mapel;

                return [
                    'aktor' => $guru,
                    'pesan' => "Submit nilai harian {$mapel} kelas {$kelas}",
                    'waktu_raw' => Carbon::parse($n->tgl_update)->timestamp,
                    'waktu' => Carbon::parse($n->tgl_update)->format('d F Y, H:i'),
                    'warna' => 'border-green-500 text-green-700',
                    'group_key' => "nilai_harian|{$guru}|{$mapel}|{$kelas}",

                    // 🔑 AKTIFKAN DROPDOWN
                    'allow_expand' => true,

                    // 🔽 DETAIL SUB AKTIVITAS
                    'detail' => [
                        'tugas' => $n->tipe_penunjang,
                        'guru' => $guru,
                    ],
                ];
            });

        /* ========= GURU - NILAI UJIAN ========= */
        $nilaiUjianAktivitas = NilaiUjian::where('status', 'Submitted')
            ->with(['kelas', 'mapel', 'guruMapel.guru'])
            ->orderByDesc('tgl_update')
            ->limit(50)
            ->get()
            ->map(function ($u) {

                $kelas = "{$u->kelas->tingkat_kelas} {$u->kelas->nama_kelas}";
                $guru  = optional($u->guruMapel->guru)->nama_guru ?? 'Guru';
                $mapel = $u->mapel->nama_mapel;

                return [
                    'pesan' => "Submit nilai {$u->tipe_ujian} {$mapel} kelas {$kelas}",
                    'aktor' => $guru,
                    'waktu_raw' => Carbon::parse($u->tgl_update)->timestamp,
                    'waktu' => Carbon::parse($u->tgl_update)->format('d F Y, H:i'),
                    'warna' => 'border-green-500 text-green-700',

                    // ❗ PENTING
                    'group_key' => null,
                    'allow_expand' => false,
                ];
            });

        /* ========= RAPOR SEMESTER (WALAS & ADMIN) ========= */
        $raporAktivitas = CatatanRaporSemester::where('status_publikasi', 'Diterbitkan')
            ->with('kelas')
            ->orderByDesc('tgl_update')
            ->get()
            ->groupBy('kelas_id')
            ->map(function ($items) {

                $first = $items->first();
                $kelas = $first->kelas;

                return [
                    'aktor' => 'Admin',
                    'pesan' => "Admin menerbitkan rapor kelas {$kelas->tingkat_kelas} {$kelas->nama_kelas}",
                    'waktu_raw' => Carbon::parse($first->tgl_update)->timestamp,
                    'waktu' => Carbon::parse($first->tgl_update)->format('d F Y, H:i'),
                    'warna' => 'border-blue-500 text-blue-700',
                    'group_key' => "rapor|{$kelas->id}",
                    'allow_expand' => false,
                    'count_force' => 1,
                ];
            })
            ->values();

        // Gabungkan semua aktivitas
        $aktivitasSemua = collect()
            ->merge($raporAktivitas)
            ->merge($pengumumanAktivitas)
            ->merge($nilaiHarianAktivitas)
            ->merge($nilaiUjianAktivitas)
            ->sortByDesc('waktu_raw')
            ->values();

        $aktivitasGrouped = $aktivitasSemua
            ->groupBy(function ($item) {

                // ✅ HANYA NILAI HARIAN YANG DIGROUP
                if (str_starts_with($item['group_key'] ?? '', 'nilai_harian')) {
                    return $item['group_key'];
                }

                // ❌ YANG LAIN → PAKSA JADI SATUAN SENDIRI
                return uniqid();
            })
            ->map(function ($items) {

                $first = $items->first();

                return [
                    'pesan' => $first['pesan'],
                    'warna' => $first['warna'],
                    'waktu' => $first['waktu'],
                    'waktu_raw' => $first['waktu_raw'],

                    // ❗ NILAI HARIAN = jumlah asli, LAINNYA = 1
                    'count' => str_starts_with($first['group_key'] ?? '', 'nilai_harian')
                        ? $items->count()
                        : 1,

                    'allow_expand' => $first['allow_expand'] ?? false,

                    'details' => $items
                        ->pluck('detail')
                        ->filter()
                        ->map(function ($d) {
                            return "Tugas: {$d['tugas']} ({$d['guru']})";
                        })
                        ->unique()
                        ->values(),
                ];
            })
            ->sortByDesc('waktu_raw')
            ->values();

        // =========================
        // 3 TERATAS UNTUK DASHBOARD
        // =========================
        $aktivitasTerbaru = $aktivitasGrouped
            ->sortByDesc(function ($item) {
                return Carbon::parse($item['waktu'])->timestamp;
            })
            ->take(3)
            ->values();

        // =========================
        // GURU BELUM ABSEN
        // =========================
        $hariIni = Carbon::now()->translatedFormat('l');

        $guruBelumAbsen = JadwalMapel::where('hari', $hariIni)
            ->whereDoesntHave('absensi')
            ->with(['penugasan.guru', 'penugasan.mapel'])
            ->limit(10)
            ->get()
            ->map(function ($jadwal) {
                return (object) [
                    'guru'  => $jadwal->penugasan->guru,
                    'mapel' => $jadwal->penugasan->mapel,
                ];
            });

        // =========================
        // PENGUMUMAN
        // =========================
        $pengumuman = Pengumuman::latest()->limit(5)->get();

        return view('dashboard.admin.index', compact(
            'totalSiswa',
            'totalGuru',
            'totalKelas',
            'tahunAjaran',
            'aktivitasTerbaru',
            'aktivitasSemua',
            'guruBelumAbsen',
            'pengumuman',
            'aktivitasGrouped'
        ));
    }

    public function ortuIndex()
    {
        $user = Auth::user();

        // 1. Ambil data ortu
        $ortu = $user->ortu;
        if (!$ortu) {
            abort(403, 'Data orang tua tidak ditemukan.');
        }

        // 2. Ambil 1 siswa (asumsi 1 ortu = 1 siswa, bisa dikembangkan)
        $siswa = $ortu->siswa()->with('enrollment.kelas.waliKelas')->first();
        if (!$siswa) {
            abort(403, 'Data siswa tidak ditemukan.');
        }

        // 3. Kelas aktif siswa
        $kelasAktif = $siswa->enrollment()
            ->whereIn('status', ['Aktif', 'aktif', 1])
            ->with(['kelas.waliKelas'])
            ->latest('tahun_ajaran')
            ->first();

        // 4. Kehadiran terbaru (5 terakhir)
        $kehadiranTerbaru = Absensi::where('id_siswa', $siswa->id_siswa)
            ->orderBy('waktu_absen', 'desc')
            ->limit(5)
            ->get();

        // 5. Rekap kehadiran semester
        $rekapKehadiran = Absensi::where('id_siswa', $siswa->id_siswa)
            ->selectRaw("
        SUM(status = 'Hadir') as hadir,
        SUM(status = 'Sakit') as sakit,
        SUM(status = 'Izin') as izin,
        SUM(
            status IN ('Alpha', 'Alfa', 'TIDAK HADIR', 'Tidak Hadir')
        ) as alpha
    ")
            ->first();


        $totalHari = max(1, $rekapKehadiran->hadir + $rekapKehadiran->sakit + $rekapKehadiran->izin + $rekapKehadiran->alpha);

        $kehadiran = [
            'Hadir' => [
                'jumlah' => $rekapKehadiran->hadir,
                'persen' => round(($rekapKehadiran->hadir / $totalHari) * 100),
                'warna' => 'bg-green-500'
            ],
            'Sakit' => [
                'jumlah' => $rekapKehadiran->sakit,
                'persen' => round(($rekapKehadiran->sakit / $totalHari) * 100),
                'warna' => 'bg-yellow-500'
            ],
            'Izin' => [
                'jumlah' => $rekapKehadiran->izin,
                'persen' => round(($rekapKehadiran->izin / $totalHari) * 100),
                'warna' => 'bg-blue-500'
            ],
            'Tidak Hadir' => [
                'jumlah' => $rekapKehadiran->alpha,
                'persen' => round(($rekapKehadiran->alpha / $totalHari) * 100),
                'warna' => 'bg-red-500'
            ],
        ];

        // 6. Rata-rata nilai semester aktif
        $rataNilai = null;
        if ($kelasAktif) {
            $catatan = \App\Models\SIAKAD\SCHOOL\CatatanRaporSemester::where('id_siswa', $siswa->id_siswa)
                ->where('kelas_id', $kelasAktif->kelas_id)
                ->where('semester', $kelasAktif->semester ?? 'Ganjil')
                ->where('tahun_ajaran', $kelasAktif->tahun_ajaran)
                ->where('status_publikasi', 'Diterbitkan')
                ->first();

            if ($catatan) {
                $rataNilai = NilaiAkhirSemester::where('id_siswa', $siswa->id_siswa)
                    ->where('kelas_id', $kelasAktif->kelas_id)
                    ->where('semester', $kelasAktif->semester ?? 'Ganjil')
                    ->where('tahun_ajaran', $kelasAktif->tahun_ajaran)
                    ->avg('nilai_rapor');
            }
        }

        // 7. Pengumuman untuk orang tua
        $pengumuman = PengumumanUser::with('pengumuman')
            ->where('users_id', $user->users_id)
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($notif) {
                return (object) [
                    'judul' => $notif->pengumuman->judul,
                    'created_at' => $notif->pengumuman->created_at,
                    'is_read' => $notif->is_read,
                ];
            });


        return view('dashboard.ortu.index', compact(
            'ortu',
            'siswa',
            'kelasAktif',
            'kehadiranTerbaru',
            'kehadiran',
            'rataNilai',
            'pengumuman'
        ));
    }
}
