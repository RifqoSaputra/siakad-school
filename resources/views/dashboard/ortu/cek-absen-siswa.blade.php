@extends('layouts.template')

@section('title', 'Cek Kehadiran Siswa')
@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Cek Kehadiran Siswa</li>
@endsection

@section('content')
    @php
        // ASUMSI: Semua variabel seperti $bulan, $bulanAktif, $tahunAktif sudah dikirim dari Controller.

        // --- FUNGSI BANTUAN PHP ---

        /**
         * Memformat tanggal menjadi Hari, Tanggal Bulan Tahun (e.g., Rabu, 13 November 2025).
         * Menerima $bulanList (array nama bulan) sebagai parameter.
         */
        function formatTanggal($date, $bulanList)
        {
            $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

            try {
                $timestamp = strtotime($date);
                $dayOfWeek = date('w', $timestamp);
                $monthIndex = date('n', $timestamp) - 1;

                return $hari[$dayOfWeek] .
                    ', ' .
                    date('j', $timestamp) .
                    ' ' .
                    $bulanList[$monthIndex] . // Menggunakan parameter yang dikirim
                    ' ' .
                    date('Y', $timestamp);
            } catch (\Exception $e) {
                return $date;
            }
        }

        /**
         * Mendapatkan kelas CSS Bootstrap untuk warna status (label).
         * Disesuaikan agar mengenali 'Alpha' dan 'Alfa' sebagai 'bg-danger'.
         */
        function getStatusColorClass($status)
        {
            // Perhatian: Status dari DB mungkin 'Alpha' atau 'Alfa'.
            $normalizedStatus = strtoupper($status);

            return match ($normalizedStatus) {
                'HADIR' => 'bg-success',
                'IZIN' => 'bg-warning text-dark',
                'SAKIT' => 'bg-info text-white',
                'ALPHA', 'ALFA', 'TIDAK HADIR' => 'bg-danger',
                default => 'bg-secondary',
            };
        }

        // FUNGSI BARU untuk menampilkan label status di UI
        function displayStatusLabel($status)
        {
            $normalizedStatus = strtoupper($status);

            return match ($normalizedStatus) {
                'ALPHA', 'ALFA' => 'Tidak Hadir', // Ubah Alpha/Alfa menjadi TIDAK HADIR
                'TIDAK HADIR' => 'TIDAK HADIR',
                default => $status,
            };
        }

        // Variabel yang harus ada (diasumsikan sudah dikirim dari controller):
        // $bulan, $bulanAktif, $tahunAktif

        // Ambil bulan dan tahun aktif (seharusnya sudah dijamin ada oleh Controller)
        $bulanAktif = $bulanAktif ?? date('n');
        $tahunAktif = $tahunAktif ?? date('Y');
    @endphp

    <div class="card shadow-sm p-4">

        {{-- 🚫 Menghilangkan Div Group Tombol Tabel/Kalender --}}
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <h1 class="h3 text-primary mb-0">Laporan Kehadiran Siswa</h1>
            {{-- Bagian tombol Tabel/Kalender dihilangkan --}}
        </div>

        {{-- 🚫 Menghilangkan Detail Siswa --}}
        {{-- Div card mb-4 border-primary dihilangkan --}}

        {{-- FILTER BULAN & TAHUN --}}
        <div class="d-flex flex-wrap align-items-center gap-3 mb-4">
            <label for="filter_periode" class="form-label mb-0 fw-bold">Pilih Periode:</label>

            <select id="filter_bulan" class="form-select w-auto">
                {{-- Menggunakan $bulan yang dikirim dari Controller --}}
                @foreach ($bulan as $key => $month)
                    <option value="{{ $key + 1 }}" {{ $bulanAktif == $key + 1 ? 'selected' : '' }}>{{ $month }}
                    </option>
                @endforeach
            </select>

            <select id="filter_tahun" class="form-select w-auto">
                @for ($year = date('Y'); $year >= date('Y') - 3; $year--)
                    <option value="{{ $year }}" {{ $tahunAktif == $year ? 'selected' : '' }}>{{ $year }}
                    </option>
                @endfor
            </select>

            {{-- Tombol Tampilkan akan mengarahkan ke URL /ortu/absensi/{tahun}/{bulan} --}}
            <button class="btn btn-outline-primary" id="btn-tampilkan-filter">
                <i class="fas fa-search me-1"></i> Tampilkan
            </button>
        </div>


        {{-- REKAPITULASI (CARDS) --}}
        <h3 class="h5 fw-bold mb-3 text-secondary">Absensi Periode {{ $bulan[$bulanAktif - 1] ?? 'Ini' }}</h3>
        <div class="row mb-5">
            @php
                // Kita akan mengganti label 'TIDAK HADIR' dengan 'Alpha' dari Controller
                // jika Controller menggunakan 'Alpha' (seperti yang terlihat di gambar).
                // Kita gunakan 'Alpha' dan 'Alfa' sebagai kunci, lalu tampilkan 'TIDAK HADIR'

                // Cek jika 'TIDAK HADIR' adalah kunci, kita harus menggunakan 'Alpha' atau 'Alfa'
                // (sesuai yang dikirim dari Controller) untuk mengambil count.
                // Karena data riwayat menunjukkan 'Alpha', kita asumsikan Controller mengirim
                // rekap menggunakan kunci 'Alpha' atau 'Alfa'. Kita akan tambahkan keduanya.

                $alphaCount = ($rekap['Alpha'] ?? 0) + ($rekap['Alfa'] ?? 0);

                $rekapData = [
                    'Hadir' => [
                        'count' => $rekap['Hadir'] ?? 0,
                        'bg' => 'bg-success',
                        'text' => 'text-success',
                        'icon' => 'check-circle',
                    ],
                    'Izin' => [
                        'count' => $rekap['Izin'] ?? 0,
                        'bg' => 'bg-warning',
                        'text' => 'text-warning',
                        'icon' => 'user-clock',
                    ],
                    'Sakit' => [
                        'count' => $rekap['Sakit'] ?? 0,
                        'bg' => 'bg-info',
                        'text' => 'text-info',
                        'icon' => 'medkit',
                    ],
                    // Perubahan di sini: menggunakan label tampilan 'TIDAK HADIR'
                    'TIDAK HADIR' => [
                        // Menggunakan count dari Alpha/Alfa yang mungkin dikirim Controller
                        'count' => $alphaCount,
                        'bg' => 'bg-danger',
                        'text' => 'text-danger',
                        'icon' => 'times-circle',
                    ],
                ];
            @endphp

            @foreach ($rekapData as $label => $data)
                <div class="col-md-3 col-6 mb-3">
                    <div class="card shadow-sm border-start border-4 border-{{ str_replace('bg-', '', $data['bg']) }}">
                        <div class="card-body p-3">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="text-xs fw-bold {{ $data['text'] }} text-uppercase mb-1">
                                        {{-- Label Tampilan (Hadir, Izin, Sakit, TIDAK HADIR) --}}
                                        {{ $label }}
                                    </div>
                                    <div class="h5 mb-0 fw-bold text-gray-800">{{ $data['count'] }} Hari</div>
                                </div>
                                <div class="col-auto">
                                    <span class="badge {{ $data['bg'] }} text-white p-2 rounded-circle">
                                        <i class="fas fa-{{ $data['icon'] }}"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- RIWAYAT KEHADIRAN (VIEW TABEL) --}}
        <h3 class="h5 fw-bold mb-3 text-secondary">Riwayat Absensi</h3>
        {{-- class="view-content" dan d-none dihilangkan karena hanya ada 1 view --}}
        <div id="view-tabel" class="table-responsive">
            <table class="table table-bordered table-striped align-middle" id="absensiTable">
                <thead class="table-light">
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 25%;">Tanggal</th>
                        <th style="width: 15%;">Status</th>
                        <th style="width: 55%;">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dataAbsensi ?? [] as $index => $absen)
                        {{-- Menggunakan status mentah dari DB untuk mendapatkan warna --}}
                        @php
                            $statusClass = getStatusColorClass($absen['status']);
                            $displayedStatus = displayStatusLabel($absen['status']); // Status untuk ditampilkan
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="fw-medium">{{ formatTanggal($absen['tanggal'], $bulan) }}</td>
                            <td>
                                {{-- Perubahan di sini: Menggunakan $displayedStatus --}}
                                <span class="badge {{ $statusClass }} p-2">{{ $displayedStatus }}</span>
                            </td>
                            <td>
                                {{ $absen['keterangan'] ?? 'Tidak ada keterangan tambahan.' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 bg-light text-muted">
                                <i class="fas fa-box-open me-2"></i> Tidak ada data kehadiran untuk periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- 🚫 Menghilangkan RIWAYAT KEHADIRAN (VIEW KALENDER) --}}
        {{-- Div id="view-kalender" dihilangkan seluruhnya --}}

    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Variabel btnTabel, btnKalender, viewKalender dihilangkan.

            const btnTampilkanFilter = document.getElementById('btn-tampilkan-filter');
            const filterBulan = document.getElementById('filter_bulan');
            const filterTahun = document.getElementById('filter_tahun');

            // --- FUNGSI GANTI VIEW (TABEL/KALENDER) DIHILANGKAN ---
            // Karena tidak ada lagi fungsi ganti view, script setActiveView dan event listenernya dihilangkan

            // --- FUNGSI FILTER URL (ORANG TUA) ---
            if (btnTampilkanFilter) {
                btnTampilkanFilter.addEventListener('click', function() {
                    const bulan = filterBulan.value;
                    const tahun = filterTahun.value;

                    // MEMBANGUN URL SESUAI STRUKTUR ROUTE ORTU/ABSENSI/{TAHUN}/{BULAN}
                    window.location.href = `/ortu/absensi/${tahun}/${bulan}`;
                });
            }
        });
    </script>
@endpush
