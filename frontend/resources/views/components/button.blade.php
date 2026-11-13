@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'disabled' => false,
])

@php
$baseClasses = 'inline-flex items-center justify-center font-medium rounded-button transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

$variants = [
    'primary' => 'bg-gradient-to-r from-gradient-button-start to-gradient-button-end text-white hover:opacity-90 focus:ring-patria-green',
    'secondary' => 'bg-white border border-patria-border text-patria-green hover:bg-patria-bg focus:ring-patria-green',
    'danger' => 'bg-patria-red text-white hover:bg-red-700 focus:ring-patria-red',
    'ghost' => 'text-patria-green hover:bg-patria-green/10 focus:ring-patria-green',
];

$sizes = [
    'sm' => 'px-3 py-1.5 text-sm',
    'md' => 'px-4 py-2 text-sm',
    'lg' => 'px-6 py-3 text-base',
];

$classes = $baseClasses . ' ' . $variants[$variant] . ' ' . $sizes[$size];
@endphp

<button type="{{ $type }}" {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>
