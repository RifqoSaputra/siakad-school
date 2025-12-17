@extends('layouts.template')

@section('title', 'Nilai Ujian Anak')

@push('scripts')
    @vite(['resources/js/announcement.js'])
@endpush

@section('content')
    @php
        $kelasFull = $kelasFull ?? ($kelasList->firstWhere('kelas_id', $kelasId) ? ($kelasList->firstWhere('kelas_id', $kelasId)->kelas->tingkat_kelas ?? '') . ' ' . ($kelasList->firstWhere('kelas_id', $kelasId)->kelas->nama_kelas ?? '') : '-');
    @endphp

    <div class="ann-layout ann-layout--ortu">
        <div class="ann-bar ann-bar--stack">
            <div class="ann-bar__left">
                <h4 class="ann-title mb-1">Rekap Nilai Ujian ({{ $jenisUjian }})</h4>
                <p class="text-muted mb-0">Pantau nilai PTS/PAS anak per mata pelajaran.</p>
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
                <div class="ann-summary-card__value ann-summary-card__value--xl">{{ $kelasFull }}</div>
                <div class="ann-summary-card__divider"></div>
                <div class="ann-summary-card__meta">
                    <div class="ann-summary-card__meta-item">
                        <div class="ann-summary-card__meta-label">Semester</div>
                        <div class="ann-summary-card__meta-value">{{ $semester ?? '-' }}</div>
                    </div>
                    <div class="ann-summary-card__meta-item">
                        <div class="ann-summary-card__meta-label">Jenis Ujian</div>
                        <div class="ann-summary-card__meta-value">{{ $jenisUjian ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <form id="filter-form" method="GET" class="ann-toolbar__row ann-toolbar__standalone ann-toolbar--inline" action="{{ route('ortu.nilai-siswa.ujian') }}">
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

                    <div class="ann-filter ann-filter--select">
                        <label class="ann-field__label">Jenis Ujian</label>
                        <select name="jenis_ujian" id="jenis_ujian" class="ann-input">
                            <option value="PTS" {{ $jenisUjian == 'PTS' ? 'selected' : '' }}>PTS (Tengah Semester)</option>
                            <option value="PAS" {{ $jenisUjian == 'PAS' ? 'selected' : '' }}>PAS (Akhir Semester)</option>
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
                    <div>Guru Pengampu</div>
                    <div>Nilai Akhir</div>
                </div>
                <div class="ann-table__body">
                    @forelse ($rekap as $i => $m)
                        @php
                            $nilai = $m['nilai'];
                            $nilaiClass = 'ann-status--pending';
                            if (!is_null($nilai)) {
                                if ($nilai < 70) $nilaiClass = 'ann-status--inactive';
                                elseif ($nilai < 80) $nilaiClass = 'ann-status--pending';
                                else $nilaiClass = 'ann-status--active';
                            }
                        @endphp
                        <div class="ann-row">
                            <div class="cell">{{ $i + 1 }}</div>
                            <div class="cell ann-row__title">{{ $m['mapel'] }}</div>
                            <div class="cell">{{ $m['guru'] }}</div>
                            <div class="cell ann-status-cell">
                                @if (is_null($nilai))
                                    <span class="ann-meta__muted">Belum ada nilai</span>
                                @else
                                    <span class="ann-status {{ $nilaiClass }}">
                                        <span class="material-symbols-rounded">verified</span>
                                        {{ $nilai }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="ann-empty">Belum ada nilai ujian untuk filter ini.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
