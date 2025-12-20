<?php

namespace Database\Seeders\SIAKAD\SCHOOL;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema; // Tambahkan ini

class NilaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // >>> SOLUSI ERROR FOREIGN KEY: Matikan pemeriksaan FK sementara
        Schema::disableForeignKeyConstraints();

        // TRUNCATE SEMUA TABEL NILAI (Menghapus data lama secara total)
        // Urutan sekarang tidak masalah
        DB::table('nilai_tambahan_siswa')->truncate();
        DB::table('nilai_tambahan')->truncate();
        DB::table('nilai_ujian_siswa')->truncate();
        DB::table('nilai_ujian')->truncate();

        // >>> HIDUPKAN KEMBALI pemeriksaan FK
        Schema::enableForeignKeyConstraints();

        // Panggil Seeder Anak untuk memasukkan data baru
        $this->call([
            NilaiTambahanSeeder::class,
            NilaiTambahanSiswaSeeder::class,
            NilaiUjianSeeder::class,
            NilaiUjianSiswaSeeder::class,
        ]);
    }
}
