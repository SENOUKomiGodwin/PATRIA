@extends('components.layout-app')

@section('title', 'Mouvements')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-patria-green-text">Mouvements</h1>
            <p class="text-sm text-patria-text-secondary mt-1">Retrouvez la traçabilité des affectations, transferts, cessions et réformes.</p>
        </div>
        <x-button id="openModalBtn" variant="primary">+ Nouveau mouvement</x-button>
    </div>

    <!-- Filters card -->
    <x-card>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm text-patria-green-text mb-1">Rechercher un bien</label>
                <x-input placeholder="Code ou nom du bien" />
            </div>
            <div>
                <label class="block text-sm text-patria-green-text mb-1">Type de mouvement</label>
                <x-select>
                    <option value="">Tous les types</option>
                    <option value="affectation">Affectation</option>
                    <option value="transfert">Transfert</option>
                    <option value="cession">Cession</option>
                    <option value="reforme">Réforme</option>
                </x-select>
            </div>
            <div>
                <label class="block text-sm text-patria-green-text mb-1">Période</label>
                <x-select>
                    <option value="">01/11/2026 – 30/11/2026</option>
                </x-select>
            </div>
        </div>
    </x-card>

    <!-- Above table -->
    <p class="text-patria-green-text font-semibold">128 mouvements ce mois</p>

    <!-- Table -->
    <x-card>
        <x-table :headers="['Date', 'Bien', 'Type', 'Origine → Destination', 'Auteur']">
                <tr class="border-b border-patria-border/50 hover:bg-patria-bg/50">
                    <td class="px-6 py-4 text-patria-green-text">11/11/2026</td>
                    <td class="px-6 py-4">PAT-2026-00001 · Toyota Hilux</td>
                    <td class="px-6 py-4">Transfert</td>
                    <td class="px-6 py-4">Administration → Logistique</td>
                    <td class="px-6 py-4">A. Diallo</td>
                </tr>
                <tr class="border-b border-patria-border/50 hover:bg-patria-bg/50">
                    <td class="px-6 py-4 text-patria-green-text">10/11/2026</td>
                    <td class="px-6 py-4">PAT-2026-00024 · Bureau double</td>
                    <td class="px-6 py-4">Affectation</td>
                    <td class="px-6 py-4">Stock central → Finances</td>
                    <td class="px-6 py-4">M. Ndiaye</td>
                </tr>
                <tr class="border-b border-patria-border/50 hover:bg-patria-bg/50">
                    <td class="px-6 py-4 text-patria-green-text">10/11/2026</td>
                    <td class="px-6 py-4">PAT-2026-00042 · Dell Latitude</td>
                    <td class="px-6 py-4">Transfert</td>
                    <td class="px-6 py-4">Finances → Informatique</td>
                    <td class="px-6 py-4">A. Diallo</td>
                </tr>
                <tr class="border-b border-patria-border/50 hover:bg-patria-bg/50">
                    <td class="px-6 py-4 text-patria-green-text">09/11/2026</td>
                    <td class="px-6 py-4">PAT-2026-00037 · Imprimante HP</td>
                    <td class="px-6 py-4">Réforme</td>
                    <td class="px-6 py-4">Archives → Sortie du parc</td>
                    <td class="px-6 py-4">M. Ndiaye</td>
                </tr>
                <tr class="border-b border-patria-border/50 hover:bg-patria-bg/50">
                    <td class="px-6 py-4 text-patria-green-text">08/11/2026</td>
                    <td class="px-6 py-4">PAT-2026-00012 · Armoire</td>
                    <td class="px-6 py-4">Cession</td>
                    <td class="px-6 py-4">Archives → Bénéficiaire externe</td>
                    <td class="px-6 py-4">A. Diallo</td>
                </tr>
                <tr class="hover:bg-patria-bg/50">
                    <td class="px-6 py-4 text-patria-green-text">07/11/2026</td>
                    <td class="px-6 py-4">PAT-2026-00083 · Onduleur APC</td>
                    <td class="px-6 py-4">Affectation</td>
                    <td class="px-6 py-4">Stock central → Informatique</td>
                    <td class="px-6 py-4">M. Ndiaye</td>
                </tr>
        </x-table>
        <!-- Table footer -->
        <div class="flex items-center justify-between px-6 py-4 border-t border-patria-border">
            <p class="text-sm text-patria-text-secondary">Affichage de 1 à 6 · Tri : plus récent</p>
            <div class="flex items-center gap-2">
                <button class="text-patria-green-text hover:underline px-2 py-1">← Précédent</button>
                <button class="text-patria-green-text font-semibold px-2 py-1">1</button>
                <button class="text-patria-green-text hover:underline px-2 py-1">Suivant →</button>
            </div>
        </div>
    </x-card>
