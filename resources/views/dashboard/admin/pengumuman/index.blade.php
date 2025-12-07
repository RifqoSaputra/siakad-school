@extends('layouts.template')

@section('title', 'Pengumuman')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-1">Daftar Pengumuman</h4>
                <p class="text-muted mb-0">Kelola pengumuman sekolah dan kirim notifikasi ke pengguna sesuai role.</p>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#pengumumanModal">
                <i class="bi bi-plus-circle"></i> Pengumuman Baru
            </button>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Target</th>
                                <th>Status</th>
                                <th>Terakhir Diubah</th>
                                <th>Notifikasi</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pengumuman as $item)
                                <tr>
                                    <td class="fw-semibold">{{ $item->judul }}</td>
                                    <td>
                                        @php
                                            $label = [
                                                'admin' => 'Admin',
                                                'guru' => 'Guru',
                                                'ortu' => 'Orang Tua',
                                                'all' => 'Semua',
                                            ][$item->target_role] ?? $item->target_role;
                                        @endphp
                                        <span class="badge bg-info text-dark text-uppercase">{{ $label }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $item->status === 'draft' ? 'bg-secondary' : 'bg-success' }}">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div>{{ optional($item->updated_at)->format('d M Y H:i') }}</div>
                                        <small class="text-muted">Terbit: {{ optional($item->created_at)->format('d M Y H:i') }}</small>
                                    </td>
                                    <td>{{ $item->notifications_count ?? 0 }} pengguna</td>
                                    <td class="text-end">
                                        @php
                                            $attachPayload = $item->attachments->map(function ($att) {
                                                return [
                                                    'id' => $att->id,
                                                    'name' => $att->nama_file,
                                                    'mime' => $att->mime_type,
                                                ];
                                            })->values();
                                        @endphp
                                        <button type="button"
                                            class="btn btn-sm btn-outline-secondary edit-pengumuman-btn"
                                            data-id="{{ $item->id_pengumuman }}"
                                            data-judul="{{ $item->judul }}"
                                            data-target="{{ $item->target_role }}"
                                            data-isi="{{ $item->isi_pengumuman }}"
                                            data-status="{{ $item->status }}"
                                            data-attachments='@json($attachPayload)'>
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>
                                        <form action="{{ route('admin.pengumuman.destroy', $item->id_pengumuman) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Hapus pengumuman ini? Semua notifikasi terkait akan ikut terhapus.');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Belum ada pengumuman.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $pengumuman->links() }}
                </div>
            </div>
        </div>
    </div>
    {{-- Modal Buat Pengumuman --}}
    <div class="modal fade" id="pengumumanModal" tabindex="-1" aria-labelledby="pengumumanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pengumumanModalLabel">Pengumuman Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="pengumumanForm" action="{{ route('admin.pengumuman.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_method" value="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Target Role</label>
                            <select name="target_role" class="form-select" required>
                                <option value="admin">Admin</option>
                                <option value="guru">Guru</option>
                                <option value="ortu">Orang Tua</option>
                                <option value="all" selected>Semua Role</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Judul</label>
                            <input type="text" name="judul" class="form-control" maxlength="150" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Isi Pengumuman</label>
                            <textarea name="isi_pengumuman" rows="5" class="form-control" placeholder="Tulis isi pengumuman..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Lampiran (dokumen/gambar, maks 10MB per file)</label>
                            <input type="file" name="lampiran[]" class="form-control" id="lampiranInput"
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif" multiple>
                            <div class="d-flex flex-wrap gap-2 mt-2" id="lampiranPreview"></div>
                            <div class="d-flex flex-wrap gap-2 mt-2" id="existingAttachments"></div>
                        </div>
                        <div class="form-text text-muted">Pilih "Simpan Draft" untuk menyimpan tanpa mengirim notifikasi.</div>
                        <input type="hidden" name="status" value="published">
                        <div id="removeAttachmentsContainer"></div>
                    </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success" onclick="this.form.status.value='draft'">Simpan Draft</button>
                            <button type="submit" class="btn btn-primary" onclick="this.form.status.value='published'">Terbitkan & Kirim</button>
                        </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = new bootstrap.Modal(document.getElementById('pengumumanModal'));
        const form = document.getElementById('pengumumanForm');
        const methodInput = form.querySelector('input[name="_method"]');
        const titleEl = form.querySelector('input[name="judul"]');
        const targetEl = form.querySelector('select[name="target_role"]');
        const isiEl = form.querySelector('textarea[name="isi_pengumuman"]');
        const modalTitle = document.getElementById('pengumumanModalLabel');
        const lampiranInput = document.getElementById('lampiranInput');
        const lampiranPreview = document.getElementById('lampiranPreview');
        const existingAttachments = document.getElementById('existingAttachments');
        const removeAttachmentsContainer = document.getElementById('removeAttachmentsContainer');

        function iconForMime(mime, name) {
            if (mime && mime.startsWith('image/')) return 'bi bi-image-fill';
            const ext = name.split('.').pop().toLowerCase();
            const docExt = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
            if (docExt.includes(ext)) return 'bi bi-file-earmark-text-fill';
            return 'bi bi-paperclip';
        }

        function renderLampiranPreview(files) {
            lampiranPreview.innerHTML = '';
            Array.from(files).forEach((file, idx) => {
                const badge = document.createElement('span');
                badge.className = 'badge rounded-pill bg-light text-dark d-flex align-items-center gap-2 border';
                badge.innerHTML = `<i class="${iconForMime(file.type, file.name)}"></i> <span>${file.name}</span>
                    <button type="button" class="btn btn-sm btn-link text-danger p-0" data-remove="${idx}">
                        <i class="bi bi-x-lg"></i>
                    </button>`;
                lampiranPreview.appendChild(badge);
            });
        }

        function renderExistingAttachments(items) {
            existingAttachments.innerHTML = '';
            items.forEach(att => {
                const badge = document.createElement('span');
                badge.className = 'badge rounded-pill bg-light text-dark d-flex align-items-center gap-2 border';
                badge.innerHTML = `<i class="${iconForMime(att.mime, att.name)}"></i> <span>${att.name}</span>
                    <button type="button" class="btn btn-sm btn-link text-danger p-0" data-remove-existing="${att.id}">
                        <i class="bi bi-x-lg"></i>
                    </button>`;
                existingAttachments.appendChild(badge);
            });
        }

        lampiranPreview.addEventListener('click', (e) => {
            if (e.target.closest('button[data-remove]')) {
                const idx = parseInt(e.target.closest('button[data-remove]').dataset.remove, 10);
                const dt = new DataTransfer();
                Array.from(lampiranInput.files).forEach((file, i) => {
                    if (i !== idx) dt.items.add(file);
                });
                lampiranInput.files = dt.files;
                renderLampiranPreview(dt.files);
            }
        });

        lampiranInput.addEventListener('change', () => {
            renderLampiranPreview(lampiranInput.files);
        });

        existingAttachments.addEventListener('click', (e) => {
            if (e.target.closest('button[data-remove-existing]')) {
                const id = e.target.closest('button[data-remove-existing]').dataset.removeExisting;
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'remove_attachments[]';
                input.value = id;
                removeAttachmentsContainer.appendChild(input);
                e.target.closest('span.badge').remove();
            }
        });

        // Reset ke mode create
        function setCreateMode() {
            form.action = "{{ route('admin.pengumuman.store') }}";
            methodInput.value = 'POST';
            modalTitle.textContent = 'Pengumuman Baru';
            titleEl.value = '';
            targetEl.value = 'all';
            isiEl.value = '';
            form.status.value = 'published';
            lampiranInput.value = '';
            lampiranPreview.innerHTML = '';
            existingAttachments.innerHTML = '';
            removeAttachmentsContainer.innerHTML = '';
        }

        // Tombol Pengumuman Baru
        document.querySelector('[data-bs-target="#pengumumanModal"]').addEventListener('click', () => {
            setCreateMode();
        });

        // Tombol Edit
        document.querySelectorAll('.edit-pengumuman-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                const judul = btn.dataset.judul || '';
                const target = btn.dataset.target || 'all';
                const isi = btn.dataset.isi || '';
                const status = btn.dataset.status || 'published';
                const attachments = btn.dataset.attachments ? JSON.parse(btn.dataset.attachments) : [];

                form.action = "{{ url('admin/pengumuman') }}/" + id;
                methodInput.value = 'PUT';
                modalTitle.textContent = 'Edit Pengumuman';
                titleEl.value = judul;
                targetEl.value = target;
                isiEl.value = isi;
                form.status.value = status;
                lampiranInput.value = '';
                lampiranPreview.innerHTML = '';
                renderExistingAttachments(attachments);
                removeAttachmentsContainer.innerHTML = '';

                modal.show();
            });
        });
    });
</script>
@endpush
