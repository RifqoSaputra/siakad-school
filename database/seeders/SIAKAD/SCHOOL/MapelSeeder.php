<?php

namespace Database\Seeders\SIAKAD\SCHOOL;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MapelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $userEntry = 1; // User ID 1: superadmin

        $mapels = [
            // 1. Mapel Umum (MKDU)
            [
                'kode_mapel' => 'IND-10',
                'nama_mapel' => 'Bahasa Indonesia',
                'kategori_mapel' => 'MKDU',
                'status' => 1,
            ],
            [
                'kode_mapel' => 'MTK-10',
                'nama_mapel' => 'Matematika Dasar',
                'kategori_mapel' => 'MKDU',
                'status' => 1,
            ],
            [
                'kode_mapel' => 'ENG-10',
                'nama_mapel' => 'Bahasa Inggris',
                'kategori_mapel' => 'MKDU',
                'status' => 1,
            ],
            [
                'kode_mapel' => 'AGM-10',
                'nama_mapel' => 'Pendidikan Agama Islam',
                'kategori_mapel' => 'MKDU',
                'status' => 1,
            ],

            // 2. Mapel Kejuruan (Multimedia/DKV)
            [
                'kode_mapel' => 'DKV-11',
                'nama_mapel' => 'Desain Grafis Percetakan',
                'kategori_mapel' => 'DKV',
                'status' => 1,
            ],
            [
                'kode_mapel' => 'VID-12',
                'nama_mapel' => 'Teknik Produksi Video',
                'kategori_mapel' => 'DKV',
                'status' => 1,
            ],
            [
                'kode_mapel' => 'FOTO-11',
                'nama_mapel' => 'Fotografi Dasar',
                'kategori_mapel' => 'DKV',
                'status' => 1,
            ],

            // 3. Mapel Kejuruan (Akuntansi/Keuangan)
            [
                'kode_mapel' => 'AK-11',
                'nama_mapel' => 'Akuntansi Keuangan Dasar',
                'kategori_mapel' => 'Akuntansi',
                'status' => 1,
            ],
            [
                'kode_mapel' => 'ADM-12',
                'nama_mapel' => 'Administrasi Perpajakan',
                'kategori_mapel' => 'Akuntansi',
                'status' => 1,
            ],

            // 4. Mapel Lain
            [
                'kode_mapel' => 'KWH-10',
                'nama_mapel' => 'Kewirausahaan',
                'kategori_mapel' => 'MKDU',
                'status' => 1,
            ],
        ];

        // Tambahkan kolom audit
        $mapels = array_map(function ($mapel) use ($now, $userEntry) {
            $mapel['user_entry'] = $userEntry;
            $mapel['tgl_entry'] = $now;
            return $mapel;
        }, $mapels);

        DB::table('mapel')->insert($mapels);
    }
}
