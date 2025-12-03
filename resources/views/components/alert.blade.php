@props([
    'id' => 'generalAlertModal', // ID unik untuk modal
    'title' => 'Peringatan!',
    'message' => 'Terdapat kesalahan atau informasi penting.',
    'buttonText' => 'Tutup',
    'type' => 'danger', // Default diubah ke danger untuk fokus pada error absensi
])

@php
    // Definisikan kelas ikon besar dan warna utama
    $iconClass =
        [
            'warning' => 'fas fa-exclamation-triangle text-warning',
            'danger' => 'fas fa-times-circle text-danger',
            'success' => 'fas fa-check-circle text-success',
            'info' => 'fas fa-info-circle text-info',
        ][$type] ?? 'fas fa-times-circle text-danger';

    $headerClass =
        [
            'warning' => 'bg-warning text-dark',
            'danger' => 'bg-danger text-white',
            'success' => 'bg-success text-white',
            'info' => 'bg-info text-white',
        ][$type] ?? 'bg-danger text-white';
@endphp

{{-- Tambahkan animasi fade --}}
<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true"
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            {{-- Header (Dibuat minimalis, ikon besar ada di body) --}}
            <div class="modal-header {{ $headerClass }} py-2">
                <h5 class="modal-title h6 m-0" id="{{ $id }}Label">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ $title }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <div class="modal-body text-center">
                {{-- Ikon Besar dengan animasi popup --}}
                <div class="mb-3 animate__animated animate__tada">
                    {{-- Perlu memasukkan library animate.css di layout utama Anda agar animasi ini berjalan --}}
                    <i class="{{ $iconClass }}" style="font-size: 4rem;"></i>
                </div>

                {{-- Pesan utama --}}
                <p class="m-0 fw-bold fs-5" id="{{ $id }}-message">{{ $message }}</p>

                {{-- Placeholder untuk list poin peringatan (diisi oleh JavaScript) --}}
                <div class="text-start mt-3 mx-4 p-2 border rounded bg-light"
                    style="max-height: 150px; overflow-y: auto;">
                    <p class="small text-muted mb-1">Daftar Siswa:</p>
                    <ul id="{{ $id }}-list" class="list-unstyled small m-0 p-0">
                        {{-- List Siswa akan diisi di sini oleh JS --}}
                    </ul>
                </div>
            </div>

            <div class="modal-footer py-2 d-flex justify-content-center">
                {{-- Tombol Tutup --}}
                <button type="button" class="btn btn-sm btn-secondary" id="{{ $id }}-close-btn"
                    data-bs-dismiss="modal">{{ $buttonText }}</button>
            </div>
        </div>
    </div>
</div>
