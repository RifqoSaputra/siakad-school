@props([
    'id' => 'add-task-modal',
    'title' => 'Tambah Tugas Penilaian Harian', // Diperbarui agar lebih spesifik
    'formAction' => null,
    'kelasTarget' => 'XI B', // Pastikan ini sudah termasuk Tingkat di Controller
    'mapel' => 'Bahasa Inggris', // Untuk Display
    'currentKelasId' => null, // ID Kelas
    'currentMapelId' => null, // ID Mapel
    'guruMapelIdTarget' => null, // ID GuruMapel
])

<div id="{{ $id }}"
    class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-75 transition-opacity"
    aria-labelledby="{{ $id }}-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen p-4">
        @if (session('success'))
            <div id="toast-success"
                class="fixed top-5 right-5 bg-green-600 text-white px-4 py-3 rounded-lg shadow-lg animate-slide-in z-[9999]">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            </div>

            <script>
                setTimeout(() => {
                    const toast = document.getElementById('toast-success');
                    if (toast) toast.classList.add('opacity-0', 'transition', 'duration-500');
                }, 2500);
            </script>

            <style>
                @keyframes slideIn {
                    from {
                        transform: translateX(50px);
                        opacity: 0;
                    }

                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }

                .animate-slide-in {
                    animation: slideIn .4s ease-out;
                }
            </style>
        @endif

        {{-- Konten Modal --}}
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg transform transition-all scale-95 duration-300"
            role="document">

            {{-- Header Modal --}}
            <div class="flex justify-between items-center p-6 border-b border-gray-200">
                <h3 class="text-2xl font-extrabold text-indigo-700" id="{{ $id }}-title">
                    <i class="fas fa-plus-circle mr-2"></i> {{ $title }}
                </h3>
                <button type="button" onclick="hideModal('{{ $id }}')"
                    class="text-gray-400 hover:text-gray-600 transition duration-150" title="Tutup">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>

            {{-- Body Modal (Form) --}}
            <form action="{{ $formAction ?? route('guru.nilai.harian.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-5">

                    {{-- HIDDEN FIELDS YANG DIBUTUHKAN CONTROLLER --}}
                    {{-- Ganti id_jadwal menjadi guru_mapel_id, kelas_id, dan mapel_id --}}
                    <input type="hidden" name="guru_mapel_id" value="{{ $guruMapelIdTarget }}">
                    <input type="hidden" name="kelas_id" value="{{ $currentKelasId }}">
                    <input type="hidden" name="mapel_id" value="{{ $currentMapelId }}">

                    {{-- Target Kelas & Mata Pelajaran (Locked/Readonly) --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="kelas_target_display" class="block text-sm font-bold text-gray-700 mb-1">Kelas
                                Target</label>
                            <input type="text" id="kelas_target_display" value="{{ $kelasTarget }}" readonly
                                class="mt-1 block w-full px-4 py-2 bg-gray-100 text-gray-600 border border-gray-300 rounded-lg shadow-inner cursor-not-allowed sm:text-sm"
                                placeholder="{{ $kelasTarget }}">
                            <p class="mt-1 text-xs text-indigo-600">Otomatis di-lock sesuai filter index.</p>
                        </div>
                        <div>
                            <label for="mata_pelajaran_display" class="block text-sm font-bold text-gray-700 mb-1">Mata
                                Pelajaran</label>
                            <input type="text" id="mata_pelajaran_display" value="{{ $mapel }}" readonly
                                class="mt-1 block w-full px-4 py-2 bg-gray-100 text-gray-600 border border-gray-300 rounded-lg shadow-inner cursor-not-allowed sm:text-sm">
                        </div>
                    </div>

                    {{-- Tanggal Dibuat (Locked/Readonly) --}}
                    <div>
                        <label for="tgl_dibuat" class="block text-sm font-bold text-gray-700 mb-1">Tanggal
                            Dibuat</label>
                        <input type="text" id="tgl_dibuat" name="tgl_entry" value="{{ now()->format('Y-m-d') }}"
                            readonly
                            class="mt-1 block w-full px-4 py-2 bg-gray-100 text-gray-600 border border-gray-300 rounded-lg shadow-inner cursor-not-allowed sm:text-sm"
                            placeholder="{{ now()->format('Y-m-d') }}">
                        <p class="mt-1 text-xs text-indigo-600">Otomatis diset ke tanggal hari ini.</p>
                    </div>

                    {{-- Jenis Penilaian (name diganti menjadi tipe_penunjang) --}}
                    <div>
                        <label for="tipe_penunjang" class="block text-sm font-bold text-gray-700 mb-1">Nama Tugas /
                            Penilaian
                            <span class="text-red-500">*</span></label>
                        <input type="text" id="tipe_penunjang" name="tipe_penunjang" required
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="Contoh: Tugas 1, Ulangan Harian Bab 3" maxlength="50">
                        <p class="mt-1 text-xs text-gray-500">Maksimal 50 karakter.</p>
                    </div>

                    {{-- Deskripsi/Keterangan (Opsional) --}}
                    <div>
                        <label for="deskripsi" class="block text-sm font-bold text-gray-700 mb-1">Deskripsi/Keterangan
                            Tambahan (Opsional)</label>
                        <textarea id="deskripsi" name="deskripsi" rows="3"
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="Tulis detail materi atau kompetensi dasar yang diuji..."></textarea>
                    </div>
                </div>

                {{-- Footer Modal (Tombol Simpan/Batal) --}}
                <div
                    class="flex justify-end items-center p-6 bg-gray-50 border-t border-gray-200 space-x-3 rounded-b-xl">
                    <button type="button" onclick="hideModal('{{ $id }}')"
                        class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-100 transition duration-150">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-6 py-2 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 transition duration-150">
                        <i class="fas fa-save mr-2"></i> Buat Tugas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Script JS untuk kontrol modal generik tetap --}}
<script>
    // ... (Script JS yang sudah ada) ...
    window.showModal = function(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Mencegah scrolling body
            // Animasi masuk (jika diperlukan)
            const modalContent = modal.querySelector('.max-w-lg'); // Sesuaikan dengan ukuran modal
            if (modalContent) {
                modalContent.classList.remove('scale-95');
                modalContent.classList.add('scale-100');
            }
        }
    };

    // Fungsi untuk menyembunyikan modal
    window.hideModal = function(id) {
        const modal = document.getElementById(id);
        if (modal) {
            // Animasi keluar
            const modalContent = modal.querySelector('.max-w-lg');
            if (modalContent) {
                modalContent.classList.remove('scale-100');
                modalContent.classList.add('scale-95');
            }

            // Beri sedikit jeda untuk animasi keluar
            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }, 300);
        }
    };

    // Menutup modal ketika klik di luar area modal
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('{{ $id }}');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    window.hideModal('{{ $id }}');
                }
            });
        }
    });
    
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.querySelector(`#{{ $id }} form`);
        if (!form) return;

        form.addEventListener("submit", function() {
            const btn = form.querySelector("button[type=submit]");
            btn.disabled = true;
            btn.innerHTML = `
            <span class="flex items-center gap-2">
                <i class="fas fa-spinner fa-spin"></i> Menyimpan...
            </span>
        `;
        });
    });
</script>
