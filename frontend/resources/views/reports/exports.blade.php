@extends('layouts.app')

@section('title', 'Exports')

<div class="space-y-6" x-data="exportsPage()">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('dashboard') }}" class="hover:text-gray-900">Accueil</a>
        <span>/</span>
        <a href="{{ route('reports.index') }}" class="hover:text-gray-900">Rapports</a>
        <span>/</span>
        <span class="text-gray-900">Exports</span>
    </nav>

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Exports</h1>
            <p class="text-gray-600 mt-1">Télécharger les données</p>
        </div>
    </div>

    <!-- Export options -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Biens</h3>
            <div class="space-y-2">
                <button @click="exportData('assets', 'pdf')" class="w-full px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                    Export PDF
                </button>
                <button @click="exportData('assets', 'xlsx')" class="w-full px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                    Export Excel
                </button>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Mouvements</h3>
            <div class="space-y-2">
                <button @click="exportData('movements', 'pdf')" class="w-full px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                    Export PDF
                </button>
                <button @click="exportData('movements', 'xlsx')" class="w-full px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                    Export Excel
                </button>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Inventaires</h3>
            <div class="space-y-2">
                <button @click="exportData('inventory', 'pdf')" class="w-full px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                    Export PDF
                </button>
                <button @click="exportData('inventory', 'xlsx')" class="w-full px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                    Export Excel
                </button>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Maintenance</h3>
            <div class="space-y-2">
                <button @click="exportData('maintenance', 'pdf')" class="w-full px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                    Export PDF
                </button>
                <button @click="exportData('maintenance', 'xlsx')" class="w-full px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                    Export Excel
                </button>
            </div>
        </div>
    </div>

    <!-- Export history -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Historique des exports</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-sm text-gray-500 border-b border-gray-200">
                        <th class="pb-3 font-medium">Type</th>
                        <th class="pb-3 font-medium">Format</th>
                        <th class="pb-3 font-medium">Filtres</th>
                        <th class="pb-3 font-medium">Date</th>
                        <th class="pb-3 font-medium">Par</th>
                        <th class="pb-3 font-medium text-right">Actions</th>
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
                    <template x-if="!loading && exports.length === 0">
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                Aucun export
                            </td>
                        </tr>
                    </template>
                    <template x-for="export in exports" :key="export.id">
                        <tr class="border-b border-gray-100">
                            <td class="px-6 py-4 font-medium text-gray-900" x-text="export.type"></td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-medium"
                                    :class="{
                                        'bg-red-100 text-red-800': export.format === 'pdf',
                                        'bg-green-100 text-green-800': export.format === 'xlsx'
                                    }"
                                    x-text="export.format.toUpperCase()"></span>
                            </td>
                            <td class="px-6 py-4 text-gray-600 text-xs" x-text="JSON.stringify(export.filters)"></td>
                            <td class="px-6 py-4 text-gray-600" x-text="formatDate(export.created_at)"></td>
                            <td class="px-6 py-4 text-gray-600" x-text="export.requested_by"></td>
                            <td class="px-6 py-4 text-right">
                                <a :href="export.file_url" class="text-[#0C6744] hover:underline text-sm">Télécharger</a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function exportsPage() {
    return {
        loading: false,
        exports: [],

        async loadExports() {
            this.loading = true;
            try {
                const response = await fetch('{{ env('API_URL') }}/api/reports/exports', {
                    headers: {
                        'Authorization': `Bearer {{ session('jwt_token') }}`,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                this.exports = data.data || [];
            } catch (error) {
                console.error('Error loading exports:', error);
            } finally {
                this.loading = false;
            }
        },

        async exportData(type, format) {
            try {
                const response = await fetch(`{{ env('API_URL') }}/api/reports/exports/${type}?format=${format}`, {
                    headers: {
                        'Authorization': `Bearer {{ session('jwt_token') }}`,
                        'Accept': format === 'pdf' ? 'application/pdf' : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    }
                });

                if (response.ok) {
                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `${type}_export.${format}`;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    window.URL.revokeObjectURL(url);
                    this.loadExports();
                } else {
                    alert('Erreur lors de l\'export');
                }
            } catch (error) {
                console.error('Error exporting:', error);
                alert('Erreur de connexion');
            }
        },

        formatDate(date) {
            if (!date) return '-';
            return new Date(date).toLocaleDateString('fr-FR');
        },

        init() {
            this.loadExports();
        }
    }
}
</script>
