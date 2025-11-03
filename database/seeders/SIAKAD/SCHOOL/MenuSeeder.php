<?php

namespace Database\Seeders\SIAKAD\SCHOOL;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('menu')->truncate();

        // Menu ID 1 - 3: Admin (Level 1 & 2)
        // Menu ID 4 - 6: Guru (Level 1 & 2)
        // Menu ID 7 - 8: Ortu (Level 1)

        $menus = [
            // --- ADMIN MENU ---
            [ // 1. Parent: Data Master
                'menu_id' => 1, 'nama_menu' => 'Data Master', 'url' => '#', 
                'icon' => 'bi-folder', 'menu_level' => 1, 'have_child' => 1, 'menu_order' => 10, 'parent_id' => null,
            ],
            [ // 2. Child: Manajemen Guru
                'menu_id' => 2, 'nama_menu' => 'Manajemen Guru', 'url' => 'admin/guru', 
                'icon' => 'bi-person-badge-fill', 'menu_level' => 2, 'have_child' => 0, 'menu_order' => 11, 'parent_id' => 1,
            ],
            [ // 3. Child: Manajemen Siswa
                'menu_id' => 3, 'nama_menu' => 'Manajemen Siswa', 'url' => 'admin/siswa', 
                'icon' => 'bi-person-lines-fill', 'menu_level' => 2, 'have_child' => 0, 'menu_order' => 12, 'parent_id' => 1,
            ],
            
            // --- GURU MENU ---
            [ // 4. Parent: Akademik
                'menu_id' => 4, 'nama_menu' => 'Akademik Guru', 'url' => '#', 
                'icon' => 'bi-book-half', 'menu_level' => 1, 'have_child' => 1, 'menu_order' => 20, 'parent_id' => null,
            ],
            [ // 5. Child: Input Nilai
                'menu_id' => 5, 'nama_menu' => 'Input Nilai', 'url' => 'guru/nilai', 
                'icon' => 'bi-journal-check', 'menu_level' => 2, 'have_child' => 0, 'menu_order' => 21, 'parent_id' => 4,
            ],
            [ // 6. Child: Jadwal Ajar
                'menu_id' => 6, 'nama_menu' => 'Jadwal Ajar', 'url' => 'guru/jadwal', 
                'icon' => 'bi-calendar-week', 'menu_level' => 2, 'have_child' => 0, 'menu_order' => 22, 'parent_id' => 4,
            ],

            // --- ORTU MENU ---
            [ // 7. Menu Tunggal: Informasi Anak
                'menu_id' => 7, 'nama_menu' => 'Informasi Anak', 'url' => 'ortu/info-anak', 
                'icon' => 'bi-person-lines-fill', 'menu_level' => 1, 'have_child' => 0, 'menu_order' => 30, 'parent_id' => null,
            ],
            [ // 8. Menu Tunggal: Rapor Online
                'menu_id' => 8, 'nama_menu' => 'Rapor Online', 'url' => 'ortu/rapor', 
                'icon' => 'bi-file-earmark-bar-graph', 'menu_level' => 1, 'have_child' => 0, 'menu_order' => 31, 'parent_id' => null,
            ],
            
            // Tambahkan menu lainnya jika diperlukan
        ];

        // Pastikan tgl_entry dan status diisi untuk setiap menu
        $finalMenus = array_map(function($menu) {
            $menu['tgl_entry'] = now();
            $menu['status'] = 1;
            return $menu;
        }, $menus);

        DB::table('menu')->insert($finalMenus);
    }
}