@props([
    'title' => null,
    'show' => false,
])

<div x-data="{ isOpen: {{ $show ? 'true' : 'false' }} }" x-show="isOpen" class="fixed inset-0 bg-patria-green/30 flex items-center justify-center z-50" style="display: none;">
    <div class="bg-white rounded-card border border-patria-border p-6 w-full max-w-3xl mx-4" @click.away="isOpen = false">
        @if ($title)
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-base font-semibold text-patria-text">{{ $title }}</h3>
                <button @click="isOpen = false" class="text-patria-text-secondary hover:text-patria-text">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        @endif
        {{ $slot }}
    </div>
</div>
