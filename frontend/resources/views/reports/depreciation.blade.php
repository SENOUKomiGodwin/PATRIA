@extends('components.layout-app')

@section('title', "Tableau d’amortissement")

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-patria-green-text">Tableau d’amortissement</h1>
            <p class="text-sm text-patria-text-secondary mt-1">Suivez la dépréciation du patrimoine et sa valeur nette comptable.</p>
        </div>
        <x-button variant="secondary">Exporter en Excel</x-button>
    </div>

    <!-- Filters Card -->
    <x-card>
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="text-sm text-patria-text-secondary">Exercice comptable</span>
                <x-select bgClass="bg-patria-bg" widthClass="w-36" selectedClass="text-patria-green-text">
                    <option>2026</option>
                </x-select>
            </div>
            <p class="text-sm text-patria-text-secondary">Méthode linéaire · Montants en millions de FCFA</p>
        </div>
    </x-card>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-kpi-card title="Dotation de l’année" value="1 096 M" />
        <x-kpi-card title="Amortissement cumulé" value="3 744 M" />
        <x-kpi-card title="Valeur nette comptable" value="8 736 M" />
    </div>

    <!-- Table Card -->
    <x-card>
        <x-table variant="compact" :headers="['Catégorie', 'Acquisition', 'Dotation 2026', 'Cumul', 'Valeur nette']">
            <tr class="border-b border-patria-border">
                <td class="px-6 py-4 text-patria-green-text">Immobilier</td>
                <td class="px-6 py-4">8 000</td>
                <td class="px-6 py-4">200</td>
                <td class="px-6 py-4">1 600</td>
                <td class="px-6 py-4">6 400</td>
            </tr>
            <tr class="border-b border-patria-border">
                <td class="px-6 py-4 text-patria-green-text">Véhicules</td>
                <td class="px-6 py-4">2 200</td>
                <td class="px-6 py-4">440</td>
                <td class="px-6 py-4">1 100</td>
                <td class="px-6 py-4">1 100</td>
            </tr>
            <tr class="border-b border-patria-border">
                <td class="px-6 py-4 text-patria-green-text">Informatique</td>
                <td class="px-6 py-4">1 200</td>
                <td class="px-6 py-4">240</td>
                <td class="px-6 py-4">600</td>
                <td class="px-6 py-4">600</td>
            </tr>
            <tr class="border-b border-patria-border">
                <td class="px-6 py-4 text-patria-green-text">Mobilier</td>
                <td class="px-6 py-4">600</td>
                <td class="px-6 py-4">120</td>
                <td class="px-6 py-4">300</td>
                <td class="px-6 py-4">300</td>
            </tr>
            <tr class="border-b border-patria-border">
                <td class="px-6 py-4 text-patria-green-text">Équipements</td>
                <td class="px-6 py-4">480</td>
                <td class="px-6 py-4">96</td>
                <td class="px-6 py-4">144</td>
                <td class="px-6 py-4">336</td>
            </tr>
            <tr class="bg-patria-green-light">
                <td class="px-6 py-4 text-patria-green-text font-bold">Total</td>
                <td class="px-6 py-4 text-patria-green-text font-bold">12 480</td>
                <td class="px-6 py-4 text-patria-green-text font-bold">1 096</td>
                <td class="px-6 py-4 text-patria-green-text font-bold">3 744</td>
                <td class="px-6 py-4 text-patria-green-text font-bold">8 736</td>
            </tr>
        </x-table>
    </x-card>

    <!-- Chart Card -->
    <x-card>
        <div class="grid grid-cols-3 gap-6 items-center h-40">
            <div class="col-span-1">
                <h3 class="text-base font-semibold text-patria-green-text">Évolution de la VNC</h3>
                <p class="text-sm text-patria-text-secondary mt-1">Milliards de FCFA</p>
            </div>
            <div class="col-span-2 h-full flex items-end">
                <div class="flex items-end justify-between w-full gap-6">
                    <!-- 2023 -->
                    <div class="flex flex-col items-center flex-1">
                        <div class="text-patria-green-text font-bold text-sm mb-1">10,820</div>
                        <div class="w-28 bg-donut-2 rounded-t-md" style="height: 100px;"></div>
                        <div class="text-sm text-patria-text-secondary mt-2">2023</div>
                    </div>
                    <!-- 2024 -->
                    <div class="flex flex-col items-center flex-1">
                        <div class="text-patria-green-text font-bold text-sm mb-1">10,210</div>
                        <div class="w-28 bg-donut-2 rounded-t-md" style="height: 96px;"></div>
                        <div class="text-sm text-patria-text-secondary mt-2">2024</div>
                    </div>
                    <!-- 2025 -->
                    <div class="flex flex-col items-center flex-1">
                        <div class="text-patria-green-text font-bold text-sm mb-1">9,832</div>
                        <div class="w-28 bg-donut-2 rounded-t-md" style="height: 92px;"></div>
                        <div class="text-sm text-patria-text-secondary mt-2">2025</div>
                    </div>
                    <!-- 2026 -->
                    <div class="flex flex-col items-center flex-1">
                        <div class="text-patria-green-text font-bold text-sm mb-1">8,736</div>
                        <div class="w-28 bg-patria-green-interface rounded-t-md" style="height: 82px;"></div>
                        <div class="text-sm text-patria-text-secondary mt-2">2026</div>
                    </div>
                </div>
            </div>
        </div>
    </x-card>
</div>
@endsection
