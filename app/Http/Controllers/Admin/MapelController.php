<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SIAKAD\SCHOOL\Mapel;

class MapelController extends Controller
{
    public function index()
    {
        $mapel = Mapel::with(['penugasanGuru.guru'])
            ->orderBy('nama_mapel')
            ->get();

        return view('dashboard.admin.manajemen-mapel', compact('mapel'));
    }
}
