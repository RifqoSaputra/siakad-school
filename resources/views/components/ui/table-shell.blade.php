@props([
    'head' => null,
])

<div {{ $attributes->merge(['class' => 'ann-card ann-card--table']) }}>
    <div class="ann-table">
        <div class="ann-table__head">
            {{ $head ?? '' }}
        </div>
        <div class="ann-table__body">
            {{ $slot }}
        </div>
    </div>
</div>
