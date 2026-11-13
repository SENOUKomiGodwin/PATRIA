@extends('components.layout-auth')

@section('title', 'Connexion')

@section('content')
<div class="min-h-screen flex">
    <!-- Left panel - Branding -->
    <div class="w-1/2 bg-gradient-to-b from-gradient-panel-start to-gradient-panel-end flex flex-col justify-between p-12 text-white">
        <div>
            <!-- Logo -->
            <div class="mb-8">
                <h1 class="text-4xl font-bold tracking-tight">PATRIA</h1>
                <p class="text-xs text-white/70 mt-2 uppercase tracking-wider">GESTION PUBLIQUE</p>
            </div>

            <!-- Main content -->
            <div class="space-y-8">
                <div>
                    <h2 class="text-4xl font-bold leading-tight mb-4">
                        Le patrimoine<br>
                        de l'État,<br>
                        maîtrisé
                    </h2>
                    <p class="text-lg text-white/80 leading-relaxed">
                        Une vision fiable de vos biens.<br>
                        Des décisions éclairées, au service du public.
                    </p>
                </div>

                <div class="w-16 h-1 bg-patria-gold"></div>

                <p class="text-xl font-medium">Recenser. Suivre. Préserver.</p>

                <!-- Three pillars -->
                <div class="grid grid-cols-3 gap-8 pt-8">
                    <div>
                        <p class="text-3xl font-bold text-patria-gold mb-2">01</p>
                        <p class="text-lg font-medium">Traçabilité</p>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-patria-gold mb-2">02</p>
                        <p class="text-lg font-medium">Transparence</p>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-patria-gold mb-2">03</p>
                        <p class="text-lg font-medium">Durabilité</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-12">
            <p class="text-sm text-white/60">Plateforme de gestion du patrimoine de l'État</p>
        </div>
    </div>

    <!-- Right panel - Login form -->
    <div class="w-1/2 bg-white flex items-center justify-center p-12">
        <div class="w-full max-w-md">
            <!-- Session expired message -->
            @if (session('session_expired'))
                <div class="mb-6 p-4 bg-patria-red/10 border border-patria-red/20 rounded-card">
                    <p class="text-sm text-patria-red">Votre session a expiré. Veuillez vous reconnecter.</p>
                </div>
            @endif

            <!-- Login error -->
            @if ($errors->any())
                <div class="mb-6 p-4 bg-patria-red/10 border border-patria-red/20 rounded-card">
                    <p class="text-sm text-patria-red font-medium">Connexion impossible</p>
                    <p class="text-sm text-patria-red/80 mt-1">{{ $errors->first() }}</p>
                </div>
            @endif

            <div>
                <p class="text-sm font-medium text-patria-text-secondary uppercase tracking-wider mb-2">ACCÈS SÉCURISÉ</p>
                <h2 class="text-3xl font-bold text-patria-green mb-2">Bienvenue</h2>
                <p class="text-patria-text-secondary mb-8">Connectez-vous à votre espace de gestion.</p>

                <form method="POST" action="{{ route('login') }}" x-data="{ loading: false }" @submit="if(document.getElementById('password').value === '********') document.getElementById('password').value = ''">
                    @csrf

                    <div class="space-y-5">
                        <x-input
                            label="Adresse e-mail professionnelle"
                            type="email"
                            name="email"
                            value="{{ old('email') ?: 'nom@patrimoine.gov' }}"
                            required
                            autofocus
                            autocomplete="email"
                        />

                        <x-input
                            label="Mot de passe"
                            type="password"
                            name="password"
                            value="********"
                            required
                            autocomplete="current-password"
                        />

                        <p class="text-sm text-patria-text-secondary">
                            Votre accès dépend du rôle attribué par l'administrateur.
                        </p>

                        <x-button type="submit" variant="primary" class="w-full">
                            <span x-show="!loading">Se connecter</span>
                            <span x-show="loading" class="flex items-center justify-center gap-2">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Connexion...
                            </span>
                        </x-button>
                    </div>
                </form>

                <div class="mt-8 space-y-3 text-sm">
                    <p class="text-patria-text-secondary">
                        Un problème de connexion ? <a href="#" class="text-patria-green hover:underline">Contactez votre administrateur.</a>
                    </p>

                    <div class="border-t border-patria-border pt-4">
                        <p class="text-patria-text-secondary text-xs">
                            Vos données sont protégées et vos actions tracées.
                        </p>
                        <p class="text-patria-text-secondary text-xs mt-1">
                            © 2026 · PATRIA
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