</div>

<!-- Modal -->
<div id="movementModal" class="fixed inset-0 bg-patria-green/30 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-card border border-patria-border p-6 w-full max-w-[720px] mx-4 max-h-[90vh] overflow-y-auto" id="modalContent">
        <!-- Header -->
        <div class="flex items-center justify-between mb-1">
            <h3 class="text-base font-semibold text-patria-green-text">Nouveau mouvement</h3>
            <button onclick="closeModal()" class="text-patria-text-secondary hover:text-patria-text">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <p class="text-patria-text-secondary mb-1">Tracer une nouvelle opération sur un bien.</p>
        <div class="border-t border-patria-border mb-4"></div>

            <!-- Type buttons -->
            <div class="mb-4">
                <label class="block text-sm text-patria-green-text mb-1">Type de mouvement *</label>
                <div class="grid grid-cols-4 gap-3">
                    <button class="px-4 py-2 bg-patria-bg text-patria-text rounded-button transition-colors">Affectation</button>
                    <button class="px-4 py-2 bg-patria-green text-white rounded-button transition-colors">Transfert</button>
                    <button class="px-4 py-2 bg-patria-bg text-patria-text rounded-button transition-colors">Cession</button>
                    <button class="px-4 py-2 bg-patria-bg text-patria-text rounded-button transition-colors">Réforme</button>
                </div>
            </div>

            <!-- Bien concerné -->
            <div class="mb-4">
                <label class="block text-sm text-patria-green-text mb-1">Bien concerné *</label>
                <x-select>
                    <option value="PAT-2026-00042">PAT-2026-00042 · Dell Latitude 5440</option>
                </x-select>
            </div>

            <!-- Info banner -->
            <div class="bg-patria-green-light rounded-lg px-4 py-2 mb-4">
                <p class="text-sm text-patria-green-text">Affectation actuelle : Direction financière · Bureau 204</p>
            </div>

            <!-- Destination -->
            <div class="grid grid-cols-2 gap-6 mb-4">
                <div>
                    <label class="block text-sm text-patria-green-text mb-1">Service de destination *</label>
                    <x-select>
                        <option value="Service informatique">Service informatique</option>
                    </x-select>
                </div>
                <div>
                    <label class="block text-sm text-patria-green-text mb-1">Lieu de destination *</label>
                    <x-select>
                        <option value="Bâtiment B · Bureau 102">Bâtiment B · Bureau 102</option>
                    </x-select>
                </div>
            </div>

            <!-- Motif -->
            <div class="mb-4">
                <label class="block text-sm text-patria-green-text mb-1">Motif *</label>
                <textarea rows="2" class="w-full px-4 py-2.5 border border-patria-border rounded-button focus-visible:ring-2 focus-visible:ring-patria-green focus-visible:border-patria-green outline-none transition-colors resize-none">Réaffectation du poste à l’équipe support informatique.</textarea>
            </div>
            <p class="text-sm text-patria-text-secondary mb-4">La destination doit être différente du service d’origine.</p>

            <!-- Footer -->
            <div class="border-t border-patria-border pt-3 modal-footer">
                <div class="flex justify-end gap-3">
                    <x-button variant="secondary" onclick="closeModal()">Annuler</x-button>
                    <x-button variant="primary">Valider</x-button>
                </div>
            </div>
    </div>
</div>

<style>
@media (max-height: 800px) {
    #modalContent {
        max-width: 640px !important;
        padding: 1rem 1.75rem !important;
    }
    #modalContent > .flex.mb-1 {
        margin-bottom: 0.25rem !important;
    }
    #modalContent > p.text-patria-text-secondary {
        margin-bottom: 0.25rem !important;
    }
    #modalContent > .border-t {
        margin-bottom: 0.75rem !important;
    }
    #modalContent > div.mb-4 {
        margin-bottom: 0.75rem !important;
    }
    #modalContent label {
        margin-bottom: 0.25rem !important;
    }
    #modalContent select,
    #modalContent input {
        height: 2.5rem !important;
        padding: 0.5rem 1rem !important;
    }
    #modalContent textarea {
        height: 5.25rem !important;
        padding: 0.5rem 1rem !important;
    }
    #modalContent .border-t.pt-3 {
        padding-top: 0.5rem !important;
        margin-top: 0.5rem !important;
    }
    #modalContent button {
        height: 2.375rem !important;
    }
}
</style>

<script>
function openModal() {
    document.getElementById('movementModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('movementModal').classList.add('hidden');
    document.body.style.overflow = '';
}

document.getElementById('openModalBtn').addEventListener('click', openModal);

document.getElementById('movementModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});
</script>
@endsection
