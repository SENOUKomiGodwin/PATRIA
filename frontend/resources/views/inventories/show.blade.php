@extends('components.layout-app')

@section('title', "Comptage d’inventaire")

@section('content')
<div class="space-y-6">
    <!-- Back link -->
    <div class="mb-6">
        <a href="{{ route('inventories.index') }}" class="text-sm text-patria-text-secondary hover:text-patria-text">← Toutes les campagnes</a>
    </div>

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-patria-green-text">Inventaire · Novembre 2026</h1>
            <p class="text-sm text-patria-text-secondary mt-1">Administration centrale · Du 1 au 30 novembre 2026</p>
            <div class="mt-2">
                <x-badge variant="warning">Ouverte</x-badge>
            </div>
        </div>
        <div class="flex gap-3">
            <a href="/inventaires/1/rapport" class="px-4 py-2 border border-patria-border text-patria-green-text rounded-button hover:bg-patria-bg transition-colors font-semibold text-sm">Voir le rapport</a>
            <x-button variant="secondary">Clôturer la campagne</x-button>
        </div>
    </div>

    <!-- Progress card -->
    <x-card>
        <div class="flex items-center justify-between mb-4">
            <p class="text-patria-green-text font-semibold">172 biens comptés sur 240</p>
            <p class="text-patria-green-text">72 %</p>
        </div>
        <div class="w-full h-2 bg-patria-green/10 rounded-full overflow-hidden mb-4">
            <div class="h-full bg-patria-green" style="width: 72%"></div>
        </div>
        <p class="text-sm text-patria-text-secondary">161 conformes · 11 écarts · 68 biens restant à compter</p>
    </x-card>

    <!-- Registration card -->
    <x-card>
        <h3 class="text-base font-semibold text-patria-green-text mb-4">Enregistrer un comptage</h3>
        <div class="flex items-end gap-6">
            <div class="flex-1">
                <label class="block text-sm text-patria-green-text mb-1">Code du bien scanné *</label>
                <x-input type="text" value="PAT-2026-00024" />
            </div>
            <div class="flex-1">
                <label class="block text-sm text-patria-green-text mb-1">Localisation constatée *</label>
                <x-select>
                    <option value="Bâtiment A · Bureau 204">Bâtiment A · Bureau 204</option>
                </x-select>
            </div>
            <div>
                <x-button variant="primary-dark" size="md">Enregistrer</x-button>
            </div>
        </div>
        <p class="text-sm text-patria-text-secondary mt-4">Scannez l’étiquette QR ou saisissez le code manuellement.</p>
    </x-card>

    <!-- Recent items -->
    <h3 class="text-base font-semibold text-patria-green-text mb-4">Derniers biens comptés</h3>
    <x-card>
        <x-table variant="compact" :headers="['Code', 'Bien', 'Lieu attendu', 'Lieu constaté', 'Résultat']">
                <tr class="border-b border-patria-border/50 hover:bg-patria-bg/50">
                    <td class="px-6 py-4 text-patria-green-text">PAT-2026-00024</td>
                    <td class="px-6 py-4">Bureau double</td>
                    <td class="px-6 py-4">Bureau 204</td>
                    <td class="px-6 py-4">Bureau 204</td>
                    <td class="px-6 py-4 text-patria-green-text">Conforme</td>
                </tr>
                <tr class="border-b border-patria-border/50 hover:bg-patria-bg/50">
                    <td class="px-6 py-4 text-patria-green-text">PAT-2026-00042</td>
                    <td class="px-6 py-4">Dell Latitude 5440</td>
                    <td class="px-6 py-4">Bureau 102</td>
                    <td class="px-6 py-4">Bureau 204</td>
                    <td class="px-6 py-4"><x-badge variant="warning">Écart</x-badge></td>
                </tr>
                <tr class="border-b border-patria-border/50 hover:bg-patria-bg/50">
                    <td class="px-6 py-4 text-patria-green-text">PAT-2026-00001</td>
                    <td class="px-6 py-4">Toyota Hilux 2.4 D</td>
                    <td class="px-6 py-4">Parc automobile</td>
                    <td class="px-6 py-4">Parc automobile</td>
                    <td class="px-6 py-4 text-patria-green-text">Conforme</td>
                </tr>
                <tr class="hover:bg-patria-bg/50">
                    <td class="px-6 py-4 text-patria-green-text">PAT-2026-00083</td>
                    <td class="px-6 py-4">Onduleur APC</td>
                    <td class="px-6 py-4">Salle serveurs</td>
                    <td class="px-6 py-4">Salle serveurs</td>
                    <td class="px-6 py-4 text-patria-green-text">Conforme</td>
                </tr>
        </x-table>
        <!-- Table footer -->
        <div class="flex items-center justify-between px-6 py-4 border-t border-patria-border">
            <p class="text-sm text-patria-text-secondary">Affichage de 1 à 4 · Tri : plus récent</p>
            <div class="flex items-center gap-2">
                <button class="text-patria-green-text hover:underline px-2 py-1">← Précédent</button>
                <button class="text-patria-green-text font-semibold px-2 py-1">1</button>
                <button class="text-patria-green-text hover:underline px-2 py-1">Suivant →</button>
            </div>
        </div>
    </x-card>
</div>
@endsection
