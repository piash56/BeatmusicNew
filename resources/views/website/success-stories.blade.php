@extends('layouts.app')

@section('title', 'Success Stories — Beat Music')

@section('content')
<section class="pt-32 pb-20 px-4 text-center">
    <div class="max-w-4xl mx-auto">
        <span class="inline-block bg-purple-600/20 text-purple-300 text-sm font-medium px-4 py-1.5 rounded-full border border-purple-500/30 mb-6">Riflettori sull'artista</span>
        <h1 class="text-4xl sm:text-6xl font-bold text-white leading-tight mb-6">
            Veri artisti, <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-indigo-400">Vero successo</span>
        </h1>
        <p class="text-xl text-gray-400 leading-relaxed max-w-2xl mx-auto">
            Migliaia di artisti indipendenti hanno utilizzato la Beat Music per raggiungere milioni di fan in tutto il mondo. Ecco alcune delle loro storie.
        </p>
    </div>
</section>

{{-- Stories --}}
<section class="py-16 px-4">
    <div class="max-w-6xl mx-auto space-y-16">
        @php
        $stories = [
            ['name'=>'Amara Diallo','genre'=>'Afrobeats','streams'=>'12M+','quote'=>'Beat Music mi ha dato accesso a piattaforme che non avrei mai pensato possibili. Entro 6 mesi dal caricamento del mio EP di debutto, avevo 12 milioni di ascolti e fan in ogni continente.','emoji'=>'🎤'],
            ['name'=>'Lucas Torres','genre'=>'Latin Pop','streams'=>'8M+','quote'=>'La promozione radiofonica è stata una vera svolta. Il mio singolo è stato scelto da 3 importanti stazioni radio latine e gli ascolti sono letteralmente esplosi.','emoji'=>'🎸'],
            ['name'=>'Yuki Tanaka','genre'=>'Electronic','streams'=>'5M+','quote'=>'Avere il mio canale Vevo tramite Beat Music è stato fantastico. Il solo aumento di credibilità mi ha aiutato a ottenere accordi con i marchi e licenze di sincronizzazione.','emoji'=>'🎹'],
            ['name'=>'Nia Roberts','genre'=>'R&B / Soul','streams'=>'20M+','quote'=>'Lo strumento di pre-salvataggio ha creato unattesa incredibile per luscita del mio album. Abbiamo raggiunto il primo posto in tre classifiche regionali il primo giorno.','emoji'=>'🎶'],
        ];
        @endphp

        @foreach($stories as $i => $story)
        <div class="glass rounded-3xl p-8 sm:p-12 border border-white/5 grid grid-cols-1 md:grid-cols-2 gap-8 items-center {{ $i % 2 !== 0 ? 'md:flex-row-reverse' : '' }}">
            <div class="{{ $i % 2 !== 0 ? 'md:order-2' : '' }}">
                <div class="text-5xl mb-4">{{ $story['emoji'] }}</div>
                <div class="flex items-center space-x-2 mb-4">
                    <span class="text-xs px-3 py-1 bg-purple-600/20 text-purple-300 border border-purple-500/30 rounded-full">{{ $story['genre'] }}</span>
                    <span class="text-xs px-3 py-1 bg-green-600/20 text-green-300 border border-green-500/30 rounded-full">{{ $story['streams'] }} streams</span>
                </div>
                <h2 class="text-2xl font-bold text-white mb-3">{{ $story['name'] }}</h2>
                <p class="text-gray-300 text-lg leading-relaxed">"{{ $story['quote'] }}"</p>
            </div>
            <div class="{{ $i % 2 !== 0 ? 'md:order-1' : '' }} flex items-center justify-center">
                <div class="w-40 h-40 rounded-3xl bg-gradient-to-br from-purple-600/30 to-indigo-600/30 border border-purple-500/20 flex items-center justify-center text-7xl">
                    {{ $story['emoji'] }}
                </div>
            </div>
        </div>
        @endforeach
    </div>
</section>

{{-- Testimonials --}}
@if(isset($testimonials) && $testimonials->count())
<section class="py-20 px-4 bg-white/2">
    <div class="max-w-5xl mx-auto">
        <h2 class="text-3xl font-bold text-white text-center mb-12">Cosa dicono gli artisti</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($testimonials as $t)
            <div class="glass rounded-2xl p-5 border border-white/5">
                <div class="flex items-center space-x-1 text-yellow-400 text-sm mb-3">
                    @for($i=0;$i<($t->rating??5);$i++)★@endfor
                </div>
                <p class="text-gray-300 text-sm leading-relaxed mb-4">"{{ $t->feedback }}"</p>
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-full bg-purple-600 flex items-center justify-center text-white text-xs font-bold">
                        {{ strtoupper(substr($t->name,0,1)) }}
                    </div>
                    <div>
                        <p class="text-white text-xs font-medium">{{ $t->customer_name }}</p>
                        <p class="text-gray-400 text-xs">{{ $t->title }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- CTA --}}
<section class="py-24 px-4">
    <div class="max-w-3xl mx-auto text-center glass rounded-3xl p-12 border border-white/5">
        <h2 class="text-3xl font-bold text-white mb-4">Scrivi la tua storia di successo</h2>
        <p class="text-gray-400 mb-8">Iscriviti oggi a Beat Music e inizia il tuo viaggio verso il riconoscimento globale.</p>
        <a href="{{ route('login') }}" class="group px-8 py-4 bg-gradient-to-r from-cyan-500 to-teal-600 hover:from-cyan-600 hover:to-teal-700 text-white font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-xl shadow-cyan-500/25 hover:shadow-cyan-500/40">
            Sign In
            <span class="inline-block ml-2 transition-transform duration-300 group-hover:translate-x-1">→</span>
        </a>
    </div>
</section>
@endsection
