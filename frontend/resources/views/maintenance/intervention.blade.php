@extends('layouts.app')

@section('title', 'Détail intervention')
@section 'content'

<div class="space-y-6" x-data="interventionDetail('{{ $id }}')">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('dashboard') }}" class="hover:text-gray-900">Accueil</a>
        <span>/</span>
        <a href="{{ route('maintenance.index') }}" class="hover:text-gray-900">Maintenance</a>
        <span>/</span>
        <span class="text-gray-900">Intervention</span>
    </nav>

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900" x-text="intervention?.title || 'Chargement...'"></h1>
            <p class="text-gray-600 mt-1" x-text="intervention?.asset_code || ''"></p>
        </div>
        <div class="flex gap-2">
            <span class="px-3 py-1.5 rounded-full text-sm font-medium"
                :class="{
                    'bg-gray-100 text-gray-800': intervention?.status === 'PLANIFIEE',
                    'bg-yellow-100 text-yellow-800': intervention?.status === 'EN_COURS',
                    'bg-green-100 text-green-800': intervention?.status === 'TERMINEE',
                    'bg-red-100 text-red-800': intervention?.status === 'ANNULEE'
                }"
                x-text="intervention?.status"></span>
            @if (in_array(session('user_role'), ['GESTIONNAIRE', 'ADMIN']))
                <button
                    x-show="intervention?.status === 'PLANIFIEE'"
                    @click="startIntervention()"
                    class="px-4 py-2 bg-[#0C6744] text-white rounded-lg hover:bg-[#0A5336] transition-colors"
                >
                    Démarrer
                </button>
                <button
                    x-show="intervention?.status === 'EN_COURS'"
                    @click="showCloseModal = true"
                    class="px-4 py-2 bg-[#0C6744] text-white rounded-lg hover:bg-[#0A5336] transition-colors"
                >
                    Clôturer
                </button>
                <button
                    x-show="intervention?.status === 'PLANIFIEE' || intervention?.status === 'EN_COURS'"
                    @click="cancelIntervention()"
                    class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                >
                    Annuler
                </button>
            @endif
        </div>
    </div>

    <template x-if="loading">
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <svg class="animate-spin h-8 w-8 mx-auto mb-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-gray-500">Chargement...</p>
        </div>
    </template>

    <template x-if="!loading && intervention">
        <div class="space-y-6">
            <!-- Info cards -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Type</span>
                            <span class="font-medium text-gray-900" x-text="intervention.type"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Statut</span>
                            <span class="font-medium text-gray-900" x-text="intervention.status"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Date planifiée</span>
                            <span class="font-medium text-gray-900" x-text="formatDate(intervention.scheduled_date)"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Début</span>
                            <span class="font-medium text-gray-900" x-text="formatDate(intervention.started_at)"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Fin</span>
                            <span class="font-medium text-gray-900" x-text="formatDate(intervention.ended_at)"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Signalé par</span>
                            <span class="font-medium text-gray-900" x-text="intervention.reported_by"></span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Description</h3>
                    <p class="text-gray-600" x-text="intervention.description || '-'"></p>

                    <template x-if="intervention.status === 'TERMINEE'">
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <h4 class="font-medium text-gray-900 mb-2">Rapport</h4>
                            <p class="text-gray-600" x-text="intervention.report || '-'"></p>
                            <div class="mt-2 text-sm">
                                <span class="text-gray-500">Coût: </span>
                                <span class="font-medium" x-text="formatCurrency(intervention.cost)"></span>
                            </div>
                            <div class="mt-1 text-sm">
                                <span class="text-gray-500">Prestataire: </span>
                                <span class="font-medium" x-text="intervention.provider || '-'"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </template>

    <!-- Close modal -->
    <div x-show="showCloseModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" style="display: none;">
        <div class="bg-white rounded-xl border border-gray-200 p-6 w-full max-w-lg mx-4" @click.away="showCloseModal = false">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Clôturer l'intervention</h3>
                <button @click="showCloseModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form @submit.prevent="closeIntervention">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Coût (€)</label>
                        <input
                            type="number"
                            x-model="closeForm.cost"
                            min="0"
                            step="0.01"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none"
                            placeholder="0.00"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Prestataire</label>
                        <input
                            type="text"
                            x-model="closeForm.provider"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none"
                            placeholder="Nom du prestataire"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rapport</label>
                        <textarea
                            x-model="closeForm.report"
                            rows="3"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none resize-none"
                            placeholder="Détails de l'intervention..."
                        ></textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="showCloseModal = false" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Annuler
                    </button>
                    <button
                        type="submit"
                        :disabled="submitting"
                        class="px-4 py-2 bg-[#0C6744] text-white rounded-lg hover:bg-[#0A5336] transition-colors disabled:opacity-50"
                    >
                        <span x-show="!submitting">Clôturer</span>
                        <span x-show="submitting">Clôture...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function interventionDetail(interventionId) {
    return {
        loading: true,
        intervention: null,
        showCloseModal: false,
        submitting: false,
        closeForm: {
            cost: '',
            provider: '',
            report: ''
        },

        async loadIntervention() {
            this.loading = true;
            try {
                const response = await fetch(`{{ env('API_URL') }}/api/interventions/${interventionId}`, {
                    headers: {
                        'Authorization': `Bearer {{ session('jwt_token') }}`,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                this.intervention = data;
            } catch (error) {
                console.error('Error loading intervention:', error);
            } finally {
                this.loading = false;
            }
        },

        async startIntervention() {
            try {
                const response = await fetch(`{{ env('API_URL') }}/api/interventions/${interventionId}/start`, {
                    method: 'PATCH',
                    headers: {
                        'Authorization': `Bearer {{ session('jwt_token') }}`,
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    this.loadIntervention();
                } else {
                    const error = await response.json();
                    alert(error.message || 'Erreur lors du démarrage');
                }
            } catch (error) {
                console.error('Error starting intervention:', error);
                alert('Erreur de connexion');
            }
        },

        async closeIntervention() {
            this.submitting = true;
            try {
                const response = await fetch(`{{ env('API_URL') }}/api/interventions/${interventionId}/close`, {
                    method: 'PATCH',
                    headers: {
                        'Authorization': `Bearer {{ session('jwt_token') }}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(this.closeForm)
                });

                if (response.ok) {
                    this.showCloseModal = false;
                    this.closeForm = { cost: '', provider: '', report: '' };
                    this.loadIntervention();
                } else {
                    const error = await response.json();
                    alert(error.message || 'Erreur lors de la clôture');
                }
            } catch (error) {
                console.error('Error closing intervention:', error);
                alert('Erreur de connexion');
            } finally {
                this.submitting = false;
            }
        },

        async cancelIntervention() {
            if (!confirm('Êtes-vous sûr de vouloir annuler cette intervention ?')) return;

            try {
                const response = await fetch(`{{ env('API_URL') }}/api/interventions/${interventionId}/cancel`, {
                    method: 'PATCH',
                    headers: {
                        'Authorization': `Bearer {{ session('jwt_token') }}`,
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    this.loadIntervention();
                } else {
                    const error = await response.json();
                    alert(error.message || 'Erreur lors de l\'annulation');
                }
            } catch (error) {
                console.error('Error cancelling intervention:', error);
                alert('Erreur de connexion');
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
            this.loadIntervention();
        }
    }
}
</script>
@endsection
