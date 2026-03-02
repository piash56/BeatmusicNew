@extends('layouts.dashboard')

@section('title', 'Analitica')
@section('page-title', 'Analitica Streams')
@section('page-subtitle', 'Tieni traccia della tua performance musicale')

@section('content')
<div class="space-y-6" x-data="{ search: @json(request('search', '')), submitSearch(){ this.$refs.searchForm.submit() } }">

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-gray-900 rounded-2xl border border-white/5 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-2xl">🎵</span>
                <span class="text-xs text-green-400 bg-green-900/30 px-2 py-1 rounded-full">Totale</span>
            </div>
            <div class="text-2xl font-bold text-white">{{ number_format($summary['total_streams'] ?? 0) }}</div>
            <div class="text-xs text-gray-400 mt-1">Streams Totale</div>
        </div>
        <div class="bg-gray-900 rounded-2xl border border-white/5 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-2xl">🚀</span>
                <span class="text-xs text-purple-400 bg-purple-900/30 px-2 py-1 rounded-full">Rilasciato</span>
            </div>
            <div class="text-2xl font-bold text-white">{{ number_format($summary['released_count'] ?? 0) }}</div>
            <div class="text-xs text-gray-400 mt-1">Tracce e Album pubblicati</div>
        </div>
        <div class="bg-gray-900 rounded-2xl border border-white/5 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-2xl">💾</span>
                <span class="text-xs text-yellow-400 bg-yellow-900/30 px-2 py-1 rounded-full">Pre-saves</span>
            </div>
            <div class="text-2xl font-bold text-white">{{ number_format($summary['total_presaves'] ?? 0) }}</div>
            <div class="text-xs text-gray-400 mt-1">Pre-saves Totale</div>
        </div>
    </div>

    <!-- Tracks Table -->
    <div class="bg-gray-900 rounded-2xl border border-white/5 overflow-hidden">
        <div class="p-4 border-b border-white/5 flex items-center justify-between">
            <h3 class="font-semibold text-white">Rilascia prestazioni</h3>
            <form method="GET" x-ref="searchForm" class="flex items-center space-x-2">
                <input type="text" name="search" x-model="search" x-on:input.debounce.300ms="submitSearch()"
                    placeholder="Search track or album..."
                    class="bg-gray-800 border border-white/10 text-white placeholder-gray-500 text-xs px-3 py-2 rounded-lg focus:outline-none focus:border-purple-500 w-56 sm:w-72">
            </form>
        </div>
        @if($tracks->isEmpty())
        <div class="p-12 text-center">
            <div class="w-16 h-16 bg-purple-600/10 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-purple-400/50" fill="currentColor" viewBox="0 0 20 20"><path d="M18 3a1 1 0 00-1.196-.98l-10 2A1 1 0 006 5v9.114A4.369 4.369 0 005 14c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V7.82l8-1.6v5.894A4.37 4.37 0 0015 12c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V3z"/></svg>
            </div>
            <p class="text-gray-400 text-sm">Nessuna traccia ancora pubblicata.</p>
            <a href="{{ route('dashboard.releases.create') }}?new=1" class="text-purple-400 hover:text-purple-300 text-sm mt-2 inline-block">Carica la tua prima traccia →</a>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-800/50 border-b border-white/5">
                    <tr>
                        <th class="text-left px-4 py-3 text-gray-400 font-medium">Pubblicazione</th>
                        <th class="text-right px-4 py-3 text-gray-400 font-medium">Streams Totale</th>
                        <th class="text-right px-4 py-3 text-gray-400 font-medium hidden sm:table-cell">Nuovi Streams</th>
                        <th class="text-right px-4 py-3 text-gray-400 font-medium hidden md:table-cell">Pre-saves</th>
                        <th class="text-right px-4 py-3 text-gray-400 font-medium hidden lg:table-cell">Reddito</th>
                        <th class="text-left px-4 py-3 text-gray-400 font-medium hidden lg:table-cell">Stato</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/3">
                    @foreach($tracks as $track)
                    <tr class="hover:bg-white/2 transition">
                        <td class="px-4 py-3">
                            <div class="flex items-center space-x-3">
                                @if($track->cover_art)
                                    <img src="{{ $track->cover_art_url }}" class="w-9 h-9 rounded-lg object-cover shrink-0">
                                @else
                                    <div class="w-9 h-9 bg-purple-600/20 rounded-lg flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4 text-purple-400" fill="currentColor" viewBox="0 0 20 20"><path d="M18 3a1 1 0 00-1.196-.98l-10 2A1 1 0 006 5v9.114A4.369 4.369 0 005 14c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V7.82l8-1.6v5.894A4.37 4.37 0 0015 12c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V3z"/></svg>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <a href="{{ route('dashboard.releases.show', $track->id) }}" class="text-white hover:text-purple-400 font-medium truncate block transition">
                                        {{ $track->release_type === 'album' ? ($track->album_title ?: $track->title) : $track->title }}
                                    </a>
                                    <p class="text-gray-500 text-xs truncate flex items-center gap-2">
                                        <span>{{ $track->artists }}</span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] border border-white/10 bg-white/5 text-gray-300">{{ $track->release_type === 'album' ? 'Album' : 'Single' }}</span>
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <span class="text-white font-semibold">{{ number_format($track->total_streams ?? 0) }}</span>
                        </td>
                        <td class="px-4 py-3 text-right hidden sm:table-cell">
                            <span class="text-green-400">+{{ number_format($track->new_streams ?? 0) }}</span>
                        </td>
                        <td class="px-4 py-3 text-right hidden md:table-cell text-gray-300">
                            {{ number_format($track->preSaves()->count()) }}
                        </td>
                        <td class="px-4 py-3 text-right hidden lg:table-cell">
                            <span class="text-green-400">${{ number_format($track->total_revenue ?? 0, 2) }}</span>
                        </td>
                        <td class="px-4 py-3 hidden lg:table-cell">
                            <span class="px-2 py-1 rounded-full text-xs {{ $track->status === 'Released' ? 'bg-green-900/50 text-green-400' : 'bg-gray-700/50 text-gray-400' }}">{{ $track->status }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-white/5">
            {{ $tracks->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
