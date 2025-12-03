<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\SIAKAD\SCHOOL\JadwalMapel;
use App\Models\SIAKAD\SCHOOL\Absensi;
use App\Models\SIAKAD\SCHOOL\SiswaKelas;
use App\Models\SIAKAD\SCHOOL\Siswa;
use App\Models\SIAKAD\SCHOOL\Guru;
use Carbon\Carbon; // Wajib import Carbon

class AbsensiController extends Controller
{
    /**
     * Menampilkan daftar jadwal mengajar guru pada tanggal tertentu.
     */
    public function index(Request $request)
    {
        // 1. Ambil data guru yang sedang login
        $user = Auth::user();
        $guru = $user->guru;

        if (!$guru) {
            return redirect()->back()->with('error', 'Data guru tidak ditemukan.');
        }

        // 2. Tentukan tanggal filter (default hari ini)
        $filterTanggal = $request->input('tanggal', date('Y-m-d'));

        // **PERBAIKAN 1:** Mengambil nama hari dari $filterTanggal untuk display.
        $carbonDate = Carbon::parse($filterTanggal);
        $hariIndonesia = $this->getHariIndonesia($carbonDate->format('N'));

        // 3. Tentukan Tahun Ajaran Aktif (Hardcode sementara)
        $tahunAjaranAktif = '2024/2025';

        // 4. Ambil Jadwal Mengajar Guru berdasarkan TANGGAL JADWAL
        // **PERBAIKAN UTAMA:** Menggunakan 'tanggal_jadwal' dan $filterTanggal
        $jadwalMengajar = JadwalMapel::whereDate('tanggal_jadwal', $filterTanggal)
            ->whereHas('penugasan', function ($query) use ($guru, $tahunAjaranAktif) {
                // Relasi penugasan harus memiliki guru yang login dan tahun ajaran aktif
                $query->where('id_guru', $guru->id_guru)
                    ->where('tahun_ajaran', $tahunAjaranAktif);
            })
            // Eager loading relasi yang dibutuhkan
            ->with(['kelas', 'penugasan.mapel'])
            ->orderBy('jam_mulai')
            ->get();

        // 5. Cek Status Absensi
        foreach ($jadwalMengajar as $jadwal) {
            // Cek status absensi berdasarkan jadwal_mapel_id dan $filterTanggal
            $absensiTerakhir = Absensi::where('jadwal_mapel_id', $jadwal->jadwal_mapel_id)
                ->whereDate('waktu_absen', $filterTanggal)
                ->orderBy('tgl_entry', 'desc')
                ->first();

            $jadwal->status_absensi = 'Belum Diisi';
            $jadwal->waktu_terakhir_isi = null;

            if ($absensiTerakhir) {
                // Menggunakan Carbon untuk menampilkan waktu entry yang sudah di-timezone
                $waktuEntry = Carbon::parse($absensiTerakhir->tgl_entry)
                    ->setTimezone(config('app.timezone'));

                $jadwal->status_absensi = 'Sudah Diisi';
                $jadwal->waktu_terakhir_isi = $waktuEntry->format('H:i');
                $jadwal->tanggal_terakhir_isi = $waktuEntry->format('Y-m-d');
            }
        }

        return view('dashboard.guru.absensi.index', [
            'jadwalMengajar' => $jadwalMengajar,
            'filterTanggal' => $filterTanggal,
            'hariIndonesia' => $hariIndonesia,
        ]);
    }

