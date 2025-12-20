<?php

namespace Database\Seeders\SIAKAD\SCHOOL;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NilaiAkhirSemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $userEntry = 1; // Superadmin
        $tahunAjaran = '2024/2025';
        $semester = 'Ganjil';

        // Ambil semua siswa_kelas aktif
        $siswaKelasList = DB::table('siswa_kelas')
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('status', 1)
            ->get();

        // Ambil semua mapel aktif
        $mapelList = DB::table('mapel')->where('status', 1)->get();

        $nilaiAkhir = [];
        $idCounter = 1;

        foreach ($siswaKelasList as $siswaKelas) {
            foreach ($mapelList as $mapel) {
                // Generate nilai rapor random 70-100
                $nilaiRapor = rand(70, 100) + (rand(0, 9) / 10);

                // KKM default 75
                $kkm = 75;

                // Deskripsi berdasarkan nilai
                if ($nilaiRapor >= 90) {
                    $deskripsi = "Sangat Baik dalam memahami materi.";
                } elseif ($nilaiRapor >= 75) {
                    $deskripsi = "Baik dalam memahami materi.";
                } else {
                    $deskripsi = "Perlu peningkatan pemahaman.";
                }

                $nilaiAkhir[] = [
                    'id' => $idCounter++,
                    'id_siswa' => $siswaKelas->id_siswa,
                    'mapel_id' => $mapel->mapel_id,
                    'kelas_id' => $siswaKelas->kelas_id,
                    'semester' => $semester,
                    'tahun_ajaran' => $tahunAjaran,
                    'nilai_rapor' => $nilaiRapor,
                    'kkm' => $kkm,
                    'deskripsi' => $deskripsi,
                    'user_entry' => $userEntry,
                    'tgl_entry' => $now,
                    'user_update' => $userEntry,
                    'tgl_update' => $now,
                ];
            }
        }

        DB::table('nilai_akhir_semester')->insert($nilaiAkhir);
    }
}
