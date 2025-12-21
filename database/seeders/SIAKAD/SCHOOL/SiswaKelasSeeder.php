<?php

namespace Database\Seeders\SIAKAD\SCHOOL;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SiswaKelasSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $userEntry = 1;
        $tahunAjaran = '2024/2025';

        // 🔥 AMBIL ID SISWA ASLI DARI DATABASE
        $siswaIds = DB::table('siswa')
            ->orderBy('id_siswa')
            ->pluck('id_siswa');

        $siswa_per_kelas = 10;
        $kelas_id = 1;
        $counter = 0;

        $data = [];

        foreach ($siswaIds as $id_siswa) {

            if ($counter > 0 && $counter % $siswa_per_kelas === 0) {
                $kelas_id++;
            }

            $data[] = [
                'id_siswa'     => $id_siswa,
                'kelas_id'     => $kelas_id,
                'tahun_ajaran' => $tahunAjaran,
                'status'       => 1,
                'user_entry'   => $userEntry,
                'tgl_entry'    => $now,
            ];

            $counter++;
        }

        DB::table('siswa_kelas')->truncate();
        DB::table('siswa_kelas')->insert($data);
    }
}
