<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('pengumuman_user')->truncate();
        DB::table('pengumuman_attachments')->truncate();
        DB::table('pengumuman')->truncate();
        Schema::enableForeignKeyConstraints();

        $adminId = DB::table('users')->value('users_id') ?? 1;
        $hasScheduledFor = Schema::hasColumn('pengumuman', 'scheduled_for');
        $hasSentAt = Schema::hasColumn('pengumuman', 'sent_at');

        $targets = ['all', 'guru', 'ortu'];

        $subjects = [
            'Pengumuman Kegiatan Sekolah',
            'Pemberitahuan Jadwal Ujian',
            'Informasi Libur dan Cuti',
            'Sosialisasi Program Baru',
            'Undangan Pertemuan Orang Tua',
            'Pengumuman Pelatihan Guru',
            'Pemberitahuan Pembayaran SPP',
            'Informasi Kegiatan Ekstrakurikuler',
            'Pengumuman Lomba Akademik',
            'Pemberitahuan Pembagian Rapor',
            'Informasi Bakti Sosial',
            'Pengumuman Hari Sehat Sekolah',
            'Pemberitahuan Evaluasi Tengah Semester',
            'Sosialisasi Kurikulum',
            'Pengumuman Workshop Siswa',
        ];

        $intros = [
            'Halo Bapak/Ibu,',
            'Yth. Orang tua/Wali,',
            'Rekan guru yang terhormat,',
            'Salam sejahtera,',
            'Assalamu\'alaikum,',
        ];

        $details = [
            'sekolah akan melaksanakan kegiatan penting pada tanggal terkait. Mohon mencatat jadwal ini.',
            'kami mengundang partisipasi aktif untuk mendukung kelancaran acara.',
            'harap mempersiapkan kebutuhan siswa sesuai ketentuan yang dilampirkan.',
            'kegiatan ini bertujuan meningkatkan kualitas belajar dan kedisiplinan siswa.',
            'jadwal lengkap dan ketentuan teknis akan dibagikan melalui wali kelas.',
        ];

        $closures = [
            'Terima kasih atas perhatian dan kerja samanya.',
            'Mohon dukungan agar kegiatan berjalan lancar.',
            'Apabila ada pertanyaan, silakan hubungi wali kelas.',
            'Kami menghargai partisipasi seluruh pihak.',
            'Semoga informasi ini bermanfaat.',
        ];

        $extras = [
            'Kegiatan pendukung mencakup sesi tanya jawab dan konsultasi singkat dengan wali kelas.',
            'Pastikan membawa perlengkapan yang diperlukan seperti alat tulis, bekal, dan pakaian yang sesuai.',
            'Akan ada dokumentasi kegiatan; mohon izin jika ada keberatan terkait publikasi internal sekolah.',
            'Panitia telah menyiapkan panduan ringkas yang akan dibagikan melalui grup kelas.',
            'Mohon hadir 15 menit lebih awal untuk registrasi dan pengarahan awal.',
        ];

        $rows = [];
        for ($i = 0; $i < 300; $i++) {
            $status = $i < 3 ? 'scheduled' : 'sent';
            $target = $targets[$i % count($targets)];

            $sentAt = null;
            $scheduledFor = null;
            $createdAt = Carbon::now();

            if ($status === 'sent') {
                $sentAt = Carbon::now()
                    ->subDays(rand(0, 450)) // beri variasi hingga tahun lalu
                    ->setTime(rand(6, 17), rand(0, 59));
                $createdAt = $sentAt->copy()->subDays(rand(0, 5));
            } elseif ($status === 'scheduled') {
                $scheduledFor = Carbon::now()
                    ->addDays(rand(2, 90))
                    ->setTime(rand(7, 16), rand(0, 59));
                $createdAt = Carbon::now()->subDays(rand(0, 30));
            }

            $subject = $subjects[$i % count($subjects)];
            $body = $intros[$i % count($intros)] . ' ' .
                $details[$i % count($details)] . ' ' .
                $extras[$i % count($extras)] . ' ' .
                $closures[$i % count($closures)];

            $rows[] = [
                'judul' => $subject,
                'isi_pengumuman' => $body,
                'target_role' => $target,
                'status' => $status,
                'scheduled_for' => $hasScheduledFor ? $scheduledFor : null,
                'sent_at' => $hasSentAt ? $sentAt : null,
                'id_admin' => $adminId,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];
        }

        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('pengumuman')->insert($chunk);
        }
    }
}
