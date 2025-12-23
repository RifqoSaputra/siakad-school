<?php

namespace Database\Seeders\SIAKAD\SCHOOL;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $userEntry = 1; // Superadmin
        $tahunAjaran = '2024/2025';

        $kelas = [
            // --- JURUSAN DKV (Desain Komunikasi Visual) ---
            // Walikelas: Guru 1, 2, 3
            ['kelas_id' => 1, 'walikelas' => 1, 'tingkat_kelas' => '10', 'nama_kelas' => 'DKV-1', 'tahun_ajaran' => $tahunAjaran, 'status' => 1, 'user_entry' => $userEntry, 'tgl_entry' => $now],
            ['kelas_id' => 2, 'walikelas' => 2, 'tingkat_kelas' => '11', 'nama_kelas' => 'DKV-1', 'tahun_ajaran' => $tahunAjaran, 'status' => 1, 'user_entry' => $userEntry, 'tgl_entry' => $now],
            ['kelas_id' => 3, 'walikelas' => 3, 'tingkat_kelas' => '12', 'nama_kelas' => 'DKV-1', 'tahun_ajaran' => $tahunAjaran, 'status' => 1, 'user_entry' => $userEntry, 'tgl_entry' => $now],

            // --- JURUSAN AKL (Akuntansi Keuangan Lembaga) ---
            // Walikelas: Guru 4, 5, 6
            ['kelas_id' => 4, 'walikelas' => 4, 'tingkat_kelas' => '10', 'nama_kelas' => 'AKL-1', 'tahun_ajaran' => $tahunAjaran, 'status' => 1, 'user_entry' => $userEntry, 'tgl_entry' => $now],
            ['kelas_id' => 5, 'walikelas' => 5, 'tingkat_kelas' => '11', 'nama_kelas' => 'AKL-1', 'tahun_ajaran' => $tahunAjaran, 'status' => 1, 'user_entry' => $userEntry, 'tgl_entry' => $now],
            ['kelas_id' => 6, 'walikelas' => 6, 'tingkat_kelas' => '12', 'nama_kelas' => 'AKL-1', 'tahun_ajaran' => $tahunAjaran, 'status' => 1, 'user_entry' => $userEntry, 'tgl_entry' => $now],
        ];

        DB::table('kelas')->insert($kelas);
    }
}
