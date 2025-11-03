<?php

namespace Database\Seeders\SIAKAD\SCHOOL;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('admin')->truncate();

        $admins = [];
        for ($i = 1; $i <= 3; $i++) {
            $admins[] = [
                'users_id' => $i,
                'nama' => "Admin {$i}",
                'alamat_rmh' => "Jl. Admin {$i} No. {$i}",
                'kota_rmh' => 'Tangerang Selatan',
                'no_hp' => '08120000' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'email' => "admin{$i}@mail.com",
                'tgl_entry' => now(),
            ];
        }

        DB::table('admin')->insert($admins);
    }
}
