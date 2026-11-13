@extends('layouts.app')

@section('title', 'Style Guide')

@section('content')
<div class="space-y-8">
    <div>
        <h1 class="text-2xl font-bold text-patria-text">Style Guide PATRIA</h1>
        <p class="text-patria-text-secondary mt-1">Composants réutilisables</p>
    </div>

    <!-- Colors -->
    <x-card>
        <h2 class="text-lg font-semibold text-patria-text mb-4">Couleurs</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <div class="w-full h-16 bg-patria-green rounded-card mb-2"></div>
                <p class="text-sm font-medium">Vert principal</p>
                <p class="text-xs text-patria-text-secondary">#0C6744</p>
            </div>
            <div>
                <div class="w-full h-16 bg-patria-green-interface rounded-card mb-2"></div>
                <p class="text-sm font-medium">Vert interface</p>
                <p class="text-xs text-patria-text-secondary">#1E8E5A</p>
            </div>
            <div>
                <div class="w-full h-16 bg-patria-gold rounded-card mb-2"></div>
                <p class="text-sm font-medium">Or / Jaune</p>
                <p class="text-xs text-patria-text-secondary">#F2B705</p>
            </div>
            <div>
                <div class="w-full h-16 bg-patria-red rounded-card mb-2"></div>
                <p class="text-sm font-medium">Rouge</p>
                <p class="text-xs text-patria-text-secondary">#C62828</p>
            </div>
            <div>
                <div class="w-full h-16 bg-patria-bg rounded-card mb-2 border border-patria-border"></div>
                <p class="text-sm font-medium">Fond page</p>
                <p class="text-xs text-patria-text-secondary">#F5F7FA</p>
            </div>
            <div>
                <div class="w-full h-16 bg-patria-border rounded-card mb-2"></div>
                <p class="text-sm font-medium">Bordure carte</p>
                <p class="text-xs text-patria-text-secondary">#DFE7E1</p>
            </div>
        </div>
    </x-card>

    <!-- Buttons -->
    <x-card>
        <h2 class="text-lg font-semibold text-patria-text mb-4">Boutons</h2>
        <div class="flex flex-wrap gap-4">
            <x-button>Primaire</x-button>
            <x-button variant="secondary">Secondaire</x-button>
            <x-button variant="danger">Danger</x-button>
            <x-button variant="ghost">Fantôme</x-button>
            <x-button size="sm">Petit</x-button>
            <x-button size="lg">Grand</x-button>
            <x-button :disabled="true">Désactivé</x-button>
        </div>
    </x-card>

    <!-- Badges -->
    <x-card>
        <h2 class="text-lg font-semibold text-patria-text mb-4">Badges</h2>
        <div class="flex flex-wrap gap-4">
            <x-badge status="success">Succès</x-badge>
            <x-badge status="warning">Alerte</x-badge>
            <x-badge status="danger">Erreur</x-badge>
            <x-badge status="info">Info</x-badge>
            <x-badge status="default">Défaut</x-badge>
        </div>
    </x-card>

    <!-- Inputs -->
    <x-card>
        <h2 class="text-lg font-semibold text-patria-text mb-4">Champs</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-input label="Email" type="email" placeholder="nom@patrimoine.gov" />
            <x-input label="Mot de passe" type="password" placeholder="********" />
            <x-select label="Catégorie">
                <option value="">Sélectionner...</option>
                <option value="IMMOBILIER">Immobilier</option>
                <option value="MOBILIER">Mobilier</option>
            </x-select>
            <x-select label="Statut">
                <option value="">Tous</option>
                <option value="EN_SERVICE">En service</option>
                <option value="EN_MAINTENANCE">En maintenance</option>
            </x-select>
        </div>
    </x-card>

    <!-- Cards -->
    <x-card>
        <h2 class="text-lg font-semibold text-patria-text mb-4">Cartes</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-card>
                <h3 class="font-semibold text-patria-text">Carte simple</h3>
                <p class="text-sm text-patria-text-secondary mt-2">Contenu de la carte</p>
            </x-card>
            <x-card padding="p-4">
                <h3 class="font-semibold text-patria-text">Carte compacte</h3>
                <p class="text-sm text-patria-text-secondary mt-2">Padding réduit</p>
            </x-card>
            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-patria-text-secondary">Total des biens</p>
                        <p class="text-3xl font-bold text-patria-green">1 248</p>
                    </div>
                    <div class="w-12 h-12 bg-patria-green/10 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-patria-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                </div>
            </x-card>
        </div>
    </x-card>

    <!-- Table -->
    <x-card>
        <h2 class="text-lg font-semibold text-patria-text mb-4">Tableau</h2>
        <x-table :headers="['Date', 'Bien', 'Type', 'Destination', 'Auteur']">
            <tr class="border-b border-patria-border">
                <td class="px-6 py-4 text-patria-text">11/11/2026</td>
                <td class="px-6 py-4 text-patria-text">PAT-2026-00001 · Toyota Hilux</td>
                <td class="px-6 py-4 text-patria-green">Transfert</td>
                <td class="px-6 py-4 text-patria-text-secondary">Service logistique</td>
                <td class="px-6 py-4 text-patria-text-secondary">A. Diallo</td>
            </tr>
            <tr class="border-b border-patria-border">
                <td class="px-6 py-4 text-patria-text">10/11/2026</td>
                <td class="px-6 py-4 text-patria-text">PAT-2026-00024 · Bureau double</td>
                <td class="px-6 py-4 text-patria-green">Affectation</td>
                <td class="px-6 py-4 text-patria-text-secondary">Direction financière</td>
                <td class="px-6 py-4 text-patria-text-secondary">M. Ndiaye</td>
            </tr>
        </x-table>
    </x-card>

    <!-- Modal -->
    <x-card>
        <h2 class="text-lg font-semibold text-patria-text mb-4">Modale</h2>
        <x-button @click="show = true">Ouvrir modale</x-button>
        <x-modal title="Exemple de modale" :show="false">
            <p class="text-patria-text-secondary">Contenu de la modale</p>
            <div class="mt-6 flex justify-end gap-3">
                <x-button variant="secondary" @click="show = false">Annuler</x-button>
                <x-button>Confirmer</x-button>
            </div>
        </x-modal>
    </x-card>
</div>
@endsection
