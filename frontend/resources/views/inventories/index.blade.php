@extends('components.layout-app')

@section('title', "Campagnes d’inventaire")

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-patria-green-text">Campagnes d’inventaire</h1>
            <p class="text-sm text-patria-text-secondary mt-1">Organisez le comptage physique et suivez les écarts de votre patrimoine.</p>
        </div>
        <x-button variant="primary">+ Nouvelle campagne</x-button>
    </div>

    <!-- KPI cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-kpi-card title="Campagne ouverte" value="1" subtitle="À poursuivre" />
        <x-kpi-card title="Biens comptés" value="172 / 240" subtitle="Campagne de novembre" />
        <x-kpi-card title="Conformité du dernier inventaire" value="91,3 %" subtitle="Campagne d’octobre clôturée" />
    </div>

    <!-- Tabs card -->
    <x-card padding="py-0 px-6">
        <div class="flex items-end border-b border-patria-border gap-6 pb-0" style="height: 64px;">
            <button class="pb-2 border-b-2 border-patria-green text-patria-green-text font-semibold">Toutes (3)</button>
            <button class="pb-2 border-b-2 border-transparent text-patria-text-secondary hover:text-patria-text">Ouvertes (1)</button>
            <button class="pb-2 border-b-2 border-transparent text-patria-text-secondary hover:text-patria-text">Clôturées (2)</button>
        </div>
    </x-card>

    <!-- Campaign cards -->
    <div class="space-y-6">
        <!-- Campaign 1 -->
        <x-card>
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <h3 class="text-base font-semibold text-patria-green-text mb-1">Inventaire · Novembre 2026</h3>
                    <p class="text-sm text-patria-text-secondary mb-3">Administration centrale · 01–30 nov. 2026</p>
                    <x-badge variant="warning">Ouverte</x-badge>
                </div>
                <div class="ml-6 text-left" style="width: 300px;">
                    <p class="text-sm text-patria-green-text font-normal">172 / 240 biens</p>
                    <div class="w-full h-2 bg-patria-green/10 rounded-full overflow-hidden mt-2">
                        <div class="h-full bg-patria-green" style="width: 72%"></div>
                    </div>
                    <p class="text-sm text-patria-text-secondary mt-1">72 % · 68 biens à compter</p>
                </div>
                <div class="ml-6" style="width: 120px; display: flex; justify-content: flex-end;">
                    <a href="/inventaires/1" class="px-4 py-2 bg-patria-green-button text-white rounded-button hover:opacity-90 transition-colors font-semibold text-sm">Poursuivre</a>
                </div>
            </div>
        </x-card>

        <!-- Campaign 2 -->
        <x-card>
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <h3 class="text-base font-semibold text-patria-green-text mb-1">Inventaire · Octobre 2026</h3>
                    <p class="text-sm text-patria-text-secondary mb-3">Administration centrale · 01–31 oct. 2026</p>
                    <x-badge variant="default">Clôturée</x-badge>
                </div>
                <div class="ml-6 text-left" style="width: 300px;">
                    <p class="text-sm text-patria-green-text font-normal">240 / 240 biens</p>
                    <div class="w-full h-2 bg-patria-green/10 rounded-full overflow-hidden mt-2">
                        <div class="h-full bg-patria-green" style="width: 100%"></div>
                    </div>
                    <p class="text-sm text-patria-text-secondary mt-1">100 % · Campagne terminée</p>
                </div>
                <div class="ml-6" style="width: 120px; display: flex; justify-content: flex-end;">
                    <x-button variant="secondary" size="md">Consulter</x-button>
                </div>
            </div>
        </x-card>

        <!-- Campaign 3 -->
        <x-card>
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <h3 class="text-base font-semibold text-patria-green-text mb-1">Inventaire · Septembre 2026</h3>
                    <p class="text-sm text-patria-text-secondary mb-3">Administration centrale · 01–30 sept. 2026</p>
                    <x-badge variant="default">Clôturée</x-badge>
                </div>
                <div class="ml-6 text-left" style="width: 300px;">
                    <p class="text-sm text-patria-green-text font-normal">232 / 232 biens</p>
                    <div class="w-full h-2 bg-patria-green/10 rounded-full overflow-hidden mt-2">
                        <div class="h-full bg-patria-green" style="width: 100%"></div>
                    </div>
                    <p class="text-sm text-patria-text-secondary mt-1">100 % · Campagne terminée</p>
                </div>
                <div class="ml-6" style="width: 120px; display: flex; justify-content: flex-end;">
                    <x-button variant="secondary" size="md">Consulter</x-button>
                </div>
            </div>
        </x-card>
    </div>
</div>
@endsection
