<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\SIAKAD\SCHOOL\User $user */
        $user = Auth::user();

        // Cek peran dan kembalikan view yang sesuai
        if ($user->hasRole('admin')) {
            // Memuat resources/views/dashboard/admin/index.blade.php
            return view('dashboard.admin.index'); 
        } 
        
        if ($user->hasRole('guru')) {
            // Memuat resources/views/dashboard/guru/index.blade.php
            return view('dashboard.guru.index');
        } 
        
        if ($user->hasRole('ortu')) {
            // Memuat resources/views/dashboard/ortu/index.blade.php
            return view('dashboard.ortu.index');
        }

        // Jika tidak ada peran yang cocok, arahkan ke halaman default (atau error 403)
        // Kita gunakan profil sebagai fallback 
        return redirect()->route('profile')->with('error', 'Akses dashboard tidak terdefinisi.');
    }
}