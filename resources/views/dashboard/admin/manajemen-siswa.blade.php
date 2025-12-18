@extends('layouts.template')

@section('title', 'Manajemen Siswa')

@push('scripts')
    @vite(['resources/js/app-ui.js'])
@endpush

@section('content')
    @php
        $hasFilters = $request->filled('search') || $request->filled('kelas') || $request->filled('jenis_kelamin');
    @endphp

    <div class="ann-layout ann-layout--siswa">
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

                    <button type="button" class="ann-reset" id="filter-reset">
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
                        <a class="ann-icon-btn {{ $siswaData->previousPageUrl() ? '' : 'disabled' }}"
                            href="{{ $siswaData->previousPageUrl() ?: '#' }}"
                            @if(!$siswaData->previousPageUrl()) aria-disabled="true" @endif>
                            <span class="material-symbols-rounded">chevron_left</span>
                        </a>
                        <a class="ann-icon-btn {{ $siswaData->nextPageUrl() ? '' : 'disabled' }}"
                            href="{{ $siswaData->nextPageUrl() ?: '#' }}"
                            @if(!$siswaData->nextPageUrl()) aria-disabled="true" @endif>
                            <span class="material-symbols-rounded">chevron_right</span>
                        </a>
                    </div>
                </div>
            </div>
        </form>
 <div class="ann-table ann-table--siswa">
                <div class="ann-table__head">
                    <div class="cell cell--no">No.</div>
                    <div class="cell">NIS</div>
                    <div class="cell">Nama</div>
                    <div class="cell">Kelas</div>
                    <div class="cell">Jenis Kelamin</div>
                    <div class="cell">Tanggal Lahir</div>
                </div>
                <div class="ann-table__body">
                    @forelse ($siswaData as $index => $siswa)
                        @php
                                $ttlDate = $siswa->tgl_lahir ? \Carbon\Carbon::parse($siswa->tgl_lahir)->format('d M Y') : '-';
                                $jk = $siswa->jenis_kelamin ?? '-';
                                $rowNumber = ($siswaData->firstItem() ?? 0) + $index;
                            @endphp
                            <div class="ann-row">
                                <div class="cell ann-meta__muted cell--no">{{ $rowNumber }}</div>
                                <div class="cell ann-meta__muted">{{ $siswa->nis }}</div>
                                <div class="cell ann-row__title">{{ $siswa->nama }}</div>
                                <div class="cell">{{ $siswa->kelas_sekarang }}</div>
                                <div class="cell">{{ $jk }}</div>
                                <div class="cell">{{ $ttlDate }}</div>
                        </div>
                    @empty
                        <div class="ann-empty">Tidak ada data siswa untuk filter yang dipilih.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
