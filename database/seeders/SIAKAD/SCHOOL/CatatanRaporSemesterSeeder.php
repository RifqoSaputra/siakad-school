<?php

namespace Database\Seeders\SIAKAD\SCHOOL;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CatatanRaporSemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Definisikan Data Umum
        $now = Carbon::now();
        $userEntry = 1; // ID User Superadmin (Asumsi: User ID 1 adalah Superadmin)
        $tahunAjaran = '2024/2025';
        $semester = 'Ganjil';
        $totalSiswa = 60;
        $siswaPerKelas = 10;
        $catatanRapor = [];
        $id_counter = 1;

        // 2. Definisikan Template Catatan Wali Kelas (Sesuai Permintaan Anda)
        $templateCatatan = [
            'Sangat Baik' => 'Secara umum, sikap dan perilaku Ananda sangat baik. Pertahankan terus kejujuran, kedisiplinan, dan tanggung jawabnya di tahun ajaran berikutnya.',
            'Baik' => 'Sikap Ananda sudah baik, namun perlu ditingkatkan lagi dalam hal inisiatif dan tanggung jawab terhadap tugas. Kami berharap Ananda lebih aktif di kegiatan sekolah.',
            'Cukup' => 'Perlu perhatian khusus terhadap kedisiplinan dan fokus belajar. Kami sarankan orang tua dan sekolah bekerja sama untuk membantu Ananda mencapai potensi maksimalnya.',
            'Kurang' => 'Terdapat beberapa catatan serius mengenai kehadiran dan kepatuhan terhadap peraturan sekolah. Kami mohon adanya peningkatan drastis dalam perilaku Ananda.',
        ];

        // 3. Looping untuk Setiap Siswa (id_siswa 1 hingga 60)
        for ($siswa_id = 1; $siswa_id <= $totalSiswa; $siswa_id++) {
            // Menentukan Kelas ID (Kelas 1 hingga 6)
            $kelas_id = ceil($siswa_id / $siswaPerKelas);

            // Menentukan Walikelas ID (Berdasarkan KelasSeeder, walikelas_id = kelas_id)
            // Walikelas ID (yang merujuk ke users.id)
            // Asumsi: users.id 1-6 adalah guru walikelas
            $wali_kelas_id = $kelas_id;

            // Menentukan Predikat Sikap secara acak
            $predikatSikapList = ['Sangat Baik', 'Baik', 'Cukup'];
            // Siswa 5 dan 55 dibuat Kurang untuk uji coba
            if ($siswa_id == 5 || $siswa_id == 55) {
                $predikatSikap = 'Kurang';
            } else {
                $predikatSikap = $predikatSikapList[array_rand($predikatSikapList)];
            }

            // Mengambil catatan berdasarkan predikat
            $catatanWalikelas = $templateCatatan[$predikatSikap];

            $catatanRapor[] = [
                'id' => $id_counter++,
                'id_siswa' => $siswa_id,
                'kelas_id' => $kelas_id,
                'semester' => $semester,
                'tahun_ajaran' => $tahunAjaran,
                'wali_kelas_id' => $wali_kelas_id,
                'status_publikasi' => 'Draft',
                'predikat_sikap' => $predikatSikap,
                'catatan_walikelas' => $catatanWalikelas,
                'status_kenaikan' => 'Belum Final',
                'user_entry' => $userEntry,
                'tgl_entry' => $now,
                'user_update' => $userEntry,
                'tgl_update' => $now,
            ];
        }

        // 4. Masukkan data ke database
        DB::table('catatan_rapor_semester')->insert($catatanRapor);
    }
}
