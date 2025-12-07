@extends('layouts.template')

@section('title', 'Edit Pengumuman')

@section('content')
    <div class="container-fluid">
        <h4 class="mb-3">Edit Pengumuman</h4>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('admin.pengumuman.update', $pengumuman->id_pengumuman) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Target Role</label>
                        <select name="target_role" class="form-select" required>
                            <option value="admin" {{ old('target_role', $pengumuman->target_role) === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="guru" {{ old('target_role', $pengumuman->target_role) === 'guru' ? 'selected' : '' }}>Guru</option>
                            <option value="ortu" {{ old('target_role', $pengumuman->target_role) === 'ortu' ? 'selected' : '' }}>Orang Tua</option>
                            <option value="all" {{ old('target_role', $pengumuman->target_role) === 'all' ? 'selected' : '' }}>Semua Role</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Judul</label>
                        <input type="text" name="judul" class="form-control" maxlength="150"
                            value="{{ old('judul', $pengumuman->judul) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Isi Pengumuman</label>
                        <textarea name="isi_pengumuman" rows="5" class="form-control" placeholder="Tulis isi pengumuman...">{{ old('isi_pengumuman', $pengumuman->isi_pengumuman) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Lampiran (dokumen/gambar, maks 10MB per file)</label>
                        <input type="file" name="lampiran[]" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif" multiple>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold d-block">Status</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" id="statusDraft" value="draft"
                                {{ old('status', $pengumuman->status) === 'draft' ? 'checked' : '' }}>
                            <label class="form-check-label" for="statusDraft">Draft (tanpa notifikasi)</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" id="statusPublished" value="published"
                                {{ old('status', $pengumuman->status) === 'published' ? 'checked' : '' }}>
                            <label class="form-check-label" for="statusPublished">Terbitkan & kirim notifikasi</label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.pengumuman.index') }}" class="btn btn-light">Kembali</a>
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
