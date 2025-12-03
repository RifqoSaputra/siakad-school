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

@if (session()->has('success') || session()->has('error') || session()->has('warning') || session()->has('info'))
    <div class="fixed top-4 right-4 z-50">
        @if (session('success'))
            <div class="flex items-center bg-green-600 text-white px-4 py-3 rounded-lg shadow-md animate-fade-slide">
                <i class="fas fa-check-circle mr-2 text-lg"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="flex items-center bg-red-600 text-white px-4 py-3 rounded-lg shadow-md animate-fade-slide">
                <i class="fas fa-times-circle mr-2 text-lg"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if (session('warning'))
            <div class="flex items-center bg-yellow-500 text-white px-4 py-3 rounded-lg shadow-md animate-fade-slide">
                <i class="fas fa-exclamation-triangle mr-2 text-lg"></i>
                <span>{{ session('warning') }}</span>
            </div>
        @endif

        @if (session('info'))
            <div class="flex items-center bg-blue-600 text-white px-4 py-3 rounded-lg shadow-md animate-fade-slide">
                <i class="fas fa-info-circle mr-2 text-lg"></i>
                <span>{{ session('info') }}</span>
            </div>
        @endif
    </div>

    {{-- Auto-hide --}}
    <script>
        setTimeout(() => {
            document.querySelectorAll('.fixed.top-4.right-4 > div').forEach(el => {
                el.style.transition = "all 0.5s ease";
                el.style.opacity = "0";
                el.style.transform = "translateY(-10px)";
                setTimeout(() => el.remove(), 500);
            });
        }, 2500);
    </script>
@endif
