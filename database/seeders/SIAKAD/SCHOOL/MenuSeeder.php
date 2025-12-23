<?php

namespace Database\Seeders\SIAKAD\SCHOOL;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $menus = [];
        $userEntry = 1;

        // Pastikan tabel dikosongkan supaya tidak terjadi duplicate PK
        DB::table('menu')->truncate();

        // ========================
        // ===  ADMIN & UMUM  ===
        // ========================

        // 1. Dashboard (Universal)
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

        // 2. Data Master (Admin parent)
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

        // Children Data Master (parent = 2)
        $menus[] = ['menu_id' => 6, 'parent_id' => 2, 'nama_menu' => 'Data Guru', 'url' => '/admin/guru', 'icon' => 'chalkboard-teacher', 'menu_level' => 2, 'have_child' => 0, 'menu_order' => 1, 'status' => 1, 'user_entry' => $userEntry, 'tgl_entry' => $now];
        $menus[] = ['menu_id' => 7, 'parent_id' => 2, 'nama_menu' => 'Data Siswa', 'url' => '/admin/siswa', 'icon' => 'user-graduate', 'menu_level' => 2, 'have_child' => 0, 'menu_order' => 2, 'status' => 1, 'user_entry' => $userEntry, 'tgl_entry' => $now];
        $menus[] = ['menu_id' => 8, 'parent_id' => 2, 'nama_menu' => 'Mata Pelajaran', 'url' => '/admin/mapel', 'icon' => 'book-open', 'menu_level' => 2, 'have_child' => 0, 'menu_order' => 3, 'status' => 1, 'user_entry' => $userEntry, 'tgl_entry' => $now];
        $menus[] = ['menu_id' => 9, 'parent_id' => 2, 'nama_menu' => 'Data Kelas', 'url' => '/admin/kelas', 'icon' => 'door-closed', 'menu_level' => 2, 'have_child' => 0, 'menu_order' => 4, 'status' => 1, 'user_entry' => $userEntry, 'tgl_entry' => $now];
        $menus[] = [
            'menu_id' => 10, // pastikan ID belum dipakai
            'parent_id' => 2, // Data Master
            'nama_menu' => 'Jadwal Pelajaran',
            'url' => '/admin/jadwal-pelajaran',
            'icon' => 'calendar-alt',
            'menu_level' => 2,
            'have_child' => 0,
            'menu_order' => 5, // setelah Data Kelas
            'status' => 1,
            'user_entry' => $userEntry,
            'tgl_entry' => $now,
        ];

        // 3. Nilai Siswa (Admin parent)
        $menus[] = [
            'menu_id' => 28,
            'parent_id' => null,
            'nama_menu' => 'Nilai Siswa',
            'url' => null,
            'icon' => 'award',
            'menu_level' => 1,
            'have_child' => 1,
            'menu_order' => 3,
            'status' => 1,
            'user_entry' => $userEntry,
            'tgl_entry' => $now,
        ];
        // children
        $menus[] = ['menu_id' => 29, 'parent_id' => 28, 'nama_menu' => 'Nilai Harian', 'url' => '/admin/nilai/harian', 'icon' => 'book', 'menu_level' => 2, 'have_child' => 0, 'menu_order' => 1, 'status' => 1, 'user_entry' => $userEntry, 'tgl_entry' => $now];
        $menus[] = ['menu_id' => 30, 'parent_id' => 28, 'nama_menu' => 'Nilai Ujian', 'url' => '/admin/nilai/ujian', 'icon' => 'pen-alt', 'menu_level' => 2, 'have_child' => 0, 'menu_order' => 2, 'status' => 1, 'user_entry' => $userEntry, 'tgl_entry' => $now];

        // 31. Laporan Rapor (DIUBAH jadi top-level menu, tanpa parent)
        $menus[] = [
            'menu_id' => 31,
            'parent_id' => null,
            'nama_menu' => 'Laporan Rapor',
            'url' => '/admin/laporan/rapor',
            'icon' => 'chart-bar',
            'menu_level' => 1,
            'have_child' => 0,
            'menu_order' => 4,
            'status' => 1,
            'user_entry' => $userEntry,
            'tgl_entry' => $now,
        ];

        // 24. Pengumuman (umum)
        $menus[] = [
            'menu_id' => 24,
            'parent_id' => null,
            'nama_menu' => 'Pengumuman',
            'url' => '/admin/pengumuman',
            'icon' => 'bullhorn',
            'menu_level' => 1,
            'have_child' => 0,
            'menu_order' => 5,
            'status' => 1,
            'user_entry' => $userEntry,
            'tgl_entry' => $now,
        ];

        // ========================
        // ======= GURU ==========
        // ========================
        // (Catatan: sesuai permintaan, HAPUS Jadwal Mengajar (15) & Materi & Tugas (18).)
        // Penilaian Akademik (parent)
        $menus[] = [
            'menu_id' => 16,
            'parent_id' => null,
            'nama_menu' => 'Penilaian Akademik',
            'url' => null,
            'icon' => 'edit',
            'menu_level' => 1,
            'have_child' => 1,
            'menu_order' => 6,
            'status' => 1,
            'user_entry' => $userEntry,
            'tgl_entry' => $now,
        ];
        // children of 16
        $menus[] = ['menu_id' => 25, 'parent_id' => 16, 'nama_menu' => 'Nilai Harian/Tugas', 'url' => '/guru/nilai/harian', 'icon' => 'book', 'menu_level' => 2, 'have_child' => 0, 'menu_order' => 1, 'status' => 1, 'user_entry' => $userEntry, 'tgl_entry' => $now];
        $menus[] = ['menu_id' => 26, 'parent_id' => 16, 'nama_menu' => 'Nilai Ujian (PTS/PAS)', 'url' => '/guru/nilai/ujian', 'icon' => 'pen-alt', 'menu_level' => 2, 'have_child' => 0, 'menu_order' => 2, 'status' => 1, 'user_entry' => $userEntry, 'tgl_entry' => $now];

        // Input Absensi (guru)
        $menus[] = ['menu_id' => 17, 'parent_id' => null, 'nama_menu' => 'Input Absensi Siswa', 'url' => '/guru/absensi/kelas', 'icon' => 'clipboard-check', 'menu_level' => 1, 'have_child' => 0, 'menu_order' => 7, 'status' => 1, 'user_entry' => $userEntry, 'tgl_entry' => $now];

        // Catatan Siswa (Wali Kelas)
        $menus[] = [
            'menu_id' => 35,
            'parent_id' => null,
            'nama_menu' => 'Catatan Siswa',
            'url' => '/guru/rapor/walikelas',
            'icon' => 'user-tie',
            'menu_level' => 1,
            'have_child' => 0,
            'menu_order' => 8,
            'status' => 1,
            'user_entry' => $userEntry,
            'tgl_entry' => $now,
        ];

        // ========================
        // ======= ORTU ===========
        // ========================
        // Per permintaan urutan ORTU: Dashboard (universal id=1), lalu:
        // Info Anak (19), Jadwal Anak (20), Riwayat Absensi (22),
        // Nilai Anak (32 parent with children 33,34), Rapor Anak (21), Pengumuman (24).
        // (INFORMASI KEUANGAN (23) DIHAPUS)

        // Info Anak
        $menus[] = ['menu_id' => 19, 'parent_id' => null, 'nama_menu' => 'Info Anak', 'url' => '/ortu/info-anak', 'icon' => 'child', 'menu_level' => 1, 'have_child' => 0, 'menu_order' => 9, 'status' => 1, 'user_entry' => $userEntry, 'tgl_entry' => $now];

        // Jadwal Anak
        $menus[] = ['menu_id' => 20, 'parent_id' => null, 'nama_menu' => 'Jadwal Anak', 'url' => '/ortu/jadwal', 'icon' => 'calendar-alt', 'menu_level' => 1, 'have_child' => 0, 'menu_order' => 10, 'status' => 1, 'user_entry' => $userEntry, 'tgl_entry' => $now];

        // Riwayat Absensi
        $menus[] = ['menu_id' => 22, 'parent_id' => null, 'nama_menu' => 'Riwayat Absensi', 'url' => '/ortu/absensi', 'icon' => 'user-times', 'menu_level' => 1, 'have_child' => 0, 'menu_order' => 11, 'status' => 1, 'user_entry' => $userEntry, 'tgl_entry' => $now];

        // Nilai Anak (rename dari "Nilai Siswa" untuk ORTU) - parent
        $menus[] = [
            'menu_id' => 32,
            'parent_id' => null,
            'nama_menu' => 'Nilai Anak',
            'url' => null,
            'icon' => 'clipboard-list',
            'menu_level' => 1,
            'have_child' => 1,
            'menu_order' => 12,
            'status' => 1,
            'user_entry' => $userEntry,
            'tgl_entry' => $now,
        ];
        // children of 32
        $menus[] = ['menu_id' => 33, 'parent_id' => 32, 'nama_menu' => 'Nilai Harian', 'url' => '/ortu/nilai-siswa/harian', 'icon' => 'book', 'menu_level' => 2, 'have_child' => 0, 'menu_order' => 1, 'status' => 1, 'user_entry' => $userEntry, 'tgl_entry' => $now];
        $menus[] = ['menu_id' => 34, 'parent_id' => 32, 'nama_menu' => 'Nilai Ujian', 'url' => '/ortu/nilai-siswa/ujian', 'icon' => 'pen-alt', 'menu_level' => 2, 'have_child' => 0, 'menu_order' => 2, 'status' => 1, 'user_entry' => $userEntry, 'tgl_entry' => $now];

        // Rapor Anak
        $menus[] = ['menu_id' => 21, 'parent_id' => null, 'nama_menu' => 'Rapor Anak', 'url' => '/ortu/rapor', 'icon' => 'chart-bar', 'menu_level' => 1, 'have_child' => 0, 'menu_order' => 13, 'status' => 1, 'user_entry' => $userEntry, 'tgl_entry' => $now];

        // Note: Pengumuman (24) already added above (menu_order 5) - it's global

        // Insert semua
        DB::table('menu')->insert($menus);
    }
}
