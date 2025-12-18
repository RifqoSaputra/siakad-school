@extends('layouts.template')

@section('title', 'Data Mata Pelajaran')

@push('scripts')
    @vite(['resources/js/app-ui.js'])
@endpush

@section('content')
<div class="ann-layout ann-layout--mapel">
    <div class="ann-table ann-table--mapel">
        <div class="ann-table__head">
            <div class="cell cell--no">No.</div>
            <div class="cell">Kode</div>
            <div class="cell">Nama Mapel</div>
        </div>
        <div class="ann-table__body">
            @forelse ($mapel as $index => $m)
                <div class="ann-row">
                    <div class="cell ann-meta__muted cell--no">{{ $index + 1 }}</div>
                    <div class="cell ann-meta__muted">{{ $m->kode_mapel ?? '-' }}</div>
                    <div class="cell ann-row__title">{{ $m->nama_mapel ?? '-' }}</div>
                </div>
            @empty
                <div class="ann-empty">Belum ada data mata pelajaran.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
