@extends('layouts.app')

@section('title', 'Détail campagne')

<div class="space-y-6" x-data="inventoryDetail('{{ $id }}')">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('dashboard') }}" class="hover:text-gray-900">Accueil</a>
        <span>/</span>
        <a href="{{ route('inventories.index') }}" class="hover:text-gray-900">Inventaires</a>
        <span>/</span>
        <span class="text-gray-900">Détail</span>
    </nav>

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900" x-text="campaign?.title || 'Chargement...'"></h1>
            <p class="text-gray-600 mt-1" x-text="campaign?.description || ''"></p>
        </div>
        <div class="flex gap-2">
            <span class="px-3 py-1.5 rounded-full text-sm font-medium"
                :class="{
                    'bg-green-100 text-green-800': campaign?.status === 'OUVERTE',
                    'bg-gray-100 text-gray-800': campaign?.status === 'CLOTUREE'
                }"
                x-text="campaign?.status"></span>
            @if (in_array(session('user_role'), ['ADMIN', 'GESTIONNAIRE']))
                <button
                    x-show="campaign?.status === 'OUVERTE'"
                    @click="closeCampaign()"
                    class="px-4 py-2 bg-[#0C6744] text-white rounded-lg hover:bg-[#0A5336] transition-colors"
                >
                    Clôturer
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

    <template x-if="!loading && campaign">
        <div class="space-y-6">
            <!-- Stats cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <p class="text-sm text-gray-500 mb-1">Biens comptés</p>
                    <p class="text-3xl font-bold text-gray-900" x-text="campaign.counted_count || 0"></p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <p class="text-sm text-gray-500 mb-1">Conformes</p>
                    <p class="text-3xl font-bold text-green-600" x-text="campaign.conform_count || 0"></p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <p class="text-sm text-gray-500 mb-1">Écarts</p>
                    <p class="text-3xl font-bold text-[#F2B705]" x-text="campaign.discrepancy_count || 0"></p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <p class="text-sm text-gray-500 mb-1">Manquants</p>
                    <p class="text-3xl font-bold text-[#C62828]" x-text="campaign.missing_count || 0"></p>
                </div>
            </div>

            <!-- Add line form (if open) -->
            <div x-show="campaign.status === 'OUVERTE'" class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Compter un bien</h3>
                <form @submit.prevent="addLine" class="flex gap-4">
                    <div class="flex-1">
                        <input
                            type="text"
                            x-model="lineForm.asset_code"
                            required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none"
                            placeholder="Code du bien"
                        >
                    </div>
                    <div class="flex-1">
                        <input
                            type="text"
                            x-model="lineForm.found_location_id"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none"
                            placeholder="Emplacement trouvé"
                        >
                    </div>
                    <button
                        type="submit"
                        :disabled="submittingLine"
                        class="px-4 py-2 bg-[#0C6744] text-white rounded-lg hover:bg-[#0A5336] transition-colors disabled:opacity-50"
                    >
                        Ajouter
                    </button>
                </form>
            </div>

            <!-- Discrepancies -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Écarts et manquants</h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-sm text-gray-500 border-b border-gray-200">
                                <th class="pb-3 font-medium">Code bien</th>
                                <th class="pb-3 font-medium">Désignation</th>
                                <th class="pb-3 font-medium">Résultat</th>
                                <th class="pb-3 font-medium">Emplacement attendu</th>
                                <th class="pb-3 font-medium">Emplacement trouvé</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            <template x-if="discrepancies.length === 0">
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-gray-500">Aucun écart</td>
                                </tr>
                            </template>
                            <template x-for="line in discrepancies" :key="line.id">
                                <tr class="border-b border-gray-100">
                                    <td class="py-3 font-medium text-gray-900" x-text="line.asset_code"></td>
                                    <td class="py-3 text-gray-600" x-text="line.asset_designation"></td>
                                    <td class="py-3">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium"
                                            :class="{
                                                'bg-green-100 text-green-800': line.result === 'CONFORME',
                                                'bg-yellow-100 text-yellow-800': line.result === 'ECART',
                                                'bg-red-100 text-red-800': line.result === 'MANQUANT'
                                            }"
                                            x-text="line.result"></span>
                                    </td>
                                    <td class="py-3 text-gray-600" x-text="line.expected_location || '-'"></td>
                                    <td class="py-3 text-gray-600" x-text="line.found_location || '-'"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
function inventoryDetail(campaignId) {
    return {
        loading: true,
        campaign: null,
        discrepancies: [],
        submittingLine: false,
        lineForm: {
            asset_code: '',
            found_location_id: ''
        },

        async loadCampaign() {
            this.loading = true;
            try {
                const response = await fetch(`{{ env('API_URL') }}/api/campaigns/${campaignId}`, {
                    headers: {
                        'Authorization': `Bearer {{ session('jwt_token') }}`,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                this.campaign = data;
            } catch (error) {
                console.error('Error loading campaign:', error);
            } finally {
                this.loading = false;
            }
        },

        async loadDiscrepancies() {
            try {
                const response = await fetch(`{{ env('API_URL') }}/api/campaigns/${campaignId}/discrepancies`, {
                    headers: {
                        'Authorization': `Bearer {{ session('jwt_token') }}`,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                this.discrepancies = data.data || [];
            } catch (error) {
                console.error('Error loading discrepancies:', error);
            }
        },

        async addLine() {
            this.submittingLine = true;
            try {
                const response = await fetch(`{{ env('API_URL') }}/api/campaigns/${campaignId}/lines`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer {{ session('jwt_token') }}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(this.lineForm)
                });

                if (response.ok) {
                    this.lineForm = { asset_code: '', found_location_id: '' };
                    this.loadCampaign();
                    this.loadDiscrepancies();
                } else {
                    const error = await response.json();
                    alert(error.message || 'Erreur lors de l\'ajout');
                }
            } catch (error) {
                console.error('Error adding line:', error);
                alert('Erreur de connexion');
            } finally {
                this.submittingLine = false;
            }
        },

        async closeCampaign() {
            if (!confirm('Êtes-vous sûr de vouloir clôturer cette campagne ?')) return;

            try {
                const response = await fetch(`{{ env('API_URL') }}/api/campaigns/${campaignId}/close`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer {{ session('jwt_token') }}`,
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    this.loadCampaign();
                    this.loadDiscrepancies();
                } else {
                    const error = await response.json();
                    alert(error.message || 'Erreur lors de la clôture');
                }
            } catch (error) {
                console.error('Error closing campaign:', error);
                alert('Erreur de connexion');
            }
        },

        init() {
            this.loadCampaign();
            this.loadDiscrepancies();
        }
    }
}
</script>
