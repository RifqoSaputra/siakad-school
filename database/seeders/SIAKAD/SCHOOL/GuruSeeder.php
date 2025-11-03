<?php

namespace Database\Seeders\SIAKAD\SCHOOL;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GuruSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('guru')->truncate();

        $gurus = [];
        for ($i = 1; $i <= 10; $i++) {
            $gurus[] = [
                'users_id' => $i + 3,
                'nip' => 'GURU' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'nama' => "Guru {$i}",
                'alamat_rmh' => "Jl. Guru {$i}",
                'kota_rmh' => 'Tangerang Selatan',
                'no_hp' => '08130000' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'email' => "guru{$i}@mail.com",
                'tgl_entry' => now(),
            ];
        }

        DB::table('guru')->insert($gurus);
    }
}
