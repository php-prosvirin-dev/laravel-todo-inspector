@props([
    'name',
    'id' => null,
    'options' => [],
    'selected' => null,
    'placeholder' => null,
    'class' => ''
])

@php
    $id = $id ?? $name;
@endphp

<select name="{{ $name }}" id="{{ $id }}" {{ $attributes->merge(['class' => "px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white {$class}"]) }}>
    @if($placeholder)
        <option value="">{{ $placeholder }}</option>
    @endif

    @foreach($options as $value => $label)
        <option value="{{ $value }}" {{ (string)$selected === (string)$value ? 'selected' : '' }}>
            {{ $label }}
        </option>
    @endforeach
</select>