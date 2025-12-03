<?php

namespace App\Http\Controllers\Ortu;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SIAKAD\SCHOOL\Siswa;
use App\Models\SIAKAD\SCHOOL\SiswaKelas;
use App\Models\SIAKAD\SCHOOL\JadwalMapel;
use App\Models\SIAKAD\SCHOOL\Kelas; // Menggunakan model Kelas untuk filter
use Carbon\Carbon;

class JadwalController extends Controller
{
    /**
     * Menampilkan jadwal pelajaran mingguan untuk siswa yang dipilih.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // 1. Dapatkan ID siswa yang sedang aktif dari session
        $selectedSiswaId = session('selected_siswa_id');

        // Logic fallback untuk mendapatkan siswa pertama
        if (!$selectedSiswaId) {
            $ortu = $user->ortu;
            if ($ortu && $ortu->siswa()->first()) {
                $selectedSiswaId = $ortu->siswa()->first()->id_siswa;
                session()->put('selected_siswa_id', $selectedSiswaId);
            } else {
                return view('dashboard.ortu.jadwal-mapel')->with('error', 'Tidak ada data siswa yang terkait dengan akun ini.');
            }
        }

        // 2. Ambil data siswa
        $siswa = Siswa::find($selectedSiswaId);
        if (!$siswa) {
            return redirect()->back()->with('error', 'Data siswa tidak ditemukan.');
        }

        // 3. Ambil RIWAYAT KELAS SISWA (untuk membatasi filter Tingkat Kelas)
        $riwayatKelas = SiswaKelas::where('id_siswa', $siswa->id_siswa)
            ->with('kelas')
            ->get();

        // 4. Batasi filter Tingkat Kelas hanya pada tingkat yang pernah/sedang diikuti siswa
        $semuaTingkatKelas = $riwayatKelas
            ->pluck('kelas.tingkat_kelas', 'kelas.tingkat_kelas')
            ->unique()
            ->sort(); // Urutkan tingkat kelas

        // 5. Tentukan Tingkat Kelas yang dipilih
        // Default Tingkat Kelas adalah yang paling tinggi (terakhir) yang pernah diikuti
        $defaultTingkatKelas = $riwayatKelas
            ->sortByDesc('kelas.tingkat_kelas')
            ->first()
            ->kelas->tingkat_kelas ?? null;

        $selectedTingkatKelas = $request->input('tingkat_kelas', $defaultTingkatKelas);

        // 6. Dapatkan Tahun Ajaran dan Semester yang terkait dengan Tingkat Kelas yang dipilih
        $siswaKelas = null;
        $selectedTahunAjaran = null;
        $selectedSemester = null; // Dihilangkan dari filter, tapi masih perlu untuk query

        if ($selectedTingkatKelas) {
            // Cari Kelas ID siswa yang paling baru/aktif untuk Tingkat Kelas yang dipilih
            $siswaKelas = SiswaKelas::where('id_siswa', $siswa->id_siswa)
                ->whereHas('kelas', function ($q) use ($selectedTingkatKelas) {
                    $q->where('tingkat_kelas', $selectedTingkatKelas);
                })
                ->with('kelas')
                // Urutkan berdasarkan Tahun Ajaran dan Semester (asumsi semester Genap lebih baru dari Ganjil di TA yang sama)
                ->join('kelas', 'siswa_kelas.kelas_id', '=', 'kelas.kelas_id')
                ->orderBy('kelas.tahun_ajaran', 'desc')
                ->orderByRaw("FIELD(kelas.semester, 'Genap', 'Ganjil')") // Genap > Ganjil
                ->select('siswa_kelas.*')
                ->first();

            if ($siswaKelas) {
                $selectedTahunAjaran = $siswaKelas->kelas->tahun_ajaran;
                $selectedSemester = $siswaKelas->kelas->semester;
            }
        }

        // Tentukan tanggal awal dan akhir minggu ini (Senin - Jumat)
        $now = Carbon::now(config('app.timezone', 'Asia/Jakarta'));
        $startOfWeek = $now->copy()->startOfWeek(Carbon::MONDAY)->format('Y-m-d');
        $endOfWeek = $now->copy()->endOfWeek(Carbon::FRIDAY)->format('Y-m-d');

        $jadwalPerHari = collect();
        $error = null;

        // 7. Proses Query Jadwal
        if (!$siswaKelas) {
            $error = 'Siswa tidak terdaftar di tingkat kelas yang dipilih atau data tidak ditemukan.';
        } else {
            // Ambil semua jadwal untuk kelas tersebut berdasarkan TANGGAL JADWAL (minggu ini)
            // Filtering Tahun Ajaran dan Semester sudah dilakukan di SiswaKelas (item 6)
            $jadwalQuery = JadwalMapel::where('kelas_id', $siswaKelas->kelas_id)
                ->whereBetween('tanggal_jadwal', [$startOfWeek, $endOfWeek])
                ->with(['penugasan.guru', 'penugasan.mapel', 'ruangan'])
                ->orderBy('tanggal_jadwal')
                ->orderBy('jam_mulai')
                ->get();

            // Kelompokkan berdasarkan nama hari dari tanggal_jadwal
            $jadwalPerHari = $jadwalQuery->groupBy(function ($item) {
                $tanggal = Carbon::parse($item->tanggal_jadwal);
                return $this->getHariIndonesia($tanggal->dayOfWeek);
            });
        }

        // 8. Tentukan hari ini untuk default tab aktif
        $dayOfWeek = Carbon::now(config('app.timezone', 'Asia/Jakarta'))->dayOfWeek; // 0=Minggu, 1=Senin, ..., 6=Sabtu
        $hariIni = $this->getHariIndonesia($dayOfWeek);

        if ($dayOfWeek == Carbon::SATURDAY || $dayOfWeek == Carbon::SUNDAY) {
            $hariIni = 'Senin';
        }

        return view('dashboard.ortu.jadwal-mapel', [
            'jadwalPerHari' => $jadwalPerHari,
            'hariIni' => $hariIni,
            'siswa' => $siswa,
            // Data filter yang relevan
            'semuaTingkatKelas' => $semuaTingkatKelas,
            'selectedTingkatKelas' => $selectedTingkatKelas,
            // Data Kelas yang terpilih (otomatis berdasarkan Tingkat Kelas)
            'selectedTahunAjaran' => $selectedTahunAjaran,
            'selectedSemester' => $selectedSemester,
            'siswaKelas' => $siswaKelas,
            'error' => $error
        ]);
    }

    /**
     * Helper: Konversi angka hari ke nama hari dalam Bahasa Indonesia.
     */
    private function getHariIndonesia($dayNumber)
    {
        // Carbon dayOfWeek: 0=Minggu, 1=Senin, ..., 6=Sabtu
        $hari = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            0 => 'Minggu',
        ];
        return $hari[$dayNumber] ?? 'Senin'; // Default ke Senin jika tidak valid
    }
}
