<?php

namespace Database\Seeders\SIAKAD\SCHOOL;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePrivilegeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('role_privilege')->truncate();

        // Role ID: 1=Admin, 2=Guru, 3=Orang Tua
        // Menu ID: 1-3=Admin, 4-6=Guru, 7-8=Ortu

        $rolePrivileges = [];

        // --- PRIVILEGE UNTUK ADMIN (Role ID 1) ---
        // Admin bisa melihat semua menu Admin (ID 1, 2, 3)
        foreach ([1, 2, 3] as $menuId) {
            $rolePrivileges[] = [
                'role_id' => 1,
                'menu_id' => $menuId,
                'can_view' => 1, // Pastikan bisa melihat
                'can_create' => 1,
                'can_update' => 1,
                'can_delete' => 1,
                'tgl_entry' => now(),
            ];
        }

        // --- PRIVILEGE UNTUK GURU (Role ID 2) ---
        // Guru bisa melihat semua menu Guru (ID 4, 5, 6)
        foreach ([4, 5, 6] as $menuId) {
            $rolePrivileges[] = [
                'role_id' => 2,
                'menu_id' => $menuId,
                'can_view' => 1, // Pastikan bisa melihat
                'can_create' => rand(0, 1),
                'can_update' => rand(0, 1),
                'can_delete' => 0,
                'tgl_entry' => now(),
            ];
        }

        // --- PRIVILEGE UNTUK ORANG TUA (Role ID 3) ---
        // Ortu bisa melihat menu Ortu (ID 7, 8)
        foreach ([7, 8] as $menuId) {
            $rolePrivileges[] = [
                'role_id' => 3,
                'menu_id' => $menuId,
                'can_view' => 1, // Pastikan bisa melihat
                'can_create' => 0,
                'can_update' => 0,
                'can_delete' => 0,
                'tgl_entry' => now(),
            ];
        }

        DB::table('role_privilege')->insert($rolePrivileges);
    }
}