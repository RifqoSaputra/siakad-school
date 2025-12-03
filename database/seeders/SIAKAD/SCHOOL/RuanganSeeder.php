<?php

namespace Database\Seeders\SIAKAD\SCHOOL;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RuanganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $userEntry = 1; // User ID 1: superadmin

        $ruangan = [
            // Ruangan Kelas (Home Base)
            [
                'kode_ruangan' => 'KLS-10-DKV',
                'status' => 1,
            ],
            [
                'kode_ruangan' => 'KLS-11-AKL',
                'status' => 1,
            ],
            [
                'kode_ruangan' => 'KLS-12-FOT',
                'status' => 1,
            ],
            // Ruangan Khusus (Lab/Studio)
            [
                'kode_ruangan' => 'LAB-KOM-01',
                'status' => 1,
            ],
            [
                'kode_ruangan' => 'STUDIO-FOTO',
                'status' => 1,
            ],
        ];

        // Tambahkan kolom audit
        $ruangan = array_map(function ($ruangan) use ($now, $userEntry) {
            $ruangan['user_entry'] = $userEntry;
            $ruangan['tgl_entry'] = $now;
            return $ruangan;
        }, $ruangan);

        DB::table('ruangan')->insert($ruangan);
    }
}
