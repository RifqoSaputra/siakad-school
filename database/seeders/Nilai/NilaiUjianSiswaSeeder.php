<?php

namespace Database\Seeders\Nilai;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NilaiUjianSiswaSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $userEntry = 1;

        $header_ujian = DB::table('nilai_ujian')->select('id', 'kelas_id')->get();

        $nilai_siswa = [];

        foreach ($header_ujian as $header) {
            $siswa_di_kelas = DB::table('siswa_kelas')
                ->where('kelas_id', $header->kelas_id)
                ->where('status', 1)
                ->pluck('id_siswa');

            foreach ($siswa_di_kelas as $id_siswa) {
                // Generate Nilai Ujian (50.0 - 100.0)
                $nilai_acak = rand(500, 1000) / 10;

                $nilai_siswa[] = [
                    'nilai_ujian_id' => $header->id,
                    'id_siswa'       => $id_siswa,
                    'nilai'          => $nilai_acak,
                    'keterangan'     => ($nilai_acak >= 75) ? 'Lulus' : 'Tidak Lulus',
                    'user_entry'     => $userEntry,
                    'tgl_entry'      => $now,
                ];
            }
        }

        foreach (array_chunk($nilai_siswa, 200) as $chunk) {
            DB::table('nilai_ujian_siswa')->insert($chunk);
        }
    }
}
