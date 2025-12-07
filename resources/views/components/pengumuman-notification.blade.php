@php
    /** @var \App\Models\SIAKAD\SCHOOL\User|null $authUser */
    $authUser = Auth::user();
    $isGuruOrOrtu = $authUser?->hasRole('Guru') || $authUser?->hasRole('Orang Tua');
    $unreadCount = $authUser?->unreadPengumumanNotifications()->count() ?? 0;
    $unreadList = $authUser
        ? $authUser->pengumumanNotifications()
            ->with(['pengumuman.attachments'])
            ->where('is_read', false)
            ->latest()
            ->get()
        : collect();
    $readList = $authUser
        ? $authUser->pengumumanNotifications()
            ->with(['pengumuman.attachments'])
            ->where('is_read', true)
            ->latest()
            ->limit(20)
            ->get()
        : collect();
@endphp

@if ($isGuruOrOrtu)
    {{-- Trigger button --}}
    <button class="btn btn-link position-relative text-decoration-none" type="button" data-bs-toggle="modal"
        data-bs-target="#notifModal">
        <i class="bi bi-bell-fill fs-4 text-secondary"></i>
        @if ($unreadCount > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                {{ $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Modal center --}}
    <div class="modal fade" id="notifModal" tabindex="-1" aria-labelledby="notifModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notifModalLabel">Notifikasi Pengumuman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div id="notifListWrapper">
                        <ul class="nav nav-tabs px-3 pt-3" id="notifTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="unread-tab" data-bs-toggle="tab" data-bs-target="#unread-pane" type="button" role="tab">Belum dibaca ({{ $unreadCount }})</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="read-tab" data-bs-toggle="tab" data-bs-target="#read-pane" type="button" role="tab">Sudah dibaca ({{ $readList->count() }})</button>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="unread-pane" role="tabpanel" aria-labelledby="unread-tab">
                                <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                                    <small class="text-muted">Klik untuk menandai dibaca</small>
                                    <form action="{{ route('pengumuman.notif.readAll') }}" method="POST">
                                        @csrf
                                        <button class="btn btn-sm btn-link text-decoration-none">Tandai semua</button>
                                    </form>
                                </div>
                                <div class="list-group list-group-flush" style="max-height: 50vh; overflow:auto;">
                                    @forelse ($unreadList as $notification)
                                        @php
                                            $p = $notification->pengumuman;
                                            $attData = $p?->attachments?->map(function ($att) {
                                                return [
                                                    'name' => $att->nama_file,
                                                    'mime' => $att->mime_type,
                                                    'url' => asset('storage/' . $att->path),
                                                ];
                                            })->values();
                                        @endphp
                                        <div class="list-group-item list-group-item-action d-flex gap-2 align-items-start bg-light notif-item"
                                            data-id="{{ $notification->id }}"
                                            data-judul="{{ $p->judul ?? 'Pengumuman' }}"
                                            data-isi="{{ $p->isi_pengumuman ?? '' }}"
                                            data-tanggal="{{ optional($p->created_at)?->format('d M Y H:i') }}"
                                            data-attachments='@json($attData)'>
                                            <div class="flex-grow-1">
                                                <div class="fw-semibold text-body">
                                                    {{ $p->judul ?? 'Pengumuman' }}
                                                </div>
                                                <small class="text-muted d-block">
                                                    {{ optional($p)->created_at?->diffForHumans() ?? '' }}
                                                </small>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="list-group-item text-center text-muted py-3">
                                            Tidak ada notifikasi baru.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                            <div class="tab-pane fade" id="read-pane" role="tabpanel" aria-labelledby="read-tab">
                                <div class="list-group list-group-flush" style="max-height: 50vh; overflow:auto;">
                                    @forelse ($readList as $notification)
                                        @php
                                            $p = $notification->pengumuman;
                                            $attData = $p?->attachments?->map(function ($att) {
                                                return [
                                                    'name' => $att->nama_file,
                                                    'mime' => $att->mime_type,
                                                    'url' => asset('storage/' . $att->path),
                                                ];
                                            })->values();
                                        @endphp
                                        <div class="list-group-item d-flex flex-column notif-item"
                                            data-id="{{ $notification->id }}"
                                            data-judul="{{ $p->judul ?? 'Pengumuman' }}"
                                            data-isi="{{ $p->isi_pengumuman ?? '' }}"
                                            data-tanggal="{{ optional($p->created_at)?->format('d M Y H:i') }}"
                                            data-attachments='@json($attData)'>
                                            <div class="fw-semibold text-body">
                                                {{ $p->judul ?? 'Pengumuman' }}
                                            </div>
                                            <small class="text-muted d-block">
                                                {{ optional($p)->created_at?->diffForHumans() ?? '' }}
                                            </small>
                                        </div>
                                    @empty
                                        <div class="list-group-item text-center text-muted py-3">
                                            Belum ada yang dibaca.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="notifDetailWrapper" class="p-4" style="display:none; max-height: 70vh; overflow:auto;">
                        <button type="button" class="btn btn-link mb-3 p-0" id="notifBackBtn">
                            <i class="bi bi-arrow-left"></i> Kembali ke daftar
                        </button>
                        <h5 id="detailTitle" class="mb-1"></h5>
                        <small class="text-muted d-block mb-2" id="detailDate"></small>
                        <div id="detailBody" class="mb-3"></div>
                        <div id="detailAttachments" class="d-flex flex-wrap gap-2"></div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-end align-items-center w-100">
                    <form id="notifReadForm" action="" method="POST" style="display:none;">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const detailBox = document.getElementById('notifDetail');
            const titleEl = document.getElementById('detailTitle');
            const dateEl = document.getElementById('detailDate');
            const bodyEl = document.getElementById('detailBody');
            const attachmentEl = document.getElementById('detailAttachments');
            const readForm = document.getElementById('notifReadForm');
            const listWrapper = document.getElementById('notifListWrapper');
            const detailWrapper = document.getElementById('notifDetailWrapper');
            const backBtn = document.getElementById('notifBackBtn');

            function iconForMime(mime) {
                if (mime && mime.startsWith('image/')) return 'bi bi-image';
                const docs = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
                if (mime) {
                    const ext = mime.split('/').pop();
                    if (docs.includes(ext)) return 'bi bi-file-earmark-text';
                }
                return 'bi bi-paperclip';
            }

            function setDetail({judul, tanggal, isi, attachments, readUrl}) {
                titleEl.textContent = judul || 'Pengumuman';
                dateEl.textContent = tanggal || '';
                bodyEl.innerHTML = isi ? isi.replace(/\n/g, '<br>') : '<em>Tidak ada isi pengumuman.</em>';
                attachmentEl.innerHTML = '';
                if (attachments && attachments.length) {
                    attachments.forEach(att => {
                        const a = document.createElement('a');
                        a.href = att.url;
                        a.target = '_blank';
                        a.className = 'badge rounded-pill bg-secondary text-white text-decoration-none d-flex align-items-center gap-2';
                        a.innerHTML = `<i class="${iconForMime(att.mime)}"></i> <span>${att.name}</span>`;
                        attachmentEl.appendChild(a);
                    });
                }
                listWrapper.style.display = 'none';
                detailWrapper.style.display = 'block';
                if (readUrl) {
                    readForm.action = readUrl;
                    fetch(readUrl, {method: 'POST', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}})
                        .catch(() => {});
                }
            }

            if (backBtn) {
                backBtn.addEventListener('click', () => {
                    detailWrapper.style.display = 'none';
                    listWrapper.style.display = 'block';
                });
            }

            document.querySelectorAll('.notif-item').forEach(item => {
                item.addEventListener('click', () => {
                    const judul = item.dataset.judul || 'Pengumuman';
                    const isi = item.dataset.isi || '';
                    const tanggal = item.dataset.tanggal || '';
                    const attachments = item.dataset.attachments ? JSON.parse(item.dataset.attachments) : [];
                    const id = item.dataset.id;
                    const readUrl = id ? "{{ url('notifikasi/pengumuman') }}/" + id + "/read" : null;
                    setDetail({judul, tanggal, isi, attachments, readUrl});
                });
            });
        });
    </script>
    @endpush
@endif
