@extends('layouts.template')

@section('title', 'Input Absensi')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('guru.absensi.kelas') }}">Pilih Jadwal Absensi</a></li>
    {{-- PERBAIKAN: Tambahkan parameter tanggal saat kembali ke index --}}
    <li class="breadcrumb-item active" aria-current="page">Input Absensi
        ({{ \Carbon\Carbon::parse($tanggalAbsen)->format('d F Y') }})</li>
@endsection

@section('content')
    <div class="mb-3">
        {{-- PERBAIKAN: Tambahkan parameter tanggal saat Kembali ke Pilih Jadwal --}}
        <a href="{{ route('guru.absensi.kelas', ['tanggal' => $tanggalAbsen]) }}" class="btn btn-danger">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Pilih Jadwal
        </a>
        <h4 class="d-inline-block ms-3 text-secondary">Tanggal Absensi:
            {{ \Carbon\Carbon::parse($tanggalAbsen)->translatedFormat('l, d F Y') }}</h4>
    </div>

    <div class="card shadow-sm p-4">

        <h2 class="h5 mb-3">Input Kehadiran Siswa</h2>

        {{-- Menampilkan pesan sukses/error bawaan Laravel/Session --}}
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

        {{-- Detail Absensi --}}
        <div class="card mb-4 border-info">
            <div class="card-header bg-info text-white py-2">
                <i class="fas fa-calendar-alt me-2"></i> Detail Sesi Mengajar
            </div>
            <div class="card-body p-3">
                @php
                    // Pastikan Anda telah mengimpor Carbon di controller jika belum (sudah dilakukan di perbaikan sebelumnya)
                    // Menggunakan DateTime bawaan PHP untuk perhitungan durasi
                    try {
                        $dt1 = new DateTime($jadwal->jam_mulai);
                        $dt2 = new DateTime($jadwal->jam_selesai);
                        $interval = $dt1->diff($dt2);
                        $menitTotal = $interval->h * 60 + $interval->i;
                        $jam = floor($menitTotal / 60);
                        $menit = $menitTotal % 60;
                        $durasiMengajar =
                            ($jam > 0 ? $jam . ' jam ' : '') . ($menit > 0 || $jam == 0 ? $menit . ' menit' : '');
                        $durasiMengajar = trim($durasiMengajar);
                    } catch (\Exception $e) {
                        $durasiMengajar = 'N/A';
                    }
                @endphp
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm m-0">
                            <tbody>
                                <tr>
                                    <td class="fw-bold">Tingkat Kelas</td>
                                    <td>: {{ $jadwal->kelas->tingkat_kelas ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold" style="width: 150px;">Kelas / Mapel</td>
                                    <td>: {{ $jadwal->kelas->nama_kelas ?? 'N/A' }} /
                                        {{ $jadwal->penugasan->mapel->nama_mapel ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Tahun Ajaran</td>
                                    <td>: {{ $jadwal->kelas->tahun_ajaran ?? 'N/A' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm m-0">
                            <tbody>
                                <tr>
                                    <td class="fw-bold">Semester</td>
                                    <td>: {{ $jadwal->kelas->semester ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold" style="width: 150px;">Waktu Mengajar</td>
                                    <td>: {{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}
                                        ({{ $durasiMengajar }})</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Terakhir Disimpan</td>
                                    <td>:
                                        @if ($waktuTerakhirSimpan)
                                            {{-- PERBAIKAN: Gunakan Carbon untuk memastikan format yang benar --}}
                                            {{ \Carbon\Carbon::parse($waktuTerakhirSimpan)->format('H:i:s') }}
                                        @else
                                            <span class="text-danger">Belum pernah disimpan</span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{-- Akhir Detail Absensi --}}

        <hr>

        {{-- Form untuk Submit Absensi --}}
        <form action="{{ route('guru.absensi.store', $jadwal->jadwal_mapel_id) }}" method="POST" id="absensiForm">
            @csrf

            {{-- PERBAIKAN UTAMA: Tambahkan hidden input untuk mengirim tanggal_absen_input --}}
            <input type="hidden" name="tanggal_absen_input" value="{{ $tanggalAbsen }}">

            {{-- Bagian Tabel Absensi --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="m-0">Daftar Siswa Kelas {{ $jadwal->kelas->nama_kelas ?? 'N/A' }}</h5>
                <div class="d-flex align-items-center">
                    {{-- Input Pencarian --}}
                    <div style="width: 250px;" class="me-2">
                        <input type="text" class="form-control" id="cariSiswa" placeholder="Cari NIS atau Nama Siswa...">
                    </div>
                    {{-- Tombol Set Semua Hadir --}}
                    <button type="button" class="btn btn-primary me-2" id="setSemuaHadir">
                        <i class="fas fa-users me-1"></i> Set Semua Hadir
                    </button>
                    {{-- Tombol Simpan --}}
                    <button type="submit" class="btn btn-success" id="simpanAbsen">
                        <i class="fas fa-save me-1"></i> Simpan Absen
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle" id="absensiTable">
                    <thead>
                        <tr class="table-light">
                            <th>No</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th style="min-width: 150px;">Status</th>
                            <th style="min-width: 250px;">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($dataSiswaAbsen as $index => $data)
                            <tr data-nis="{{ $data->nis }}" data-nama="{{ $data->nama }}" class="siswa-row">
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $data->nis }}</td>
                                <td>{{ $data->nama }}</td>
                                <td>
                                    <input type="hidden" name="absensi[{{ $index }}][id_siswa]"
                                        value="{{ $data->id_siswa }}">

                                    <select class="form-select status-absensi" name="absensi[{{ $index }}][status]">
                                        <option value="" disabled
                                            {{ is_null($data->status) && !old("absensi.$index.status") ? 'selected' : '' }}>
                                            Pilih Status
                                        </option>
                                        <option value="Hadir"
                                            {{ old("absensi.$index.status") == 'Hadir' || $data->status == 'Hadir' ? 'selected' : '' }}>
                                            Hadir
                                        </option>
                                        <option value="Izin"
                                            {{ old("absensi.$index.status") == 'Izin' || $data->status == 'Izin' ? 'selected' : '' }}>
                                            Izin
                                        </option>
                                        <option value="Sakit"
                                            {{ old("absensi.$index.status") == 'Sakit' || $data->status == 'Sakit' ? 'selected' : '' }}>
                                            Sakit
                                        </option>
                                        <option value="Alpha"
                                            {{ old("absensi.$index.status") == 'Alpha' || $data->status == 'Alpha' ? 'selected' : '' }}>
                                            Tidak Hadir 
                                        </option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control catatan-absensi"
                                        placeholder="Cth: Surat dokter / Urusan keluarga / Tanpa keterangan"
                                        name="absensi[{{ $index }}][catatan]"
                                        value="{{ old("absensi.$index.catatan") ?? ($data->keterangan ?? '') }}"
                                        data-siswa-nama="{{ $data->nama }}"
                                        {{ old("absensi.$index.status") == 'Hadir' || $data->status == 'Hadir' || is_null(old("absensi.$index.status") && is_null($data->status)) ? 'disabled' : '' }}>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada siswa terdaftar di kelas ini untuk tahun
                                    ajaran aktif.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- End Tabel Absensi --}}
        </form>
    </div>

    {{-- Panggil komponen modal kustom --}}
    @include('components.alert', [
        'id' => 'validationModal',
        'title' => 'Gagal Menyimpan Absensi',
        'type' => 'danger',
        'message' => 'Terdapat siswa yang status kehadirannya belum diisi.',
    ])

@endsection

@push('scripts')
    {{-- Anda perlu memastikan Carbon/Locale Indonesia sudah diatur di environment Laravel Anda agar translatedFormat() berfungsi --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ... (Kode JavaScript sebelumnya tetap) ...

            // Inisialisasi Modal
            const validationModal = new bootstrap.Modal(document.getElementById('validationModal'));
            const modalMessage = document.getElementById('validationModal-message');
            const modalList = document.getElementById('validationModal-list');

            const setSemuaHadirButton = document.getElementById('setSemuaHadir');
            const cariSiswaInput = document.getElementById('cariSiswa');
            const absensiForm = document.getElementById('absensiForm');

            // --- Fungsi untuk mengontrol input Catatan (Catatan wajib disable/kosong jika Hadir) ---
            const controlCatatan = (selectElement) => {
                const row = selectElement.closest('tr');
                const catatanInput = row ? row.querySelector('.catatan-absensi') : null;
                if (!catatanInput) return;

                // Catatan hanya di-disable/dikosongkan jika status Hadir atau Belum Dipilih
                if (selectElement.value === 'Hadir' || selectElement.value === '') {
                    catatanInput.value = '';
                    catatanInput.disabled = true;
                } else {
                    // Enable jika status non-Hadir (Izin, Sakit, Alpha)
                    catatanInput.disabled = false;
                }
            };

            // --- Inisialisasi dan Event Listener untuk Status Absensi ---
            document.querySelectorAll('.status-absensi').forEach(select => {
                controlCatatan(select);

                select.addEventListener('change', function() {
                    controlCatatan(this);
                });
            });

            // --- Fungsi Pencarian NIS atau Nama Siswa ---
            cariSiswaInput.addEventListener('keyup', function() {
                const searchValue = this.value.toLowerCase();
                document.querySelectorAll('.siswa-row').forEach(row => {
                    const nis = row.dataset.nis.toLowerCase();
                    const nama = row.dataset.nama.toLowerCase();

                    if (nis.includes(searchValue) || nama.includes(searchValue)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });

            // --- Logika Tombol: Setel semua ke Hadir (TANPA ALERT) ---
            if (setSemuaHadirButton) {
                setSemuaHadirButton.addEventListener('click', function() {
                    document.querySelectorAll('.siswa-row').forEach(row => {
                        const statusSelect = row.querySelector('.status-absensi');

                        if (row.style.display !== 'none') {
                            // Set nilai dropdown menjadi 'Hadir'
                            statusSelect.value = 'Hadir';

                            // Memanggil fungsi kontrol catatan
                            controlCatatan(statusSelect);

                            // Memicu event 'change' secara manual (penting untuk konsistensi)
                            const event = new Event('change');
                            statusSelect.dispatchEvent(event);
                        }
                    });
                });
            }


            // --- PERBAIKAN LOGIKA SUBMIT FORM: Menggunakan Modal Kustom untuk Status Belum Dipilih ---
            absensiForm.addEventListener('submit', function(e) {
                let siswaBelumDipilih = [];

                document.querySelectorAll('.siswa-row').forEach(row => {
                    const statusSelect = row.querySelector('.status-absensi');
                    const catatanInput = row.querySelector('.catatan-absensi');
                    // Ambil nama dari data-attribute, bukan dari input catatan
                    const namaSiswa = row.dataset.nama;

                    // 1. Cek status yang belum dipilih
                    if (statusSelect.value === '') {
                        siswaBelumDipilih.push(namaSiswa);
                    }
                });

                if (siswaBelumDipilih.length > 0) {
                    e.preventDefault(); // Mencegah submit form

                    // Siapkan konten Modal
                    modalMessage.textContent = 'Status kehadiran siswa berikut belum diisi:';
                    modalList.innerHTML = ''; // Kosongkan list sebelumnya

                    const ul = document.createElement('ul');
                    ul.className = 'list-unstyled';
                    siswaBelumDipilih.forEach(nama => {
                        const listItem = document.createElement('li');
                        listItem.textContent = `- ${nama}`;
                        ul.appendChild(listItem);
                    });
                    modalList.appendChild(ul);


                    // Tampilkan Modal Kustom
                    validationModal.show();
                    return;
                }

                // Jika tidak ada siswa yang "Belum Dipilih" statusnya, form akan di-submit secara otomatis.
            });

        });
    </script>
@endpush
