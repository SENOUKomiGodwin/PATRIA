@props([
    'label' => null,
    'required' => false,
])

<div class="space-y-2">
    @if ($label)
        <label class="block text-sm font-medium text-patria-green">{{ $label }}</label>
    @endif
    <select
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'w-full px-4 py-2.5 border border-patria-border rounded-button focus:ring-2 focus:ring-patria-green focus:border-patria-green outline-none transition-colors']) }}
    >
        {{ $slot }}
    </select>
</div>
