<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class KelasController extends Controller
{
    public function index()
    {
        // ambil semua data kelas dari tabel "kelas"
        $kelas = DB::table('kelas')->get();

        // kirim data ke view
        return view('dashboard.admin.manajemen-kelas', compact('kelas'));
    }
}