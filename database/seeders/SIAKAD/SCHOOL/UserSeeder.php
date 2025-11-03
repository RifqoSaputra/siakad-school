<?php

namespace Database\Seeders\SIAKAD\SCHOOL;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('user')->truncate();

        $users = [];
        for ($i = 1; $i <= 63; $i++) {
            $users[] = [
                'username' => "user{$i}",
                'password' => Hash::make('password'),
                'status' => 1,
                'tgl_entry' => now(),
            ];
        }

        DB::table('user')->insert($users);
    }
}
