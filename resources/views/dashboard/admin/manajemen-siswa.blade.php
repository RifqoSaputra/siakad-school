@extends('layouts.template')

@section('title', 'Manajemen Siswa')

@push('styles')
    @vite(['resources/css/announcement.css'])
@endpush

@push('scripts')
    @vite(['resources/js/announcement.js'])
@endpush

@section('content')
    @php
        $hasFilters = $request->filled('search') || $request->filled('kelas') || $request->filled('jenis_kelamin');
        $totalNonAktif = $totalSiswaNonAktif ?? 0;
        $totalAktif = $totalSiswaAktif ?? 0;
        $totalAll = $totalAll ?? $totalAktif;
        $genderStats = $genderStats ?? [
            'Laki-laki' => 0,
            'Perempuan' => 0,
        ];
        $prodiStats = $prodiStats ?? [];
        $fmtStat = fn($v) => isset($v) ? number_format((int) $v, 0, ',', '.') : '-';
    @endphp

    <div class="ann-layout ann-layout--siswa">
        <div class="ann-summary-grid">
            <div class="ann-summary-card ann-summary-card--stat ann-summary-card--with-meta ann-summary-card--siswa">
                <div class="ann-summary-card__body">
                    <div class="ann-summary-card__label">Total Seluruh Siswa</div>
                    <div class="ann-summary-card__value ann-summary-card__value--xl">{{ $fmtStat($totalAll) }}</div>
                    <div class="ann-summary-card__divider ann-summary-card__divider--wide"></div>
                    <div class="ann-summary-card__meta ann-summary-card__meta--two-col">
                        @foreach ($genderStats as $label => $value)
                            <div class="ann-summary-card__meta-item">
                                <div class="ann-summary-card__meta-label">{{ $label }}</div>
                                <div class="ann-summary-card__meta-value">{{ $fmtStat($value) }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            @foreach ($prodiStats as $prodi)
                <div class="ann-summary-card ann-summary-card--stat ann-summary-card--with-meta ann-summary-card--prodi">
                    <div class="ann-summary-card__body">
                        <div class="ann-summary-card__header">
                            <div class="ann-summary-card__label">{{ $prodi['title'] }}</div>
                            <div class="ann-summary-card__badge {{ $prodi['icon']['variant'] ?? '' }}">
                                <span class="material-symbols-rounded">{{ $prodi['icon']['symbol'] ?? 'school' }}</span>
                            </div>
                        </div>
                        <div class="ann-summary-card__value-line">
                            <div class="ann-summary-card__value ann-summary-card__value--lg">{{ $fmtStat($prodi['total']) }}</div>
                            <span class="ann-summary-card__value-icon material-symbols-rounded">group</span>
                        </div>
                        <div class="ann-summary-card__divider ann-summary-card__divider--wide"></div>
                        <div class="ann-summary-card__meta ann-summary-card__meta--inline">
                            @foreach ($prodi['levels'] as $level)
                                <div class="ann-summary-card__meta-item">
                                    <div class="ann-summary-card__meta-label">{{ $level['label'] }}</div>
                                    <div class="ann-summary-card__meta-value">{{ $fmtStat($level['value']) }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <form method="GET" action="{{ route('admin.siswa') }}" class="ann-toolbar__row ann-toolbar__standalone ann-toolbar--inline">
            <input type="hidden" name="tahun_ajaran" value="{{ $tahunAjaranAktif }}">
            <div class="ann-toolbar__chunk ann-toolbar__chunk--search">
                <div class="ann-search__cluster">
                    <div class="ann-search__field">
                        <div class="ann-search__input">
                            <span class="material-symbols-rounded">search</span>
                            <input type="text" name="search" id="search_siswa" placeholder="Cari nama atau NIS"
                                value="{{ $request->get('search') }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="ann-toolbar__chunk ann-toolbar__chunk--filters">
                <div class="ann-toolbar__filters">
                    <div class="ann-filter" data-filter="kelas">
                        <button type="button" class="ann-filter__btn">
                            <span>{{ $request->get('kelas') ? ($allKelas->firstWhere('kelas_id', $request->get('kelas'))->nama_kelas_lengkap ?? 'Kelas') : 'Kelas' }}</span>
                            <span class="material-symbols-rounded">arrow_drop_down</span>
                        </button>
                        <div class="ann-filter__menu">
                            <button type="button" class="ann-filter__option" data-value="">
                                <span class="ann-radio {{ $request->filled('kelas') ? '' : 'active' }}"></span>Semua Kelas
                            </button>
                            @foreach ($allKelas as $kelas)
                                <button type="button" class="ann-filter__option" data-value="{{ $kelas->kelas_id }}">
                                    <span class="ann-radio {{ $request->get('kelas') == $kelas->kelas_id ? 'active' : '' }}"></span>{{ $kelas->nama_kelas_lengkap }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="ann-filter" data-filter="jenis_kelamin">
                        <button type="button" class="ann-filter__btn">
                            <span>{{ $request->get('jenis_kelamin') ?: 'Jenis Kelamin' }}</span>
                            <span class="material-symbols-rounded">arrow_drop_down</span>
                        </button>
                        <div class="ann-filter__menu">
                            <button type="button" class="ann-filter__option" data-value="">
                                <span class="ann-radio {{ $request->filled('jenis_kelamin') ? '' : 'active' }}"></span>Semua
                            </button>
                            @foreach (['Laki-laki', 'Perempuan'] as $jk)
                                <button type="button" class="ann-filter__option" data-value="{{ $jk }}">
                                    <span class="ann-radio {{ $request->get('jenis_kelamin') == $jk ? 'active' : '' }}"></span>{{ $jk }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <button type="button" class="ann-reset {{ $hasFilters ? 'show' : '' }}" id="filter-reset">
                        <span class="material-symbols-rounded">refresh</span>
                        Reset
                    </button>
                </div>
            </div>

            <div class="ann-toolbar__chunk ann-toolbar__chunk--pagination">
                <div class="ann-pagination ann-pagination--inline ann-pagination--right">
                    <span class="ann-pagination__text">
                        <span class="ann-pagination__label">Menampilkan</span>
                        <span class="ann-pagination__current">{{ $siswaData->firstItem() }}–{{ $siswaData->lastItem() }}</span>
                        <span class="ann-pagination__total">dari {{ $siswaData->total() }}</span>
                    </span>
                    <div class="ann-pagination__arrows">
                        <button class="ann-icon-btn" data-nav-url="{{ $siswaData->previousPageUrl() }}" @disabled(!$siswaData->previousPageUrl())>
                            <span class="material-symbols-rounded">chevron_left</span>
                        </button>
                        <button class="ann-icon-btn" data-nav-url="{{ $siswaData->nextPageUrl() }}" @disabled(!$siswaData->nextPageUrl())>
                            <span class="material-symbols-rounded">chevron_right</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <div class="ann-section">
            <div class="ann-table ann-table--siswa">
                <div class="ann-table__head">
                    <div>No.</div>
                    <div>NIS</div>
                    <div>Nama</div>
                    <div>Kelas</div>
                    <div>Jenis Kelamin</div>
                    <div>Tempat & Tanggal Lahir</div>
                </div>
                <div class="ann-table__body">
                    @forelse ($siswaData as $index => $siswa)
                        @php
                            $ttlDate = $siswa->tgl_lahir ? \Carbon\Carbon::parse($siswa->tgl_lahir)->format('d M Y') : null;
                            $ttlText = trim(($siswa->tempat_lahir ?? '') . ', ' . ($ttlDate ?? ''));
                            if ($ttlText === ',' || $ttlText === '') {
                                $ttlText = '-';
                            }
                        @endphp
                        <div class="ann-row">
                            <div class="cell">{{ $siswaData->firstItem() + $index }}</div>
                            <div class="cell ann-meta__muted">{{ $siswa->nis }}</div>
                            <div class="cell ann-row__title">{{ $siswa->nama }}</div>
                            <div class="cell">{{ $siswa->kelas_sekarang }}</div>
                            <div class="cell">{{ $siswa->jenis_kelamin ?? '-' }}</div>
                            <div class="cell">{{ $ttlText }}</div>
                        </div>
                    @empty
                        <div class="ann-empty">Tidak ada data siswa untuk filter yang dipilih.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
