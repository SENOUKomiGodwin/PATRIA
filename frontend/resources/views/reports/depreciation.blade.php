@extends('layouts.app')

@section('title', 'Tableau d\'amortissement')

<div class="space-y-6" x-data="depreciationPage()">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('dashboard') }}" class="hover:text-gray-900">Accueil</a>
        <span>/</span>
        <a href="{{ route('reports.index') }}" class="hover:text-gray-900">Rapports</a>
        <span>/</span>
        <span class="text-gray-900">Amortissement</span>
    </nav>

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tableau d'amortissement</h1>
            <p class="text-gray-600 mt-1">VNC et dotations par année</p>
        </div>
        <div class="flex items-center gap-3">
            <select x-model="selectedYear" @change="loadDepreciation()" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none">
                <template x-for="year in availableYears" :key="year">
                    <option :value="year" x-text="year"></option>
                </template>
            </select>
            <button @click="exportData('pdf')" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Export PDF
            </button>
            <button @click="exportData('xlsx')" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Export Excel
            </button>
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

    <template x-if="!loading && depreciation">
        <div class="space-y-6">
            <!-- Summary cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <p class="text-sm text-gray-500 mb-1">Dotation de l'année</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="formatCurrency(depreciation.annual_depreciation || 0)"></p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <p class="text-sm text-gray-500 mb-1">Cumul amortissements</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="formatCurrency(depreciation.accumulated_depreciation || 0)"></p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <p class="text-sm text-gray-500 mb-1">VNC totale</p>
                    <p class="text-2xl font-bold text-[#0C6744]" x-text="formatCurrency(depreciation.total_net_value || 0)"></p>
                </div>
            </div>

            <!-- By category table -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Par catégorie</h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-sm text-gray-500 border-b border-gray-200 bg-gray-50">
                                <th class="px-6 py-4 font-medium">Catégorie</th>
                                <th class="px-6 py-4 font-medium text-right">Dotation</th>
                                <th class="px-6 py-4 font-medium text-right">Cumul</th>
                                <th class="px-6 py-4 font-medium text-right">VNC</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            <template x-for="item in depreciation.by_category" :key="item.category">
                                <tr class="border-b border-gray-100">
                                    <td class="px-6 py-4 font-medium text-gray-900" x-text="item.category"></td>
                                    <td class="px-6 py-4 text-right text-gray-600" x-text="formatCurrency(item.annual_depreciation)"></td>
                                    <td class="px-6 py-4 text-right text-gray-600" x-text="formatCurrency(item.accumulated_depreciation)"></td>
                                    <td class="px-6 py-4 text-right font-medium text-gray-900" x-text="formatCurrency(item.net_value)"></td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 border-gray-200 font-semibold">
                                <td class="px-6 py-4 text-gray-900">Total</td>
                                <td class="px-6 py-4 text-right text-gray-900" x-text="formatCurrency(depreciation.annual_depreciation)"></td>
                                <td class="px-6 py-4 text-right text-gray-900" x-text="formatCurrency(depreciation.accumulated_depreciation)"></td>
                                <td class="px-6 py-4 text-right text-gray-900" x-text="formatCurrency(depreciation.total_net_value)"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
function depreciationPage() {
    return {
        loading: false,
        depreciation: null,
        selectedYear: new Date().getFullYear(),
        availableYears: [2024, 2023, 2022, 2021, 2020],

        async loadDepreciation() {
            this.loading = true;
            try {
                const response = await fetch(`{{ env('API_URL') }}/api/reports/depreciation?year=${this.selectedYear}`, {
                    headers: {
                        'Authorization': `Bearer {{ session('jwt_token') }}`,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                this.depreciation = data;
            } catch (error) {
                console.error('Error loading depreciation:', error);
            } finally {
                this.loading = false;
            }
        },

        async exportData(format) {
            try {
                const response = await fetch(`{{ env('API_URL') }}/api/reports/exports/depreciation?format=${format}&year=${this.selectedYear}`, {
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
                    a.download = `amortissement_${this.selectedYear}.${format}`;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    window.URL.revokeObjectURL(url);
                } else {
                    alert('Erreur lors de l\'export');
                }
            } catch (error) {
                console.error('Error exporting:', error);
                alert('Erreur de connexion');
            }
        },

        formatCurrency(value) {
            return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(value);
        },

        init() {
            this.loadDepreciation();
        }
    }
}
</script>
