<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $settings->site_title ?? 'Beat Music') - Beat Music</title>
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
                            50: '#ecfeff',
                            100: '#cffafe',
                            200: '#a5f3fc',
                            300: '#67e8f9',
                            400: '#22d3ee',
                            500: '#06b6d4',
                            600: '#0891b2',
                            700: '#0e7490',
                            800: '#155e75',
                            900: '#164e63',
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
                    animation: {
                        'fade-in': 'fadeIn 0.6s ease-out',
                        'fade-in-up': 'fadeInUp 0.8s ease-out',
                        'slide-in': 'slideIn 0.5s ease-out',
                        'float': 'float 6s ease-in-out infinite',
                        'glow': 'glow 2s ease-in-out infinite alternate',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        slideIn: {
                            '0%': { opacity: '0', transform: 'translateX(-20px)' },
                            '100%': { opacity: '1', transform: 'translateX(0)' },
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-20px)' },
                        },
                        glow: {
                            '0%': { boxShadow: '0 0 20px rgba(6, 182, 212, 0.3)' },
                            '100%': { boxShadow: '0 0 40px rgba(6, 182, 212, 0.6)' },
                        },
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
            background: linear-gradient(135deg, #020617 0%, #0f172a 50%, #1e293b 100%);
            background-attachment: fixed;
            color: #f1f5f9; 
            font-family: 'Inter', system-ui, sans-serif;
        }
        .gradient-text { 
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 50%, #0e7490 100%); 
            -webkit-background-clip: text; 
            -webkit-text-fill-color: transparent; 
            background-clip: text; 
        }
        .glass { 
            background: rgba(15, 23, 42, 0.7); 
            backdrop-filter: blur(16px) saturate(180%);
            border: 1px solid rgba(148, 163, 184, 0.1); 
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        }
        .hero-glow { 
            background: radial-gradient(ellipse 80% 50% at 50% 0%, rgba(6, 182, 212, 0.15) 0%, transparent 100%);
        }
        .nav-blur {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(20px) saturate(180%);
            border-bottom: 1px solid rgba(148, 163, 184, 0.08);
        }
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(6, 182, 212, 0.15);
        }
        .text-gradient {
            background: linear-gradient(135deg, #06b6d4, #0891b2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body class="text-slate-100 font-sans antialiased">

    <!-- Navbar -->
    <nav x-data="{ mobileOpen: false }"
         class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 nav-blur shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center space-x-3 group" x-data="{ hover: false }" @mouseenter="hover = true" @mouseleave="hover = false">
                    @if(isset($settings) && $settings->logo_url)
                        <img src="{{ asset('storage/' . $settings->logo_url) }}" alt="{{ $settings->logo_alt }}" class="h-9 transition-transform duration-300 group-hover:scale-110">
                    @else
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-cyan-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg shadow-cyan-500/20 transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M18 3a1 1 0 00-1.196-.98l-10 2A1 1 0 006 5v9.114A4.369 4.369 0 005 14c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V7.82l8-1.6v5.894A4.37 4.37 0 0015 12c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V3z"/></svg>
                            </div>
                            <span class="text-xl font-bold gradient-text tracking-tight">{{ $settings->site_title ?? 'Beat Music' }}</span>
                        </div>
                    @endif
                </a>

                <!-- Desktop Nav Links -->
                <div class="hidden md:flex items-center space-x-1">
                    <a href="{{ route('features') }}"
                       class="px-4 py-2 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('features') ? 'text-cyan-400 bg-slate-900/60' : 'text-slate-300 hover:text-cyan-400 hover:bg-slate-800/50' }}">
                        Features
                    </a>
                    <a href="{{ route('success-stories') }}"
                       class="px-4 py-2 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('success-stories') ? 'text-cyan-400 bg-slate-900/60' : 'text-slate-300 hover:text-cyan-400 hover:bg-slate-800/50' }}">
                        Success Stories
                    </a>
                    <a href="{{ route('about-us') }}"
                       class="px-4 py-2 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('about-us') ? 'text-cyan-400 bg-slate-900/60' : 'text-slate-300 hover:text-cyan-400 hover:bg-slate-800/50' }}">
                        About Us
                    </a>
                    <a href="{{ route('help-center') }}"
                       class="px-4 py-2 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('help-center') ? 'text-cyan-400 bg-slate-900/60' : 'text-slate-300 hover:text-cyan-400 hover:bg-slate-800/50' }}">
                        Contact
                    </a>
                </div>

                <!-- Auth Buttons -->
                <div class="hidden md:flex items-center space-x-3">
                    @auth
                        <a href="{{ route('dashboard.home') }}"
                           class="text-sm bg-gradient-to-r from-cyan-500 to-teal-600 hover:from-cyan-600 hover:to-teal-700 text-white px-5 py-2.5 rounded-lg transition-all duration-200 font-semibold shadow-lg shadow-cyan-500/25 hover:shadow-cyan-500/40 hover:scale-105">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="text-sm bg-gradient-to-r from-cyan-500 to-teal-600 hover:from-cyan-600 hover:to-teal-700 text-white px-5 py-2.5 rounded-lg transition-all duration-200 font-semibold shadow-lg shadow-cyan-500/25 hover:shadow-cyan-500/40 hover:scale-105">
                            Sign In
                        </a>
                    @endauth
                </div>

                <!-- Mobile menu button -->
                <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 text-slate-300 hover:text-cyan-400 transition-colors rounded-lg hover:bg-slate-800/50">
                    <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-show="mobileOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileOpen" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="md:hidden border-t border-slate-800/50 glass">
            <div class="px-4 py-4 space-y-2">
                <a href="{{ route('features') }}"
                   class="block py-2.5 px-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('features') ? 'bg-slate-900/70 text-cyan-400' : 'text-slate-300 hover:text-cyan-400 hover:bg-slate-800/60' }}">
                    Features
                </a>
                <a href="{{ route('success-stories') }}"
                   class="block py-2.5 px-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('success-stories') ? 'bg-slate-900/70 text-cyan-400' : 'text-slate-300 hover:text-cyan-400 hover:bg-slate-800/60' }}">
                    Success Stories
                </a>
                <a href="{{ route('about-us') }}"
                   class="block py-2.5 px-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('about-us') ? 'bg-slate-900/70 text-cyan-400' : 'text-slate-300 hover:text-cyan-400 hover:bg-slate-800/60' }}">
                    About Us
                </a>
                <a href="{{ route('help-center') }}"
                   class="block py-2.5 px-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('help-center') ? 'bg-slate-900/70 text-cyan-400' : 'text-slate-300 hover:text-cyan-400 hover:bg-slate-800/60' }}">
                    Contact
                </a>
                <div class="pt-3 border-t border-slate-800/50 flex flex-col space-y-2">
                    @auth
                        <a href="{{ route('dashboard.home') }}"
                           class="block text-center py-2.5 bg-gradient-to-r from-cyan-500 to-teal-600 text-white rounded-lg font-semibold shadow-lg shadow-cyan-500/25">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="block text-center py-2.5 bg-gradient-to-r from-cyan-500 to-teal-600 text-white rounded-lg font-semibold shadow-lg shadow-cyan-500/25">
                            Sign In
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <div class="fixed top-24 right-4 z-50 space-y-3 w-80 max-w-[calc(100vw-2rem)]" id="flash-container">
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
        @if(session('info'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 5000)"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-x-full"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 translate-x-0"
                 x-transition:leave-end="opacity-0 translate-x-full"
                 class="glass bg-cyan-900/30 border border-cyan-500/30 text-cyan-200 px-4 py-3 rounded-xl flex items-center justify-between shadow-xl shadow-cyan-500/10">
                <span class="text-sm font-medium">{{ session('info') }}</span>
                <button @click="show = false" class="ml-3 text-cyan-400 hover:text-cyan-200 transition-colors text-lg leading-none">×</button>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main class="pt-16">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800/50 mt-24 bg-slate-950/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 mb-12">
                <!-- Brand -->
                <div class="col-span-1">
                    <div class="flex items-center space-x-3 mb-5">
                        <div class="w-10 h-10 bg-gradient-to-br from-cyan-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg shadow-cyan-500/20">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M18 3a1 1 0 00-1.196-.98l-10 2A1 1 0 006 5v9.114A4.369 4.369 0 005 14c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V7.82l8-1.6v5.894A4.37 4.37 0 0015 12c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V3z"/></svg>
                        </div>
                        <span class="font-bold text-white text-lg tracking-tight">{{ isset($settings) ? $settings->site_title : 'Beat Music' }}</span>
                    </div>
                    <p class="text-slate-400 text-sm leading-relaxed mb-4">{{ isset($settings) ? $settings->footer_text : 'Empowering independent artists worldwide.' }}</p>
                    @if(isset($settings) && $settings->social_links)
                        <div class="flex space-x-3">
                            @foreach($settings->social_links as $platform => $url)
                                @if($url)
                                    @php
                                        $key = strtolower($platform);
                                    @endphp
                                    <a href="{{ $url }}" target="_blank"
                                       class="w-9 h-9 flex items-center justify-center rounded-lg bg-slate-800/50 text-slate-400 hover:text-cyan-400 hover:bg-slate-800 transition-all duration-200"
                                       aria-label="{{ ucfirst($key) }}">
                                        @switch($key)
                                            @case('facebook')
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M13 3h4a1 1 0 0 1 1 1v3h-3a1 1 0 0 0-1 1v3h4l-1 4h-3v6h-4v-6H9v-4h2V8a5 5 0 0 1 5-5z"/>
                                                </svg>
                                                @break
                                            @case('twitter')
                                            @case('x')
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M18 3h-2.5L12 8.1 8.5 3H6l5 7-5 7h2.5L12 11.9 15.5 17H18l-5-7 5-7z"/>
                                                </svg>
                                                @break
                                            @case('instagram')
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M7 3h10a4 4 0 0 1 4 4v10a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4V7a4 4 0 0 1 4-4zm0 2a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H7zm5 2.5A4.5 4.5 0 1 1 7.5 12 4.5 4.5 0 0 1 12 7.5zm0 2A2.5 2.5 0 1 0 14.5 12 2.5 2.5 0 0 0 12 9.5zm5-3.25a.75.75 0 1 1-.75.75.75.75 0 0 1 .75-.75z"/>
                                                </svg>
                                                @break
                                            @case('youtube')
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M21.8 8.001a2.75 2.75 0 0 0-1.93-1.947C18.25 5.5 12 5.5 12 5.5s-6.25 0-7.87.554A2.75 2.75 0 0 0 2.2 8.001 28.6 28.6 0 0 0 1.5 12a28.6 28.6 0 0 0 .7 3.999 2.75 2.75 0 0 0 1.93 1.947C5.75 18.5 12 18.5 12 18.5s6.25 0 7.87-.554a2.75 2.75 0 0 0 1.93-1.947A28.6 28.6 0 0 0 22.5 12a28.6 28.6 0 0 0-.7-3.999zM10 15.25v-6.5L15 12z"/>
                                                </svg>
                                                @break
                                            @default
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 13.5L9 21l3-1.5L15 21l-1.5-7.5M4 9l8-6 8 6-8 6z"/>
                                                </svg>
                                        @endswitch
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Company / Product Links -->
                <div>
                    <h3 class="text-white font-semibold mb-5 text-sm uppercase tracking-wider">Company</h3>
                    <ul class="space-y-3">
                        <li><a href="{{ route('features') }}" class="text-slate-400 hover:text-cyan-400 text-sm transition-colors duration-200">Features</a></li>
                        <li><a href="{{ route('success-stories') }}" class="text-slate-400 hover:text-cyan-400 text-sm transition-colors duration-200">Success Stories</a></li>
                        <li><a href="{{ route('about-us') }}" class="text-slate-400 hover:text-cyan-400 text-sm transition-colors duration-200">About Us</a></li>
                        <li><a href="{{ route('help-center') }}" class="text-slate-400 hover:text-cyan-400 text-sm transition-colors duration-200">Help Center</a></li>
                    </ul>
                </div>

                <!-- Newsletter -->
                <div>
                    <h3 class="text-white font-semibold mb-5 text-sm uppercase tracking-wider">Stay Updated</h3>
                    <p class="text-slate-400 text-sm mb-4 leading-relaxed">Get music industry news and updates.</p>
                    <div x-data="{ email: '', loading: false, done: false, error: '' }" class="flex flex-col space-y-2">
                        <input
                            x-model="email"
                            type="email"
                            placeholder="Your email address"
                            class="bg-slate-800/50 border border-slate-700/50 text-white placeholder-slate-500 px-4 py-2.5 rounded-lg text-sm focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/20 transition-all"
                        >
                        <p x-show="error" x-text="error" x-cloak class="text-xs text-rose-400"></p>
                        <button
                            @click="
                                error = '';
                                done = false;
                                const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                                if (!pattern.test(email)) {
                                    error = 'Please enter a valid email address.';
                                    return;
                                }
                                loading = true;
                                fetch('{{ route('newsletter.subscribe') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                        'Accept': 'application/json',
                                    },
                                    body: JSON.stringify({ email })
                                }).then(async (r) => {
                                    if (r.ok) {
                                        done = true;
                                        email = '';
                                        error = '';
                                    } else {
                                        let data = null;
                                        try {
                                            data = await r.json();
                                        } catch (e) {}
                                        if (data && data.errors && data.errors.email && data.errors.email[0]) {
                                            error = data.errors.email[0];
                                        } else if (data && data.message) {
                                            error = data.message;
                                        } else {
                                            error = 'Unable to subscribe. Please try again.';
                                        }
                                    }
                                }).catch(() => {
                                    error = 'Unable to subscribe. Please try again.';
                                }).finally(() => {
                                    loading = false;
                                });
                            "
                            :disabled="loading"
                            class="bg-gradient-to-r from-cyan-500 to-teal-600 hover:from-cyan-600 hover:to-teal-700 text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 disabled:opacity-50 shadow-lg shadow-cyan-500/25 hover:shadow-cyan-500/40"
                        >
                            <span x-show="!done && !loading">Subscribe</span>
                            <span x-show="loading" x-cloak>Subscribing...</span>
                            <span x-show="done && !loading" x-cloak>✓ Subscribed!</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="border-t border-slate-800/50 pt-8 grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                <p class="text-slate-500 text-sm md:text-left">{{ isset($settings) ? $settings->copyright_text : '© ' . date('Y') . ' Beat Music. All rights reserved.' }}</p>
                <div class="flex flex-wrap justify-center md:justify-end gap-6">
                    <a href="{{ route('knowledge-base') }}" class="text-slate-500 hover:text-cyan-400 text-sm transition-colors duration-200">Knowledge Base</a>
                    <a href="{{ route('terms') }}" class="text-slate-500 hover:text-cyan-400 text-sm transition-colors duration-200">Terms</a>
                    <a href="{{ route('privacy') }}" class="text-slate-500 hover:text-cyan-400 text-sm transition-colors duration-200">Privacy</a>
                    <a href="{{ route('cookie-policy') }}" class="text-slate-500 hover:text-cyan-400 text-sm transition-colors duration-200">Cookie Policy</a>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
