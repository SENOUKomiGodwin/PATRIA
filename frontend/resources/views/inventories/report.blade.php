@extends('components.layout-app')

@section('title', "Rapport d’écarts")

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-patria-green-text">Rapport d’écarts</h1>
            <p class="text-sm text-patria-text-secondary mt-1">Inventaire d’octobre 2026 · Administration centrale · Clôturé le 31/10/2026</p>
        </div>
        <x-button variant="secondary">Exporter le rapport</x-button>
    </div>

    <!-- Colored cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-patria-green rounded-card p-6">
            <p class="text-sm text-white/80 mb-2">Biens conformes</p>
            <p class="text-4xl font-bold text-white mb-1">219</p>
            <p class="text-sm text-white/80">91,3 % des 240 biens du périmètre</p>
        </div>
        <div class="bg-patria-gold rounded-card p-6">
            <p class="text-sm text-patria-text/80 mb-2">Écarts de localisation</p>
            <p class="text-4xl font-bold text-patria-text mb-1">11</p>
            <p class="text-sm text-patria-text/80">À régulariser</p>
        </div>
        <div class="bg-patria-red rounded-card p-6">
            <p class="text-sm text-white/80 mb-2">Biens manquants</p>
            <p class="text-4xl font-bold text-white mb-1">10</p>
            <p class="text-sm text-white/80">À régulariser</p>
        </div>
    </div>

    <!-- Info banner -->
    <div class="bg-patria-green-light rounded-lg px-6 py-4">
        <p class="text-patria-green-text font-semibold mb-1">21 anomalies à traiter</p>
        <p class="text-sm text-patria-text-secondary">11 localisations à vérifier et 10 biens non retrouvés lors du comptage.</p>
    </div>

    <!-- Table -->
    <x-card>
        <x-table :headers="['Code', 'Bien', 'Lieu attendu', 'Lieu constaté', 'Résultat']">
                <tr class="border-b border-patria-border/50 hover:bg-patria-bg/50">
                    <td class="px-6 py-4 text-patria-green-text">PAT-2026-00042</td>
                    <td class="px-6 py-4">Dell Latitude 5440</td>
                    <td class="px-6 py-4">Bureau 102</td>
                    <td class="px-6 py-4">Bureau 204</td>
                    <td class="px-6 py-4 text-patria-gold">Écart</td>
                </tr>
                <tr class="border-b border-patria-border/50 hover:bg-patria-bg/50">
                    <td class="px-6 py-4 text-patria-green-text">PAT-2026-00058</td>
                    <td class="px-6 py-4">Écran Samsung 24”</td>
                    <td class="px-6 py-4">Bureau 105</td>
                    <td class="px-6 py-4">Salle de réunion</td>
                    <td class="px-6 py-4 text-patria-gold">Écart</td>
                </tr>
                <tr class="border-b border-patria-border/50 hover:bg-patria-bg/50">
                    <td class="px-6 py-4 text-patria-green-text">PAT-2026-00065</td>
                    <td class="px-6 py-4">Fauteuil de bureau</td>
                    <td class="px-6 py-4">Bureau 210</td>
                    <td class="px-6 py-4">—</td>
                    <td class="px-6 py-4 text-patria-red">Manquant</td>
                </tr>
                <tr class="border-b border-patria-border/50 hover:bg-patria-bg/50">
                    <td class="px-6 py-4 text-patria-green-text">PAT-2026-00071</td>
                    <td class="px-6 py-4">Vidéoprojecteur Epson</td>
                    <td class="px-6 py-4">Salle de formation</td>
                    <td class="px-6 py-4">—</td>
                    <td class="px-6 py-4 text-patria-red">Manquant</td>
                </tr>
                <tr class="hover:bg-patria-bg/50">
                    <td class="px-6 py-4 text-patria-green-text">PAT-2026-00093</td>
                    <td class="px-6 py-4">Table de réunion</td>
                    <td class="px-6 py-4">Salle de réunion</td>
                    <td class="px-6 py-4">Bureau 308</td>
                    <td class="px-6 py-4 text-patria-gold">Écart</td>
                </tr>
        </x-table>
        <!-- Table footer -->
        <div class="flex items-center justify-between px-6 py-4 border-t border-patria-border">
            <p class="text-sm text-patria-text-secondary">Affichage de 1 à 5 · Tri : plus récent</p>
            <div class="flex items-center gap-2">
                <button class="text-patria-green-text hover:underline px-2 py-1">← Précédent</button>
                <button class="text-patria-green-text font-semibold px-2 py-1">1</button>
                <button class="text-patria-green-text hover:underline px-2 py-1">Suivant →</button>
            </div>
        </div>
    </x-card>

    <!-- Footer text -->
    <p class="text-sm text-patria-text-secondary">Aucune modification du bien n’est appliquée automatiquement à la clôture.</p>
</div>
@endsection
