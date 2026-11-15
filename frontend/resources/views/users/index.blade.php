@extends('components.layout-app')

@section('title', "Utilisateurs")

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-patria-green-text">Utilisateurs</h1>
            <p class="text-sm text-patria-text-secondary mt-1">Administrez les accès, les rôles et le périmètre des utilisateurs.</p>
        </div>
        <x-button variant="primary">+ Nouvel utilisateur</x-button>
    </div>

    <!-- Info Banner -->
    <div class="bg-patria-green-light rounded-lg px-4 py-3">
        <p class="text-sm font-semibold text-patria-green-text">Administration des accès</p>
        <p class="text-xs text-sm text-patria-text-secondary mt-1">Chaque compte dispose d’un rôle et d’un service de rattachement.</p>
    </div>

    <!-- Filters Card -->
    <x-card>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-patria-green-text mb-1">Rechercher</label>
                <x-input placeholder="Nom ou adresse e-mail" />
            </div>
            <div>
                <label class="block text-sm font-medium text-patria-green-text mb-1">Rôle</label>
                <x-select>
                    <option>Tous les rôles</option>
                </x-select>
            </div>
            <div>
                <label class="block text-sm font-medium text-patria-green-text mb-1">État</label>
                <x-select>
                    <option>Actifs</option>
                </x-select>
            </div>
        </div>
    </x-card>

    <!-- Users count -->
    <p class="text-patria-green-text font-semibold">4 utilisateurs actifs</p>

    <!-- Table Card -->
    <x-card>
        <x-table :headers="['Nom', 'Adresse e-mail', 'Rôle', 'Service', 'État', '']">
            <tr class="border-b border-patria-border">
                <td class="px-6 py-4 text-patria-green-text">Aminata Diallo</td>
                <td class="px-6 py-4">admin@patrimoine.gov</td>
                <td class="px-6 py-4">Admin</td>
                <td class="px-6 py-4">Administration</td>
                <td class="px-6 py-4">
                    <x-badge variant="success">Actif</x-badge>
                </td>
                <td class="px-6 py-4 text-right">
                    <a href="#" class="text-patria-green-text hover:underline">Modifier</a>
                </td>
            </tr>
            <tr class="border-b border-patria-border">
                <td class="px-6 py-4 text-patria-green-text">Moussa Ndiaye</td>
                <td class="px-6 py-4">gestionnaire@patrimoine.gov</td>
                <td class="px-6 py-4">Gestionnaire</td>
                <td class="px-6 py-4">Moyens généraux</td>
                <td class="px-6 py-4">
                    <x-badge variant="success">Actif</x-badge>
                </td>
                <td class="px-6 py-4 text-right">
                    <a href="#" class="text-patria-green-text hover:underline">Modifier</a>
                </td>
            </tr>
            <tr class="border-b border-patria-border">
                <td class="px-6 py-4 text-patria-green-text">Ibrahima Fall</td>
                <td class="px-6 py-4">agent@patrimoine.gov</td>
                <td class="px-6 py-4">Agent</td>
                <td class="px-6 py-4">Logistique</td>
                <td class="px-6 py-4">
                    <x-badge variant="success">Actif</x-badge>
                </td>
                <td class="px-6 py-4 text-right">
                    <a href="#" class="text-patria-green-text hover:underline">Modifier</a>
                </td>
            </tr>
            <tr>
                <td class="px-6 py-4 text-patria-green-text">Fatou Diop</td>
                <td class="px-6 py-4">auditeur@patrimoine.gov</td>
                <td class="px-6 py-4">Auditeur</td>
                <td class="px-6 py-4">Audit interne</td>
                <td class="px-6 py-4">
                    <x-badge variant="success">Actif</x-badge>
                </td>
                <td class="px-6 py-4 text-right">
                    <a href="#" class="text-patria-green-text hover:underline">Modifier</a>
                </td>
            </tr>
        </x-table>
    </x-card>

    <!-- Roles Info Card -->
    <x-card>
        <h3 class="text-base font-semibold text-patria-green-text mb-3">Des accès adaptés à chaque rôle</h3>
        <div class="space-y-2 text-sm text-patria-text-secondary">
            <p>Admin · Tous les droits &nbsp;&nbsp; Gestionnaire · Gestion de son service &nbsp;&nbsp; Agent · Comptage et déclaration de panne</p>
            <p>Auditeur · Consultation et exports uniquement, sans action d’écriture.</p>
        </div>
    </x-card>
</div>
@endsection
