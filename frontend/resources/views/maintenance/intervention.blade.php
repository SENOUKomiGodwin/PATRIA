@extends('components.layout-app')

@section('title', 'Détail d’intervention')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="/maintenance" class="text-sm text-patria-text-secondary hover:text-patria-text">← Toutes les interventions</a>
            <h1 class="text-2xl font-bold text-patria-green-text mt-2">Le climatiseur ne refroidit plus</h1>
            <p class="text-sm text-patria-text-secondary mt-1">INT-2026-00086 · PAT-2026-00018 · Climatiseur Daikin</p>
            <div class="flex gap-2 mt-2">
                <x-badge variant="warning">En cours</x-badge>
                <x-badge variant="danger-solid">Corrective</x-badge>
            </div>
        </div>
        <div class="flex gap-3">
            <x-button variant="secondary" disabled>Démarrer</x-button>
            <x-button variant="primary" onclick="openCloseModal()">Clôturer</x-button>
            <x-button variant="secondary">Annuler</x-button>
        </div>
    </div>

    <!-- Main grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left column (wide) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Description card -->
            <x-card>
                <h3 class="text-base font-semibold text-patria-green-text mb-4">Description et périmètre</h3>
                <p class="text-patria-text mb-6">Le climatiseur du bureau 204 ne produit plus d’air froid depuis ce matin. Le ventilateur fonctionne mais la température ne baisse pas.</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-patria-text-secondary">Service</p>
                        <p class="text-patria-green-text font-medium">Moyens généraux</p>
                    </div>
                    <div>
                        <p class="text-sm text-patria-text-secondary">Localisation</p>
                        <p class="text-patria-green-text font-medium">Bâtiment A · Bureau 204</p>
                    </div>
                    <div>
                        <p class="text-sm text-patria-text-secondary">Déclarée par</p>
                        <p class="text-patria-green-text font-medium">Moussa Ndiaye</p>
                    </div>
                    <div>
                        <p class="text-sm text-patria-text-secondary">Type</p>
                        <p class="text-patria-green-text font-medium">Maintenance corrective</p>
                    </div>
                </div>
            </x-card>

            <!-- Cost card -->
            <x-card>
                <h3 class="text-base font-semibold text-patria-green-text mb-4">Coût et compte rendu</h3>
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <p class="text-sm text-patria-text-secondary">Prestataire</p>
                        <p class="text-patria-green-text font-medium">À renseigner à la clôture</p>
                    </div>
                    <div>
                        <p class="text-sm text-patria-text-secondary">Coût final</p>
                        <p class="text-patria-green-text font-medium">Non renseigné</p>
                    </div>
                </div>
                <div class="border-t border-patria-border pt-4">
                    <p class="text-sm text-patria-text-secondary mb-2">Rapport d’intervention</p>
                    <p class="text-patria-text-secondary">Le compte rendu sera disponible une fois l’intervention clôturée.</p>
                    <p class="text-sm text-patria-green-text mt-4">La clôture remettra le bien en service.</p>
                </div>
            </x-card>
        </div>

        <!-- Right column (narrow) -->
        <div class="space-y-6">
            <!-- Chronology card -->
            <x-card>
                <h3 class="text-base font-semibold text-patria-green-text mb-4">Chronologie</h3>
                <div class="space-y-6">
                    <!-- Timeline item 1 -->
                    <div class="flex gap-3">
                        <div class="flex flex-col items-center">
                            <div class="w-3 h-3 rounded-full bg-patria-green"></div>
                            <div class="w-0.5 h-full bg-patria-green -mt-1"></div>
                        </div>
                        <div class="flex-1 pb-6">
                            <p class="text-xs text-patria-text-secondary">11/11/2026 · 08:15</p>
                            <p class="text-patria-green-text font-semibold">Panne déclarée</p>
                            <p class="text-sm text-patria-text-secondary">Par Moussa Ndiaye</p>
                        </div>
                    </div>
                    <!-- Timeline item 2 -->
                    <div class="flex gap-3">
                        <div class="flex flex-col items-center">
                            <div class="w-3 h-3 rounded-full bg-patria-green"></div>
                            <div class="w-0.5 h-full bg-patria-green -mt-1"></div>
                        </div>
                        <div class="flex-1 pb-6">
                            <p class="text-xs text-patria-text-secondary">11/11/2026 · 08:15</p>
                            <p class="text-patria-green-text font-semibold">Intervention démarrée</p>
                            <p class="text-sm text-patria-text-secondary">Bien passé en maintenance</p>
                        </div>
                    </div>
                    <!-- Timeline item 3 -->
                    <div class="flex gap-3">
                        <div class="flex flex-col items-center">
                            <div class="w-3 h-3 rounded-full bg-patria-border"></div>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs text-patria-text-secondary">À venir</p>
                            <p class="text-patria-text-secondary font-semibold">Intervention clôturée</p>
                            <p class="text-sm text-patria-text-secondary">Rapport et coût à renseigner</p>
                        </div>
                    </div>
                </div>
            </x-card>

            <!-- Warning card -->
            <div class="bg-patria-gold rounded-lg px-6 py-6">
                <p class="text-base font-semibold text-patria-text">Bien en maintenance</p>
                <p class="text-sm text-patria-text mt-2">Ce bien reste indisponible pendant l’intervention. Son statut sera mis à jour lors de la clôture ou de l’annulation.</p>
            </div>
        </div>
    </div>
