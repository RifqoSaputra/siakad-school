@extends('layouts.template')

@section('title', 'Manajemen Siswa')

@section('content')
    <div class="container-fluid">

        {{-- ======================================================= --}}
        {{-- 1. CARD RINGKASAN --}}
        {{-- ======================================================= --}}
        <div class="row mb-2">
            <div class="col-md-6 mb-4 pr-3">
                <div class="card card-body shadow-sm border-left-primary p-4">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="font-weight-bold text-primary text-uppercase mb-2" style="font-size: 0.8rem;">
                                Total Siswa Aktif ({{ $tahunAjaranAktif }})
                            </div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">
                                {{ $totalSiswaAktif }} Siswa
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person-check-fill fa-3x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card card-body shadow-sm border-left-warning p-4">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="font-weight-bold text-warning text-uppercase mb-2" style="font-size: 0.8rem;">
                                Siswa Non-Aktif / Perlu Aksi
                            </div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">
                                {{ $totalSiswaNonAktif }} Siswa
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person-x-fill fa-3x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ======================================================= --}}
        {{-- 2. FILTER --}}
        {{-- ======================================================= --}}
        <form method="GET" action="{{ route('admin.siswa') }}">
            <div class="row mb-3 align-items-end">

                <div class="col-md-4">
                    <label class="form-label font-weight-bold">Cari Nama / NIS</label>
                    <input type="text" name="search" class="form-control" value="{{ $request->get('search') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label font-weight-bold">Tahun Ajaran</label>
                    <x-dropdown name="tahun_ajaran" onchange="this.form.submit()">
                        @foreach ($allTahunAjaran as $ta)
                            <option value="{{ $ta }}" {{ $tahunAjaranAktif == $ta ? 'selected' : '' }}>
                                {{ $ta }}
                            </option>
                        @endforeach
                    </x-dropdown>
                </div>

                <div class="col-md-3">
                    <label class="form-label font-weight-bold">Kelas</label>
                    <x-dropdown name="kelas">
                        <option value="">Semua Kelas</option>
                        @foreach ($allKelas as $kelas)
                            <option value="{{ $kelas->kelas_id }}"
                                {{ $request->get('kelas') == $kelas->kelas_id ? 'selected' : '' }}>
                                {{ $kelas->nama_kelas_lengkap }}
                            </option>
                        @endforeach
                    </x-dropdown>
                </div>

                {{-- TOMBOL TERAPKAN FILTER --}}
                <div class="col-md-1">
                    <button class="btn btn-primary w-100 rounded-pill">
                        <i class="bi bi-funnel-fill"></i>
                    </button>
                </div>

            </div>
        </form>

        {{-- ======================================================= --}}
        {{-- 3. TABEL --}}
        {{-- ======================================================= --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    Kelola Data Siswa • {{ $siswaData->total() }} siswa
                </h6>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIS</th>
                                <th>Nama</th>
                                <th>Kelas</th>
                                <th>Status Akademik</th>
                                <th>Tanggal Lahir</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($siswaData as $index => $siswa)
                                <tr>
                                    <td>{{ $siswaData->firstItem() + $index }}</td>
                                    <td>{{ $siswa->nis }}</td>
                                    <td>{{ $siswa->nama }}</td>
                                    <td>{{ $siswa->kelas_sekarang ?? '-' }}</td>
                                    <td>
                                        @if ($siswa->status_akademik === 'Aktif')
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Non Aktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $siswa->tgl_lahir ? \Carbon\Carbon::parse($siswa->tgl_lahir)->format('d M Y') : '-' }}
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-warning btn-edit-siswa"
                                            data-id="{{ $siswa->id_siswa }}" data-nis="{{ $siswa->nis }}"
                                            data-nama="{{ $siswa->nama }}" data-tgl="{{ $siswa->tgl_lahir }}"
                                            data-status="{{ $siswa->status_akademik }}">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        Tidak ada data siswa
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION --}}
                <div class="row">
                    <div class="col-md-6">
                        Showing {{ $siswaData->firstItem() }} to {{ $siswaData->lastItem() }}
                        of {{ $siswaData->total() }} results
                    </div>
                    <div class="col-md-6">
                        @include('components.pagination', ['paginator' => $siswaData])
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ======================================================= --}}
    {{-- MODAL EDIT SISWA --}}
    {{-- ======================================================= --}}
    <div class="modal fade" id="modalEditSiswa" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Data Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="edit_id_siswa">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">NIS</label>
                            <input type="text" id="edit_nis" class="form-control" disabled>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nama Siswa</label>
                            <input type="text" id="edit_nama" class="form-control">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" id="edit_tgl_lahir" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Status Akademik</label>
                            <select id="edit_status" class="form-control">
                                <option value="Aktif">Aktif</option>
                                <option value="Non Aktif">Non Aktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="alert alert-info mb-0">
                        <small>
                            Perubahan status akan mempengaruhi keaktifan siswa pada tahun ajaran berjalan.
                        </small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" disabled>Simpan Perubahan</button>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = new bootstrap.Modal(document.getElementById('modalEditSiswa'));

            document.querySelectorAll('.btn-edit-siswa').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.getElementById('edit_id_siswa').value = this.dataset.id;
                    document.getElementById('edit_nis').value = this.dataset.nis;
                    document.getElementById('edit_nama').value = this.dataset.nama;
                    document.getElementById('edit_tgl_lahir').value = this.dataset.tgl;
                    document.getElementById('edit_status').value = this.dataset.status;
                    modal.show();
                });
            });
        });
    </script>
@endpush
