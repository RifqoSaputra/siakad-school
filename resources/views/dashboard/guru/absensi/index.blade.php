@extends('layouts.template')

@section('title', 'Daftar Hadir - Pilih Jadwal')
@section('breadcrumb', 'Pilih Jadwal Absensi')

@section('content')

    <div class="card shadow-sm p-4">
        {{-- PERBAIKAN 1: Menggunakan Carbon untuk menampilkan tanggal yang lebih baik --}}
        <h2 class="h5 mb-4">Pilih Sesi Mengajar untuk Absensi pada Tanggal
            {{ \Carbon\Carbon::parse($filterTanggal)->translatedFormat('d F Y') }}
            ({{ $hariIndonesia }})</h2>
        <p class="text-muted">Pilih sesi mengajar yang sudah/sedang berlangsung untuk menginput atau mengedit absensi.
            Absensi yang telah melewati batas waktu edit (misalnya, 2 hari) tidak dapat diubah.</p>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- FORM FILTER --}}
        <div class="row mb-4 g-3">
            <div class="col-md-3">
                <label for="filterTanggal" class="form-label">Tanggal Mengajar</label>
                {{-- Bungkus input dan tombol dalam form --}}
                <form action="{{ route('guru.absensi.kelas') }}" method="GET" class="d-flex">
                    <input type="date" class="form-control me-2" name="tanggal" id="filterTanggal"
                        value="{{ $filterTanggal }}">
                    <button type="submit" class="btn btn-primary" style="white-space: nowrap;">Terapkan</button>
                </form>
            </div>
        </div>
        {{-- AKHIR FORM FILTER --}}

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr class="table-light">
                        <th>No</th>
                        <th>Jam Ke- (Waktu)</th>
                        <th>Mata Pelajaran</th>
                        <th>Semester</th>
                        <th>Kelas</th>
                        <th style="width: 400px;">Status Absensi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($jadwalMengajar as $index => $jadwal)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}</td>
                            <td>{{ $jadwal->penugasan->mapel->nama_mapel ?? 'N/A' }}</td>
                            {{-- PERBAIKAN 2: Ambil Semester dari relasi Penugasan (jika ada, atau Kelas) --}}
                            <td>{{ $jadwal->penugasan->semester ?? ($jadwal->kelas->semester ?? 'N/A') }}</td>
                            <td>{{ ($jadwal->kelas->tingkat_kelas ?? '') . ' ' . ($jadwal->kelas->nama_kelas ?? 'N/A') }}
                            </td>
                            <td>
                                @if ($jadwal->status_absensi === 'Sudah Diisi')
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-success me-2"><i class="fas fa-check-circle me-1"></i>
                                            {{ $jadwal->status_absensi }}</span>
                                        <small class="text-muted fw-normal" style="font-size: 0.9rem; white-space: nowrap;">
                                            Diisi pada:
                                            {{-- Menampilkan tanggal absensi (bukan tanggal entry) dan jam entry --}}
                                            {{ \Carbon\Carbon::parse($filterTanggal)->format('d/m/Y') }} Pukul
                                            {{ $jadwal->waktu_terakhir_isi }}
                                        </small>
                                    </div>
                                @else
                                    <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>
                                        {{ $jadwal->status_absensi }}</span>
                                @endif
                            </td>
                            <td>
                                {{-- PERBAIKAN UTAMA 3: Tambahkan parameter 'tanggal' ke link detail --}}
                                @php
                                    // Logika status terkunci tetap berada di Controller untuk kebersihan.
                                    $isAbsenLocked = false; // Ganti dengan properti dari Controller jika ada
                                @endphp

                                @if ($isAbsenLocked)
                                    <button class="btn btn-sm btn-secondary disabled" disabled>
                                        <i class="fas fa-lock me-1"></i>
                                        Terkunci
                                    </button>
                                @else
                                    <a href="{{ route('guru.absensi.detail', [
                                        'id_jadwal' => $jadwal->jadwal_mapel_id,
                                        'tanggal' => $filterTanggal, // INI PENTING!
                                    ]) }}"
                                        class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit me-1"></i>
                                        {{ $jadwal->status_absensi === 'Sudah Diisi' ? 'Lihat/Edit Absen' : 'Isi Absen' }}
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada jadwal mengajar pada tanggal
                                {{ \Carbon\Carbon::parse($filterTanggal)->format('d-m-Y') }} (Hari {{ $hariIndonesia }}).
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
