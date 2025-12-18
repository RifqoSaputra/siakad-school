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
        $users = [];
        $password = Hash::make('password'); // Password default: password

        // 1. Akun Administrator (ID 1 & 2)
        $users[] = [
            'users_id' => 1,
            'username' => 'superadmin',
            'password' => $password,
            'status' => 1,
            'tgl_entry' => $now,
        ];
        $users[] = [
            'users_id' => 2,
            'username' => 'adminskp',
            'password' => $password,
            'status' => 1,
            'tgl_entry' => $now,
        ];

        // 2. Akun Guru (ID 3 sampai 10)
        for ($i = 3; $i <= 10; $i++) {
            $users[] = [
                'users_id' => $i,
                'username' => 'guru' . str_pad($i - 2, 2, '0', STR_PAD_LEFT),
                'password' => $password,
                'status' => 1,
                'tgl_entry' => $now,
            ];
        }


        // 3. Akun Orang Tua (ID 11 sampai 20)
        for ($i = 11; $i <= 20; $i++) {
            $users[] = [
                'users_id' => $i,
                'username' => 'ortu' . str_pad($i - 10, 2, '0', STR_PAD_LEFT),
                'password' => $password,
                'status' => 1,
                'tgl_entry' => $now,
            ];
        }

        DB::table('users')->insert($users);
    }
}
