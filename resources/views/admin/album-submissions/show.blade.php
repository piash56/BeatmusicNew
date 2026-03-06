@extends('layouts.admin')

@section('title', $track->album_title ?? $track->title)
@section('page-title', 'Dettagli dellalbum')

@section('content')
<div class="space-y-6">
    <a href="{{ route('admin.album-submissions') }}" class="flex items-center space-x-2 text-gray-400 hover:text-white transition text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        <span>Torniamo agli album</span>
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="space-y-4">
            <div class="bg-gray-900 rounded-2xl border border-white/5 overflow-hidden">
                @if($track->cover_art)
                    <img src="{{ $track->cover_art_url }}" class="w-full aspect-square object-cover">
                @else
                    <div class="w-full aspect-square bg-indigo-900/20 flex items-center justify-center">
                        <svg class="w-16 h-16 text-indigo-400/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2z"/></svg>
                    </div>
                @endif
                @if($track->cover_art)
                <div class="p-3 border-t border-white/5 flex justify-center">
                    <a href="{{ route('files.cover.download', $track->id) }}" class="inline-flex items-center gap-2 px-3 py-2 bg-white/10 hover:bg-white/15 text-gray-300 text-sm rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Scarica la copertina
                    </a>
                </div>
                @endif
                <div class="p-4">
                    <h2 class="font-bold text-white text-lg">{{ $track->album_title ?? $track->title }}</h2>
                    <p class="text-gray-400 text-sm">{{ $track->artists }}</p>
                    <div class="mt-3 space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-gray-500">Tipo</span><span class="text-gray-200 capitalize">{{ $track->release_type }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Genere</span><span class="text-gray-200">{{ $track->primary_genre }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Tracce</span><span class="text-gray-200">{{ is_array($track->album_tracks) ? count($track->album_tracks) : 0 }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Data di rilascio</span><span class="text-gray-200">{{ $track->release_date ? $track->release_date->format('M d, Y') : '—' }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Esplicito</span><span class="text-gray-200">{{ $track->is_explicit ? 'Yes' : 'No' }}</span></div>
                        @if($track->isrc)<div class="flex justify-between"><span class="text-gray-500">ISRC</span><span class="text-gray-200 font-mono text-xs">{{ $track->isrc }}</span></div>@endif
                        @if($track->upc)<div class="flex justify-between"><span class="text-gray-500">UPC</span><span class="text-gray-200 font-mono text-xs">{{ $track->upc }}</span></div>@endif
                        <div class="flex justify-between"><span class="text-gray-500">Flussi</span><span class="text-gray-200">{{ number_format($track->total_streams ?? 0) }}</span></div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-900 rounded-2xl border border-white/5 p-4">
                <h3 class="text-sm font-semibold text-white mb-3">Aggiorna stato</h3>
                <form method="POST" action="{{ route('admin.track-submissions.status', $track->id) }}">
                    @csrf @method('PUT')
                    <select name="status" class="w-full bg-gray-800 border border-white/10 text-gray-300 px-3 py-2 rounded-lg text-sm mb-3">
                        @foreach(['Draft','On Request','On Process','Released','Rejected','Modify Pending','Modify Process','Modify Released','Modify Rejected'] as $s)
                        <option value="{{ $s }}" {{ $track->status === $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="w-full px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-lg transition">Aggiorna stato</button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-4">
            @if($track->status === 'Released')
            <div class="rounded-2xl border border-cyan-500/20 bg-gradient-to-br from-slate-950 to-cyan-950/40 p-5" x-data="{ copied: false }">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-white">Pre-Save Link</h3>
                        <p class="mt-1 max-w-2xl text-sm text-slate-400">Share this public page with fans. Opening the link starts the Spotify pre-save flow.</p>
                    </div>
                    <div class="text-sm text-slate-400">
                        <span class="font-semibold text-white">{{ number_format($preSaves->total()) }}</span> pre-saves
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
                    <span>Release: {{ $track->release_date->format('n/j/Y') }}</span>
                    <span>Spotify only for now</span>
                </div>
                @endif
            </div>
            @endif

            @if($track->user)
            <div class="bg-gray-900 rounded-2xl border border-white/5 p-5">
                <h3 class="font-semibold text-white mb-3">Artista (utente)</h3>
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-purple-600 to-indigo-600 rounded-full flex items-center justify-center text-white text-sm font-bold">{{ strtoupper(substr($track->user->full_name ?? 'U', 0, 2)) }}</div>
                    <div>
                        <p class="text-white font-medium">{{ $track->user->full_name ?? '—' }}</p>
                        <p class="text-gray-400 text-sm">{{ $track->user->email ?? '—' }}</p>
                    </div>
                    <a href="{{ route('admin.users.show', $track->user->id) }}" class="ml-auto text-purple-400 hover:text-purple-300 text-xs">Visualizza profilo →</a>
                </div>
            </div>
            @endif

            <!-- Album Tracks -->
            @if(is_array($track->album_tracks) && count($track->album_tracks) > 0)
            <div class="bg-gray-900 rounded-2xl border border-white/5 overflow-hidden">
                <div class="p-4 border-b border-white/5">
                    <h3 class="font-semibold text-white">Tracce dell'album ({{ count($track->album_tracks) }})</h3>
                </div>
                <div class="divide-y divide-white/5">
                    @foreach($track->album_tracks as $i => $albumTrack)
                    <div class="p-4 flex items-center space-x-3">
                        <span class="text-gray-600 w-6 text-center text-sm">{{ $i + 1 }}</span>
                        <div class="flex-1 min-w-0">
                            <p class="text-white text-sm truncate">{{ $albumTrack['title'] ?? 'Track '.($i+1) }}</p>
                        </div>
                        @if(isset($albumTrack['audio_file']) || isset($albumTrack['audioFile']))
                        <div class="flex items-center gap-2">
                            <audio controls class="h-8" style="accent-color: #7c3aed;">
                                <source src="{{ route('files.album-track', [$track->id, $i]) }}" type="audio/mpeg">
                            </audio>
                            <a href="{{ route('files.album-track', [$track->id, $i]) }}?download=1" class="p-1.5 text-gray-400 hover:text-white rounded-lg hover:bg-white/10 transition" title="Download track">
                                <svg class="w-4 h-4" fill="none" stroke="CurrentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            </a>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Release Information -->
            <div class="bg-gray-900 rounded-2xl border border-white/5 p-5">
                <h3 class="text-sm font-semibold text-white mb-4">Informazioni sul rilascio</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                    <div><span class="text-gray-500">Titolo</span><p class="text-gray-200 mt-0.5">{{ $track->title ?: '—' }}</p></div>
                    <div><span class="text-gray-500">Titolo dell'album</span><p class="text-gray-200 mt-0.5">{{ $track->album_title ?: '—' }}</p></div>
                    <div><span class="text-gray-500">Titolo della traccia principale</span><p class="text-gray-200 mt-0.5">{{ $track->main_track_title ?: '—' }}</p></div>
                    <div><span class="text-gray-500">Artisti</span><p class="text-gray-200 mt-0.5">{{ $track->artists ?: '—' }}</p></div>
                    <div><span class="text-gray-500">Genere primario</span><p class="text-gray-200 mt-0.5">{{ $track->primary_genre ?: '—' }}</p></div>
                    <div><span class="text-gray-500">Genere secondario</span><p class="text-gray-200 mt-0.5">{{ $track->secondary_genre ?: '—' }}</p></div>
                    <div><span class="text-gray-500">Release date</span><p class="text-gray-200 mt-0.5">{{ $track->release_date ? $track->release_date->format('M d, Y') : '—' }}</p></div>
                    <div><span class="text-gray-500">Spotify/Apple</span><p class="text-gray-200 mt-0.5">{{ $track->has_spotify_apple ?: '—' }}</p></div>
                </div>
            </div>

            <!-- Artist & Credits -->
            <div class="bg-gray-900 rounded-2xl border border-white/5 p-5">
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

            <!-- Additional Details -->
            <div class="bg-gray-900 rounded-2xl border border-white/5 p-5">
                <h3 class="text-sm font-semibold text-white mb-4">Additional Details</h3>
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
            <div class="bg-gray-900 rounded-2xl border border-white/5 p-5">
                <h3 class="text-sm font-semibold text-white mb-4">Collegamenti di distribuzione</h3>
                <div class="space-y-2 text-sm">
                    @if($track->spotify_link)<a href="{{ $track->spotify_link }}" target="_blank" class="flex items-center space-x-2 text-green-400 hover:text-green-300">Spotify</a>@endif
                    @if($track->apple_music_link)<a href="{{ $track->apple_music_link }}" target="_blank" class="flex items-center space-x-2 text-pink-400 hover:text-pink-300">Apple Music</a>@endif
                    @if($track->tik_tok_link)<a href="{{ $track->tik_tok_link }}" target="_blank" class="flex items-center space-x-2 text-gray-400 hover:text-white">TikTok</a>@endif
                    @if($track->youtube_link)<a href="{{ $track->youtube_link }}" target="_blank" class="flex items-center space-x-2 text-red-400 hover:text-red-300">YouTube</a>@endif
                </div>
            </div>
            @endif

            <!-- Description & Lyrics -->
            @if($track->description || $track->lyrics)
            <div class="bg-gray-900 rounded-2xl border border-white/5 p-5">
                @if($track->description)
                    <h3 class="text-sm font-semibold text-white mb-2">Descrizione</h3>
                    <p class="text-gray-400 text-sm leading-relaxed whitespace-pre-wrap">{{ $track->description }}</p>
                @endif
                @if($track->lyrics)
                    <h3 class="text-sm font-semibold text-white mt-3 mb-2">Testi</h3>
                    <p class="text-gray-400 text-sm leading-relaxed whitespace-pre-wrap font-mono">{{ $track->lyrics }}</p>
                @endif
            </div>
            @endif

            <!-- Pre-saves -->
            <div class="bg-gray-900 rounded-2xl border border-white/5 p-5">
                <h3 class="font-semibold text-white mb-3">Pre-saves ({{ $preSaves->total() }})</h3>
                @if($preSaves->isEmpty())
                    <p class="text-gray-500 text-sm">No pre-saves yet.</p>
                @else
                    <div class="space-y-2">
                        @foreach($preSaves as $ps)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-300">{{ $ps->fan_email }}</span>
                            <span class="text-gray-500 text-xs">{{ $ps->created_at->format('M d, Y') }}</span>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-3">{{ $preSaves->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
