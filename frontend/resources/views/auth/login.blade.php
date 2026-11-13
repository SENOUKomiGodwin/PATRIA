@extends('layouts.auth')

@section('title', 'Connexion')

@section('content')
<div class="min-h-screen flex">
    <!-- Left panel - Branding -->
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-[#0C6744] to-[#7A5C00] items-center justify-center p-12">
        <div class="text-center text-white max-w-md">
            <h1 class="text-6xl font-bold mb-6 tracking-tight">PATRIA</h1>
            <p class="text-2xl font-light opacity-90 mb-12">Le patrimoine de l'État, maîtrisé</p>

            <div class="space-y-6 text-left">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg">Traçabilité</h3>
                        <p class="text-sm opacity-80 mt-1">Suivi complet de chaque bien</p>
                    </div>
                </div>

                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg">Transparence</h3>
                        <p class="text-sm opacity-80 mt-1">Données accessibles et auditées</p>
                    </div>
                </div>

                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg">Durabilité</h3>
                        <p class="text-sm opacity-80 mt-1">Maintenance proactive et optimisée</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right panel - Login form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-white">
        <div class="w-full max-w-md">
            <!-- Session expired message -->
            @if (session('session_expired'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-sm text-red-800">Votre session a expiré. Veuillez vous reconnecter.</p>
                </div>
            @endif

            <!-- Login error -->
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-sm text-red-800 font-medium">Connexion impossible</p>
                    <p class="text-sm text-red-700 mt-1">{{ $errors->first() }}</p>
                </div>
            @endif

            <div>
                <h2 class="text-2xl font-bold text-[#0C6744] mb-1 tracking-wide">ACCÈS SÉCURISÉ</h2>
                <p class="text-3xl font-bold text-gray-900 mb-8">Bienvenue</p>

                <form method="POST" action="{{ route('login') }}" x-data="{ loading: false }" @submit="if(document.getElementById('password').value === '********') document.getElementById('password').value = ''">
                    @csrf

                    <div class="space-y-5">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Adresse e-mail professionnelle</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') ?: 'nom@patrimoine.gov' }}"
                                required
                                autofocus
                                autocomplete="email"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none transition-colors"
                                :disabled="loading"
                            >
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Mot de passe</label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                value="********"
                                required
                                autocomplete="current-password"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none transition-colors"
                                :disabled="loading"
                            >
                        </div>

                        <button
                            type="submit"
                            class="w-full py-3 px-4 bg-gradient-to-r from-[#2D753A] to-[#D7B305] text-white font-medium rounded-lg hover:opacity-90 transition-opacity disabled:opacity-50"
                            :disabled="loading"
                        >
                            <span x-show="!loading">Se connecter</span>
                            <span x-show="loading" class="flex items-center justify-center gap-2">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Connexion...
                            </span>
                        </button>
                    </div>
                </form>

                <div class="mt-8 space-y-3 text-sm text-gray-500">
                    <p>Accès selon votre rôle : ADMIN, GESTIONNAIRE, AGENT ou AUDITEUR</p>
                    <p>Problème de connexion ? <a href="#" class="text-[#0C6744] hover:underline">Contactez le support technique</a></p>
                    <p class="text-xs">Vos données sont protégées conformément à la réglementation en vigueur</p>
                    <p class="text-xs">© 2024 PATRIA - Tous droits réservés</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
