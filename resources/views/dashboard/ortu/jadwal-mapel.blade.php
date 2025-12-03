@extends('layouts.template') {{-- Sesuaikan dengan nama layout utama Anda --}}

@section('title', 'Jadwal Pelajaran Siswa')
@section('breadcrumb', 'Jadwal Pelajaran')

@section('content')
    <div class="card shadow-sm p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="h5 mb-1">Jadwal Pelajaran</h2>
                <p class="text-muted mb-0">Jadwal pelajaran mingguan untuk <strong>{{ $siswa->nama ?? 'Siswa' }}</strong>.
                </p>

                {{-- Informasi Kelas/TA/Semester yang aktif (Otomatis) --}}
                @if (isset($siswaKelas))
                    <span class="badge bg-primary">Kelas: {{ $siswaKelas->kelas->nama_kelas ?? 'N/A' }}</span>
                @endif
                <span class="badge bg-info">TA: {{ $selectedTahunAjaran ?? 'N/A' }}</span>
                <span class="badge bg-info">Semester: {{ $selectedSemester ?? 'N/A' }}</span>
                {{-- End Informasi Otomatis --}}

            </div>
        </div>

        @if ($error)
            <div class="alert alert-danger">
                {{ $error }}
            </div>
        @endif

        {{-- Formulir Filter (Hanya Tingkat Kelas) --}}
        <form action="{{ route('ortu.jadwal') }}" method="GET" class="mb-4 p-3 border rounded bg-light">
            <div class="row g-3 align-items-end">

                {{-- Filter Tingkat Kelas (Tingkat yang pernah/sedang diambil siswa) --}}
                <div class="col-md-5">
                    <label for="tingkat_kelas" class="form-label">Filter Tingkat Kelas</label>
                    <select name="tingkat_kelas" id="tingkat_kelas" class="form-select">
                        <option value="">Pilih Tingkat Kelas</option>
                        {{-- HANYA menampilkan tingkat kelas yang pernah/sedang diikuti siswa --}}
                        @foreach ($semuaTingkatKelas as $tingkat => $val)
                            <option value="{{ $tingkat }}" {{ $tingkat == $selectedTingkatKelas ? 'selected' : '' }}>
                                Kelas {{ $tingkat }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Tombol Tampilkan --}}
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i> Tampilkan
                    </button>
                </div>

                {{-- Kolom Kosong untuk meratakan tampilan --}}
                <div class="col-md-5"></div>
            </div>
        </form>
        {{-- End Formulir Filter --}}

        @php
            // Urutan hari yang ingin ditampilkan (Senin-Jumat)
            $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        @endphp

        {{-- Navigasi Tab Hari dan Konten Jadwal TETAP SAMA --}}
        <ul class="nav nav-tabs mb-3" id="jadwalTab" role="tablist">
            @foreach ($days as $hari)
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $hariIni == $hari ? 'active' : '' }}" id="{{ strtolower($hari) }}-tab"
                        data-bs-toggle="tab" data-bs-target="#{{ strtolower($hari) }}-content" type="button"
                        role="tab" aria-controls="{{ strtolower($hari) }}"
                        aria-selected="{{ $hariIni == $hari ? 'true' : 'false' }}">
                        {{ $hari }}
                    </button>
                </li>
            @endforeach
        </ul>

        <div class="tab-content" id="jadwalTabContent">
            @foreach ($days as $hari)
                <div class="tab-pane fade {{ $hariIni == $hari ? 'show active' : '' }}"
                    id="{{ strtolower($hari) }}-content" role="tabpanel" aria-labelledby="{{ strtolower($hari) }}-tab">

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th style="width: 150px;">Tanggal</th>
                                    <th style="width: 150px;">Jam</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Guru</th>
                                    <th>Ruangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($jadwalPerHari->get($hari, []) as $index => $jadwal)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ \Carbon\Carbon::parse($jadwal->tanggal_jadwal)->translatedFormat('d M Y') }}
                                        </td>
                                        <td>{{ date('H:i', strtotime($jadwal->jam_mulai)) }} -
                                            {{ date('H:i', strtotime($jadwal->jam_selesai)) }}</td>
                                        <td>{{ $jadwal->penugasan->mapel->nama_mapel ?? 'N/A' }}</td>
                                        <td>{{ $jadwal->penugasan->guru->nama_guru ?? 'N/A' }}</td>
                                        <td>{{ $jadwal->ruangan->kode_ruangan ?? 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Tidak ada jadwal pelajaran di hari
                                            {{ $hari }} pada minggu ini berdasarkan filter.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
@endsection
