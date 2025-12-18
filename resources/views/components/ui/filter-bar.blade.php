@props([
    /**
     * Optional actions slot on the right (e.g., reset button or primary action).
     */
    'actions' => null,
])

<div {{ $attributes->merge(['class' => 'filters']) }}>
    {{ $slot }}
    @if ($actions)
        <div class="filters__actions">
            {{ $actions }}
        </div>
    @endif
</div>
