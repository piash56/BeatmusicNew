@extends('layouts.dashboard')

@section('title', 'Supporto')
@section('page-title', 'Aiuto e supporto')
@section('page-subtitle', 'Ottieni assistenza per il tuo account e le tue release')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div></div>
        <a href="{{ route('dashboard.support.create') }}" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-xl transition flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Nuovo biglietto</span>
        </a>
    </div>

    <!-- Tickets -->
    <div class="bg-gray-900 rounded-2xl border border-white/5 overflow-hidden">
        <div class="p-4 border-b border-white/5 flex items-center justify-between">
            <h3 class="font-semibold text-white">I miei biglietti</h3>
            <form method="GET" class="flex items-center space-x-2">
                <select name="status" onchange="this.form.submit()" class="bg-gray-800 border border-white/10 text-gray-400 text-xs px-3 py-1.5 rounded-lg">
                    <option value="">Tutti gli stati</option>
                    <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Aprire</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In corso</option>
                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Chiuso</option>
                </select>
            </form>
        </div>
        @if($tickets->isEmpty())
        <div class="p-12 text-center">
            <div class="w-16 h-16 bg-purple-600/10 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-purple-400/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <p class="text-gray-400 text-sm">Ancora nessun ticket di supporto.</p>
            <a href="{{ route('dashboard.support.create') }}" class="text-purple-400 hover:text-purple-300 text-sm mt-2 inline-block">Crea il tuo primo biglietto →</a>
        </div>
        @else
        <div class="divide-y divide-white/5">
            @foreach($tickets as $ticket)
            <a href="{{ route('dashboard.support.show', $ticket->id) }}" class="flex items-start p-4 hover:bg-white/2 transition group">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center space-x-3 flex-wrap gap-y-1">
                        <p class="text-white font-medium group-hover:text-purple-400 transition">{{ $ticket->subject }}</p>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $ticket->status === 'open' ? 'bg-green-900/50 text-green-400' :
                               ($ticket->status === 'in_progress' ? 'bg-blue-900/50 text-blue-400' :
                               ($ticket->status === 'closed' ? 'bg-gray-700/50 text-gray-400' : 'bg-yellow-900/50 text-yellow-400')) }}">
                            {{ ucfirst(str_replace('_', ' ', $ticket->status ?? 'open')) }}
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $ticket->priority === 'high' ? 'bg-red-900/50 text-red-400' :
                               ($ticket->priority === 'medium' ? 'bg-yellow-900/50 text-yellow-400' : 'bg-gray-700/50 text-gray-400') }}">
                            {{ ucfirst($ticket->priority ?? 'low') }}
                        </span>
                    </div>
                    <p class="text-gray-500 text-xs mt-1">{{ $ticket->replies_count ?? 0 }} risposte · {{ $ticket->created_at->diffForHumans() }}</p>
                </div>
                <svg class="w-4 h-4 text-gray-600 group-hover:text-purple-400 transition flex-shrink-0 mt-0.5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            @endforeach
        </div>
        <div class="p-4 border-t border-white/5">
            {{ $tickets->links() }}
        </div>
        @endif
    </div>

    <!-- Help Links -->
    {{-- <div class="bg-gray-900 rounded-2xl border border-white/5 p-6">
        <h3 class="font-semibold text-white mb-4">Quick Help</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @foreach([
                ['icon' => '📚', 'title' => 'Knowledge Base', 'desc' => 'Browse articles and guides', 'url' => route('knowledge-base')],
                ['icon' => '❓', 'title' => 'FAQs', 'desc' => 'Frequently asked questions', 'url' => route('help-center')],
            ] as $item)
            <a href="{{ $item['url'] }}" class="flex items-center space-x-3 p-4 bg-white/3 rounded-xl border border-white/5 hover:border-purple-500/30 transition">
                <span class="text-2xl">{{ $item['icon'] }}</span>
                <div>
                    <p class="text-white text-sm font-medium">{{ $item['title'] }}</p>
                    <p class="text-gray-500 text-xs">{{ $item['desc'] }}</p>
                </div>
                <svg class="w-4 h-4 text-gray-600 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            @endforeach
        </div>
    </div> --}}
</div>
@endsection
