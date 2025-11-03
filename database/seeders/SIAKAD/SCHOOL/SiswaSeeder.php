<?php

namespace Database\Seeders\SIAKAD\SCHOOL;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('siswa')->truncate();

        $siswas = [];
        for ($i = 1; $i <= 50; $i++) {
            $siswas[] = [
                'id_ortu' => $i,
                'nis' => 'NIS' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'nama' => "Siswa {$i}",
                'tgl_lahir' => now()->subYears(rand(10, 18))->format('Y-m-d'),
                'agama' => 'Islam',
                'alamat_rmh' => "Jl. Siswa {$i}",
                'kota_rmh' => 'Tangerang Selatan',
                'kode_pos' => '15310',
                'no_hp' => '08150000' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'email' => "siswa{$i}@mail.com",
                'tgl_entry' => now(),
            ];
        }

        DB::table('siswa')->insert($siswas);
    }
}
