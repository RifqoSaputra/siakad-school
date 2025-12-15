<?php

namespace App\Http\Controllers\Ortu;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SIAKAD\SCHOOL\Absensi; // Import Model Absensi
use App\Models\SIAKAD\SCHOOL\Siswa;   // Import Model Siswa
use App\Models\SIAKAD\SCHOOL\Mapel;
use App\Models\SIAKAD\SCHOOL\GuruMapel;
use App\Models\SIAKAD\SCHOOL\JadwalMapel;
use App\Models\SIAKAD\SCHOOL\SiswaKelas;
use Illuminate\Support\Facades\Log;

class CekAbsenController extends Controller
{
    /**
     * Menampilkan riwayat absensi siswa yang aktif, dengan filter opsional (tahun/bulan).
     * @param \Illuminate\Http\Request $request
     * @param int|null $tahun Tahun yang dipilih (dari URL)
     * @param int|null $bulan Bulan yang dipilih (dari URL)
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // 1. Dapatkan ID Siswa
        $selectedSiswaId = $request->session()->get('selected_siswa_id', 1);
        $siswa = Siswa::find($selectedSiswaId);
        $siswaName = $siswa ? $siswa->nama : 'Siswa Tidak Ditemukan';

        // 2. Filter dari Request (Query String: ?kelas_id=X&mapel_id=Y)
        $kelasId = $request->input('kelas_id');
        $mapelId = $request->input('mapel_id');

        // 3. Dapatkan Daftar Kelas Siswa
        $siswaKelasData = SiswaKelas::where('id_siswa', $selectedSiswaId)
            ->with('kelas')
            ->orderBy('tahun_ajaran', 'desc')
            ->get();

        $daftarKelas = $siswaKelasData->map(function ($sk) {
            $kelas = $sk->kelas;
            $namaLengkap = ($kelas->tingkat_kelas ?? '') . ' ' . ($kelas->nama_kelas ?? 'Kelas N/A');
            return [
                'id' => $sk->kelas_id,
                'nama' => trim($namaLengkap),
            ];
        })->unique('id')->values();

        // Tentukan Kelas Aktif (Default ke yang pertama jika tidak ada filter)
        $kelasAktifId = $kelasId ?? ($daftarKelas->first()['id'] ?? null);

        // 4. Ambil Daftar Mapel berdasarkan Kelas Aktif
        $daftarMapel = collect();
        if ($kelasAktifId) {
            $mapelList = GuruMapel::join('jadwal_mapel', 'guru_mapel.guru_mapel_id', '=', 'jadwal_mapel.guru_mapel_id')
                ->join('mapel', 'guru_mapel.mapel_id', '=', 'mapel.mapel_id')
                ->where('jadwal_mapel.kelas_id', $kelasAktifId)
                ->select('mapel.mapel_id as id', 'mapel.nama_mapel as nama')
                ->distinct()
                ->get();

            // Tambahkan opsi "Semua Mata Pelajaran"
            $daftarMapel->push(['id' => 0, 'nama' => 'Semua Mata Pelajaran']);
            $daftarMapel = $daftarMapel->merge($mapelList);
        }

        // Tentukan Mapel Aktif (Default 0 / Semua)
        $mapelAktifId = (int) ($mapelId ?? 0);

        // =============================================================
        // 5a. Ambil Data Absensi & Hitung Rekap GLOBAL (HANYA berdasarkan Kelas)
        // Kita paksa mapelId = 0 (Semua Mapel) untuk Rekapitulasi dan Warning
        $dataAbsensiGlobal = $this->getAbsensiData($selectedSiswaId, $kelasAktifId, 0);
        $rekapGlobal = $this->calculateRecap($dataAbsensiGlobal);

        // 6a. LOGIKA WARNING GLOBAL
        $alphaCountGlobal = ($rekapGlobal['Alpha'] ?? 0) + ($rekapGlobal['Alfa'] ?? 0);
        $warningDataGlobal = $this->getWarningData($alphaCountGlobal);

        // 5b. Ambil Data Absensi FILTERED (untuk Tabel Riwayat)
        $dataAbsensiFiltered = $this->getAbsensiData($selectedSiswaId, $kelasAktifId, $mapelAktifId);
        // =============================================================

        // Persiapkan data yang akan dikirim ke View/JSON
        $dataAbsensi = $dataAbsensiFiltered; // Data untuk Tabel Riwayat
        $rekap = $rekapGlobal; // Data untuk Cards Rekap
        $warningData = $warningDataGlobal; // Data untuk Alert Warning

        // 7. JIKA REQUEST ADALAH AJAX, KEMBALIKAN JSON
        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'daftar_mapel' => $daftarMapel,
                'absensi' => $dataAbsensi, // Data Absensi (Filtered)
                'rekap' => $rekap, // Rekap Global (untuk Cards)
                'warning' => $warningData, // Warning Global (untuk Alert)
            ]);
        }

        // 8. JIKA BUKAN AJAX, TAMPILKAN VIEW BIASA
        return view('dashboard.ortu.cek-absen-siswa', [
            'dataAbsensi' => $dataAbsensi,
            'rekap' => $rekap,
            'siswaName' => $siswaName,
            'daftarKelas' => $daftarKelas,
            'daftarMapel' => $daftarMapel,
            'kelasAktifId' => (int) $kelasAktifId,
            'mapelAktifId' => (int) $mapelAktifId,
            'warning' => $warningData
        ]);
    }

    /**
     * Helper untuk mendapatkan data warning berdasarkan jumlah Alpha.
     */
    private function getWarningData(int $alphaCount)
    {
        $warningData = [
            'level' => 0,
            'class' => '',
            'message' => ''
        ];

        if ($alphaCount >= 4) {
            $warningData = [
                'level' => 3,
                'class' => 'alert-danger',
                'message' => '⚠️ <strong>PERINGATAN KRITIS:</strong> Anak Anda tidak hadir sebanyak <strong>' . $alphaCount . ' kali</strong> (Seluruh Mapel). <strong>Nilai Ujian tidak dapat diinput!</strong> Mohon segera hubungi wali kelas.'
            ];
        } elseif ($alphaCount === 3) {
            $warningData = [
                'level' => 2,
                'class' => 'alert-danger',
                'message' => '❗ <strong>PERINGATAN KERAS:</strong> Anak Anda tidak hadir (Alpha/Alfa) sebanyak <strong>' . $alphaCount . ' kali</strong>. Jika mencapai 4 kali, nilai ujian tidak akan diinput.'
            ];
        } elseif ($alphaCount === 2) {
            $warningData = [
                'level' => 1,
                'class' => 'alert-warning',
                'message' => '🔔 <strong>PERINGATAN:</strong> Anak Anda tidak hadir (Alpha/Alfa) sebanyak <strong>' . $alphaCount . ' kali</strong>. Perhatikan kehadiran siswa.'
            ];
        }

        return $warningData;
    }

