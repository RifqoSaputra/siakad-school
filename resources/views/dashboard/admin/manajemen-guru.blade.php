@extends('layouts.template')

@section('title', 'Manajemen Guru')

@push('styles')
    <style>
        .topbar { background: var(--bg-card, #fff); }
        .page-container { padding-top: 4px; }
    </style>
@endpush

@push('scripts')
    @vite(['resources/js/app-ui.js'])
@endpush

@section('content')
    @php
        $hasFilters = $request->filled('search') || $request->filled('status') || $request->filled('jenis_kelamin');
        $firstItem = $guruData->firstItem();
        $lastItem = $guruData->lastItem();
        $totalItems = $guruData->total() ?? 0;
        $rangeText = ($firstItem && $lastItem) ? "{$firstItem}–{$lastItem}" : '0';
        $totalAktif = $totalGuruAktif ?? 0;
        $totalSemua = $totalGuru ?? 0;
        $totalNonaktif = max($totalSemua - $totalAktif, 0);
        $statusMap = [
            'aktif' => ['label' => 'Aktif', 'icon' => 'check_circle', 'class' => 'status--active'],
            'nonaktif' => ['label' => 'Nonaktif', 'icon' => 'error', 'class' => 'status--inactive'],
        ];
        $summaryCards = [
            [
                'title' => 'Total Seluruh Guru',
                'icon' => 'group',
                'primary' => $totalSemua,
                'meta' => [
                    ['label' => 'Aktif', 'value' => $totalAktif],
                    ['label' => 'Nonaktif', 'value' => $totalNonaktif],
                ],
            ],
            [
                'title' => 'Guru Aktif',
                'icon' => 'verified',
                'primary' => $totalAktif,
                'meta' => [
                    ['label' => 'Status', 'value' => 'Aktif'],
                    ['label' => 'Nonaktif', 'value' => $totalNonaktif],
                ],
            ],
            [
                'title' => 'Guru Nonaktif',
                'icon' => 'do_not_disturb_on',
                'primary' => $totalNonaktif,
                'meta' => [
                    ['label' => 'Total', 'value' => $totalSemua],
                    ['label' => 'Aktif', 'value' => $totalAktif],
                ],
            ],
        ];
    @endphp

    <div class="ann-layout ann-layout--guru">

        <form method="GET" action="{{ route('admin.guru') }}" class="toolbar__row toolbar__standalone toolbar--inline">
            <input type="hidden" name="status" value="{{ $request->get('status') }}">
            <input type="hidden" name="jenis_kelamin" value="{{ $request->get('jenis_kelamin') }}">
            <div class="ann-search__cluster">
                <div class="ann-search__field">
                    <div class="ann-search__input">
                        <span class="material-symbols-rounded">search</span>
                        <input type="text" name="search" id="search_guru" placeholder="Ketik nama atau NIP"
                            value="{{ $request->get('search') }}">
                    </div>
                </div>
            </div>

            <div class="ann-toolbar__chunk ann-toolbar__chunk--filters">
                <x-ui.filter-bar>
                    <x-ui.filter-dropdown :label="'Status Aktif'" :value="$request->get('status') ?: 'Status Aktif'" filter="status">
                        <x-ui.filter-option value="" :active="!$request->filled('status')">Semua Status</x-ui.filter-option>
                        @foreach ($allStatus as $status)
                            <x-ui.filter-option :value="$status" :active="$request->get('status') == $status">
                                {{ $status }}
                            </x-ui.filter-option>
                        @endforeach
                    </x-ui.filter-dropdown>

                    <x-ui.filter-dropdown :label="'Jenis Kelamin'" :value="$request->get('jenis_kelamin') ?: 'Jenis Kelamin'" filter="jenis_kelamin">
                        <x-ui.filter-option value="" :active="!$request->filled('jenis_kelamin')">Semua</x-ui.filter-option>
                        @foreach ($allJenisKelamin as $jk)
                            <x-ui.filter-option :value="$jk" :active="$request->get('jenis_kelamin') == $jk">
                                {{ $jk }}
                            </x-ui.filter-option>
                        @endforeach
                    </x-ui.filter-dropdown>

                    <button type="button" class="reset {{ $hasFilters ? 'show' : '' }}" id="filter-reset">
                        <span class="material-symbols-rounded">refresh</span>
                        Reset
                    </button>
                </x-ui.filter-bar>
            </div>

            <div class="ann-toolbar__chunk ann-toolbar__chunk--pagination">
                <div class="ann-pagination ann-pagination--inline ann-pagination--right">
                    <span class="ann-pagination__text">
                        <span class="ann-pagination__label">Menampilkan</span>
                        <span class="ann-pagination__current">{{ $rangeText }}</span>
                        <span class="ann-pagination__total">dari {{ $totalItems }}</span>
                    </span>
                    <div class="ann-pagination__arrows">
                        <button class="ann-icon-btn" data-nav-url="{{ $guruData->previousPageUrl() }}"
                            @disabled(!$guruData->previousPageUrl())>
                            <span class="material-symbols-rounded">chevron_left</span>
                        </button>
                        <button class="ann-icon-btn" data-nav-url="{{ $guruData->nextPageUrl() }}"
                            @disabled(!$guruData->nextPageUrl())>
                            <span class="material-symbols-rounded">chevron_right</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <div class="ann-table-scroll">
            <div class="ann-table ann-table--guru">
                <div class="ann-table__head">
                    <div class="cell cell--no">No.</div>
                    <div class="cell cell--status">Status</div>
                    <div class="cell">Kode Guru</div>
                    <div class="cell">Nama</div>
                    <div class="cell">Jenis Kelamin</div>
                    <div class="cell cell--email">Email</div>
                </div>

                <div class="ann-table__body">
                    @forelse ($guruData as $idx => $guru)
                        @php
                            $statusValue = ($guru->user_status ?? 1) ? 'aktif' : 'nonaktif';
                            $statusMeta = $statusMap[$statusValue] ?? [
                                'label' => 'Perlu verifikasi',
                                'icon' => 'hourglass_empty',
                                'class' => 'status--pending',
                            ];
                            $rowNumber = ($guruData->firstItem() ?? 0) + $idx;
                            $formattedGuruId = 'G' . str_pad($guru->id_guru ?? 0, 4, '0', STR_PAD_LEFT);
                            $kodeGuru = $guru->kode_guru ?? $formattedGuruId;
                            $gender = $guru->jenis_kelamin ?? 'Tidak diketahui';
                            $emailPrefix = $guru->email ? explode('@', $guru->email)[0] : \Illuminate\Support\Str::slug($guru->nama_guru ?? 'guru', '.');
                            $emailDisplay = $emailPrefix ? $emailPrefix . '@mutiarabangsa.ac.id' : 'Email belum diisi';
                        @endphp
                        <div class="ann-row ann-row--clickable"
                            data-guru-id="{{ $kodeGuru }}"
                            data-guru-kode="{{ $kodeGuru }}"
                            data-guru-name="{{ $guru->nama_guru }}"
                            data-guru-nip="{{ $guru->kode_guru ?? '' }}"
                            data-guru-email="{{ $emailDisplay }}"
                            data-guru-gender="{{ $gender }}"
                            data-guru-status="{{ $statusMeta['label'] }}">
                            <div class="cell ann-meta__muted cell--no">{{ $rowNumber }}</div>
                            <div class="cell cell--status ann-status-cell">
                                <div class="ann-status-dropdown" data-status-dropdown data-guru-id="{{ $formattedGuruId }}"
                                    data-guru-name="{{ $guru->nama_guru }}" data-current-status="{{ $statusMeta['label'] }}">
                                    <button type="button" class="ann-status status ann-status--action {{ $statusMeta['class'] }}"
                                        data-status-trigger>
                                        <span class="ann-status__label">{{ $statusMeta['label'] }}</span>
                                        <span class="material-symbols-rounded ann-status__caret">arrow_drop_down</span>
                                    </button>
                                    <div class="ann-status-menu">
                                        <button type="button" class="ann-status-menu__item" data-status-option="Aktif">Aktif</button>
                                        <button type="button" class="ann-status-menu__item" data-status-option="Nonaktif">Nonaktif</button>
                                    </div>
                                </div>
                            </div>
                            <div class="cell ann-meta__muted cell--id">{{ $kodeGuru }}</div>
                            <div class="cell ann-title-cell">
                                <div class="ann-row__title">{{ $guru->nama_guru }}</div>
                            </div>
                            <div class="cell ann-meta__muted">{{ $gender }}</div>
                            <div class="cell ann-meta__muted cell--email">{{ $emailDisplay }}</div>
                        </div>
                    @empty
                        <div class="ann-empty">Belum ada data guru.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@include('dashboard.admin.partials.modal-guru-detail')
@include('dashboard.admin.partials.modal-guru-status')
@endsection
