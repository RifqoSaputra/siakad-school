@extends('layouts.template')

@section('title', 'Orang Tua Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')
    <h1 class="mb-4">Selamat Datang, **Bapak/Ibu {{ Auth::user()->name }}**!</h1>
    <p class="text-muted">Pantau perkembangan akademik putra/putri Anda melalui portal ini.</p>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow p-3">
                <div class="card-body">
                    <h5 class="card-title">Nama Anak</h5>
                    <p class="card-text fs-2 fw-bold">Rizky Ananda</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card shadow p-3">
                <div class="card-body">
                    <h5 class="card-title">Nilai Rata-rata Terakhir</h5>
                    <p class="card-text fs-2 fw-bold text-success">85.5</p>
                </div>
            </div>
        </div>
    </div>
    
    <hr>
    <p>Akses: Informasi Anak, Rapor Online.</p>
@endsection