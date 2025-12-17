@extends('layouts.template')

@section('title', 'Data Mata Pelajaran')

@push('scripts')
    @vite(['resources/js/announcement.js'])
@endpush

@section('content')
<div class="ann-layout ann-layout--mapel">
    <div class="ann-bar ann-bar--stack">
        <div>
            <h4 class="ann-title mb-1">Data Mata Pelajaran</h4>
            <p class="text-muted mb-0">Daftar mata pelajaran yang tersedia.</p>
        </div>
    </div>

    <div class="ann-table ann-table--mapel">
        <div class="ann-table__head">
            <div>No.</div>
            <div>Kode</div>
            <div>Nama Mapel</div>
        </div>
        <div class="ann-table__body">
            @forelse ($mapel as $index => $m)
                <div class="ann-row">
                    <div class="cell">{{ $index + 1 }}</div>
                    <div class="cell">{{ $m->kode_mapel ?? '-' }}</div>
                    <div class="cell ann-row__title">{{ $m->nama_mapel ?? '-' }}</div>
                </div>
            @empty
                <div class="ann-empty">Belum ada data mata pelajaran.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
