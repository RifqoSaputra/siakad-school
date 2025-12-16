@extends('layouts.template')

@section('title', 'Manajemen Guru')

@push('styles')
    @vite(['resources/css/announcement.css'])
@endpush

@push('scripts')
    @vite(['resources/js/announcement.js'])
@endpush

@section('content')
    @php
        $hasFilters = $request->filled('search') || $request->filled('status') || $request->filled('status_kepegawaian');
        $firstItem = $guruData->firstItem();
        $lastItem = $guruData->lastItem();
        $totalItems = $guruData->total() ?? 0;
        $rangeText = ($firstItem && $lastItem) ? "{$firstItem}–{$lastItem}" : '0';
        $totalAktif = $totalGuruAktif ?? 0;
        $totalSemua = $totalGuru ?? 0;
        $totalPns = $totalGuruPNS ?? 0;
        $totalNonaktif = max($totalSemua - $totalAktif, 0);
        $totalNonPns = max($totalSemua - $totalPns, 0);
        $statusMap = [
            'aktif' => ['label' => 'Aktif', 'icon' => 'check_circle', 'class' => 'ann-status--active'],
            'nonaktif' => ['label' => 'Nonaktif', 'icon' => 'error', 'class' => 'ann-status--inactive'],
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
                    ['label' => 'PNS', 'value' => $totalPns],
                    ['label' => 'Non PNS', 'value' => $totalNonPns],
                ],
            ],
            [
                'title' => 'Guru PNS',
                'icon' => 'workspace_premium',
                'primary' => $totalPns,
                'meta' => [
                    ['label' => 'Total', 'value' => $totalSemua],
                    ['label' => 'Non PNS', 'value' => $totalNonPns],
                ],
            ],
        ];
    @endphp

    <div class="ann-layout ann-layout--guru">
        <div class="ann-summary-grid">
            @foreach ($summaryCards as $card)
                <div class="ann-summary-card ann-summary-card--stat ann-summary-card--with-meta">
                    <div class="ann-summary-card__icon ann-summary-card__icon--subtle material-symbols-rounded">{{ $card['icon'] }}</div>
                    <div class="ann-summary-card__body">
                        <div class="ann-summary-card__label">{{ $card['title'] }}</div>
                        <div class="ann-summary-card__value ann-summary-card__value--xl">{{ $card['primary'] }}</div>
                        <div class="ann-summary-card__divider"></div>
                        <div class="ann-summary-card__meta">
                            @foreach ($card['meta'] as $meta)
                                <div class="ann-summary-card__meta-item">
                                    <div class="ann-summary-card__meta-label">{{ $meta['label'] }}</div>
                                    <div class="ann-summary-card__meta-value">{{ $meta['value'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <form method="GET" action="{{ route('admin.guru') }}" class="ann-toolbar__row ann-toolbar__standalone ann-toolbar--inline">
            <input type="hidden" name="status" value="{{ $request->get('status') }}">
            <input type="hidden" name="status_kepegawaian" value="{{ $request->get('status_kepegawaian') }}">
            <div class="ann-toolbar__chunk ann-toolbar__chunk--search">
                <div class="ann-search__cluster">
                    <div class="ann-search__field">
                        <div class="ann-search__input">
                            <span class="material-symbols-rounded">search</span>
                            <input type="text" name="search" id="search_guru" placeholder="Ketik nama atau NIP"
                                value="{{ $request->get('search') }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="ann-toolbar__chunk ann-toolbar__chunk--filters">
                <div class="ann-toolbar__filters">
                    <div class="ann-filter" data-filter="status">
                        <button type="button" class="ann-filter__btn">
                            <span>{{ $request->get('status') ?: 'Status Aktif' }}</span>
                            <span class="material-symbols-rounded">arrow_drop_down</span>
                        </button>
                        <div class="ann-filter__menu">
                            <button type="button" class="ann-filter__option" data-value="">
                                <span class="ann-radio {{ $request->filled('status') ? '' : 'active' }}"></span>Semua Status
                            </button>
                            @foreach ($allStatus as $status)
                                <button type="button" class="ann-filter__option" data-value="{{ $status }}">
                                    <span
                                        class="ann-radio {{ $request->get('status') == $status ? 'active' : '' }}"></span>{{ $status }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="ann-filter" data-filter="status_kepegawaian">
                        <button type="button" class="ann-filter__btn">
                            <span>{{ $request->get('status_kepegawaian') ?: 'Status Kepegawaian' }}</span>
                            <span class="material-symbols-rounded">arrow_drop_down</span>
                        </button>
                        <div class="ann-filter__menu">
                            <button type="button" class="ann-filter__option" data-value="">
                                <span
                                    class="ann-radio {{ $request->filled('status_kepegawaian') ? '' : 'active' }}"></span>Semua Kepegawaian
                            </button>
                            @foreach ($allStatusKepegawaian as $sk)
                                <button type="button" class="ann-filter__option" data-value="{{ $sk }}">
                                    <span
                                        class="ann-radio {{ $request->get('status_kepegawaian') == $sk ? 'active' : '' }}"></span>{{ $sk }}
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

        <div class="ann-table ann-table--guru">
            <div class="ann-table__head">
                <div>Guru</div>
                <div>NIP</div>
                <div>Email</div>
                <div>Nomor Telepon</div>
                <div>Kepegawaian</div>
                <div>Status</div>
                <div>Domisili</div>
            </div>
            <div class="ann-table__body">
                @forelse ($guruData as $guru)
                    @php
                        $statusValue = strtolower($guru->status ?? $guru->status_aktif ?? '');
                        $statusMeta = $statusMap[$statusValue] ?? [
                            'label' => $guru->status ?? $guru->status_aktif ?? 'Perlu verifikasi',
                            'icon' => 'hourglass_empty',
                            'class' => 'ann-status--pending',
                        ];
                    @endphp
                    <div class="ann-row">
                        <div class="cell ann-title-cell">
                            <div class="ann-row__title">{{ $guru->nama_guru }}</div>
                        </div>
                        <div class="cell ann-meta__muted">{{ $guru->nip ?? 'NIP tidak tersedia' }}</div>
                        <div class="cell ann-meta__muted">{{ $guru->email ?? 'Email belum diisi' }}</div>
                        <div class="cell ann-meta__muted">{{ $guru->no_hp ?? 'No HP belum diisi' }}</div>
                        <div class="cell cell--pill">
                            <span class="ann-pill">{{ $guru->status_kepegawaian ?? '–' }}</span>
                        </div>
                        <div class="cell cell--status ann-status-cell">
                            <span class="ann-status {{ $statusMeta['class'] }}">
                                <span class="material-symbols-rounded">{{ $statusMeta['icon'] }}</span>
                                {{ $statusMeta['label'] }}
                            </span>
                        </div>
                        <div class="cell">{{ $guru->kota_rmh ?? '–' }}</div>
                    </div>
                @empty
                    <div class="ann-empty">Belum ada data guru.</div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
