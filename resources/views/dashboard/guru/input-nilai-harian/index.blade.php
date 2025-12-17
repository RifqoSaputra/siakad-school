@extends('layouts.template')

@section('title', 'Daftar Penilaian Harian')

@section('content')
@push('scripts')
    @vite(['resources/js/announcement.js'])
@endpush

<div class="ann-layout ann-layout--guru">
    <div class="ann-container">
        {{-- Filter --}}
        <form id="form-filter" method="GET" action="{{ route('nilai.harian.index') }}"
            class="toolbar__row toolbar__standalone toolbar--inline"
            style="margin-bottom:24px; display:flex; flex-wrap:nowrap; gap:24px; align-items:center;">
            <div class="select">
                <select id="tahun_ajaran" name="tahun_ajaran" class="input input--select"
                    onchange="document.getElementById('form-filter').submit()">
                    <option value="2024/2025" {{ $tahunAjaranFilter == '2024/2025' ? 'selected' : '' }}>2024/2025 (Ganjil)
                    </option>
                </select>
                <span class="material-symbols-rounded select__icon">expand_more</span>
            </div>
            <div class="select">
                <select id="kelas_id" name="kelas_id" class="input input--select"
                    onchange="document.getElementById('form-filter').submit()">
                    <option value="">Semua Kelas</option>
                    @foreach ($kelasYangTersedia as $kelas)
                        <option value="{{ $kelas->kelas_id }}" {{ $currentKelasId == $kelas->kelas_id ? 'selected' : '' }}>
                            {{ $kelas->nama_kelas_lengkap }}
                        </option>
                    @endforeach
                </select>
                <span class="material-symbols-rounded select__icon">expand_more</span>
            </div>
            <div class="select">
                <select id="mapel_id" name="mapel_id" class="input input--select"
                    onchange="document.getElementById('form-filter').submit()">
                    <option value="">Semua Mapel</option>
                    @foreach ($listMapel as $mapel)
                        <option value="{{ $mapel->mapel_id }}" {{ $currentMapelId == $mapel->mapel_id ? 'selected' : '' }}>
                            {{ $mapel->nama_mapel }}
                        </option>
                    @endforeach
                </select>
                <span class="material-symbols-rounded select__icon">expand_more</span>
            </div>
        </form>

        {{-- Header actions --}}
        <div class="toolbar__row toolbar__standalone toolbar--inline" style="margin-bottom:24px;">
            <div class="toolbar__chunk">
                <div class="ann-modal__label" style="padding-top:0;">Daftar Tugas</div>
            </div>
            <div class="toolbar__chunk" style="gap:8px; flex-wrap:wrap;">
                <button type="button" id="open-summary-modal" class="btn btn--secondary" style="width:auto; min-width:200px;">
                    Rekap Nilai Harian
                </button>
                <button type="button" id="open-add-task-modal" data-guru-mapel-id="{{ $guruMapelIdTarget }}"
                    data-kelas-id="{{ $currentKelasId }}" data-mapel-id="{{ $currentMapelId }}"
                    class="btn btn--primary" style="width:auto; min-width:200px;">
                    Tambah Tugas Harian
                </button>
                @if ($currentKelasId && $currentMapelId && count($listTugas) > 0)
                    @php $isFinalSubmit = $listTugas->every(fn($t) => $t->status === 'Submitted'); @endphp
                    @if ($isFinalSubmit)
                        <span class="btn btn--secondary" style="width:auto; min-width:200px; opacity:0.7; cursor:not-allowed;">
                            Sudah Submit Final
                        </span>
                    @else
                        <button type="button" id="btn-submit-index" class="btn btn--primary" style="width:auto; min-width:200px;">
                            Submit Semua Nilai
                        </button>
                    @endif
                @endif
            </div>
        </div>

        {{-- Table --}}
        <div class="card card--table">
            <div class="table table--nilai-harian">
                <div class="table__head">
                    <div>No.</div>
                    <div>Tgl. Dibuat</div>
                    <div>Kelas</div>
                    <div>Jenis Penilaian</div>
                    <div>Keterangan</div>
                    <div>Status</div>
                    <div>Aksi</div>
                </div>
                <div class="table__body">
                    @forelse($listTugas as $idx => $tugas)
                        @php
                            $isSelesai = $tugas->statusPengisian == 'Selesai';
                            $statusClass = $isSelesai ? 'status--active' : 'status--inactive';
                            $statusLabel = $isSelesai ? 'Selesai' : 'Belum diisi';
                            $isSubmitted = $tugas->status === 'Submitted';
                            $rowNumber = $loop->iteration;
                        @endphp
                        <div class="table__row">
                            <div class="cell ann-meta__muted cell--no">{{ $rowNumber }}</div>
                            <div class="cell ann-meta__muted">{{ \Carbon\Carbon::parse($tugas->tgl_entry)->format('d M Y') }}</div>
                            <div class="cell ann-meta__muted">{{ $tugas->kelas?->nama_kelas_lengkap ?? 'N/A' }}</div>
                            <div class="cell ann-meta__muted">{{ $tugas->tipe_penunjang }}</div>
                            <div class="cell ann-meta__muted cell--truncate" title="{{ $tugas->deskripsi }}">{{ $tugas->deskripsi }}</div>
                            <div class="cell cell--status ann-status-cell">
                                <span id="row-status-{{ $tugas->id }}" class="status {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </div>
                            <div class="cell cell--actions">
                                @if ($isSubmitted)
                                    <span class="btn btn--secondary" style="width:auto; cursor:not-allowed; opacity:0.8;">Sudah Submit</span>
                                @else
                                    <div class="ann-actions__stack">
                                        <button type="button" onclick="loadNilaiInputModal({{ $tugas->id }})"
                                            class="btn btn--primary" style="width:auto;">
                                            {{ $isSelesai ? 'Edit Nilai' : 'Input Nilai' }}
                                        </button>
                                        <button type="button" onclick="deleteTask({{ $tugas->id }})"
                                            class="btn btn--danger" style="width:auto; min-width:46px;">Hapus</button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="ann-empty">Tidak ada tugas harian untuk filter ini. Silakan buat tugas baru.</div>
                    @endforelse
                </div>
            </div>
        </div>
        </div>
    </div>

    <div id="toastBox"></div>

    {{-- modal existing --}}
    @include('dashboard.guru.input-nilai-harian.summary')
    @include('dashboard.guru.input-nilai-harian.detail')
    @include('dashboard.guru.input-nilai-harian.create', [
        'formAction' => route('nilai.harian.store'),
        'kelasTarget' => $currentKelas,
        'mapel' => $currentMapel,
        'currentKelasId' => $currentKelasId,
        'currentMapelId' => $currentMapelId,
    ])
    @include('dashboard.guru.input-nilai-harian.confirm-delete')
    @include('dashboard.guru.input-nilai-harian.confirm-submit')
</div>
@endsection
