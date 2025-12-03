<?php

namespace Database\Seeders\SIAKAD\SCHOOL;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RolePrivilegeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $userEntry = 1; // Superadmin
        $privileges = [];
        $id_counter = 1;

        // Kosongkan tabel terlebih dahulu untuk menghindari duplikasi saat re-seeding
        DB::table('role_privilege')->truncate();

        // Mendefinisikan hak akses dasar (semua 0)
        $base_priv = [
            'can_view' => 0,
            'can_create' => 0,
            'can_update' => 0,
            'can_delete' => 0,
            'can_export' => 0,
            'can_print' => 0,
            'user_entry' => $userEntry,
            'tgl_entry' => $now
        ];

        // Daftar Menu ID yang diizinkan untuk Admin (Full Access CRUD)
        $admin_menu_ids = [
            1, // Dashboard
            2, // Data Master (Parent)
            6,
            7,
            8,
            9, // Data Master Children
            4, // Manajemen User (Parent)
            12,
            13, // Manajemen User Children
            28, // Nilai Siswa (Parent Baru)
            29,
            30, // Nilai Siswa Children (Nilai Harian, Nilai Ujian)
            5, // Laporan (Parent)
            14, // Laporan Absensi Siswa
            31, // Laporan Rapor (Baru)
            24, // Pengumuman
        ];

        // ----------------------------------------------------
        // --- Role 1: Administrator (Akses Penuh ke menu baru) ---
        // ----------------------------------------------------
        foreach ($admin_menu_ids as $menu_id) {
            $privileges[] = array_merge($base_priv, [
                'role_priv_id' => $id_counter++,
                'role_id' => 1,
                'menu_id' => $menu_id,
                'can_view' => 1,
                'can_create' => 1,
                'can_update' => 1,
                'can_delete' => 1,
                'can_export' => 1,
                'can_print' => 1,
            ]);
        }

        // ------------------------------------------------------------------
        // --- Role 2: Guru (Akses Dashboard, View Master Data, dan Menu Guru + Pengumuman) ---
        // ------------------------------------------------------------------

        // Akses Dashboard (ID 1)
        $privileges[] = array_merge($base_priv, [
            'role_priv_id' => $id_counter++,
            'role_id' => 2,
            'menu_id' => 1,
            'can_view' => 1,
        ]);

        // Akses View Only (Master Data Admin yang relevan untuk Guru)
        // [6] Data Guru, [8] Mata Pelajaran, [9] Data Kelas
        $view_only_menus_guru = [6, 8, 9];
        foreach ($view_only_menus_guru as $menu_id) {
            $privileges[] = array_merge($base_priv, [
                'role_priv_id' => $id_counter++,
                'role_id' => 2,
                'menu_id' => $menu_id,
                'can_view' => 1,
            ]);
        }

        // Akses Pengumuman (ID 24) - NEW for Guru (View Only)
        $privileges[] = array_merge($base_priv, [
            'role_priv_id' => $id_counter++,
            'role_id' => 2,
            'menu_id' => 24,
            'can_view' => 1,
        ]);

        // Akses CRUD (Menu Tunggal Guru: ID 15, 17, 18 dan Children 25, 26)
        $guru_menus_crud = [15, 17, 18, 25, 26];
        foreach ($guru_menus_crud as $menu_id) {
            $privileges[] = array_merge($base_priv, [
                'role_priv_id' => $id_counter++,
                'role_id' => 2,
                'menu_id' => $menu_id,
                'can_view' => 1,
                'can_create' => 1,
                'can_update' => 1,
                'can_delete' => 1,
            ]);
        }

        // Penilaian Akademik Parent (ID 16) - View Only
        $privileges[] = array_merge($base_priv, [
            'role_priv_id' => $id_counter++,
            'role_id' => 2,
            'menu_id' => 16,
            'can_view' => 1,
        ]);

        // ----------------------------------------------------------------------
        // --- Role 3: Orang Tua (Akses Dashboard dan Menu Tunggal Orang Tua VIEW) ---
        // ----------------------------------------------------------------------

        // Akses Dashboard (ID 1)
        $privileges[] = array_merge($base_priv, [
            'role_priv_id' => $id_counter++,
            'role_id' => 3,
            'menu_id' => 1,
            'can_view' => 1,
        ]);

        // Menu yang dapat diakses Ortu (View Only: ID 19 - 24)
        for ($menu_id = 19; $menu_id <= 24; $menu_id++) {
            $privileges[] = array_merge($base_priv, [
                'role_priv_id' => $id_counter++,
                'role_id' => 3,
                'menu_id' => $menu_id,
                'can_view' => 1,
            ]);
        }

        // === TAMBAHAN BARU UNTUK ORTU (Nilai Siswa) ===
        // Menu Nilai Siswa (ID 32, 33, 34)
        $ortu_new_menus = [32, 33, 34];
        foreach ($ortu_new_menus as $menu_id) {
            $privileges[] = array_merge($base_priv, [
                'role_priv_id' => $id_counter++,
                'role_id' => 3,
                'menu_id' => $menu_id,
                'can_view' => 1, // Ortu hanya bisa melihat
            ]);
        }


        DB::table('role_privilege')->insert($privileges);
    }
}
