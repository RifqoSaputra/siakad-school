@extends('layouts.template')

@section('title', 'Guru Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')
    <h1 class="mb-4">Selamat Datang, **Guru {{ Auth::user()->name }}**!</h1>
    <p class="text-muted">Selamat bertugas. Lakukan penginputan nilai dan absensi melalui menu yang tersedia di samping.</p>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow p-3">
                <div class="card-body">
                    <h5 class="card-title">Jadwal Hari Ini</h5>
                    <p class="card-text fs-2 fw-bold">3 Sesi</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card shadow p-3">
                <div class="card-body">
                    <h5 class="card-title">Kelas Diampu</h5>
                    <p class="card-text fs-2 fw-bold">Kelas 7A & 8B</p>
                </div>
            </div>
        </div>
    </div>

    <hr>
    <p>Akses: Jadwal Ajar, Input Nilai, Absensi.</p>
@endsection