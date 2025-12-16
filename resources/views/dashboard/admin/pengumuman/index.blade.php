@extends('layouts.template')

@section('title', 'Pengumuman')

@push('styles')
    @vite(['resources/css/announcement.css'])
@endpush

@push('scripts')
    @vite(['resources/js/announcement.js'])
@endpush

@section('content')
<div class="ann-layout">
    <div class="ann-bar">
        @php
            $dateMap = [
                'any' => 'Kapan saja',
                'week' => 'Seminggu terakhir',
                'month' => 'Sebulan terakhir',
                '6months' => '6 bulan terakhir',
                'year' => 'Setahun terakhir',
            ];

            $hasFilters = ($dateRange !== 'any') || ($target !== 'any') || ($status !== 'any');
            $firstItem = $pengumuman->firstItem();
            $lastItem = $pengumuman->lastItem();
            $totalItems = $pengumuman->total() ?? 0;

            $rangeText = ($firstItem && $lastItem) ? "{$firstItem}–{$lastItem}" : '0';
        @endphp

        <div class="ann-filters">
            <div class="ann-filter" data-filter="date_range">
                <button class="ann-filter__btn">
                    <span>{{ $dateMap[$dateRange] ?? 'Kapan saja' }}</span>
                    <span class="material-symbols-rounded">arrow_drop_down</span>
                </button>
                <div class="ann-filter__menu">
                    @foreach ($dateMap as $val => $label)
                        <button class="ann-filter__option" data-value="{{ $val }}">
                            <span class="ann-radio {{ $dateRange === $val ? 'active' : '' }}"></span>{{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

        <div class="ann-filter" data-filter="target_role">
            <button class="ann-filter__btn">
                <span>{{ ['any'=>'Penerima','all'=>'Semua','guru'=>'Guru','ortu'=>'Ortu'][$target] ?? 'Penerima' }}</span>
                <span class="material-symbols-rounded">arrow_drop_down</span>
            </button>
            <div class="ann-filter__menu">
                @foreach (['all'=>'Semua','guru'=>'Guru','ortu'=>'Ortu'] as $val => $label)
                    <button class="ann-filter__option {{ $val === 'all' ? 'ann-filter__option--divider' : '' }}" data-value="{{ $val }}">
                            <span class="ann-radio {{ $target === $val ? 'active' : '' }}"></span>{{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="ann-filter" data-filter="status">
                <button class="ann-filter__btn">
                    <span>{{ ['any'=>'Status','sent'=>'Dikirim','scheduled'=>'Dijadwalkan','draft'=>'Draft'][$status] ?? 'Status' }}</span>
                    <span class="material-symbols-rounded">arrow_drop_down</span>
                </button>
                <div class="ann-filter__menu">
                    @foreach (['any'=>'Semua','sent'=>'Dikirim','scheduled'=>'Dijadwalkan','draft'=>'Draft'] as $val => $label)
                        <button class="ann-filter__option {{ $val === 'any' ? 'ann-filter__option--divider' : '' }}" data-value="{{ $val }}">
                            <span class="ann-radio {{ $status === $val ? 'active' : '' }}"></span>{{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>
            <button type="button" class="ann-reset {{ $hasFilters ? 'show' : '' }}" id="filter-reset">
                <span class="material-symbols-rounded">refresh</span>
                Reset Filter
            </button>
        </div>

        <div class="ann-header__right">
            <div class="ann-pagination">
                <span class="ann-pagination__text">
                    <span class="ann-pagination__label">Menampilkan</span>
                    <span class="ann-pagination__current">{{ $rangeText }}</span>
                    <span class="ann-pagination__total">dari {{ $totalItems }}</span>
                </span>
                <div class="ann-pagination__arrows">
                    <button class="ann-icon-btn" data-nav-url="{{ $pengumuman->previousPageUrl() }}" @disabled(!$pengumuman->previousPageUrl())>
                        <span class="material-symbols-rounded">chevron_left</span>
                    </button>
                    <button class="ann-icon-btn" data-nav-url="{{ $pengumuman->nextPageUrl() }}" @disabled(!$pengumuman->nextPageUrl())>
                        <span class="material-symbols-rounded">chevron_right</span>
                    </button>
                </div>
            </div>
            <button class="ann-btn ann-btn--primary" id="btn-open-create" type="button">
                Buat <span class="material-symbols-rounded">add</span>
            </button>
        </div>
    </div>

        <div class="ann-table">
            <div class="ann-table__head">
                <div>Waktu</div>
                <div>Penerima</div>
                <div>Subjek</div>
                <div class="ann-status-head">Status</div>
            </div>
        <div class="ann-table__body">
            @forelse ($pengumuman as $row)
                @php
                        $attachments = $row->attachments->map(function($att) {
                            $mime = $att->file_mime ?? $att->mime_type ?? '';
                            $icon = str_starts_with($mime, 'image') ? 'image' : 'picture_as_pdf';
                            $label = $att->file_name ?? $att->nama_file ?? basename($att->file_path ?? $att->path ?? 'Lampiran');
                            $url = $att->file_path ?? $att->path ? asset('storage/'.($att->file_path ?? $att->path)) : null;
                            return ['icon'=>$icon, 'label'=>$label, 'url'=>$url];
                        });
                    $displayTitle = preg_replace('/\s+#\d+$/', '', $row->judul);
                    $detailPayload = [
                        'id' => $row->getKey(),
                        'judul' => $row->judul,
                        'isi_pengumuman' => $row->isi_pengumuman,
                        'meta_date' => $row->metaDateLong(),
                        'target_label' => $row->targetLabel(),
                        'status_label' => $row->statusLabel(),
                        'status' => $row->status,
                        'status_icon' => $row->statusIcon(),
                        'scheduled_for_iso' => optional($row->scheduled_for)->format('Y-m-d\TH:i'),
                        'sent_at_iso' => optional($row->sent_at)->format('Y-m-d\TH:i'),
                        'attachments' => $row->attachments->map(function($att) {
                            $mime = $att->file_mime ?? $att->mime_type ?? '';
                            $icon = str_starts_with($mime, 'image') ? 'image' : 'picture_as_pdf';
                            $label = $att->file_name ?? $att->nama_file ?? basename($att->file_path ?? $att->path ?? 'Lampiran');
                            return [
                                'id'=>$att->getKey(),
                                'icon'=>$icon,
                                'label'=>$label,
                                'size'=>$att->file_size ?? $att->size,
                                'url'=> $att->file_path ?? $att->path ? asset('storage/'.($att->file_path ?? $att->path)) : null,
                            ];
                        }),
                    ];
                @endphp
                <div class="ann-row {{ $attachments->count() ? 'has-attachments' : '' }}" data-detail='@json($detailPayload)'>
                    <div class="cell">{{ $row->waktuSingkat() }}</div>
                    <div class="cell">{{ $row->targetLabel() }}</div>
                    <div class="cell ann-title-cell">
                        <div class="ann-row__title">{{ $displayTitle }}</div>
                        @if($attachments->count())
                            <div class="ann-attachments">
                                @foreach ($attachments as $chip)
                                    @php
                                        $isPdf = $chip['icon'] === 'picture_as_pdf';
                                        $isImage = $chip['icon'] === 'image';
                                        $iconClass = $isPdf ? 'pdf-icon' : ($isImage ? 'img-icon' : '');
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
                    <div class="cell ann-status-cell">
                        <span class="ann-status {{ $row->statusClass() }}">
                            <span class="material-symbols-rounded">{{ $row->statusIcon() }}</span>
                            {{ $row->statusLabel() }}
                        </span>
                        @if($row->status !== 'sent')
                            <button class="ann-edit-btn" data-edit="{{ $row->getKey() }}">
                                Edit <span class="material-symbols-rounded">edit</span>
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="ann-empty">Belum ada pengumuman.</div>
            @endforelse
        </div>
    </div>

</div>

{{-- Detail Modal --}}
<div class="ann-modal" id="modal-detail">
    <div class="ann-modal__overlay"></div>
        <div class="ann-modal__card">
            <div class="ann-modal__header">
                <div class="ann-modal__label">Detail Pengumuman</div>
                <button class="ann-icon-btn" data-close="detail"><span class="material-symbols-rounded">close</span></button>
            </div>
        <div class="ann-modal__heading">
            <h2 class="ann-modal__title" id="detail-title"></h2>
            <div class="ann-modal__meta" id="detail-meta"></div>
        </div>
        <div class="ann-divider-stack">
            <div class="ann-divider"></div>
            <div class="ann-divider"></div>
        </div>
        <div class="ann-modal__body" id="detail-body"></div>
        <div class="ann-attachments" id="detail-attachments"></div>
    </div>
</div>

{{-- Form Modal --}}
<div class="ann-modal" id="modal-form">
    <div class="ann-modal__overlay"></div>
    <div class="ann-modal__card ann-modal__card--form">
        <div class="ann-modal__header">
            <div class="ann-modal__label" id="form-title">Buat Pengumuman</div>
            <button class="ann-icon-btn" data-close="form"><span class="material-symbols-rounded">close</span></button>
        </div>

        <form id="announcement-form" method="POST" action="{{ route('admin.pengumuman.store') }}"
            data-store="{{ route('admin.pengumuman.store') }}"
            data-update-template="{{ route('admin.pengumuman.update', ['pengumuman' => '__ID__']) }}"
            data-destroy-template="{{ route('admin.pengumuman.destroy', ['pengumuman' => '__ID__']) }}"
            enctype="multipart/form-data">
            @csrf
            @method('POST')
            <input type="hidden" name="action_status" id="action_status" value="sent">
            <div class="ann-form">
                <div class="ann-field">
                    <label class="ann-field__label" for="send_mode_now">Diumumkan Pada</label>
                </div>

                <div class="ann-sendmode">
                    <label class="ann-sendmode__option" for="send_mode_now">
                        <input type="radio" id="send_mode_now" name="send_mode" value="now" checked>
                        <span class="ann-radio"></span>
                        <div class="ann-sendmode__text">
                            <div class="ann-sendmode__title">Saat ini, <span class="ann-sendmode__date">{{ now()->locale('id')->translatedFormat('j M Y') }}</span></div>
                        </div>
                    </label>
                    <div class="ann-sendmode__divider"></div>
                    <label class="ann-sendmode__option" for="send_mode_schedule">
                        <input type="radio" id="send_mode_schedule" name="send_mode" value="schedule">
                        <span class="ann-radio"></span>
                        <div class="ann-sendmode__text">
                            <div class="ann-sendmode__title">Jadwalkan</div>
                        </div>
                    </label>
                </div>

                <div class="ann-field-inline" id="schedule_section" style="display:none;">
                    <div class="ann-field">
                        <label class="ann-field__label" for="scheduled_display_text">Tanggal</label>
                        <div class="ann-datetime">
                            <input type="hidden" class="ann-input ann-input--datetime" id="scheduled_input" name="scheduled_for">
                            <div class="ann-datetime__display" id="scheduled_display" tabindex="0">
                                <input type="text" id="scheduled_display_text" name="scheduled_display_text" class="ann-datetime__input" placeholder="Pilih tanggal" autocomplete="off">
                                <span class="material-symbols-rounded">calendar_month</span>
                            </div>
                            <div class="ann-datetime__popover" id="scheduled_popover">
                                <div class="ann-datetime__header">
                                    <span id="scheduled_month_label">Dec 2025</span>
                                    <div class="ann-datetime__nav">
                                        <button type="button" data-month-nav="prev" class="ann-icon-btn"><span class="material-symbols-rounded">chevron_left</span></button>
                                        <button type="button" data-month-nav="next" class="ann-icon-btn"><span class="material-symbols-rounded">chevron_right</span></button>
                                    </div>
                                </div>
                                <div class="ann-datetime__weekday">
                                    <span>Min</span><span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span>
                                </div>
                                <div class="ann-datetime__grid" id="scheduled_days"></div>
                            </div>
                        </div>
                    </div>

                    <div class="ann-field">
                        <label class="ann-field__label" for="scheduled_time_input">Waktu</label>
                        <div class="ann-datetime__display ann-datetime__display--time">
                            <input type="text" id="scheduled_time_input" name="scheduled_time_input" class="ann-input ann-input--time" inputmode="numeric" placeholder="00:00" autocomplete="off">
                            <span class="material-symbols-rounded">schedule</span>
                        </div>
                    </div>
                </div>
                <div class="ann-error" id="datetime-error">Tanggal telah berlalu. Silahkan pilih jadwal lain.</div>

                <div class="ann-field">
                    <label class="ann-field__label" for="recipient_all">Penerima</label>
                    <div class="ann-recipient" id="recipient_field">
                        <label class="ann-recipient__option is-active" for="recipient_all">
                            <input type="radio" id="recipient_all" name="target_role" value="all" checked>
                            <span class="ann-radio active"></span>
                            <span class="ann-recipient__label">Semua</span>
                        </label>
                        <div class="ann-sendmode__divider"></div>
                        <label class="ann-recipient__option" for="recipient_guru">
                            <input type="radio" id="recipient_guru" name="target_role" value="guru">
                            <span class="ann-radio"></span>
                            <span class="ann-recipient__label">Guru</span>
                        </label>
                        <div class="ann-sendmode__divider"></div>
                            <label class="ann-recipient__option" for="recipient_ortu">
                            <input type="radio" id="recipient_ortu" name="target_role" value="ortu">
                            <span class="ann-radio"></span>
                            <span class="ann-recipient__label">Ortu</span>
                        </label>
                    </div>
                </div>

                <div class="ann-field" id="field-title">
                    <label class="ann-field__label" for="title-input">
                        Subjek
                        <span class="ann-counter" id="title-count">0/200</span>
                    </label>
                    <input type="text" name="judul" class="ann-input" id="title-input" required placeholder="Masukkan subjek...">
                    <div class="ann-field__error" id="title-error"></div>
                </div>

                <div class="ann-field" id="field-body">
                    <label class="ann-field__label" for="body-input">
                        Isi Pengumuman
                        <span class="ann-counter" id="body-count">0/2000</span>
                    </label>
                    <textarea name="isi_pengumuman" rows="6" class="ann-textarea" id="body-input" required placeholder="Masukkan isi pengumuman..."></textarea>
                    <div class="ann-field__error" id="body-error"></div>
                </div>

                <div class="ann-field">
                    <label class="ann-field__label" for="attachments_input">Lampiran</label>
                    <div class="ann-attach">
                        <div class="ann-attach__row">
                            <div class="ann-attach__info">
                                <p class="ann-attach__hint">Unggah file pendukung (PDF, gambar, dokumen).</p>
                                <span class="ann-attach__total" id="attachments_total">0/10MB</span>
                            </div>
                            <button type="button" class="ann-attach__action" id="attachments_trigger">
                                <span class="material-symbols-rounded">upload</span> Unggah Lampiran
                            </button>
                        </div>
                        <div class="ann-attach__error" id="attachments-error" style="display:none">Ukuran lampiran melebihi 10MB, silahkan unggah kembali.</div>
                        <input type="file" id="attachments_input" name="attachments[]" multiple style="display:none" accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg,.gif">
                    </div>
                    <div class="ann-attach__list" id="attachments_list"></div>
                </div>
            </div>

            <div class="ann-modal__footer">
                <button type="button" class="ann-btn ann-btn--danger" id="btn-delete" style="display:none;">
                    <span class="material-symbols-rounded">delete</span>
                </button>
                <button type="button" class="ann-btn ann-btn--secondary" id="btn-save-draft" disabled>Simpan Draf</button>
                <button type="submit" class="ann-btn ann-btn--primary" id="btn-send" disabled>Umumkan</button>
            </div>
            <div id="removed_attachments" style="display:none;"></div>
        </form>
        <form id="delete-announcement-form" method="POST" style="display:none;">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>

{{-- Confirm Close Modal --}}
<div class="ann-modal" id="modal-confirm-close">
    <div class="ann-modal__overlay"></div>
    <div class="ann-modal__card ann-modal__card--form">
        <div class="ann-modal__header">
            <div class="ann-modal__label">Simpan Perubahan?</div>
            <button class="ann-icon-btn" data-close="confirm-close"><span class="material-symbols-rounded">close</span></button>
        </div>
        <div class="ann-modal__body">
            <p>Perubahan belum disimpan. Apakah ingin menyimpannya sebagai draf?</p>
        </div>
        <div class="ann-modal__footer">
            <button type="button" class="ann-btn ann-btn--danger-ghost" id="btn-discard">Tidak, Hapus</button>
            <button type="button" class="ann-btn ann-btn--primary" id="btn-confirm-draft">Simpan Draf</button>
        </div>
    </div>
</div>

{{-- Delete Draft Modal --}}
<div class="ann-modal" id="modal-delete">
    <div class="ann-modal__overlay"></div>
    <div class="ann-modal__card ann-modal__card--form">
        <div class="ann-modal__header">
            <div class="ann-modal__label">Hapus Pengumuman</div>
            <button class="ann-icon-btn" data-close="delete"><span class="material-symbols-rounded">close</span></button>
        </div>
        <div class="ann-modal__body">
            <p>Pengumuman ini akan dihapus secara permanen. Lanjutkan?</p>
        </div>
        <div class="ann-modal__footer">
            <button type="button" class="ann-btn ann-btn--secondary" id="btn-cancel-delete">Tidak, Batalkan</button>
            <button type="button" class="ann-btn ann-btn--danger" id="btn-confirm-delete">
                <span class="material-symbols-rounded">delete</span> Ya, Hapus
            </button>
        </div>
    </div>
</div>
@endsection
