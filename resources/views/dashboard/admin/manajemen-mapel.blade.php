@extends('layouts.template')

@section('title', 'Manajemen Mata Pelajaran')

@section('content')
    <div class="container-fluid">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-gray-800">Manajemen Mata Pelajaran</h1>
        </div>

        {{-- SEARCH + ADD --}}
        <div class="card shadow-sm mb-3">
            <div class="card-body d-flex gap-2">
                <input type="text" id="searchMapel" class="form-control" placeholder="Cari kode / nama mapel / guru...">
                <button class="btn btn-primary" id="btnTambahMapel">
                    <i class="bi bi-plus-circle"></i> Tambah
                </button>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="card shadow">
            <div class="card-body table-responsive">
                <table class="table table-hover" id="tableMapel">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Nama Mapel</th>
                            <th>Guru Pengampu</th>
                            <th>Status</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($mapel as $i => $m)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $m->kode_mapel }}</td>
                                <td>{{ $m->nama_mapel }}</td>
                                <td>
                                    @forelse ($m->penugasanGuru as $gm)
                                        <span class="badge bg-info">
                                            {{ $gm->guru->nama_guru ?? '-' }}
                                        </span>
                                    @empty
                                        <span class="text-muted">Belum ada</span>
                                    @endforelse
                                </td>
                                <td>
                                    <span class="badge bg-{{ $m->status ? 'success' : 'secondary' }}">
                                        {{ $m->status ? 'Aktif' : 'Non Aktif' }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning btn-edit-mapel" data-id="{{ $m->mapel_id }}"
                                        data-kode="{{ $m->kode_mapel }}" data-nama="{{ $m->nama_mapel }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL MAPEL --}}
    <div class="modal fade" id="modalMapel">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Form Mapel</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="mapel_id">
                    <div class="mb-3">
                        <label>Kode Mapel</label>
                        <input type="text" id="kode_mapel" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Nama Mapel</label>
                        <input type="text" id="nama_mapel" class="form-control">
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('searchMapel').addEventListener('keyup', function() {
            let keyword = this.value.toLowerCase();
            document.querySelectorAll('#tableMapel tbody tr').forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(keyword) ?
                    '' : 'none';
            });
        });

        const modalMapel = new bootstrap.Modal(document.getElementById('modalMapel'));

        document.getElementById('btnTambahMapel').onclick = () => {
            document.getElementById('mapel_id').value = '';
            document.getElementById('kode_mapel').value = '';
            document.getElementById('nama_mapel').value = '';
            modalMapel.show();
        };

        document.querySelectorAll('.btn-edit-mapel').forEach(btn => {
            btn.onclick = () => {
                document.getElementById('mapel_id').value = btn.dataset.id;
                document.getElementById('kode_mapel').value = btn.dataset.kode;
                document.getElementById('nama_mapel').value = btn.dataset.nama;
                modalMapel.show();
            };
        });
    </script>
@endpush
