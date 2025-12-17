<?php

namespace Database\Seeders\SIAKAD\SCHOOL;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JadwalMapelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // CATATAN: TRUNCATE DITANGANI OLEH SiakadSeeder.php, kita tidak perlu TRUNCATE di sini.
        // Hapus DB::table('jadwal_mapel')->truncate();

        // 1. PENENTUAN RENTANG TANGGAL
        // Mulai dari hari Senin minggu ini/lalu, berakhir 1 bulan ke depan.
        $startDate = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $endDate = $startDate->copy()->addMonths(1);
        $currentDate = $startDate->copy();

        $now = Carbon::now();
        $userEntry = 1; // Superadmin

        // Hapus: $id_counter = 1; -> Biarkan database menangani Primary Key

        $jadwal_mapel_entries = []; // Ubah nama variabel agar lebih jelas

        // Waktu Pelajaran (Tidak diubah)
        $waktu_mapel = [
            'pagi' => ['07:30:00', '09:00:00'],
            'siang_1' => ['09:30:00', '11:00:00'],
            'siang_2' => ['13:00:00', '14:30:00'],
        ];

        // LOGIKA JADWAL LENGKAP (SENIN - JUMAT) - TIDAK DIUBAH
        // Format: [Kelas ID (0), Hari (1), Jam Mulai (2), Jam Selesai (3), Guru Mapel ID (4), Ruangan ID (5)]
        $data_jadwal = [
            // =============================================
            // HARI SENIN
            // =============================================
            [1, 'SENIN', $waktu_mapel['pagi'][0], $waktu_mapel['pagi'][1], 1, 1],
            [2, 'SENIN', $waktu_mapel['pagi'][0], $waktu_mapel['pagi'][1], 3, 1],
            [3, 'SENIN', $waktu_mapel['pagi'][0], $waktu_mapel['pagi'][1], 5, 1],
            [4, 'SENIN', $waktu_mapel['pagi'][0], $waktu_mapel['pagi'][1], 7, 2],
            [5, 'SENIN', $waktu_mapel['pagi'][0], $waktu_mapel['pagi'][1], 9, 2],
            [6, 'SENIN', $waktu_mapel['pagi'][0], $waktu_mapel['pagi'][1], 11, 2],
            [4, 'SENIN', $waktu_mapel['siang_1'][0], $waktu_mapel['siang_1'][1], 2, 2],
            [2, 'SENIN', $waktu_mapel['siang_1'][0], $waktu_mapel['siang_1'][1], 12, 1],
            [3, 'SENIN', $waktu_mapel['siang_1'][0], $waktu_mapel['siang_1'][1], 6, 1],
            [1, 'SENIN', $waktu_mapel['siang_1'][0], $waktu_mapel['siang_1'][1], 8, 1],
            [5, 'SENIN', $waktu_mapel['siang_1'][0], $waktu_mapel['siang_1'][1], 10, 2],
            [6, 'SENIN', $waktu_mapel['siang_1'][0], $waktu_mapel['siang_1'][1], 12, 2],

            // =============================================
            // HARI SELASA
            // =============================================
            [1, 'SELASA', $waktu_mapel['pagi'][0], $waktu_mapel['pagi'][1], 1, 1],
            [2, 'SELASA', $waktu_mapel['pagi'][0], $waktu_mapel['pagi'][1], 4, 1],
            [3, 'SELASA', $waktu_mapel['pagi'][0], $waktu_mapel['pagi'][1], 5, 1],
            [4, 'SELASA', $waktu_mapel['pagi'][0], $waktu_mapel['pagi'][1], 7, 2],
            [5, 'SELASA', $waktu_mapel['pagi'][0], $waktu_mapel['pagi'][1], 9, 2],
            [6, 'SELASA', $waktu_mapel['pagi'][0], $waktu_mapel['pagi'][1], 11, 2],
            [1, 'SELASA', $waktu_mapel['siang_1'][0], $waktu_mapel['siang_1'][1], 13, 1],
            [2, 'SELASA', $waktu_mapel['siang_1'][0], $waktu_mapel['siang_1'][1], 3, 1],
            [3, 'SELASA', $waktu_mapel['siang_1'][0], $waktu_mapel['siang_1'][1], 2, 1],
            [4, 'SELASA', $waktu_mapel['siang_1'][0], $waktu_mapel['siang_1'][1], 8, 2],
            [5, 'SELASA', $waktu_mapel['siang_1'][0], $waktu_mapel['siang_1'][1], 10, 2],
            [6, 'SELASA', $waktu_mapel['siang_1'][0], $waktu_mapel['siang_1'][1], 8, 2],

            // =============================================
            // HARI RABU
            // =============================================
            [1, 'RABU', $waktu_mapel['pagi'][0], $waktu_mapel['pagi'][1], 1, 1],
            [2, 'RABU', $waktu_mapel['pagi'][0], $waktu_mapel['pagi'][1], 4, 1],
            [3, 'RABU', $waktu_mapel['pagi'][0], $waktu_mapel['pagi'][1], 6, 1],
            [4, 'RABU', $waktu_mapel['pagi'][0], $waktu_mapel['pagi'][1], 7, 2],
            [5, 'RABU', $waktu_mapel['pagi'][0], $waktu_mapel['pagi'][1], 9, 2],
            [6, 'RABU', $waktu_mapel['pagi'][0], $waktu_mapel['pagi'][1], 12, 2],
            [1, 'RABU', $waktu_mapel['siang_1'][0], $waktu_mapel['siang_1'][1], 14, 1],
            [2, 'RABU', $waktu_mapel['siang_1'][0], $waktu_mapel['siang_1'][1], 14, 1],
            [3, 'RABU', $waktu_mapel['siang_1'][0], $waktu_mapel['siang_1'][1], 5, 1],
            [4, 'RABU', $waktu_mapel['siang_1'][0], $waktu_mapel['siang_1'][1], 9, 2],
            [5, 'RABU', $waktu_mapel['siang_1'][0], $waktu_mapel['siang_1'][1], 7, 2],
            [6, 'RABU', $waktu_mapel['siang_1'][0], $waktu_mapel['siang_1'][1], 11, 2],


            // =============================================
            // HARI KAMIS
            // =============================================
            [1, 'KAMIS', $waktu_mapel['pagi'][0], $waktu_mapel['pagi'][1], 2, 1],
            [2, 'KAMIS', $waktu_mapel['pagi'][0], $waktu_mapel['pagi'][1], 3, 1],
            [3, 'KAMIS', $waktu_mapel['pagi'][0], $waktu_mapel['pagi'][1], 6, 1],
            [4, 'KAMIS', $waktu_mapel['pagi'][0], $waktu_mapel['pagi'][1], 8, 2],
            [5, 'KAMIS', $waktu_mapel['pagi'][0], $waktu_mapel['pagi'][1], 10, 2],
            [6, 'KAMIS', $waktu_mapel['pagi'][0], $waktu_mapel['pagi'][1], 11, 2],
            [1, 'KAMIS', $waktu_mapel['siang_1'][0], $waktu_mapel['siang_1'][1], 6, 1],
            [2, 'KAMIS', $waktu_mapel['siang_1'][0], $waktu_mapel['siang_1'][1], 9, 1],
            [3, 'KAMIS', $waktu_mapel['siang_1'][0], $waktu_mapel['siang_1'][1], 12, 1],
            [4, 'KAMIS', $waktu_mapel['siang_1'][0], $waktu_mapel['siang_1'][1], 9, 2],
            [5, 'KAMIS', $waktu_mapel['siang_1'][0], $waktu_mapel['siang_1'][1], 7, 2],
            [6, 'KAMIS', $waktu_mapel['siang_1'][0], $waktu_mapel['siang_1'][1], 8, 2],

            // =============================================
            // HARI JUMAT
            // =============================================
            [1, 'JUMAT', $waktu_mapel['pagi'][0], $waktu_mapel['pagi'][1], 3, 1],
            [2, 'JUMAT', $waktu_mapel['pagi'][0], $waktu_mapel['pagi'][1], 4, 1],
            [3, 'JUMAT', $waktu_mapel['pagi'][0], $waktu_mapel['pagi'][1], 1, 1],
            [4, 'JUMAT', $waktu_mapel['pagi'][0], $waktu_mapel['pagi'][1], 9, 2],
            [5, 'JUMAT', $waktu_mapel['pagi'][0], $waktu_mapel['pagi'][1], 8, 2],
            [6, 'JUMAT', $waktu_mapel['pagi'][0], $waktu_mapel['pagi'][1], 7, 2],
        ];

        // 2. KONVERSI POLA JADWAL PER HARI
        $pola_mingguan = [];
        foreach ($data_jadwal as $data) {
            $dayName = strtoupper($data[1]); // Ambil nama hari Indonesia
            // Format yang disimpan: [Kelas ID (0), Jam Mulai (2), Jam Selesai (3), Guru Mapel ID (4), Ruangan ID (5)]
            $pola_mingguan[$dayName][] = [
                $data[0], // Kelas ID
                $data[2], // Jam Mulai
                $data[3], // Jam Selesai
                $data[4], // Guru Mapel ID
                $data[5], // Ruangan ID
            ];
        }

        // 3. PERULANGAN UNTUK MENGHASILKAN DATA HARIAN (1 BULAN)
        while ($currentDate->lessThan($endDate)) {
            $dayNameEnglish = strtoupper($currentDate->format('l')); // Dapatkan nama hari Inggris (MONDAY, TUESDAY, etc.)

            // === PENTING: MAPPING NAMA HARI INGGRIS KE INDONESIA ===
            $indonesianDayName = match ($dayNameEnglish) {
                'MONDAY' => 'SENIN',
                'TUESDAY' => 'SELASA',
                'WEDNESDAY' => 'RABU',
                'THURSDAY' => 'KAMIS',
                'FRIDAY' => 'JUMAT',
                default => null, // Abaikan Sabtu dan Minggu
            };

            if ($indonesianDayName && isset($pola_mingguan[$indonesianDayName])) {
                foreach ($pola_mingguan[$indonesianDayName] as $data) {

                    // Pola data: [Kelas ID (0), Jam Mulai (1), Jam Selesai (2), Guru Mapel ID (3), Ruangan ID (4)]

                    $jadwal_mapel_entries[] = [
                        // HAPUS Primary Key manual: 'jadwal_mapel_id' => $id_counter++,

                        'tanggal_jadwal' => $currentDate->format('Y-m-d'), // *KEY BARU: tanggal_jadwal*

                        'kelas_id' => $data[0],
                        'jam_mulai' => $data[1],
                        'jam_selesai' => $data[2],

                        'guru_mapel_id' => $data[3],
                        'ruangan_id' => $data[4],

                        'status' => 1,
                        'user_entry' => $userEntry,
                        'tgl_entry' => $now,
                    ];
                }
            }
            $currentDate->addDay(); // Lanjut ke hari berikutnya
        }

        // 4. INSERT DATA
        if (!empty($jadwal_mapel_entries)) {
            // Jika ada error Foreign Key, error akan muncul dari seeder induk atau Laravel
            DB::table('jadwal_mapel')->insert($jadwal_mapel_entries);
        }
    }
}
