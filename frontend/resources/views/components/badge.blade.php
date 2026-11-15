@props([
    'variant' => 'default',
])

@php
$statuses = [
    'success' => 'bg-patria-green-light text-patria-green-text',
    'warning' => 'bg-patria-gold text-patria-text',
    'danger' => 'bg-patria-red/10 text-patria-red',
    'danger-solid' => 'bg-patria-red text-white',
    'info' => 'bg-blue-100 text-blue-800',
    'default' => 'bg-patria-bg text-patria-text-secondary',
];

$classes = 'px-2 py-1 rounded-md text-xs font-medium ' . $statuses[$variant];

if ($variant === 'warning') {
    $classes .= ' px-3.5 font-semibold min-h-7';
}
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
