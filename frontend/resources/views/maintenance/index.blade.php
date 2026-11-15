@extends('components.layout-app')

@section('title', "Interventions de maintenance")

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-patria-green-text">Interventions de maintenance</h1>
            <p class="text-sm text-patria-text-secondary mt-1">Préservez la disponibilité de vos équipements et maîtrisez vos coûts.</p>
        </div>
        <x-button id="openFailureModalBtn" variant="primary">+ Déclarer une panne</x-button>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-kpi-card title="En cours" value="68" subtitle="Biens immobilisés" />
        <x-kpi-card title="Planifiées" value="12" subtitle="Interventions à venir" />
        <x-kpi-card title="Coût cumulé en 2026" value="18,4 M" subtitle="FCFA · interventions clôturées" />
    </div>

    <!-- Filters Card -->
    <x-card>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-patria-green-text mb-1">Bien</label>
                <x-input placeholder="Code ou nom du bien" />
            </div>
            <div>
                <label class="block text-sm font-medium text-patria-green-text mb-1">Statut</label>
                <x-select>
                    <option>Tous les statuts</option>
                </x-select>
            </div>
            <div>
                <label class="block text-sm font-medium text-patria-green-text mb-1">Type</label>
                <x-select>
                    <option>Tous les types</option>
                </x-select>
            </div>
            <div>
                <label class="block text-sm font-medium text-patria-green-text mb-1">Période</label>
                <x-select>
                    <option>Novembre 2026</option>
                </x-select>
            </div>
        </div>
    </x-card>

    <!-- Table Card -->
    <x-card>
        <x-table :headers="['Bien', 'Type', 'Statut', 'Date prévue', 'Coût (FCFA)', '']">
            <tr class="border-b border-patria-border hover:bg-patria-bg/50">
                <td class="px-6 py-4 text-patria-green-text">PAT-2026-00018 · Climatiseur Daikin</td>
                <td class="px-6 py-4 text-patria-red">Corrective</td>
                <td class="px-6 py-4">En cours</td>
                <td class="px-6 py-4">11/11/2026</td>
                <td class="px-6 py-4">À renseigner</td>
                <td class="px-6 py-4 text-right text-patria-text">→</td>
            </tr>
            <tr class="border-b border-patria-border hover:bg-patria-bg/50">
                <td class="px-6 py-4 text-patria-green-text">PAT-2026-00001 · Toyota Hilux</td>
                <td class="px-6 py-4 text-patria-green-text">Préventive</td>
                <td class="px-6 py-4">Planifiée</td>
                <td class="px-6 py-4">15/11/2026</td>
                <td class="px-6 py-4">—</td>
                <td class="px-6 py-4 text-right text-patria-text">→</td>
            </tr>
            <tr class="border-b border-patria-border hover:bg-patria-bg/50">
                <td class="px-6 py-4 text-patria-green-text">PAT-2026-00083 · Onduleur APC</td>
                <td class="px-6 py-4 text-patria-green-text">Préventive</td>
                <td class="px-6 py-4">Planifiée</td>
                <td class="px-6 py-4">18/11/2026</td>
                <td class="px-6 py-4">—</td>
                <td class="px-6 py-4 text-right text-patria-text">→</td>
            </tr>
            <tr class="border-b border-patria-border hover:bg-patria-bg/50">
                <td class="px-6 py-4 text-patria-green-text">PAT-2026-00007 · Groupe électrogène</td>
                <td class="px-6 py-4 text-patria-red">Corrective</td>
                <td class="px-6 py-4 text-patria-green-text">Terminée</td>
                <td class="px-6 py-4">06/11/2026</td>
                <td class="px-6 py-4">480 000</td>
                <td class="px-6 py-4 text-right text-patria-text">→</td>
            </tr>
            <tr class="hover:bg-patria-bg/50">
                <td class="px-6 py-4 text-patria-green-text">PAT-2026-00056 · Pompe à eau</td>
                <td class="px-6 py-4 text-patria-red">Corrective</td>
                <td class="px-6 py-4 text-patria-text-secondary">Annulée</td>
                <td class="px-6 py-4">04/11/2026</td>
                <td class="px-6 py-4">—</td>
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
</div>

