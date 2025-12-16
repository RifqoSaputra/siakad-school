<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SIAKAD\SCHOOL\Pengumuman;
use App\Models\SIAKAD\SCHOOL\Ortu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Halaman utama setelah login.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user && $user->hasRole('Admin')) {
            $totalSiswa = DB::table('siswa')->count();
            $totalGuru = DB::table('guru')->count();
            $totalKelas = DB::table('kelas')->count();

            $pengumumanTerbaru = Pengumuman::query()
                ->with('admin')
                ->latest('created_at')
                ->limit(5)
                ->get()
                ->map(function ($item) {
                    $item->nama_admin = $item->admin->username ?? $item->admin->users_id ?? null;
                    return $item;
                });

            return view('dashboard.admin.index', compact(
                'totalSiswa',
                'totalGuru',
                'totalKelas',
                'pengumumanTerbaru'
            ));
        }

        if ($user && $user->hasRole('Guru')) {
            $pengumumanTerbaruGuru = Pengumuman::query()
                ->whereIn('target_role', ['guru', 'semua'])
                ->latest('created_at')
                ->limit(5)
                ->get();

            $totalSiswa = DB::table('siswa')->count(); // TODO: filter hanya siswa yang diajar guru ini
            $totalKelas = DB::table('kelas')->count(); // TODO: filter hanya kelas yang diajar guru ini
            $jadwalHariIni = collect([
                [
                    'jam_mulai' => '07:30',
                    'jam_selesai' => '09:00',
                    'mapel' => 'Matematika',
                    'kelas' => 'VII-A',
                    'ruang' => 'Ruang 101',
                ],
                [
                    'jam_mulai' => '09:15',
                    'jam_selesai' => '10:45',
                    'mapel' => 'IPA',
                    'kelas' => 'VII-B',
                    'ruang' => 'Ruang 102',
                ],
                [
                    'jam_mulai' => '11:00',
                    'jam_selesai' => '12:30',
                    'mapel' => 'Matematika',
                    'kelas' => 'VIII-A',
                    'ruang' => 'Lab 1',
                ],
            ]);
            $ringkasanKelas = collect([
                [
                    'nama' => 'Desain Komunikasi Visual',
                    'ikon' => 'palette',
                    'total' => 30,
                    'tingkatan' => [
                        ['label' => 'X DKV', 'jumlah' => 10],
                        ['label' => 'XI DKV', 'jumlah' => 10],
                        ['label' => 'XII DKV', 'jumlah' => 10],
                    ],
                ],
                [
                    'nama' => 'Akuntansi & Keuangan Lembaga',
                    'ikon' => 'bank',
                    'total' => 30,
                    'tingkatan' => [
                        ['label' => 'X AKL', 'jumlah' => 10],
                        ['label' => 'XI AKL', 'jumlah' => 10],
                        ['label' => 'XII AKL', 'jumlah' => 10],
                    ],
                ],
                [
                    'nama' => 'Mata Pelajaran Aktif',
                    'ikon' => 'book',
                    'total' => 80,
                    'target' => 100,
                    'tingkatan' => [
                        ['label' => 'Umum', 'jumlah' => 20],
                    ],
                ],
            ]);

            return view('dashboard.guru.index', compact(
                'pengumumanTerbaruGuru',
                'totalSiswa',
                'totalKelas',
                'jadwalHariIni',
                'ringkasanKelas'
            ));
        }

        if ($user && $user->hasRole('Orang Tua')) {
            $ortu = Ortu::with(['siswa.enrollment.kelas'])
                ->where('users_id', $user->users_id ?? null)
                ->first();

            $anakList = collect();

            if ($ortu) {
                $anakList = $ortu->siswa->map(function ($siswa) {
                    $kelasAktif = $siswa->enrollment
                        ->sortByDesc(fn($enroll) => $enroll->tahun_ajaran)
                        ->first()
                        ?->kelas;

                    $siswa->kelas_aktif = $kelasAktif;
                    return $siswa;
                });
            }

            $selectedId = session('selected_siswa_id');
            $siswaAktif = $anakList->firstWhere('id_siswa', $selectedId) ?? $anakList->first();

            return view('dashboard.ortu.index', compact(
                'anakList',
                'siswaAktif'
            ));
        }

        // TODO: Tambahkan fallback view yang lebih sesuai jika role tidak terdeteksi
        return view('dashboard.admin.index', [
            'totalSiswa' => DB::table('siswa')->count(),
            'totalGuru' => DB::table('guru')->count(),
            'totalKelas' => DB::table('kelas')->count(),
            'pengumumanTerbaru' => Pengumuman::query()
                ->with('admin')
                ->latest('created_at')
                ->limit(5)
                ->get()
                ->map(function ($item) {
                    $item->nama_admin = $item->admin->username ?? $item->admin->users_id ?? null;
                    return $item;
                }),
        ]);
    }
}
