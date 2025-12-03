<?php

namespace Database\Seeders\SIAKAD\SCHOOL;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $userEntry = 1; // Superadmin
        $user_roles = [];
        $id_counter = 1;

        // 1. Administrator (Role ID 1) -> User ID 1 & 2
        for ($i = 1; $i <= 2; $i++) {
            $user_roles[] = [
                'user_role_id' => $id_counter++,
                'role_id' => 1,
                'users_id' => $i,
                'user_entry' => $userEntry,
                'tgl_entry' => $now
            ];
        }

        // 2. Guru (Role ID 2) -> User ID 3 s/d 10
        for ($i = 3; $i <= 10; $i++) {
            $user_roles[] = [
                'user_role_id' => $id_counter++,
                'role_id' => 2,
                'users_id' => $i,
                'user_entry' => $userEntry,
                'tgl_entry' => $now
            ];
        }

        // 3. Orang Tua (Role ID 3) -> User ID 11 s/d 20
        for ($i = 11; $i <= 20; $i++) {
            $user_roles[] = [
                'user_role_id' => $id_counter++,
                'role_id' => 3,
                'users_id' => $i,
                'user_entry' => $userEntry,
                'tgl_entry' => $now
            ];
        }

        DB::table('user_role')->insert($user_roles);
    }
}
