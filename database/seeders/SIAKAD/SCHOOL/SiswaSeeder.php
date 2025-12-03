<?php

namespace Database\Seeders\SIAKAD\SCHOOL;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $userEntry = 1; // Superadmin
        $siswa = [];
        $nis_start = 20240001; // NIS awal
        $ortu_id_counter = 1; // Mulai dari Ortu ID 1

        // Daftar Nama Siswa Unik (campuran)
        $nama_siswa = [
            'Aura Kasih',
            'Bima Sakti',
            'Cinta Laura',
            'Dion Agung',
            'Elisa Putri',
            'Fahri Hidayat',
            'Gilang Ramadhan',
            'Hana Saraswati',
            'Irfan Bachdim',
            'Jessica Mila',
            'Kevin Sanjaya',
            'Lina Marlina',
            'Maya Estianti',
            'Nova Eliza',
            'Oscar Darmawan',
            'Putri Ayu',
            'Rizky Febian',
            'Sarah Wijayanto',
            'Taufik Hidayat',
            'Vira Yuniar',
            'Wulan Guritno',
            'Xavier Putra',
            'Yuni Shara',
            'Zidan Maulana',
            'Adam Jaya',
            'Bella Vista',
            'Cahyo Abadi',
            'Diana Safira',
            'Edo Rahmawan',
            'Fani Kusuma',
            'Gavin Wijaya',
            'Hendra Setiawan',
            'Indah Permata',
            'Jaka Samudra',
            'Kiki Fatmala',
            'Lutfi Hakim',
            'Mira Hayati',
            'Nanda Aris',
            'Oki Setiana',
            'Prilly Latuconsina',
            'Raffi Ahmad',
            'Syahrini',
            'Tegar Satria',
            'Umar Bakri',
            'Vina Panduwinata',
            'Wawan Febrianto',
            'Yoga Pratama',
            'Zaskia Gotik',
            'Agus Salim',
            'Bagus Kuncoro',
            'Cindy Fatikasari',
            'David Wijaya',
            'Erlina Sofia',
            'Ferry Irawan',
            'Gita Gutawa',
            'Hari Santoso',
            'Intan Nuraini',
            'Jonas Rivano',
            'Krisdayanti',
            'Liliyana Natsir',
            'Marsha Timothy',
            'Nico Saputra',
            'Olivia Jensen',
            'Pasha Ungu',
            'Risa Saraswati',
            'Slamet Riyadi'
        ];

        // Looping 60 siswa
        for ($i = 0; $i < 60; $i++) {
            // Setelah 6 siswa, ganti ke Ortu berikutnya
            if ($i > 0 && $i % 6 == 0) {
                $ortu_id_counter++;
            }

            // Atur Tanggal Lahir (17-19 tahun untuk SMA/SMK)
            $birthYear = rand(2006, 2008);
            $birthMonth = rand(1, 12);
            $birthDay = rand(1, 28);
            $tgl_lahir = Carbon::create($birthYear, $birthMonth, $birthDay)->toDateString();

            $siswa[] = [
                'id_siswa' => $i + 1,
                'id_ortu' => $ortu_id_counter, // FK ke Ortu
                'nis' => (string)($nis_start + $i),
                'nama' => $nama_siswa[$i],
                'tgl_lahir' => $tgl_lahir,
                'agama' => rand(0, 1) ? 'Islam' : 'Kristen',
                'alamat_rmh' => 'Jl. Siswa No. ' . ($i + 1),
                'kota_rmh' => rand(0, 1) ? 'Bandung' : 'Cimahi',
                'user_entry' => $userEntry,
                'tgl_entry' => $now,
            ];
        }

        DB::table('siswa')->insert($siswa);
    }
}
