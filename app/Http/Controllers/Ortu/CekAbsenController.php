<?php

namespace App\Http\Controllers\Ortu;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SIAKAD\SCHOOL\Absensi; // Import Model Absensi
use App\Models\SIAKAD\SCHOOL\Siswa;   // Import Model Siswa

class CekAbsenController extends Controller
{
    /**
     * Menampilkan riwayat absensi siswa yang aktif, dengan filter opsional (tahun/bulan).
     * @param \Illuminate\Http\Request $request
     * @param int|null $tahun Tahun yang dipilih (dari URL)
     * @param int|null $bulan Bulan yang dipilih (dari URL)
     * @return \Illuminate\View\View
     */
    public function index(Request $request, $tahun = null, $bulan = null)
    {
        // 1. Mendapatkan ID siswa yang sedang aktif/dipilih
        // **Ganti logika ini dengan cara Anda mendapatkan ID Siswa yang sedang login/dipilih Ortu!**
        $selectedSiswaId = $request->session()->get('selected_siswa_id', 1);

        // 2. Ambil data Siswa untuk mendapatkan Nama
        $siswa = Siswa::find($selectedSiswaId);
        $siswaName = $siswa ? $siswa->nama : 'Siswa Tidak Ditemukan';

        // 3. Daftar Bulan untuk Dropdown
        $daftarBulan = [
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        ];

        // 4. Tentukan bulan dan tahun aktif berdasarkan parameter URL atau default
        $tahunAktif = (int) ($tahun ?? date('Y'));
        $bulanAktif = (int) ($bulan ?? date('n'));

        // 5. AMBIL DATA ABSENSI DARI DATABASE
        $dataAbsensi = $this->getAbsensiData($selectedSiswaId, $tahunAktif, $bulanAktif);

        // 6. Hitung rekapitulasi
        $rekap = $this->calculateRecap($dataAbsensi);

        // 7. Mengirimkan data ke view
        return view('dashboard.ortu.cek-absen-siswa', [
            'dataAbsensi' => $dataAbsensi,
            'rekap' => $rekap,
            'siswaName' => $siswaName,
            'selectedSiswaId' => $selectedSiswaId,
            'bulan' => $daftarBulan,
            'bulanAktif' => $bulanAktif,
            'tahunAktif' => $tahunAktif,
        ]);
    }

    /**
     * Mengambil data absensi dari database untuk Siswa dan periode tertentu.
     * @param int $siswaId
     * @param int $tahun
     * @param int $bulan
     * @return array
     */
    private function getAbsensiData($siswaId, $tahun, $bulan)
    {
        // Format bulan menjadi 2 digit (misal: '01', '12')
        $bulanPad = str_pad($bulan, 2, '0', STR_PAD_LEFT);
        $searchPrefix = "{$tahun}-{$bulanPad}";

        // Gunakan whereRaw untuk memfilter berdasarkan bulan dan tahun dari kolom tgl_entry/waktu_absen
        // Karena kolom 'tgl_entry' lebih umum untuk tanggal input data, kita gunakan itu. 
        // JIKA TANGGAL ABSEN ADA DI KOLOM WAKTU_ABSEN, GANTI tgl_entry DENGAN waktu_absen.
        $absensi = Absensi::where('id_siswa', $siswaId)
            ->whereRaw("DATE_FORMAT(tgl_entry, '%Y-%m') = ?", [$searchPrefix])
            ->orderBy('tgl_entry', 'asc') // Urutkan berdasarkan tanggal
            ->get();

        // Format data agar sesuai dengan struktur yang diharapkan oleh Blade
        $formattedData = $absensi->map(function ($item) {
            // Asumsi tanggal yang relevan ada di kolom 'tgl_entry'
            $tanggal = date('Y-m-d', strtotime($item->tgl_entry));

            return [
                'tanggal' => $tanggal,
                'status' => $item->status, // Ambil dari kolom 'status_kehadiran'
                'keterangan' => $item->keterangan,
            ];
        })->toArray();

        return $formattedData;
    }

    /**
     * Menghitung rekapitulasi dari data absensi.
     * Tidak ada perubahan signifikan di sini, hanya menerima hasil query DB.
     */
    private function calculateRecap(array $dataAbsensi)
    {
        $rekap = [
            'Hadir' => 0,
            'Izin' => 0,
            'Sakit' => 0,
            'Alfa' => 0,
        ];

        foreach ($dataAbsensi as $absen) {
            $status = $absen['status'];
            // Menangani status 'Alpha' dan 'Alfa' disamakan
            if ($status === 'Alpha') {
                $status = 'Alfa';
            }
            if (isset($rekap[$status])) {
                $rekap[$status]++;
            }
        }

        return $rekap;
    }
}
