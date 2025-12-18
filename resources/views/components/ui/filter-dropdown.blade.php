@props([
    'label' => '',
    'value' => null,
    'filter' => null,
    'buttonId' => null,
])

@php
    $dataFilter = $filter ? ['data-filter' => $filter] : [];
    $displayLabel = $value ?? $label;
@endphp

<div {{ $attributes->merge(['class' => 'filter'])->merge($dataFilter) }}>
    <button type="button" class="filter__btn" @if($buttonId) id="{{ $buttonId }}" @endif>
        <span>{{ $displayLabel }}</span>
        <span class="material-symbols-rounded">expand_more</span>
    </button>
    <div class="filter__menu">
        {{ $slot }}
    </div>
</div>
