<?php

namespace Database\Seeders\SIAKAD\SCHOOL;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $userEntry = 1;

        $detectGender = function (string $name): string {
            $femaleKeywords = [
                'putri',
                'ayu',
                'sari',
                'indah',
                'mila',
                'elisa',
                'hana',
                'jessica',
                'maya',
                'nova',
                'sarah',
                'vira',
                'wulan',
                'bella',
                'diana',
                'fani',
                'gita',
                'intan',
                'olivia',
                'risa',
                'zaskia'
            ];

            $nameLower = strtolower($name);

            foreach ($femaleKeywords as $keyword) {
                if (str_contains($nameLower, $keyword)) {
                    return 'P';
                }
            }

            return 'L';
        };

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
            'Krisdayanti'
        ];

        $siswa = [];
        $nis_start = 20240001;
        $ortu_id_counter = 1;

        foreach ($nama_siswa as $i => $nama) {

            if ($i > 0 && $i % 6 === 0) {
                $ortu_id_counter++;
            }

            $siswa[] = [
                'id_ortu'        => $ortu_id_counter,
                'nis'            => (string)($nis_start + $i),
                'nama'           => $nama,
                'jenis_kelamin'  => $detectGender($nama),
                'tgl_lahir'      => Carbon::create(
                    rand(2006, 2008),
                    rand(1, 12),
                    rand(1, 28)
                )->toDateString(),
                'agama'          => rand(0, 1) ? 'Islam' : 'Kristen',
                'alamat_rmh'     => 'Jl. Siswa No. ' . ($i + 1),
                'kota_rmh'       => rand(0, 1) ? 'Bandung' : 'Cimahi',
                'status_siswa'   => 'Aktif',
                'user_entry'     => $userEntry,
                'tgl_entry'      => $now,
            ];
        }

        DB::table('siswa')->truncate(); // penting biar clean
        DB::table('siswa')->insert($siswa);
    }
}
