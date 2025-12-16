@extends('layouts.template')

@section('title', 'Notifikasi Pengumuman')

@push('styles')
    @vite(['resources/css/announcement.css'])
@endpush

@push('scripts')
    @vite(['resources/js/announcement.js'])
@endpush

@section('content')
    @php
        $firstItem = $notifications->firstItem();
        $lastItem = $notifications->lastItem();
        $totalItems = $notifications->total() ?? 0;
        $rangeText = ($firstItem && $lastItem) ? "{$firstItem}–{$lastItem}" : '0';
    @endphp

    <div class="ann-layout ann-layout--notif">
        <div class="ann-bar ann-bar--stack">
            <div class="ann-bar__left">
                <h4 class="ann-title mb-1">Notifikasi Pengumuman</h4>
                <p class="text-muted mb-0">Semua notifikasi pengumuman yang pernah dikirim ke akun Anda.</p>
            </div>
            <div class="ann-toolbar__actions">
                <form action="{{ route('pengumuman.notif.readAll') }}" method="POST">
                    @csrf
                    <button class="ann-btn ann-btn--secondary" type="submit">
                        Tandai semua dibaca <span class="material-symbols-rounded">done_all</span>
                    </button>
                </form>
            </div>
        </div>

        <div class="ann-section">
            <div class="ann-table ann-table--notif">
                <div class="ann-table__head">
                    <div>Judul</div>
                    <div>Target</div>
                    <div>Waktu</div>
                    <div>Status</div>
                </div>
                <div class="ann-table__body">
                    @forelse ($notifications as $notification)
                        @php
                            $target = optional($notification->pengumuman)->target_role;
                            $label = [
                                'guru' => 'Guru',
                                'ortu' => 'Ortu',
                                'all' => 'Semua',
                                'semua' => 'Semua',
                            ][$target] ?? ($target ? ucfirst($target) : '-');
                            $isRead = $notification->is_read;
                        @endphp
                        <div class="ann-row">
                            <div class="cell ann-row__title">{{ $notification->pengumuman->judul ?? 'Pengumuman' }}</div>
                            <div class="cell"><span class="ann-pill">{{ $label }}</span></div>
                            <div class="cell">{{ optional($notification->pengumuman)->created_at?->format('d M Y H:i') }}</div>
                            <div class="cell">
                                <form action="{{ route('pengumuman.notif.read', $notification->id) }}" method="POST">
                                    @csrf
                                    <button class="ann-btn ann-btn--secondary ann-btn--inline" type="submit">
                                        <span class="ann-status {{ $isRead ? 'ann-status--active' : 'ann-status--pending' }}">
                                            <span class="material-symbols-rounded">{{ $isRead ? 'done' : 'hourglass_empty' }}</span>
                                            {{ $isRead ? 'Sudah dibaca' : 'Belum dibaca' }}
                                        </span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="ann-empty">Belum ada notifikasi pengumuman.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="ann-header__right ann-header--guru">
            <div class="ann-pagination ann-pagination--inline ann-pagination--right">
                <span class="ann-pagination__text">
                    <span class="ann-pagination__label">Menampilkan</span>
                    <span class="ann-pagination__current">{{ $rangeText }}</span>
                    <span class="ann-pagination__total">dari {{ $totalItems }}</span>
                </span>
                <div class="ann-pagination__arrows">
                    <button class="ann-icon-btn" data-nav-url="{{ $notifications->previousPageUrl() }}" @disabled(!$notifications->previousPageUrl())>
                        <span class="material-symbols-rounded">chevron_left</span>
                    </button>
                    <button class="ann-icon-btn" data-nav-url="{{ $notifications->nextPageUrl() }}" @disabled(!$notifications->nextPageUrl())>
                        <span class="material-symbols-rounded">chevron_right</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
