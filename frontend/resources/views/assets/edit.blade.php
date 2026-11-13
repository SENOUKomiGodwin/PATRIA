@extends('layouts.app')

@section('title', 'Modifier bien')

<div class="space-y-6" x-data="assetForm('{{ $id }}')">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('dashboard') }}" class="hover:text-gray-900">Accueil</a>
        <span>/</span>
        <a href="{{ route('assets.index') }}" class="hover:text-gray-900">Biens</a>
        <span>/</span>
        <span class="text-gray-900">Modifier</span>
    </nav>

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Modifier le bien</h1>
            <p class="text-gray-600 mt-1" x-text="asset?.code || 'Chargement...'"></p>
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

    <template x-if="!loading && asset">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <form @submit.prevent="submit">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Code *</label>
                        <input
                            type="text"
                            x-model="form.code"
                            required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Désignation *</label>
                        <input
                            type="text"
                            x-model="form.designation"
                            required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie *</label>
                        <select x-model="form.category" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none">
                            <option value="IMMOBILIER">Immobilier</option>
                            <option value="MOBILIER">Mobilier</option>
                            <option value="MATERIEL">Matériel</option>
                            <option value="VEHICULE">Véhicule</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Service *</label>
                        <select x-model="form.service_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none">
                            <option value="1">Direction Générale</option>
                            <option value="2">Ressources Humaines</option>
                            <option value="3">Finance</option>
                            <option value="4">Informatique</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Emplacement</label>
                        <input
                            type="text"
                            x-model="form.location"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date d'acquisition *</label>
                        <input
                            type="date"
                            x-model="form.acquisition_date"
                            required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Valeur d'acquisition (€) *</label>
                        <input
                            type="number"
                            x-model="form.acquisition_value"
                            required
                            min="0"
                            step="0.01"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Durée d'amortissement (années) *</label>
                        <input
                            type="number"
                            x-model="form.amortization_years"
                            required
                            min="1"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fournisseur</label>
                        <input
                            type="text"
                            x-model="form.provider"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Numéro de série</label>
                        <input
                            type="text"
                            x-model="form.serial_number"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none"
                        >
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea
                            x-model="form.description"
                            rows="3"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none resize-none"
                        ></textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <a href="{{ route('assets.show', $id) }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Annuler
                    </a>
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
    </template>
</div>

<script>
function assetForm(assetId) {
    return {
        loading: true,
        asset: null,
        submitting: false,
        form: {
            code: '',
            designation: '',
            category: '',
            service_id: '',
            location: '',
            acquisition_date: '',
            acquisition_value: '',
            amortization_years: '',
            provider: '',
            serial_number: '',
            description: ''
        },

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
                this.form = { ...data };
            } catch (error) {
                console.error('Error loading asset:', error);
            } finally {
                this.loading = false;
            }
        },

        async submit() {
            this.submitting = true;
            try {
                const response = await fetch(`{{ env('API_URL') }}/api/assets/${assetId}`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': `Bearer {{ session('jwt_token') }}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(this.form)
                });

                if (response.ok) {
                    window.location.href = `{{ route('assets.show', $id) }}`;
                } else {
                    const error = await response.json();
                    alert(error.message || 'Erreur lors de la modification');
                }
            } catch (error) {
                console.error('Error updating asset:', error);
                alert('Erreur de connexion');
            } finally {
                this.submitting = false;
            }
        },

        init() {
            this.loadAsset();
        }
    }
}
</script>
