@extends('layouts.template')

@section('title', 'Nilai Harian Anak')

@push('styles')
    @vite(['resources/css/announcement.css'])
@endpush

@push('scripts')
    @vite(['resources/js/announcement.js'])
@endpush

@section('content')
    @php
        $kelasFull = $kelasList->firstWhere('kelas_id', $kelasId);
        $kelasNama = $kelasFull ? (($kelasFull->kelas->tingkat_kelas ?? '') . ' ' . ($kelasFull->kelas->nama_kelas ?? '')) : '-';
        $semesterLabel = $semester ?? '-';
    @endphp

    <div class="ann-layout ann-layout--ortu">
        <div class="ann-bar ann-bar--stack">
            <div class="ann-bar__left">
                <h4 class="ann-title mb-1">Rekap Nilai Harian</h4>
                <p class="text-muted mb-0">Pantau rata-rata nilai harian anak untuk tiap mata pelajaran.</p>
            </div>
        </div>

        @if (session('error'))
            <div class="ann-section">
                <div class="ann-error" style="display:block;">{{ session('error') }}</div>
            </div>
        @endif

        <div class="ann-summary-grid">
            <div class="ann-summary-card ann-summary-card--stat">
                <div class="ann-summary-card__top">
                    <div class="ann-summary-card__label">Kelas</div>
                    <span class="ann-summary-card__icon ann-summary-card__icon--subtle material-symbols-rounded">class</span>
                </div>
                <div class="ann-summary-card__value ann-summary-card__value--xl">{{ $kelasNama }}</div>
                <div class="ann-summary-card__divider"></div>
                <div class="ann-summary-card__meta">
                    <div class="ann-summary-card__meta-item">
                        <div class="ann-summary-card__meta-label">Semester</div>
                        <div class="ann-summary-card__meta-value">{{ $semesterLabel }}</div>
                    </div>
                    <div class="ann-summary-card__meta-item">
                        <div class="ann-summary-card__meta-label">Mapel</div>
                        <div class="ann-summary-card__meta-value">{{ count($rekap ?? []) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <form id="filter-form" method="GET" class="ann-toolbar__row ann-toolbar__standalone ann-toolbar--inline" action="{{ route('ortu.nilai-siswa.harian') }}">
            <div class="ann-toolbar__chunk ann-toolbar__chunk--filters">
                <div class="ann-toolbar__filters">
                    <div class="ann-filter ann-filter--select">
                        <label class="ann-field__label">Pilih Kelas</label>
                        <select name="kelas_id" id="kelas_id" class="ann-input">
                            @foreach ($kelasList as $k)
                                @php $k_full_name = ($k->kelas->tingkat_kelas ?? '') . ' ' . ($k->kelas->nama_kelas ?? ''); @endphp
                                <option value="{{ $k->kelas_id }}" {{ $kelasId == $k->kelas_id ? 'selected' : '' }}>
                                    {{ $k_full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="ann-filter ann-filter--select">
                        <label class="ann-field__label">Semester</label>
                        <select name="semester" id="semester" class="ann-input">
                            <option value="Ganjil" {{ $semester == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                            <option value="Genap" {{ $semester == 'Genap' ? 'selected' : '' }}>Genap</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="ann-toolbar__chunk ann-toolbar__chunk--pagination">
                <button type="submit" class="ann-btn ann-btn--primary" style="width:auto; min-width:0;">
                    Terapkan
                </button>
            </div>
        </form>

        <div class="ann-section">
            <div class="ann-table ann-table--nilai">
                <div class="ann-table__head">
                    <div>No</div>
                    <div>Mata Pelajaran</div>
                    <div>Guru</div>
                    <div>Rata-rata</div>
                </div>
                <div class="ann-table__body">
                    @forelse ($rekap as $i => $m)
                        @php
                            $rata = $m['rata'];
                            $nilaiClass = 'ann-status--pending';
                            if (!is_null($rata)) {
                                if ($rata < 70) $nilaiClass = 'ann-status--inactive';
                                elseif ($rata < 80) $nilaiClass = 'ann-status--pending';
                                else $nilaiClass = 'ann-status--active';
                            }
                        @endphp
                        <div class="ann-row">
                            <div class="cell">{{ $i + 1 }}</div>
                            <div class="cell ann-row__title">{{ $m['mapel'] }}</div>
                            <div class="cell">{{ $m['guru'] }}</div>
                            <div class="cell ann-status-cell">
                                <span class="ann-status {{ $nilaiClass }}">
                                    <span class="material-symbols-rounded">star</span>
                                    {{ is_null($rata) ? '-' : $rata }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="ann-empty">Tidak ada rekap nilai untuk filter ini.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
