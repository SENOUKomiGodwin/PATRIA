@props([
    'title' => null,
    'value' => null,
    'subtitle' => null,
    'subtitleClass' => 'text-patria-text-secondary',
])

<x-card>
    <p class="text-[13px] text-patria-text-secondary mb-1">{{ $title }}</p>
    <p class="text-3xl font-bold text-patria-green-text">{{ $value }}</p>
    @if ($subtitle)
        <p class="text-sm {{ $subtitleClass }} mt-3 whitespace-nowrap">{{ $subtitle }}</p>
    @endif
</x-card>
