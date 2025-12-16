@extends('layouts.template')

@section('title', 'Manajemen Guru')

@section('content')
    <div class="container-fluid">

        {{-- CARD RINGKASAN --}}
        <div class="row mb-4">
            @foreach ([['Total Guru Aktif', $totalGuruAktif, 'primary', 'person-check-fill'], ['Total Semua Guru', $totalGuru, 'info', 'person-fill'], ['Guru Aktif Status PNS', $totalGuruPNS, 'warning', 'award-fill']] as $card)
                <div class="col-md-4">
                    <div class="card shadow-sm border-left-{{ $card[2] }}">
                        <div class="card-body">
                            <div class="text-xs text-uppercase font-weight-bold text-{{ $card[2] }}">
                                {{ $card[0] }}
                            </div>
                            <div class="h4 font-weight-bold">{{ $card[1] }} Guru</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- SEARCH --}}
        <div class="mb-3">
            <input type="text" id="searchGuru" class="form-control" placeholder="Cari nama atau NIP...">
        </div>

        {{-- TABEL --}}
        <div class="card shadow">
            <div class="card-body table-responsive">
                <table class="table table-hover" id="guruTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIP</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>No HP</th>
                            <th>Kota</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($guruData as $i => $guru)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $guru->nip }}</td>
                                <td>{{ $guru->nama_guru }}</td>
                                <td>{{ $guru->email ?? '-' }}</td>
                                <td>{{ $guru->no_hp ?? '-' }}</td>
                                <td>{{ $guru->kota_rmh ?? '-' }}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning"
                                        onclick='openEditModal(@json($guru))'>
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT --}}
    <div class="modal fade" id="editGuruModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form method="POST" action="{{ route('admin.guru') }}" class="modal-content">
                @csrf
                <input type="hidden" name="id_guru" id="id_guru">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Data Guru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body row g-3">
                    <div class="col-md-6">
                        <label>NIP</label>
                        <input type="text" name="nip" id="nip" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label>Nama Guru</label>
                        <input type="text" name="nama_guru" id="nama_guru" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label>Email</label>
                        <input type="email" name="email" id="email" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label>No HP</label>
                        <input type="text" name="no_hp" id="no_hp" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label>Kota</label>
                        <input type="text" name="kota_rmh" id="kota_rmh" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label>Alamat</label>
                        <input type="text" name="alamat_rmh" id="alamat_rmh" class="form-control">
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <script>
        function openEditModal(guru) {
            for (const key in guru) {
                if (document.getElementById(key)) {
                    document.getElementById(key).value = guru[key] ?? '';
                }
            }
            new bootstrap.Modal(document.getElementById('editGuruModal')).show();
        }

        // SEARCH CEPAT
        document.getElementById('searchGuru').addEventListener('keyup', function() {
            const value = this.value.toLowerCase();
            document.querySelectorAll('#guruTable tbody tr').forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(value) ?
                    '' :
                    'none';
            });
        });
    </script>
@endpush
