@extends('layouts.template')

@section('title', 'Manajemen Kelas')

@section('content')
    <div class="container-fluid">

        <h1 class="h3 mb-4 text-gray-800">Manajemen Kelas</h1>

        <div class="card shadow-sm mb-3">
            <div class="card-body d-flex gap-2">
                <input type="text" id="searchKelas" class="form-control" placeholder="Cari kelas / wali kelas...">
                <button class="btn btn-primary" id="btnTambahKelas">
                    <i class="bi bi-plus-circle"></i> Tambah
                </button>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-body table-responsive">
                <table class="table table-hover" id="tableKelas">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kelas</th>
                            <th>Semester</th>
                            <th>Wali Kelas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($kelas as $i => $k)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $k->nama_kelas_lengkap }}</td>
                                <td>{{ $k->semester }}</td>
                                <td>{{ $k->waliKelas->nama_guru ?? '-' }}</td>
                                <td>
                                    <button class="btn btn-sm btn-info btn-walas" data-id="{{ $k->kelas_id }}"
                                        data-walas="{{ $k->walikelas }}">
                                        <i class="bi bi-person-badge"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL WALAS --}}
    <div class="modal fade" id="modalWalas">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Wali Kelas</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="kelas_id">
                    <label>Pilih Guru</label>
                    <select class="form-control" id="walas">
                        <option value="">-- Pilih Guru --</option>
                        @foreach ($guruList as $g)
                            <option value="{{ $g->id_guru }}">{{ $g->nama_guru }}</option>
                        @endforeach
                    </select>
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
        document.getElementById('searchKelas').addEventListener('keyup', function() {
            let keyword = this.value.toLowerCase();
            document.querySelectorAll('#tableKelas tbody tr').forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(keyword) ?
                    '' : 'none';
            });
        });

        const modalWalas = new bootstrap.Modal(document.getElementById('modalWalas'));

        document.querySelectorAll('.btn-walas').forEach(btn => {
            btn.onclick = () => {
                document.getElementById('kelas_id').value = btn.dataset.id;
                document.getElementById('walas').value = btn.dataset.walas || '';
                modalWalas.show();
            };
        });
    </script>
@endpush
