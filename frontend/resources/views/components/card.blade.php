@props([
    'padding' => 'p-6',
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-card border border-patria-border ' . $padding]) }}>
    {{ $slot }}
</div>
