<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\SIAKAD\SCHOOL\Guru;
use App\Models\SIAKAD\SCHOOL\GuruMapel;
use App\Models\SIAKAD\SCHOOL\JadwalMapel;
use App\Models\SIAKAD\SCHOOL\Kelas;
use App\Models\SIAKAD\SCHOOL\Mapel;
// Import model khusus Ujian yang baru
use App\Models\SIAKAD\SCHOOL\NilaiUjian;
use App\Models\SIAKAD\SCHOOL\NilaiUjianSiswa;
use App\Models\SIAKAD\SCHOOL\SiswaKelas;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class InputNilaiUjianController extends Controller
{
    /**
     * Menampilkan daftar Nilai UJIAN berdasarkan filter.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->users_id) {
            return redirect('/login')->with('error', 'Silakan login sebagai guru.');
        }

        $guru = Guru::where('users_id', $user->users_id)->first();

        if (!$guru) {
            return redirect()->back()->with('error', 'Data guru tidak ditemukan.');
        }

        $idGuru = $guru->id_guru;

        // 1. Tentukan Tahun Ajaran dan Semester aktif
        $defaultTahunAjaran = '2024/2025'; // Ganti dengan logika mendapatkan TA aktif
        $defaultSemester = 'Ganjil';

        $tahunAjaranFilter = $request->input('tahun_ajaran', $defaultTahunAjaran);
        $semesterFilter = $request->input('semester', $defaultSemester);
        $kelasIdFilter = $request->input('kelas_id');
        $mapelIdFilter = $request->input('mapel_id');

        // Tipe Ujian yang valid. Sesuai dengan tipe_ujian di NilaiUjian
        $tipeUjian = ['PTS', 'PAS'];

        // --- Mendapatkan Data Filter Awal (Kelas dan Mapel yang Diampu) ---
        $penugasanMapel = GuruMapel::where('id_guru', $idGuru)
            ->where('tahun_ajaran', $tahunAjaranFilter)
            ->where('semester', $semesterFilter)
            ->with(['mapel'])
            ->get();

        $listMapel = $penugasanMapel->pluck('mapel')->unique('mapel_id');
        $guruMapelIds = $penugasanMapel->pluck('guru_mapel_id');

        $kelasYangTersedia = JadwalMapel::whereIn('guru_mapel_id', $guruMapelIds)
            ->distinct('kelas_id')
            ->with(['kelas'])
            ->get()
            ->pluck('kelas')
            ->filter()
            ->unique('kelas_id')
            ->sortBy('nama_kelas')
            ->map(function ($kelas) {
                $kelas->nama_kelas_lengkap = trim(($kelas->tingkat_kelas ?? '') . ' ' . ($kelas->nama_kelas ?? ''));
                return $kelas;
            });

        // Tentukan nilai filter saat ini
        $currentMapelId = $mapelIdFilter ?? ($listMapel->first()->mapel_id ?? null);
        $currentKelasId = $kelasIdFilter ?? ($kelasYangTersedia->first()->kelas_id ?? null);

        // Untuk menampilkan nama di blade
        $currentMapel = $currentMapelId ? Mapel::find($currentMapelId)?->nama_mapel : null;
        $kelasObj = $currentKelasId ? $kelasYangTersedia->firstWhere('kelas_id', $currentKelasId) : null;
        $currentKelas = $kelasObj ? $kelasObj->nama_kelas_lengkap : null;

        // --- 2. Ambil Data Nilai Ujian (`NilaiUjian`) Sesuai Filter ---
        $listUjian = collect();
        if ($currentMapelId && $currentKelasId) {
            // Menggunakan MODEL NILAIUJIAN yang baru
            $listUjian = NilaiUjian::with('kelas', 'mapel', 'nilaiSiswa')
                ->where('kelas_id', $currentKelasId)
                ->where('mapel_id', $currentMapelId)
                ->whereIn('tipe_ujian', $tipeUjian) // Filter berdasarkan tipe_ujian
                ->where('tahun_ajaran', $tahunAjaranFilter)
                ->orderBy('tanggal_ujian', 'desc') // Urutkan berdasarkan tanggal_ujian
                ->get();

            // Tambahkan status pengisian nilai
            foreach ($listUjian as $ujian) {
                // Ambil total siswa yang aktif di kelas tersebut pada TA Ujian
                $totalSiswa = SiswaKelas::where('kelas_id', $ujian->kelas_id)
                    ->where('tahun_ajaran', $ujian->tahun_ajaran)
                    ->where('status', 1)
                    ->pluck('id_siswa');
                $jumlahSiswa = $totalSiswa->count();

                // Hitung nilai yang telah diisi. Menggunakan MODEL NILAIUJIANSISWA
                $siswaSudahDiisi = NilaiUjianSiswa::where('nilai_ujian_id', $ujian->id)
                    ->whereIn('id_siswa', $totalSiswa)
                    ->whereNotNull('nilai')
                    ->count();

                $ujian->totalSiswa = $jumlahSiswa;
                $ujian->siswaSudahDiisi = $siswaSudahDiisi;

                // Tentukan status pengisian
                if ($jumlahSiswa == 0) {
                    $ujian->statusPengisian = 'Belum Diisi';
                } elseif ($siswaSudahDiisi == $jumlahSiswa) {
                    $ujian->statusPengisian = 'Selesai';
                } elseif ($siswaSudahDiisi > 0) {
                    $ujian->statusPengisian = 'Sebagian';
                } else {
                    $ujian->statusPengisian = 'Belum Diisi';
                }
            }
        }

        // Kirim data ke view
        return view('dashboard.guru.input-nilai-ujian.index', compact(
            'tahunAjaranFilter',
            'semesterFilter',
            'currentKelasId',
            'currentMapelId',
            'currentMapel',
            'currentKelas',
            'listMapel',
            'kelasYangTersedia',
            'listUjian' // Mengganti listTugas menjadi listUjian
        ));
    }

    /**
     * Mengambil data siswa dan nilai yang sudah ada untuk input
     */
    public function getSiswaForInput(NilaiUjian $nilaiUjian) // Menggunakan parameter type NilaiUjian
    {
        $kelas = Kelas::where('kelas_id', $nilaiUjian->kelas_id)->first();

        if (!$kelas) {
            return response()->json([
                'success' => false,
                'message' => 'Kelas tidak ditemukan'
            ], 404);
        }

        // Ambil siswa yang aktif di kelas pada tahun ajaran ujian
        $listSiswa = SiswaKelas::where('kelas_id', $kelas->kelas_id)
            ->where('tahun_ajaran', $nilaiUjian->tahun_ajaran)
            ->where('status', 1)
            ->with('siswa:id_siswa,nis,nama')
            ->get()
            ->pluck('siswa')
            ->filter()
            ->values();

        // Ambil nilai existing: [id_siswa => nilai] Menggunakan MODEL NILAIUJIANSISWA
        $nilaiExisting = NilaiUjianSiswa::where('nilai_ujian_id', $nilaiUjian->id)
            ->pluck('nilai', 'id_siswa')
            ->toArray();

        // Lampirkan nilai ke masing2 objek siswa
        $dataSiswaWithNilai = $listSiswa->map(function ($s) use ($nilaiExisting) {
            $id = $s->id_siswa ?? $s->id ?? null;

            if (isset($nilaiExisting[$id])) {
                // Nilai disimpan sebagai decimal, pastikan dikonversi ke integer atau float yang sesuai
                $s->nilai = floatval($nilaiExisting[$id]);
            } else {
                $s->nilai = null;
            }

            return $s;
        })->values();

        return response()->json([
            'success' => true,
            'message' => 'Data siswa berhasil diambil',
            'info_ujian' => [ // Diubah namanya menjadi info_ujian
                'tanggal_ujian' => $nilaiUjian->tanggal_ujian, // Kolom tanggal_ujian
                'kelas' => $kelas->tingkat_kelas . ' ' . $kelas->nama_kelas,
                'mapel' => $nilaiUjian->mapel->nama_mapel ?? 'N/A',
                'tipe_ujian' => $nilaiUjian->tipe_ujian,
                'deskripsi' => $nilaiUjian->deskripsi,
            ],
            'data_siswa' => $dataSiswaWithNilai,
        ]);
    }

    /**
     * Menyimpan nilai siswa yang dikirim dari modal (via API).
     */
    public function saveNilai(Request $request)
    {
        $request->validate([
            // NilaiUjian::id
            'nilai_ujian_id' => 'required|exists:nilai_ujian,id',
            'nilai_siswa' => 'required|array',
            'nilai_siswa.*' => 'nullable|numeric|between:0,100', // Ubah ke numeric karena di DB decimal
        ]);

        $nilaiUjianId = $request->nilai_ujian_id;
        $nilaiSiswaData = $request->nilai_siswa;

        $userUpdate = Auth::id();
        $tglUpdate = Carbon::now();

        DB::beginTransaction();
        try {
            foreach ($nilaiSiswaData as $idSiswa => $nilai) {
                // Konversi ke float karena DB menggunakan decimal(4,1)
                $nilaiFinal = is_null($nilai) || $nilai === '' ? null : (float)$nilai;

                if (is_null($nilaiFinal)) {
                    // Menggunakan MODEL NILAIUJIANSISWA
                    NilaiUjianSiswa::where('nilai_ujian_id', $nilaiUjianId)
                        ->where('id_siswa', $idSiswa)
                        ->delete();
                    continue;
                }

                // Menggunakan MODEL NILAIUJIANSISWA
                NilaiUjianSiswa::updateOrCreate(
                    [
                        'nilai_ujian_id' => $nilaiUjianId,
                        'id_siswa' => $idSiswa
                    ],
                    [
                        'nilai' => $nilaiFinal,
                        'user_entry' => $userUpdate,
                        'tgl_entry' => $tglUpdate,
                        // Tambahkan kolom update jika digunakan
                        'user_update' => $userUpdate,
                        'tgl_update' => $tglUpdate,
                    ]
                );
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Nilai Ujian berhasil disimpan.',
                'nilai_ujian_id' => $nilaiUjianId
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saveNilai Ujian: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan nilai ujian: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mengambil data rekap nilai ujian.
     */
    public function rekap(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json(['success' => false, 'message' => 'Sesi habis. Silakan login ulang.'], 401);
            }

            $guru = Guru::where('users_id', Auth::id())->first();
            if (!$guru) {
                return response()->json(['success' => false, 'message' => 'Data guru tidak ditemukan.'], 403);
            }

            $kelasId    = $request->input('kelas_id');
            $mapelId    = $request->input('mapel_id');
            $tahunAjaran = $request->input('tahun_ajaran');

            if (!$kelasId || !$mapelId || !$tahunAjaran) {
                return response()->json(['success' => false, 'message' => 'Filter tidak lengkap.'], 400);
            }

            $kelas = Kelas::find($kelasId);
            $mapel = Mapel::find($mapelId);

            if (!$kelas) return response()->json(['success' => false, 'message' => 'Kelas tidak valid.'], 404);
            if (!$mapel) return response()->json(['success' => false, 'message' => 'Mapel tidak valid.'], 404);

            $kelasName = trim(($kelas->tingkat_kelas ?? '') . ' ' . ($kelas->nama_kelas ?? ''));
            $mapelName = $mapel->nama_mapel ?? '';

            $tipeUjian = ['PTS', 'PAS']; // Filter tipe Ujian

            // Ambil semua Ujian. Menggunakan MODEL NILAIUJIAN
            $listUjian = NilaiUjian::where('kelas_id', $kelasId)
                ->where('mapel_id', $mapelId)
                ->whereIn('tipe_ujian', $tipeUjian)
                ->where('tahun_ajaran', $tahunAjaran)
                ->orderBy('tanggal_ujian') // Urutkan berdasarkan tanggal_ujian
                ->get();

            $semester = optional($listUjian->first())->semester ?? 1;
            $semesterDisplay = $semester == 1 ? 'Ganjil' : 'Genap';

            // Ambil siswa di kelas dan tahun ajaran tersebut
            $siswa = SiswaKelas::where('kelas_id', $kelasId)
                ->where('tahun_ajaran', $tahunAjaran)
                ->where('status', 1)
                ->with('siswa:id_siswa,nis,nama')
                ->get()
                ->pluck('siswa')
                ->filter()
                ->keyBy('id_siswa');

            $ujianIds = $listUjian->pluck('id')->toArray();
            if (!empty($ujianIds)) {
                // Menggunakan MODEL NILAIUJIANSISWA
                $nilaiSiswa = NilaiUjianSiswa::whereIn('nilai_ujian_id', $ujianIds)
                    ->get()
                    ->groupBy('id_siswa');
            } else {
                $nilaiSiswa = collect();
            }

            $rekap = [];
            foreach ($siswa as $idSiswa => $s) {

                $total = 0;
                $count = 0;
                $nilaiPerUjian = []; // Diubah namanya

                foreach ($listUjian as $ujian) { // Iterasi melalui listUjian
                    $nilaiCollection = $nilaiSiswa->get($idSiswa, collect());
                    // Cari berdasarkan nilai_ujian_id
                    $nilaiObj = $nilaiCollection->firstWhere('nilai_ujian_id', $ujian->id);
                    $nilai = $nilaiObj->nilai ?? null;

                    $nilaiPerUjian[(string) $ujian->id] = $nilai;

                    if ($nilai !== null) {
                        $total += (float) $nilai;
                        $count++;
                    }
                }

                $rekap[] = [
                    'id_siswa' => $idSiswa,
                    'nis'      => $s->nis ?? '',
                    'nama'     => $s->nama ?? '',
                    'nilai_per_ujian' => $nilaiPerUjian, // Diubah namanya
                ];
            }

            // Format tasks untuk JS 
            $ujianTasks = $listUjian->map(function ($u) {
                $tglLabel = 'N/A';
                if (!empty($u->tanggal_ujian)) {
                    try {
                        // Menggunakan tanggal_ujian
                        $tglLabel = Carbon::parse($u->tanggal_ujian)->format('d/m');
                    } catch (\Throwable $ex) {
                        $tglLabel = 'N/A';
                    }
                }
                return [
                    'id' => (string)$u->id,
                    'nama' => $u->tipe_ujian, // Menggunakan tipe_ujian
                    'label' => ($u->tipe_ujian ?? '') . ' (' . $tglLabel . ')',
                ];
            })->values()->toArray();

            return response()->json([
                'success' => true,
                'kelas' => $kelasName,
                'mapel' => $mapelName,
                'tahun_ajaran' => $tahunAjaran,
                'semester' => $semesterDisplay,
                'tasks' => $ujianTasks, // Mengganti tasks menjadi ujianTasks di sini (jika frontend Anda menggunakan tasks)
                'students' => $rekap,
            ]);
        } catch (\Throwable $e) {
            Log::error('Rekap error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Internal error',
                'detail' => $e->getMessage(),
            ], 500);
        }
    }
    
    public function submitFinal($id)
    {
        $ujian = NilaiUjian::findOrFail($id);

        if ($ujian->status === 'Submitted') {
            return response()->json(['success' => false, 'message' => 'Nilai sudah disubmit sebelumnya.']);
        }

        // Validasi kelengkapan nilai
        $totalSiswa = SiswaKelas::where('kelas_id', $ujian->kelas_id)
            ->where('tahun_ajaran', $ujian->tahun_ajaran)
            ->where('status', 1)
            ->count();

        $nilaiTerisi = NilaiUjianSiswa::where('nilai_ujian_id', $ujian->id)
            ->whereNotNull('nilai')
            ->count();

        if ($nilaiTerisi < $totalSiswa) {
            return response()->json(['success' => false, 'message' => 'Masih ada nilai yang belum diisi.']);
        }

        $ujian->update([
            'status' => 'Submitted',
            'user_update' => Auth::id(),
            'tgl_update' => Carbon::now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Nilai ujian berhasil disubmit!']);
    }
}
