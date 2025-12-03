<?php

namespace Database\Seeders\SIAKAD\SCHOOL;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GuruMapelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $userEntry = 1; // Superadmin
        $id_counter = 1;

        // --- DEFINISI TAHUN AJARAN & SEMESTER AKTIF ---
        $tahunAjaran = '2024/2025';
        $semester = 'Ganjil';
        // -----------------------------------------------------------

        // Asumsi ID Mapel: 1-4 (Umum), 5-7 (DKV), 8-9 (AKL), 10 (Kwh)
        // Asumsi ID Guru: 1-8

        $guru_mapel = [
            // Guru 1 (Walikelas DKV-10, Guru Bahasa Indonesia)
            ['guru_mapel_id' => $id_counter++, 'id_guru' => 1, 'mapel_id' => 1, 'tahun_ajaran' => $tahunAjaran, 'semester' => $semester, 'user_entry' => $userEntry, 'tgl_entry' => $now], // Bahasa Indonesia
            ['guru_mapel_id' => $id_counter++, 'id_guru' => 1, 'mapel_id' => 10, 'tahun_ajaran' => $tahunAjaran, 'semester' => $semester, 'user_entry' => $userEntry, 'tgl_entry' => $now], // Kewirausahaan

            // Guru 2 (Walikelas DKV-11, Guru Desain Grafis)
            ['guru_mapel_id' => $id_counter++, 'id_guru' => 2, 'mapel_id' => 5, 'tahun_ajaran' => $tahunAjaran, 'semester' => $semester, 'user_entry' => $userEntry, 'tgl_entry' => $now], // Desain Grafis Percetakan
            ['guru_mapel_id' => $id_counter++, 'id_guru' => 2, 'mapel_id' => 6, 'tahun_ajaran' => $tahunAjaran, 'semester' => $semester, 'user_entry' => $userEntry, 'tgl_entry' => $now], // Produksi Video

            // Guru 3 (Walikelas DKV-12, Guru Fotografi)
            ['guru_mapel_id' => $id_counter++, 'id_guru' => 3, 'mapel_id' => 7, 'tahun_ajaran' => $tahunAjaran, 'semester' => $semester, 'user_entry' => $userEntry, 'tgl_entry' => $now], // Fotografi Dasar
            ['guru_mapel_id' => $id_counter++, 'id_guru' => 3, 'mapel_id' => 3, 'tahun_ajaran' => $tahunAjaran, 'semester' => $semester, 'user_entry' => $userEntry, 'tgl_entry' => $now], // Bahasa Inggris

            // Guru 4 (Walikelas AKL-10, Guru Akuntansi)
            ['guru_mapel_id' => $id_counter++, 'id_guru' => 4, 'mapel_id' => 8, 'tahun_ajaran' => $tahunAjaran, 'semester' => $semester, 'user_entry' => $userEntry, 'tgl_entry' => $now], // Akuntansi Keuangan Dasar
            ['guru_mapel_id' => $id_counter++, 'id_guru' => 4, 'mapel_id' => 9, 'tahun_ajaran' => $tahunAjaran, 'semester' => $semester, 'user_entry' => $userEntry, 'tgl_entry' => $now], // Administrasi Perpajakan

            // Guru 5 (Walikelas AKL-11, Guru Matematika)
            ['guru_mapel_id' => $id_counter++, 'id_guru' => 5, 'mapel_id' => 2, 'tahun_ajaran' => $tahunAjaran, 'semester' => $semester, 'user_entry' => $userEntry, 'tgl_entry' => $now], // Matematika Dasar
            ['guru_mapel_id' => $id_counter++, 'id_guru' => 5, 'mapel_id' => 8, 'tahun_ajaran' => $tahunAjaran, 'semester' => $semester, 'user_entry' => $userEntry, 'tgl_entry' => $now], // Akuntansi Keuangan (Bantu)

            // Guru 6 (Walikelas AKL-12, Guru Agama)
            ['guru_mapel_id' => $id_counter++, 'id_guru' => 6, 'mapel_id' => 4, 'tahun_ajaran' => $tahunAjaran, 'semester' => $semester, 'user_entry' => $userEntry, 'tgl_entry' => $now], // Pendidikan Agama Islam

            // Guru 7 (Guru Bahasa Indonesia & Inggris Tambahan)
            ['guru_mapel_id' => $id_counter++, 'id_guru' => 7, 'mapel_id' => 1, 'tahun_ajaran' => $tahunAjaran, 'semester' => $semester, 'user_entry' => $userEntry, 'tgl_entry' => $now], // Bahasa Indonesia

            // Guru 8 (Guru DKV Tambahan)
            ['guru_mapel_id' => $id_counter++, 'id_guru' => 8, 'mapel_id' => 5, 'tahun_ajaran' => $tahunAjaran, 'semester' => $semester, 'user_entry' => $userEntry, 'tgl_entry' => $now], // Desain Grafis Percetakan
            ['guru_mapel_id' => $id_counter++, 'id_guru' => 8, 'mapel_id' => 6, 'tahun_ajaran' => $tahunAjaran, 'semester' => $semester, 'user_entry' => $userEntry, 'tgl_entry' => $now], // Produksi Video
        ];

        DB::table('guru_mapel')->insert($guru_mapel);
    }
}
