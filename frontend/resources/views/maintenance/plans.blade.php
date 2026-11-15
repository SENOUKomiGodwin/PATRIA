@extends('components.layout-app')

@section('title', "Plans préventifs")

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-patria-green-text">Plans préventifs</h1>
            <p class="text-sm text-patria-text-secondary mt-1">Anticipez les échéances pour prolonger la durée de vie des biens.</p>
        </div>
        <x-button variant="primary">+ Nouveau plan</x-button>
    </div>

    <!-- Alert Banner -->
    <div class="bg-white border border-patria-red rounded-card p-4">
        <p class="text-patria-red font-semibold">2 plans en retard nécessitent votre attention.</p>
        <p class="text-patria-red text-sm mt-1">Les échéances dépassées génèrent une intervention planifiée à démarrer.</p>
    </div>

    <!-- Filters Card -->
    <x-card>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-patria-green-text mb-1">Rechercher un plan</label>
                <x-input placeholder="Titre, code ou nom du bien" />
            </div>
            <div>
                <label class="block text-sm font-medium text-patria-green-text mb-1">Échéance</label>
                <x-select>
                    <option>Toutes les échéances</option>
                </x-select>
            </div>
            <div>
                <label class="block text-sm font-medium text-patria-green-text mb-1">Activation</label>
                <x-select>
                    <option>Plans actifs</option>
                </x-select>
            </div>
        </div>
    </x-card>

    <!-- Table Card -->
    <x-card>
        <x-table :headers="['Plan et bien', 'Périodicité', 'Prochaine échéance', 'État', '']">
            <tr class="border-b border-patria-border hover:bg-patria-bg/50">
                <td class="px-6 py-4 text-patria-green-text">Vidange · Toyota Hilux (PAT-2026-00001)</td>
                <td class="px-6 py-4">Tous les 180 jours</td>
                <td class="px-6 py-4">08/11/2026</td>
                <td class="px-6 py-4 text-patria-red">En retard</td>
                <td class="px-6 py-4 text-right text-patria-text">→</td>
            </tr>
            <tr class="border-b border-patria-border hover:bg-patria-bg/50">
                <td class="px-6 py-4 text-patria-green-text">Entretien · Groupe électrogène (00007)</td>
                <td class="px-6 py-4">Tous les 90 jours</td>
                <td class="px-6 py-4">10/11/2026</td>
                <td class="px-6 py-4 text-patria-red">En retard</td>
                <td class="px-6 py-4 text-right text-patria-text">→</td>
            </tr>
            <tr class="border-b border-patria-border hover:bg-patria-bg/50">
                <td class="px-6 py-4 text-patria-green-text">Contrôle · Onduleur APC (00083)</td>
                <td class="px-6 py-4">Tous les 90 jours</td>
                <td class="px-6 py-4">18/11/2026</td>
                <td class="px-6 py-4">À venir</td>
                <td class="px-6 py-4 text-right text-patria-text">→</td>
            </tr>
            <tr class="border-b border-patria-border hover:bg-patria-bg/50">
                <td class="px-6 py-4 text-patria-green-text">Révision · Climatiseur Daikin (00018)</td>
                <td class="px-6 py-4">Tous les 180 jours</td>
                <td class="px-6 py-4">02/12/2026</td>
                <td class="px-6 py-4 text-patria-green-text">OK</td>
                <td class="px-6 py-4 text-right text-patria-text">→</td>
            </tr>
            <tr class="hover:bg-patria-bg/50">
                <td class="px-6 py-4 text-patria-green-text">Inspection · Pompe à eau (00056)</td>
                <td class="px-6 py-4">Tous les 30 jours</td>
                <td class="px-6 py-4">05/12/2026</td>
                <td class="px-6 py-4 text-patria-green-text">OK</td>
                <td class="px-6 py-4 text-right text-patria-text">→</td>
            </tr>
        </x-table>

        <!-- Table footer -->
        <div class="flex items-center justify-between mt-4 pt-4 border-t border-patria-border">
            <p class="text-sm text-patria-text-secondary">Affichage de 1 à 5 · Les plus récentes</p>
            <div class="flex items-center gap-2">
                <span class="text-sm text-patria-green-text">← Précédent</span>
                <span class="text-sm text-patria-green-text font-medium">1</span>
                <span class="text-sm text-patria-green-text">Suivant →</span>
            </div>
        </div>
    </x-card>

    <div class="text-sm text-patria-text-secondary">La prochaine échéance est recalculée après la clôture de l’intervention associée.</div>
</div>
@endsection
