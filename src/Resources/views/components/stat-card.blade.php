@props([
    'label',
    'value',
    'icon' => null,
    'href' => null,
    'color' => 'blue',
    'badge' => null
])

@php
    $colors = [
        'blue' => 'bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 text-blue-600 dark:text-blue-400',
        'gray' => 'bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700',
        'red' => 'bg-red-50 dark:bg-red-900/30 hover:bg-red-100 dark:hover:bg-red-900/50',
        'orange' => 'bg-orange-50 dark:bg-orange-900/30 hover:bg-orange-100 dark:hover:bg-orange-900/50',
        'green' => 'bg-green-50 dark:bg-green-900/30 hover:bg-green-100 dark:hover:bg-green-900/50',
    ];
@endphp

<a href="{{ $href ?? '#' }}" class="rounded-lg p-4 text-center transition cursor-pointer block {{ $colors[$color] ?? $colors['gray'] }}">
    <div class="text-2xl font-bold">{{ $value }}</div>
    <div class="text-sm text-gray-600 dark:text-gray-400 flex items-center justify-center gap-1 flex-wrap">
        @if($icon)<span>{{ $icon }}</span>@endif
        <span>{{ $label }}</span>
        @if($badge)<span class="badge badge-{{ $badge }}">{{ $badge }}</span>@endif
    </div>
</a>