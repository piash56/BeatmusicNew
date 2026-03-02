@extends('layouts.app')

@section('title', 'Home')

@section('content')
<!-- Hero Section -->
<section class="relative min-h-screen flex items-center justify-center overflow-hidden pt-20">
    <div class="absolute inset-0 hero-glow pointer-events-none"></div>
    <!-- Animated Background Elements -->
    <div class="absolute top-20 left-10 w-96 h-96 bg-cyan-500/5 rounded-full blur-3xl pointer-events-none animate-float"></div>
    <div class="absolute bottom-20 right-10 w-[500px] h-[500px] bg-teal-500/5 rounded-full blur-3xl pointer-events-none animate-float" style="animation-delay: 2s;"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-cyan-400/3 rounded-full blur-3xl pointer-events-none animate-float" style="animation-delay: 4s;"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center py-20">
        <div class="inline-flex items-center space-x-2.5 bg-cyan-500/10 border border-cyan-500/20 rounded-full px-5 py-2.5 mb-8 animate-fade-in-up" style="animation-delay: 0.2s;">
            <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
            <span class="text-sm text-cyan-300 font-medium">Trusted by 50,000+ Independent Artists</span>
        </div>
        <h1 class="text-5xl sm:text-6xl lg:text-7xl font-extrabold text-white leading-tight mb-6 animate-fade-in-up" style="animation-delay: 0.4s;">
            Distribuisci la tua musica<br>
            <span class="gradient-text">in tutto il mondo</span>
        </h1>
        <p class="text-xl text-slate-400 max-w-2xl mx-auto mb-10 leading-relaxed animate-fade-in-up" style="animation-delay: 0.6s;">
            Beat Music la soluzione definitiva per artisti, etichette, manager e promotori. Aumenta la tua visibilità, accedi ad analisi esclusive e sfrutta la nostra rete di distribuzione per massimizzare il tuo impatto sul mercato musicale.
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 animate-fade-in-up" style="animation-delay: 0.8s;">
            <a href="{{ route('login') }}" class="group px-8 py-4 bg-gradient-to-r from-cyan-500 to-teal-600 hover:from-cyan-600 hover:to-teal-700 text-white font-semibold rounded-xl text-lg transition-all duration-300 transform hover:scale-105 shadow-xl shadow-cyan-500/25 hover:shadow-cyan-500/40">
                Sign In
                <span class="inline-block ml-2 transition-transform duration-300 group-hover:translate-x-1">→</span>
            </a>
        </div>

        <!-- Platform Logos -->
        <div class="mt-20 animate-fade-in-up" style="animation-delay: 1s;">
            <p class="text-slate-500 text-sm mb-8 font-medium uppercase tracking-wider">Distribuisci su tutte le principali piattaforme</p>
            <div class="flex flex-wrap justify-center items-center gap-4 opacity-70">
                @foreach(['Spotify', 'Apple Music', 'YouTube Music', 'Amazon Music', 'Deezer', 'TikTok', 'Tidal', 'SoundCloud'] as $platform)
                    <div class="glass px-5 py-2.5 rounded-lg text-sm text-slate-300 font-medium border border-slate-700/30 hover:border-cyan-500/30 hover:text-cyan-400 transition-all duration-200 card-hover">{{ $platform }}</div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-24 bg-slate-900/30 relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-b from-transparent via-slate-900/20 to-transparent"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-16 animate-fade-in-up">
            <span class="inline-block bg-cyan-500/10 border border-cyan-500/20 text-cyan-300 text-xs font-semibold px-4 py-1.5 rounded-full mb-6 uppercase tracking-wider">caratteristiche</span>
            <h2 class="text-4xl sm:text-5xl font-bold text-white mb-4">Caratteristiche potenti</h2>
            <p class="text-slate-400 text-lg max-w-2xl mx-auto leading-relaxed">Tutto ciò di cui hai bisogno per distribuire, promuovere e monetizzare la tua musica alle tue condizioni.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @php
            $features = [
                ['icon' => '🎵', 'title' => 'Distribuzione globale', 'desc' => 'Ottieni la tua musica su Spotify, Apple Music, TikTok, Amazon e oltre 100 piattaforme in tutto il mondo allistante.'],
                ['icon' => '📊', 'title' => 'Analisi in tempo reale', 'desc' => 'Tieni traccia dei flussi, dei ricavi e dei dati sul pubblico in tempo reale con approfondimenti e report dettagliati.'],
                ['icon' => '💰', 'title' => 'Mantieni il 100% delle royalty', 'desc' => 'Guadagna il 100% delle tue royalty. Addebitiamo solo una piccola quota annuale, non prendiamo mai una percentuale sui tuoi guadagni.'],
                ['icon' => '📻', 'title' => 'Promozione radiofonica', 'desc' => 'Ottieni visibilità su reti radiofoniche e podcast in tutto il mondo per aumentare il tuo pubblico e i tuoi ascolti.'],
                ['icon' => '🎤', 'title' => 'Slot per concerti dal vivo', 'desc' => 'Richiedi spazi per esibirti a concerti ed eventi dal vivo per mettere in mostra il tuo talento dal vivo.'],
                ['icon' => '🎬', 'title' => 'Distribuzione Vevo', 'desc' => 'Crea il tuo canale Vevo e distribuisci video musicali a milioni di spettatori.'],
            ];
            @endphp
            @foreach($features as $index => $feature)
            <div class="glass rounded-2xl p-6 border border-slate-700/30 hover:border-cyan-500/30 transition-all duration-500 card-hover group opacity-100" 
                 x-data="{ inView: true }"
                 x-intersect.threshold.10="inView = true"
                 :class="inView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                 style="animation: fadeInUp 0.6s ease-out {{ $index * 0.1 }}s both;">
                <div class="text-4xl mb-4 transform transition-transform duration-300 group-hover:scale-110">{{ $feature['icon'] }}</div>
                <h3 class="text-lg font-semibold text-white mb-2 group-hover:text-cyan-400 transition-colors duration-200">{{ $feature['title'] }}</h3>
                <p class="text-slate-400 text-sm leading-relaxed">{{ $feature['desc'] }}</p>
            </div>
            @endforeach
        </div>
        <div class="text-center mt-12">
            <a href="{{ route('features') }}" class="inline-flex items-center text-cyan-400 hover:text-cyan-300 font-semibold transition-colors duration-200 group">
                Vedi tutte le funzionalità
                <span class="ml-2 transition-transform duration-300 group-hover:translate-x-1">→</span>
            </a>
        </div>
    </div>