    /**
     * Menampilkan formulir absensi detail untuk jadwal tertentu.
     * @param Request $request
     * @param int $id_jadwal
     * @return \Illuminate\View\View
     */
    public function detail(Request $request, $id_jadwal)
    {
        // **PERBAIKAN 2:** Ambil tanggal dari URL/input (default hari ini)
        $tanggalAbsen = $request->input('tanggal', date('Y-m-d'));

        // 1. Ambil data Jadwal Mapel
        $jadwal = JadwalMapel::where('jadwal_mapel_id', $id_jadwal)
            ->with(['kelas.siswaTerdaftar.siswa', 'penugasan.mapel'])
            ->firstOrFail();

        // 2. Tentukan Tahun Ajaran Aktif (Hardcode sementara)
        $tahunAjaranAktif = '2024/2025';

        // 3. Ambil daftar Siswa yang terdaftar di kelas tersebut
        $siswaKelas = SiswaKelas::where('kelas_id', $jadwal->kelas_id)
            ->where('tahun_ajaran', $tahunAjaranAktif)
            ->with(['siswa' => function ($query) {
                $query->select('id_siswa', 'nis', 'nama');
            }])
            ->get()
            ->pluck('siswa')
            ->filter();

        // 4. Ambil data Absensi yang sudah tersimpan untuk jadwal pada $tanggalAbsen
        // **PERBAIKAN 3:** Absensi dicari berdasarkan $tanggalAbsen dari request
        $absensiTersimpan = Absensi::where('jadwal_mapel_id', $id_jadwal)
            ->whereDate('waktu_absen', $tanggalAbsen)
            ->get()
            ->keyBy('id_siswa');

        // 5. Gabungkan data siswa dengan data absensi
        $dataSiswaAbsen = $siswaKelas->map(function ($siswa) use ($absensiTersimpan) {
            $status = $absensiTersimpan->get($siswa->id_siswa);
            return (object) [
                'id_siswa' => $siswa->id_siswa,
                'nis' => $siswa->nis,
                'nama' => $siswa->nama,
                'status' => optional($status)->status,
                'keterangan' => optional($status)->keterangan ?? '',
            ];
        })->sortBy('nama');

        // 6. Ambil waktu terakhir absensi disimpan
        $waktuTerakhirSimpan = Absensi::where('jadwal_mapel_id', $id_jadwal)
            ->whereDate('waktu_absen', $tanggalAbsen) // Menggunakan $tanggalAbsen
            ->max('tgl_entry');

        return view('dashboard.guru.absensi.detail', [
            'jadwal' => $jadwal,
            'dataSiswaAbsen' => $dataSiswaAbsen,
            'tanggalAbsen' => $tanggalAbsen, // Kirim tanggal ke view untuk digunakan di form
            'waktuTerakhirSimpan' => $waktuTerakhirSimpan ? Carbon::parse($waktuTerakhirSimpan)->format('Y-m-d H:i:s') : null,
        ]);
    }

    /**
     * Menyimpan data absensi dari form detail.
     * @param Request $request
     * @param int $id_jadwal
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, $id_jadwal)
    {
        $request->validate([
            'absensi' => 'required|array',
            'absensi.*.id_siswa' => 'required|exists:siswa,id_siswa',
            'absensi.*.status' => 'required|in:Hadir,Izin,Sakit,Alpha',
            'absensi.*.catatan' => 'nullable|string|max:255',
            'tanggal_absen_input' => 'required|date_format:Y-m-d',
        ]);

        $user = Auth::user();
        // Ambil tanggal dari input form yang dikirim dari view detail
        $tanggalAbsen = $request->input('tanggal_absen_input');
        $now = date('Y-m-d H:i:s');

        DB::beginTransaction();
        try {
            // 1. Hapus data absensi lama untuk jadwal dan TANGGAL yang spesifik
            Absensi::where('jadwal_mapel_id', $id_jadwal)
                ->whereDate('waktu_absen', $tanggalAbsen)
                ->delete();

            // 2. Insert data absensi baru
            $dataToInsert = [];
            foreach ($request->absensi as $data) {
                $keterangan = $data['status'] === 'Hadir' ? null : ($data['catatan'] ?? null);

                $dataToInsert[] = [
                    'id_siswa' => $data['id_siswa'],
                    'jadwal_mapel_id' => $id_jadwal,
                    'status' => $data['status'],
                    // **PERBAIKAN 5:** Menggunakan tanggal yang diambil dari form
                    'waktu_absen' => $tanggalAbsen,
                    'keterangan' => $keterangan,
                    'user_entry' => $user->guru->id_guru,
                    'tgl_entry' => $now,
                ];
            }

            if (!empty($dataToInsert)) {
                Absensi::insert($dataToInsert);
            }

            DB::commit();

            // Redirect kembali ke detail jadwal, sertakan tanggal di URL agar data yang tampil konsisten
            return redirect()->route('guru.absensi.detail', [
                'id_jadwal' => $id_jadwal,
                'tanggal' => $tanggalAbsen
            ])
                ->with('success', 'Absensi berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menyimpan absensi. Silakan coba lagi. Error: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Helper: Konversi angka hari ke nama hari dalam Bahasa Indonesia.
     * @param int $dayNumber (1=Senin, ..., 7=Minggu)
     * @return string
     */
    private function getHariIndonesia($dayNumber)
    {
        $hari = [
            '1' => 'Senin',
            '2' => 'Selasa',
            '3' => 'Rabu',
            '4' => 'Kamis',
            '5' => 'Jumat',
            '6' => 'Sabtu',
            '7' => 'Minggu',
        ];
        // Menggunakan (string) untuk memastikan kecocokan dengan kunci array
        return $hari[(string)$dayNumber] ?? 'Unknown';
    }
}
