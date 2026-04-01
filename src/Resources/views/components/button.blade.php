@props([
    'type' => 'button',
    'variant' => 'primary', // primary, secondary, success, danger
    'icon' => null,
    'href' => null,
    'onclick' => null,
    'disabled' => false
])

@php
    $variants = [
        'primary' => 'bg-blue-600 hover:bg-blue-700 text-white',
        'secondary' => 'bg-gray-500 hover:bg-gray-600 text-white',
        'success' => 'bg-green-600 hover:bg-green-700 text-white',
        'danger' => 'bg-red-600 hover:bg-red-700 text-white',
        'outline' => 'bg-transparent border border-gray-300 hover:bg-gray-100 text-gray-700',
    ];

    $classes = "inline-flex items-center px-4 py-2 rounded-lg transition-all duration-200 font-medium " . ($variants[$variant] ?? $variants['primary']);

    if ($disabled) {
        $classes .= " opacity-50 cursor-not-allowed";
    }
@endphp

@if($href)
    <a href="{{ $href }}" class="{{ $classes }}" {{ $attributes }}>
        @if($icon)<i class="fas fa-{{ $icon }}"></i>@endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" onclick="{{ $onclick }}" class="{{ $classes }}" {{ $disabled ? 'disabled' : '' }} {{ $attributes }}>
        @if($icon)<i class="fas fa-{{ $icon }}"></i>@endif
        {{ $slot }}
    </button>
@endif