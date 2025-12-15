<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Import Models yang dibutuhkan
use App\Models\SIAKAD\SCHOOL\Kelas;
use App\Models\SIAKAD\SCHOOL\SiswaKelas;
use App\Models\SIAKAD\SCHOOL\CatatanRaporSemester;

class CatatanSiswaController extends Controller
{
    /**
     * Menampilkan halaman input catatan rapor wali kelas.
     */
    public function index()
    {
        // 1. Dapatkan Wali Kelas ID dari user yang sedang login
        $idGuru = Auth::user()->guru->id_guru;
        $idGuru = (int) $idGuru;

        // 2. Cari kelas yang diwalikan oleh guru ini.
        $kelas = Kelas::where('walikelas', $idGuru)
            ->where('status', 1) // Asumsi: status 1 = kelas aktif
            ->first();

        if (!$kelas) {
            return redirect()->back()->with('error', 'Anda tidak terdaftar sebagai Wali Kelas pada kelas aktif.');
        }

        $kelasId = $kelas->kelas_id;
        $semester = $kelas->semester;
        $tingkatKelas = $kelas->tingkat_kelas;
        $tahunAjaran = $kelas->tahun_ajaran;

        // 3. Ambil Status Publikasi Rapor untuk kelas ini
        $statusPublikasi = CatatanRaporSemester::getStatusPublikasi($kelasId, $semester, $tahunAjaran);

        // 4. Ambil Daftar Siswa dan Catatan Rapor mereka
        $dataSiswaRapor = SiswaKelas::join('siswa', 'siswa_kelas.id_siswa', '=', 'siswa.id_siswa')
            ->leftJoin('catatan_rapor_semester', function ($join) use ($kelasId, $semester, $tahunAjaran) {
                $join->on('siswa.id_siswa', '=', 'catatan_rapor_semester.id_siswa')
                    ->where('catatan_rapor_semester.kelas_id', $kelasId)
                    ->where('catatan_rapor_semester.semester', $semester)
                    ->where('catatan_rapor_semester.tahun_ajaran', $tahunAjaran);
            })
            ->where('siswa_kelas.kelas_id', $kelasId)
            ->where('siswa_kelas.status', 1)
            ->where('siswa_kelas.tahun_ajaran', $tahunAjaran)
            ->orderBy('siswa.nama')
            ->select(
                'siswa.id_siswa',
                'siswa.nis',
                'siswa.nama',
                'catatan_rapor_semester.predikat_sikap',
                'catatan_rapor_semester.catatan_walikelas',
                'catatan_rapor_semester.status_kenaikan',
                'catatan_rapor_semester.tgl_update as waktu_terakhir_simpan_siswa'
            )
            ->get();

        // 5. Ambil waktu terakhir simpan keseluruhan
        $waktuTerakhirSimpan = $dataSiswaRapor->max('waktu_terakhir_simpan_siswa');

        // 6. Tentukan opsi Status Kenaikan yang valid
        $opsiKenaikan = $this->getOpsiStatusKenaikan($tingkatKelas, $semester);

        return view('dashboard.guru.catatan-rapor-siswa', compact(
            'kelas',
            'dataSiswaRapor',
            'waktuTerakhirSimpan',
            'statusPublikasi',
            'opsiKenaikan' // Pass opsi kenaikan ke view
        ));
    }

