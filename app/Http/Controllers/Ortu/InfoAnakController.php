<?php

namespace App\Http\Controllers\Ortu;

use App\Http\Controllers\Controller;
use App\Models\SIAKAD\SCHOOL\Ortu;
use Illuminate\Support\Facades\Auth;

class InfoAnakController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Ambil data ortu beserta anak-anaknya + kelas terakhir tiap anak
        $ortu = Ortu::with(['siswa.enrollment.kelas'])
            ->where('users_id', $user->users_id ?? null)
            ->first();

        $anakList = collect();

        if ($ortu) {
            // Tambahkan properti kelas_aktif di setiap siswa untuk memudahkan view
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

        return view('dashboard.ortu.info-anak', [
            'anakList' => $anakList,
            'siswaAktif' => $siswaAktif,
        ]);
    }
}