@extends('components.layout-app')

@section('title', "Exports et historique")

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-patria-green-text">Exports et historique</h1>
            <p class="text-sm text-patria-text-secondary mt-1">Générez des rapports officiels pour partager et archiver vos données.</p>
        </div>
        <span class="px-3 py-1 bg-patria-green-light text-patria-green-text text-sm rounded-full">Données de démonstration</span>
    </div>

    <!-- Export Cards Grid -->
    <div class="grid grid-cols-2 gap-6">
        <!-- Biens -->
        <x-card>
            <h3 class="text-base font-semibold text-patria-green-text mb-2">Biens</h3>
            <p class="text-sm text-patria-text-secondary mb-2">Catalogue, affectations et valeurs comptables.</p>
            <p class="text-xs text-patria-text-secondary mb-4">1 248 biens</p>
            <div class="flex gap-3">
                <x-button variant="secondary" size="xs" class="gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    PDF
                </x-button>
                <x-button variant="secondary" size="xs" class="gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Excel
                </x-button>
            </div>
        </x-card>

        <!-- Mouvements -->
        <x-card>
            <h3 class="text-base font-semibold text-patria-green-text mb-2">Mouvements</h3>
            <p class="text-sm text-patria-text-secondary mb-2">Journal des affectations, transferts et sorties.</p>
            <p class="text-xs text-patria-text-secondary mb-4">128 mouvements · novembre</p>
            <div class="flex gap-3">
                <x-button variant="secondary" size="xs" class="gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    PDF
                </x-button>
                <x-button variant="secondary" size="xs" class="gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Excel
                </x-button>
            </div>
        </x-card>

        <!-- Inventaire -->
        <x-card>
            <h3 class="text-base font-semibold text-patria-green-text mb-2">Inventaire</h3>
            <p class="text-sm text-patria-text-secondary mb-2">Comptages et écarts de la dernière campagne.</p>
            <p class="text-xs text-patria-text-secondary mb-4">Octobre 2026 · campagne clôturée</p>
            <div class="flex gap-3">
                <x-button variant="secondary" size="xs" class="gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    PDF
                </x-button>
                <x-button variant="secondary" size="xs" class="gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Excel
                </x-button>
            </div>
        </x-card>

        <!-- Maintenance -->
        <x-card>
            <h3 class="text-base font-semibold text-patria-green-text mb-2">Maintenance</h3>
            <p class="text-sm text-patria-text-secondary mb-2">Interventions, coûts et suivi des équipements.</p>
            <p class="text-xs text-patria-text-secondary mb-4">Exercice 2026</p>
            <div class="flex gap-3">
                <x-button variant="secondary" size="xs" class="gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    PDF
                </x-button>
                <x-button variant="secondary" size="xs" class="gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Excel
                </x-button>
            </div>
        </x-card>
    </div>

    <!-- Recent Exports Section -->
    <div class="space-y-4">
        <div>
            <h3 class="text-base font-semibold text-patria-green-text">Derniers exports</h3>
            <p class="text-sm text-patria-text-secondary mt-1">Les exports sont tracés et limités à votre périmètre d’accès.</p>
        </div>

        <x-card>
            <x-table variant="compact" :headers="['Date', 'Type de rapport', 'Format', 'Utilisateur', '']">
                <tr class="border-b border-patria-border">
                    <td class="px-6 py-4">11/11/2026 · 09:20</td>
                    <td class="px-6 py-4">Biens</td>
                    <td class="px-6 py-4">Excel</td>
                    <td class="px-6 py-4">Aminata Diallo</td>
                    <td class="px-6 py-4 text-right">
                        <a href="#" class="text-patria-green-text hover:underline">Télécharger ↓</a>
                    </td>
                </tr>
                <tr class="border-b border-patria-border">
                    <td class="px-6 py-4">10/11/2026 · 16:45</td>
                    <td class="px-6 py-4">Inventaire</td>
                    <td class="px-6 py-4">PDF</td>
                    <td class="px-6 py-4">Fatou Diop</td>
                    <td class="px-6 py-4 text-right">
                        <a href="#" class="text-patria-green-text hover:underline">Télécharger ↓</a>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4">08/11/2026 · 11:32</td>
                    <td class="px-6 py-4">Maintenance</td>
                    <td class="px-6 py-4">PDF</td>
                    <td class="px-6 py-4">Moussa Ndiaye</td>
                    <td class="px-6 py-4 text-right">
                        <a href="#" class="text-patria-green-text hover:underline">Télécharger ↓</a>
                    </td>
                </tr>
            </x-table>
        </x-card>
    </div>
</div>
@endsection
