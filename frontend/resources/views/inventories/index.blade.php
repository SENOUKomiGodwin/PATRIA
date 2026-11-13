@extends('layouts.app')

@section('title', 'Campagnes d\'inventaire')
@section 'content'

<div class="space-y-6" x-data="inventoriesPage()">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('dashboard') }}" class="hover:text-gray-900">Accueil</a>
        <span>/</span>
        <span class="text-gray-900">Inventaires</span>
    </nav>

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Campagnes d'inventaire</h1>
            <p class="text-gray-600 mt-1">Suivi des inventaires</p>
        </div>
        @if (in_array(session('user_role'), ['ADMIN', 'GESTIONNAIRE']))
            <button @click="showCreateModal = true" class="px-4 py-2 bg-[#0C6744] text-white rounded-lg hover:bg-[#0A5336] transition-colors">
                Nouvelle campagne
            </button>
        @endif
    </div>

    <!-- Campaigns grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <template x-if="loading">
            <div class="col-span-full bg-white rounded-xl border border-gray-200 p-12 text-center">
                <svg class="animate-spin h-6 w-6 mx-auto mb-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-gray-500">Chargement...</p>
            </div>
        </template>

        <template x-if="!loading && campaigns.length === 0">
            <div class="col-span-full bg-white rounded-xl border border-gray-200 p-12 text-center">
                <p class="text-gray-500">Aucune campagne d'inventaire</p>
            </div>
        </template>

        <template x-for="campaign in campaigns" :key="campaign.id">
            <div class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="font-semibold text-gray-900" x-text="campaign.title"></h3>
                        <p class="text-sm text-gray-500 mt-1" x-text="formatDate(campaign.created_at)"></p>
                    </div>
                    <span class="px-2 py-1 rounded-full text-xs font-medium"
                        :class="{
                            'bg-green-100 text-green-800': campaign.status === 'OUVERTE',
                            'bg-gray-100 text-gray-800': campaign.status === 'CLOTUREE'
                        }"
                        x-text="campaign.status"></span>
                </div>

                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Biens comptés</span>
                        <span class="font-medium text-gray-900" x-text="campaign.counted_count || 0"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Écarts</span>
                        <span class="font-medium text-[#F2B705]" x-text="campaign.discrepancy_count || 0"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Manquants</span>
                        <span class="font-medium text-[#C62828]" x-text="campaign.missing_count || 0"></span>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-100">
                    <a :href="`/inventaires/${campaign.id}`" class="text-sm text-[#0C6744] hover:underline">
                        Voir le détail →
                    </a>
                </div>
            </div>
        </template>
    </div>

    <!-- Create campaign modal -->
    <div x-show="showCreateModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" style="display: none;">
        <div class="bg-white rounded-xl border border-gray-200 p-6 w-full max-w-lg mx-4" @click.away="showCreateModal = false">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Nouvelle campagne</h3>
                <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form @submit.prevent="submitCampaign">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Titre *</label>
                        <input
                            type="text"
                            x-model="campaignForm.title"
                            required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none"
                            placeholder="Ex: Inventaire Q4 2024"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea
                            x-model="campaignForm.description"
                            rows="3"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none resize-none"
                            placeholder="Périmètre, objectifs..."
                        ></textarea>
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
                        <span x-show="!submitting">Créer</span>
                        <span x-show="submitting">Création...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function inventoriesPage() {
    return {
        loading: false,
        campaigns: [],
        showCreateModal: false,
        submitting: false,
        campaignForm: {
            title: '',
            description: ''
        },

        async loadCampaigns() {
            this.loading = true;
            try {
                const response = await fetch('{{ env('API_URL') }}/api/campaigns', {
                    headers: {
                        'Authorization': `Bearer {{ session('jwt_token') }}`,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                this.campaigns = data.data || [];
            } catch (error) {
                console.error('Error loading campaigns:', error);
            } finally {
                this.loading = false;
            }
        },

        async submitCampaign() {
            this.submitting = true;
            try {
                const response = await fetch('{{ env('API_URL') }}/api/campaigns', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer {{ session('jwt_token') }}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(this.campaignForm)
                });

                if (response.ok) {
                    this.showCreateModal = false;
                    this.campaignForm = { title: '', description: '' };
                    this.loadCampaigns();
                } else {
                    const error = await response.json();
                    alert(error.message || 'Erreur lors de la création');
                }
            } catch (error) {
                console.error('Error creating campaign:', error);
                alert('Erreur de connexion');
            } finally {
                this.submitting = false;
            }
        },

        formatDate(date) {
            if (!date) return '-';
            return new Date(date).toLocaleDateString('fr-FR');
        },

        init() {
            this.loadCampaigns();
        }
    }
}
</script>
@endsection
