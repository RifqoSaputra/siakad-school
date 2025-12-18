@props([
    'value' => '',
    'active' => false,
])

<button type="button" class="filter__option" data-value="{{ $value }}">
    <span class="radio {{ $active ? 'active' : '' }}"></span>
    <span>{{ $slot }}</span>
</button>
