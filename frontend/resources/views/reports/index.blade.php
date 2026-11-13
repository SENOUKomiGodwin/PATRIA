@extends('layouts.app')

@section('title', 'Rapports')

<div class="space-y-6" x-data="reportsPage()">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('dashboard') }}" class="hover:text-gray-900">Accueil</a>
        <span>/</span>
        <span class="text-gray-900">Rapports</span>
    </nav>

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Rapports</h1>
            <p class="text-gray-600 mt-1">Exports et analyses</p>
        </div>
    </div>

    <!-- Quick links -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="{{ route('reports.depreciation') }}" class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-md transition-shadow block">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">Tableau d'amortissement</h3>
                    <p class="text-sm text-gray-500 mt-1">VNC et dotations par année</p>
                </div>
            </div>
        </a>

        <a href="{{ route('reports.exports') }}" class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-md transition-shadow block">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">Exports</h3>
                    <p class="text-sm text-gray-500 mt-1">PDF et Excel</p>
                </div>
            </div>
        </a>

        <a href="{{ route('dashboard') }}" class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-md transition-shadow block">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">Tableau de bord</h3>
                    <p class="text-sm text-gray-500 mt-1">KPI et indicateurs</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Recent exports -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Exports récents</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-sm text-gray-500 border-b border-gray-200">
                        <th class="pb-3 font-medium">Type</th>
                        <th class="pb-3 font-medium">Format</th>
                        <th class="pb-3 font-medium">Date</th>
                        <th class="pb-3 font-medium">Par</th>
                        <th class="pb-3 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    <template x-if="loading">
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
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
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                Aucun export récent
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
                            <td class="px-6 py-4 text-gray-600" x-text="formatDate(created_at)"></td>
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
function reportsPage() {
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
