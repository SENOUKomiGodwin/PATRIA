@extends('components.layout-auth')

@section('title', 'Connexion')

@section('content')
<div class="h-screen flex overflow-hidden">
    <!-- Left panel - Branding -->
    <div class="relative w-[47.22%] h-full bg-gradient-to-b from-gradient-panel-start to-gradient-panel-end overflow-hidden flex flex-col justify-between login-panel p-12 text-white">
        <img
            src="{{ asset('images/armoiries-togo.png') }}"
            alt=""
            aria-hidden="true"
            class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 h-[80%] w-auto opacity-10 pointer-events-none select-none"
        >

        <div class="relative z-10">
            <!-- Logo -->
            <div class="login-logo mb-8">
                <h1 class="text-4xl font-bold tracking-tight">PATRIA</h1>
                <p class="text-xs text-white/70 mt-2 uppercase tracking-wider">GESTION PUBLIQUE</p>
            </div>

            <!-- Main content -->
            <div class="login-stack flex flex-col gap-8">
                <div>
                    <h2 class="login-title text-[52px] font-bold leading-[78px]">
                        Le patrimoine<br>
                        de l’État,<br>
                        maîtrisé
                    </h2>
                    <p class="login-subtitle text-lg text-white/80 leading-relaxed mt-4">
                        Une vision fiable de vos biens.<br>
                        Des décisions éclairées, au service du public.
                    </p>
                </div>

                <div class="w-16 h-1 bg-patria-gold"></div>

                <p class="login-tagline text-xl font-medium">Recenser. Suivre. Préserver.</p>

                <!-- Three pillars -->
                <div class="login-pillars grid grid-cols-[repeat(3,180px)] pl-6 pt-8">
                    <div>
                        <p class="login-num text-3xl font-bold text-patria-gold mb-2">01</p>
                        <p class="login-label text-lg font-medium">Traçabilité</p>
                    </div>
                    <div>
                        <p class="login-num text-3xl font-bold text-patria-gold mb-2">02</p>
                        <p class="login-label text-lg font-medium">Transparence</p>
                    </div>
                    <div>
                        <p class="login-num text-3xl font-bold text-patria-gold mb-2">03</p>
                        <p class="login-label text-lg font-medium">Durabilité</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="relative z-10">
            <p class="text-xs text-white/60">Plateforme de gestion du patrimoine de l’État</p>
        </div>
    </div>

    <!-- Right panel - Login form -->
    <div class="relative flex-1 h-full bg-white flex flex-col login-panel-right py-12 px-[104px]">
        <div class="flex-1 flex items-center justify-center min-h-0">
            <div class="login-col">
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
                    <p class="text-[11px] font-normal text-patria-text-secondary uppercase tracking-wider mb-2">ACCÈS SÉCURISÉ</p>
                    <h2 class="text-3xl font-bold text-patria-login-title mb-2">Bienvenue</h2>
                    <p class="text-patria-text-secondary mb-8">Connectez-vous à votre espace de gestion.</p>

                    <form method="POST" action="{{ route('login') }}" class="login-form" x-data="{ loading: false }" @submit="if(document.getElementById('password').value === '••••••••••••') document.getElementById('password').value = ''">
                        @csrf

                        <div class="space-y-5">
                            <x-input
                                label="Adresse e-mail professionnelle"
                                type="email"
                                name="email"
                                id="email"
                                value="{{ old('email') }}"
                                placeholder="nom@patrimoine.gov"
                                class="placeholder:text-patria-text-secondary"
                                required
                                autofocus
                                autocomplete="email"
                            />

                            <x-input
                                label="Mot de passe"
                                type="password"
                                name="password"
                                id="password"
                                value="••••••••••••"
                                required
                                autocomplete="current-password"
                            />

                            <p class="text-sm text-patria-text-secondary">
                                Votre accès dépend du rôle attribué par l’administrateur.
                            </p>

                            <x-button type="submit" variant="primary" class="w-full bg-login-gradient">
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

                    <p class="mt-8 text-xs text-patria-text-secondary">
                        Un problème de connexion ? <a href="#" class="text-patria-text-secondary no-underline hover:no-underline">Contactez votre administrateur.</a>
                    </p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="login-col mx-auto border-t border-patria-border pt-4">
            <p class="text-xs text-patria-text-secondary">Vos données sont protégées et vos actions tracées.</p>
            <p class="text-xs text-patria-text-secondary mt-3">© 2026 · PATRIA</p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Tab') {
            var form = document.querySelector('.login-form');
            if (form) form.dataset.kb = '1';
        }
    });
    document.addEventListener('mousedown', function () {
        var form = document.querySelector('.login-form');
        if (form) delete form.dataset.kb;
    });
</script>
@endsection
