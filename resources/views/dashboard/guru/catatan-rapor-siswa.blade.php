@extends('layouts.template')

@section('title', 'Input Catatan Rapor')
@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Catatan Rapor Wali Kelas</li>
@endsection

@section('content')
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <div class="mb-3 d-flex justify-content-between align-items-center">
        <h2 class="h4 m-0 text-primary">Input Catatan Rapor Siswa</h2>

        {{-- STATUS PUBLIKASI --}}
        <span
            class="badge bg-{{ $statusPublikasi == 'Terkunci' ? 'danger' : ($statusPublikasi == 'Draft' ? 'secondary' : 'success') }} py-2 px-3">
            Status: {{ $statusPublikasi }}
        </span>
    </div>

    <div class="card shadow-sm p-4">

        {{-- ALERT --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- DETAIL KELAS --}}
        <div class="card mb-4 border-primary">
            <div class="card-header bg-primary text-white py-2">
                <i class="fas fa-school me-2"></i> Detail Kelas yang Diwalikan
            </div>
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm m-0">
                            <tr>
                                <td class="fw-bold" style="width:150px;">Kelas</td>
                                <td>: {{ $kelas->tingkat_kelas }} / {{ $kelas->nama_kelas }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Tahun Ajaran</td>
                                <td>: {{ $kelas->tahun_ajaran }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-md-6">
                        <table class="table table-borderless table-sm m-0">
                            <tr>
                                <td class="fw-bold" style="width:150px;">Semester</td>
                                <td>: {{ $kelas->semester }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Terakhir Disimpan</td>
                                <td>:
                                    @if ($waktuTerakhirSimpan)
                                        {{ \Carbon\Carbon::parse($waktuTerakhirSimpan)->format('d F Y H:i:s') }}
                                    @else
                                        <span class="text-danger">Belum pernah disimpan</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>

                </div>
            </div>
        </div>

        {{-- FORM CATATAN --}}
        <form action="{{ route('guru.rapor.walikelas.store') }}" method="POST" id="catatanRaporForm">
            @csrf

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="m-0">Input Catatan Siswa di Kelas {{ $kelas->nama_kelas }}</h5>

                <div class="d-flex">
                    @if ($statusPublikasi === 'Draft')
                        <button type="button" class="btn btn-warning me-2" id="kunciFinalBtn">
                            <i class="fas fa-lock me-1"></i> Submit Catatan
                        </button>
                    @endif

                    <button type="submit" class="btn btn-success" @if ($statusPublikasi !== 'Draft') disabled @endif>
                        <i class="fas fa-save me-1"></i> Simpan Catatan
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>NIS</th>
                            <th style="min-width:200px;">Nama Siswa</th>
                            <th style="min-width:150px;">Predikat Sikap</th>
                            <th style="min-width:300px;">Catatan Wali Kelas</th>
                            <th style="min-width:150px;">Status Kenaikan</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($dataSiswaRapor as $i => $data)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $data->nis }}</td>
                                <td>{{ $data->nama }}</td>

                                <td>
                                    <input type="hidden" name="rapor[{{ $i }}][id_siswa]"
                                        value="{{ $data->id_siswa }}">

                                    <select name="rapor[{{ $i }}][predikat_sikap]" class="form-select"
                                        @disabled($statusPublikasi !== 'Draft')>
                                        <option value="" disabled selected>Pilih Predikat</option>
                                        @foreach (['Sangat Baik', 'Baik', 'Cukup', 'Kurang'] as $p)
                                            <option value="{{ $p }}" @selected($data->predikat_sikap == $p)>
                                                {{ $p }}</option>
                                        @endforeach
                                    </select>
                                </td>

                                <td>
                                    <textarea class="form-control" rows="2" name="rapor[{{ $i }}][catatan_walikelas]"
                                        @disabled($statusPublikasi !== 'Draft')>{{ $data->catatan_walikelas }}</textarea>
                                </td>

                                <td>
                                    <select class="form-select" name="rapor[{{ $i }}][status_kenaikan]"
                                        @disabled($statusPublikasi !== 'Draft' || strtolower($kelas->semester) == 'ganjil')>

                                        @foreach ($opsiKenaikan as $opt)
                                            <option value="{{ $opt }}" @selected($data->status_kenaikan == $opt)
                                                @disabled($opt === 'Belum Final')>
                                                {{ $opt }}
                                            </option>
                                        @endforeach

                                        @if (!in_array($data->status_kenaikan, $opsiKenaikan) && $data->status_kenaikan)
                                            <option selected value="{{ $data->status_kenaikan }}">
                                                {{ $data->status_kenaikan }} (Data Lama)</option>
                                        @endif

                                    </select>

                                    @if (strtolower($kelas->semester) == 'ganjil')
                                        <small class="text-muted">Diisi hanya pada Semester Genap.</small>
                                    @endif
                                </td>

                            </tr>

                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada siswa.</td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </form>
    </div>

    {{-- MODAL KONFIRMASI (SAMA DENGAN INPUT NILAI HARIAN) --}}
    @include('components.confirm-delete', [
        'id' => 'lockWarningModal',
        'title' => 'Kunci Final Catatan Rapor',
        'message' =>
            'Anda akan mengunci seluruh catatan rapor untuk kelas ini. Setelah dikunci, catatan tidak dapat diubah lagi. Pastikan semua data sudah benar.',
        'confirmText' => 'Ya, Kunci Sekarang',
        'cancelText' => 'Batal',
        'confirmClass' => 'bg-yellow-600 hover:bg-yellow-700',
    ])

@endsection


@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const kunciFinalBtn = document.getElementById("kunciFinalBtn");
            const confirmBtn = document.getElementById("lockWarningModal-confirm-btn");

            const kelasId = "{{ $kelas->kelas_id }}";
            const csrf = document.querySelector('meta[name="csrf-token"]').content;

            // === BUKA MODAL ===
            if (kunciFinalBtn) {
                kunciFinalBtn.addEventListener("click", function() {
                    window.showModal("lockWarningModal");
                });
            }

            // === KONFIRMASI LOCK ===
            if (confirmBtn) {
                confirmBtn.addEventListener("click", function(e) {
                    e.preventDefault();

                    confirmBtn.disabled = true;
                    confirmBtn.innerHTML =
                        '<span class="spinner-border spinner-border-sm"></span> Mengunci...';

                    fetch("{{ route('guru.rapor.walikelas.lock') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "Accept": "application/json", // <==== WAJIB kalau tetap pakai wantsJson
                                "X-CSRF-TOKEN": csrf,
                            },
                            body: JSON.stringify({
                                kelas_id: kelasId
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            window.hideModal("lockWarningModal");
                            if (data.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: data.message,
                                }).then(() => location.reload());
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: data.message,
                                });
                            }
                        })
                        .catch(err => {
                            confirmBtn.disabled = false;
                            confirmBtn.innerHTML = "Ya, Kunci Sekarang";
                            alert("Gagal: " + err.message);
                        });
                });
            }

        });
    </script>
@endpush
