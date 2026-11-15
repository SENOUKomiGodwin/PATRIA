@props([
    'headers' => [],
    'variant' => 'standard',
])

<div class="patria-table patria-table-{{ $variant }} -mx-6 overflow-x-auto">
    <table class="w-full">
        <thead>
            <tr class="text-left text-sm text-patria-text-secondary bg-patria-bg border-b border-patria-border">
                @foreach ($headers as $header)
                    <th class="px-6 py-4 font-medium">{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody class="text-sm">
            {{ $slot }}
        </tbody>
    </table>
</div>
