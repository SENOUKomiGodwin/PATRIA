@extends('components.layout-app')

@section('title', 'Nouveau bien')

@section('content')
<div class="space-y-6">
    <!-- Back link -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-patria-green-text">Nouveau bien</h1>
            <p class="text-sm text-patria-text-secondary mt-1">Renseignez les informations nécessaires à l’entrée au patrimoine.</p>
        </div>
        <a href="{{ route('assets.index') }}" class="text-sm text-patria-green-text hover:underline">← Retour au catalogue</a>
    </div>

    <!-- Main grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left column (wide) -->
        <div class="lg:col-span-2">
            <x-card>
                <x-asset-form :bouton_principal="'Créer le bien'" />
            </x-card>
        </div>

        <!-- Right column (narrow) -->
        <div class="space-y-6">
            <!-- À savoir card -->
            <div class="bg-patria-green-light rounded-lg p-6 min-h-[270px]">
                <h3 class="text-base font-semibold text-patria-green-text mb-4">À savoir</h3>
                <p class="text-sm text-patria-green-text mb-3">Le code du bien et son QR code sont générés automatiquement après la création.</p>
                <p class="text-sm text-patria-text-secondary">L’amortissement est calculé selon la durée définie pour la catégorie.</p>
            </div>

            <!-- Statut card -->
            <x-card>
                <h3 class="text-base font-semibold text-patria-green-text mb-4">Statut</h3>
                <x-badge variant="success">En service</x-badge>
                <p class="text-sm text-patria-text-secondary mt-4">Les changements de statut passent par un mouvement ou une intervention.</p>
            </x-card>
        </div>
    </div>
</div>
@endsection
