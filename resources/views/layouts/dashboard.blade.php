<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Beat Music</title>
    @if(isset($settings) && $settings->favicon)
        <link rel="icon" href="{{ asset('storage/' . $settings->favicon) }}">
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: '#0891b2',
                            500: '#06b6d4',
                            600: '#0891b2',
                            700: '#0e7490',
                        },
                        slate: {
                            850: '#1e293b',
                            900: '#0f172a',
                            950: '#020617',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        * { scroll-behavior: smooth; }
        body { 
            background: #0a0f1a;
            color: #e2e8f0; 
            font-family: 'Inter', system-ui, sans-serif;
        }
        .sidebar-link { 
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 10px;
            color: #94a3b8;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            margin: 2px 8px;
        }
        .sidebar-link:hover {
            background: rgba(30, 41, 59, 0.8);
            color: #06b6d4;
        }
        .sidebar-link.active {
            background: rgba(6, 182, 212, 0.15);
            color: #06b6d4;
            border-left: 3px solid #06b6d4;
            font-weight: 600;
        }
        .sidebar-link svg {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }
        .glass { 
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(30, 41, 59, 0.5);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.5);
        }
        .sidebar-dark {
            background: linear-gradient(180deg, #0f172a 0%, #0a0f1a 100%);
            border-right: 1px solid rgba(30, 41, 59, 0.6);
        }
        /* Custom Scrollbar */
        nav::-webkit-scrollbar {
            width: 6px;
        }
        nav::-webkit-scrollbar-track {
            background: transparent;
        }
        nav::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.3);
            border-radius: 3px;
        }
        nav::-webkit-scrollbar-thumb:hover {
            background: rgba(148, 163, 184, 0.5);
        }
    </style>
    @stack('styles')
