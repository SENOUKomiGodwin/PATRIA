<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PATRIA - {{ $title ?? 'Tableau de bord' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-[#F5F7FA] font-sans antialiased" x-data="{ sidebarOpen: true }">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'w-64' : 'w-20'" class="bg-[#0C6744] text-white flex flex-col transition-all duration-300 relative overflow-hidden">
            <!-- Logo -->
            <div class="p-6 border-b border-white/10">
                <h1 class="text-2xl font-bold tracking-tight">PATRIA</h1>
                <p x-show="sidebarOpen" class="text-xs text-white/70 mt-1 transition-opacity">ESPACE DE GESTION</p>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 py-6">
                <ul class="space-y-1 px-3">
                    <li>
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-white/20' : 'hover:bg-white/10' }} transition-colors">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            <span x-show="sidebarOpen" class="text-sm font-medium transition-opacity">Tableau de bord</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('assets.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('assets.*') ? 'bg-white/20' : 'hover:bg-white/10' }} transition-colors">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            <span x-show="sidebarOpen" class="text-sm font-medium transition-opacity">Biens</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('movements.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('movements.*') ? 'bg-white/20' : 'hover:bg-white/10' }} transition-colors">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            <span x-show="sidebarOpen" class="text-sm font-medium transition-opacity">Mouvements</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('inventories.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('inventories.*') ? 'bg-white/20' : 'hover:bg-white/10' }} transition-colors">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            <span x-show="sidebarOpen" class="text-sm font-medium transition-opacity">Inventaires</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('maintenance.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('maintenance.*') ? 'bg-white/20' : 'hover:bg-white/10' }} transition-colors">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span x-show="sidebarOpen" class="text-sm font-medium transition-opacity">Maintenance</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('reports.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('reports.*') ? 'bg-white/20' : 'hover:bg-white/10' }} transition-colors">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span x-show="sidebarOpen" class="text-sm font-medium transition-opacity">Rapports</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('users.*') ? 'bg-white/20' : 'hover:bg-white/10' }} transition-colors">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span x-show="sidebarOpen" class="text-sm font-medium transition-opacity">Utilisateurs</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Help link -->
            <div class="p-4 border-t border-white/10">
                <a href="#" x-show="sidebarOpen" class="text-xs text-white/80 hover:text-white transition-colors">
                    Besoin d'aide ? Consulter le guide d'utilisation →
                </a>
            </div>

            <!-- Gradient overlay at bottom -->
            <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-[#7A5C00] to-transparent pointer-events-none"></div>
        </aside>

        <!-- Main content -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Top bar -->
            <header class="bg-white border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Gestion du patrimoine public</h2>
                        <p class="text-sm text-gray-500 mt-0.5">
                            {{ session('user_service') ?? 'Service général' }} · {{ session('user_name') ?? 'Utilisateur' }} · {{ session('user_role') ?? 'Invité' }}
                        </p>
                    </div>
                    <div class="flex items-center gap-4">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-sm text-gray-600 hover:text-gray-900 transition-colors">
                                Déconnexion
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Page content -->
            <main class="flex-1 p-6 overflow-auto">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
