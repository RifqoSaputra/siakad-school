<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SIAKAD\SCHOOL\Guru;

class ManajemenGuruController extends Controller
{
    public function index(Request $request)
    {
        // =============================
        // HANDLE UPDATE VIA MODAL
        // =============================
        if ($request->isMethod('post') && $request->filled('id_guru')) {
            $guru = Guru::findOrFail($request->id_guru);

            $guru->update([
                'nip'        => $request->nip,
                'nama_guru'  => $request->nama_guru,
                'email'      => $request->email,
                'no_hp'      => $request->no_hp,
                'kota_rmh'   => $request->kota_rmh,
                'alamat_rmh' => $request->alamat_rmh,
            ]);

            return redirect()->route('admin.guru')
                ->with('success', 'Data guru berhasil diperbarui');
        }

        // =============================
        // DATA GURU
        // =============================
        $guruData = Guru::orderBy('nama_guru')->get();

        // =============================
        // RINGKASAN
        // =============================
        $totalGuru = $guruData->count();
        $totalGuruAktif = $totalGuru;
        $totalGuruPNS = 0; // belum ada field

        return view('dashboard.admin.manajemen-guru', compact(
            'guruData',
            'totalGuru',
            'totalGuruAktif',
            'totalGuruPNS'
        ));
    }
}
