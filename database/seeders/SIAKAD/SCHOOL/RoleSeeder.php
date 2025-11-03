<?php

namespace Database\Seeders\SIAKAD\SCHOOL;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('role')->truncate();

        $roles = [
            ['deskripsi' => 'Admin', 'status' => 1, 'tgl_entry' => now()],
            ['deskripsi' => 'Guru', 'status' => 1, 'tgl_entry' => now()],
            ['deskripsi' => 'Orang Tua', 'status' => 1, 'tgl_entry' => now()],
        ];

        DB::table('role')->insert($roles);
    }
}
