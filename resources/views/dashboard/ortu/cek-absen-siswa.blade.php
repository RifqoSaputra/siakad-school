@extends('layouts.template')

@section('title', 'Cek Kehadiran Siswa')
@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Cek Kehadiran Siswa</li>
@endsection

@section('content')

    <div class="card shadow-sm p-4">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <h1 class="h3 text-primary mb-0">Laporan Kehadiran Siswa</h1>
        </div>

        {{-- FILTER --}}
        <div class="card shadow-sm p-4 mb-4">
            <div class="d-flex flex-wrap align-items-center gap-3 mb-4">
                <label for="filter_kelas" class="form-label mb-0 fw-bold">Pilih Kelas:</label>
                <select id="filter_kelas" class="form-select w-auto">
                    @forelse ($daftarKelas as $kelas)
                        <option value="{{ $kelas['id'] }}" {{ $kelasAktifId == $kelas['id'] ? 'selected' : '' }}>
                            {{ $kelas['nama'] }}
                        </option>
                    @empty
                        <option value="" disabled>Tidak ada data kelas</option>
                    @endforelse
                </select>

                <label for="filter_mapel" class="form-label mb-0 fw-bold">Pilih Mata Pelajaran:</label>
                <select id="filter_mapel" class="form-select w-auto">
                    @forelse ($daftarMapel as $mapel)
                        <option value="{{ $mapel['id'] }}" {{ $mapelAktifId == $mapel['id'] ? 'selected' : '' }}>
                            {{ $mapel['nama'] }}
                        </option>
                    @empty
                        <option value="" disabled>Pilih Kelas terlebih dahulu</option>
                    @endforelse
                </select>

                <button class="btn btn-primary" id="btn-tampilkan-filter">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
            </div>

            {{-- ALERT/WARNING VALIDASI KETIDAKHADIRAN --}}
            <div id="warning-container">
                @if ($warning['level'] > 0)
                    <div class="alert {{ $warning['class'] }} alert-dismissible fade show shadow-sm" role="alert">
                        <h5 class="alert-heading h6 mb-1"><i class="fas fa-exclamation-triangle me-2"></i> Status Kehadiran
                            Kritis</h5>
                        <div id="warning-message">{!! $warning['message'] !!}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>

            {{-- REKAPITULASI (CARDS) - MENGGUNAKAN DATA GLOBAL DARI CONTROLLER --}}
            <h3 class="h5 fw-bold mb-3 text-secondary">Rekapitulasi Absensi (Global Kelas)</h3>
            <div class="row mb-5">
                {{-- HADIR --}}
                <div class="col-md-3 col-6 mb-3">
                    <div class="card shadow-sm border-start border-4 border-success">
                        <div class="card-body p-3">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">Hadir
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800"><span
                                    id="count-hadir">{{ $rekap['Hadir'] ?? 0 }}</span> Hari</div>
                        </div>
                    </div>
                </div>
                {{-- IZIN --}}
                <div class="col-md-3 col-6 mb-3">
                    <div class="card shadow-sm border-start border-4 border-warning">
                        <div class="card-body p-3">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">Izin
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800"><span
                                    id="count-izin">{{ $rekap['Izin'] ?? 0 }}</span> Hari</div>
                        </div>
                    </div>
                </div>
                {{-- SAKIT --}}
                <div class="col-md-3 col-6 mb-3">
                    <div class="card shadow-sm border-start border-4 border-info">
                        <div class="card-body p-3">
                            <div class="text-xs fw-bold text-info text-uppercase mb-1">Sakit
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800"><span
                                    id="count-sakit">{{ $rekap['Sakit'] ?? 0 }}</span> Hari</div>
                        </div>
                    </div>
                </div>
                {{-- TIDAK HADIR (ALPHA/ALFA) --}}
                <div class="col-md-3 col-6 mb-3">
                    <div class="card shadow-sm border-start border-4 border-danger">
                        <div class="card-body p-3">
                            <div class="text-xs fw-bold text-danger text-uppercase mb-1">Tidak
                                Hadir</div>
                            <div class="h5 mb-0 fw-bold text-gray-800"><span
                                    id="count-alpha">{{ $rekap['Alpha'] ?? 0 }}</span> Hari</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIWAYAT KEHADIRAN (VIEW TABEL) --}}
            <h3 class="h5 fw-bold mb-3 text-secondary">Riwayat Absensi (Tabel Difilter)</h3>
            <div id="view-tabel" class="table-responsive">
                <table class="table table-bordered table-striped align-middle" id="absensiTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th style="width: 20%;">Tanggal</th>
                            <th style="width: 30%;">Mata Pelajaran</th>
                            <th style="width: 15%;">Status</th>
                            <th style="width: 30%;">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody id="absensi-body">
                        {{-- Data awal dari server-side rendering --}}
                        @forelse ($dataAbsensi ?? [] as $index => $absen)
                            @php
                                $status = strtoupper($absen['status']);
                                $badgeClass = 'bg-secondary';
                                $displayStatus = $status;

                                if ($status === 'HADIR') {
                                    $badgeClass = 'bg-success';
                                } elseif ($status === 'IZIN') {
                                    $badgeClass = 'bg-warning text-dark';
                                } elseif ($status === 'SAKIT') {
                                    $badgeClass = 'bg-info text-white';
                                } elseif (in_array($status, ['ALPHA', 'ALFA', 'TIDAK HADIR'])) {
                                    $badgeClass = 'bg-danger';
                                    $displayStatus = 'TIDAK HADIR';
                                }
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="fw-medium">{{ $absen['tanggal_formatted'] }}</td>
                                <td>{{ $absen['mapel'] ?? '-' }}</td>
                                <td>
                                    <span class="badge {{ $badgeClass }} p-2">{{ $displayStatus }}</span>
                                </td>
                                <td>
                                    {{ $absen['keterangan'] ?? 'Tidak ada keterangan tambahan.' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 bg-light text-muted">
                                    <i class="fas fa-box-open me-2"></i> Tidak ada data kehadiran untuk periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnFilter = document.getElementById('btn-tampilkan-filter');
            const filterKelas = document.getElementById('filter_kelas');
            const filterMapel = document.getElementById('filter_mapel');

            // Tidak ada lagi elemen spinner/loading global
            const tbody = document.getElementById('absensi-body');
            const warningContainer = document.getElementById('warning-container');

            // Elements Counts (Untuk Cards Rekap)
            const countHadir = document.getElementById('count-hadir');
            const countIzin = document.getElementById('count-izin');
            const countSakit = document.getElementById('count-sakit');
            const countAlpha = document.getElementById('count-alpha');

            // Fungsi untuk Mengambil Data via AJAX
            async function fetchData() {
                const kelasId = filterKelas.value;
                const mapelId = filterMapel.value;

                // Beri indikasi loading pada tabel saja (misal opacity 0.5)
                tbody.style.opacity = '0.5';

                try {
                    const url = `{{ route('ortu.absensi') }}?kelas_id=${kelasId}&mapel_id=${mapelId}`;
                    const response = await fetch(url, {
                        headers: {
                            "X-Requested-With": "XMLHttpRequest"
                        }
                    });

                    if (!response.ok) throw new Error('Gagal mengambil data');

                    const data = await response.json();

                    // 1. Update Dropdown Mapel (Jika Ganti Kelas)
                    updateMapelDropdown(data.daftar_mapel, mapelId);

                    // 2. Update Counts (Rekap GLOBAL)
                    countHadir.textContent = data.rekap.Hadir || 0;
                    countIzin.textContent = data.rekap.Izin || 0;
                    countSakit.textContent = data.rekap.Sakit || 0;
                    // Gunakan Alpha karena controller sudah menyatukan Alfa ke Alpha
                    countAlpha.textContent = data.rekap.Alpha || 0;

                    // 3. Update Warning Alert (GLOBAL)
                    updateWarning(data.warning);

                    // 4. Update Table Rows (FILTERED)
                    updateTable(data.absensi);

                } catch (error) {
                    console.error(error);
                    alert('Terjadi kesalahan saat memuat data.');
                } finally {
                    tbody.style.opacity = '1'; // Hapus indikasi loading tabel
                }
            }

            function updateMapelDropdown(mapels, currentSelectedId) {
                filterMapel.innerHTML = '';

                mapels.forEach(mapel => {
                    const option = document.createElement('option');
                    option.value = mapel.id;
                    option.textContent = mapel.nama;
                    if (mapel.id == currentSelectedId) {
                        option.selected = true;
                    }
                    filterMapel.appendChild(option);
                });
            }

            function updateWarning(warning) {
                warningContainer.innerHTML = '';
                if (warning.level > 0) {
                    const alertDiv = document.createElement('div');
                    // Gunakan class Bootstrap dari data Controller
                    alertDiv.className = `alert ${warning.class} alert-dismissible fade show shadow-sm`;
                    alertDiv.role = 'alert';
                    alertDiv.innerHTML = `
                        <h5 class="alert-heading h6 mb-1"><i class="fas fa-exclamation-triangle me-2"></i> Status Kehadiran Kritis</h5>
                        <div>${warning.message}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    `;
                    warningContainer.appendChild(alertDiv);
                }
            }

            function updateTable(absensiData) {
                tbody.innerHTML = ''; // Bersihkan tabel

                if (absensiData.length === 0) {
                    tbody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-4 bg-light text-muted"> 
                    <i class="fas fa-box-open me-2"></i> Tidak ada data kehadiran untuk filter ini.
                </td>
            </tr>
        `;
                    return;
                }

                absensiData.forEach((row, index) => {
                    // ... (Logika penentuan badgeClass dan label tetap sama) ...
                    let badgeClass = 'bg-secondary';
                    let label = row.status.toUpperCase();

                    if (label === 'HADIR') badgeClass = 'bg-success';
                    else if (label === 'IZIN') badgeClass = 'bg-warning text-dark';
                    else if (label === 'SAKIT') badgeClass = 'bg-info text-white';
                    else if (label === 'ALPHA' || label === 'ALFA' || label === 'TIDAK HADIR') {
                        badgeClass = 'bg-danger';
                        label = 'TIDAK HADIR';
                    }

                    const tr = `
            <tr>
                <td>${index + 1}</td>
                <td class="fw-medium">${row.tanggal_formatted}</td>
                
                <td>${row.mapel || '-'}</td> 
                
                <td><span class="badge ${badgeClass} p-2">${label}</span></td>
                <td>${row.keterangan || 'Tidak ada keterangan tambahan.'}</td>
            </tr>
        `;
                    tbody.innerHTML += tr;
                });
            }

            btnFilter.addEventListener('click', fetchData);

            filterKelas.addEventListener('change', () => {
                filterMapel.value = 0;
                fetchData();
            });
        });
    </script>
@endpush
