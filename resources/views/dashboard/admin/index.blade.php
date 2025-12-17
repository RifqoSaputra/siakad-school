@extends('layouts.template')

@section('title', 'Dashboard')

@push('scripts')
    @vite(['resources/js/announcement.js'])
@endpush

@section('content')
    <div class="ann-layout">
        <div class="ann-bar ann-bar--stack"></div>

        <div class="ann-summary-grid ann-summary-grid--compact">
            <a class="ann-summary-card ann-summary-card--stat" href="{{ route('admin.guru') }}">
                <div class="ann-summary-card__icon ann-summary-card__icon--blue material-symbols-rounded">school</div>
                <div class="ann-summary-card__body">
                    <div class="ann-summary-card__label">Total Guru</div>
                    <div class="ann-summary-card__value ann-summary-card__value--xl">{{ $totalGuru ?? 0 }}</div>
                </div>
                <span class="ann-summary-card__chevron material-symbols-rounded" aria-hidden="true">chevron_right</span>
            </a>
            <a class="ann-summary-card ann-summary-card--stat" href="{{ route('admin.siswa') }}">
                <div class="ann-summary-card__icon ann-summary-card__icon--green material-symbols-rounded">group</div>
                <div class="ann-summary-card__body">
                    <div class="ann-summary-card__label">Total Siswa</div>
                    <div class="ann-summary-card__value ann-summary-card__value--xl">{{ $totalSiswa ?? 0 }}</div>
                </div>
                <span class="ann-summary-card__chevron material-symbols-rounded" aria-hidden="true">chevron_right</span>
            </a>
            <a class="ann-summary-card ann-summary-card--stat" href="{{ route('admin.kelas') }}">
                <div class="ann-summary-card__icon ann-summary-card__icon--purple material-symbols-rounded">import_contacts</div>
                <div class="ann-summary-card__body">
                    <div class="ann-summary-card__label">Total Kelas</div>
                    <div class="ann-summary-card__value ann-summary-card__value--xl">{{ $totalKelas ?? 0 }}</div>
                </div>
                <span class="ann-summary-card__chevron material-symbols-rounded" aria-hidden="true">chevron_right</span>
            </a>
        </div>

        <div class="ann-section">
            <div class="ann-bar ann-bar--stack">
                <div class="ann-bar__left">
                    <h5 class="ann-title mb-0">Pengumuman Terbaru</h5>
                    <div class="ann-meta">
                        <span class="ann-meta__dot"></span>
                        <span>{{ count($pengumumanTerbaru ?? []) }} item</span>
                    </div>
                </div>
                <div class="ann-toolbar__actions">
                    <a class="ann-btn ann-btn--primary" href="{{ route('admin.pengumuman.index') }}">
                        Pengumuman Baru <span class="material-symbols-rounded">add</span>
                    </a>
                </div>
            </div>

            <div class="ann-table ann-table--dashboard">
                <div class="ann-table__head">
                    <div>Waktu</div>
                    <div>Penerima</div>
                    <div>Subjek</div>
                    <div class="ann-status-head">Status</div>
                </div>
                <div class="ann-table__body">
                    @forelse (collect($pengumumanTerbaru ?? [])->take(10) as $row)
                        @php
                            $attachments = $row->attachments->map(function($att) {
                                $mime = $att->file_mime ?? $att->mime_type ?? '';
                                $icon = str_starts_with($mime, 'image') ? 'image' : 'picture_as_pdf';
                                $label = $att->file_name ?? $att->nama_file ?? basename($att->file_path ?? $att->path ?? 'Lampiran');
                                $url = $att->file_path ?? $att->path ? asset('storage/'.($att->file_path ?? $att->path)) : null;
                                return ['icon'=>$icon, 'label'=>$label, 'url'=>$url];
                            });
                            $displayTitle = preg_replace('/\\s+#\\d+$/', '', $row->judul);
                        @endphp
                        <div class="ann-row {{ $attachments->count() ? 'has-attachments' : '' }}">
                            @php
                                $dateSource = $row->sent_at ?? $row->scheduled_for ?? $row->created_at;
                                $dateText = '-';
                                if ($dateSource) {
                                    $date = $dateSource instanceof \Illuminate\Support\Carbon
                                        ? $dateSource
                                        : \Illuminate\Support\Carbon::parse($dateSource);
                                    $dateText = $date->locale('id')->translatedFormat('j M Y, H.i');
                                }
                                $targetLabel = [
                                    'guru' => 'Guru',
                                    'ortu' => 'Ortu',
                                    'all' => 'Semua',
                                    'semua' => 'Semua',
                                ][$row->target_role] ?? ($row->target_role ?? '-');
                            @endphp
                            <div class="cell">{{ $dateText }}</div>
                            <div class="cell">{{ $targetLabel }}</div>
                            <div class="cell ann-title-cell">
                                <div class="ann-row__title">{{ $displayTitle }}</div>
                                @if($attachments->count())
                                    <div class="ann-attachments">
                                        @foreach ($attachments as $chip)
                                            @php
                                                $isPdf = $chip['icon'] === 'picture_as_pdf';
                                                $iconClass = $isPdf ? 'pdf-icon' : ($chip['icon'] === 'image' ? 'img-icon' : '');
                                            @endphp
                                            @if($chip['url'])
                                                <a class="ann-chip {{ $isPdf ? 'ann-chip--pdf' : '' }}" href="{{ $chip['url'] }}" target="_blank" rel="noopener">
                                                    <span class="material-symbols-rounded {{ $iconClass }}">{{ $chip['icon'] }}</span>
                                                    <span class="ann-chip__text">{{ $chip['label'] }}</span>
                                                </a>
                                            @else
                                                <span class="ann-chip {{ $isPdf ? 'ann-chip--pdf' : '' }}">
                                                    <span class="material-symbols-rounded {{ $iconClass }}">{{ $chip['icon'] }}</span>
                                                    <span class="ann-chip__text">{{ $chip['label'] }}</span>
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            @php
                                $statusLabelMap = [
                                    'sent' => 'Diumumkan',
                                    'scheduled' => 'Dijadwalkan',
                                    'draft' => 'Draf',
                                    'published' => 'Diumumkan',
                                ];
                                $statusClassMap = [
                                    'sent' => 'ann-status--sent',
                                    'scheduled' => 'ann-status--scheduled',
                                    'draft' => 'ann-status--draft',
                                    'published' => 'ann-status--sent',
                                ];
                                $statusIconMap = [
                                    'sent' => 'public',
                                    'scheduled' => 'schedule',
                                    'draft' => 'description',
                                    'published' => 'public',
                                ];
                                $statusKey = $row->status ?? '';
                                $statusLabel = $statusLabelMap[$statusKey] ?? ($statusKey ?: '-');
                                $statusClass = $statusClassMap[$statusKey] ?? '';
                                $statusIcon = $statusIconMap[$statusKey] ?? 'public';
                            @endphp
                            <div class="cell ann-status-cell">
                                <span class="ann-status {{ $statusClass }}">
                                    <span class="material-symbols-rounded">{{ $statusIcon }}</span>
                                    {{ $statusLabel }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="ann-empty">Belum ada pengumuman.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
