@extends('layouts.app')

@section('title', 'Biens')
@section 'content'

<div class="space-y-6" x-data="assetsPage()">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('dashboard') }}" class="hover:text-gray-900">Accueil</a>
        <span>/</span>
        <span class="text-gray-900">Biens</span>
    </nav>

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Biens</h1>
            <p class="text-gray-600 mt-1">Gestion du patrimoine</p>
        </div>
        @if (in_array(session('user_role'), ['ADMIN', 'GESTIONNAIRE']))
            <a href="{{ route('assets.create') }}" class="px-4 py-2 bg-[#0C6744] text-white rounded-lg hover:bg-[#0A5336] transition-colors">
                Nouveau bien
            </a>
        @endif
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                <input
                    type="text"
                    x-model="filters.search"
                    placeholder="Code, designation..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none"
                >
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
                <select x-model="filters.category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none">
                    <option value="">Toutes</option>
                    <option value="IMMOBILIER">Immobilier</option>
                    <option value="MOBILIER">Mobilier</option>
                    <option value="MATERIEL">Matériel</option>
                    <option value="VEHICULE">Véhicule</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select x-model="filters.status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none">
                    <option value="">Tous</option>
                    <option value="EN_SERVICE">En service</option>
                    <option value="EN_MAINTENANCE">En maintenance</option>
                    <option value="REFORME">Réformé</option>
                    <option value="CEDE">Cédé</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Service</label>
                <select x-model="filters.service" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none">
                    <option value="">Tous</option>
                    <option value="Direction Générale">Direction Générale</option>
                    <option value="RH">Ressources Humaines</option>
                    <option value="Finance">Finance</option>
                    <option value="IT">Informatique</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Assets table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-sm text-gray-500 border-b border-gray-200 bg-gray-50">
                        <th class="px-6 py-4 font-medium">Code</th>
                        <th class="px-6 py-4 font-medium">Désignation</th>
                        <th class="px-6 py-4 font-medium">Catégorie</th>
                        <th class="px-6 py-4 font-medium">Service</th>
                        <th class="px-6 py-4 font-medium">Statut</th>
                        <th class="px-6 py-4 font-medium">Valeur</th>
                        <th class="px-6 py-4 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    <template x-if="loading">
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <svg class="animate-spin h-6 w-6 mx-auto mb-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Chargement...
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loading && assets.length === 0">
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                Aucun bien trouvé
                            </td>
                        </tr>
                    </template>
                    <template x-for="asset in assets" :key="asset.id">
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900" x-text="asset.code"></td>
                            <td class="px-6 py-4 text-gray-600" x-text="asset.designation"></td>
                            <td class="px-6 py-4 text-gray-600" x-text="asset.category"></td>
                            <td class="px-6 py-4 text-gray-600" x-text="asset.service"></td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-medium"
                                    :class="{
                                        'bg-green-100 text-green-800': asset.status === 'EN_SERVICE',
                                        'bg-yellow-100 text-yellow-800': asset.status === 'EN_MAINTENANCE',
                                        'bg-gray-100 text-gray-800': asset.status === 'REFORME',
                                        'bg-red-100 text-red-800': asset.status === 'CEDE'
                                    }"
                                    x-text="asset.status_label"></span>
                            </td>
                            <td class="px-6 py-4 text-gray-900" x-text="formatCurrency(asset.value)"></td>
                            <td class="px-6 py-4 text-right">
                                <a :href="`/biens/${asset.id}`" class="text-[#0C6744] hover:underline text-sm">Voir</a>
                            </td>
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
</div>

<script>
function assetsPage() {
    return {
        loading: false,
        assets: [],
        filters: {
            search: '',
            category: '',
            status: '',
            service: ''
        },
        pagination: {
            current_page: 1,
            last_page: 1,
            from: 0,
            to: 0,
            total: 0
        },

        async loadAssets() {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    page: this.pagination.current_page,
                    ...Object.fromEntries(Object.entries(this.filters).filter(([_, v]) => v !== ''))
                });
                const response = await fetch(`{{ env('API_URL') }}/api/assets?${params}`, {
                    headers: {
                        'Authorization': `Bearer {{ session('jwt_token') }}`,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                this.assets = data.data || [];
                this.pagination = {
                    current_page: data.current_page || 1,
                    last_page: data.last_page || 1,
                    from: data.from || 0,
                    to: data.to || 0,
                    total: data.total || 0
                };
            } catch (error) {
                console.error('Error loading assets:', error);
            } finally {
                this.loading = false;
            }
        },

        previousPage() {
            if (this.pagination.current_page > 1) {
                this.pagination.current_page--;
                this.loadAssets();
            }
        },

        nextPage() {
            if (this.pagination.current_page < this.pagination.last_page) {
                this.pagination.current_page++;
                this.loadAssets();
            }
        },

        formatCurrency(value) {
            return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(value);
        },

        init() {
            this.loadAssets();
        }
    }
}
</script>
@endsection
