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
        $tahunAjaranSeeding = '2024/2025';
        
        $now = Carbon::now();
        $userEntry = 1; // Superadmin

        $jadwal_mapel_entries = [];

        $waktu_mapel = [
            'pagi' => ['07:30:00', '09:00:00'],
            'siang_1' => ['09:30:00', '11:00:00'],
            'siang_2' => ['13:00:00', '14:30:00'],
        ];

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

        // 2. PERULANGAN UNTUK MENGHASILKAN DATA TEMPLATE MINGGUAN
        foreach ($data_jadwal as $data) {
            // Data array: [Kelas ID (0), Hari (1), Jam Mulai (2), Jam Selesai (3), Guru Mapel ID (4), Ruangan ID (5)]
            $jadwal_mapel_entries[] = [
                'kelas_id'      => $data[0],
                'hari'          => $data[1], // Masukkan Hari
                'jam_mulai'     => $data[2],
                'jam_selesai'   => $data[3],
                'guru_mapel_id' => $data[4],
                'ruangan_id'    => $data[5],

                'tahun_ajaran'  => $tahunAjaranSeeding,

                'status' => 1,
                'user_entry' => $userEntry,
                'tgl_entry' => $now,
            ];
        }

        // 3. INSERT DATA
        if (!empty($jadwal_mapel_entries)) {
            DB::table('jadwal_mapel')->insert($jadwal_mapel_entries);
        }
    }
}
