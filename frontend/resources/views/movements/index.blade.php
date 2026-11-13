@extends('layouts.app')

@section('title', 'Mouvements')

<div class="space-y-6" x-data="movementsPage()">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('dashboard') }}" class="hover:text-gray-900">Accueil</a>
        <span>/</span>
        <span class="text-gray-900">Mouvements</span>
    </nav>

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Mouvements</h1>
            <p class="text-gray-600 mt-1">Historique des déplacements</p>
        </div>
        @if (in_array(session('user_role'), ['ADMIN', 'GESTIONNAIRE']))
            <button @click="showModal = true" class="px-4 py-2 bg-[#0C6744] text-white rounded-lg hover:bg-[#0A5336] transition-colors">
                Nouveau mouvement
            </button>
        @endif
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Code bien</label>
                <input
                    type="text"
                    x-model="filters.asset_code"
                    placeholder="Code du bien..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none"
                >
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select x-model="filters.type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none">
                    <option value="">Tous</option>
                    <option value="AFFECTATION">Affectation</option>
                    <option value="TRANSFERT">Transfert</option>
                    <option value="CESSION">Cession</option>
                    <option value="REFORME">Réforme</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date début</label>
                <input
                    type="date"
                    x-model="filters.date_from"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none"
                >
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date fin</label>
                <input
                    type="date"
                    x-model="filters.date_to"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none"
                >
            </div>
        </div>
    </div>

    <!-- Movements table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-sm text-gray-500 border-b border-gray-200 bg-gray-50">
                        <th class="px-6 py-4 font-medium">Date</th>
                        <th class="px-6 py-4 font-medium">Code bien</th>
                        <th class="px-6 py-4 font-medium">Type</th>
                        <th class="px-6 py-4 font-medium">De</th>
                        <th class="px-6 py-4 font-medium">Vers</th>
                        <th class="px-6 py-4 font-medium">Par</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    <template x-if="loading">
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <svg class="animate-spin h-6 w-6 mx-auto mb-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Chargement...
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loading && movements.length === 0">
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                Aucun mouvement trouvé
                            </td>
                        </tr>
                    </template>
                    <template x-for="movement in movements" :key="movement.id">
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-6 py-4 text-gray-600" x-text="formatDate(movement.date)"></td>
                            <td class="px-6 py-4 font-medium text-gray-900" x-text="movement.asset_code"></td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-medium"
                                    :class="{
                                        'bg-green-100 text-green-800': movement.type === 'AFFECTATION',
                                        'bg-blue-100 text-blue-800': movement.type === 'TRANSFERT',
                                        'bg-red-100 text-red-800': movement.type === 'CESSION',
                                        'bg-gray-100 text-gray-800': movement.type === 'REFORME'
                                    }"
                                    x-text="movement.type"></span>
                            </td>
                            <td class="px-6 py-4 text-gray-600" x-text="movement.from_service || '-'"></td>
                            <td class="px-6 py-4 text-gray-600" x-text="movement.to_service || '-'"></td>
                            <td class="px-6 py-4 text-gray-600" x-text="movement.by"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <p class="text-sm text-gray-500" x-text="`Affichage ${pagination.from}-${pagination.to} sur ${pagination.total}`"></p>
            <div class="flex gap-2">
                <button
                    @click="previousPage()"
                    :disabled="pagination.current_page === 1"
                    class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
                >
                    Précédent
                </button>
                <button
                    @click="nextPage()"
                    :disabled="pagination.current_page === pagination.last_page"
                    class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
                >
                    Suivant
                </button>
            </div>
        </div>
    </div>

    <!-- New movement modal -->
    <div x-show="showModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" style="display: none;">
        <div class="bg-white rounded-xl border border-gray-200 p-6 w-full max-w-lg mx-4" @click.away="showModal = false">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Nouveau mouvement</h3>
                <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form @submit.prevent="submitMovement">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Code bien *</label>
                        <input
                            type="text"
                            x-model="movementForm.asset_code"
                            required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none"
                            placeholder="Code du bien"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                        <select x-model="movementForm.type" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none">
                            <option value="">Sélectionner...</option>
                            <option value="AFFECTATION">Affectation</option>
                            <option value="TRANSFERT">Transfert</option>
                            <option value="CESSION">Cession</option>
                            <option value="REFORME">Réforme</option>
                        </select>
                    </div>

                    <div x-show="movementForm.type === 'TRANSFERT' || movementForm.type === 'AFFECTATION'">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Service de destination *</label>
                        <select x-model="movementForm.to_service_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none">
                            <option value="">Sélectionner...</option>
                            <option value="1">Direction Générale</option>
                            <option value="2">Ressources Humaines</option>
                            <option value="3">Finance</option>
                            <option value="4">Informatique</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Motif</label>
                        <textarea
                            x-model="movementForm.reason"
                            rows="2"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none resize-none"
                            placeholder="Motif du mouvement..."
                        ></textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="showModal = false" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Annuler
                    </button>
                    <button
                        type="submit"
                        :disabled="submitting"
                        class="px-4 py-2 bg-[#0C6744] text-white rounded-lg hover:bg-[#0A5336] transition-colors disabled:opacity-50"
                    >
                        <span x-show="!submitting">Enregistrer</span>
                        <span x-show="submitting">Enregistrement...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function movementsPage() {
    return {
        loading: false,
        movements: [],
        showModal: false,
        submitting: false,
        filters: {
            asset_code: '',
            type: '',
            date_from: '',
            date_to: ''
        },
        pagination: {
            current_page: 1,
            last_page: 1,
            from: 0,
            to: 0,
            total: 0
        },
        movementForm: {
            asset_code: '',
            type: '',
            to_service_id: '',
            reason: ''
        },

        async loadMovements() {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    page: this.pagination.current_page,
                    ...Object.fromEntries(Object.entries(this.filters).filter(([_, v]) => v !== ''))
                });
                const response = await fetch(`{{ env('API_URL') }}/api/movements?${params}`, {
                    headers: {
                        'Authorization': `Bearer {{ session('jwt_token') }}`,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                this.movements = data.data || [];
                this.pagination = {
                    current_page: data.current_page || 1,
                    last_page: data.last_page || 1,
                    from: data.from || 0,
                    to: data.to || 0,
                    total: data.total || 0
                };
            } catch (error) {
                console.error('Error loading movements:', error);
            } finally {
                this.loading = false;
            }
        },

        async submitMovement() {
            this.submitting = true;
            try {
                const response = await fetch('{{ env('API_URL') }}/api/movements', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer {{ session('jwt_token') }}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(this.movementForm)
                });

                if (response.ok) {
                    this.showModal = false;
                    this.movementForm = { asset_code: '', type: '', to_service_id: '', reason: '' };
                    this.loadMovements();
                } else {
                    const error = await response.json();
                    alert(error.message || 'Erreur lors de la création');
                }
            } catch (error) {
                console.error('Error creating movement:', error);
                alert('Erreur de connexion');
            } finally {
                this.submitting = false;
            }
        },

        previousPage() {
            if (this.pagination.current_page > 1) {
                this.pagination.current_page--;
                this.loadMovements();
            }
        },

        nextPage() {
            if (this.pagination.current_page < this.pagination.last_page) {
                this.pagination.current_page++;
                this.loadMovements();
            }
        },

        formatDate(date) {
            if (!date) return '-';
            return new Date(date).toLocaleDateString('fr-FR');
        },

        init() {
            this.loadMovements();
        }
    }
}
</script>