    /**
     * Menyimpan data catatan rapor siswa.
     */
    public function store(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'rapor' => 'required|array',
            'rapor.*.id_siswa' => 'required|integer',
            'rapor.*.predikat_sikap' => 'nullable|string|max:50',
            'rapor.*.catatan_walikelas' => 'nullable|string|max:500',
            'rapor.*.status_kenaikan' => 'nullable|string|in:Belum Final,Naik Kelas,Tinggal Kelas,Lulus,Tidak Lulus',
        ]);

        // 2. Ambil data kelas dan user
        $idGuru = Auth::user()->guru->id_guru;
        $idGuru = (int) $idGuru;
        $kelas = Kelas::where('walikelas', $idGuru)->where('status', 1)->firstOrFail();
        $userUpdate = Auth::user()->id;
        $now = Carbon::now();

        // 3. Periksa status publikasi (Hanya bisa disimpan jika 'Draft')
        $statusPublikasi = CatatanRaporSemester::getStatusPublikasi($kelas->kelas_id, $kelas->semester, $kelas->tahun_ajaran);
        if ($statusPublikasi != 'Draft') {
            return redirect()->back()->with('error', 'Penyimpanan gagal. Data rapor sudah berstatus: ' . $statusPublikasi);
        }

        DB::beginTransaction();
        try {
            foreach ($request->rapor as $data) {
                // Tentukan nilai default Status Kenaikan jika kosong
                $statusKenaikan = $data['status_kenaikan'] ?? 'Belum Final';

                CatatanRaporSemester::updateOrCreate(
                    [
                        'id_siswa' => $data['id_siswa'],
                        'kelas_id' => $kelas->kelas_id,
                        'semester' => $kelas->semester,
                        'tahun_ajaran' => $kelas->tahun_ajaran,
                    ],
                    [
                        'predikat_sikap' => $data['predikat_sikap'],
                        'catatan_walikelas' => $data['catatan_walikelas'],
                        'status_kenaikan' => $statusKenaikan,
                        'status_publikasi' => 'Draft', // Pertahankan status draft saat menyimpan
                        'user_update' => $userUpdate,
                        'tgl_update' => $now,
                        // Jika baru dibuat, tgl_entry dan user_entry juga akan diisi
                        'user_entry' => CatatanRaporSemester::where('id_siswa', $data['id_siswa'])->value('user_entry') ?? $userUpdate,
                        'tgl_entry' => CatatanRaporSemester::where('id_siswa', $data['id_siswa'])->value('tgl_entry') ?? $now,
                    ]
                );
            }

            DB::commit();
            return redirect()->back()->with('success', 'Catatan Rapor berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Gagal menyimpan catatan rapor. Pesan: ' . $e->getMessage());
        }
    }

    /**
     * Mengunci Rapor (Mengubah status publikasi menjadi 'Terkunci').
     */
    public function lockFinal(Request $request)
    {
        // Ambil guru ID dari relasi
        $idGuru = Auth::user()->guru->id_guru;

        $kelas = Kelas::where('walikelas', $idGuru)
            ->where('status', 1)
            ->first();

        if (!$kelas) {
            return response()->json(['status' => 'error', 'message' => 'Kelas tidak ditemukan atau Anda bukan wali kelas aktif.'], 404);
        }

        $kelasId = $kelas->kelas_id;
        $semester = strtolower($kelas->semester);
        $tahunAjaran = $kelas->tahun_ajaran;
        $now = Carbon::now();

        // ✔ KHUSUS SEMESTER GENAP → cek status kenaikan
        if ($semester === 'genap') {

            $incompleteCount = CatatanRaporSemester::where('kelas_id', $kelasId)
                ->where('semester', $kelas->semester)
                ->where('tahun_ajaran', $tahunAjaran)
                ->where('status_kenaikan', 'Belum Final')
                ->count();

            if ($incompleteCount > 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Masih ada {$incompleteCount} siswa dengan Status Kenaikan 'Belum Final'. Harap lengkapi dulu."
                ], 422);
            }
        }

        // ✔ Update status publikasi menjadi terkunci
        CatatanRaporSemester::where('kelas_id', $kelasId)
            ->where('semester', $kelas->semester)
            ->where('tahun_ajaran', $tahunAjaran)
            ->update([
                'status_publikasi' => 'Terkunci',
                'user_update' => Auth::user()->id,
                'tgl_update' => $now,
            ]);

        return response()->json([
            'status' => 'success',
            'message' => "Catatan rapor kelas {$kelas->nama_kelas} berhasil dikunci!"
        ], 200);
    }

    /**
     * Logika untuk menentukan opsi status kenaikan berdasarkan tingkatan kelas dan semester.
     *
     * @param string $tingkatKelas
     * @param string $semester
     * @return array
     */
    protected function getOpsiStatusKenaikan($tingkatKelas, $semester)
    {
        // Selalu sertakan Belum Final sebagai default/placeholder
        $baseOpsi = ['Belum Final'];

        // Untuk semester Ganjil, status kenaikan tidak relevan
        if (strtolower($semester) === 'ganjil') {
            return $baseOpsi;
        }

        // Untuk semester Genap:
        if (in_array($tingkatKelas, ['10', '11'])) {
            // Kelas 10 atau 11: Pilihan Naik/Tinggal Kelas
            return array_merge($baseOpsi, ['Naik Kelas', 'Tinggal Kelas']);
        } elseif ($tingkatKelas == '12') {
            // Kelas 12: Pilihan Lulus/Tidak Lulus
            return array_merge($baseOpsi, ['Lulus', 'Tidak Lulus']);
        }

        return $baseOpsi; // Default jika tingkat/semester tidak sesuai
    }
}
