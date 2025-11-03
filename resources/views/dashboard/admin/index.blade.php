@extends('layouts.template')

@section('title', 'Admin Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')
    <h1 class="mb-4">Selamat Datang, **Admin {{ Auth::user()->name }}**!</h1>
    <p class="text-muted">Ini adalah pusat kendali Anda. Gunakan menu di samping untuk manajemen sistem secara keseluruhan.</p>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow p-3">
                <div class="card-body">
                    <h5 class="card-title">Total Pengguna</h5>
                    <p class="card-text fs-2 fw-bold">1200</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card shadow p-3">
                <div class="card-body">
                    <h5 class="card-title">Siswa Aktif</h5>
                    <p class="card-text fs-2 fw-bold">850</p>
                </div>
            </div>
        </div>
    </div>
    
    <hr>
    <p>Akses: Manajemen Guru, Manajemen Siswa, Setting Aplikasi.</p>
@endsection