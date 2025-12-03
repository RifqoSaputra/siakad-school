<?php

namespace Database\Seeders\SIAKAD\SCHOOL;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $userEntry = 1; // User ID 1: superadmin

        $admin = [
            [
                'id_admin' => 1,
                'users_id' => 1, // Superadmin
                'nama_admin' => 'Rizky Alamsyah',
                'alamat_rmh' => 'Jl. Gatot Subroto No. 5',
                'kota_rmh' => 'Jakarta',
                'no_hp' => '081210001001',
                'email' => 'rizky.admin@sekolah.ac.id',
            ],
            [
                'id_admin' => 2,
                'users_id' => 2, // Admin SKP
                'nama_admin' => 'Siti Khadijah',
                'alamat_rmh' => 'Perumahan Indah Blok C1',
                'kota_rmh' => 'Bekasi',
                'no_hp' => '085720002002',
                'email' => 'siti.admin@sekolah.ac.id',
            ],
        ];

        // Tambahkan kolom audit
        $admin = array_map(function ($data) use ($now, $userEntry) {
            $data['user_entry'] = $userEntry;
            $data['tgl_entry'] = $now;
            return $data;
        }, $admin);

        DB::table('admin')->insert($admin);
    }
}
