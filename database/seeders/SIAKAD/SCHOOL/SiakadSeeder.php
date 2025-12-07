<?php

namespace Database\Seeders\SIAKAD\SCHOOL;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Database\Seeders\SIAKAD\SCHOOL\PengumumanSeeder;

class SIAKADSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::connection()->getDriverName() == 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        DB::table('pengumuman_user')->truncate();
        DB::table('pengumuman')->truncate();
        DB::table('nilai_ujian')->truncate();
        DB::table('jadwal_mapel')->truncate();
        DB::table('siswa_kelas')->truncate();
        DB::table('guru_mapel')->truncate();
        DB::table('kelas')->truncate();
        DB::table('user_role')->truncate();
        DB::table('role_privilege')->truncate();
        DB::table('siswa')->truncate();
        DB::table('guru')->truncate();
        DB::table('ortu')->truncate();
        DB::table('admin')->truncate();
        DB::table('ruangan')->truncate();
        DB::table('mapel')->truncate();
        DB::table('users')->truncate();
        DB::table('menu')->truncate();
        DB::table('role')->truncate();
        
        $this->call([
            RoleSeeder::class,
            MenuSeeder::class,
            UserSeeder::class,
            MapelSeeder::class,
            RuanganSeeder::class,
            AdminSeeder::class,
            OrtuSeeder::class,
            GuruSeeder::class,
            SiswaSeeder::class,
            UserRoleSeeder::class,
            RolePrivilegeSeeder::class,
            KelasSeeder::class,
            GuruMapelSeeder::class,
            SiswaKelasSeeder::class,
            JadwalMapelSeeder::class,
            NilaiUjianSeeder::class,
            PengumumanSeeder::class,
        ]);

        if (DB::connection()->getDriverName() == 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
}
