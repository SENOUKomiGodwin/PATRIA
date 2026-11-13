@extends('components.layout-app')

@section('title', 'Tableau de bord')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-sm text-patria-text-secondary">
        <span>PATRIA</span>
        <span>/</span>
        <span class="text-patria-text font-medium">TABLEAU DE BORD</span>
    </nav>

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-patria-green">Tableau de bord</h1>
            <p class="text-patria-text-secondary mt-1">Une vue d'ensemble de votre patrimoine et des actions à suivre.</p>
        </div>
        <x-button variant="secondary">Exporter le rapport</x-button>
    </div>

    <!-- Filters and date -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <span class="px-3 py-1 bg-patria-green/10 text-patria-green rounded-full text-sm font-medium">Toutes les structures</span>
        </div>
        <p class="text-sm text-patria-text-secondary">Mis à jour le 11 novembre 2026 à 09:42 · Données de démonstration</p>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <x-kpi-card
            title="Nombre de biens"
            value="1 248"
            subtitle="+ 24 biens ce mois"
            :icon="'<svg class=\"w-6 h-6 text-patria-green\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10\"/></svg>'"
        />

        <x-kpi-card
            title="Valeur d'acquisition"
            value="12,48 Md"
            subtitle="FCFA · coût historique"
            :icon="'<svg class=\"w-6 h-6 text-patria-green\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z\"/></svg>'"
        />

        <x-kpi-card
            title="Valeur nette comptable"
            value="8,736 Md"
            subtitle="FCFA · après amortissement"
            :icon="'<svg class=\"w-6 h-6 text-patria-green\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z\"/></svg>'"
        />

        <x-kpi-card
            title="Biens en maintenance"
            value="68"
            subtitle="5,4 % du parc"
            subtitle-class="text-patria-red"
            :icon="'<svg class=\"w-6 h-6 text-patria-gold\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z\"/><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M15 12a3 3 0 11-6 0 3 3 0 016 0z\"/></svg>'"
        />
    </div>

    <!-- Charts row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Category distribution -->
        <x-card>
            <h3 class="text-lg font-semibold text-patria-green mb-6">Répartition par catégorie</h3>
            <div class="flex items-center gap-8">
                <!-- Donut chart -->
                <div class="relative w-40 h-40">
                    <svg class="w-full h-full transform -rotate-90">
                        <circle cx="80" cy="80" r="60" stroke="#DFE7E1" stroke-width="20" fill="none"/>
                        <!-- 5 segments -->
                        <circle cx="80" cy="80" r="60" stroke="#1E8E5A" stroke-width="20" fill="none"
                            stroke-dasharray="150.8 226.2" stroke-linecap="round"/>
                        <circle cx="80" cy="80" r="60" stroke="#8A9F3C" stroke-width="20" fill="none"
                            stroke-dasharray="123.1 254.0" stroke-dashoffset="-150.8" stroke-linecap="round"/>
                        <circle cx="80" cy="80" r="60" stroke="#8CBDA5" stroke-width="20" fill="none"
                            stroke-dasharray="80.1 297.0" stroke-dashoffset="-273.9" stroke-linecap="round"/>
                        <circle cx="80" cy="80" r="60" stroke="#F2B705" stroke-width="20" fill="none"
                            stroke-dasharray="54.4 323.6" stroke-dashoffset="-354.0" stroke-linecap="round"/>
                        <circle cx="80" cy="80" r="60" stroke="#208658" stroke-width="20" fill="none"
                            stroke-dasharray="40.7 337.4" stroke-dashoffset="-408.4" stroke-linecap="round"/>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-2xl font-bold text-patria-green">1 248</span>
                        <span class="text-sm text-patria-text-secondary">biens</span>
                    </div>
                </div>

                <!-- Legend -->
                <div class="flex-1 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-donut-1"></div>
                            <span class="text-sm text-patria-text-secondary">Mobilier</span>
                        </div>
                        <span class="text-sm font-bold text-patria-green">412</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-donut-2"></div>
                            <span class="text-sm text-patria-text-secondary">Informatique</span>
                        </div>
                        <span class="text-sm font-bold text-patria-green">336</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-donut-3"></div>
                            <span class="text-sm text-patria-text-secondary">Équipements</span>
                        </div>
                        <span class="text-sm font-bold text-patria-green">218</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-donut-4"></div>
                            <span class="text-sm text-patria-text-secondary">Véhicules</span>
                        </div>
                        <span class="text-sm font-bold text-patria-green">176</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-donut-5"></div>
                            <span class="text-sm text-patria-text-secondary">Immobilier</span>
                        </div>
                        <span class="text-sm font-bold text-patria-green">106</span>
                    </div>
                </div>
            </div>
        </x-card>

        <!-- Status distribution -->
        <x-card>
            <h3 class="text-lg font-semibold text-patria-green mb-2">Répartition par statut</h3>
            <p class="text-sm text-patria-text-secondary mb-6">Un parc majoritairement opérationnel</p>
            <div class="space-y-4">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-patria-text-secondary">En service</span>
                        <span class="text-sm font-medium text-patria-green">1 126</span>
                    </div>
                    <div class="w-full h-3 bg-patria-border rounded-full overflow-hidden">
                        <div class="h-full bg-patria-green" style="width: 90%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-patria-text-secondary">Maintenance</span>
                        <span class="text-sm font-medium text-patria-gold">68</span>
                    </div>
                    <div class="w-full h-3 bg-patria-border rounded-full overflow-hidden">
                        <div class="h-full bg-patria-gold" style="width: 5%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-patria-text-secondary">Réformé</span>
                        <span class="text-sm font-medium text-patria-red">34</span>
                    </div>
                    <div class="w-full h-3 bg-patria-border rounded-full overflow-hidden">
                        <div class="h-full bg-patria-red" style="width: 3%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-patria-text-secondary">Cédé</span>
                        <span class="text-sm font-medium text-patria-text-secondary">20</span>
                    </div>
                    <div class="w-full h-3 bg-patria-border rounded-full overflow-hidden">
                        <div class="h-full bg-patria-text-secondary" style="width: 2%"></div>
                    </div>
                </div>
            </div>
        </x-card>
    </div>

    <!-- Recent movements -->
    <x-card>
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-patria-green">Derniers mouvements</h3>
            <a href="{{ route('movements.index') }}" class="text-sm text-patria-green hover:underline">Tout consulter →</a>
        </div>
        <x-table :headers="['Date', 'Bien', 'Type', 'Destination', 'Auteur']">
            <tr class="border-b border-patria-border hover:bg-patria-bg">
                <td class="px-6 py-4 text-patria-text">11/11/2026</td>
                <td class="px-6 py-4 text-patria-text">PAT-2026-00001 · Toyota Hilux</td>
                <td class="px-6 py-4 text-patria-green font-medium">Transfert</td>
                <td class="px-6 py-4 text-patria-text-secondary">Service logistique</td>
                <td class="px-6 py-4 text-patria-text-secondary">A. Diallo</td>
            </tr>
            <tr class="border-b border-patria-border hover:bg-patria-bg">
                <td class="px-6 py-4 text-patria-text">10/11/2026</td>
                <td class="px-6 py-4 text-patria-text">PAT-2026-00024 · Bureau double</td>
                <td class="px-6 py-4 text-patria-green font-medium">Affectation</td>
                <td class="px-6 py-4 text-patria-text-secondary">Direction financière</td>
                <td class="px-6 py-4 text-patria-text-secondary">M. Ndiaye</td>
            </tr>
            <tr class="border-b border-patria-border hover:bg-patria-bg">
                <td class="px-6 py-4 text-patria-text">10/11/2026</td>
                <td class="px-6 py-4 text-patria-text">PAT-2026-00042 · PC Dell</td>
                <td class="px-6 py-4 text-patria-green font-medium">Transfert</td>
                <td class="px-6 py-4 text-patria-text-secondary">Service informatique</td>
                <td class="px-6 py-4 text-patria-text-secondary">A. Diallo</td>
            </tr>
            <tr class="border-b border-patria-border hover:bg-patria-bg">
                <td class="px-6 py-4 text-patria-text">09/11/2026</td>
                <td class="px-6 py-4 text-patria-text">PAT-2026-00037 · Imprimante HP</td>
                <td class="px-6 py-4 text-patria-green font-medium">Réforme</td>
                <td class="px-6 py-4 text-patria-text-secondary">—</td>
                <td class="px-6 py-4 text-patria-text-secondary">M. Ndiaye</td>
            </tr>
            <tr class="hover:bg-patria-bg">
                <td class="px-6 py-4 text-patria-text">08/11/2026</td>
                <td class="px-6 py-4 text-patria-text">PAT-2026-00012 · Armoire</td>
                <td class="px-6 py-4 text-patria-green font-medium">Cession</td>
                <td class="px-6 py-4 text-patria-text-secondary">—</td>
                <td class="px-6 py-4 text-patria-text-secondary">A. Diallo</td>
            </tr>
        </x-table>
    </x-card>
</div>
@endsection
