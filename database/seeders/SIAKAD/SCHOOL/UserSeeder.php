<?php

namespace Database\Seeders\SIAKAD\SCHOOL;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $password = Hash::make('password'); // Password default: password

        $users = [
            // Admin
            ['users_id' => 1, 'username' => 'rizky.alamsyah@admin.mutiarabangsa.ac.id', 'password' => $password, 'status' => 1, 'tgl_entry' => $now],
            ['users_id' => 2, 'username' => 'siti.khadijah@admin.mutiarabangsa.ac.id', 'password' => $password, 'status' => 1, 'tgl_entry' => $now],

            // Guru (user_id 3-10)
            ['users_id' => 3, 'username' => 'rina.ps@mutiarabangsa.ac.id', 'password' => $password, 'status' => 1, 'tgl_entry' => $now],
            ['users_id' => 4, 'username' => 'budi.s@mutiarabangsa.ac.id', 'password' => $password, 'status' => 1, 'tgl_entry' => $now],
            ['users_id' => 5, 'username' => 'maria.a@mutiarabangsa.ac.id', 'password' => $password, 'status' => 1, 'tgl_entry' => $now],
            ['users_id' => 6, 'username' => 'ahmad.f@mutiarabangsa.ac.id', 'password' => $password, 'status' => 1, 'tgl_entry' => $now],
            ['users_id' => 7, 'username' => 'amelia.s@mutiarabangsa.ac.id', 'password' => $password, 'status' => 1, 'tgl_entry' => $now],
            ['users_id' => 8, 'username' => 'dede.p@mutiarabangsa.ac.id', 'password' => $password, 'status' => 1, 'tgl_entry' => $now],
            ['users_id' => 9, 'username' => 'ani.w@mutiarabangsa.ac.id', 'password' => $password, 'status' => 1, 'tgl_entry' => $now],
            ['users_id' => 10, 'username' => 'fajar.s@mutiarabangsa.ac.id', 'password' => $password, 'status' => 1, 'tgl_entry' => $now],

            // Orang tua (user_id 11-20)
            ['users_id' => 11, 'username' => 'bambang.sugeng@gmail.com', 'password' => $password, 'status' => 1, 'tgl_entry' => $now],
            ['users_id' => 12, 'username' => 'fitriani.jaya@gmail.com', 'password' => $password, 'status' => 1, 'tgl_entry' => $now],
            ['users_id' => 13, 'username' => 'chairul.t@gmail.com', 'password' => $password, 'status' => 1, 'tgl_entry' => $now],
            ['users_id' => 14, 'username' => 'dewi.sartika@gmail.com', 'password' => $password, 'status' => 1, 'tgl_entry' => $now],
            ['users_id' => 15, 'username' => 'eko.p@email.com', 'password' => $password, 'status' => 1, 'tgl_entry' => $now],
            ['users_id' => 16, 'username' => 'fanny.aulia@gmail.com', 'password' => $password, 'status' => 1, 'tgl_entry' => $now],
            ['users_id' => 17, 'username' => 'gatot.kaca@email.com', 'password' => $password, 'status' => 1, 'tgl_entry' => $now],
            ['users_id' => 18, 'username' => 'hera.wati@gmail.com', 'password' => $password, 'status' => 1, 'tgl_entry' => $now],
            ['users_id' => 19, 'username' => 'irfan.hakim@gmail.com', 'password' => $password, 'status' => 1, 'tgl_entry' => $now],
            ['users_id' => 20, 'username' => 'joko.w@gmail.com', 'password' => $password, 'status' => 1, 'tgl_entry' => $now],
        ];

        DB::table('users')->upsert(
            $users,
            ['users_id'],
            ['username', 'password', 'status', 'tgl_entry']
        );
    }
}
