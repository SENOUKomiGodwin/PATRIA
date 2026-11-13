@props([
    'status' => 'default',
])

@php
$statuses = [
    'success' => 'bg-green-100 text-green-800',
    'warning' => 'bg-patria-gold/20 text-patria-gold',
    'danger' => 'bg-patria-red/10 text-patria-red',
    'info' => 'bg-blue-100 text-blue-800',
    'default' => 'bg-gray-100 text-gray-800',
];

$classes = 'px-2 py-1 rounded-full text-xs font-medium ' . $statuses[$status];
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
