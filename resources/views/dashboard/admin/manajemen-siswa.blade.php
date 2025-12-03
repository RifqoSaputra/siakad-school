@extends('layouts.template')

@section('title', 'Manajemen Siswa')

@section('content')
    {{-- MENGHAPUS SELURUH CSS KUSTOM KARENA TIDAK TERAPPLIKASI --}}
    {{-- Kita akan menggunakan Utility Class Bootstrap di HTML --}}

    <div class="container-fluid">
        {{-- ======================================================= --}}
        {{-- 1. CARD RINGKASAN DATA (Diperbesar) --}}
        {{-- ======================================================= --}}
        <div class="row mb-2">
            {{-- Card Total Siswa Aktif --}}
            <div class="col-md-6 mb-4 pr-3">
                {{-- Tambahkan 'p-4' untuk padding card yang lebih besar --}}
                <div class="card card-body shadow-sm border-left-primary p-4">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            {{-- Judul: Gunakan font-weight-bold dan margin yang cukup --}}
                            <div class="font-weight-bold text-primary text-uppercase mb-2" style="font-size: 0.8rem;">
                                Total Siswa Aktif ({{ $tahunAjaranAktif }})
                            </div>
                            {{-- Angka: Diperbesar dari h5 menjadi h3 --}}
                            <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $totalSiswaAktif ?? 0 }} Siswa</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person-check-fill fa-3x text-gray-300"></i> {{-- Icon juga sedikit diperbesar --}}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card Siswa Non-Aktif --}}
            <div class="col-md-6 mb-4">
                {{-- Tambahkan 'p-4' untuk padding card yang lebih besar --}}
                <div class="card card-body shadow-sm border-left-warning p-4">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            {{-- Judul: Gunakan font-weight-bold dan margin yang cukup --}}
                            <div class="font-weight-bold text-warning text-uppercase mb-2" style="font-size: 0.8rem;">
                                Siswa Non-Aktif / Perlu Aksi (Lulus/Pindah)
                            </div>
                            {{-- Angka: Diperbesar dari h5 menjadi h3 --}}
                            <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $totalSiswaNonAktif ?? 0 }} Siswa</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person-x-fill fa-3x text-gray-300"></i> {{-- Icon juga sedikit diperbesar --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>

        {{-- ======================================================== --}}
        {{-- 2. BARIS FILTER (Ditambah Label dan Ditebalkan) --}}
        {{-- ======================================================== --}}
        <form method="GET" action="{{ route('admin.siswa') }}">
            <div class="row mb-2">

                {{-- Search Box (NIS/Nama) --}}
                <div class="col-md-3 col-sm-6 mb-3">
                    {{-- Label: Tambahkan font-weight-bold untuk menebalkan teks --}}
                    <label for="search_siswa" class="form-label font-weight-bold">Cari Nama atau NIS</label>
                    <input type="text" name="search" id="search_siswa" class="form-control"
                        placeholder="Masukkan kata kunci..." value="{{ $request->get('search') }}">
                </div>

                {{-- Filter Tahun Ajaran --}}
                <div class="col-md-3 col-sm-6 mb-3">
                    {{-- Label: Tambahkan font-weight-bold untuk menebalkan teks --}}
                    <label for="tahun_ajaran" class="form-label font-weight-bold">Tahun Ajaran</label>
                    <x-dropdown name="tahun_ajaran" id="tahun_ajaran" onchange="this.form.submit()">
                        @foreach ($allTahunAjaran as $ta)
                            <option value="{{ $ta }}" {{ $tahunAjaranAktif == $ta ? 'selected' : '' }}>
                                Tahun Ajaran {{ $ta }}
                            </option>
                        @endforeach
                    </x-dropdown>
                </div>

                {{-- FILTER KELAS --}}
                <div class="col-md-3 col-sm-6 mb-3">
                    {{-- Label: Tambahkan font-weight-bold untuk menebalkan teks --}}
                    <label for="filter_kelas" class="form-label font-weight-bold">Filter Kelas</label>
                    <x-dropdown name="kelas" id="filter_kelas">
                        <option value="">Semua Kelas</option>
                        @foreach ($allKelas as $kelas)
                            <option value="{{ $kelas->kelas_id }}"
                                {{ $request->get('kelas') == $kelas->kelas_id ? 'selected' : '' }}>
                                {{ $kelas->nama_kelas_lengkap }}
                            </option>
                        @endforeach
                    </x-dropdown>
                </div>

                {{-- FILTER JENIS KELAMIN --}}
                <div class="col-md-3 col-sm-6 mb-3">
                    {{-- Label: Tambahkan font-weight-bold untuk menebalkan teks --}}
                    <label for="filter_jk" class="form-label font-weight-bold">Jenis Kelamin</label>
                    <x-dropdown name="jenis_kelamin" id="filter_jk">
                        <option value="">Semua Jenis Kelamin</option>
                        <option value="Laki-laki" {{ $request->get('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>
                            Laki-laki</option>
                        <option value="Perempuan" {{ $request->get('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>
                            Perempuan</option>
                    </x-dropdown>
                </div>
            </div>

            {{-- ======================================================== --}}
            {{-- 3. BARIS TOMBOL AKSI (Tidak ada perubahan) --}}
            {{-- ======================================================== --}}
            <div class="row mb-2 align-items-center">
                <div class="col-12 text-right">
                    <div>
                        {{-- Tombol Pertama --}}
                        <button type="button" class="btn btn-success rounded-pill">
                            <i class="bi bi-plus-circle"></i> Tambah Siswa
                        </button>

                        {{-- Tombol Kedua dan Ketiga --}}
                        <button type="submit" class="btn btn-primary rounded-pill ms-2">
                            <i class="bi bi-funnel-fill"></i> Terapkan Filter
                        </button>

                        <button type="button" class="btn btn-info rounded-pill ms-2 text-white">
                            <i class="bi bi-file-earmark-spreadsheet"></i> Export CSV
                        </button>
                    </div>
                </div>
            </div>
        </form>
        <br>

        {{-- ============================================= --}}
        {{-- 4. WRAPPER TABEL DATA SISWA (Tidak ada perubahan) --}}
        {{-- ============================================= --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Kelola data siswa • {{ $siswaData->total() }} siswa</h6>
            </div>
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>NIS</th>
                                <th>Nama</th>
                                <th>Kelas (Sekarang)</th>
                                <th>Jenis Kelamin</th>
                                <th>Tempat & Tanggal Lahir</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($siswaData as $index => $siswa)
                                <tr>
                                    <td>{{ $siswaData->firstItem() + $index }}</td>
                                    <td>{{ $siswa->nis }}</td>
                                    <td>{{ $siswa->nama }}</td>
                                    <td>
                                        {{-- kelas_sekarang didapat dari CONCAT di controller --}}
                                        <span class="badge bg-info text-white">{{ $siswa->kelas_sekarang }}</span>
                                    </td>
                                    <td>{{ $siswa->jenis_kelamin ?? '-' }}</td>
                                    <td>{{ ($siswa->tempat_lahir ?? 'Tidak Diketahui') . ', ' . \Carbon\Carbon::parse($siswa->tgl_lahir)->format('d M Y') }}
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-warning"><i class="bi bi-pencil-square"></i>
                                            Edit</a>
                                        <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Hapus</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data siswa aktif untuk filter yang
                                        dipilih.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINASI: Showing result dan Pagination dipisah baris --}}
                <div class="row align-items-center">
                    <div class="col-md-6 mb-3 mb-md-0">
                        {{-- Text Showing Results --}}
                        Showing {{ $siswaData->firstItem() }} to {{ $siswaData->lastItem() }} of
                        {{ $siswaData->total() }} results
                    </div>
                    <div class="col-md-6">
                        {{-- Link Pagination --}}
                        @include('components.pagination', ['paginator' => $siswaData])
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@endpush
