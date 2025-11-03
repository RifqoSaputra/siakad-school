<?php

namespace Database\Seeders\SIAKAD\SCHOOL;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrtuSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('ortu')->truncate();

        $ortus = [];
        for ($i = 1; $i <= 50; $i++) {
            $ortus[] = [
                'user_id' => $i + 13,
                'nama_wali' => "Ortu {$i}",
                'pekerjaan' => "Pekerjaan {$i}",
                'no_hp' => '08140000' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'email' => "ortu{$i}@mail.com",
                'tgl_entry' => now(),
            ];
        }

        DB::table('ortu')->insert($ortus);
    }
}