</div>

<!-- Close Intervention Modal -->
<div id="closeModal" class="fixed inset-0 bg-patria-green/30 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-card border border-patria-border p-6 w-full max-w-[720px] mx-4 max-h-[90vh] overflow-y-auto scrollbar-hide" id="closeModalContent" style="scrollbar-width: none; -ms-overflow-style: none;">
        <!-- Header -->
        <div class="flex items-center justify-between mb-1">
            <h3 class="text-base font-semibold text-patria-green-text">Clôturer l’intervention</h3>
            <button onclick="closeCloseModal()" class="text-patria-text-secondary hover:text-patria-text">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <p class="text-patria-text-secondary mb-1">INT-2026-00086 · Climatiseur Daikin</p>
        <div class="border-t border-patria-border mb-4"></div>

        <!-- Prestataire -->
        <div class="mb-4">
            <label class="block text-sm text-patria-green-text mb-1">Prestataire</label>
            <x-input type="text" value="Froid & Services" />
        </div>

        <!-- Cost and date -->
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm text-patria-green-text mb-1">Coût total (FCFA) *</label>
                <x-input type="text" value="85 000" />
            </div>
            <div>
                <label class="block text-sm text-patria-green-text mb-1">Date de clôture</label>
                <x-input type="text" value="11/11/2026" />
            </div>
        </div>

        <!-- Rapport -->
        <div class="mb-4">
            <label class="block text-sm text-patria-green-text mb-1">Rapport d’intervention *</label>
            <textarea rows="4" class="w-full px-4 py-2.5 border border-patria-border rounded-button focus-visible:ring-2 focus-visible:ring-patria-green focus-visible:border-patria-green outline-none transition-colors resize-none" style="height: 116px;">Contrôle du circuit, remplacement du condensateur et recharge en fluide. Test de fonctionnement conforme.</textarea>
        </div>

        <!-- Info banner -->
        <div class="bg-patria-green-light rounded-lg px-4 py-3 mb-4">
            <p class="text-sm font-semibold text-patria-green-text">Le bien sera remis en service.</p>
            <p class="text-xs text-patria-green-text mt-1">L’intervention clôturée restera consultable dans l’historique.</p>
        </div>

        <!-- Footer -->
        <div class="border-t border-patria-border pt-3">
            <div class="flex justify-end gap-3">
                <x-button variant="secondary" onclick="closeCloseModal()">Annuler</x-button>
                <x-button variant="primary">Confirmer la clôture</x-button>
            </div>
        </div>
    </div>
</div>

<style>
@media (max-height: 800px) {
    #closeModalContent {
        max-width: 640px !important;
        padding: 1rem 1.75rem !important;
    }
    #closeModalContent > .flex.mb-1 {
        margin-bottom: 0.25rem !important;
    }
    #closeModalContent > p.text-patria-text-secondary {
        margin-bottom: 0.25rem !important;
    }
    #closeModalContent > .border-t {
        margin-bottom: 0.75rem !important;
    }
    #closeModalContent > div.mb-4 {
        margin-bottom: 0.75rem !important;
    }
    #closeModalContent label {
        margin-bottom: 0.25rem !important;
    }
    #closeModalContent input {
        height: 2.5rem !important;
        padding: 0.5rem 1rem !important;
    }
    #closeModalContent textarea {
        height: 5.25rem !important;
        padding: 0.5rem 1rem !important;
    }
    #closeModalContent .border-t.pt-3 {
        padding-top: 0.5rem !important;
        margin-top: 0.5rem !important;
    }
    #closeModalContent button {
        height: 2.375rem !important;
    }
}
</style>

<script>
function openCloseModal() {
    document.getElementById('closeModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeCloseModal() {
    document.getElementById('closeModal').classList.add('hidden');
    document.body.style.overflow = '';
}

document.getElementById('closeModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCloseModal();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeCloseModal();
    }
});
</script>
@endsection
