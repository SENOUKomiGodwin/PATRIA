@extends('layouts.app')

@section('title', 'Maintenance')

<div class="space-y-6" x-data="maintenancePage()">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('dashboard') }}" class="hover:text-gray-900">Accueil</a>
        <span>/</span>
        <span class="text-gray-900">Maintenance</span>
    </nav>

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Maintenance</h1>
            <p class="text-gray-600 mt-1">Interventions et plans préventifs</p>
        </div>
        <div class="flex gap-2">
            @if (in_array(session('user_role'), ['AGENT', 'GESTIONNAIRE', 'ADMIN']))
                <button @click="showReportModal = true" class="px-4 py-2 bg-[#C62828] text-white rounded-lg hover:bg-red-700 transition-colors">
                    Déclarer une panne
                </button>
            @endif
            <a href="{{ route('maintenance.plans') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Plans préventifs
            </a>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="border-b border-gray-200">
            <nav class="flex gap-6 px-6">
                <button
                    @click="activeTab = 'interventions'"
                    :class="activeTab === 'interventions' ? 'border-b-2 border-[#0C6744] text-[#0C6744]' : 'text-gray-500 hover:text-gray-700'"
                    class="py-4 text-sm font-medium transition-colors"
                >
                    Interventions
                </button>
                <button
                    @click="activeTab = 'stats'"
                    :class="activeTab === 'stats' ? 'border-b-2 border-[#0C6744] text-[#0C6744]' : 'text-gray-500 hover:text-gray-700'"
                    class="py-4 text-sm font-medium transition-colors"
                >
                    Statistiques
                </button>
            </nav>
        </div>

        <!-- Interventions tab -->
        <div x-show="activeTab === 'interventions'" class="p-6">
            <!-- Filters -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select x-model="filters.type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none">
                        <option value="">Tous</option>
                        <option value="PREVENTIVE">Préventive</option>
                        <option value="CORRECTIVE">Corrective</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select x-model="filters.status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none">
                        <option value="">Tous</option>
                        <option value="PLANIFIEE">Planifiée</option>
                        <option value="EN_COURS">En cours</option>
                        <option value="TERMINEE">Terminée</option>
                        <option value="ANNULEE">Annulée</option>
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

            <!-- Interventions table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-sm text-gray-500 border-b border-gray-200 bg-gray-50">
                            <th class="px-6 py-4 font-medium">Date</th>
                            <th class="px-6 py-4 font-medium">Bien</th>
                            <th class="px-6 py-4 font-medium">Type</th>
                            <th class="px-6 py-4 font-medium">Titre</th>
                            <th class="px-6 py-4 font-medium">Statut</th>
                            <th class="px-6 py-4 font-medium text-right">Actions</th>
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
                        <template x-if="!loading && interventions.length === 0">
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    Aucune intervention
                                </td>
                            </tr>
                        </template>
                        <template x-for="intervention in interventions" :key="intervention.id">
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="px-6 py-4 text-gray-600" x-text="formatDate(intervention.scheduled_date)"></td>
                                <td class="px-6 py-4 font-medium text-gray-900" x-text="intervention.asset_code"></td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium"
                                        :class="{
                                            'bg-blue-100 text-blue-800': intervention.type === 'PREVENTIVE',
                                            'bg-red-100 text-red-800': intervention.type === 'CORRECTIVE'
                                        }"
                                        x-text="intervention.type"></span>
                                </td>
                                <td class="px-6 py-4 text-gray-600" x-text="intervention.title"></td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium"
                                        :class="{
                                            'bg-gray-100 text-gray-800': intervention.status === 'PLANIFIEE',
                                            'bg-yellow-100 text-yellow-800': intervention.status === 'EN_COURS',
                                            'bg-green-100 text-green-800': intervention.status === 'TERMINEE',
                                            'bg-red-100 text-red-800': intervention.status === 'ANNULEE'
                                        }"
                                        x-text="intervention.status"></span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a :href="`/maintenance/interventions/${intervention.id}`" class="text-[#0C6744] hover:underline text-sm">Voir</a>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4 flex items-center justify-between">
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

        <!-- Stats tab -->
        <div x-show="activeTab === 'stats'" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-gray-50 rounded-lg p-6">
                    <p class="text-sm text-gray-500 mb-1">Coût total</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="formatCurrency(stats.total_cost || 0)"></p>
                </div>
                <div class="bg-gray-50 rounded-lg p-6">
                    <p class="text-sm text-gray-500 mb-1">Durée moyenne</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="`${stats.avg_duration || 0} jours`"></p>
                </div>
                <div class="bg-gray-50 rounded-lg p-6">
                    <p class="text-sm text-gray-500 mb-1">En cours</p>
                    <p class="text-2xl font-bold text-yellow-600" x-text="stats.by_status?.EN_COURS || 0"></p>
                </div>
                <div class="bg-gray-50 rounded-lg p-6">
                    <p class="text-sm text-gray-500 mb-1">Terminées</p>
                    <p class="text-2xl font-bold text-green-600" x-text="stats.by_status?.TERMINEE || 0"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Report failure modal -->
    <div x-show="showReportModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" style="display: none;">
        <div class="bg-white rounded-xl border border-gray-200 p-6 w-full max-w-lg mx-4" @click.away="showReportModal = false">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Déclarer une panne</h3>
                <button @click="showReportModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form @submit.prevent="submitReport">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Code bien *</label>
                        <input
                            type="text"
                            x-model="reportForm.asset_code"
                            required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none"
                            placeholder="Code du bien en panne"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Titre *</label>
                        <input
                            type="text"
                            x-model="reportForm.title"
                            required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none"
                            placeholder="Description courte du problème"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea
                            x-model="reportForm.description"
                            rows="3"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none resize-none"
                            placeholder="Détails de la panne..."
                        ></textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="showReportModal = false" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Annuler
                    </button>
                    <button
                        type="submit"
                        :disabled="submitting"
                        class="px-4 py-2 bg-[#C62828] text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50"
                    >
                        <span x-show="!submitting">Déclarer</span>
                        <span x-show="submitting">Déclaration...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function maintenancePage() {
    return {
        activeTab: 'interventions',
        loading: false,
        interventions: [],
        showReportModal: false,
        submitting: false,
        filters: {
            type: '',
            status: '',
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
        stats: {},
        reportForm: {
            asset_code: '',
            title: '',
            description: ''
        },

        async loadInterventions() {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    page: this.pagination.current_page,
                    ...Object.fromEntries(Object.entries(this.filters).filter(([_, v]) => v !== ''))
                });
                const response = await fetch(`{{ env('API_URL') }}/api/interventions?${params}`, {
                    headers: {
                        'Authorization': `Bearer {{ session('jwt_token') }}`,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                this.interventions = data.data || [];
                this.pagination = {
                    current_page: data.current_page || 1,
                    last_page: data.last_page || 1,
                    from: data.from || 0,
                    to: data.to || 0,
                    total: data.total || 0
                };
            } catch (error) {
                console.error('Error loading interventions:', error);
            } finally {
                this.loading = false;
            }
        },

        async loadStats() {
            try {
                const response = await fetch('{{ env('API_URL') }}/api/interventions/stats', {
                    headers: {
                        'Authorization': `Bearer {{ session('jwt_token') }}`,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                this.stats = data;
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        },

        async submitReport() {
            this.submitting = true;
            try {
                const response = await fetch('{{ env('API_URL') }}/api/interventions', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer {{ session('jwt_token') }}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(this.reportForm)
                });

                if (response.ok) {
                    this.showReportModal = false;
                    this.reportForm = { asset_code: '', title: '', description: '' };
                    this.loadInterventions();
                } else {
                    const error = await response.json();
                    alert(error.message || 'Erreur lors de la déclaration');
                }
            } catch (error) {
                console.error('Error reporting failure:', error);
                alert('Erreur de connexion');
            } finally {
                this.submitting = false;
            }
        },

        previousPage() {
            if (this.pagination.current_page > 1) {
                this.pagination.current_page--;
                this.loadInterventions();
            }
        },

        nextPage() {
            if (this.pagination.current_page < this.pagination.last_page) {
                this.pagination.current_page++;
                this.loadInterventions();
            }
        },

        formatCurrency(value) {
            return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(value);
        },

        formatDate(date) {
            if (!date) return '-';
            return new Date(date).toLocaleDateString('fr-FR');
        },

        init() {
            this.loadInterventions();
            this.loadStats();
        }
    }
}
</script>
