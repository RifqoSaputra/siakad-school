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
use App\Models\SIAKAD\SCHOOL\NilaiTambahan;
use App\Models\SIAKAD\SCHOOL\NilaiTambahanSiswa;
use App\Models\SIAKAD\SCHOOL\SiswaKelas;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class InputNilaiHarianController extends Controller
{
    /**
     * Menampilkan daftar tugas/penilaian harian berdasarkan filter Guru, Tahun Ajaran, Kelas, dan Mapel.
     */
    public function index(Request $request)
    {
        // 1. Dapatkan ID Guru yang sedang login (Asumsi menggunakan guard 'web' atau 'guru')
        // *Ganti 'id_guru' dengan cara yang benar untuk mendapatkan ID guru dari sesi/user saat ini*
        $user = Auth::user();
        if (!$user || !$user->users_id) {
            // Tangani jika user tidak login atau tidak punya users_id
            return redirect('/login')->with('error', 'Silakan login sebagai guru.');
        }

        // Ambil data guru dari users_id
        $guru = Guru::where('users_id', $user->users_id)->first();

        if (!$guru) {
            return redirect()->back()->with('error', 'Data guru tidak ditemukan.');
        }

        $idGuru = $guru->id_guru;

        // 2. Tentukan Tahun Ajaran dan Semester aktif (Bisa dari settings atau filter request)
        $defaultTahunAjaran = '2024/2025'; // Ganti dengan logika mendapatkan TA aktif
        $defaultSemester = 1; // 1=Ganjil, 2=Genap

        $tahunAjaranFilter = $request->input('tahun_ajaran', $defaultTahunAjaran);
        $semesterFilter = $request->input('semester', $defaultSemester);
        $kelasIdFilter = $request->input('kelas_id');
        $mapelIdFilter = $request->input('mapel_id');

        // --- Mendapatkan Data Filter Awal (Kelas dan Mapel yang Diampu oleh Guru) ---

        // Semua penugasan mapel untuk guru ini di TA & Semester aktif
        $penugasanMapel = GuruMapel::where('id_guru', $idGuru)
            ->where('tahun_ajaran', $tahunAjaranFilter)
            ->where('semester', $semesterFilter)
            ->with(['mapel']) // Eager load Mapel
            ->get();

        // Daftar Mapel unik yang diampu
        $listMapel = $penugasanMapel->pluck('mapel')->unique('mapel_id');

        // Ambil semua ID GuruMapel yang dimiliki guru
        $guruMapelIds = $penugasanMapel->pluck('guru_mapel_id');

        // Dapatkan daftar Kelas yang diampu dari tabel jadwal_mapel
        $kelasYangTersedia = \App\Models\SIAKAD\SCHOOL\JadwalMapel::whereIn('guru_mapel_id', $guruMapelIds)
            ->distinct('kelas_id')
            ->with(['kelas']) // Eager load Kelas
            ->get()
            ->pluck('kelas')
            ->filter() // Hapus entri null jika relasi kelas_id tidak ditemukan
            ->unique('kelas_id')
            ->sortBy('nama_kelas') // Urutkan berdasarkan nama kelas
            ->map(function ($kelas) {
                // PERBAIKAN: Gunakan trim() dan Null Coalescing untuk keamanan data
                $kelas->nama_kelas_lengkap = trim(($kelas->tingkat_kelas ?? '') . ' ' . ($kelas->nama_kelas ?? ''));
                return $kelas;
            });        // Tentukan nilai filter saat ini
        $currentMapelId = $mapelIdFilter ?? ($listMapel->first()->mapel_id ?? null);
        $currentKelasId = $kelasIdFilter ?? ($kelasYangTersedia->first()->kelas_id ?? null);

        // Untuk menampilkan nama di blade
        $currentMapel = $currentMapelId ? Mapel::find($currentMapelId)?->nama_mapel : null;
        $kelasObj = $currentKelasId ? $kelasYangTersedia->firstWhere('kelas_id', $currentKelasId) : null;
        $currentKelas = $kelasObj ? $kelasObj->nama_kelas_lengkap : null;

        $guruMapelIdTarget = null;
        if ($currentMapelId && $currentKelasId) {
            $guruMapelTarget = GuruMapel::where('id_guru', $idGuru)
                ->where('mapel_id', $currentMapelId)
                ->first(); // Ambil salah satu jika ada banyak penugasan untuk mapel yang sama
            $guruMapelIdTarget = $guruMapelTarget ? $guruMapelTarget->guru_mapel_id : null;
        }

        // --- 3. Ambil Data Tugas Harian (`NilaiTambahan`) Sesuai Filter ---
        $listTugas = collect();
        if ($currentMapelId && $currentKelasId) {
            $listTugas = NilaiTambahan::with('kelas', 'mapel', 'nilaiSiswa')
                ->where('kelas_id', $currentKelasId)
                ->where('mapel_id', $currentMapelId)
                // Filter tambahan jika perlu, e.g., berdasarkan tipe atau tanggal
                ->orderBy('tgl_entry', 'desc')
                ->get();

            // Tambahkan status pengisian nilai
            $listTugas = $listTugas->map(function ($tugas) use ($currentKelasId) {

                // Hitung jumlah siswa pada kelas dan tahun ajaran tugas
                $totalSiswa = SiswaKelas::where('kelas_id', $currentKelasId)
                    ->where('tahun_ajaran', $tugas->tahun_ajaran)
                    ->pluck('id_siswa'); // ambil daftar ID siswa

                $jumlahSiswa = $totalSiswa->count();

                // Hitung nilai yang telah diisi berdasarkan siswa kelas tersebut
                $siswaSudahDiisi = NilaiTambahanSiswa::where('nilai_tambahan_id', $tugas->id)
                    ->whereIn('id_siswa', $totalSiswa) // memastikan hanya siswa di kelas itu
                    ->count();

                $tugas->totalSiswa = $jumlahSiswa;
                $tugas->siswaSudahDiisi = $siswaSudahDiisi;
                $tugas->isSubmitted = $tugas->status === 'Submitted';

                $tugas->statusPengisian = ($jumlahSiswa > 0 && $siswaSudahDiisi == $jumlahSiswa)
                    ? 'Selesai'
                    : 'Belum Diisi';

                return $tugas;
            });
        }

        foreach ($listTugas as $tugas) {

            // ambil total siswa dari siswa_kelas
            $totalSiswa = \App\Models\SIAKAD\SCHOOL\SiswaKelas::where('kelas_id', $tugas->kelas_id)
                ->where('status', 1)
                ->count();

            // hitung nilai yang masuk dari tabel nilai_tambahan_siswa
            $nilaiMasuk = \App\Models\SIAKAD\SCHOOL\NilaiTambahanSiswa::where('nilai_tambahan_id', $tugas->id)
                ->whereNotNull('nilai')
                ->count();

            if ($totalSiswa == 0) {
                $tugas->statusPengisian = 'Belum Diisi';
            } elseif ($nilaiMasuk == 0) {
                $tugas->statusPengisian = 'Belum Diisi';
            } elseif ($nilaiMasuk < $totalSiswa) {
                $tugas->statusPengisian = 'Sebagian';
            } else {
                $tugas->statusPengisian = 'Selesai';
            }
        }


        // Kirim data ke view
        return view('dashboard.guru.input-nilai-harian.index', compact(
            'tahunAjaranFilter',
            'semesterFilter',
            'currentKelasId',
            'currentMapelId',
            'currentMapel',
            'currentKelas',
            'listMapel',
            'kelasYangTersedia', // Kirim daftar kelas yang pernah diajar/ditugaskan
            'listTugas',
            'guruMapelIdTarget'
        ));
    }

    /**
     * Menampilkan form input nilai untuk tugas/penilaian tertentu.
     */
    public function getSiswaForInput(NilaiTambahan $nilaiTambahan)
    {
        $kelas = Kelas::where('kelas_id', $nilaiTambahan->kelas_id)->first();

        if (!$kelas) {
            return response()->json([
                'success' => false,
                'message' => 'Kelas tidak ditemukan'
            ], 404);
        }

        // Ambil siswa (SiswaKelas -> relasi siswa)
        $listSiswa = SiswaKelas::where('kelas_id', $kelas->kelas_id)
            ->where('tahun_ajaran', request('tahun_ajaran') ?? $kelas->tahun_ajaran)
            ->where('status', 1)
            ->with('siswa:id_siswa,nis,nama')
            ->get()
            ->pluck('siswa')
            ->filter()
            ->values();

        // Ambil nilai existing: [id_siswa => nilai]
        $nilaiExisting = NilaiTambahanSiswa::where('nilai_tambahan_id', $nilaiTambahan->id)
            ->pluck('nilai', 'id_siswa')
            ->toArray();

        // Lampirkan nilai ke masing2 objek siswa (pastikan key sesuai id_siswa)
        $dataSiswaWithNilai = $listSiswa->map(function ($s) use ($nilaiExisting) {
            $id = $s->id_siswa ?? $s->id ?? null;

            if (isset($nilaiExisting[$id])) {
                // pastikan nilai yang keluar ke frontend berupa integer 0–100
                $s->nilai = intval($nilaiExisting[$id]);
            } else {
                $s->nilai = null;
            }

            return $s;
        })->values();

        return response()->json([
            'success' => true,
            'message' => 'Data siswa berhasil diambil',
            'info_tugas' => [
                'tgl_dibuat' => $nilaiTambahan->tgl_entry,
                'kelas' => $kelas->tingkat_kelas . ' ' . $kelas->nama_kelas,
                'mapel' => $nilaiTambahan->mapel->nama_mapel,
                'tipe_penunjang' => $nilaiTambahan->tipe_penunjang,
                'deskripsi' => $nilaiTambahan->deskripsi ?? null,
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
            'nilai_tambahan_id' => 'required|exists:nilai_tambahan,id',
            // nilai_siswa harus berupa array asosiatif (id_siswa => nilai)
            'nilai_siswa' => 'required|array',
            'nilai_siswa.*' => 'nullable|integer|between:0,100',
        ]);

        $nilaiTambahanId = $request->nilai_tambahan_id;
        $nilaiSiswaData = $request->nilai_siswa; // [id_siswa => nilai]

        $nilaiTambahan = NilaiTambahan::findOrFail($nilaiTambahanId);
        if ($nilaiTambahan->status === 'Submitted') {
            return response()->json([
                'success' => false,
                'message' => 'Nilai ini sudah dikunci (Submitted) dan tidak dapat diubah.'
            ], 403);
        }

        $userUpdate = Auth::id();
        $tglUpdate = Carbon::now();
        $dataToInsertOrUpdate = [];

        DB::beginTransaction();
        try {
            foreach ($nilaiSiswaData as $idSiswa => $nilai) {
                // Konversi nilai string kosong ke null, dan pastikan nilai valid
                $nilaiFinal = is_null($nilai) || $nilai === '' ? null : (int)$nilai;

                // Jika nilai null (dikosongkan), hapus/skip record. Kita hanya simpan nilai > 0
                if (is_null($nilaiFinal)) {
                    // Hapus record nilai yang ada jika nilai di-set kosong
                    NilaiTambahanSiswa::where('nilai_tambahan_id', $nilaiTambahanId)
                        ->where('id_siswa', $idSiswa)
                        ->delete();
                    continue; // Lanjut ke siswa berikutnya
                }

                // Insert atau Update nilai
                NilaiTambahanSiswa::updateOrCreate(
                    [
                        'nilai_tambahan_id' => $nilaiTambahanId,
                        'id_siswa' => $idSiswa
                    ],
                    [
                        'nilai' => $nilaiFinal,
                        'user_entry' => $userUpdate, // Asumsi ini diisi untuk semua operasi CRUD
                        'tgl_entry' => $tglUpdate,
                        // Jika `tgl_update` dan `user_update` ada di skema:
                        // 'user_update' => $userUpdate,
                        // 'tgl_update' => $tglUpdate,
                    ]
                );
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Nilai berhasil disimpan.',
                'nilai_tambahan_id' => $nilaiTambahanId
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saveNilai: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan nilai: ' . $e->getMessage()], 500);
        }
    }
    /**
     * Menyimpan tugas/penilaian baru ke tabel `nilai_tambahan`.
     */
    public function store(Request $request)
    {
        $request->validate([
            'guru_mapel_id' => 'required|exists:guru_mapel,guru_mapel_id',
            'kelas_id' => 'required|exists:kelas,kelas_id',
            'mapel_id' => 'required|exists:mapel,mapel_id',
            'tipe_penunjang' => 'required|string|max:50',
            'deskripsi' => 'nullable|string',
            // Asumsi TA diambil dari GuruMapel atau input tersembunyi
        ]);

        $guruMapel = GuruMapel::findOrFail($request->guru_mapel_id);

        // Dapatkan data dari GuruMapel untuk Audit Trail dan TA/Semester
        $guruMapel = GuruMapel::find($request->guru_mapel_id);

        // Asumsi user_entry diambil dari user yang login
        $userEntry = Auth::id(); // Ambil ID user dari tabel users

        DB::beginTransaction();
        try {
            NilaiTambahan::create([
                'guru_mapel_id' => $request->guru_mapel_id,
                'kelas_id'      => $request->kelas_id,
                'mapel_id'      => $request->mapel_id,
                'tipe_penunjang' => $request->tipe_penunjang,
                'deskripsi'     => $request->deskripsi,

                'semester'      => $guruMapel->semester,
                'tahun_ajaran'  => $guruMapel->tahun_ajaran,
                'status'        => 'Draft',

                'user_entry'    => Auth::id(),
                'tgl_entry'     => now(),
            ]);
            DB::commit();
            return redirect()
                ->route('nilai.harian.index', [
                    'kelas_id' => $request->kelas_id,
                    'mapel_id' => $request->mapel_id,
                    'tahun_ajaran' => $guruMapel->tahun_ajaran,
                    'semester' => $guruMapel->semester
                ])
                ->with('success', 'Tugas/Penilaian harian berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambahkan tugas: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus tugas/penilaian dari tabel `nilai_tambahan`.
     */
    public function destroy(NilaiTambahan $nilaiTambahan)
    {
        // 1. Validasi Akses Guru
        $user = Auth::user();
        $guru = Guru::where('users_id', Auth::id())->first();

        if (!$guru) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        if ($nilaiTambahan->status === 'Submitted') {
            return response()->json([
                'success' => false,
                'message' => 'Tugas ini sudah dikunci (Submitted) dan tidak dapat dihapus.'
            ], 403);
        }
        if ($nilaiTambahan->status === 'Submitted') {
            return response()->json([
                'success' => false,
                'message' => 'Tugas ini sudah dikunci (Submitted) dan tidak dapat dihapus.'
            ], 403);
        }

        // 2. Pastikan Guru yang menghapus adalah Guru yang membuat atau mengampu Mapel/Kelas ini
        $penugasanMapel = GuruMapel::where('id_guru', $guru->id_guru)
            ->where('mapel_id', $nilaiTambahan->mapel_id)
            ->first();

        if (!$penugasanMapel) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak: Anda tidak memiliki izin untuk menghapus tugas ini.'], 403);
        }

        DB::beginTransaction();
        try {
            // Karena relasi sudah di-set 'onDelete('cascade')' di migrasi, 
            // `nilai_tambahan_siswa` akan otomatis terhapus
            $nilaiTambahan->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Tugas berhasil dihapus.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal menghapus tugas: ' . $e->getMessage()], 500);
        }
    }

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

            // Validasi parameter wajib
            $kelasId  = $request->input('kelas_id');
            $mapelId  = $request->input('mapel_id');
            $tahunAjaran = $request->input('tahun_ajaran');

            if (!$kelasId || !$mapelId || !$tahunAjaran) {
                return response()->json(['success' => false, 'message' => 'Filter tidak lengkap.'], 400);
            }

            // Validasi data dasar
            $kelas = Kelas::find($kelasId);
            $mapel = Mapel::find($mapelId);

            if (!$kelas) return response()->json(['success' => false, 'message' => 'Kelas tidak valid.'], 404);
            if (!$mapel) return response()->json(['success' => false, 'message' => 'Mapel tidak valid.'], 404);

            $kelasName = ($kelas->tingkat_kelas ?? '') . ' ' . ($kelas->nama_kelas ?? '');
            $mapelName = $mapel->nama_mapel ?? '';

            // Ambil semua tugas berdasarkan TA
            $listTugas = NilaiTambahan::where('kelas_id', $kelasId)
                ->where('mapel_id', $mapelId)
                ->whereHas('guruMapel', function ($q) use ($tahunAjaran) {
                    $q->where('tahun_ajaran', $tahunAjaran);
                })
                ->orderBy('tgl_entry')
                ->get();

            // Tentukan semester (aman jika listTugas kosong)
            $semester = optional($listTugas->first())->semester ?? 1;
            $semesterDisplay = $semester == 1 ? 'Ganjil' : 'Genap';

            // Ambil siswa di kelas dan tahun ajaran tersebut (safe)
            $siswa = SiswaKelas::where('kelas_id', $kelasId)
                ->where('tahun_ajaran', $tahunAjaran)
                ->with('siswa:id_siswa,nis,nama')
                ->get()
                ->pluck('siswa')
                ->filter()
                ->keyBy('id_siswa');

            // Ambil nilai untuk semua tugas — jika tidak ada tugas, kosongkan koleksi
            $taskIds = $listTugas->pluck('id')->toArray();
            if (!empty($taskIds)) {
                $nilaiSiswa = NilaiTambahanSiswa::whereIn('nilai_tambahan_id', $taskIds)
                    ->get()
                    ->groupBy('id_siswa');
            } else {
                $nilaiSiswa = collect(); // kosong
            }

            $rekap = [];
            foreach ($siswa as $idSiswa => $s) {

                $total = 0;
                $count = 0;
                $nilaiPerTugas = [];

                foreach ($listTugas as $tugas) {
                    // Ambil collection nilai untuk siswa ini, default collect() agar tidak null
                    $nilaiCollection = $nilaiSiswa->get($idSiswa, collect());

                    // Cari nilai untuk tugas ini (firstWhere lebih rapi)
                    $nilaiObj = $nilaiCollection->firstWhere('nilai_tambahan_id', $tugas->id);

                    $nilai = $nilaiObj->nilai ?? null;

                    // simpan dengan key string supaya konsisten saat dikirim ke JS
                    $nilaiPerTugas[(string) $tugas->id] = $nilai;

                    if ($nilai !== null) {
                        // Pastikan numeric cast aman
                        $total += (float) $nilai;
                        $count++;
                    }
                }

                $rekap[] = [
                    'id_siswa' => $idSiswa,
                    'nis'      => $s->nis ?? '',
                    'nama'     => $s->nama ?? '',
                    'nilai_per_tugas' => $nilaiPerTugas,
                    'rata_rata' => $count ? round($total / $count, 2) : 0,
                ];
            }

            // Format tasks untuk JS — lindungi tgl_entry agar tidak error saat null
            $tasks = $listTugas->map(function ($t) {
                $tglLabel = 'N/A';

                if (!empty($t->tgl_entry)) {
                    try {
                        $tglLabel = Carbon::parse($t->tgl_entry)->format('d/m');
                    } catch (\Throwable $ex) {
                        $tglLabel = 'N/A';
                    }
                }

                return [
                    'id' => (string)$t->id,
                    'nama' => $t->tipe_penunjang,
                    'label' => ($t->tipe_penunjang ?? '') . ' (' . $tglLabel . ')',
                ];
            })->values()->toArray();

            foreach ($listTugas as $tugas) {
                $totalSiswa = SiswaKelas::where('kelas_id', $kelasId)
                    ->where('tahun_ajaran', $tahunAjaran)
                    ->count();

                $nilaiMasuk = NilaiTambahanSiswa::where('nilai_tambahan_id', $tugas->id)
                    ->whereNotNull('nilai')
                    ->count();

                if ($totalSiswa == 0 || $nilaiMasuk == 0) {
                    $tugas->statusPengisian = 'Belum Diisi';
                } elseif ($nilaiMasuk < $totalSiswa) {
                    $tugas->statusPengisian = 'Sebagian';
                } else {
                    $tugas->statusPengisian = 'Selesai';
                }
            }

            return response()->json([
                'success' => true,
                'kelas' => $kelasName,
                'mapel' => $mapelName,
                'tahun_ajaran' => $tahunAjaran,
                'semester' => $semesterDisplay,
                'tasks' => $tasks,
                'students' => $rekap,
            ]);
        } catch (\Throwable $e) {
            // Berikan detail error untuk debugging (hapus detail ini di production)
            Log::error('Rekap error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Internal error',
                'detail' => $e->getMessage(),
            ], 500);
        }
    }

    public function submitNilaiHarian(Request $request)
    {
        $request->validate([
            'kelas_id'      => 'required|exists:kelas,kelas_id',
            'mapel_id'      => 'required|exists:mapel,mapel_id',
            'tahun_ajaran'  => 'required|string',
        ]);

        $kelasId      = $request->kelas_id;
        $mapelId      = $request->mapel_id;
        $tahunAjaran  = $request->tahun_ajaran;

        /** 1. Validasi guru pengampu */
        $guru = Guru::where('users_id', Auth::id())->first();
        if (!$guru) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $penugasan = GuruMapel::where('id_guru', $guru->id_guru)
            ->where('mapel_id', $mapelId)
            ->where('tahun_ajaran', $tahunAjaran)
            ->first();

        if (!$penugasan) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki penugasan untuk mapel ini.'], 403);
        }

        /** 2. Ambil siswa aktif */
        $listIdSiswa = SiswaKelas::where('kelas_id', $kelasId)
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('status', 1)
            ->pluck('id_siswa');

        $totalSiswa = $listIdSiswa->count();

        /** Jika tidak ada siswa → tetap submit (tidak ada yang perlu diisi) */
        if ($totalSiswa > 0) {

            /** 3. Ambil semua tugas yang masih Draft */
            $draftTugas = NilaiTambahan::where('kelas_id', $kelasId)
                ->where('mapel_id', $mapelId)
                ->where('tahun_ajaran', $tahunAjaran)
                ->where('status', 'Draft')
                ->get();

            /** 4. Validasi kelengkapan nilai tiap tugas */
            $tugasBelumSelesai = [];
            foreach ($draftTugas as $tugas) {
                $jumlahTerisi = NilaiTambahanSiswa::where('nilai_tambahan_id', $tugas->id)
                    ->whereIn('id_siswa', $listIdSiswa)
                    ->whereNotNull('nilai')
                    ->count();

                if ($jumlahTerisi < $totalSiswa) {
                    $tugasBelumSelesai[] = [
                        'tipe'   => $tugas->tipe_penunjang,
                        'terisi' => $jumlahTerisi,
                        'total'  => $totalSiswa,
                    ];
                }
            }

            /** 5. Jika ada tugas yang belum selesai → Kembalikan error detail */
            if (!empty($tugasBelumSelesai)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengunci nilai. Ada tugas yang belum diisi lengkap.',
                    'detail_tugas' => $tugasBelumSelesai
                ], 400);
            }
        }

        /** 6. Update semua status menjadi Submitted */
        DB::beginTransaction();
        try {
            $affected = NilaiTambahan::where('kelas_id', $kelasId)
                ->where('mapel_id', $mapelId)
                ->where('tahun_ajaran', $tahunAjaran)
                ->where('status', 'Draft')
                ->update([
                    'status'      => 'Submitted',
                    'user_update' => Auth::id(),
                    'tgl_update'  => Carbon::now(),
                ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $affected > 0
                    ? "Berhasil mengunci $affected tugas harian."
                    : 'Semua nilai sudah dalam status Submitted.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Submit Error Nilai Harian: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal submit nilai.'], 500);
        }
    }
}
