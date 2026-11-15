@props([
    'label' => null,
    'type' => 'text',
    'required' => false,
    'disabled' => false,
])

<div class="space-y-2">
    @if ($label)
        <label class="block text-sm font-medium text-patria-green-text">{{ $label }}</label>
    @endif
    <input
        type="{{ $type }}"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->merge(['class' => 'w-full px-4 py-2.5 border border-patria-border rounded-button focus-visible:ring-2 focus-visible:ring-patria-green focus-visible:border-patria-green outline-none transition-colors disabled:opacity-50']) }}
    >
</div>
