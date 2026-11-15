@props([
    'label' => null,
    'required' => false,
    'bgClass' => 'bg-white',
    'widthClass' => 'w-full',
    'selectedClass' => 'text-patria-text',
])

<div class="space-y-2 relative" x-data="{ hasValue: false, updateSelect() { this.hasValue = $el.value !== ''; $el.classList.remove('text-patria-text', 'text-patria-text-secondary', '{{ $selectedClass }}'); $el.classList.add(this.hasValue ? '{{ $selectedClass }}' : 'text-patria-text-secondary'); } }">
    @if ($label)
        <label class="block text-sm font-medium text-patria-green-text">{{ $label }}</label>
    @endif
    <select
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => $widthClass . ' px-4 py-2.5 border border-patria-border rounded-button appearance-none ' . $bgClass . ' focus-visible:ring-2 focus-visible:ring-patria-green focus-visible:border-patria-green outline-none transition-colors cursor-pointer pr-10 text-patria-text-secondary', '@change' => 'updateSelect()', 'x-init' => 'updateSelect()']) }}
    >
        {{ $slot }}
    </select>
    <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-patria-text-secondary pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7"/>
    </svg>
</div>
