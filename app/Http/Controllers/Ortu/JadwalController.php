<?php

namespace App\Http\Controllers\Ortu;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SIAKAD\SCHOOL\Siswa;
use App\Models\SIAKAD\SCHOOL\SiswaKelas;
use App\Models\SIAKAD\SCHOOL\JadwalMapel;
use App\Models\SIAKAD\SCHOOL\Kelas;
use Carbon\Carbon;

class JadwalController extends Controller
{
    /**
     * Helper: Mendapatkan tanggal Senin s.d Jumat untuk minggu ini.
     * @return array ['Senin' => 'YYYY-MM-DD', ...]
     */
    private function getTanggalMingguan()
    {
        $now = Carbon::now(config('app.timezone', 'Asia/Jakarta'));
        $startOfWeek = $now->copy()->startOfWeek(Carbon::MONDAY);
        $days = [];
        // PERBAIKAN: Mendapatkan tanggal untuk Senin (1) sampai Minggu (0)
        // Kita loop 7 hari penuh dari Senin.
        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $dayNumber = $date->dayOfWeek; // 1=Senin, 0=Minggu

            // Panggil getHariIndonesia dengan flag $fullDay=true untuk mendapatkan nama hari lengkap
            $days[$this->getHariIndonesia($dayNumber, true)] = $date->format('Y-m-d');
        }
        return $days;
    }

    /**
     * Menampilkan jadwal pelajaran mingguan untuk siswa yang dipilih.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $selectedSiswaId = session('selected_siswa_id');

        if (!$selectedSiswaId) {
            $ortu = $user->ortu;
            if ($ortu && $ortu->siswa()->first()) {
                $selectedSiswaId = $ortu->siswa()->first()->id_siswa;
                session()->put('selected_siswa_id', $selectedSiswaId);
            } else {
                return view('dashboard.ortu.jadwal-mapel')->with('error', 'Tidak ada data siswa yang terkait dengan akun ini.');
            }
        }

        $siswa = Siswa::find($selectedSiswaId);
        if (!$siswa) {
            return redirect()->back()->with('error', 'Data siswa tidak ditemukan.');
        }

        // ... (Logika penentuan Kelas, Tahun Ajaran, dan Semester tetap sama) ...
        $riwayatKelas = SiswaKelas::where('id_siswa', $siswa->id_siswa)
            ->with('kelas')
            ->get();

        $semuaTingkatKelas = $riwayatKelas
            ->pluck('kelas.tingkat_kelas', 'kelas.tingkat_kelas')
            ->unique()
            ->sort();

        $defaultTingkatKelas = $riwayatKelas
            ->sortByDesc('kelas.tingkat_kelas')
            ->first()
            ->kelas->tingkat_kelas ?? null;

        $selectedTingkatKelas = $request->input('tingkat_kelas', $defaultTingkatKelas);

        $siswaKelas = null;
        $selectedTahunAjaran = null;
        $selectedSemester = null;

        if ($selectedTingkatKelas) {
            $siswaKelas = SiswaKelas::where('id_siswa', $siswa->id_siswa)
                ->whereHas('kelas', function ($q) use ($selectedTingkatKelas) {
                    $q->where('tingkat_kelas', $selectedTingkatKelas);
                })
                ->with('kelas')
                ->join('kelas', 'siswa_kelas.kelas_id', '=', 'kelas.kelas_id')
                ->orderBy('kelas.tahun_ajaran', 'desc')
                ->orderByRaw("FIELD(kelas.semester, 'Genap', 'Ganjil')")
                ->select('siswa_kelas.*')
                ->first();

            if ($siswaKelas) {
                $selectedTahunAjaran = $siswaKelas->kelas->tahun_ajaran;
                $selectedSemester = $siswaKelas->kelas->semester;
            }
        }

        $tanggalMingguan = $this->getTanggalMingguan();
        $error = null;
        $jadwalPerHari = collect();

        // 7. Proses Query Jadwal (Mengambil TEMPLATE Mingguan)
        if (!$siswaKelas || !$selectedTahunAjaran) {
            $error = 'Siswa tidak terdaftar di tingkat kelas yang dipilih atau data tidak ditemukan.';
            $jadwalPerHari = collect();
        } else {
            // PERBAIKAN: Gunakan format KAPITAL SEMUA ('SENIN', 'SELASA') untuk filter WHERE
            $hariFilter = ['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT']; // <--- UBAH KE KAPITAL SEMUA

            $jadwalQuery = JadwalMapel::where('kelas_id', $siswaKelas->kelas_id)
                ->where('tahun_ajaran', $selectedTahunAjaran)
                ->whereIn('hari', $hariFilter)
                ->with(['penugasan.guru', 'penugasan.mapel', 'ruangan'])
                ->orderByRaw("FIELD(hari, 'SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT')") // Urutkan KAPITAL SEMUA
                ->orderBy('jam_mulai')
                ->get();

            // Kelompokkan berdasarkan kolom 'hari' (Contoh: 'SENIN'). Key hasil grouping adalah KAPITAL SEMUA.
            $jadwalPerHari = $jadwalQuery->groupBy('hari')->map(function ($jadwalHarian) {
                return $jadwalHarian->values();
            });
        }

        // 8. Tentukan hari ini untuk default tab aktif
        $dayOfWeek = Carbon::now(config('app.timezone', 'Asia/Jakarta'))->dayOfWeek;
        // KEMBALIKAN HARI INI DALAM FORMAT KAPITAL SEMUA
        $hariIni = $this->getHariIndonesia($dayOfWeek); // <-- Hasilnya sekarang 'SENIN'

        if ($dayOfWeek == Carbon::SATURDAY || $dayOfWeek == Carbon::SUNDAY) {
            $hariIni = 'SENIN'; // Default ke SENIN jika weekend
        }

        return view('dashboard.ortu.jadwal-mapel', [
            'jadwalPerHari' => $jadwalPerHari,
            'hariIni' => $hariIni,
            'siswa' => $siswa,
            'semuaTingkatKelas' => $semuaTingkatKelas,
            'selectedTingkatKelas' => $selectedTingkatKelas,
            'selectedTahunAjaran' => $selectedTahunAjaran,
            'selectedSemester' => $selectedSemester,
            'siswaKelas' => $siswaKelas,
            'error' => $error,
            'tanggalMingguan' => $tanggalMingguan,
        ]);
    }

    /**
     * Helper: Konversi angka hari ke nama hari dalam Bahasa Indonesia.
     */
    private function getHariIndonesia($dayNumber, $fullDay = false)
    {
        // Carbon dayOfWeek: 0=Minggu, 1=Senin, ..., 6=Sabtu
        $hari = [
            1 => 'SENIN', // <--- UBAH KE KAPITAL SEMUA
            2 => 'SELASA', // <--- UBAH KE KAPITAL SEMUA
            3 => 'RABU', // <--- UBAH KE KAPITAL SEMUA
            4 => 'KAMIS', // <--- UBAH KE KAPITAL SEMUA
            5 => 'JUMAT', // <--- UBAH KE KAPITAL SEMUA
            6 => 'SABTU', // <--- UBAH KE KAPITAL SEMUA
            0 => 'MINGGU', // <--- UBAH KE KAPITAL SEMUA
        ];

        // JIKA dipanggil oleh getTanggalMingguan ($fullDay=true), kembalikan nama hari apa adanya.
        if ($fullDay) {
            return $hari[$dayNumber] ?? 'SENIN';
        }

        // LOGIKA LAMA (untuk menentukan tab aktif): Jika hari kerja, kembalikan nama hari. 
        if ($dayNumber >= Carbon::MONDAY && $dayNumber <= Carbon::FRIDAY) {
            return $hari[$dayNumber];
        }
        return 'SENIN'; // Default ke SENIN jika weekend atau tidak valid
    }
}