</head>
<body class="text-slate-100 font-sans antialiased" x-data="{ sidebarOpen: false }">

    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 bg-black/60 z-30 lg:hidden"></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed top-0 left-0 h-full w-72 sidebar-dark z-40 transition-transform duration-300 flex flex-col overflow-y-auto shadow-2xl">

        <!-- Logo -->
        <div class="flex items-center space-x-3 p-6 border-b border-slate-800/60 bg-slate-900/50">
            <div class="w-12 h-12 bg-gradient-to-br from-cyan-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg shadow-cyan-500/20 flex-shrink-0">
                <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M18 3a1 1 0 00-1.196-.98l-10 2A1 1 0 006 5v9.114A4.369 4.369 0 005 14c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V7.82l8-1.6v5.894A4.37 4.37 0 0015 12c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V3z"/></svg>
            </div>
            <div class="min-w-0">
                <span class="font-bold text-white text-base tracking-tight block">Beat Music</span>
                <p class="text-xs text-cyan-400 font-semibold uppercase tracking-wider">Artist Portal</p>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 py-4 overflow-y-auto">
            <a href="{{ route('dashboard.home') }}" class="sidebar-link {{ request()->routeIs('dashboard.home') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5v2M16 5v2"/></svg>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('dashboard.releases.index') }}" class="sidebar-link {{ request()->routeIs('dashboard.releases*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                <span>Releases</span>
            </a>
            <a href="{{ route('dashboard.playlists') }}" class="sidebar-link {{ request()->routeIs('dashboard.playlists') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h10M4 18h6"/></svg>
                <span>Editorial Playlists</span>
            </a>
            <a href="{{ route('dashboard.radio-promotion') }}" class="sidebar-link {{ request()->routeIs('dashboard.radio-promotion') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                <span>Radio Promotion</span>
            </a>
            <a href="{{ route('dashboard.concert-live') }}" class="sidebar-link {{ request()->routeIs('dashboard.concert-live') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.871v6.258a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/></svg>
                <span>Concert Live</span>
            </a>
            <a href="{{ route('dashboard.vevo') }}" class="sidebar-link {{ request()->routeIs('dashboard.vevo') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Vevo Account</span>
            </a>

            <div class="border-t border-slate-800/60 my-3 mx-4"></div>

            <a href="{{ route('dashboard.streams') }}" class="sidebar-link {{ request()->routeIs('dashboard.streams') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span>Analytics</span>
            </a>
            <a href="{{ route('dashboard.revenue') }}" class="sidebar-link {{ request()->routeIs('dashboard.revenue') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Revenue</span>
            </a>

            <div class="border-t border-slate-800/60 my-3 mx-4"></div>

            <a href="{{ route('dashboard.profile') }}" class="sidebar-link {{ request()->routeIs('dashboard.profile') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span>Profile</span>
            </a>
            <a href="{{ route('dashboard.settings') }}" class="sidebar-link {{ request()->routeIs('dashboard.settings') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Settings</span>
            </a>
            <a href="{{ route('dashboard.support') }}" class="sidebar-link {{ request()->routeIs('dashboard.support*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span>Help & Support</span>
            </a>
        </nav>

        <!-- User Info at Bottom -->
        <div class="p-5 border-t border-slate-800/60 mt-auto bg-slate-900/50">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3 min-w-0 flex-1">
                    @if(auth()->user()->profile_picture)
                        <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" class="w-11 h-11 rounded-full object-cover flex-shrink-0 border border-slate-700/50">
                    @else
                        <div class="w-11 h-11 bg-gradient-to-br from-cyan-500 to-teal-600 rounded-full flex items-center justify-center text-white text-sm font-bold shadow-lg shadow-cyan-500/20 flex-shrink-0">
                            {{ strtoupper(substr(auth()->user()->full_name, 0, 2)) }}
                        </div>
                    @endif
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->full_name }}</p>
                        <p class="text-xs text-cyan-400 font-semibold uppercase tracking-wider truncate">{{ auth()->user()->is_company ? 'Company' : 'Individual' }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-slate-400 hover:text-red-400 transition-colors duration-200 p-2 rounded-lg hover:bg-slate-800/50 flex-shrink-0" title="Logout">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="lg:ml-72 flex flex-col min-h-screen">
        <!-- Top Header -->
        <header class="sticky top-0 z-20 bg-slate-950/95 backdrop-blur-xl border-b border-slate-800/60 px-6 py-4 flex items-center justify-between shadow-lg">
            <div class="flex items-center space-x-4">
                <!-- Mobile menu toggle -->
                <button @click="sidebarOpen = true" class="lg:hidden text-slate-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div>
                    <h1 class="text-lg font-semibold text-white">@yield('page-title', 'Dashboard')</h1>
                    @hasSection('page-subtitle')
                        <p class="text-sm text-slate-400">@yield('page-subtitle')</p>
                    @endif
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('home') }}" target="_blank" rel="noopener noreferrer" class="text-slate-400 hover:text-cyan-400 text-sm flex items-center space-x-1 transition-colors duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span class="hidden sm:inline">Website</span>
                </a>
            </div>
        </header>

        <!-- Flash Messages -->
        <div class="fixed top-20 right-4 z-50 space-y-2 w-80">
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                     x-transition class="bg-emerald-900/80 border border-emerald-500/30 text-emerald-300 px-4 py-3 rounded-lg flex items-center justify-between shadow-lg">
                    <span class="text-sm">{{ session('success') }}</span>
                    <button @click="show = false" class="ml-2 text-emerald-400 hover:text-emerald-200 font-bold">×</button>
                </div>
            @endif
            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                     x-transition class="bg-rose-900/80 border border-rose-500/30 text-rose-300 px-4 py-3 rounded-lg flex items-center justify-between shadow-lg">
                    <span class="text-sm">{{ session('error') }}</span>
                    <button @click="show = false" class="ml-2 text-rose-400 hover:text-rose-200 font-bold">×</button>
                </div>
            @endif
        </div>

        <!-- Page Content -->
        <main class="flex-1 p-6 lg:p-8 bg-slate-950/30">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
