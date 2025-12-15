<?php

namespace Database\Seeders\SIAKAD\SCHOOL;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AbsensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // =================================================================
        // 1. DEFINISI RENTANG WAKTU SEEDING
        //    Kita hanya akan men-seed absensi untuk 30 hari sekolah
        // =================================================================
        $numberOfSchoolDays = 30;
        $startDate = Carbon::create(2025, 11, 1);
        $userEntry = 1;

        DB::table('absensi')->truncate();

        // =================================================================
        // 2. PERSIAPAN DATA
        // =================================================================
        $siswaPerKelas = DB::table('siswa_kelas')
            ->select('id_siswa', 'kelas_id')
            ->get()
            ->groupBy('kelas_id');

        // Ambil semua template jadwal mapel untuk mendapatkan JADWAL MAPEL ID (FK) per hari/kelas
        $jadwalMapelTemplates = DB::table('jadwal_mapel')
            ->select('jadwal_mapel_id', 'kelas_id', 'hari')
            ->get();

        $absensiEntries = [];
        $currentDate = $startDate->copy();
        $seededDays = 0;

        // Counter untuk melacak jumlah Alpha spesifik per siswa (Skenario Testing)
        $alphaCounter = [2 => 0, 3 => 0, 4 => 0];

        // =================================================================
        // 3. GENERASI ABSENSI HARIAN (Hanya 1 entry per Siswa per Hari)
        // =================================================================

        while ($seededDays < $numberOfSchoolDays) {

            $currentDayOfWeek = $currentDate->dayOfWeek;
            $currentDayString = strtoupper(
                ['MINGGU', 'SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU'][$currentDayOfWeek]
            );

            // Skip weekend (Minggu = 0, Sabtu = 6)
            if ($currentDayOfWeek === 0 || $currentDayOfWeek === 6) {
                $currentDate->addDay();
                continue;
            }

            $dateOnly = $currentDate->format('Y-m-d');
            $dateTimeEntry = $currentDate->toDateTimeString(); // Waktu entry saat seeding

            // Ambil semua Kelas yang ada jadwalnya hari ini
            $classesWithScheduleToday = $jadwalMapelTemplates
                ->where('hari', $currentDayString)
                ->pluck('kelas_id')
                ->unique();

            // Loop per KELAS
            foreach ($classesWithScheduleToday as $kelasId) {
                if (!isset($siswaPerKelas[$kelasId])) {
                    continue;
                }

                $students = $siswaPerKelas[$kelasId];

                // Ambil jadwal mapel ID PERTAMA di kelas tersebut hari ini
                // Ini hanya untuk memenuhi Foreign Key (kita berasumsi status ini adalah absensi global harian)
                $firstJadwalId = $jadwalMapelTemplates
                    ->where('kelas_id', $kelasId)
                    ->where('hari', $currentDayString)
                    ->first()->jadwal_mapel_id;

                foreach ($students as $siswa) {
                    $status = 'Hadir';
                    $keterangan = null;

                    // --- LOGIKA ABSENSI HARIAN (Mempertahankan Logika Warning Testing) ---
                    // Logika sekarang mengacu pada HARI, bukan per Jam Pelajaran

                    if ($siswa->id_siswa == 2) {
                        if ($alphaCounter[2] < 2 && $status != 'Alpha') {
                            if ($currentDayString === 'SENIN') { // Absen Alpha di hari Senin
                                $status = 'Alpha';
                                $alphaCounter[2]++;
                            }
                        }
                    } elseif ($siswa->id_siswa == 3) {
                        if ($alphaCounter[3] < 3 && $status != 'Alpha') {
                            if ($currentDayString === 'SELASA') { // Absen Alpha di hari Selasa
                                $status = 'Alpha';
                                $alphaCounter[3]++;
                            }
                        }
                    } elseif ($siswa->id_siswa == 4) {
                        if ($alphaCounter[4] < 5 && $status != 'Alpha') {
                            if ($currentDayString === 'RABU') { // Absen Alpha di hari Rabu
                                $status = 'Alpha';
                                $keterangan = 'Bolos tanpa keterangan';
                                $alphaCounter[4]++;
                            }
                        }
                    }
                    // Siswa Lainnya (Random Normal)
                    else {
                        $rand = rand(1, 100);
                        if ($rand > 85 && $rand <= 90) { // 5% Sakit
                            $status = 'Sakit';
                            $keterangan = 'Sakit Flu (Generated)';
                        } elseif ($rand > 90 && $rand <= 95) { // 5% Izin
                            $status = 'Izin';
                            $keterangan = 'Acara Keluarga (Generated)';
                        } elseif ($rand > 95) { // 5% Alpha
                            $status = 'Alpha';
                            $keterangan = 'Tanpa Keterangan';
                        }
                    }

                    // Masukkan ke array data (Hanya 1 Entry per Siswa per Hari)
                    $absensiEntries[] = [
                        'id_siswa' => $siswa->id_siswa,
                        'jadwal_mapel_id' => $firstJadwalId, // Menggunakan jadwal mapel pertama hari itu
                        'status' => $status,
                        'keterangan' => $keterangan,
                        'waktu_absen' => $dateOnly, // TANGGAL MURNI
                        'user_entry' => $userEntry,
                        'tgl_entry' => $dateTimeEntry, // Waktu entry saat seeding
                    ];
                }
            }

            $seededDays++;
            $currentDate->addDay();
        }

        // 4. INSERT BATCH
        $chunks = array_chunk($absensiEntries, 500);
        foreach ($chunks as $chunk) {
            DB::table('absensi')->insert($chunk);
        }
    }
}
