@extends('layouts.app')

@section('title', 'About Us — Beat Music')

@section('content')
{{-- Hero --}}
<section class="pt-32 pb-20 px-4 text-center">
    <div class="max-w-4xl mx-auto">
        <span class="inline-block bg-cyan-500/10 border border-cyan-500/20 text-cyan-300 text-xs font-semibold px-5 py-2 rounded-full mb-6 uppercase tracking-wider">La nostra storia</span>
        <h1 class="text-4xl sm:text-6xl font-bold text-white leading-tight mb-6">
            Costruito da artisti, <span class="gradient-text">per Artisti</span>
        </h1>
        <p class="text-xl text-gray-400 leading-relaxed max-w-2xl mx-auto">
            Beat Music è stata fondata con una missione: offrire agli artisti indipendenti gli stessi strumenti e le stesse opportunità degli artisti delle major, ma senza i controlli.
        </p>
    </div>
</section>

{{-- Mission --}}
<section class="py-16 px-4">
    <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
        <div>
            <h2 class="text-3xl font-bold text-white mb-4">La nostra missione</h2>
            <p class="text-gray-400 leading-relaxed mb-4">
                L'industria musicale è stata a lungo dominata da poche e potenti etichette discografiche che controllano chi viene ascoltato e chi no. Beat Music sta cambiando questa situazione offrendo una piattaforma di distribuzione di livello mondiale che mette gli artisti al primo posto.
            </p>
            <p class="text-gray-400 leading-relaxed">
                Crediamo che ogni artista meriti royalties eque, una portata globale e gli strumenti per costruire una carriera musicale sostenibile. Ecco perché manteniamo i nostri compensi trasparenti e la nostra tecnologia all'avanguardia.
            </p>
        </div>
        <div class="grid grid-cols-2 gap-4">
            @foreach(['10K+' => 'Artisti in tutto il mondo', '150+' => 'Piattaforme', '50M+' => 'Stream mensili', '98%' => 'Tasso di soddisfazione'] as $num => $label)
            <div class="glass rounded-2xl p-6 text-center border border-white/5">
                <p class="text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-indigo-400">{{ $num }}</p>
                <p class="text-gray-400 text-sm mt-1">{{ $label }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Values --}}
<section class="py-20 px-4 bg-white/2">
    <div class="max-w-5xl mx-auto">
        <h2 class="text-3xl font-bold text-white text-center mb-12">I nostri valori</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @php
            $values = [
                ['icon'=>'🎯','title'=>'Prima lartista','desc'=>'Ogni decisione che prendiamo è guidata da ciò che è meglio per i nostri artisti. Il vostro successo è il nostro successo.'],
                ['icon'=>'🔍','title'=>'Trasparenza','desc'=>'Nessun costo nascosto. Nessuna sorpresa. Dichiarazioni di royalty chiare e comunicazione onesta, sempre.'],
                ['icon'=>'🚀','title'=>'Innovazione','desc'=>'Miglioriamo costantemente la nostra piattaforma per consentirti di avere sempre accesso alle tecnologie di distribuzione più recenti.'],
            ];
            @endphp
            @foreach($values as $v)
            <div class="glass rounded-2xl p-6 border border-slate-700/30 hover:border-cyan-500/30 transition-all duration-300 card-hover text-center">
                <div class="text-4xl mb-3">{{ $v['icon'] }}</div>
                <h3 class="text-white font-semibold text-lg mb-2">{{ $v['title'] }}</h3>
                <p class="text-gray-400 text-sm">{{ $v['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Team --}}
<section class="py-20 px-4">
    <div class="max-w-5xl mx-auto text-center">
        <h2 class="text-3xl font-bold text-white mb-4">La squadra</h2>
        <p class="text-gray-400 mb-12">Appassionati di musica ed esperti di tecnologia lavorano insieme al servizio degli artisti.</p>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6">
            @php
            $team = [
                ['name'=>'Alex Rivera','role'=>'CEO & Co-founder'],['name'=>'Maya Chen','role'=>'CTO'],
                ['name'=>'Jordan Lee','role'=>'Head of Artist Relations'],['name'=>'Sam Okafor','role'=>'Lead Engineer'],
                ['name'=>'Priya Sharma','role'=>'Marketing Director'],['name'=>'Chris Bolton','role'=>'Product Manager'],
                ['name'=>'Fatima Al-Zahra','role'=>'Community Manager'],['name'=>'Liam Park','role'=>'Data Analyst'],
            ];
            @endphp
            @foreach($team as $member)
            <div class="glass rounded-2xl p-4 border border-white/5">
                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-purple-600 to-indigo-600 flex items-center justify-center text-white font-bold text-xl mx-auto mb-3">
                    {{ strtoupper(substr($member['name'], 0, 1)) }}
                </div>
                <p class="text-white font-medium text-sm">{{ $member['name'] }}</p>
                <p class="text-gray-400 text-xs">{{ $member['role'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="py-24 px-4">
    <div class="max-w-3xl mx-auto text-center glass rounded-3xl p-12 border border-white/5">
        <h2 class="text-3xl font-bold text-white mb-4">Unisciti alla famiglia Beat Music</h2>
        <p class="text-gray-400 mb-8">Inizia oggi stesso il tuo percorso come artista indipendente.</p>
        <a href="{{ route('login') }}" class="group px-8 py-4 bg-gradient-to-r from-cyan-500 to-teal-600 hover:from-cyan-600 hover:to-teal-700 text-white font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-xl shadow-cyan-500/25 hover:shadow-cyan-500/40">
            Sign In
            <span class="inline-block ml-2 transition-transform duration-300 group-hover:translate-x-1">→</span>
        </a>
    </div>
</section>
@endsection
