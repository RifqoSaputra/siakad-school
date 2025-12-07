<?php

namespace Database\Seeders\SIAKAD\SCHOOL;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $menus = [];
        $userEntry = 1; // User ID 1: superadmin

        // Kosongkan tabel menu
        DB::table('menu')->truncate();

        // ===================================
        // --- 1. Menu Level 1 & 2 (ADMIN) ---
        // ===================================

        // 1. Dashboard (Universal) - Order 1
        $menus[] = [
            'menu_id' => 1,
            'parent_id' => null,
            'nama_menu' => 'Dashboard',
            'url' => '/dashboard',
            'icon' => 'tachometer-alt',
            'menu_level' => 1,
            'have_child' => 0,
            'menu_order' => 1,
            'status' => 1,
            'user_entry' => $userEntry,
            'tgl_entry' => $now,
        ];

        // 2. Data Master (Parent - ADMIN) - Order 2
        $menus[] = [
            'menu_id' => 2,
            'parent_id' => null,
            'nama_menu' => 'Data Master',
            'url' => null,
            'icon' => 'database',
            'menu_level' => 1,
            'have_child' => 1,
            'menu_order' => 2,
            'status' => 1,
            'user_entry' => $userEntry,
            'tgl_entry' => $now,
        ];

        // 4. Manajemen User (Parent - ADMIN) - Order 3
        $menus[] = [
            'menu_id' => 4,
            'parent_id' => null,
            'nama_menu' => 'Manajemen User',
            'url' => null,
            'icon' => 'users-cog',
            'menu_level' => 1,
            'have_child' => 1,
            'menu_order' => 3,
            'status' => 1,
            'user_entry' => $userEntry,
            'tgl_entry' => $now,
        ];

        // 28. Nilai Siswa (Parent Baru untuk Admin) - Order 4
        $menus[] = [
            'menu_id' => 28, // ID Baru
            'parent_id' => null,
            'nama_menu' => 'Nilai Siswa',
            'url' => null,
            'icon' => 'award',
            'menu_level' => 1,
            'have_child' => 1,
            'menu_order' => 4,
            'status' => 1,
            'user_entry' => $userEntry,
            'tgl_entry' => $now,
        ];

        // 5. Laporan (Parent - ADMIN) - Order 5
        $menus[] = [
            'menu_id' => 5,
            'parent_id' => null,
            'nama_menu' => 'Laporan',
            'url' => null,
            'icon' => 'file-alt',
            'menu_level' => 1,
            'have_child' => 1,
            'menu_order' => 5,
            'status' => 1,
            'user_entry' => $userEntry,
            'tgl_entry' => $now,
        ];

        // 24. Pengumuman (Menu Level 1 Admin/Guru/Ortu) - Order 6
        $menus[] = [
            'menu_id' => 24,
            'parent_id' => null,
            'nama_menu' => 'Pengumuman',
            'url' => '/admin/pengumuman', // URL utama Admin
            'icon' => 'bullhorn',
            'menu_level' => 1,
            'have_child' => 0,
            'menu_order' => 6,
            'status' => 1,
            'user_entry' => $userEntry,
            'tgl_entry' => $now,
        ];


        // Children Data Master (Parent 2)
        $menus[] = ['menu_id' => 6, 'parent_id' => 2, 'nama_menu' => 'Data Guru', 'url' => '/admin/guru', 'icon' => 'chalkboard-teacher', 'menu_level' => 2, 'have_child' => 0, 'menu_order' => 1, 'status' => 1, 'user_entry' => $userEntry, 'tgl_entry' => $now,];
        $menus[] = ['menu_id' => 7, 'parent_id' => 2, 'nama_menu' => 'Data Siswa', 'url' => '/admin/siswa', 'icon' => 'user-graduate', 'menu_level' => 2, 'have_child' => 0, 'menu_order' => 2, 'status' => 1, 'user_entry' => $userEntry, 'tgl_entry' => $now,];
        $menus[] = ['menu_id' => 8, 'parent_id' => 2, 'nama_menu' => 'Mata Pelajaran', 'url' => '/admin/mapel', 'icon' => 'book-open', 'menu_level' => 2, 'have_child' => 0, 'menu_order' => 3, 'status' => 1, 'user_entry' => $userEntry, 'tgl_entry' => $now,];
        $menus[] = ['menu_id' => 9, 'parent_id' => 2, 'nama_menu' => 'Data Kelas', 'url' => '/admin/kelas', 'icon' => 'door-closed', 'menu_level' => 2, 'have_child' => 0, 'menu_order' => 4, 'status' => 1, 'user_entry' => $userEntry, 'tgl_entry' => $now,];

        // Children Manajemen User (Parent 4)
        $menus[] = ['menu_id' => 12, 'parent_id' => 4, 'nama_menu' => 'Manajemen Role', 'url' => '/admin/manajemen/role', 'icon' => 'user-tag', 'menu_level' => 2, 'have_child' => 0, 'menu_order' => 1, 'status' => 1, 'user_entry' => $userEntry, 'tgl_entry' => $now,];
        $menus[] = ['menu_id' => 13, 'parent_id' => 4, 'nama_menu' => 'Manajemen Menu', 'url' => '/admin/manajemen/menu', 'icon' => 'list-ul', 'menu_level' => 2, 'have_child' => 0, 'menu_order' => 2, 'status' => 1, 'user_entry' => $userEntry, 'tgl_entry' => $now,];

        // Children Nilai Siswa (Parent 28)
        $menus[] = ['menu_id' => 29, 'parent_id' => 28, 'nama_menu' => 'Nilai Harian', 'url' => '/admin/nilai/harian', 'icon' => 'book', 'menu_level' => 2, 'have_child' => 0, 'menu_order' => 1, 'status' => 1, 'user_entry' => $userEntry, 'tgl_entry' => $now,];
        $menus[] = ['menu_id' => 30, 'parent_id' => 28, 'nama_menu' => 'Nilai Ujian', 'url' => '/admin/nilai/ujian', 'icon' => 'pen-alt', 'menu_level' => 2, 'have_child' => 0, 'menu_order' => 2, 'status' => 1, 'user_entry' => $userEntry, 'tgl_entry' => $now,];

        // Children Laporan (Parent 5)
        $menus[] = ['menu_id' => 14, 'parent_id' => 5, 'nama_menu' => 'Laporan Absensi Siswa', 'url' => '/admin/laporan/absensi', 'icon' => 'clipboard-check', 'menu_level' => 2, 'have_child' => 0, 'menu_order' => 1, 'status' => 1, 'user_entry' => $userEntry, 'tgl_entry' => $now,];
        $menus[] = ['menu_id' => 31, 'parent_id' => 5, 'nama_menu' => 'Laporan Rapor', 'url' => '/admin/laporan/rapor', 'icon' => 'chart-bar', 'menu_level' => 2, 'have_child' => 0, 'menu_order' => 2, 'status' => 1, 'user_entry' => $userEntry, 'tgl_entry' => $now,];


        // ===========================================
        // --- 3. Menu Level 1 (Menu Tunggal GURU) ---
        // Urutan menu Guru dimulai dari Order 7, mengikuti Admin
        // ===========================================

        // 15. Jadwal Mengajar 
        $menus[] = ['menu_id' => 15, 'parent_id' => null, 'nama_menu' => 'Jadwal Mengajar', 'url' => '/guru/jadwal', 'icon' => 'calendar-check', 'menu_level' => 1, 'have_child' => 0, 'menu_order' => 7, 'status' => 1, 'user_entry' => $userEntry, 'tgl_entry' => $now,];

        // 16. Penilaian Akademik (Parent Baru)
        $menus[] = [
            'menu_id' => 16,
            'parent_id' => null,
            'nama_menu' => 'Penilaian Akademik',
            'url' => null,
            'icon' => 'edit',
            'menu_level' => 1,
            'have_child' => 1,
            'menu_order' => 8,
            'status' => 1,
            'user_entry' => $userEntry,
            'tgl_entry' => $now,
        ];

        // 17. Input Absensi
        $menus[] = ['menu_id' => 17, 'parent_id' => null, 'nama_menu' => 'Input Absensi Siswa', 'url' => '/guru/absensi/kelas', 'icon' => 'clipboard-check', 'menu_level' => 1, 'have_child' => 0, 'menu_order' => 9, 'status' => 1, 'user_entry' => $userEntry, 'tgl_entry' => $now,];

        // 18. Materi & Tugas
        $menus[] = ['menu_id' => 18, 'parent_id' => null, 'nama_menu' => 'Materi & Tugas', 'url' => '/guru/materi', 'icon' => 'cloud-upload-alt', 'menu_level' => 1, 'have_child' => 0, 'menu_order' => 10, 'status' => 1, 'user_entry' => $userEntry, 'tgl_entry' => $now,];

        // 25. Input Nilai Harian/Tugas (Child dari 16)
        $menus[] = [
            'menu_id' => 25,
            'parent_id' => 16,
            'nama_menu' => 'Nilai Harian/Tugas',
            'url' => '/guru/nilai/harian',
            'icon' => 'book',
            'menu_level' => 2,
            'have_child' => 0,
            'menu_order' => 1,
            'status' => 1,
            'user_entry' => $userEntry,
            'tgl_entry' => $now,
        ];

        // 26. Input Nilai Ujian (PTS/PAS) (Child dari 16)
        $menus[] = [
            'menu_id' => 26,
            'parent_id' => 16,
            'nama_menu' => 'Nilai Ujian (PTS/PAS)',
            'url' => '/guru/nilai/ujian',
            'icon' => 'pen-alt',
            'menu_level' => 2,
            'have_child' => 0,
            'menu_order' => 2,
            'status' => 1,
            'user_entry' => $userEntry,
            'tgl_entry' => $now,
        ];

        // ===========================================
        // --- 4. Menu Level 1 (Menu Tunggal ORTU) ---
        // Urutan menu Ortu dimulai dari Order 11, mengikuti Guru
        // ===========================================

        // 19. Info Anak
        $menus[] = ['menu_id' => 19, 'parent_id' => null, 'nama_menu' => 'Info Anak', 'url' => '/ortu/info-anak', 'icon' => 'child', 'menu_level' => 1, 'have_child' => 0, 'menu_order' => 11, 'status' => 1, 'user_entry' => $userEntry, 'tgl_entry' => $now,];
        // 20. Jadwal Anak
        $menus[] = ['menu_id' => 20, 'parent_id' => null, 'nama_menu' => 'Jadwal Anak', 'url' => '/ortu/jadwal', 'icon' => 'calendar-alt', 'menu_level' => 1, 'have_child' => 0, 'menu_order' => 12, 'status' => 1, 'user_entry' => $userEntry, 'tgl_entry' => $now,];
        // 21. Rapor Anak
        $menus[] = ['menu_id' => 21, 'parent_id' => null, 'nama_menu' => 'Rapor Anak', 'url' => '/ortu/rapor', 'icon' => 'chart-bar', 'menu_level' => 1, 'have_child' => 0, 'menu_order' => 13, 'status' => 1, 'user_entry' => $userEntry, 'tgl_entry' => $now,];
        // 22. Riwayat Absensi (Diasumsikan /ortu/absensi)
        $menus[] = ['menu_id' => 22, 'parent_id' => null, 'nama_menu' => 'Riwayat Absensi', 'url' => '/ortu/absensi', 'icon' => 'user-times', 'menu_level' => 1, 'have_child' => 0, 'menu_order' => 14, 'status' => 1, 'user_entry' => $userEntry, 'tgl_entry' => $now,];
        // 23. Informasi Keuangan (Diasumsikan /ortu/keuangan)
        $menus[] = ['menu_id' => 23, 'parent_id' => null, 'nama_menu' => 'Informasi Keuangan', 'url' => '/ortu/keuangan', 'icon' => 'money-bill-wave', 'menu_level' => 1, 'have_child' => 0, 'menu_order' => 15, 'status' => 1, 'user_entry' => $userEntry, 'tgl_entry' => $now,];

        // === TAMBAHAN BARU UNTUK ORTU ===

        // 32. Nilai Siswa (Parent Baru untuk Ortu) - Order 16
        $menus[] = [
            'menu_id' => 32, // ID Baru
            'parent_id' => null,
            'nama_menu' => 'Nilai Siswa',
            'url' => null,
            'icon' => 'clipboard-list',
            'menu_level' => 1,
            'have_child' => 1,
            'menu_order' => 16,
            'status' => 1,
            'user_entry' => $userEntry,
            'tgl_entry' => $now,
        ];

        // Children Nilai Siswa (Parent 32)
        // 33. Nilai Harian
        $menus[] = [
            'menu_id' => 33,
            'parent_id' => 32,
            'nama_menu' => 'Nilai Harian',
            'url' => '/ortu/nilai-siswa/harian', // Sesuai views/dashboard/ortu/nilai-siswa/nilai-harian.blade
            'icon' => 'book',
            'menu_level' => 2,
            'have_child' => 0,
            'menu_order' => 1,
            'status' => 1,
            'user_entry' => $userEntry,
            'tgl_entry' => $now,
        ];

        // 34. Nilai Ujian
        $menus[] = [
            'menu_id' => 34,
            'parent_id' => 32,
            'nama_menu' => 'Nilai Ujian',
            'url' => '/ortu/nilai-siswa/ujian', // Sesuai views/dashboard/ortu/nilai-siswa/nilai-ujian.blade
            'icon' => 'pen-alt',
            'menu_level' => 2,
            'have_child' => 0,
            'menu_order' => 2,
            'status' => 1,
            'user_entry' => $userEntry,
            'tgl_entry' => $now,
        ];

        // 24. Pengumuman (sudah didefinisikan di atas)
        // Menggeser Pengumuman (ID 24) ke bagian bawah untuk konsistensi di Ortu (Order 17)
        // Saya akan menambahkan kembali Pengumuman untuk Ortu di urutan terakhir, walaupun sudah ada di atas.

        // Perlu dipastikan Pengumuman (ID 24) juga di-insert untuk Ortu di RolePrivilege, 
        // tapi karena sudah didefinisikan di atas dengan order 6, saya akan mengandalkan RolePrivilege Seeder.


        // Jalankan insert
        DB::table('menu')->insert($menus);
    }
}
