<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - Beat Music Admin</title>
    @if(isset($settings) && $settings->favicon)
        <link rel="icon" href="{{ asset('storage/' . $settings->favicon) }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
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
        .admin-link { 
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
        .admin-link:hover {
            background: rgba(30, 41, 59, 0.8);
            color: #06b6d4;
        }
        .admin-link.active {
            background: rgba(6, 182, 212, 0.15);
            color: #06b6d4;
            border-left: 3px solid #06b6d4;
            font-weight: 600;
        }
        .admin-link svg {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }
        .section-header {
            padding: 16px 24px 8px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #64748b;
        }
        .submenu-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px 10px 48px;
            border-radius: 8px;
            color: #64748b;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s ease;
            margin: 2px 8px;
        }
        .submenu-link:hover {
            background: rgba(30, 41, 59, 0.6);
            color: #06b6d4;
        }
        .submenu-link.active {
            background: rgba(6, 182, 212, 0.1);
            color: #06b6d4;
            border-left: 2px solid #06b6d4;
            font-weight: 600;
        }
        button.admin-link {
            cursor: pointer;
        }
        button.admin-link svg:last-child {
            margin-left: auto;
        }
        .glass {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(30, 41, 59, 0.5);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.5);
        }
        .nav-blur {
            background: rgba(10, 15, 26, 0.95);
            backdrop-filter: blur(20px) saturate(180%);
            border-bottom: 1px solid rgba(30, 41, 59, 0.5);
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

    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 bg-black/60 z-30 lg:hidden transition-opacity duration-300"></div>

    <!-- Admin Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed top-0 left-0 h-full w-72 sidebar-dark z-40 transition-transform duration-300 flex flex-col overflow-y-auto shadow-2xl">
        <div class="flex items-center space-x-3 p-6 border-b border-slate-800/60 bg-slate-900/50">
            <div class="w-12 h-12 bg-gradient-to-br from-cyan-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg shadow-cyan-500/20 flex-shrink-0">
                <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
            </div>
            <div class="min-w-0">
                <span class="font-bold text-white text-base tracking-tight block">Beat Music</span>
                <p class="text-xs text-cyan-400 font-semibold uppercase tracking-wider">Admin Panel</p>
            </div>
        </div>

        <nav class="flex-1 py-4 overflow-y-auto">
            <a href="{{ route('admin.dashboard') }}" class="admin-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
                <span>Dashboard</span>
            </a>

            <p class="section-header">Submissions</p>
            <a href="{{ route('admin.track-submissions') }}" class="admin-link {{ request()->routeIs('admin.track-submissions*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2z"/></svg>
                <span>Track Submissions</span>
            </a>
            <a href="{{ route('admin.album-submissions') }}" class="admin-link {{ request()->routeIs('admin.album-submissions*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <span>Album Submissions</span>
            </a>
            <a href="{{ route('admin.editorial-playlists') }}" class="admin-link {{ request()->routeIs('admin.editorial-playlists*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h10M4 18h6"/></svg>
                <span>Editorial Playlists</span>
            </a>
            <a href="{{ route('admin.vevo-accounts') }}" class="admin-link {{ request()->routeIs('admin.vevo-accounts*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><circle cx="12" cy="12" r="9" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                <span>Vevo Accounts</span>
            </a>

            <!-- Radio (collapsible) -->
            <div x-data="{ open: {{ request()->routeIs('admin.radio*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="admin-link w-full justify-between {{ request()->routeIs('admin.radio*') ? 'active' : '' }}">
                    <div class="flex items-center" style="gap: 12px;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                        <span>Radio</span>
                    </div>
                    <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     x-cloak>
                    <a href="{{ route('admin.radio-networks') }}" class="submenu-link {{ request()->routeIs('admin.radio-networks') ? 'active' : '' }}">
                        All Networks
                    </a>
                    <a href="{{ route('admin.radio-requests') }}" class="submenu-link {{ request()->routeIs('admin.radio-requests') ? 'active' : '' }}">
                        Radio Requests
                    </a>
                </div>
            </div>

            <!-- Concert Lives (collapsible) -->
            <div x-data="{ open: {{ request()->routeIs('admin.concert-lives*') || request()->routeIs('admin.live-requests*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="admin-link w-full justify-between {{ request()->routeIs('admin.concert-lives*') || request()->routeIs('admin.live-requests*') ? 'active' : '' }}">
                    <div class="flex items-center" style="gap: 12px;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.871v6.258a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/></svg>
                        <span>Concert Lives</span>
                    </div>
                    <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     x-cloak>
                    <a href="{{ route('admin.concert-lives') }}" class="submenu-link {{ request()->routeIs('admin.concert-lives') && !request()->routeIs('admin.live-requests*') ? 'active' : '' }}">
                        All Lives
                    </a>
                    <a href="{{ route('admin.live-requests') }}" class="submenu-link {{ request()->routeIs('admin.live-requests*') ? 'active' : '' }}">
                        Live Requests
                    </a>
                </div>
            </div>

            <p class="section-header">Management</p>
            <a href="{{ route('admin.streams') }}" class="admin-link {{ request()->routeIs('admin.streams*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span>Streams Management</span>
            </a>
            <a href="{{ route('admin.users') }}" class="admin-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span>User Management</span>
            </a>
            <a href="{{ route('admin.payout-requests') }}" class="admin-link {{ request()->routeIs('admin.payout-requests*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span>Artists Payouts</span>
            </a>
            <a href="{{ route('admin.update-royalties') }}" class="admin-link {{ request()->routeIs('admin.update-royalties*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Update Royalties</span>
            </a>

            <p class="section-header">Content</p>
            <a href="{{ route('admin.faqs') }}" class="admin-link {{ request()->routeIs('admin.faqs*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>FAQs</span>
            </a>
            <a href="{{ route('admin.knowledge-base') }}" class="admin-link {{ request()->routeIs('admin.knowledge-base*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span>Knowledge Base</span>
            </a>
            <a href="{{ route('admin.newsletter-subscribers') }}" class="admin-link {{ request()->routeIs('admin.newsletter-subscribers') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <span>Newsletter</span>
            </a>
            <a href="{{ route('admin.testimonials') }}" class="admin-link {{ request()->routeIs('admin.testimonials*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                <span>Testimonials</span>
            </a>
            <a href="{{ route('admin.support') }}" class="admin-link {{ request()->routeIs('admin.support*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span>Support Tickets</span>
            </a>

            <p class="section-header">Admin</p>
            <a href="{{ route('admin.profile') }}" class="admin-link {{ request()->routeIs('admin.profile*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span>View Profile</span>
            </a>
            <a href="{{ route('admin.site-settings') }}" class="admin-link {{ request()->routeIs('admin.site-settings*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Site Settings</span>
            </a>
        </nav>

        <div class="p-5 border-t border-slate-800/60 mt-auto bg-slate-900/50">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3 min-w-0 flex-1">
                    <div class="w-11 h-11 bg-gradient-to-br from-cyan-500 to-teal-600 rounded-full flex items-center justify-center text-white text-sm font-bold shadow-lg shadow-cyan-500/20 flex-shrink-0">
                        {{ strtoupper(substr(auth()->user()->full_name ?? 'A', 0, 2)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->full_name ?? 'Admin' }}</p>
                        <p class="text-xs text-cyan-400 font-semibold uppercase tracking-wider">Administrator</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="text-slate-400 hover:text-red-400 transition-colors duration-200 p-2 rounded-lg hover:bg-slate-800/50 flex-shrink-0" title="Logout">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Admin Main Content -->
    <div class="lg:ml-72 flex flex-col min-h-screen">
        <header class="sticky top-0 z-20 nav-blur border-b border-slate-800/60 px-6 py-4 flex items-center justify-between shadow-lg">
            <div class="flex items-center space-x-3">
                <button @click="sidebarOpen = true" class="lg:hidden text-slate-400 hover:text-cyan-400 transition-colors p-2 rounded-lg hover:bg-slate-800/50">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h1 class="text-lg font-bold text-white tracking-tight">@yield('page-title', 'Admin Panel')</h1>
            </div>
            <div class="flex items-center space-x-3">
                <span class="text-xs bg-cyan-500/10 text-cyan-400 border border-cyan-500/30 px-3 py-1.5 rounded-full font-semibold uppercase tracking-wider">Admin</span>
            </div>
        </header>

        <div class="fixed top-20 right-4 z-50 space-y-3 w-80 max-w-[calc(100vw-2rem)]">
            @if(session('success'))
                <div x-data="{ show: true }" 
                     x-show="show" 
                     x-init="setTimeout(() => show = false, 5000)"
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="opacity-0 translate-x-full"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     x-transition:leave="transition ease-in duration-200 transform"
                     x-transition:leave-start="opacity-100 translate-x-0"
                     x-transition:leave-end="opacity-0 translate-x-full"
                     class="glass bg-emerald-900/30 border border-emerald-500/30 text-emerald-200 px-4 py-3 rounded-xl flex items-center justify-between shadow-xl shadow-emerald-500/10">
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                    <button @click="show = false" class="ml-3 text-emerald-400 hover:text-emerald-200 transition-colors text-lg leading-none">×</button>
                </div>
            @endif
            @if(session('error'))
                <div x-data="{ show: true }" 
                     x-show="show" 
                     x-init="setTimeout(() => show = false, 5000)"
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="opacity-0 translate-x-full"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     x-transition:leave="transition ease-in duration-200 transform"
                     x-transition:leave-start="opacity-100 translate-x-0"
                     x-transition:leave-end="opacity-0 translate-x-full"
                     class="glass bg-rose-900/30 border border-rose-500/30 text-rose-200 px-4 py-3 rounded-xl flex items-center justify-between shadow-xl shadow-rose-500/10">
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                    <button @click="show = false" class="ml-3 text-rose-400 hover:text-rose-200 transition-colors text-lg leading-none">×</button>
                </div>
            @endif
        </div>

        <main class="flex-1 p-6 lg:p-8 bg-slate-950/30">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
