@extends('layouts.dashboard')

@section('title', $track->title)
@section('page-title', 'Dettagli del rilascio')
@section('page-subtitle', $track->title)

@section('content')
<div class="space-y-6">

    <!-- Back + Actions -->
    <div class="flex items-center justify-between">
        <a href="{{ route('dashboard.releases.index') }}" class="flex items-center space-x-2 text-gray-400 hover:text-white transition text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            <span>Tutte le versioni</span>
        </a>
        <div class="flex items-center space-x-2">
            @if(in_array($track->status, ['Draft','On Request','Rejected','Released']))
            <a href="{{ route('dashboard.releases.edit', $track->id) }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-gray-300 text-sm rounded-lg border border-white/10 transition flex items-center space-x-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Modificare</span>
            </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Cover + Meta -->
        <div class="lg:col-span-1 space-y-4">
            <div class="bg-gray-900 rounded-2xl border border-white/5 overflow-hidden">
                @if($track->cover_art)
                    <img src="{{ $track->cover_art_url }}" alt="{{ $track->title }}" class="w-full aspect-square object-cover">
                @else
                    <div class="w-full aspect-square bg-gradient-to-br from-purple-900/50 to-indigo-900/50 flex items-center justify-center">
                        <svg class="w-16 h-16 text-purple-400/50" fill="currentColor" viewBox="0 0 20 20"><path d="M18 3a1 1 0 00-1.196-.98l-10 2A1 1 0 006 5v9.114A4.369 4.369 0 005 14c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V7.82l8-1.6v5.894A4.37 4.37 0 0015 12c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V3z"/></svg>
                    </div>
                @endif
                <div class="p-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="font-bold text-white text-lg">{{ $track->title }}</h2>
                            <p class="text-gray-400 text-sm">{{ $track->artists }}</p>
                        </div>
                        <span class="ml-2 px-2.5 py-1 rounded-full text-xs font-medium flex-shrink-0
                            {{ $track->status === 'Released' ? 'bg-green-900/50 text-green-400' :
                               ($track->status === 'On Request' ? 'bg-blue-900/50 text-blue-400' :
                               ($track->status === 'Modify Pending' ? 'bg-amber-900/50 text-amber-400' :
                               ($track->status === 'On Process' ? 'bg-yellow-900/50 text-yellow-400' :
                               ($track->status === 'Rejected' ? 'bg-red-900/50 text-red-400' : 'bg-gray-700/50 text-gray-400')))) }}">
                            {{ $track->status }}
                        </span>
                    </div>

                    @if($track->release_type === 'single' && $track->audio_file)
                    <div class="mt-4">
                        <p class="text-gray-500 text-xs mb-2">Traccia file</p>
                        <audio controls class="w-full h-10" style="accent-color: #7c3aed;">
                            <source src="{{ route('files.audio', $track->id) }}" type="audio/mpeg">
                                Il tuo browser non supporta la riproduzione audio.
                        </audio>
                    </div>
                    @endif

                    <!-- Download cover & track -->
                    <div class="mt-4 flex flex-wrap gap-2">
                        @if($track->cover_art)
                        <a href="{{ route('files.cover.download', $track->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/10 hover:bg-white/15 text-gray-300 text-sm rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Scarica la copertina
                        </a>
                        @endif
                        @if($track->release_type === 'single' && $track->audio_file)
                        <a href="{{ route('files.audio', $track->id) }}?download=1" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/10 hover:bg-white/15 text-gray-300 text-sm rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Scarica la copertina
                        </a>
                        @endif
                    </div>

                    <div class="mt-4 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Tipo</span>
                            <span class="text-gray-200 capitalize">{{ $track->release_type }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Genere</span>
                            <span class="text-gray-200">{{ $track->primary_genre }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Data di rilascioe</span>
                            <span class="text-gray-200">{{ $track->release_date ? \Carbon\Carbon::parse($track->release_date)->format('M d, Y') : '—' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Data di invio</span>
                            <span class="text-gray-200">{{ $track->created_at->format('M d, Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Ultimo aggiornamento</span>
                            <span class="text-gray-200">{{ $track->updated_at->format('M d, Y H:i') }}</span>
                        </div>
                        @if($track->isrc)
                        <div class="flex justify-between">
                            <span class="text-gray-500">ISRC</span>
                            <span class="text-gray-200 font-mono text-xs">{{ $track->isrc }}</span>
                        </div>
                        @endif
                        @if($track->upc)
                        <div class="flex justify-between">
                            <span class="text-gray-500">UPC</span>
                            <span class="text-gray-200 font-mono text-xs">{{ $track->upc }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-500">Esplicito</span>
                            <span class="text-gray-200">{{ $track->is_explicit ? 'Yes' : 'No' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="bg-gray-900 rounded-2xl border border-white/5 p-4">
                <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-3">Statistiche</h3>
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-white/3 rounded-xl p-3 text-center">
                        <div class="text-2xl font-bold text-white">{{ number_format($track->total_streams ?? 0) }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">Streams Totale</div>
                    </div>
                    <div class="bg-white/3 rounded-xl p-3 text-center">
                        <div class="text-2xl font-bold text-white">{{ number_format($preSaves) }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">Pre-saves</div>
                    </div>
                    <div class="bg-white/3 rounded-xl p-3 text-center">
                        <div class="text-2xl font-bold text-white">{{ number_format($track->new_streams ?? 0) }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">Streams Nuovo</div>
                    </div>
                    <div class="bg-white/3 rounded-xl p-3 text-center">
                        <div class="text-2xl font-bold text-green-400">${{ number_format($track->total_revenue ?? 0, 2) }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">Reddito</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details -->
        <div class="lg:col-span-2 space-y-4">

            <!-- Pre-save Link -->
            @if($track->status === 'Released')
            <div class="rounded-2xl border border-cyan-500/20 bg-gradient-to-br from-slate-950 to-cyan-950/40 p-5" x-data="{ copied: false }">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-white">Pre-Save Link</h3>
                        <p class="mt-1 max-w-2xl text-sm text-slate-400">Share this public page with your fans. Opening the link starts the Spotify pre-save or opens Spotify directly when the release is already live.</p>
                    </div>
                </div>

                <div class="mt-4 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('presave.show', $track->id) }}" target="_blank" rel="noopener noreferrer"
                        class="flex-1 truncate rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-purple-300 underline underline-offset-2 transition hover:bg-white/10">
                        {{ route('presave.show', $track->id) }}
                    </a>
                    <button type="button"
                        @click="navigator.clipboard.writeText('{{ route('presave.show', $track->id) }}').then(() => { copied = true; setTimeout(() => copied = false, 2000); })"
                        class="rounded-2xl bg-slate-800 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-700">
                        <span x-show="!copied">Copy Link</span>
                        <span x-show="copied" x-cloak>Copied</span>
                    </button>
                </div>

                @if($track->release_date)
                <div class="mt-3 flex items-center gap-5 text-xs text-slate-400">
                    <div class="text-sm text-slate-400">
                        <span class="text-white font-semibold">{{ number_format($preSaves) }}</span> pre-saves
                    </div>
                    <span>Release: {{ $track->release_date->format('n/j/Y') }}</span>
                </div>
                @endif
            </div>
            @endif

            <!-- Audio Player (if released) -->
            @if($track->release_type === 'single' && $track->audio_file && $track->status === 'Released')
            <div class="bg-gray-900 rounded-2xl border border-white/5 p-4">
                <h3 class="text-sm font-semibold text-white mb-3">Anteprima dell'audio</h3>
                <audio controls class="w-full" style="accent-color: #7c3aed;">
                    <source src="{{ route('files.audio', $track->id) }}" type="audio/mpeg">
                        Il tuo browser non supporta la riproduzione audio.
                </audio>
            </div>
            @endif

            <!-- Album Tracks -->
            @if($track->release_type === 'album' && $track->album_tracks)
            <div class="bg-gray-900 rounded-2xl border border-white/5 overflow-hidden">
                <div class="p-4 border-b border-white/5">
                    <h3 class="text-sm font-semibold text-white">Tracce Album</h3>
                </div>
                <div class="divide-y divide-white/5">
                    @foreach($track->album_tracks as $index => $albumTrack)
                    <div class="flex items-center space-x-3 p-4">
                        <span class="text-gray-600 text-sm w-6 text-center">{{ $index + 1 }}</span>
                        <div class="flex-1 min-w-0">
                            <p class="text-white text-sm truncate">{{ $albumTrack['title'] ?? 'Track '.($index+1) }}</p>
                        </div>
                        @if(isset($albumTrack['audio_file']))
                        <div class="flex items-center gap-2">
                            @if($track->status === 'Released')
                            <audio controls class="h-8" style="accent-color: #7c3aed;">
                                <source src="{{ route('files.album-track', [$track->id, $index]) }}" type="audio/mpeg">
                            </audio>
                            @endif
                            <a href="{{ route('files.album-track', [$track->id, $index]) }}?download=1" class="p-1.5 text-gray-400 hover:text-white rounded-lg hover:bg-white/10 transition" title="Download track">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            </a>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Main release info (all submitted) -->
            <div class="bg-gray-900 rounded-2xl border border-white/5 p-4">
                <h3 class="text-sm font-semibold text-white mb-4">Informazioni sul rilascio</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                    <div><span class="text-gray-500">Titolo</span><p class="text-gray-200 mt-0.5">{{ $track->title ?: '—' }}</p></div>
                    @if($track->release_type === 'album')
                    <div><span class="text-gray-500">Titolo Album</span><p class="text-gray-200 mt-0.5">{{ $track->album_title ?: '—' }}</p></div>
                    <div><span class="text-gray-500">Titolo della traccia principale</span><p class="text-gray-200 mt-0.5">{{ $track->main_track_title ?: '—' }}</p></div>
                    @endif
                    <div><span class="text-gray-500">Artisti</span><p class="text-gray-200 mt-0.5">{{ $track->artists ?: '—' }}</p></div>
                    <div><span class="text-gray-500">Genere primario</span><p class="text-gray-200 mt-0.5">{{ $track->primary_genre ?: '—' }}</p></div>
                    <div><span class="text-gray-500">Genere secondario</span><p class="text-gray-200 mt-0.5">{{ $track->secondary_genre ?: '—' }}</p></div>
                    <div><span class="text-gray-500">Data di rilascio</span><p class="text-gray-200 mt-0.5">{{ $track->release_date ? $track->release_date->format('M d, Y') : '—' }}</p></div>
                    <div><span class="text-gray-500">Spotify/Apple</span><p class="text-gray-200 mt-0.5">{{ $track->has_spotify_apple ?: '—' }}</p></div>
                </div>
            </div>

            <!-- Artist & credits -->
            <div class="bg-gray-900 rounded-2xl border border-white/5 p-4">
                <h3 class="text-sm font-semibold text-white mb-4">Artista e crediti</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                    <div><span class="text-gray-500">Nome di battesimo</span><p class="text-gray-200 mt-0.5">{{ $track->first_name ?: '—' }}</p></div>
                    <div><span class="text-gray-500">Cognome</span><p class="text-gray-200 mt-0.5">{{ $track->last_name ?: '—' }}</p></div>
                    <div><span class="text-gray-500">Nome d'arte</span><p class="text-gray-200 mt-0.5">{{ $track->stage_name ?: '—' }}</p></div>
                    <div><span class="text-gray-500">Dotato</span><p class="text-gray-200 mt-0.5">{{ $track->featuring_artists ?: '—' }}</p></div>
                    <div><span class="text-gray-500">Autori</span><p class="text-gray-200 mt-0.5">{{ $track->authors ?: '—' }}</p></div>
                    <div><span class="text-gray-500">Compositori</span><p class="text-gray-200 mt-0.5">{{ $track->composers ?: '—' }}</p></div>
                    <div><span class="text-gray-500">Produttore</span><p class="text-gray-200 mt-0.5">{{ $track->producer ?: '—' }}</p></div>
                </div>
            </div>

            <!-- Additional details -->
            <div class="bg-gray-900 rounded-2xl border border-white/5 p-4">
                <h3 class="text-sm font-semibold text-white mb-4">Ulteriori dettagli</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                    <div><span class="text-gray-500">ISRC</span><p class="text-gray-200 mt-0.5 font-mono text-xs">{{ $track->isrc ?: '—' }}</p></div>
                    <div><span class="text-gray-500">UPC</span><p class="text-gray-200 mt-0.5 font-mono text-xs">{{ $track->upc ?: '—' }}</p></div>
                    <div><span class="text-gray-500">Esplicito</span><p class="text-gray-200 mt-0.5">{{ $track->is_explicit ? 'Yes' : 'No' }}</p></div>
                    <div><span class="text-gray-500">Battere YouTube</span><p class="text-gray-200 mt-0.5">{{ $track->is_youtube_beat ? 'Yes' : 'No' }}</p></div>
                    <div><span class="text-gray-500">Ha la licenza</span><p class="text-gray-200 mt-0.5">{{ $track->has_license ? 'Yes' : 'No' }}</p></div>
                    <div><span class="text-gray-500">Ora di inizio di TikTok</span><p class="text-gray-200 mt-0.5">{{ $track->tik_tok_start_time ?: '—' }}</p></div>
                    <div><span class="text-gray-500">Durata della canzone</span><p class="text-gray-200 mt-0.5">{{ $track->song_duration ?: '—' }}</p></div>
                    <div><span class="text-gray-500">Società CM</span><p class="text-gray-200 mt-0.5">{{ $track->cm_society ?: '—' }}</p></div>
                    <div><span class="text-gray-500">Posizione della SIAE</span><p class="text-gray-200 mt-0.5">{{ $track->siae_position ?: '—' }}</p></div>
                </div>
                @if($track->short_bio)
                <div class="mt-3"><span class="text-gray-500 block mb-1">Breve biografia</span><p class="text-gray-200 text-sm whitespace-pre-wrap">{{ $track->short_bio }}</p></div>
                @endif
                @if($track->track_description)
                <div class="mt-3"><span class="text-gray-500 block mb-1">Descrizione della traccia</span><p class="text-gray-200 text-sm whitespace-pre-wrap">{{ $track->track_description }}</p></div>
                @endif
                @if($track->distribution_details)
                <div class="mt-3"><span class="text-gray-500 block mb-1">Dettagli sulla distribuzione</span><p class="text-gray-200 text-sm whitespace-pre-wrap">{{ $track->distribution_details }}</p></div>
                @endif
            </div>

            <!-- Distribution Links -->
            @if($track->spotify_link || $track->apple_music_link || $track->tik_tok_link || $track->youtube_link)
            <div class="bg-gray-900 rounded-2xl border border-white/5 p-4">
                <h3 class="text-sm font-semibold text-white mb-4">Collegamenti di distribuzione</h3>
                <div class="space-y-2">
                    @if($track->spotify_link)
                    <a href="{{ $track->spotify_link }}" target="_blank" class="flex items-center space-x-3 text-sm text-gray-300 hover:text-white transition">
                        <span class="w-6 h-6 bg-green-600/20 rounded flex items-center justify-center text-green-400 text-xs">🎵</span>
                        <span>Spotify</span>
                        <svg class="w-3 h-3 text-gray-500 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                    @endif
                    @if($track->apple_music_link)
                    <a href="{{ $track->apple_music_link }}" target="_blank" class="flex items-center space-x-3 text-sm text-gray-300 hover:text-white transition">
                        <span class="w-6 h-6 bg-pink-600/20 rounded flex items-center justify-center text-pink-400 text-xs">🍎</span>
                        <span>Apple Music</span>
                        <svg class="w-3 h-3 text-gray-500 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                    @endif
                    @if($track->youtube_link)
                    <a href="{{ $track->youtube_link }}" target="_blank" class="flex items-center space-x-3 text-sm text-gray-300 hover:text-white transition">
                        <span class="w-6 h-6 bg-red-600/20 rounded flex items-center justify-center text-red-400 text-xs">▶️</span>
                        <span>YouTube</span>
                        <svg class="w-3 h-3 text-gray-500 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                    @endif
                    @if($track->tik_tok_link)
                    <a href="{{ $track->tik_tok_link }}" target="_blank" class="flex items-center space-x-3 text-sm text-gray-300 hover:text-white transition">
                        <span class="w-6 h-6 bg-gray-700/50 rounded flex items-center justify-center text-gray-400 text-xs">🎵</span>
                        <span>TikTok</span>
                        <svg class="w-3 h-3 text-gray-500 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                    @endif
                </div>
            </div>
            @endif

            <!-- Description / Lyrics -->
            @if($track->description || $track->lyrics)
            <div class="bg-gray-900 rounded-2xl border border-white/5 p-4" x-data="{ tab: 'desc' }">
                <div class="flex space-x-4 mb-4 border-b border-white/5 pb-3">
                    @if($track->description)
                    <button @click="tab='desc'" :class="tab==='desc' ? 'text-white border-b-2 border-purple-500' : 'text-gray-500'" class="text-sm font-medium pb-1 -mb-3.5 transition">Descrizione</button>
                    @endif
                    @if($track->lyrics)
                    <button @click="tab='lyrics'" :class="tab==='lyrics' ? 'text-white border-b-2 border-purple-500' : 'text-gray-500'" class="text-sm font-medium pb-1 -mb-3.5 transition">Testi</button>
                    @endif
                </div>
                @if($track->description)
                <div x-show="tab==='desc'" class="text-gray-300 text-sm leading-relaxed whitespace-pre-wrap">{{ $track->description }}</div>
                @endif
                @if($track->lyrics)
                <div x-show="tab==='lyrics'" x-cloak class="text-gray-300 text-sm leading-relaxed whitespace-pre-wrap font-mono">{{ $track->lyrics }}</div>
                @endif
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
