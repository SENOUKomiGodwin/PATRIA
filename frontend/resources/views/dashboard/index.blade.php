@extends('layouts.app')

@section('title', 'Tableau de bord')

<div class="space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('dashboard') }}" class="hover:text-gray-900">Accueil</a>
        <span>/</span>
        <span class="text-gray-900">Tableau de bord</span>
    </nav>

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tableau de bord</h1>
            <p class="text-gray-600 mt-1">Vue d'ensemble du patrimoine</p>
        </div>
    </div>

    @if ($error)
        <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-sm text-red-800">{{ $error }}</p>
        </div>
    @endif

    @if ($dashboard)
        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Total des biens</p>
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($dashboard['total_assets'] ?? 0) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-[#0C6744]/10 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-[#0C6744]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Valeur d'acquisition</p>
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($dashboard['total_acquisition_value'] ?? 0, 0, ',', ' ') }} €</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Valeur nette comptable</p>
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($dashboard['total_net_value'] ?? 0, 0, ',', ' ') }} €</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Coût maintenance</p>
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($dashboard['maintenance_cost'] ?? 0, 0, ',', ' ') }} €</p>
                    </div>
                    <div class="w-12 h-12 bg-[#F2B705]/10 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-[#F2B705]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Category distribution -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Répartition par catégorie</h3>
                <div class="space-y-3">
                    @foreach (($dashboard['by_category'] ?? []) as $category)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">{{ $category['name'] ?? 'Autre' }}</span>
                            <div class="flex items-center gap-3">
                                <div class="w-32 h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-[#0C6744]" style="width: {{ $category['percentage'] ?? 0 }}%"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-900">{{ $category['count'] ?? 0 }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Status distribution -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Répartition par statut</h3>
                <div class="space-y-3">
                    @foreach (($dashboard['by_status'] ?? []) as $status)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">{{ $status['name'] ?? 'Autre' }}</span>
                            <div class="flex items-center gap-3">
                                <div class="w-32 h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full {{ $status['color'] ?? 'bg-gray-400' }}" style="width: {{ $status['percentage'] ?? 0 }}%"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-900">{{ $status['count'] ?? 0 }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Recent movements -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Derniers mouvements</h3>
                <a href="{{ route('movements.index') }}" class="text-sm text-[#0C6744] hover:underline">Voir tout</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-sm text-gray-500 border-b border-gray-200">
                            <th class="pb-3 font-medium">Bien</th>
                            <th class="pb-3 font-medium">Type</th>
                            <th class="pb-3 font-medium">Date</th>
                            <th class="pb-3 font-medium">Par</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @foreach (($dashboard['recent_movements'] ?? []) as $movement)
                            <tr class="border-b border-gray-100">
                                <td class="py-3 text-gray-900">{{ $movement['asset_code'] ?? '-' }}</td>
                                <td class="py-3">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium
                                        {{ $movement['type'] === 'AFFECTATION' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $movement['type'] === 'TRANSFERT' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $movement['type'] === 'CESSION' ? 'bg-red-100 text-red-800' : '' }}
                                        {{ $movement['type'] === 'REFORME' ? 'bg-gray-100 text-gray-800' : '' }}">
                                        {{ $movement['type'] ?? '-' }}
                                    </span>
                                </td>
                                <td class="py-3 text-gray-600">{{ $movement['date'] ?? '-' }}</td>
                                <td class="py-3 text-gray-600">{{ $movement['by'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                        @if (empty($dashboard['recent_movements'] ?? []))
                            <tr>
                                <td colspan="4" class="py-8 text-center text-gray-500">Aucun mouvement récent</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Inventory compliance -->
        @if (isset($dashboard['inventory_compliance']))
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Taux de conformité</h3>
                <div class="flex items-center gap-6">
                    <div class="relative w-32 h-32">
                        <svg class="w-full h-full transform -rotate-90">
                            <circle cx="64" cy="64" r="56" stroke="#E5E7EB" stroke-width="12" fill="none"/>
                            <circle cx="64" cy="64" r="56" stroke="#0C6744" stroke-width="12" fill="none"
                                stroke-dasharray="{{ $dashboard['inventory_compliance'] * 3.52 }} 352"
                                stroke-linecap="round"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-2xl font-bold text-gray-900">{{ $dashboard['inventory_compliance'] }}%</span>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Dernière campagne d'inventaire</p>
                        <p class="text-sm text-gray-500 mt-1">{{ $dashboard['last_campaign_date'] ?? '-' }}</p>
                    </div>
                </div>
            </div>
        @endif
    @else
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-gray-500">Aucune donnée disponible</p>
        </div>
    @endif
</div>
