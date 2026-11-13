@extends('layouts.app')

@section('title', 'Nouvel utilisateur')

<div class="space-y-6" x-data="userForm()">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('dashboard') }}" class="hover:text-gray-900">Accueil</a>
        <span>/</span>
        <a href="{{ route('users.index') }}" class="hover:text-gray-900">Utilisateurs</a>
        <span>/</span>
        <span class="text-gray-900">Nouvel utilisateur</span>
    </nav>

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Nouvel utilisateur</h1>
            <p class="text-gray-600 mt-1">Créer un compte</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form @submit.prevent="submit">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                    <input
                        type="text"
                        x-model="form.name"
                        required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none"
                        placeholder="Nom complet"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input
                        type="email"
                        x-model="form.email"
                        required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none"
                        placeholder="email@exemple.fr"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe *</label>
                    <input
                        type="password"
                        x-model="form.password"
                        required
                        minlength="8"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none"
                        placeholder="Min. 8 caractères"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirmer mot de passe *</label>
                    <input
                        type="password"
                        x-model="form.password_confirmation"
                        required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none"
                        placeholder="Confirmer"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Service</label>
                    <input
                        type="text"
                        x-model="form.service"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none"
                        placeholder="Direction Générale, RH, Finance..."
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rôle *</label>
                    <select x-model="form.role" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none">
                        <option value="">Sélectionner...</option>
                        <option value="ADMIN">ADMIN</option>
                        <option value="GESTIONNAIRE">GESTIONNAIRE</option>
                        <option value="AGENT">AGENT</option>
                        <option value="AUDITEUR">AUDITEUR</option>
                    </select>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('users.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Annuler
                </a>
                <button
                    type="submit"
                    :disabled="submitting"
                    class="px-4 py-2 bg-[#0C6744] text-white rounded-lg hover:bg-[#0A5336] transition-colors disabled:opacity-50"
                >
                    <span x-show="!submitting">Créer l'utilisateur</span>
                    <span x-show="submitting">Création...</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function userForm() {
    return {
        submitting: false,
        form: {
            name: '',
            email: '',
            password: '',
            password_confirmation: '',
            service: '',
            role: ''
        },

        async submit() {
            if (this.form.password !== this.form.password_confirmation) {
                alert('Les mots de passe ne correspondent pas');
                return;
            }

            this.submitting = true;
            try {
                const response = await fetch('{{ env('API_URL') }}/api/users', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer {{ session('jwt_token') }}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(this.form)
                });

                if (response.ok) {
                    window.location.href = '{{ route('users.index') }}';
                } else {
                    const error = await response.json();
                    alert(error.message || 'Erreur lors de la création');
                }
            } catch (error) {
                console.error('Error creating user:', error);
                alert('Erreur de connexion');
            } finally {
                this.submitting = false;
            }
        }
    }
}
</script>
