<style>
    @keyframes fadeSlide {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-slide {
        animation: fadeSlide 0.3s ease-out;
    }
</style>

@if (session()->has('success') || session()->has('error') || session()->has('warning') || session()->has('info') || session()->has('deleted'))
    <div class="position-fixed bottom-0 start-50 translate-middle-x mb-3 z-50" style="pointer-events: none;">
        @php
            $toastClasses = [
                'base' => 'd-inline-flex align-items-center text-white px-4 py-3 rounded-pill shadow-lg animate-fade-slide',
                'success' => 'bg-success',
                'deleted' => 'bg-secondary',
                'error' => 'bg-danger',
                'warning' => 'bg-warning text-dark',
                'info' => 'bg-primary',
            ];
        @endphp

        @if (session('success'))
            <div class="{{ $toastClasses['base'] }} {{ $toastClasses['success'] }} mb-2" style="pointer-events: auto;">
                <i class="fas fa-check-circle me-2 text-lg"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('deleted'))
            <div class="{{ $toastClasses['base'] }} {{ $toastClasses['deleted'] }} mb-2" style="pointer-events: auto;">
                <i class="fas fa-minus-circle me-2 text-lg"></i>
                <span>{{ session('deleted') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="{{ $toastClasses['base'] }} {{ $toastClasses['error'] }} mb-2" style="pointer-events: auto;">
                <i class="fas fa-times-circle me-2 text-lg"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if (session('warning'))
            <div class="{{ $toastClasses['base'] }} {{ $toastClasses['warning'] }} mb-2" style="pointer-events: auto;">
                <i class="fas fa-exclamation-triangle me-2 text-lg"></i>
                <span>{{ session('warning') }}</span>
            </div>
        @endif

        @if (session('info'))
            <div class="{{ $toastClasses['base'] }} {{ $toastClasses['info'] }} mb-2" style="pointer-events: auto;">
                <i class="fas fa-info-circle me-2 text-lg"></i>
                <span>{{ session('info') }}</span>
            </div>
        @endif
    </div>

    {{-- Auto-hide --}}
    <script>
        setTimeout(() => {
            document.querySelectorAll('.position-fixed.bottom-0.start-50.translate-middle-x.mb-3 > div').forEach(el => {
                el.style.transition = "all 0.5s ease";
                el.style.opacity = "0";
                el.style.transform = "translateY(10px)";
                setTimeout(() => el.remove(), 500);
            });
        }, 2500);
    </script>
@endif
