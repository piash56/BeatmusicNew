@extends('layouts.dashboard')

@section('title', 'Non idoneo')
@section('page-title', 'Funzionalità non disponibile')

@section('content')
<div class="flex items-center justify-center min-h-[60vh]">
    <div class="text-center max-w-md">
        <div class="w-20 h-20 bg-yellow-600/20 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-white mb-3">Aggiornamento del piano richiesto</h2>
        <p class="text-gray-400 mb-8">Questa funzionalità richiede un abbonamento di livello superiore. Aggiorna il tuo piano per accedere a tutte le funzionalità premium, tra cui promozioni radiofoniche, slot per concerti dal vivo, Vevo e altro ancora.</p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('dashboard.billing') }}" class="px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold rounded-xl transition">
                Piano di aggiornamento
            </a>
            <a href="{{ route('dashboard.home') }}" class="px-6 py-3 bg-white/5 hover:bg-white/10 text-gray-300 font-semibold rounded-xl border border-white/10 transition">
                Torna alla dashboard
            </a>
        </div>

        <div class="mt-10 grid grid-cols-1 gap-3 text-left">
            @foreach([
                ['icon' => '📻', 'name' => 'Promozione radiofonica', 'desc' => 'Trasmetti la tua musica sulle migliori reti radiofoniche'],
                ['icon' => '🎤', 'name' => 'Concerto dal vivo', 'desc' => 'Prenota slot per spettacoli dal vivo'],
                ['icon' => '▶️', 'name' => 'Conto Vevo', 'desc' => 'Ottieni il tuo canale Vevo ufficiale'],
                ['icon' => '📋', 'name' => 'Playlist editoriali', 'desc' => 'Invia a playlist curate'],
            ] as $feature)
            <div class="flex items-center space-x-3 p-3 bg-white/3 rounded-xl border border-white/5">
                <span class="text-2xl">{{ $feature['icon'] }}</span>
                <div>
                    <p class="text-white text-sm font-medium">{{ $feature['name'] }}</p>
                    <p class="text-gray-500 text-xs">{{ $feature['desc'] }}</p>
                </div>
                <div class="ml-auto">
                    <span class="text-xs bg-purple-600/20 text-purple-400 px-2 py-1 rounded-full">Premio+</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
