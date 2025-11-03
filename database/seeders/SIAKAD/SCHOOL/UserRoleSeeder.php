<?php

namespace Database\Seeders\SIAKAD\SCHOOL;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('user_role')->truncate();

        $userRoles = [];

        // 3 admin
        for ($i = 1; $i <= 3; $i++) {
            $userRoles[] = ['users_id' => $i, 'role_id' => 1, 'tgl_entry' => now()];
        }

        // 10 guru
        for ($i = 4; $i <= 13; $i++) {
            $userRoles[] = ['users_id' => $i, 'role_id' => 2, 'tgl_entry' => now()];
        }

        // 50 ortu
        for ($i = 14; $i <= 63; $i++) {
            $userRoles[] = ['users_id' => $i, 'role_id' => 3, 'tgl_entry' => now()];
        }

        DB::table('user_role')->insert($userRoles);
    }
}
