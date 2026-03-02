@extends('layouts.app')

@section('title', 'Features — Beat Music')

@section('content')
{{-- Hero --}}
<section class="pt-32 pb-20 px-4 text-center relative overflow-hidden">
    <div class="absolute inset-0 hero-glow pointer-events-none opacity-50"></div>
    <div class="max-w-4xl mx-auto relative z-10 animate-fade-in-up">
        <span class="inline-block bg-cyan-500/10 border border-cyan-500/20 text-cyan-300 text-xs font-semibold px-5 py-2 rounded-full mb-6 uppercase tracking-wider">Tutto ciò di cui hai bisogno</span>
        <h1 class="text-4xl sm:text-6xl font-bold text-white leading-tight mb-6">
            Funzionalità potenti per <span class="gradient-text">Artisti indipendenti</span>
        </h1>
        <p class="text-xl text-slate-400 leading-relaxed max-w-2xl mx-auto">
            Tutto ciò di cui hai bisogno per distribuire, promuovere e monetizzare la tua musica alle tue condizioni.
        </p>
    </div>
</section>

{{-- Features Grid --}}
<section class="py-20 px-4 bg-slate-900/30 relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-b from-transparent via-slate-900/20 to-transparent"></div>
    <div class="max-w-6xl mx-auto relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            @php
            $features = [
                ['icon'=>'🎵','title'=>'Distribuzione globale','desc'=>'Distribuisci la tua musica su oltre 150 piattaforme di streaming, tra cui Spotify, Apple Music, YouTube Music, Tidal e altre ancora.'],
                ['icon'=>'📊','title'=>'Analisi in tempo reale','desc'=>'Tieni traccia dei tuoi flussi, ascoltatori e ricavi su tutte le piattaforme da ununica, splendida dashboard.'],
                ['icon'=>'📻','title'=>'Promozione radiofonica','desc'=>'Fai in modo che la tua musica venga trasmessa nelle stazioni radio di tutto il mondo attraverso la nostra vasta rete di partner radiofonici.'],
                ['icon'=>'🎬','title'=>'Verifica Vevo','desc'=>'Richiedi un canale Vevo verificato e distribuisci i tuoi video musicali su YouTube con il marchio Vevo.'],
                ['icon'=>'🎭','title'=>'Slot per concerti dal vivo','desc'=>'Richiedi slot per esibizioni dal vivo in eventi e festival gestiti tramite la rete Beat Music.'],
                ['icon'=>'💿','title'=>'Singoli e album','desc'=>'Carica singoli con una sola traccia o album completi con un massimo di 20 tracce. Supporta tutti i principali formati audio.'],
                ['icon'=>'💾','title'=>'Campagne di pre-salvataggio','desc'=>'Crea campagne di pre-salvataggio in modo che i tuoi fan possano salvare le tue prossime uscite prima che vengano pubblicate.'],
                ['icon'=>'🎼','title'=>'Playlist editoriali','desc'=>'Invia i tuoi brani affinché vengano presi in considerazione nelle playlist editoriali curate sulle principali piattaforme.'],
                ['icon'=>'💰','title'=>'Entrate e royalties','desc'=>'Guadagna royalties da ogni streaming. Report trasparenti e semplici richieste di pagamento alla tua banca o a PayPal.'],
                ['icon'=>'🏷️','title'=>'Etichetta bianca UPC/ISRC','desc'=>'A ogni versione vengono assegnati automaticamente il proprio codice a barre UPC e i codici ISRC.'],
                ['icon'=>'🎙️','title'=>'Profilo dellartista','desc'=>'Crea il tuo profilo artista con biografia, link social e un portfolio di tutte le tue uscite.'],
                ['icon'=>'🛡️','title'=>'24/7 Supporto','desc'=>'Il nostro team di supporto è sempre disponibile tramite il centro assistenza, la knowledge base e il sistema di ticketing.'],
            ];
            @endphp

            @foreach($features as $index => $feature)
            <div class="glass rounded-2xl p-6 border border-slate-700/30 hover:border-cyan-500/30 transition-all duration-500 card-hover group opacity-100"
                 style="animation: fadeInUp 0.6s ease-out {{ $index * 0.05 }}s both;">
                <div class="text-4xl mb-4 transform transition-transform duration-300 group-hover:scale-110">{{ $feature['icon'] }}</div>
                <h3 class="text-white font-semibold text-lg mb-2 group-hover:text-cyan-400 transition-colors duration-200">{{ $feature['title'] }}</h3>
                <p class="text-slate-400 text-sm leading-relaxed">{{ $feature['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="py-24 px-4">
    <div class="max-w-3xl mx-auto text-center glass rounded-3xl p-12 border border-slate-700/30 bg-gradient-to-br from-cyan-500/10 to-teal-600/10 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-cyan-500/5 to-transparent"></div>
        <div class="relative z-10 animate-fade-in-up">
            <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4">Pronti per iniziare?</h2>
            <p class="text-slate-400 mb-8 leading-relaxed">Unisciti a migliaia di artisti indipendenti che già utilizzano Beat Music per far crescere la propria carriera.</p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('login') }}" class="group px-8 py-4 bg-gradient-to-r from-cyan-500 to-teal-600 hover:from-cyan-600 hover:to-teal-700 text-white font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-xl shadow-cyan-500/25 hover:shadow-cyan-500/40">
                    Sign In
                    <span class="inline-block ml-2 transition-transform duration-300 group-hover:translate-x-1">→</span>
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
