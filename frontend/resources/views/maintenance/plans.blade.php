@extends('layouts.app')

@section('title', 'Plans préventifs')

<div class="space-y-6" x-data="maintenancePlans()">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('dashboard') }}" class="hover:text-gray-900">Accueil</a>
        <span>/</span>
        <a href="{{ route('maintenance.index') }}" class="hover:text-gray-900">Maintenance</a>
        <span>/</span>
        <span class="text-gray-900">Plans préventifs</span>
    </nav>

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Plans préventifs</h1>
            <p class="text-gray-600 mt-1">Planification de la maintenance</p>
        </div>
        @if (in_array(session('user_role'), ['ADMIN', 'GESTIONNAIRE']))
            <button @click="showCreateModal = true" class="px-4 py-2 bg-[#0C6744] text-white rounded-lg hover:bg-[#0A5336] transition-colors">
                Nouveau plan
            </button>
        @endif
    </div>

    <!-- Plans table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-sm text-gray-500 border-b border-gray-200 bg-gray-50">
                        <th class="px-6 py-4 font-medium">Bien</th>
                        <th class="px-6 py-4 font-medium">Titre</th>
                        <th class="px-6 py-4 font-medium">Périodicité</th>
                        <th class="px-6 py-4 font-medium">Prochaine échéance</th>
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
                    <template x-if="!loading && plans.length === 0">
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                Aucun plan préventif
                            </td>
                        </tr>
                    </template>
                    <template x-for="plan in plans" :key="plan.id">
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900" x-text="plan.asset_code"></td>
                            <td class="px-6 py-4 text-gray-600" x-text="plan.title"></td>
                            <td class="px-6 py-4 text-gray-600" x-text="`${plan.periodicity_days} jours`"></td>
                            <td class="px-6 py-4 text-gray-600" x-text="formatDate(plan.next_due_date)"></td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-medium"
                                    :class="{
                                        'bg-green-100 text-green-800': plan.active,
                                        'bg-gray-100 text-gray-800': !plan.active
                                    }"
                                    x-text="plan.active ? 'Actif' : 'Inactif'"></span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if (in_array(session('user_role'), ['ADMIN', 'GESTIONNAIRE']))
                                    <button @click="editPlan(plan)" class="text-[#0C6744] hover:underline text-sm mr-3">Modifier</button>
                                @endif
                                <button
                                    x-show="plan.active"
                                    @click="togglePlan(plan)"
                                    class="text-gray-500 hover:text-gray-700 text-sm"
                                >
                                    Désactiver
                                </button>
                                <button
                                    x-show="!plan.active"
                                    @click="togglePlan(plan)"
                                    class="text-gray-500 hover:text-gray-700 text-sm"
                                >
                                    Activer
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create/Edit modal -->
    <div x-show="showCreateModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" style="display: none;">
        <div class="bg-white rounded-xl border border-gray-200 p-6 w-full max-w-lg mx-4" @click.away="showCreateModal = false">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900" x-text="editingPlan ? 'Modifier le plan' : 'Nouveau plan'"></h3>
                <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form @submit.prevent="submitPlan">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Code bien *</label>
                        <input
                            type="text"
                            x-model="planForm.asset_code"
                            required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none"
                            placeholder="Code du bien"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Titre *</label>
                        <input
                            type="text"
                            x-model="planForm.title"
                            required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none"
                            placeholder="Ex: Maintenance annuelle"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Périodicité (jours) *</label>
                        <input
                            type="number"
                            x-model="planForm.periodicity_days"
                            required
                            min="1"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none"
                            placeholder="Ex: 365"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Prochaine échéance *</label>
                        <input
                            type="date"
                            x-model="planForm.next_due_date"
                            required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none"
                        >
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="showCreateModal = false" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
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
function maintenancePlans() {
    return {
        loading: false,
        plans: [],
        showCreateModal: false,
        editingPlan: null,
        submitting: false,
        planForm: {
            asset_code: '',
            title: '',
            periodicity_days: '',
            next_due_date: ''
        },

        async loadPlans() {
            this.loading = true;
            try {
                const response = await fetch('{{ env('API_URL') }}/api/maintenance-plans', {
                    headers: {
                        'Authorization': `Bearer {{ session('jwt_token') }}`,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                this.plans = data.data || [];
            } catch (error) {
                console.error('Error loading plans:', error);
            } finally {
                this.loading = false;
            }
        },

        async submitPlan() {
            this.submitting = true;
            try {
                const method = this.editingPlan ? 'PUT' : 'POST';
                const url = this.editingPlan
                    ? `{{ env('API_URL') }}/api/maintenance-plans/${this.editingPlan.id}`
                    : '{{ env('API_URL') }}/api/maintenance-plans';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Authorization': `Bearer {{ session('jwt_token') }}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(this.planForm)
                });

                if (response.ok) {
                    this.showCreateModal = false;
                    this.editingPlan = null;
                    this.planForm = { asset_code: '', title: '', periodicity_days: '', next_due_date: '' };
                    this.loadPlans();
                } else {
                    const error = await response.json();
                    alert(error.message || 'Erreur lors de l\'enregistrement');
                }
            } catch (error) {
                console.error('Error saving plan:', error);
                alert('Erreur de connexion');
            } finally {
                this.submitting = false;
            }
        },

        editPlan(plan) {
            this.editingPlan = plan;
            this.planForm = {
                asset_code: plan.asset_code,
                title: plan.title,
                periodicity_days: plan.periodicity_days,
                next_due_date: plan.next_due_date
            };
            this.showCreateModal = true;
        },

        async togglePlan(plan) {
            try {
                const response = await fetch(`{{ env('API_URL') }}/api/maintenance-plans/${plan.id}`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': `Bearer {{ session('jwt_token') }}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ active: !plan.active })
                });

                if (response.ok) {
                    this.loadPlans();
                } else {
                    const error = await response.json();
                    alert(error.message || 'Erreur lors de la modification');
                }
            } catch (error) {
                console.error('Error toggling plan:', error);
                alert('Erreur de connexion');
            }
        },

        formatDate(date) {
            if (!date) return '-';
            return new Date(date).toLocaleDateString('fr-FR');
        },

        init() {
            this.loadPlans();
        }
    }
}
</script>
