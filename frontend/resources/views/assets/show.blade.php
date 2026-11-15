@extends('components.layout-app')

@section('title', 'Fiche du bien')

@section('content')
<div class="space-y-6">
    <!-- Back link -->
    <a href="{{ route('assets.index') }}" class="text-sm text-patria-text-secondary hover:text-patria-text">← Retour aux biens</a>

    <!-- Header -->
    <div class="flex items-start justify-between">
        <div>
            <h1 class="text-2xl font-bold text-patria-green-text">Toyota Hilux 2.4 D</h1>
            <div class="flex items-center gap-3 mt-1">
                <p class="text-patria-text-secondary">PAT-2026-00001</p>
                <x-badge variant="success">En service</x-badge>
            </div>
        </div>
        <div class="flex gap-2">
            <x-button variant="secondary">Affecter</x-button>
            <x-button variant="secondary">Transférer</x-button>
            <x-button variant="secondary">Céder</x-button>
        </div>
    </div>

    <!-- Main grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left column (wide) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- General info card -->
            <x-card>
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-base font-semibold text-patria-green-text">Informations générales</h3>
                    <a href="#" class="text-sm text-patria-green-text hover:underline">Modifier →</a>
                </div>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-patria-text-secondary mb-1">Catégorie</p>
                        <p class="text-patria-green-text">Véhicules</p>
                    </div>
                    <div>
                        <p class="text-sm text-patria-text-secondary mb-1">État</p>
                        <p class="text-patria-green-text">Bon</p>
                    </div>
                    <div>
                        <p class="text-sm text-patria-text-secondary mb-1">Date d’acquisition</p>
                        <p class="text-patria-green-text">01/01/2025</p>
                    </div>
                    <div>
                        <p class="text-sm text-patria-text-secondary mb-1">Valeur d’acquisition</p>
                        <p class="text-patria-green-text">28 000 000 FCFA</p>
                    </div>
                    <div>
                        <p class="text-sm text-patria-text-secondary mb-1">Valeur nette comptable</p>
                        <p class="text-patria-green-text">19 600 000 FCFA</p>
                    </div>
                    <div>
                        <p class="text-sm text-patria-text-secondary mb-1">Durée d’amortissement</p>
                        <p class="text-patria-green-text">5 ans · linéaire</p>
                    </div>
                    <div>
                        <p class="text-sm text-patria-text-secondary mb-1">Service affectataire</p>
                        <p class="text-patria-green-text">Service logistique</p>
                    </div>
                    <div>
                        <p class="text-sm text-patria-text-secondary mb-1">Localisation</p>
                        <p class="text-patria-green-text">Parc automobile · Bâtiment A</p>
                    </div>
                </div>
            </x-card>

            <!-- Amortissement card -->
            <x-card>
                <h3 class="text-base font-semibold text-patria-green-text mb-6">Amortissement du bien</h3>
                <div class="grid grid-cols-2 gap-6 mb-4">
                    <div>
                        <p class="text-sm text-patria-text-secondary mb-1">Coût historique</p>
                        <p class="text-2xl font-bold text-patria-green-text">28 000 000 FCFA</p>
                    </div>
                    <div>
                        <p class="text-sm text-patria-text-secondary mb-1">Amortissement cumulé</p>
                        <p class="text-2xl font-bold text-patria-green-text">8 400 000 FCFA</p>
                    </div>
                </div>
                <div class="w-full h-3 bg-patria-green/10 rounded-full overflow-hidden mb-2">
                    <div class="h-full bg-patria-green" style="width: 70%"></div>
                </div>
                <p class="text-sm text-patria-green-text">70 % de la valeur d’origine</p>
                <p class="text-sm text-patria-text-secondary mt-1">Situation au 01/07/2026 · 18 mois écoulés</p>
            </x-card>
        </div>

        <!-- Right column (narrow) -->
        <div class="space-y-6">
            <!-- Identification card -->
            <x-card>
                <h3 class="text-base font-semibold text-patria-green-text mb-6">Identification du bien</h3>
                <div class="flex flex-col items-center" x-data="qrCode('PAT-2026-00001')">
                    <div class="w-36 h-36 bg-white flex items-center justify-center mb-4">
                        <div class="w-32 h-32" x-html="svg"></div>
                    </div>
                    <p class="text-sm text-patria-text-secondary">PAT-2026-00001</p>
                    <a href="#" class="text-sm text-patria-green-text hover:underline mt-4">Télécharger l’étiquette →</a>
                </div>
            </x-card>

            <!-- History card -->
            <x-card>
                <h3 class="text-base font-semibold text-patria-green-text mb-6">Historique des mouvements</h3>
                <div class="space-y-6 relative">
                    <!-- Timeline line -->
                    <div class="absolute left-2 top-2 bottom-2 w-0.5 bg-patria-border"></div>

                    <!-- Item 1 -->
                    <div class="relative pl-8">
                        <div class="absolute left-0 top-1 w-4 h-4 rounded-full bg-patria-green border-2 border-patria-green"></div>
                        <p class="text-xs text-patria-text-secondary">11 novembre 2026</p>
                        <p class="text-sm font-bold text-patria-green-text">Transfert</p>
                        <p class="text-sm text-patria-text-secondary">Vers le service logistique</p>
                    </div>

                    <!-- Item 2 -->
                    <div class="relative pl-8">
                        <div class="absolute left-0 top-1 w-4 h-4 rounded-full bg-patria-border border-2 border-patria-border"></div>
                        <p class="text-xs text-patria-text-secondary">4 mars 2025</p>
                        <p class="text-sm font-bold text-patria-green-text">Affectation</p>
                        <p class="text-sm text-patria-text-secondary">À la direction administrative</p>
                    </div>

                    <!-- Item 3 -->
                    <div class="relative pl-8">
                        <div class="absolute left-0 top-1 w-4 h-4 rounded-full bg-patria-border border-2 border-patria-border"></div>
                        <p class="text-xs text-patria-text-secondary">1 janvier 2025</p>
                        <p class="text-sm font-bold text-patria-green-text">Enregistrement</p>
                        <p class="text-sm text-patria-text-secondary">Entrée dans le patrimoine</p>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
