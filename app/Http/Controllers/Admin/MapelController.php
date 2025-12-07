<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class MapelController extends Controller
{
    public function index()
    {
        // Ambil semua data dari tabel 'mapel'
        $mapel = DB::table('mapel')->get();

        // Kirim data ke view
        return view('dashboard.admin.manajemen-mapel', compact('mapel'));
    }
}