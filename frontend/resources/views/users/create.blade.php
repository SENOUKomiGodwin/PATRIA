@extends('components.layout-app')

@section('title', 'Nouvel utilisateur')

@section('content')

<div class="space-y-6" x-data="userForm()">
    <nav class="flex items-center gap-2 text-sm text-patria-text-secondary">
        <a href="{{ route('dashboard') }}" class="hover:text-patria-text">Accueil</a>
        <span>/</span>
        <a href="{{ route('users.index') }}" class="hover:text-patria-text">Utilisateurs</a>
        <span>/</span>
        <span class="text-patria-text">Nouvel utilisateur</span>
    </nav>

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-patria-green-text">Nouvel utilisateur</h1>
            <p class="text-sm text-patria-text-secondary mt-1">Créer un compte</p>
        </div>
    </div>

    <x-card>
        <form @submit.prevent="submit">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-input
                    label="Nom"
                    type="text"
                    x-model="form.name"
                    required
                    placeholder="Nom complet"
                />

                <x-input
                    label="Email"
                    type="email"
                    x-model="form.email"
                    required
                    placeholder="email@exemple.fr"
                />

                <x-input
                    label="Mot de passe"
                    type="password"
                    x-model="form.password"
                    required
                    minlength="8"
                    placeholder="Min. 8 caractères"
                />

                <x-input
                    label="Confirmer mot de passe"
                    type="password"
                    x-model="form.password_confirmation"
                    required
                    placeholder="Confirmer"
                />

                <x-input
                    label="Service"
                    type="text"
                    x-model="form.service"
                    placeholder="Direction Générale, RH, Finance..."
                />

                <x-select label="Rôle" required x-model="form.role">
                    <option value="">Sélectionner...</option>
                    <option value="ADMIN">ADMIN</option>
                    <option value="GESTIONNAIRE">GESTIONNAIRE</option>
                    <option value="AGENT">AGENT</option>
                    <option value="AUDITEUR">AUDITEUR</option>
                </x-select>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('users.index') }}" class="inline-flex items-center justify-center h-11 px-4 border border-patria-border text-patria-green-text rounded-button hover:bg-patria-bg transition-colors text-sm font-semibold">
                    Annuler
                </a>
                <x-button
                    type="submit"
                    variant="primary"
                    x-bind:disabled="submitting"
                >
                    <span x-show="!submitting">Créer l’utilisateur</span>
                    <span x-show="submitting">Création...</span>
                </x-button>
            </div>
        </form>
    </x-card>
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
@endsection
