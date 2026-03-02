@extends('layouts.dashboard')

@section('title', 'Rilasci')
@section('page-title', 'Rilasci')
@section('page-subtitle', 'Gestisci le tue uscite musicali')

@section('content')
@php
    $currentTab = request('tab', 'all');
    $statusParam = request('status');
    $queryTab = fn($tab) => route('dashboard.releases.index', array_filter(['tab' => $tab, 'status' => $statusParam]));
@endphp
<div>
    <!-- Filter: All | Single | Album + Upload Button -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div class="flex bg-white/5 border border-white/10 rounded-xl p-1 space-x-1">
            <a href="{{ $queryTab('all') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $currentTab === 'all' ? 'bg-purple-600 text-white' : 'text-gray-400 hover:text-white' }}">Tutto</a>
            <a href="{{ $queryTab('single') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $currentTab === 'single' ? 'bg-purple-600 text-white' : 'text-gray-400 hover:text-white' }}">Singolo</a>
            <a href="{{ $queryTab('album') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $currentTab === 'album' ? 'bg-purple-600 text-white' : 'text-gray-400 hover:text-white' }}">Album</a>
        </div>
        <a href="{{ route('dashboard.releases.create') }}?new=1" class="flex items-center space-x-2 px-5 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-medium rounded-xl transition text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Carica nuovo</span>
        </a>
    </div>

    <!-- Status filter -->
    <div class="glass rounded-xl p-4 mb-6">
        <form method="GET" class="flex flex-col sm:flex-row gap-3">
            <input type="hidden" name="tab" value="{{ $currentTab }}">
            <select name="status" class="bg-gray-800 border border-white/10 text-gray-300 px-3 py-2 rounded-lg text-sm focus:outline-none focus:border-purple-500">
                <option value="">Tutti gli stati</option>
                @foreach(['Draft','On Request','On Process','Released','Rejected','Modify Pending','Modify Process','Modify Released','Modify Rejected'] as $s)
                    <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-white/10 hover:bg-white/15 text-white text-sm rounded-lg transition">Filtro</button>
        </form>
    </div>

    <!-- Releases table -->
    <div class="glass rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[640px]">
                <thead>
                    <tr class="border-b border-white/10 text-left text-xs text-gray-500 uppercase tracking-wider">
                        <th class="px-4 py-3 font-medium w-20">Traccia</th>
                        <th class="px-4 py-3 font-medium">Genere</th>
                        <th class="px-4 py-3 font-medium w-24">Tipo</th>
                        <th class="px-4 py-3 font-medium">Data di invio</th>
                        <th class="px-4 py-3 font-medium">Data di rilascio</th>
                        <th class="px-4 py-3 font-medium w-28">Stato</th>
                        <th class="px-4 py-3 font-medium text-right w-24">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tracks as $track)
                    <tr class="border-b border-white/5 last:border-0 hover:bg-white/[0.02] transition">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if($track->cover_art)
                                    <img src="{{ $track->cover_art_url }}" alt="" class="w-12 h-12 rounded-lg object-cover shrink-0">
                                @else
                                    <div class="w-12 h-12 bg-gradient-to-br from-purple-600/30 to-indigo-600/30 rounded-lg flex items-center justify-center shrink-0">
                                        <svg class="w-6 h-6 text-purple-400" fill="currentColor" viewBox="0 0 20 20"><path d="M18 3a1 1 0 00-1.196-.98l-10 2A1 1 0 006 5v9.114A4.369 4.369 0 005 14c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V7.82l8-1.6v5.894A4.37 4.37 0 0015 12c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V3z"/></svg>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="text-white font-medium truncate text-sm">
                                        {{ $track->release_type === 'album' ? ($track->album_title ?: $track->title) : $track->title }}
                                    </p>
                                    <p class="text-gray-400 text-xs truncate">{{ $track->artists }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-300 text-sm">{{ $track->primary_genre ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-300 text-sm capitalize">{{ $track->release_type }}</td>
                        <td class="px-4 py-3 text-gray-300 text-sm">{{ $track->created_at->format('M d, Y') }}</td>
                        <td class="px-4 py-3 text-gray-300 text-sm">{{ $track->release_date ? $track->release_date->format('M d, Y') : '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs px-2 py-1 rounded-full font-medium whitespace-nowrap
                                {{ match($track->status) {
                                    'Released' => 'bg-green-900/50 text-green-400',
                                    'On Process', 'Modify Process' => 'bg-yellow-900/50 text-yellow-400',
                                    'Rejected', 'Modify Rejected' => 'bg-red-900/50 text-red-400',
                                    'On Request', 'Modify Pending' => 'bg-blue-900/50 text-blue-400',
                                    default => 'bg-gray-700/50 text-gray-400'
                                } }}">
                                {{ $track->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end space-x-1">
                                <a href="{{ route('dashboard.releases.show', $track->id) }}" class="p-2 text-gray-400 hover:text-white rounded-lg hover:bg-white/10 transition" title="View">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('dashboard.releases.edit', $track->id) }}" class="p-2 text-gray-400 hover:text-white rounded-lg hover:bg-white/10 transition" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-16 text-center">
                            <div class="text-5xl mb-4">🎵</div>
                            <h3 class="text-white font-semibold mb-2">Nessuna versione trovata</h3>
                            <p class="text-gray-400 text-sm mb-6">Per iniziare, carica il tuo primo singolo o album.</p>
                            <a href="{{ route('dashboard.releases.create') }}?new=1" class="inline-block px-6 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white text-sm font-semibold rounded-xl transition">Carica versione</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $tracks->links() }}
</div>
@endsection
