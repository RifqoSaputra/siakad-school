@extends('layouts.template')

@section('title', 'Data Kelas')

@push('scripts')
    @vite(['resources/js/app-ui.js'])
@endpush

@section('content')
<div class="ann-layout ann-layout--kelas">
    <div class="ann-bar ann-bar--stack">
        <div>
            <h4 class="ann-title mb-1">Data Kelas</h4>
            <p class="text-muted mb-0">Daftar kelas, tingkat, tahun ajaran, dan wali kelas.</p>
        </div>
    </div>

    <div class="ann-table ann-table--kelas">
        <div class="ann-table__head">
            <div>No.</div>
            <div>Tingkat</div>
            <div>Nama Kelas</div>
            <div>Tahun Ajaran</div>
            <div>Semester</div>
            <div>Wali Kelas</div>
        </div>
        <div class="ann-table__body">
            @forelse ($kelas as $i => $k)
                <div class="ann-row">
                    <div class="cell">{{ $i + 1 }}</div>
                    <div class="cell">{{ $k->tingkat_kelas }}</div>
                    <div class="cell ann-row__title">{{ $k->nama_kelas }}</div>
                    <div class="cell">{{ $k->tahun_ajaran }}</div>
                    <div class="cell">{{ $k->semester }}</div>
                    <div class="cell">{{ $k->walikelas ?? '-' }}</div>
                </div>
            @empty
                <div class="ann-empty">Belum ada data kelas.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