</section>

<!-- Artist Showcase -->
<section class="py-24 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 animate-fade-in-up">
            <h2 class="text-4xl sm:text-5xl font-bold text-white mb-4">Unisciti ad artisti di successo</h2>
            <p class="text-slate-400 text-lg leading-relaxed">Migliaia di artisti indipendenti stanno già sviluppando la loro carriera con Beat Music.</p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-12">
            @foreach([['50K+', 'Artists'], ['500M+', 'Streams'], ['100+', 'Platforms'], ['$10M+', 'Paid Out']] as $index => $stat)
            <div class="glass rounded-2xl p-6 text-center border border-slate-700/30 hover:border-cyan-500/30 transition-all duration-500 card-hover opacity-100"
                 style="animation: fadeInUp 0.6s ease-out {{ $index * 0.1 }}s both;">
                <div class="text-3xl font-bold gradient-text mb-2">{{ $stat[0] }}</div>
                <div class="text-slate-400 text-sm font-medium">{{ $stat[1] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Testimonials Section -->
@if($testimonials->count())
<section class="py-24 bg-slate-900/30 relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-b from-transparent via-slate-900/20 to-transparent"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-16 animate-fade-in-up">
            <h2 class="text-4xl sm:text-5xl font-bold text-white mb-4">Artisti soddisfatti</h2>
            <p class="text-slate-400 text-lg">Unisciti a migliaia di artisti e aziende che stanno facendo crescere le loro carriere con la nostra piattaforma.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($testimonials as $index => $t)
            <div class="glass rounded-2xl p-6 border border-slate-700/30 hover:border-cyan-500/30 transition-all duration-300 card-hover animate-fade-in-up"
                 style="animation-delay: {{ $index * 0.1 }}s;">
                <div class="flex mb-4">
                    @for($i = 0; $i < $t->rating; $i++)
                        <span class="text-yellow-400 text-sm">★</span>
                    @endfor
                </div>
                <p class="text-slate-300 text-sm mb-5 italic leading-relaxed">"{{ $t->feedback }}"</p>
                <div class="flex items-center space-x-3 pt-4 border-t border-slate-700/30">
                    @if($t->has_profile_image)
                        <img src="{{ $t->profile_picture_url }}" class="w-11 h-11 rounded-full object-cover ring-2 ring-slate-700/50">
                    @else
                        <div class="w-11 h-11 bg-gradient-to-br from-cyan-500 to-teal-600 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-lg shadow-cyan-500/20">
                            {{ strtoupper(substr($t->customer_name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <p class="text-white font-semibold text-sm">{{ $t->customer_name }}</p>
                        @if($t->title)<p class="text-slate-400 text-xs">{{ $t->title }}</p>@endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="text-center mt-12">
            <a href="{{ route('success-stories') }}" class="inline-flex items-center text-cyan-400 hover:text-cyan-300 font-semibold transition-colors duration-200 group">
                Leggi altre storie
                <span class="ml-2 transition-transform duration-300 group-hover:translate-x-1">→</span>
            </a>
        </div>
    </div>
</section>
@endif

<!-- CTA Section -->
<section class="py-24 relative">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="glass rounded-3xl p-12 bg-gradient-to-br from-cyan-500/10 to-teal-600/10 border border-cyan-500/20 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-cyan-500/5 to-transparent"></div>
            <div class="relative z-10 animate-fade-in-up">
                <h2 class="text-4xl sm:text-5xl font-bold text-white mb-4">Pronto a lanciare la tua carriera musicale?</h2>
                <p class="text-slate-400 text-lg mb-8 leading-relaxed">Unisciti a oltre 50.000 artisti indipendenti e inizia a distribuire la tua musica oggi stesso.</p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="{{ route('login') }}" class="group px-8 py-4 bg-gradient-to-r from-cyan-500 to-teal-600 hover:from-cyan-600 hover:to-teal-700 text-white font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-xl shadow-cyan-500/25 hover:shadow-cyan-500/40">
                        Sign In
                        <span class="inline-block ml-2 transition-transform duration-300 group-hover:translate-x-1">→</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
