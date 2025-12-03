{{-- resources/views/components/dropdown.blade.php --}}
@props(['name', 'id' => null, 'onchange' => null, 'selected' => null])

{{-- 1. Markup Select Elemen --}}
<select
    name="{{ $name }}"
    id="{{ $id ?? $name }}"
    {{-- Tambahkan class inisialisasi dan class Bootstrap default (form-select) --}}
    class="form-select select2-init {{ $attributes->get('class') }}" 
    {{ $attributes->whereDoesntStartWith('class') }}
    {{-- Simpan onchange di data attribute untuk dipanggil oleh JS Select2 --}}
    @if($onchange) data-onchange="{{ $onchange }}" @endif 
>
    {{ $slot }}
</select>

{{-- 2. Style & Script Select2 (Dimuat hanya SEKALI menggunakan @once) --}}
@once
    @push('styles')
        {{-- CSS Select2 --}}
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        
        <style>
            /* CSS Kustom untuk Tampilan Select2 agar menyerupai rounded-pill */
            
            /* A. Styling Trigger Dropdown (Kotak yang terlihat) */
            .select2-container .select2-selection--single {
                height: calc(1.5em + 0.75rem + 2px); /* Menyamakan tinggi form-control */
                border-radius: 50rem !important; /* Membuat bentuk rounded-pill */
                padding-top: 0.375rem; /* Menyesuaikan padding */
                padding-bottom: 0.375rem; 
                border-color: #ced4da; /* Border default Bootstrap */
            }
            
            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 1.5; 
                padding-left: 1rem;
            }

            /* B. Styling Dropdown List (Kotak yang muncul agar rounded) */
            .select2-dropdown {
                border-radius: 0.5rem; /* Memberi rounded pada list yang terbuka */
                box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            }
            
            /* Panah */
            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 100%;
                top: 1px;
                right: 0.5rem;
            }
        </style>
    @endpush

    @push('scripts')
        {{-- JS Select2 --}}
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <script>
            $(document).ready(function() {
                // Inisialisasi Select2 pada semua elemen dengan class 'select2-init'
                $('.select2-init').select2({
                    minimumResultsForSearch: Infinity, 
                    width: '100%'
                }).on('change', function() {
                    // Memicu event onchange dari data-attribute
                    var onchange_func = $(this).data('onchange');
                    if (onchange_func) {
                        eval(onchange_func);
                    }
                });
            });
        </script>
    @endpush
@endonce