<!-- Failure Modal -->
<div id="failureModal" class="fixed inset-0 bg-patria-green/30 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-card border border-patria-border p-6 w-full max-w-[720px] mx-4 max-h-[90vh] overflow-y-auto scrollbar-hide" id="failureModalContent" style="scrollbar-width: none; -ms-overflow-style: none;">
        <!-- Header -->
        <div class="flex items-center justify-between mb-1">
            <h3 class="text-base font-semibold text-patria-green-text">Déclarer une panne</h3>
            <button onclick="closeFailureModal()" class="text-patria-text-secondary hover:text-patria-text">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <p class="text-patria-text-secondary mb-1">Une intervention corrective sera ouverte immédiatement.</p>
        <div class="border-t border-patria-border mb-4"></div>

        <!-- Bien concerné -->
        <div class="mb-4">
            <label class="block text-sm text-patria-green-text mb-1">Bien concerné *</label>
            <x-select>
                <option value="" disabled selected>Rechercher un code ou un nom de bien</option>
            </x-select>
        </div>

        <!-- Titre de la panne -->
        <div class="mb-4">
            <label class="block text-sm text-patria-green-text mb-1">Titre de la panne *</label>
            <x-input type="text" placeholder="Ex. Le climatiseur ne refroidit plus" />
        </div>

        <!-- Description -->
        <div class="mb-4">
            <label class="block text-sm text-patria-green-text mb-1">Description *</label>
            <textarea rows="4" placeholder="Décrivez les symptômes et les circonstances de la panne…" class="w-full px-4 py-2.5 border border-patria-border rounded-button focus-visible:ring-2 focus-visible:ring-patria-green focus-visible:border-patria-green outline-none transition-colors resize-none" style="height: 116px;"></textarea>
        </div>

        <!-- Warning banner -->
        <div class="bg-patria-gold rounded-lg px-4 py-3 mb-4">
            <p class="text-sm font-semibold text-patria-text">Le bien passera au statut « En maintenance ».</p>
            <p class="text-xs text-patria-text mt-1">Les biens réformés ou cédés ne peuvent pas faire l’objet d’une panne.</p>
        </div>

        <!-- Footer -->
        <div class="border-t border-patria-border pt-3">
            <div class="flex justify-end gap-3">
                <x-button variant="secondary" onclick="closeFailureModal()">Annuler</x-button>
                <x-button variant="primary">Déclarer</x-button>
            </div>
        </div>
    </div>
</div>

<style>
@media (max-height: 800px) {
    #failureModalContent {
        max-width: 640px !important;
        padding: 1rem 1.75rem !important;
    }
    #failureModalContent > .flex.mb-1 {
        margin-bottom: 0.25rem !important;
    }
    #failureModalContent > p.text-patria-text-secondary {
        margin-bottom: 0.25rem !important;
    }
    #failureModalContent > .border-t {
        margin-bottom: 0.75rem !important;
    }
    #failureModalContent > div.mb-4 {
        margin-bottom: 0.75rem !important;
    }
    #failureModalContent label {
        margin-bottom: 0.25rem !important;
    }
    #failureModalContent select,
    #failureModalContent input {
        height: 2.5rem !important;
        padding: 0.5rem 1rem !important;
    }
    #failureModalContent textarea {
        height: 5.25rem !important;
        padding: 0.5rem 1rem !important;
    }
    #failureModalContent .border-t.pt-3 {
        padding-top: 0.5rem !important;
        margin-top: 0.5rem !important;
    }
    #failureModalContent button {
        height: 2.375rem !important;
    }
}
</style>

<script>
function openFailureModal() {
    document.getElementById('failureModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeFailureModal() {
    document.getElementById('failureModal').classList.add('hidden');
    document.body.style.overflow = '';
}

document.getElementById('openFailureModalBtn').addEventListener('click', openFailureModal);

document.getElementById('failureModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeFailureModal();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeFailureModal();
    }
});
</script>
@endsection
