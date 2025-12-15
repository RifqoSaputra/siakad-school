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
        // DIHAPUS: 4,12,13,5,14,23 sesuai permintaan (manajemen user, laporan parent & child 14, info keuangan)
        $admin_menu_ids = [
            1, // Dashboard
            2, // Data Master (Parent)
            6,
            7,
            8,
            9, // Data Master Children
            28, // Nilai Siswa (Parent)
            29,
            30, // Nilai Siswa Children
            31, // Laporan Rapor (sekarang top-level)
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
        $view_only_menus_guru = [6, 8, 9];
        foreach ($view_only_menus_guru as $menu_id) {
            $privileges[] = array_merge($base_priv, [
                'role_priv_id' => $id_counter++,
                'role_id' => 2,
                'menu_id' => $menu_id,
                'can_view' => 1,
            ]);
        }

        // Akses Pengumuman (ID 24) - Guru boleh lihat
        $privileges[] = array_merge($base_priv, [
            'role_priv_id' => $id_counter++,
            'role_id' => 2,
            'menu_id' => 24,
            'can_view' => 1,
        ]);

        // Akses CRUD untuk menu-menu GURU yang tersisa:
        // (HAPUS 15, 18 sesuai permintaan)
        $guru_menus_crud = [17, 25, 26]; // 17 = input absensi; 25/26 = nilai anak
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

        // Catatan Siswa (35) untuk Guru (wali kelas) - CRUD (tidak delete)
        $privileges[] = array_merge($base_priv, [
            'role_priv_id' => $id_counter++,
            'role_id' => 2,
            'menu_id' => 35,
            'can_view' => 1,
            'can_create' => 1,
            'can_update' => 1,
            'can_delete' => 0,
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

        // ORTU: hanya akses ke menu tertentu — sesuai permintaan.
        $ortu_menus = [19, 20, 21, 22, 24]; // Info Anak, Jadwal, Rapor, Riwayat Absensi, Pengumuman (tanpa 23)
        foreach ($ortu_menus as $menu_id) {
            $privileges[] = array_merge($base_priv, [
                'role_priv_id' => $id_counter++,
                'role_id' => 3,
                'menu_id' => $menu_id,
                'can_view' => 1,
            ]);
        }

        // ORTU: akses Nilai Anak (32) dan children (33,34) - view only
        $ortu_new_menus = [32, 33, 34];
        foreach ($ortu_new_menus as $menu_id) {
            $privileges[] = array_merge($base_priv, [
                'role_priv_id' => $id_counter++,
                'role_id' => 3,
                'menu_id' => $menu_id,
                'can_view' => 1,
            ]);
        }

        DB::table('role_privilege')->insert($privileges);
    }
}
