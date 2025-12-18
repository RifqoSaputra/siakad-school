@props([
    'status' => 'sent', // sent|scheduled|draft|published
    'icon' => null,
    'label' => null,
])

@php
    $map = [
        'sent' => ['class' => 'ann-status--sent', 'icon' => 'check_circle', 'label' => 'Diumumkan'],
        'published' => ['class' => 'ann-status--sent', 'icon' => 'check_circle', 'label' => 'Diumumkan'],
        'scheduled' => ['class' => 'ann-status--scheduled', 'icon' => 'schedule', 'label' => 'Dijadwalkan'],
        'draft' => ['class' => 'ann-status--draft', 'icon' => 'description', 'label' => 'Draf'],
    ];
    $conf = $map[$status] ?? $map['sent'];
    $iconName = $icon ?? $conf['icon'];
    $text = $label ?? $conf['label'];
@endphp

<span {{ $attributes->merge(['class' => 'ann-status ' . $conf['class']]) }}>
    <span class="material-symbols-rounded">{{ $iconName }}</span>
    {{ $text }}
</span>
