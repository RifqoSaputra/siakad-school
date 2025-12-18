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

        // Daftar Nama Siswa Unik (campuran) + jenis kelamin yang ditetapkan dari nama
        $nama_siswa = [
            ['nama' => 'Aura Kasih', 'jenis_kelamin' => 'Perempuan'],
            ['nama' => 'Bima Sakti', 'jenis_kelamin' => 'Laki-laki'],
            ['nama' => 'Cinta Laura', 'jenis_kelamin' => 'Perempuan'],
            ['nama' => 'Dion Agung', 'jenis_kelamin' => 'Laki-laki'],
            ['nama' => 'Elisa Putri', 'jenis_kelamin' => 'Perempuan'],
            ['nama' => 'Fahri Hidayat', 'jenis_kelamin' => 'Laki-laki'],
            ['nama' => 'Gilang Ramadhan', 'jenis_kelamin' => 'Laki-laki'],
            ['nama' => 'Hana Saraswati', 'jenis_kelamin' => 'Perempuan'],
            ['nama' => 'Irfan Bachdim', 'jenis_kelamin' => 'Laki-laki'],
            ['nama' => 'Jessica Mila', 'jenis_kelamin' => 'Perempuan'],
            ['nama' => 'Kevin Sanjaya', 'jenis_kelamin' => 'Laki-laki'],
            ['nama' => 'Lina Marlina', 'jenis_kelamin' => 'Perempuan'],
            ['nama' => 'Maya Estianti', 'jenis_kelamin' => 'Perempuan'],
            ['nama' => 'Nova Eliza', 'jenis_kelamin' => 'Perempuan'],
            ['nama' => 'Oscar Darmawan', 'jenis_kelamin' => 'Laki-laki'],
            ['nama' => 'Putri Ayu', 'jenis_kelamin' => 'Perempuan'],
            ['nama' => 'Rizky Febian', 'jenis_kelamin' => 'Laki-laki'],
            ['nama' => 'Sarah Wijayanto', 'jenis_kelamin' => 'Perempuan'],
            ['nama' => 'Taufik Hidayat', 'jenis_kelamin' => 'Laki-laki'],
            ['nama' => 'Vira Yuniar', 'jenis_kelamin' => 'Perempuan'],
            ['nama' => 'Wulan Guritno', 'jenis_kelamin' => 'Perempuan'],
            ['nama' => 'Xavier Putra', 'jenis_kelamin' => 'Laki-laki'],
            ['nama' => 'Yuni Shara', 'jenis_kelamin' => 'Perempuan'],
            ['nama' => 'Zidan Maulana', 'jenis_kelamin' => 'Laki-laki'],
            ['nama' => 'Adam Jaya', 'jenis_kelamin' => 'Laki-laki'],
            ['nama' => 'Bella Vista', 'jenis_kelamin' => 'Perempuan'],
            ['nama' => 'Cahyo Abadi', 'jenis_kelamin' => 'Laki-laki'],
            ['nama' => 'Diana Safira', 'jenis_kelamin' => 'Perempuan'],
            ['nama' => 'Edo Rahmawan', 'jenis_kelamin' => 'Laki-laki'],
            ['nama' => 'Fani Kusuma', 'jenis_kelamin' => 'Perempuan'],
            ['nama' => 'Gavin Wijaya', 'jenis_kelamin' => 'Laki-laki'],
            ['nama' => 'Hendra Setiawan', 'jenis_kelamin' => 'Laki-laki'],
            ['nama' => 'Indah Permata', 'jenis_kelamin' => 'Perempuan'],
            ['nama' => 'Jaka Samudra', 'jenis_kelamin' => 'Laki-laki'],
            ['nama' => 'Kiki Fatmala', 'jenis_kelamin' => 'Perempuan'],
            ['nama' => 'Lutfi Hakim', 'jenis_kelamin' => 'Laki-laki'],
            ['nama' => 'Mira Hayati', 'jenis_kelamin' => 'Perempuan'],
            ['nama' => 'Nanda Aris', 'jenis_kelamin' => 'Laki-laki'],
            ['nama' => 'Oki Setiana', 'jenis_kelamin' => 'Perempuan'],
            ['nama' => 'Prilly Latuconsina', 'jenis_kelamin' => 'Perempuan'],
            ['nama' => 'Raffi Ahmad', 'jenis_kelamin' => 'Laki-laki'],
            ['nama' => 'Syahrini', 'jenis_kelamin' => 'Perempuan'],
            ['nama' => 'Tegar Satria', 'jenis_kelamin' => 'Laki-laki'],
            ['nama' => 'Umar Bakri', 'jenis_kelamin' => 'Laki-laki'],
            ['nama' => 'Vina Panduwinata', 'jenis_kelamin' => 'Perempuan'],
            ['nama' => 'Wawan Febrianto', 'jenis_kelamin' => 'Laki-laki'],
            ['nama' => 'Yoga Pratama', 'jenis_kelamin' => 'Laki-laki'],
            ['nama' => 'Zaskia Gotik', 'jenis_kelamin' => 'Perempuan'],
            ['nama' => 'Agus Salim', 'jenis_kelamin' => 'Laki-laki'],
            ['nama' => 'Bagus Kuncoro', 'jenis_kelamin' => 'Laki-laki'],
            ['nama' => 'Cindy Fatikasari', 'jenis_kelamin' => 'Perempuan'],
            ['nama' => 'David Wijaya', 'jenis_kelamin' => 'Laki-laki'],
            ['nama' => 'Erlina Sofia', 'jenis_kelamin' => 'Perempuan'],
            ['nama' => 'Ferry Irawan', 'jenis_kelamin' => 'Laki-laki'],
            ['nama' => 'Gita Gutawa', 'jenis_kelamin' => 'Perempuan'],
            ['nama' => 'Hari Santoso', 'jenis_kelamin' => 'Laki-laki'],
            ['nama' => 'Intan Nuraini', 'jenis_kelamin' => 'Perempuan'],
            ['nama' => 'Jonas Rivano', 'jenis_kelamin' => 'Laki-laki'],
            ['nama' => 'Krisdayanti', 'jenis_kelamin' => 'Perempuan'],
            ['nama' => 'Liliyana Natsir', 'jenis_kelamin' => 'Perempuan'],
            ['nama' => 'Marsha Timothy', 'jenis_kelamin' => 'Perempuan'],
            ['nama' => 'Nico Saputra', 'jenis_kelamin' => 'Laki-laki'],
            ['nama' => 'Olivia Jensen', 'jenis_kelamin' => 'Perempuan'],
            ['nama' => 'Pasha Ungu', 'jenis_kelamin' => 'Laki-laki'],
            ['nama' => 'Risa Saraswati', 'jenis_kelamin' => 'Perempuan'],
            ['nama' => 'Slamet Riyadi', 'jenis_kelamin' => 'Laki-laki']
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
                'nama' => $nama_siswa[$i]['nama'],
                'jenis_kelamin' => $nama_siswa[$i]['jenis_kelamin'],
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
