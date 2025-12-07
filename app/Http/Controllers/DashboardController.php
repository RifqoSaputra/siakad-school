<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    /**
     * Halaman utama setelah login.
     */
    public function index()
    {
        // Sesuaikan dengan view yang memang ada.
        // Dari error sebelumnya, kamu punya:
        // resources/views/dashboard/admin/index.blade.php
        // jadi kita pakai itu dulu:
        return view('dashboard.admin.index');
    }
}