    /**
     * Mengambil data absensi dari database untuk Siswa, Kelas, dan Mapel tertentu.
     */
    private function getAbsensiData($siswaId, $kelasId, $mapelId)
    {
        if (!$kelasId) return [];

        $jadwalQuery = JadwalMapel::join('guru_mapel', 'jadwal_mapel.guru_mapel_id', '=', 'guru_mapel.guru_mapel_id')
            ->where('jadwal_mapel.kelas_id', $kelasId);

        // Jika mapelId > 0, filter berdasarkan mapel
        if ((int) $mapelId > 0) {
            $jadwalQuery->where('guru_mapel.mapel_id', $mapelId);
        }

        $jadwalIds = $jadwalQuery->pluck('jadwal_mapel_id');

        if ($jadwalIds->isEmpty()) return [];

        $absensi = Absensi::where('id_siswa', $siswaId)
            ->whereIn('absensi.jadwal_mapel_id', $jadwalIds)
            ->with(['jadwal.penugasan.mapel'])
            ->orderBy('absensi.tgl_entry', 'asc')
            ->select('absensi.*')
            ->get();

        // Helper Bulan Indonesia untuk format tanggal di Controller
        $bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        return $absensi->map(function ($item) use ($bulanIndo) {
            $mapelName = $item->jadwal->penugasan->mapel->nama_mapel ?? 'N/A';
            $rawDate = date('Y-m-d', strtotime($item->tgl_entry));
            
            $timestamp = strtotime($rawDate);
            $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'][date('w', $timestamp)];
            $tgl = date('j', $timestamp);
            $bln = $bulanIndo[date('n', $timestamp) - 1];
            $thn = date('Y', $timestamp);
            $formattedDate = "$hari, $tgl $bln $thn";

            return [
                'tanggal_formatted' => $formattedDate,
                'mapel' => $mapelName,
                'status' => $item->status,
                'keterangan' => $item->keterangan,
            ];
        })->toArray();
    }

    /**
     * Menghitung rekapitulasi dari data absensi.
     */
    private function calculateRecap(array $dataAbsensi)
    {
        $rekap = [
            'Hadir' => 0,
            'Izin' => 0,
            'Sakit' => 0,
            'Alfa' => 0, // Digunakan untuk menampung Alpha dan Alfa
        ];

        foreach ($dataAbsensi as $absen) {
            $status = $absen['status'];
            if (in_array($status, ['Alpha', 'Alfa', 'TIDAK HADIR'])) {
                $status = 'Alfa'; // Disamakan untuk rekap
            }
            if (isset($rekap[$status])) {
                $rekap[$status]++;
            }
        }

        $rekap['Alpha'] = $rekap['Alfa'];
        unset($rekap['Alfa']);

        return $rekap;
    }

    // Contoh Fungsi Helper atau Model Scope
    public static function getTotalNonHadirPerMapelSemester($siswaId, $mapelId, $tahunAjaran, $semester)
    {
        // 1. Dapatkan semua ID Penugasan Guru (GuruMapel) yang sesuai dengan Mapel dan Periode.
        $penugasanIds = GuruMapel::where('mapel_id', $mapelId)
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('semester', $semester)
            ->pluck('guru_mapel_id');

        // 2. Dapatkan semua ID Jadwal (JadwalMapel) yang terkait dengan Penugasan tersebut.
        $jadwalIds = JadwalMapel::whereIn('guru_mapel_id', $penugasanIds)
            ->pluck('jadwal_mapel_id');

        // 3. Hitung total absensi dengan status Non-Hadir (Izin, Sakit, Alpha).
        $nonHadirCount = Absensi::where('id_siswa', $siswaId)
            ->whereIn('jadwal_mapel_id', $jadwalIds)
            ->whereIn('status', ['Izin', 'Sakit', 'Alpha', 'Alfa']) // Sertakan Alpha dan Alfa
            ->count();

        return $nonHadirCount;
    }
}
