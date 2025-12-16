@extends('layouts.template')

@section('title', 'Cek Kehadiran Siswa')

@push('styles')
    @vite(['resources/css/announcement.css'])
@endpush

@push('scripts')
    @vite(['resources/js/announcement.js'])
@endpush

@section('content')
    @php
        use Carbon\Carbon;
        $bulanAktif = $bulanAktif ?? date('n');
        $tahunAktif = $tahunAktif ?? date('Y');
        $alphaCount = ($rekap['Alpha'] ?? 0) + ($rekap['Alfa'] ?? 0) + ($rekap['TIDAK HADIR'] ?? 0);
        $rekapData = [
            ['label' => 'Hadir', 'count' => $rekap['Hadir'] ?? 0, 'class' => 'ann-status--active', 'icon' => 'check_circle'],
            ['label' => 'Izin', 'count' => $rekap['Izin'] ?? 0, 'class' => 'ann-status--pending', 'icon' => 'info'],
            ['label' => 'Sakit', 'count' => $rekap['Sakit'] ?? 0, 'class' => 'ann-status--pending', 'icon' => 'medication'],
            ['label' => 'Tidak Hadir', 'count' => $alphaCount, 'class' => 'ann-status--inactive', 'icon' => 'cancel'],
        ];
        $statusMap = [
            'HADIR' => ['label' => 'Hadir', 'class' => 'ann-status--active'],
            'IZIN' => ['label' => 'Izin', 'class' => 'ann-status--pending'],
            'SAKIT' => ['label' => 'Sakit', 'class' => 'ann-status--pending'],
            'ALPHA' => ['label' => 'Tidak Hadir', 'class' => 'ann-status--inactive'],
            'ALFA' => ['label' => 'Tidak Hadir', 'class' => 'ann-status--inactive'],
            'TIDAK HADIR' => ['label' => 'Tidak Hadir', 'class' => 'ann-status--inactive'],
        ];
    @endphp

    <div class="ann-layout ann-layout--ortu">
        <div class="ann-bar ann-bar--stack">
            <div class="ann-bar__left">
                <h4 class="ann-title mb-1">Laporan Kehadiran Siswa</h4>
                <p class="text-muted mb-0">Periksa rekap dan riwayat absensi anak.</p>
            </div>
        </div>

        <div class="ann-toolbar__row ann-toolbar__standalone ann-toolbar--inline">
            <div class="ann-toolbar__chunk ann-toolbar__chunk--filters">
                <div class="ann-toolbar__filters">
                    <div class="ann-filter" data-filter="bulan">
                        <button type="button" class="ann-filter__btn">
                            <span>{{ $bulan[$bulanAktif - 1] ?? 'Bulan' }}</span>
                            <span class="material-symbols-rounded">arrow_drop_down</span>
                        </button>
                        <div class="ann-filter__menu">
                            @foreach ($bulan as $key => $month)
                                @php $val = $key + 1; @endphp
                                <button type="button" class="ann-filter__option" data-value="{{ $val }}">
                                    <span class="ann-radio {{ $bulanAktif == $val ? 'active' : '' }}"></span>{{ $month }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="ann-filter" data-filter="tahun">
                        <button type="button" class="ann-filter__btn">
                            <span>{{ $tahunAktif }}</span>
                            <span class="material-symbols-rounded">arrow_drop_down</span>
                        </button>
                        <div class="ann-filter__menu">
                            @for ($year = date('Y'); $year >= date('Y') - 3; $year--)
                                <button type="button" class="ann-filter__option" data-value="{{ $year }}">
                                    <span class="ann-radio {{ $tahunAktif == $year ? 'active' : '' }}"></span>{{ $year }}
                                </button>
                            @endfor
                        </div>
                    </div>

                    <button class="ann-reset" id="filter-reset">
                        <span class="material-symbols-rounded">refresh</span>
                        Reset
                    </button>
                </div>
            </div>

            <div class="ann-toolbar__chunk ann-toolbar__chunk--pagination">
                <div class="ann-pagination ann-pagination--inline ann-pagination--right">
                    <span class="ann-pagination__text">
                        <span class="ann-pagination__label">Periode</span>
                        <span class="ann-pagination__current">{{ $bulan[$bulanAktif - 1] ?? 'Bulan' }} {{ $tahunAktif }}</span>
                    </span>
                </div>
            </div>
        </div>

        <div class="ann-summary-grid">
            @foreach ($rekapData as $item)
                <div class="ann-summary-card ann-summary-card--stat">
                    <div class="ann-summary-card__top">
                        <div class="ann-summary-card__label">{{ $item['label'] }}</div>
                        <span class="ann-summary-card__icon ann-summary-card__icon--subtle material-symbols-rounded">{{ $item['icon'] }}</span>
                    </div>
                    <div class="ann-summary-card__value ann-summary-card__value--xl">{{ $item['count'] }} Hari</div>
                </div>
            @endforeach
        </div>

        <div class="ann-section">
            <div class="ann-bar ann-bar--stack">
                <div class="ann-bar__left">
                    <h5 class="ann-title mb-0">Riwayat Absensi</h5>
                    <div class="ann-meta">
                        <span class="ann-meta__dot"></span>
                        <span>Periode {{ $bulan[$bulanAktif - 1] ?? 'Ini' }}</span>
                    </div>
                </div>
            </div>
            <div class="ann-table ann-table--absensi">
                <div class="ann-table__head">
                    <div>No</div>
                    <div>Tanggal</div>
                    <div>Status</div>
                    <div>Keterangan</div>
                </div>
                <div class="ann-table__body">
                    @forelse ($dataAbsensi ?? [] as $index => $absen)
                        @php
                            $key = strtoupper($absen['status']);
                            $meta = $statusMap[$key] ?? ['label' => $absen['status'], 'class' => 'ann-status--pending'];
                            $tanggal = formatTanggal($absen['tanggal'], $bulan);
                        @endphp
                        <div class="ann-row">
                            <div class="cell">{{ $index + 1 }}</div>
                            <div class="cell">{{ $tanggal }}</div>
                            <div class="cell ann-status-cell">
                                <span class="ann-status {{ $meta['class'] }}">
                                    <span class="material-symbols-rounded">event_available</span>
                                    {{ $meta['label'] }}
                                </span>
                            </div>
                            <div class="cell">{{ $absen['keterangan'] ?? 'Tidak ada keterangan tambahan.' }}</div>
                        </div>
                    @empty
                        <div class="ann-empty">Tidak ada data kehadiran untuk periode ini.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const filterBulan = document.querySelector('[data-filter="bulan"]');
                const filterTahun = document.querySelector('[data-filter="tahun"]');
                const resetBtn = document.getElementById('filter-reset');

                function navigate(period) {
                    const url = `/ortu/absensi/${period.tahun}/${period.bulan}`;
                    window.location.href = url;
                }

                function bindFilter(filterEl, key) {
                    const options = filterEl?.querySelectorAll('.ann-filter__option') || [];
                    options.forEach(opt => {
                        opt.addEventListener('click', () => {
                            const val = opt.dataset.value;
                            const period = {
                                bulan: key === 'bulan' ? val : '{{ $bulanAktif }}',
                                tahun: key === 'tahun' ? val : '{{ $tahunAktif }}'
                            };
                            navigate(period);
                        });
                    });
                }

                bindFilter(filterBulan, 'bulan');
                bindFilter(filterTahun, 'tahun');

                resetBtn?.addEventListener('click', () => navigate({
                    bulan: '{{ date('n') }}',
                    tahun: '{{ date('Y') }}'
                }));
            });
        </script>
    @endpush
@endsection
