@extends('layouts.template')

@section('title', 'Manajemen Guru')

@section('content')
    <div class="container-fluid">
        {{-- ======================================================= --}}
        {{-- 1. CARD RINGKASAN DATA (Dipertahankan) --}}
        {{-- ======================================================= --}}
        <div class="row mb-2">
            {{-- Card Total Guru Aktif (4 kolom) --}}
            <div class="col-md-4 mb-4 pr-3">
                <div class="card card-body shadow-sm border-left-primary p-4">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="font-weight-bold text-primary text-uppercase mb-2" style="font-size: 0.8rem;">
                                Total Guru Aktif
                            </div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $totalGuruAktif ?? 0 }} Guru</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person-check-fill fa-3x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card Total Semua Guru (4 kolom) --}}
            <div class="col-md-4 mb-4 pr-3">
                <div class="card card-body shadow-sm border-left-info p-4">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="font-weight-bold text-info text-uppercase mb-2" style="font-size: 0.8rem;">
                                Total Semua Guru
                            </div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $totalGuru ?? 0 }} Guru</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person-fill fa-3x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card Total Guru PNS (4 kolom) --}}
            <div class="col-md-4 mb-4">
                <div class="card card-body shadow-sm border-left-warning p-4">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="font-weight-bold text-warning text-uppercase mb-2" style="font-size: 0.8rem;">
                                Guru Aktif Status PNS
                            </div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $totalGuruPNS ?? 0 }} Guru</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-award-fill fa-3x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>

        {{-- ======================================================== --}}
        {{-- 2. BARIS FILTER DAN TOMBOL AKSI (Diratakan: 3 filter + 1 tombol submit) --}}
        {{-- ======================================================== --}}
        <form method="GET" action="{{ route('admin.guru') }}">
            <div class="row mb-2 align-items-end"> {{-- Gunakan align-items-end agar input rata bawah dengan tombol --}}

                {{-- Cari Nama/NIP (Porsi lebih besar: 4/12) --}}
                <div class="col-md-4 col-sm-12 mb-3">
                    <label for="search_guru" class="form-label font-weight-bold">Cari Nama atau NIP</label>
                    <input type="text" name="search" id="search_guru" class="form-control"
                        placeholder="Masukkan kata kunci..." value="{{ $request->get('search') }}">
                </div>

                {{-- Filter Status Aktif/Nonaktif (Porsi 3/12) --}}
                <div class="col-md-3 col-sm-6 mb-3">
                    <label for="filter_status" class="form-label font-weight-bold">Status</label>
                    <x-dropdown name="status" id="filter_status">
                        <option value="">Semua Status</option>
                        @foreach ($allStatus as $status)
                            <option value="{{ $status }}" {{ $request->get('status') == $status ? 'selected' : '' }}>
                                {{ $status }}
                            </option>
                        @endforeach
                    </x-dropdown>
                </div>

                {{-- Filter Status Kepegawaian (Porsi 3/12) --}}
                <div class="col-md-3 col-sm-6 mb-3">
                    <label for="filter_kepegawaian" class="form-label font-weight-bold">Status Kepegawaian</label>
                    <x-dropdown name="status_kepegawaian" id="filter_kepegawaian">
                        <option value="">Semua Kepegawaian</option>
                        @foreach ($allStatusKepegawaian as $sk)
                            <option value="{{ $sk }}"
                                {{ $request->get('status_kepegawaian') == $sk ? 'selected' : '' }}>
                                {{ $sk }}
                            </option>
                        @endforeach
                    </x-dropdown>
                </div>

                {{-- Tombol Terapkan Filter (Porsi 2/12) --}}
                <div class="col-md-2 col-sm-12 mb-3">
                    {{-- Kosongkan label, nanti tombol akan rata dengan input/dropdown di atasnya --}}
                    <label class="form-label font-weight-bold d-none d-md-block" style="visibility: hidden;">Aksi</label>
                    <button type="submit" class="btn btn-primary rounded-pill w-100">
                        <i class="bi bi-funnel-fill"></i> Terapkan
                    </button>
                </div>
            </div>
        </form>

        {{-- ======================================================== --}}
        {{-- 3. BARIS TOMBOL UTAMA (Tambah dan Export) --}}
        {{-- ======================================================== --}}
        <div class="row mb-2">
            <div class="col-12 text-right">
                <button type="button" class="btn btn-success rounded-pill">
                    <i class="bi bi-person-plus-fill"></i> Tambah Guru
                </button>
                <button type="button" class="btn btn-info rounded-pill ms-2 text-white">
                    <i class="bi bi-file-earmark-spreadsheet"></i> Export CSV
                </button>
            </div>
        </div>
        <br>

        {{-- ============================================= --}}
        {{-- 4. WRAPPER TABEL DATA GURU (Tidak ada perubahan) --}}
        {{-- ============================================= --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Kelola data guru • {{ $guruData->total() }} guru</h6>
            </div>
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-hover" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>NIP</th>
                                <th>Nama</th>
                                <th>Jenis Kelamin</th>
                                <th>Status Kepegawaian</th>
                                <th>Status Aktif</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($guruData as $index => $guru)
                                <tr>
                                {{-- Nomor urut --}}
                                <td>{{ $guruData->firstItem() + $index }}</td>

                                {{-- NIP --}}
                                <td>{{ $guru->nip }}</td>

                                {{-- Nama Guru (kolom di DB: nama_guru) --}}
                                <td>{{ $guru->nama_guru }}</td>

                                {{-- Email --}}
                                <td>{{ $guru->email ?? '–' }}</td>

                                {{-- No HP --}}
                                <td>{{ $guru->no_hp ?? '–' }}</td>

                                {{-- Kota / Alamat singkat --}}
                                <td>{{ $guru->kota_rmh ?? '–' }}</td>
                                </tr>
                                @empty
                                <tr>
                                <td colspan="6" class="text-center">Belum ada data guru</td>
                                 </tr>
                                @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINASI --}}
                <div class="row align-items-center">
                    <div class="col-md-6 mb-3 mb-md-0">
                        Showing {{ $guruData->firstItem() }} to {{ $guruData->lastItem() }} of {{ $guruData->total() }}
                        results
                    </div>
                    <div class="col-md-6">
                        @include('components.pagination', ['paginator' => $guruData])
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@endpush