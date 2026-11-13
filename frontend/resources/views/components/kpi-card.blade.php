@props([
    'title' => null,
    'value' => null,
    'subtitle' => null,
    'subtitleClass' => 'text-patria-text-secondary',
    'icon' => null,
])

<x-card>
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-patria-text-secondary mb-1">{{ $title }}</p>
            <p class="text-3xl font-bold text-patria-green">{{ $value }}</p>
            @if ($subtitle)
                <p class="text-sm {{ $subtitleClass }} mt-1">{{ $subtitle }}</p>
            @endif
        </div>
        @if ($icon)
            <div class="w-12 h-12 bg-patria-green/10 rounded-lg flex items-center justify-center">
                {{ $icon }}
            </div>
        @endif
    </div>
</x-card>
