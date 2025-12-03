<?php

namespace Database\Seeders\SIAKAD\SCHOOL;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SiswaKelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $userEntry = 1; // Superadmin
        $siswa_kelas = [];
        $id_counter = 1;
        $siswa_per_kelas = 10;
        $total_siswa = 60;
        
        // --- KOREKSI: Tambahkan definisi Tahun Ajaran ---
        $tahunAjaran = '2024/2025';
        // ----------------------------------------------------

        for ($siswa_id = 1; $siswa_id <= $total_siswa; $siswa_id++) {
            // Menentukan Kelas ID berdasarkan urutan siswa
            // 1-10 -> Kelas 1, 11-20 -> Kelas 2, dst.
            $kelas_id = ceil($siswa_id / $siswa_per_kelas);

            $siswa_kelas[] = [
                'siswa_kelas_id' => $id_counter++,
                'id_siswa' => $siswa_id,
                'kelas_id' => $kelas_id,
                
                // --- KOREKSI: Tambahkan kolom tahun_ajaran ---
                'tahun_ajaran' => $tahunAjaran, 
                // ----------------------------------------------
                
                'status' => 1,
                'user_entry' => $userEntry,
                'tgl_entry' => $now
            ];
        }

        DB::table('siswa_kelas')->insert($siswa_kelas);
    }
}