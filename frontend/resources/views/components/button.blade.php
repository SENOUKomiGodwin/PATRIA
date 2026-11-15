@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'disabled' => false,
])

@php
$baseClasses = 'inline-flex items-center justify-center font-semibold text-sm rounded-button transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

$variants = [
    'primary' => 'bg-patria-green-button text-white hover:opacity-90 focus-visible:ring-patria-green-button disabled:bg-gray-100 disabled:text-gray-400',
    'primary-dark' => 'bg-patria-green text-white hover:opacity-90 focus-visible:ring-patria-green disabled:bg-gray-100 disabled:text-gray-400',
    'secondary' => 'bg-white border border-patria-border text-patria-green-text hover:bg-patria-bg focus-visible:ring-patria-green disabled:bg-gray-100 disabled:text-gray-400 disabled:border-gray-300',
    'danger' => 'bg-patria-red text-white hover:opacity-90 focus-visible:ring-patria-red disabled:bg-gray-100 disabled:text-gray-400',
    'ghost' => 'text-patria-green-text hover:bg-patria-green/10 focus-visible:ring-patria-green disabled:text-gray-400',
];

$sizes = [
    'xs' => 'h-9 px-3',
    'sm' => 'h-11 px-3',
    'md' => 'h-11 px-4',
    'lg' => 'h-11 px-6',
];

$classes = $baseClasses . ' ' . $variants[$variant] . ' ' . $sizes[$size];
@endphp

<button type="{{ $type }}" {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => $classes . ($disabled ? ' opacity-50 cursor-not-allowed' : '')]) }}>
    {{ $slot }}
</button>
