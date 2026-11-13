@props([
    'headers' => [],
])

<div class="overflow-x-auto">
    <table class="w-full">
        <thead>
            <tr class="text-left text-sm text-patria-text-secondary border-b border-patria-border bg-patria-bg">
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
