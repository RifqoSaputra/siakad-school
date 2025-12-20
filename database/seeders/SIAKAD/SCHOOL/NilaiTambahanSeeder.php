<?php

namespace Database\Seeders\SIAKAD\SCHOOL;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NilaiTambahanSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $userEntry = 1;

        // Tentukan ID Kelas Target (10 DKV-1) yang akan di-Submitted
        $target_kelas_id = 1;

        // 1. Dapatkan SEMUA KOMBINASI PENGAJARAN UNIK (Mapel, Semester, Tahun Ajaran)
        $unique_teachings = DB::table('guru_mapel')
            ->select('mapel_id', 'semester', 'tahun_ajaran')
            ->distinct()
            ->get();

        // 2. Dapatkan SEMUA KELAS yang ada
        $kelas = DB::table('kelas')->pluck('kelas_id');

        $nilai_tambahan = [];

        foreach ($unique_teachings as $teaching) {
            // Cari salah satu guru_mapel_id yang mengajar mapel/semester/tahun ini (cukup 1 ID untuk FK)
            $gm_id = DB::table('guru_mapel')
                ->where('mapel_id', $teaching->mapel_id)
                ->where('semester', $teaching->semester)
                ->where('tahun_ajaran', $teaching->tahun_ajaran)
                ->value('guru_mapel_id');

            if (!$gm_id) continue;

            // 3. Cross Join dengan semua kelas (HANYA SEKALI per Kombinasi Unik)
            foreach ($kelas as $k_id) {

                // PERBAIKAN LOGIKA: Tentukan status berdasarkan kelas_id
                $status = ($k_id === $target_kelas_id) ? 'Submitted' : 'Draft';

                // Sekarang, buat HANYA 5 Tugas untuk setiap kombinasi UNIK Mapel/Kelas/Semester/Tahun
                for ($i = 1; $i <= 5; $i++) {
                    $nilai_tambahan[] = [
                        'guru_mapel_id' => $gm_id,
                        'kelas_id'      => $k_id,
                        'mapel_id'      => $teaching->mapel_id,
                        'semester'      => $teaching->semester,
                        'tahun_ajaran'  => $teaching->tahun_ajaran,
                        'status'        => $status, // Gunakan status yang sudah ditentukan
                        'tipe_penunjang' => 'Tugas ' . $i,
                        'deskripsi'     => 'Tugas Harian ke-' . $i . ' untuk Mapel ' . $teaching->mapel_id . ' di Kelas ' . $k_id,
                        'user_entry'    => $userEntry,
                        'tgl_entry'     => $now,
                        'user_update'   => null,
                        'tgl_update'    => null,
                    ];
                }
            }
        }

        foreach (array_chunk($nilai_tambahan, 100) as $chunk) {
            DB::table('nilai_tambahan')->insert($chunk);
        }
    }
}
