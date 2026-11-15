@extends('components.layout-app')

@section('title', 'Biens')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-patria-green-text">Biens</h1>
            <p class="text-sm text-patria-text-secondary mt-1">Consultez, recherchez et gérez les biens de votre patrimoine.</p>
        </div>
        <x-button variant="primary">+ Nouveau bien</x-button>
    </div>

    <!-- Filters card -->
    <div class="bg-white rounded-card p-6">
        <div class="grid grid-cols-1 md:grid-cols-[1.55fr_1fr_0.96fr_1.2fr] gap-5">
            <div>
                <label class="block text-[13px] text-patria-green-text mb-1">Rechercher un bien</label>
                <x-input placeholder="Code ou nom du bien" class="text-sm" />
            </div>
            <div>
                <label class="block text-[13px] text-patria-green-text mb-1">Catégorie</label>
                <x-select class="text-sm">
                    <option>Toutes les catégories</option>
                </x-select>
            </div>
            <div>
                <label class="block text-[13px] text-patria-green-text mb-1">Statut</label>
                <x-select class="text-sm">
                    <option>Tous les statuts</option>
                </x-select>
            </div>
            <div>
                <label class="block text-[13px] text-patria-green-text mb-1">Service</label>
                <x-select class="text-sm">
                    <option>Tous les services</option>
                </x-select>
            </div>
        </div>
    </div>

    <!-- Above table -->
    <div class="flex items-center justify-between">
        <p class="text-sm font-bold text-patria-green-text">1 248 biens</p>
        <p class="text-xs text-patria-text-secondary">Les montants sont exprimés en FCFA.</p>
    </div>

    <!-- Table card -->
    <x-card>
        <!-- Table -->
        <x-table :headers="['Code', 'Nom du bien', 'Catégorie', 'Service', 'Valeur nette', 'Statut', '']">
            <tr class="border-b border-patria-border hover:bg-patria-bg/50">
                <td class="px-6 py-4 text-patria-green-text font-medium">PAT-2026-00001</td>
                <td class="px-6 py-4 text-patria-text">Toyota Hilux 2.4 D</td>
                <td class="px-6 py-4 text-patria-text-secondary">Véhicules</td>
                <td class="px-6 py-4 text-patria-text-secondary">Logistique</td>
                <td class="px-6 py-4 text-patria-text">19 600 000</td>
                <td class="pl-[35px] pr-6 py-4 text-patria-green-text">En service</td>
                <td class="px-6 py-4 text-right"><svg class="ml-auto h-4 w-4 text-patria-green-text" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/></svg></td>
            </tr>
            <tr class="border-b border-patria-border hover:bg-patria-bg/50">
                <td class="px-6 py-4 text-patria-green-text font-medium">PAT-2026-00018</td>
                <td class="px-6 py-4 text-patria-text">Climatiseur Daikin</td>
                <td class="px-6 py-4 text-patria-text-secondary">Équipements</td>
                <td class="px-6 py-4 text-patria-text-secondary">Moyens généraux</td>
                <td class="px-6 py-4 text-patria-text">420 000</td>
                <td class="pl-[35px] pr-6 py-4 text-patria-gold">En maintenance</td>
                <td class="px-6 py-4 text-right"><svg class="ml-auto h-4 w-4 text-patria-green-text" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/></svg></td>
            </tr>
            <tr class="border-b border-patria-border hover:bg-patria-bg/50">
                <td class="px-6 py-4 text-patria-green-text font-medium">PAT-2026-00024</td>
                <td class="px-6 py-4 text-patria-text">Bureau double</td>
                <td class="px-6 py-4 text-patria-text-secondary">Mobilier</td>
                <td class="px-6 py-4 text-patria-text-secondary">Finances</td>
                <td class="px-6 py-4 text-patria-text">280 000</td>
                <td class="pl-[35px] pr-6 py-4 text-patria-green-text">En service</td>
                <td class="px-6 py-4 text-right"><svg class="ml-auto h-4 w-4 text-patria-green-text" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/></svg></td>
            </tr>
            <tr class="border-b border-patria-border hover:bg-patria-bg/50">
                <td class="px-6 py-4 text-patria-green-text font-medium">PAT-2026-00042</td>
                <td class="px-6 py-4 text-patria-text">Dell Latitude 5440</td>
                <td class="px-6 py-4 text-patria-text-secondary">Informatique</td>
                <td class="px-6 py-4 text-patria-text-secondary">Informatique</td>
                <td class="px-6 py-4 text-patria-text">650 000</td>
                <td class="pl-[35px] pr-6 py-4 text-patria-green-text">En service</td>
                <td class="px-6 py-4 text-right"><svg class="ml-auto h-4 w-4 text-patria-green-text" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/></svg></td>
            </tr>
            <tr class="border-b border-patria-border hover:bg-patria-bg/50">
                <td class="px-6 py-4 text-patria-green-text font-medium">PAT-2026-00037</td>
                <td class="px-6 py-4 text-patria-text">Imprimante HP M404</td>
                <td class="px-6 py-4 text-patria-text-secondary">Informatique</td>
                <td class="px-6 py-4 text-patria-text-secondary">Archives</td>
                <td class="px-6 py-4 text-patria-text">0</td>
                <td class="pl-[35px] pr-6 py-4 text-patria-red">Réformé</td>
                <td class="px-6 py-4 text-right"><svg class="ml-auto h-4 w-4 text-patria-green-text" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/></svg></td>
            </tr>
            <tr class="hover:bg-patria-bg/50">
                <td class="px-6 py-4 text-patria-green-text font-medium">PAT-2026-00012</td>
                <td class="px-6 py-4 text-patria-text">Armoire métallique</td>
                <td class="px-6 py-4 text-patria-text-secondary">Mobilier</td>
                <td class="px-6 py-4 text-patria-text-secondary">Archives</td>
                <td class="px-6 py-4 text-patria-text">0</td>
                <td class="pl-[35px] pr-6 py-4 text-patria-text-secondary">Cédé</td>
                <td class="px-6 py-4 text-right"><svg class="ml-auto h-4 w-4 text-patria-green-text" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/></svg></td>
            </tr>
        </x-table>

        <!-- Table footer -->
        <div class="flex items-center justify-between mt-4 pt-4 border-t border-patria-border">
            <p class="text-sm text-patria-text-secondary">Affichage de 1 à 6 sur 1 248 biens</p>
            <div class="flex items-center gap-2">
                <span class="text-sm text-patria-text-secondary">Précédent</span>
                <span class="text-sm text-patria-green-text font-medium">1</span>
                <span class="text-sm text-patria-text-secondary">2</span>
                <span class="text-sm text-patria-text-secondary">3</span>
                <span class="text-sm text-patria-text-secondary">…</span>
                <span class="text-sm text-patria-text-secondary">208</span>
                <span class="text-sm text-patria-green-text">Suivant →</span>
            </div>
        </div>
    </x-card>

    <!-- Info banner -->
    <div class="bg-patria-green/10 border border-patria-green/20 rounded-lg px-4 py-3">
        <p class="text-sm text-patria-green-text">
            <span class="font-medium">Accès administratrice</span> · Tous les services sont visibles dans ce catalogue.
        </p>
    </div>
</div>
@endsection
