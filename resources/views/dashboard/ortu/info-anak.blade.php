@extends('layouts.template')

@section('title', 'Info Anak')
@section('breadcrumb', 'Info Anak')

@section('content')
    @php
        use Carbon\Carbon;
    @endphp

    @if ($anakList->isEmpty())
        <div class="card shadow-sm p-5 text-center">
            <h2 class="h4 mb-2">Data anak belum tersedia</h2>
            <p class="text-muted mb-4">Pastikan akun Orang Tua sudah terhubung dengan data siswa di sistem.</p>
            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
        </div>
    @else
        @php
            $kelasAktif = $siswaAktif->kelas_aktif ?? null;
            $usia = $siswaAktif?->tgl_lahir ? Carbon::parse($siswaAktif->tgl_lahir)->age : null;
        @endphp

        <div class="card shadow-sm border-0 overflow-hidden mb-4">
            <div class="position-relative" style="background: linear-gradient(120deg, #4A46E0, #5AC8FA);">
                <div class="p-4 p-lg-5 text-white">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-4">
                        <div>
                            <p class="text-uppercase small mb-1 opacity-75">Anak aktif</p>
                            <h1 class="h3 fw-bold mb-2">{{ $siswaAktif->nama ?? 'Nama tidak tersedia' }}</h1>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-light text-dark">NIS: {{ $siswaAktif->nis ?? 'N/A' }}</span>
                                <span class="badge bg-light text-dark">Usia: {{ $usia ? $usia . ' th' : 'N/A' }}</span>
                                <span class="badge bg-light text-dark">
                                    Kelas: {{ $kelasAktif->nama_kelas ?? 'Belum ada kelas' }}
                                </span>
                            </div>
                        </div>
                        <div class="text-lg-end">
                            <div class="bg-white text-primary fw-semibold px-3 py-2 rounded-pill d-inline-flex align-items-center">
                                <i class="fas fa-map-marker-alt me-2"></i>
                                {{ $siswaAktif->kota_rmh ?? 'Kota belum diisi' }}
                            </div>
                            <div class="text-white-50 small mt-2">
                                Tahun Ajaran:
                                {{ $kelasAktif->tahun_ajaran ?? 'Belum terdata' }}
                                @if ($kelasAktif?->semester)
                                    &bull; Semester {{ $kelasAktif->semester }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="small text-muted text-uppercase mb-2">Ringkasan</div>
                        <div class="d-flex flex-column gap-2">
                            <div class="p-3 bg-primary bg-opacity-10 rounded-3">
                                <div class="text-muted small mb-1">Kelas</div>
                                <div class="fw-semibold">{{ $kelasAktif->nama_kelas ?? 'Belum ada kelas' }}</div>
                            </div>
                            <div class="p-3 bg-success bg-opacity-10 rounded-3">
                                <div class="text-muted small mb-1">Status</div>
                                <div class="fw-semibold text-success">Aktif</div>
                            </div>
                            <div class="p-3 bg-secondary bg-opacity-10 rounded-3">
                                <div class="text-muted small mb-1">Tahun Ajaran / Semester</div>
                                <div class="fw-semibold">
                                    {{ $kelasAktif->tahun_ajaran ?? '-' }}
                                    @if ($kelasAktif?->semester)
                                        &middot; Semester {{ $kelasAktif->semester }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="small text-muted text-uppercase mb-3">Biodata & Kontak</div>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="text-muted small">Nama Lengkap</div>
                                <div class="fw-semibold">{{ $siswaAktif->nama ?? '-' }}</div>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-muted small">NIS</div>
                                <div class="fw-semibold">{{ $siswaAktif->nis ?? '-' }}</div>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-muted small">Agama</div>
                                <div class="fw-semibold">{{ $siswaAktif->agama ?? '-' }}</div>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-muted small">Tanggal Lahir</div>
                                <div class="fw-semibold">
                                    {{ $siswaAktif->tgl_lahir ? Carbon::parse($siswaAktif->tgl_lahir)->translatedFormat('d F Y') : '-' }}
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-muted small">Usia</div>
                                <div class="fw-semibold">{{ $usia ? $usia . ' tahun' : '-' }}</div>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-muted small">Kota</div>
                                <div class="fw-semibold">{{ $siswaAktif->kota_rmh ?? '-' }}</div>
                            </div>
                            <div class="col-12">
                                <div class="text-muted small">Alamat</div>
                                <div class="fw-semibold">{{ $siswaAktif->alamat_rmh ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
