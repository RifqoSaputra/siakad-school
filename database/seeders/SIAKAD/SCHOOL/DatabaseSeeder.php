<?php

namespace Database\Seeders\SIAKAD\SCHOOL;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Nonaktifkan FK check sementara
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Jalankan seeder berurutan agar tidak error FK
        $this->call([
            RoleSeeder::class,
            MenuSeeder::class,
            UserSeeder::class,
            UserRoleSeeder::class,
            AdminSeeder::class,
            GuruSeeder::class,
            OrtuSeeder::class,
            SiswaSeeder::class,
            RolePrivilegeSeeder::class,
        ]);

        // Aktifkan kembali FK check
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
