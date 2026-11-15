@extends('components.layout-app')

@section('title', 'Tableau de bord')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-patria-green-text">Tableau de bord</h1>
            <p class="text-sm text-patria-text-secondary mt-1">Une vue d’ensemble de votre patrimoine et des actions à suivre.</p>
        </div>
        <x-button variant="secondary">Exporter le rapport</x-button>
    </div>

    <!-- Filters and date -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <span class="px-3 py-1 bg-patria-green-text/10 text-patria-green-text rounded-md text-sm font-medium">Toutes les structures</span>
        </div>
        <p class="text-[11px] text-patria-text-secondary">Mis à jour le 11 novembre 2026 à 09:42 · Données de démonstration</p>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <x-kpi-card
            title="Nombre de biens"
            value="1 248"
            subtitle="+ 24 biens ce mois"
            subtitle-class="text-patria-green-text"
        />

        <x-kpi-card
            title="Valeur d’acquisition"
            value="12,48 Md"
            subtitle="FCFA · coût historique"
        />

        <x-kpi-card
            title="Valeur nette comptable"
            value="8,736 Md"
            subtitle="FCFA · après amortissement"
        />

        <x-kpi-card
            title="Biens en maintenance"
            value="68"
            subtitle="5,4 % du parc"
            subtitle-class="text-patria-red"
        />
    </div>

    <!-- Charts row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Category distribution -->
        <x-card>
            <h3 class="text-base font-semibold text-patria-green-text mb-1">Répartition par catégorie</h3>
            <p class="text-xs text-patria-text-secondary mb-6">1 248 biens enregistrés</p>
            <div class="flex items-center gap-8">
                <!-- Donut chart -->
                <div class="relative w-40 h-40">
                    <svg class="w-full h-full transform -rotate-90">
                        <circle cx="80" cy="80" r="60" style="stroke: var(--color-patria-border)" stroke-width="20" fill="none"/>
                        <!-- 5 segments -->
                        <circle cx="80" cy="80" r="60" style="stroke: var(--color-patria-green-text)" stroke-width="20" fill="none"
                            stroke-dasharray="150.8 226.2" stroke-linecap="round"/>
                        <circle cx="80" cy="80" r="60" style="stroke: var(--color-donut-2)" stroke-width="20" fill="none"
                            stroke-dasharray="123.1 254.0" stroke-dashoffset="-150.8" stroke-linecap="round"/>
                        <circle cx="80" cy="80" r="60" style="stroke: var(--color-patria-green-interface)" stroke-width="20" fill="none"
                            stroke-dasharray="80.1 297.0" stroke-dashoffset="-273.9" stroke-linecap="round"/>
                        <circle cx="80" cy="80" r="60" style="stroke: var(--color-donut-3)" stroke-width="20" fill="none"
                            stroke-dasharray="54.4 323.6" stroke-dashoffset="-354.0" stroke-linecap="round"/>
                        <circle cx="80" cy="80" r="60" style="stroke: var(--color-patria-gold)" stroke-width="20" fill="none"
                            stroke-dasharray="40.7 337.4" stroke-dashoffset="-408.4" stroke-linecap="round"/>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-2xl font-bold text-patria-green-text">1 248</span>
                        <span class="text-sm text-patria-text-secondary">biens</span>
                    </div>
                </div>

                <!-- Legend -->
                <div class="flex-1 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-patria-green-text"></div>
                            <span class="text-[13px] text-patria-text-secondary">Mobilier</span>
                        </div>
                        <span class="text-[13px] font-bold text-patria-green-text">412</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-donut-2"></div>
                            <span class="text-[13px] text-patria-text-secondary">Informatique</span>
                        </div>
                        <span class="text-[13px] font-bold text-patria-green-text">336</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-patria-green-interface"></div>
                            <span class="text-[13px] text-patria-text-secondary">Équipements</span>
                        </div>
                        <span class="text-[13px] font-bold text-patria-green-text">218</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-donut-3"></div>
                            <span class="text-[13px] text-patria-text-secondary">Véhicules</span>
                        </div>
                        <span class="text-[13px] font-bold text-patria-green-text">176</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-patria-gold"></div>
                            <span class="text-[13px] text-patria-text-secondary">Immobilier</span>
                        </div>
                        <span class="text-[13px] font-bold text-patria-green-text">106</span>
                    </div>
                </div>
            </div>
        </x-card>

        <!-- Status distribution -->
        <x-card>
            <h3 class="text-base font-semibold text-patria-green-text mb-2">Répartition par statut</h3>
            <p class="text-sm text-patria-text-secondary mb-6">Un parc majoritairement opérationnel</p>
            <div class="flex items-stretch justify-between gap-4">
                <div class="flex flex-1 flex-col items-center">
                    <div class="flex h-36 w-full flex-col items-center justify-end">
                        <span class="mb-1 text-[13px] font-bold text-patria-green-text">1126</span>
                        <div class="h-28 w-14 rounded-t-md bg-patria-green-interface"></div>
                    </div>
                    <span class="mt-2 text-xs text-patria-text-secondary">En service</span>
                </div>
                <div class="flex flex-1 flex-col items-center">
                    <div class="flex h-36 w-full flex-col items-center justify-end">
                        <span class="mb-1 text-[13px] font-bold text-patria-green-text">68</span>
                        <div class="h-1.5 w-14 rounded-full bg-patria-gold"></div>
                    </div>
                    <span class="mt-2 text-xs text-patria-text-secondary">Maintenance</span>
                </div>
                <div class="flex flex-1 flex-col items-center">
                    <div class="flex h-36 w-full flex-col items-center justify-end">
                        <span class="mb-1 text-[13px] font-bold text-patria-green-text">34</span>
                        <div class="h-1.5 w-14 rounded-full bg-patria-red"></div>
                    </div>
                    <span class="mt-2 text-xs text-patria-text-secondary">Réformé</span>
                </div>
                <div class="flex flex-1 flex-col items-center">
                    <div class="flex h-36 w-full flex-col items-center justify-end">
                        <span class="mb-1 text-[13px] font-bold text-patria-green-text">20</span>
                        <div class="h-1.5 w-14 rounded-full bg-patria-sage"></div>
                    </div>
                    <span class="mt-2 text-xs text-patria-text-secondary">Cédé</span>
                </div>
            </div>
        </x-card>
    </div>

    <!-- Recent movements -->
    <x-card>
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-patria-green-text">Derniers mouvements</h3>
            <a href="{{ route('movements.index') }}" class="text-sm text-patria-green-text hover:underline">Tout consulter →</a>
        </div>
        <x-table variant="compact" :headers="['Date', 'Bien', 'Type', 'Destination', 'Auteur']">
            <tr class="border-b border-patria-border hover:bg-patria-bg">
                <td class="px-6 py-4 text-patria-text">11/11/2026</td>
                <td class="px-6 py-4 text-patria-text">PAT-2026-00001 · Toyota Hilux</td>
                <td class="px-6 py-4 text-patria-green-text font-medium">Transfert</td>
                <td class="px-6 py-4 text-patria-text-secondary">Service logistique</td>
                <td class="px-6 py-4 text-patria-text-secondary">A. Diallo</td>
            </tr>
            <tr class="border-b border-patria-border hover:bg-patria-bg">
                <td class="px-6 py-4 text-patria-text">10/11/2026</td>
                <td class="px-6 py-4 text-patria-text">PAT-2026-00024 · Bureau double</td>
                <td class="px-6 py-4 text-patria-green-text font-medium">Affectation</td>
                <td class="px-6 py-4 text-patria-text-secondary">Direction financière</td>
                <td class="px-6 py-4 text-patria-text-secondary">M. Ndiaye</td>
            </tr>
            <tr class="border-b border-patria-border hover:bg-patria-bg">
                <td class="px-6 py-4 text-patria-text">10/11/2026</td>
                <td class="px-6 py-4 text-patria-text">PAT-2026-00042 · PC Dell</td>
                <td class="px-6 py-4 text-patria-green-text font-medium">Transfert</td>
                <td class="px-6 py-4 text-patria-text-secondary">Service informatique</td>
                <td class="px-6 py-4 text-patria-text-secondary">A. Diallo</td>
            </tr>
            <tr class="border-b border-patria-border hover:bg-patria-bg">
                <td class="px-6 py-4 text-patria-text">09/11/2026</td>
                <td class="px-6 py-4 text-patria-text">PAT-2026-00037 · Imprimante HP</td>
                <td class="px-6 py-4 text-patria-green-text font-medium">Réforme</td>
                <td class="px-6 py-4 text-patria-text-secondary">—</td>
                <td class="px-6 py-4 text-patria-text-secondary">M. Ndiaye</td>
            </tr>
            <tr class="hover:bg-patria-bg">
                <td class="px-6 py-4 text-patria-text">08/11/2026</td>
                <td class="px-6 py-4 text-patria-text">PAT-2026-00012 · Armoire</td>
                <td class="px-6 py-4 text-patria-green-text font-medium">Cession</td>
                <td class="px-6 py-4 text-patria-text-secondary">—</td>
                <td class="px-6 py-4 text-patria-text-secondary">A. Diallo</td>
            </tr>
        </x-table>
    </x-card>
</div>
@endsection
