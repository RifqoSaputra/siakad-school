<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SIAKAD\SCHOOL\Kelas;
use App\Models\SIAKAD\SCHOOL\Guru;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::with('waliKelas')
            ->orderBy('tingkat_kelas')
            ->orderBy('nama_kelas')
            ->get();

        $guruList = Guru::orderBy('nama_guru')->get();

        return view('dashboard.admin.manajemen-kelas', compact('kelas', 'guruList'));
    }
}
