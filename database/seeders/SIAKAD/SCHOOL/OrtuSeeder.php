<?php

namespace Database\Seeders\SIAKAD\SCHOOL;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrtuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $userEntry = 1; // Superadmin

        $ortu = [
            // users_id 11 - 20 (ortu01 - ortu10)
            ['id_ortu' => 1, 'users_id' => 11, 'nama_ortu' => 'Bambang Sugeng', 'pekerjaan' => 'Wiraswasta', 'no_hp' => '08111000101', 'email' => 'bambang.sugeng@email.com', 'user_entry' => $userEntry, 'tgl_entry' => $now],
            ['id_ortu' => 2, 'users_id' => 12, 'nama_ortu' => 'Fitriani Jaya', 'pekerjaan' => 'Karyawan Swasta', 'no_hp' => '08111000102', 'email' => 'fitriani.jaya@email.com', 'user_entry' => $userEntry, 'tgl_entry' => $now],
            ['id_ortu' => 3, 'users_id' => 13, 'nama_ortu' => 'Chairul Tanjung', 'pekerjaan' => 'PNS', 'no_hp' => '08111000103', 'email' => 'chairul.t@email.com', 'user_entry' => $userEntry, 'tgl_entry' => $now],
            ['id_ortu' => 4, 'users_id' => 14, 'nama_ortu' => 'Dewi Sartika', 'pekerjaan' => 'Ibu Rumah Tangga', 'no_hp' => '08111000104', 'email' => 'dewi.sartika@email.com', 'user_entry' => $userEntry, 'tgl_entry' => $now],
            ['id_ortu' => 5, 'users_id' => 15, 'nama_ortu' => 'Eko Prasetyo', 'pekerjaan' => 'Security', 'no_hp' => '08111000105', 'email' => 'eko.p@email.com', 'user_entry' => $userEntry, 'tgl_entry' => $now],
            ['id_ortu' => 6, 'users_id' => 16, 'nama_ortu' => 'Fanny Aulia', 'pekerjaan' => 'Guru Bimbel', 'no_hp' => '08111000106', 'email' => 'fanny.aulia@email.com', 'user_entry' => $userEntry, 'tgl_entry' => $now],
            ['id_ortu' => 7, 'users_id' => 17, 'nama_ortu' => 'Gatot Kaca', 'pekerjaan' => 'Pilot', 'no_hp' => '08111000107', 'email' => 'gatot.kaca@email.com', 'user_entry' => $userEntry, 'tgl_entry' => $now],
            ['id_ortu' => 8, 'users_id' => 18, 'nama_ortu' => 'Hera Wati', 'pekerjaan' => 'Pengusaha Catering', 'no_hp' => '08111000108', 'email' => 'hera.wati@email.com', 'user_entry' => $userEntry, 'tgl_entry' => $now],
            ['id_ortu' => 9, 'users_id' => 19, 'nama_ortu' => 'Irfan Hakim', 'pekerjaan' => 'Driver Ojol', 'no_hp' => '08111000109', 'email' => 'irfan.hakim@email.com', 'user_entry' => $userEntry, 'tgl_entry' => $now],
            ['id_ortu' => 10, 'users_id' => 20, 'nama_ortu' => 'Joko Widodo', 'pekerjaan' => 'Pegawai Bank', 'no_hp' => '08111000110', 'email' => 'joko.w@email.com', 'user_entry' => $userEntry, 'tgl_entry' => $now],
        ];

        DB::table('ortu')->insert($ortu);
    }
}
