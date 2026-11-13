@extends('layouts.app')

@section('title', 'Fiche bien')

<div class="space-y-6" x-data="assetPage('{{ $id }}')">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('dashboard') }}" class="hover:text-gray-900">Accueil</a>
        <span>/</span>
        <a href="{{ route('assets.index') }}" class="hover:text-gray-900">Biens</a>
        <span>/</span>
        <span class="text-gray-900">Fiche</span>
    </nav>

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900" x-text="asset?.code || 'Chargement...'"></h1>
            <p class="text-gray-600 mt-1" x-text="asset?.designation || ''"></p>
        </div>
        @if (in_array(session('user_role'), ['ADMIN', 'GESTIONNAIRE']))
            <div class="flex gap-2">
                <a :href="`/biens/${asset?.id}/edit`" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Modifier
                </a>
            </div>
        @endif
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

    <template x-if="!loading && asset">
        <div class="space-y-6">
            <!-- Info cards -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main info -->
                <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations générales</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Code</p>
                            <p class="font-medium text-gray-900" x-text="asset.code"></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Catégorie</p>
                            <p class="font-medium text-gray-900" x-text="asset.category"></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Service actuel</p>
                            <p class="font-medium text-gray-900" x-text="asset.service"></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Emplacement</p>
                            <p class="font-medium text-gray-900" x-text="asset.location || '-'"></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Date d'acquisition</p>
                            <p class="font-medium text-gray-900" x-text="formatDate(asset.acquisition_date)"></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Fournisseur</p>
                            <p class="font-medium text-gray-900" x-text="asset.provider || '-'"></p>
                        </div>
                    </div>
                </div>

                <!-- QR Code -->
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">QR Code & Étiquette</h3>
                    <div class="bg-gray-50 rounded-lg p-4 flex items-center justify-center mb-4">
                        <div class="w-32 h-32 bg-white border-2 border-gray-300 flex items-center justify-center">
                            <svg class="w-24 h-24 text-gray-800" viewBox="0 0 24 24" fill="currentColor">
                                <rect x="2" y="2" width="8" height="8"/>
                                <rect x="14" y="2" width="8" height="8"/>
                                <rect x="2" y="14" width="8" height="8"/>
                                <rect x="12" y="6" width="2" height="2"/>
                                <rect x="16" y="12" width="2" height="2"/>
                                <rect x="6" y="12" width="2" height="2"/>
                                <rect x="12" y="16" width="2" height="2"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 text-center" x-text="asset.code"></p>
                    <button class="w-full mt-4 px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50 transition-colors">
                        Imprimer l'étiquette
                    </button>
                </div>
            </div>

            <!-- Financial info -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Amortissement</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Valeur d'acquisition</p>
                        <p class="text-2xl font-bold text-gray-900" x-text="formatCurrency(asset.acquisition_value)"></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Cumul amortissements</p>
                        <p class="text-2xl font-bold text-gray-900" x-text="formatCurrency(asset.accumulated_depreciation || 0)"></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Valeur nette comptable</p>
                        <p class="text-2xl font-bold text-[#0C6744]" x-text="formatCurrency(asset.net_value || 0)"></p>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span class="text-gray-600">Progression amortissement</span>
                        <span class="font-medium" x-text="`${asset.depreciation_percentage || 0}%`"></span>
                    </div>
                    <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-[#0C6744]" :style="`width: ${asset.depreciation_percentage || 0}%`"></div>
                    </div>
                </div>
            </div>

            <!-- History -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Historique des mouvements</h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-sm text-gray-500 border-b border-gray-200">
                                <th class="pb-3 font-medium">Date</th>
                                <th class="pb-3 font-medium">Type</th>
                                <th class="pb-3 font-medium">De</th>
                                <th class="pb-3 font-medium">Vers</th>
                                <th class="pb-3 font-medium">Par</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            <template x-if="movements.length === 0">
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-gray-500">Aucun mouvement</td>
                                </tr>
                            </template>
                            <template x-for="movement in movements" :key="movement.id">
                                <tr class="border-b border-gray-100">
                                    <td class="py-3 text-gray-600" x-text="formatDate(movement.date)"></td>
                                    <td class="py-3">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium"
                                            :class="{
                                                'bg-green-100 text-green-800': movement.type === 'AFFECTATION',
                                                'bg-blue-100 text-blue-800': movement.type === 'TRANSFERT',
                                                'bg-red-100 text-red-800': movement.type === 'CESSION',
                                                'bg-gray-100 text-gray-800': movement.type === 'REFORME'
                                            }"
                                            x-text="movement.type"></span>
                                    </td>
                                    <td class="py-3 text-gray-600" x-text="movement.from_service || '-'"></td>
                                    <td class="py-3 text-gray-600" x-text="movement.to_service || '-'"></td>
                                    <td class="py-3 text-gray-600" x-text="movement.by"></td>
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
function assetPage(assetId) {
    return {
        loading: true,
        asset: null,
        movements: [],

        async loadAsset() {
            this.loading = true;
            try {
                const response = await fetch(`{{ env('API_URL') }}/api/assets/${assetId}`, {
                    headers: {
                        'Authorization': `Bearer {{ session('jwt_token') }}`,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                this.asset = data;
            } catch (error) {
                console.error('Error loading asset:', error);
            } finally {
                this.loading = false;
            }
        },

        async loadMovements() {
            try {
                const response = await fetch(`{{ env('API_URL') }}/api/movements?asset_id=${assetId}`, {
                    headers: {
                        'Authorization': `Bearer {{ session('jwt_token') }}`,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                this.movements = data.data || [];
            } catch (error) {
                console.error('Error loading movements:', error);
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
            this.loadAsset();
            this.loadMovements();
        }
    }
}
</script>
