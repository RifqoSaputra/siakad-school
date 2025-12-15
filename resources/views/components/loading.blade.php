{{-- Global Fullscreen Loading Overlay (Sudah Diperbaiki) --}}
<div id="global-loading"
    class="fixed inset-0 z-99999 bg-gray-800 bg-opacity-50 backdrop-blur-sm flex items-center justify-center"
    style="display: none;"> {{-- Hapus 'hidden', ganti dengan inline style --}}

    <div class="flex flex-col items-center space-y-3">
        <div class="w-12 h-12 border-4 border-white border-t-transparent rounded-full animate-spin"></div>
        <p class="text-white text-sm font-semibold">Loading...</p>
    </div>

</div>
