<?php

namespace Database\Seeders\SIAKAD\SCHOOL;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class GuruSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $userEntry = 1;

        // Helper deteksi jenis kelamin dari nama
        $detectGender = function (string $name): string {
            $femaleKeywords = ['ibu', 'bu', 'suster', 'rina', 'maria', 'amelia', 'ani'];
            $nameLower = strtolower($name);

            foreach ($femaleKeywords as $keyword) {
                if (str_contains($nameLower, $keyword)) {
                    return 'P';
                }
            }

            return 'L';
        };

        $guru = [
            ['id_guru' => 1, 'users_id' => 3, 'nip' => '198001012005011001', 'nama_guru' => 'Dr. Rina Puspita Sari'],
            ['id_guru' => 2, 'users_id' => 4, 'nip' => '198505152010022002', 'nama_guru' => 'Bapak Budi Santoso, S.Kom'],
            ['id_guru' => 3, 'users_id' => 5, 'nip' => '199003202015031003', 'nama_guru' => 'Ibu Maria Agustina, SE'],
            ['id_guru' => 4, 'users_id' => 6, 'nip' => '197507252000042004', 'nama_guru' => 'Prof. Dr. Ahmad Fikri'],
            ['id_guru' => 5, 'users_id' => 7, 'nip' => '199511302020051005', 'nama_guru' => 'Suster Amelia, M.Pd'],
            ['id_guru' => 6, 'users_id' => 8, 'nip' => '198202102008062006', 'nama_guru' => 'Pak Dede Permana'],
            ['id_guru' => 7, 'users_id' => 9, 'nip' => '197804042003071007', 'nama_guru' => 'Bu Ani Wijaya, M.Kom'],
            ['id_guru' => 8, 'users_id' => 10, 'nip' => '199208082017082008', 'nama_guru' => 'Kang Fajar Sidik'],
        ];

        $finalGuru = [];

        foreach ($guru as $g) {
            $finalGuru[] = array_merge($g, [
                'jenis_kelamin' => $detectGender($g['nama_guru']),
                'alamat_rmh' => 'Bandung',
                'kota_rmh' => 'Bandung',
                'no_hp' => '08' . rand(1000000000, 9999999999),
                'email' => Str::slug($g['nama_guru'], '.') . '@mutiarabangsa.ac.id',
                'status_guru' => 'Aktif',
                'user_entry' => $userEntry,
                'tgl_entry' => $now,
            ]);
        }

        DB::table('guru')->upsert(
            $finalGuru,
            ['id_guru'],
            [
                'users_id',
                'nip',
                'nama_guru',
                'jenis_kelamin',
                'alamat_rmh',
                'kota_rmh',
                'no_hp',
                'email',
                'status_guru',
                'user_entry',
                'tgl_entry'
            ]
        );
    }
}
