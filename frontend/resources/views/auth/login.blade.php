@extends('layouts.auth')

@section('title', 'Connexion')

@section('content')
<div class="min-h-screen flex">
    <!-- Left panel - Branding -->
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-[#0C6744] to-[#7A5C00] items-center justify-center p-12">
        <div class="text-center text-white">
            <h1 class="text-5xl font-bold mb-4 tracking-tight">PATRIA</h1>
            <p class="text-xl font-light opacity-90">Le patrimoine de l'État, maîtrisé</p>
        </div>
    </div>

    <!-- Right panel - Login form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
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

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Connexion</h2>
                <p class="text-gray-600 mb-6">Accédez à votre espace de gestion</p>

                <form method="POST" action="{{ route('login') }}" x-data="{ loading: false }">
                    @csrf

                    <div class="space-y-4">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="email"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none transition-colors"
                                :disabled="loading"
                            >
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0C6744] focus:border-[#0C6744] outline-none transition-colors"
                                :disabled="loading"
                            >
                        </div>

                        <button
                            type="submit"
                            class="w-full py-2.5 px-4 bg-gradient-to-r from-[#2D753A] to-[#D7B305] text-white font-medium rounded-lg hover:opacity-90 transition-opacity disabled:opacity-50"
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

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-500">
                        Comptes de test :
                        <br>
                        <span class="text-gray-600">admin@patria.state.fr (ADMIN)</span>
                        <br>
                        <span class="text-gray-600">gestionnaire@patria.state.fr (GESTIONNAIRE)</span>
                        <br>
                        <span class="text-gray-600">agent@patria.state.fr (AGENT)</span>
                        <br>
                        <span class="text-gray-600">auditeur@patria.state.fr (AUDITEUR)</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
