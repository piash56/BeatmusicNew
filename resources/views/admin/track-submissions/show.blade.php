@extends('layouts.admin')

@section('title', $track->title)
@section('page-title', 'Dettagli traccia')

@section('content')
<div class="space-y-6" x-data="{ editing: false }">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.track-submissions') }}" class="flex items-center space-x-2 text-gray-400 hover:text-white transition text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            <span>Torna agli invii</span>
        </a>
        <button type="button" @click="editing = !editing" class="px-4 py-2 rounded-lg text-sm font-medium transition"
            :class="editing ? 'bg-gray-600 hover:bg-gray-700 text-white' : 'bg-purple-600 hover:bg-purple-700 text-white'">
            <span x-show="!editing">Modifica Rilascio</span>
            <span x-show="editing" x-cloak>Cancellare</span>
        </button>
    </div>

    @if(session('success'))
    <div class="p-3 rounded-xl bg-green-900/30 border border-green-500/30 text-green-300 text-sm">{{ session('success') }}</div>
    @endif

    @if($errors->any())
    <div class="p-3 rounded-xl border border-red-500/30 bg-red-900/10 text-red-400 text-sm">
        <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <!-- View mode: all release info -->
    <div x-show="!editing" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="space-y-4">
            <div class="bg-gray-900 rounded-2xl border border-white/5 overflow-hidden">
                @if($track->cover_art)
                    <img src="{{ $track->cover_art_url }}" class="w-full aspect-square object-cover">
                @else
                    <div class="w-full aspect-square bg-purple-900/20 flex items-center justify-center">
                        <svg class="w-16 h-16 text-purple-400/30" fill="currentColor" viewBox="0 0 20 20"><path d="M18 3a1 1 0 00-1.196-.98l-10 2A1 1 0 006 5v9.114A4.369 4.369 0 005 14c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V7.82l8-1.6v5.894A4.37 4.37 0 0015 12c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V3z"/></svg>
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
                    <h2 class="font-bold text-white text-lg">{{ $track->title }}</h2>
                    <p class="text-gray-400 text-sm">{{ $track->artists }}</p>
                    <div class="mt-3 space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-gray-500">Tipo</span><span class="text-gray-200 capitalize">{{ $track->release_type }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Genere</span><span class="text-gray-200">{{ $track->primary_genre }}</span></div>
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
                        <span class="font-semibold text-white">{{ number_format($preSaves->total()) }}</span> pre-saves
                    </div>
                    <span>Release: {{ $track->release_date->format('n/j/Y') }}</span>
                </div>
                @endif
            </div>
            @endif

            @if($track->user)
            <div class="bg-gray-900 rounded-2xl border border-white/5 p-5">
                <h3 class="font-semibold text-white mb-3">Artista (Utente)</h3>
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
            @if($track->audio_file)
            <div class="bg-gray-900 rounded-2xl border border-white/5 p-5">
                <h3 class="font-semibold text-white mb-3">File audio</h3>
                <audio controls class="w-full mb-3" style="accent-color: #7c3aed;"><source src="{{ route('files.audio', $track->id) }}" type="audio/mpeg"></audio>
                <a href="{{ route('files.audio', $track->id) }}?download=1" class="inline-flex items-center gap-2 px-3 py-2 bg-white/10 hover:bg-white/15 text-gray-300 text-sm rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Scarica traccia
                </a>
            </div>
            @endif
            <div class="bg-gray-900 rounded-2xl border border-white/5 p-5">
                <h3 class="text-sm font-semibold text-white mb-4">Informazioni sul rilascio</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                    <div><span class="text-gray-500">Titolo</span><p class="text-gray-200 mt-0.5">{{ $track->title ?: '—' }}</p></div>
                    @if($track->release_type === 'album')
                    <div><span class="text-gray-500">Titolo dell'album</span><p class="text-gray-200 mt-0.5">{{ $track->album_title ?: '—' }}</p></div>
                    <div><span class="text-gray-500">Titolo della traccia principale</span><p class="text-gray-200 mt-0.5">{{ $track->main_track_title ?: '—' }}</p></div>
                    @endif
                    <div><span class="text-gray-500">Artisti</span><p class="text-gray-200 mt-0.5">{{ $track->artists ?: '—' }}</p></div>
                    <div><span class="text-gray-500">Genere primario</span><p class="text-gray-200 mt-0.5">{{ $track->primary_genre ?: '—' }}</p></div>
                    <div><span class="text-gray-500">Genere secondario</span><p class="text-gray-200 mt-0.5">{{ $track->secondary_genre ?: '—' }}</p></div>
                    <div><span class="text-gray-500">Data di rilascio</span><p class="text-gray-200 mt-0.5">{{ $track->release_date ? $track->release_date->format('M d, Y') : '—' }}</p></div>
                    <div><span class="text-gray-500">Spotify/Apple</span><p class="text-gray-200 mt-0.5">{{ $track->has_spotify_apple ?: '—' }}</p></div>
                </div>
            </div>
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
            <div class="bg-gray-900 rounded-2xl border border-white/5 p-5">
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
                @if($track->short_bio)<div class="mt-3"><span class="text-gray-500 block mb-1">Breve biografia</span><p class="text-gray-200 text-sm whitespace-pre-wrap">{{ $track->short_bio }}</p></div>@endif
                @if($track->track_description)<div class="mt-3"><span class="text-gray-500 block mb-1">Descrizione della traccia</span><p class="text-gray-200 text-sm whitespace-pre-wrap">{{ $track->track_description }}</p></div>@endif
                @if($track->distribution_details)<div class="mt-3"><span class="text-gray-500 block mb-1">Dettagli sulla distribuzione</span><p class="text-gray-200 text-sm whitespace-pre-wrap">{{ $track->distribution_details }}</p></div>@endif
            </div>
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
            @if($track->description || $track->lyrics)
            <div class="bg-gray-900 rounded-2xl border border-white/5 p-5">
                @if($track->description)<h3 class="text-sm font-semibold text-white mb-2">Descrizione</h3><p class="text-gray-400 text-sm leading-relaxed whitespace-pre-wrap">{{ $track->description }}</p>@endif
                @if($track->lyrics)<h3 class="text-sm font-semibold text-white mt-3 mb-2">Testi</h3><p class="text-gray-400 text-sm leading-relaxed whitespace-pre-wrap font-mono">{{ $track->lyrics }}</p>@endif
            </div>
            @endif
            <div class="bg-gray-900 rounded-2xl border border-white/5 p-5">
                <h3 class="font-semibold text-white mb-3">Pre-saves ({{ $preSaves->total() }})</h3>
                @if($preSaves->isEmpty())<p class="text-gray-500 text-sm">No pre-saves yet.</p>
                @else
                <div class="space-y-2">@foreach($preSaves as $ps)<div class="flex justify-between text-sm"><span class="text-gray-300">{{ $ps->fan_email }}</span><span class="text-gray-500 text-xs">{{ $ps->created_at->format('M d, Y') }}</span></div>@endforeach</div>
                <div class="mt-3">{{ $preSaves->links() }}</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Edit mode: full form -->
    <div x-show="editing" x-cloak class="bg-gray-900 rounded-2xl border border-white/5 p-6">
        <form method="POST" action="{{ route('admin.track-submissions.update', $track->id) }}">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-sm text-gray-400 mb-1">Titolo</label><input type="text" name="title" value="{{ old('title', $track->title) }}" class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-lg"></div>
                @if($track->release_type === 'album')
                <div><label class="block text-sm text-gray-400 mb-1">Titolo dell'album</label><input type="text" name="album_title" value="{{ old('album_title', $track->album_title) }}" class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-lg"></div>
                <div><label class="block text-sm text-gray-400 mb-1">Titolo della traccia principale</label><input type="text" name="main_track_title" value="{{ old('main_track_title', $track->main_track_title) }}" class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-lg"></div>
                @endif
                <div><label class="block text-sm text-gray-400 mb-1">Artisti</label><input type="text" name="artists" value="{{ old('artists', $track->artists) }}" class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-lg"></div>
                <div><label class="block text-sm text-gray-400 mb-1">PrimaryGenere primario genre</label><input type="text" name="primary_genre" value="{{ old('primary_genre', $track->primary_genre) }}" class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-lg"></div>
                <div><label class="block text-sm text-gray-400 mb-1">Genere secondario</label><input type="text" name="secondary_genre" value="{{ old('secondary_genre', $track->secondary_genre) }}" class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-lg"></div>
                <div><label class="block text-sm text-gray-400 mb-1">Data di rilascio</label><input type="date" name="release_date" value="{{ old('release_date', $track->release_date ? $track->release_date->format('Y-m-d') : '') }}" class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-lg"></div>
                <div><label class="block text-sm text-gray-400 mb-1">Nome di battesimo</label><input type="text" name="first_name" value="{{ old('first_name', $track->first_name) }}" class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-lg"></div>
                <div><label class="block text-sm text-gray-400 mb-1">Cognome</label><input type="text" name="last_name" value="{{ old('last_name', $track->last_name) }}" class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-lg"></div>
                <div><label class="block text-sm text-gray-400 mb-1">Nome d'arte</label><input type="text" name="stage_name" value="{{ old('stage_name', $track->stage_name) }}" class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-lg"></div>
                <div><label class="block text-sm text-gray-400 mb-1">Dotato</label><input type="text" name="featuring_artists" value="{{ old('featuring_artists', $track->featuring_artists) }}" class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-lg"></div>
                <div><label class="block text-sm text-gray-400 mb-1">Autori</label><input type="text" name="authors" value="{{ old('authors', $track->authors) }}" class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-lg"></div>
                <div><label class="block text-sm text-gray-400 mb-1">Compositori</label><input type="text" name="composers" value="{{ old('composers', $track->composers) }}" class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-lg"></div>
                <div><label class="block text-sm text-gray-400 mb-1">Produttore</label><input type="text" name="producer" value="{{ old('producer', $track->producer) }}" class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-lg"></div>
                <div><label class="block text-sm text-gray-400 mb-1">ISRC</label><input type="text" name="isrc" value="{{ old('isrc', $track->isrc) }}" class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-lg"></div>
                <div><label class="block text-sm text-gray-400 mb-1">UPC</label><input type="text" name="upc" value="{{ old('upc', $track->upc) }}" class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-lg"></div>
                <div class="md:col-span-2 flex items-center gap-4">
                    <label class="flex items-center gap-2 text-gray-400 text-sm"><input type="checkbox" name="is_explicit" value="1" {{ old('is_explicit', $track->is_explicit) ? 'checked' : '' }} class="rounded border-white/20 text-purple-600">Esplicito</label>
                    <label class="flex items-center gap-2 text-gray-400 text-sm"><input type="checkbox" name="is_youtube_beat" value="1" {{ old('is_youtube_beat', $track->is_youtube_beat) ? 'checked' : '' }} class="rounded border-white/20 text-purple-600">YouTube beat</label>
                    <label class="flex items-center gap-2 text-gray-400 text-sm"><input type="checkbox" name="has_license" value="1" {{ old('has_license', $track->has_license) ? 'checked' : '' }} class="rounded border-white/20 text-purple-600">Ha la licenza</label>
                </div>
                <div><label class="block text-sm text-gray-400 mb-1">Ora di inizio di TikTok</label><input type="text" name="tik_tok_start_time" value="{{ old('tik_tok_start_time', $track->tik_tok_start_time) }}" class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-lg"></div>
                <div><label class="block text-sm text-gray-400 mb-1">Durata della canzone</label><input type="text" name="song_duration" value="{{ old('song_duration', $track->song_duration) }}" class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-lg"></div>
                <div><label class="block text-sm text-gray-400 mb-1">CM society</label><input type="text" name="cm_society" value="{{ old('cm_society', $track->cm_society) }}" class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-lg"></div>
                <div><label class="block text-sm text-gray-400 mb-1">SIAE position</label><input type="text" name="siae_position" value="{{ old('siae_position', $track->siae_position) }}" class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-lg"></div>
                <div><label class="block text-sm text-gray-400 mb-1">Ha Spotify/Apple</label><input type="text" name="has_spotify_apple" value="{{ old('has_spotify_apple', $track->has_spotify_apple) }}" class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-lg" placeholder="YES or NO"></div>
                <div><label class="block text-sm text-gray-400 mb-1">Spotify link</label><input type="url" name="spotify_link" value="{{ old('spotify_link', $track->spotify_link) }}" class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-lg"></div>
                <div><label class="block text-sm text-gray-400 mb-1">Apple Music link</label><input type="url" name="apple_music_link" value="{{ old('apple_music_link', $track->apple_music_link) }}" class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-lg"></div>
                <div><label class="block text-sm text-gray-400 mb-1">TikTok link</label><input type="url" name="tik_tok_link" value="{{ old('tik_tok_link', $track->tik_tok_link) }}" class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-lg"></div>
                <div><label class="block text-sm text-gray-400 mb-1">YouTube link</label><input type="url" name="youtube_link" value="{{ old('youtube_link', $track->youtube_link) }}" class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-lg"></div>
                <div class="md:col-span-2"><label class="block text-sm text-gray-400 mb-1">Breve biografia</label><textarea name="short_bio" rows="2" class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-lg">{{ old('short_bio', $track->short_bio) }}</textarea></div>
                <div class="md:col-span-2"><label class="block text-sm text-gray-400 mb-1">Descrizione della traccia</label><textarea name="track_description" rows="2" class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-lg">{{ old('track_description', $track->track_description) }}</textarea></div>
                <div class="md:col-span-2"><label class="block text-sm text-gray-400 mb-1">Dettagli sulla distribuzione</label><textarea name="distribution_details" rows="2" class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-lg">{{ old('distribution_details', $track->distribution_details) }}</textarea></div>
                <div class="md:col-span-2"><label class="block text-sm text-gray-400 mb-1">Descrizione</label><textarea name="description" rows="3" class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-lg">{{ old('description', $track->description) }}</textarea></div>
                <div class="md:col-span-2"><label class="block text-sm text-gray-400 mb-1">Testi</label><textarea name="lyrics" rows="6" class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-lg">{{ old('lyrics', $track->lyrics) }}</textarea></div>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" @click="editing = false" class="px-4 py-2 bg-white/10 hover:bg-white/15 text-gray-300 rounded-lg text-sm">Cancellare</button>
                <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm">Salva modifiche</button>
            </div>
        </form>
    </div>
</div>
@endsection
