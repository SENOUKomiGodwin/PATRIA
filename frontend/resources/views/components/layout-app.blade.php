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
<body class="bg-patria-bg font-sans antialiased h-screen overflow-hidden">
    <div class="flex h-full">
        <!-- Sidebar (full height) -->
        <aside class="w-60 bg-gradient-to-b from-sidebar-start to-sidebar-end text-white flex flex-col relative overflow-hidden shrink-0 h-full">
            <!-- Logo -->
            <div class="px-6 pt-6 pb-3">
                <h1 class="text-2xl font-bold tracking-tight">PATRIA</h1>
                <div class="h-px bg-white/20 my-3"></div>
                <p class="text-xs text-white/70 uppercase tracking-wider">ESPACE DE GESTION</p>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 pt-1 pb-2 overflow-y-auto scrollbar-hide">
                <ul class="flex flex-col gap-3 px-3 sidebar-nav">
                    <li>
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-patria-sidebar-dark font-bold border-l-4 border-patria-gold' : 'hover:bg-white/10' }} transition-colors">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
                            </svg>
                            <span class="text-sm">Tableau de bord</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('assets.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('assets.*') ? 'bg-patria-sidebar-dark font-bold border-l-4 border-patria-gold' : 'hover:bg-white/10' }} transition-colors">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/>
                            </svg>
                            <span class="text-sm">Biens</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('movements.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('movements.*') ? 'bg-patria-sidebar-dark font-bold border-l-4 border-patria-gold' : 'hover:bg-white/10' }} transition-colors">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            <span class="text-sm">Mouvements</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('inventories.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('inventories.*') ? 'bg-patria-sidebar-dark font-bold border-l-4 border-patria-gold' : 'hover:bg-white/10' }} transition-colors">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            <span class="text-sm">Inventaires</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('maintenance.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('maintenance.*') ? 'bg-patria-sidebar-dark font-bold border-l-4 border-patria-gold' : 'hover:bg-white/10' }} transition-colors">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/>
                            </svg>
                            <span class="text-sm">Maintenance</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('reports.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('reports.*') ? 'bg-patria-sidebar-dark font-bold border-l-4 border-patria-gold' : 'hover:bg-white/10' }} transition-colors">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="text-sm">Rapports</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('users.*') ? 'bg-patria-sidebar-dark font-bold border-l-4 border-patria-gold' : 'hover:bg-white/10' }} transition-colors">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span class="text-sm">Utilisateurs</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Help card -->
            <div class="p-4">
                <div class="bg-white/10 rounded-lg p-4">
                    <p class="text-sm font-bold">Besoin d’aide ?</p>
                    <p class="text-xs text-white/80 mt-1">Consulter le guide d’utilisation →</p>
                </div>
            </div>

            <!-- Version -->
            <div class="p-4">
                <p class="text-xs text-white/60">Version de démonstration · 2026</p>
            </div>
        </aside>

        <!-- Main content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top bar -->
            <header class="bg-white border-b border-patria-border px-6 py-4 shrink-0">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-normal text-patria-green-text/70">Gestion du patrimoine public</h2>
                    <div class="flex items-center gap-4">
                        <span class="text-sm text-patria-text-secondary">Administration centrale</span>
                        <div class="h-6 w-px bg-patria-border"></div>
                        <div class="text-right">
                            <p class="text-sm text-patria-green-text font-medium">Aminata Diallo</p>
                            <p class="text-xs text-patria-text-secondary">Administratrice</p>
                        </div>
                        <span class="text-patria-text-secondary text-xs">˅</span>
                    </div>
                </div>
            </header>

            <!-- Page content (scrollable) -->
            <main class="flex-1 p-6 overflow-auto">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
