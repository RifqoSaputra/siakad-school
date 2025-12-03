{{-- resources/views/components/confirm-delete.blade.php (TAILWIND STYLED) --}}
@props([
    'id' => 'confirmModal',
    'title' => 'Konfirmasi Aksi',
    'message' => 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
    'confirmText' => 'Ya, Lanjutkan',
    'cancelText' => 'Batal',
    'confirmClass' => 'bg-red-600 hover:bg-red-700', // Default untuk Delete
    'iconClass' => 'fa-trash-alt text-red-600', // Default untuk Delete
])

<div id="{{ $id }}"
    class="fixed inset-0 z-[100] hidden overflow-y-auto bg-gray-900 bg-opacity-75 transition-opacity duration-300"
    role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-title" aria-hidden="true">
    {{-- z-[100] lebih tinggi dari z-50 modal utama --}}
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-sm mx-auto transform transition-all duration-300 scale-100"
            id="{{ $id }}-content">

            {{-- HEADER --}}
            <div class="p-4 border-b">
                <h5 class="text-xl font-bold text-gray-800" id="{{ $id }}-title">{{ $title }}</h5>
            </div>

            {{-- BODY --}}
            <div class="p-4">
                <p class="text-gray-700">
                    {!! nl2br(e($message)) !!}
                </p>
            </div>

            {{-- FOOTER --}}
            <div class="p-4 border-t flex justify-end space-x-3 bg-gray-50 rounded-b-lg">
                {{-- Tombol Batal --}}
                <button type="button" onclick="window.hideModal('{{ $id }}')" {{-- <-- TAMBAHKAN INI --}}
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    {{ $cancelText }}
                </button>

                {{-- Tombol Konfirmasi --}}
                <button type="button" id="{{ $id }}-confirm-btn"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    {{ $confirmText }}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    window.hideModal = function(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.add("hidden");
            document.body.style.overflow = "";
        }
    };

    window.showModal = function(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove("hidden");
            document.body.style.overflow = "hidden"; // mencegah scroll
        }
    };
</script>
