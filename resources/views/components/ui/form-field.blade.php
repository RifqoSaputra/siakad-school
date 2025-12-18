@props([
    'label' => null,
    'counter' => null,
    'error' => null,
])

<label {{ $attributes->merge(['class' => 'ann-field']) }}>
    @if ($label)
        <span class="ann-field__label">
            <span>{{ $label }}</span>
            @if (!is_null($counter))
                <span class="ann-counter">{{ $counter }}</span>
            @endif
        </span>
    @endif

    {{ $slot }}

    @if ($error)
        <span class="ann-field__error" role="alert">{{ $error }}</span>
    @endif
</label>
