@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Dashboard')

@section('content')
<div class="space-y-8">

    <!-- Welcome Section -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-white mb-2">Bentornato, {{ auth()->user()->full_name }}!</h2>
        <p class="text-slate-400">Ecco cosa sta succedendo oggi con la tua piattaforma.</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 lg:gap-6">
        @php
        $statCards = [
            ['value' => $stats['tracks_require_review'] + $stats['tracks_processing'], 'label' => 'Uscite in sospeso', 'subtext' => 'richiedere una revisione: ' . $stats['tracks_require_review'] . '  ', 'subtext2' => 'Elaborazione: ' . $stats['tracks_processing'] . ' ', 'subtext2_class' => 'text-rose-400', 'icon' => '📋', 'route' => 'admin.track-submissions', 'bg' => 'from-yellow-500/10 to-yellow-600/5'],
            ['value' => $stats['vevo_pending'], 'label' => 'Vevo in attesa', 'subtext' => $stats['vevo_approved'] . ' approvato, ' . $stats['vevo_pending'] . ' in attesa di', 'icon' => '▶️', 'route' => 'admin.vevo-accounts', 'bg' => 'from-rose-500/10 to-rose-600/5'],
            ['value' => $stats['pending_concerts'], 'label' => 'Concerti in sospeso', 'subtext' => 'richieste pendenti', 'icon' => '🎤', 'route' => 'admin.live-requests', 'bg' => 'from-orange-500/10 to-orange-600/5'],
            ['value' => $stats['pending_playlists'], 'label' => 'Playlist in attesa', 'subtext' => 'iscrizioni in sospeso', 'icon' => '📑', 'route' => 'admin.editorial-playlists', 'bg' => 'from-pink-500/10 to-pink-600/5'],
            ['value' => $stats['radio_published'], 'label' => 'Promozione radiofonica', 'subtext' => ' Radio in attesa ' . $stats['pending_radio'], 'icon' => '📻', 'route' => 'admin.radio-requests', 'bg' => 'from-blue-500/10 to-blue-600/5'],
            ['value' => $stats['released_singles'] + $stats['released_albums'], 'label' => 'Totale rilasciato', 'subtext' => $stats['released_singles'] . ' singolo(i), ' . $stats['released_albums'] . ' album(i)', 'icon' => '🎵', 'route' => 'admin.track-submissions', 'bg' => 'from-purple-500/10 to-purple-600/5'],
            ['value' => $stats['approved_playlists'], 'label' => 'Playlist approvate', 'subtext' => 'pubblicato con successo', 'subtext_class' => 'text-emerald-400', 'icon' => '✅', 'route' => 'admin.editorial-playlists', 'bg' => 'from-emerald-500/10 to-emerald-600/5'],
            ['value' => $stats['approved_concerts'], 'label' => 'Concerti dal vivo approvati', 'subtext' => 'richieste approvate', 'subtext_class' => 'text-emerald-400', 'icon' => '✅', 'route' => 'admin.live-requests', 'bg' => 'from-emerald-500/10 to-emerald-600/5'],
            ['value' => $stats['total_users'], 'label' => 'Utenti totali', 'subtext' => '+' . $stats['admin_users'] . ' utenti amministratori', 'subtext_class' => 'text-emerald-400', 'icon' => '👥', 'route' => 'admin.users', 'bg' => 'from-cyan-500/10 to-cyan-600/5'],
            ['value' => $stats['artist_accounts'], 'label' => 'Conti degli artisti', 'subtext' => 'singoli artisti', 'icon' => '👤', 'route' => 'admin.users', 'bg' => 'from-emerald-500/10 to-emerald-600/5'],
            ['value' => $stats['company_accounts'], 'label' => 'Conti aziendali', 'subtext' => 'conti aziendali', 'icon' => '🏢', 'route' => 'admin.users', 'bg' => 'from-yellow-500/10 to-yellow-600/5'],
            ['value' => $stats['tickets_active'], 'label' => 'Ticket di supporto', 'subtext' => $stats['tickets_in_progress'] . ' biglietti in corso', 'subtext2' => 'Totale: ' . $stats['tickets_active'] . ' biglietti attivi', 'icon' => '🎫', 'route' => 'admin.support', 'bg' => 'from-cyan-500/10 to-cyan-600/5'],
        ];
        @endphp
        @foreach($statCards as $card)
        <a href="{{ route($card['route']) }}" class="group relative overflow-hidden rounded-xl bg-slate-900/80 border border-slate-800/60 hover:border-cyan-500/50 transition-all duration-300 p-5 hover:shadow-xl hover:shadow-cyan-500/10 hover:-translate-y-1">
            <div class="absolute inset-0 bg-gradient-to-br {{ $card['bg'] }} opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-slate-800/60 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform duration-300">
                        {{ $card['icon'] }}
                    </div>
                    <svg class="w-5 h-5 text-slate-600 group-hover:text-cyan-400 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
                <div class="text-3xl font-bold text-white mb-1 group-hover:text-cyan-400 transition-colors duration-300">{{ number_format($card['value']) }}</div>
                <div class="text-xs text-slate-400 font-semibold uppercase tracking-wider">{{ $card['label'] }}</div>
                @if(!empty($card['subtext']))
                    <div class="mt-1.5 text-xs {{ $card['subtext_class'] ?? 'text-amber-400' }}">{{ $card['subtext'] }}</div>
                @endif
                @if(!empty($card['subtext2']))
                    <div class="mt-0.5 text-xs {{ $card['subtext2_class'] ?? 'text-slate-400' }}">{{ $card['subtext2'] }}</div>
                @endif
            </div>
        </a>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Recent Users -->
        <div class="bg-slate-900/80 rounded-2xl border border-slate-800/60 overflow-hidden shadow-xl">
            <div class="p-5 border-b border-slate-800/60 flex items-center justify-between bg-slate-800/40">
                <div>
                    <h3 class="font-bold text-white text-lg mb-1">Utenti recenti</h3>
                    <p class="text-xs text-slate-400">Ultimi artisti registrati</p>
                </div>
                <a href="{{ route('admin.users') }}" class="text-cyan-400 hover:text-cyan-300 text-xs font-semibold transition-colors duration-200 flex items-center space-x-1">
                    <span>Visualizza tutto</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
            <div class="divide-y divide-slate-800/60">
                @forelse($recentUsers as $u)
                <div class="flex items-center space-x-3 p-4 hover:bg-slate-800/40 transition-all duration-200 group">
                    <div class="w-11 h-11 bg-gradient-to-br from-cyan-500 to-teal-600 rounded-full flex items-center justify-center text-white text-sm font-bold shadow-lg shadow-cyan-500/20 flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
                        {{ strtoupper(substr($u->full_name, 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-white text-sm font-semibold truncate group-hover:text-cyan-400 transition-colors">{{ $u->full_name }}</p>
                        <p class="text-slate-400 text-xs truncate">{{ $u->email }}</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <span class="text-xs px-3 py-1.5 rounded-full font-semibold {{ $u->subscription === 'Pro' ? 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/30' : ($u->subscription === 'Premium' ? 'bg-cyan-500/20 text-cyan-400 border border-cyan-500/30' : 'bg-slate-700/60 text-slate-400 border border-slate-600/40') }}">{{ $u->subscription }}</span>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center">
                    <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-slate-800/60 flex items-center justify-center">
                        <svg class="w-8 h-8 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <p class="text-slate-500 text-sm">Nessun utente ancora</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Tracks -->
        <div class="bg-slate-900/80 rounded-2xl border border-slate-800/60 overflow-hidden shadow-xl">
            <div class="p-5 border-b border-slate-800/60 flex items-center justify-between bg-slate-800/40">
                <div>
                    <h3 class="font-bold text-white text-lg mb-1">Invii di brani recenti</h3>
                    <p class="text-xs text-slate-400">Ultimi contributi musicali</p>
                </div>
                <a href="{{ route('admin.track-submissions') }}" class="text-cyan-400 hover:text-cyan-300 text-xs font-semibold transition-colors duration-200 flex items-center space-x-1">
                    <span>Visualizza tutto</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
            <div class="divide-y divide-slate-800/60">
                @forelse($recentTracks as $track)
                <div class="flex items-center space-x-3 p-4 hover:bg-slate-800/40 transition-all duration-200 group">
                    @if($track->cover_art)
                        <img src="{{ $track->cover_art_url }}" class="w-11 h-11 rounded-lg object-cover flex-shrink-0 border border-slate-700/60 group-hover:border-cyan-500/50 transition-colors shadow-md">
                    @else
                        <div class="w-11 h-11 bg-gradient-to-br from-cyan-500/20 to-teal-600/20 rounded-lg flex items-center justify-center border border-cyan-500/30 flex-shrink-0 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-cyan-400" fill="currentColor" viewBox="0 0 20 20"><path d="M18 3a1 1 0 00-1.196-.98l-10 2A1 1 0 006 5v9.114A4.369 4.369 0 005 14c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V7.82l8-1.6v5.894A4.37 4.37 0 0015 12c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V3z"/></svg>
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="text-white text-sm font-semibold truncate group-hover:text-cyan-400 transition-colors">{{ $track->title }}</p>
                        <p class="text-slate-400 text-xs truncate">{{ $track->user->full_name ?? 'Unknown' }}</p>
                    </div>
                    <span class="text-xs px-3 py-1.5 rounded-full font-semibold flex-shrink-0 {{ $track->status === 'Released' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : ($track->status === 'On Request' ? 'bg-blue-500/20 text-blue-400 border border-blue-500/30' : 'bg-slate-700/60 text-slate-400 border border-slate-600/40') }}">{{ $track->status }}</span>
                </div>
                @empty
                <div class="p-8 text-center">
                    <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-slate-800/60 flex items-center justify-center">
                        <svg class="w-8 h-8 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2z"/></svg>
                    </div>
                    <p class="text-slate-500 text-sm">Nessuna traccia inviata ancora</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Pending Payouts -->
        <div class="bg-slate-900/80 rounded-2xl border border-slate-800/60 overflow-hidden shadow-xl">
            <div class="p-5 border-b border-slate-800/60 flex items-center justify-between bg-slate-800/40">
                <div>
                    <h3 class="font-bold text-white text-lg mb-1">Pagamenti in sospeso</h3>
                    <p class="text-xs text-slate-400">Richiede la tua attenzione</p>
                </div>
                <a href="{{ route('admin.payout-requests') }}" class="text-cyan-400 hover:text-cyan-300 text-xs font-semibold transition-colors duration-200 flex items-center space-x-1">
                    <span>Visualizza tutto</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
            @forelse($pendingPayouts as $payout)
            <div class="flex items-center space-x-3 p-4 border-b border-slate-800/60 last:border-0 hover:bg-slate-800/40 transition-all duration-200 group">
                <div class="w-11 h-11 rounded-full bg-gradient-to-br from-emerald-500/20 to-emerald-600/10 flex items-center justify-center border border-emerald-500/30 flex-shrink-0 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-white text-sm font-semibold truncate group-hover:text-cyan-400 transition-colors">{{ $payout->user->full_name ?? $payout->user_full_name }}</p>
                    <p class="text-slate-400 text-xs truncate">{{ $payout->paypal_email }}</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <span class="text-emerald-400 font-bold text-base block">${{ number_format($payout->amount, 2) }}</span>
                </div>
                <a href="{{ route('admin.payout-requests') }}" class="text-xs px-3 py-1.5 bg-yellow-500/20 text-yellow-400 border border-yellow-500/30 rounded-lg font-semibold hover:bg-yellow-500/30 transition-colors duration-200 flex-shrink-0">Revisione</a>
            </div>
            @empty
            <div class="p-8 text-center">
                <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-slate-800/60 flex items-center justify-center">
                    <svg class="w-8 h-8 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-slate-500 text-sm">Nessun pagamento in sospeso</p>
            </div>
            @endforelse
        </div>

        <!-- Open Tickets -->
        <div class="bg-slate-900/80 rounded-2xl border border-slate-800/60 overflow-hidden shadow-xl">
            <div class="p-5 border-b border-slate-800/60 flex items-center justify-between bg-slate-800/40">
                <div>
                    <h3 class="font-bold text-white text-lg mb-1">Apri ticket di supporto</h3>
                    <p class="text-xs text-slate-400">Ha bisogno di risposta</p>
                </div>
                <a href="{{ route('admin.support') }}" class="text-cyan-400 hover:text-cyan-300 text-xs font-semibold transition-colors duration-200 flex items-center space-x-1">
                    <span>Visualizza tutto</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
            @forelse($openTickets as $ticket)
            <div class="flex items-center space-x-3 p-4 border-b border-slate-800/60 last:border-0 hover:bg-slate-800/40 transition-all duration-200 group">
                <div class="w-11 h-11 rounded-full bg-gradient-to-br from-rose-500/20 to-rose-600/10 flex items-center justify-center border border-rose-500/30 flex-shrink-0 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-white text-sm font-semibold truncate group-hover:text-cyan-400 transition-colors">{{ $ticket->subject }}</p>
                    <p class="text-slate-400 text-xs truncate">{{ $ticket->user->full_name ?? 'Unknown' }}</p>
                </div>
                <span class="text-xs px-3 py-1.5 rounded-full font-semibold flex-shrink-0 {{ $ticket->priority === 'high' ? 'bg-rose-500/20 text-rose-400 border border-rose-500/30' : ($ticket->priority === 'medium' ? 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/30' : 'bg-slate-700/60 text-slate-400 border border-slate-600/40') }}">{{ ucfirst($ticket->priority) }}</span>
                <a href="{{ route('admin.support.show', $ticket->id) }}" class="text-slate-400 hover:text-cyan-400 text-xs px-3 py-1.5 bg-slate-800/60 rounded-lg font-semibold hover:bg-slate-800 border border-slate-700/50 hover:border-cyan-500/50 transition-all duration-200 flex-shrink-0">Rispondere</a>
            </div>
            @empty
            <div class="p-8 text-center">
                <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-slate-800/60 flex items-center justify-center">
                    <svg class="w-8 h-8 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <p class="text-slate-500 text-sm">Nessun ticket aperto</p>
            </div>
            @endforelse
        </div>

    </div>
</div>
@endsection
