@extends('layouts.app')

@section('title', 'Utilisateurs')
@section 'content'

<div class="space-y-6" x-data="usersPage()">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('dashboard') }}" class="hover:text-gray-900">Accueil</a>
        <span>/</span>
        <span class="text-gray-900">Utilisateurs</span>
    </nav>

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Utilisateurs</h1>
            <p class="text-gray-600 mt-1">Gestion des comptes</p>
        </div>
        @if (session('user_role') === 'ADMIN')
            <a href="{{ route('users.create') }}" class="px-4 py-2 bg-[#0C6744] text-white rounded-lg hover:bg-[#0A5336] transition-colors">
                Nouvel utilisateur
            </a>
        @endif
    </div>

    <!-- Users table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-sm text-gray-500 border-b border-gray-200 bg-gray-50">
                        <th class="px-6 py-4 font-medium">Nom</th>
                        <th class="px-6 py-4 font-medium">Email</th>
                        <th class="px-6 py-4 font-medium">Service</th>
                        <th class="px-6 py-4 font-medium">Rôle</th>
                        <th class="px-6 py-4 font-medium">Statut</th>
                        <th class="px-6 py-4 font-medium text-right">Actions</th>
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
                    <template x-if="!loading && users.length === 0">
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                Aucun utilisateur
                            </td>
                        </tr>
                    </template>
                    <template x-for="user in users" :key="user.id">
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900" x-text="user.name"></td>
                            <td class="px-6 py-4 text-gray-600" x-text="user.email"></td>
                            <td class="px-6 py-4 text-gray-600" x-text="user.service || '-'"></td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-medium"
                                    :class="{
                                        'bg-purple-100 text-purple-800': user.role === 'ADMIN',
                                        'bg-blue-100 text-blue-800': user.role === 'GESTIONNAIRE',
                                        'bg-green-100 text-green-800': user.role === 'AGENT',
                                        'bg-gray-100 text-gray-800': user.role === 'AUDITEUR'
                                    }"
                                    x-text="user.role"></span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-medium"
                                    :class="{
                                        'bg-green-100 text-green-800': user.active,
                                        'bg-red-100 text-red-800': !user.active
                                    }"
                                    x-text="user.active ? 'Actif' : 'Inactif'"></span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if (session('user_role') === 'ADMIN')
                                    <button @click="toggleUser(user)" class="text-gray-500 hover:text-gray-700 text-sm mr-3">
                                        <span x-text="user.active ? 'Désactiver' : 'Activer'"></span>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function usersPage() {
    return {
        loading: false,
        users: [],

        async loadUsers() {
            this.loading = true;
            try {
                const response = await fetch('{{ env('API_URL') }}/api/users', {
                    headers: {
                        'Authorization': `Bearer {{ session('jwt_token') }}`,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                this.users = data.data || [];
            } catch (error) {
                console.error('Error loading users:', error);
            } finally {
                this.loading = false;
            }
        },

        async toggleUser(user) {
            try {
                const response = await fetch(`{{ env('API_URL') }}/api/users/${user.id}`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': `Bearer {{ session('jwt_token') }}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ active: !user.active })
                });

                if (response.ok) {
                    this.loadUsers();
                } else {
                    const error = await response.json();
                    alert(error.message || 'Erreur lors de la modification');
                }
            } catch (error) {
                console.error('Error toggling user:', error);
                alert('Erreur de connexion');
            }
        },

        init() {
            this.loadUsers();
        }
    }
}
</script>
@endsection
