@props([
    'id' => null,
    'title' => null,
    'cardClass' => 'ann-modal__card--form',
])

<div {{ $attributes->merge(['class' => 'ann-modal'])->when($id, fn($attr) => $attr->merge(['id' => $id])) }}>
    <div class="ann-modal__overlay"></div>
    <div class="ann-modal__card {{ $cardClass }}">
        <div class="ann-modal__header">
            <div class="ann-modal__heading">
                @if ($title)
                    <h2 class="ann-modal__label">{{ $title }}</h2>
                @endif
                {{ $heading ?? '' }}
            </div>
            <button type="button" class="ann-icon-btn" data-close>
                <span class="material-symbols-rounded">close</span>
            </button>
        </div>
        <div class="ann-modal__body">
            {{ $slot }}
        </div>
        @if (isset($footer))
            <div class="ann-modal__footer">
                {{ $footer }}
            </div>
        @endif
    </div>
    <div class="ann-modal__backdrop"></div>
</div>
