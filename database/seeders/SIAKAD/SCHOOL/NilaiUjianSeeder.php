<?php

namespace Database\Seeders\SIAKAD\SCHOOL;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NilaiUjianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $userEntry = 1; // Asumsi Superadmin
        $tahunAjaran = '2024/2025';
        $semester = 'Ganjil';

        // TANGGAL UJIAN (Asumsi)
        $tanggal_pts = '2024-10-14';
        $tanggal_pas = '2024-12-16';

        // Definisi Tipe Ujian dan Deskripsi Otomatis
        $tipeUjianDefinitions = [
            'PTS' => [
                'tanggal' => $tanggal_pts,
                'deskripsi' => 'Penilaian Tengah Semester',
            ],
            'PAS' => [
                'tanggal' => $tanggal_pas,
                'deskripsi' => 'Penilaian Akhir Semester',
            ],
        ];

        // 1. Ambil semua kombinasi unik Kelas, Mapel, dan GuruMapel yang terdaftar di Jadwal Mapel.
        $validAssignments = DB::table('jadwal_mapel')
            ->join('guru_mapel', 'jadwal_mapel.guru_mapel_id', '=', 'guru_mapel.guru_mapel_id')
            ->select('jadwal_mapel.guru_mapel_id', 'jadwal_mapel.kelas_id', 'guru_mapel.mapel_id')
            ->where('guru_mapel.tahun_ajaran', $tahunAjaran)
            ->where('guru_mapel.semester', $semester)
            ->distinct()
            ->get();

        $nilaiUjianToInsert = [];
        $uniqueKeys = [];

        foreach ($validAssignments as $assignment) {
            $key = $assignment->kelas_id . '-' . $assignment->mapel_id;

            foreach ($tipeUjianDefinitions as $tipe => $def) {

                // Pastikan kombinasi Kelas-Mapel-Tipe Ujian ini belum dimasukkan (mencegah duplikasi)
                if (!isset($uniqueKeys[$key . '-' . $tipe])) {
                    $nilaiUjianToInsert[] = [
                        'guru_mapel_id' => $assignment->guru_mapel_id,
                        'kelas_id' => $assignment->kelas_id,
                        'mapel_id' => $assignment->mapel_id,
                        'tipe_ujian' => $tipe,
                        'tanggal_ujian' => $def['tanggal'],
                        'deskripsi' => $def['deskripsi'], // <--- DIISI OTOMATIS
                        'semester' => $semester,
                        'tahun_ajaran' => $tahunAjaran,
                        'status' => 'Draft',
                        'user_entry' => $userEntry,
                        'tgl_entry' => $now,
                        'user_update' => null,
                        'tgl_update' => null,
                    ];
                    $uniqueKeys[$key . '-' . $tipe] = true;
                }
            }
        }

        // 2. INSERT DATA
        if (!empty($nilaiUjianToInsert)) {
            // Hapus data lama (opsional, untuk memastikan seeder bersih)
            DB::table('nilai_ujian')->where('tahun_ajaran', $tahunAjaran)->where('semester', $semester)->delete();

            // Insert data baru
            DB::table('nilai_ujian')->insert($nilaiUjianToInsert);

            // Log konfirmasi
            \Illuminate\Support\Facades\Log::info("Seeder NilaiUjian: Berhasil menyisipkan " . count($nilaiUjianToInsert) . " entri Ujian (PTS/PAS) untuk TA $tahunAjaran Semester $semester.");
        }
    }
}
