@extends('layouts.dashboard')

@section('title', 'Playlist editoriali')
@section('page-title', 'Playlist editoriali')
@section('page-subtitle', 'Invia i tuoi brani alle playlist curate')

@section('content')
@php
    $tab = request('tab', 'submit');
    $waitingCount = $submissions->total();
    $reachedCount = $published->count();
@endphp

<div class="space-y-6">

    <!-- Tabs -->
    <div class="flex bg-white/5 border border-white/10 rounded-xl p-1 space-x-1">
        <a href="{{ route('dashboard.playlists', ['tab' => 'submit']) }}"
           class="flex-1 px-4 py-2 rounded-lg text-sm font-medium transition text-center {{ $tab === 'submit' ? 'bg-purple-600 text-white' : 'text-gray-400 hover:text-white' }}">
            Invia brani
        </a>
        <a href="{{ route('dashboard.playlists', ['tab' => 'waiting']) }}"
           class="flex-1 px-4 py-2 rounded-lg text-sm font-medium transition text-center {{ $tab === 'waiting' ? 'bg-purple-600 text-white' : 'text-gray-400 hover:text-white' }}">
            In attesa ({{ $waitingCount }})
        </a>
        <a href="{{ route('dashboard.playlists', ['tab' => 'reached']) }}"
           class="flex-1 px-4 py-2 rounded-lg text-sm font-medium transition text-center {{ $tab === 'reached' ? 'bg-purple-600 text-white' : 'text-gray-400 hover:text-white' }}">
            Playlist Raggiunte ({{ $reachedCount }})
        </a>
    </div>

    <!-- TAB 1: Submit songs -->
    @if($tab === 'submit')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6"
         x-data="{
            selectedTrackId: null,
            search: '',
            platform: '',
            playlistName: '',
            submitting: false,
            canSubmit() {
                return this.selectedTrackId && this.platform && this.playlistName;
            }
         }">
        <!-- Left: track selection -->
        <div class="lg:col-span-2 space-y-4">
            <div>
                <h3 class="text-white font-semibold mb-1">Seleziona una canzone</h3>
                <p class="text-gray-400 text-sm">Scegli uno dei brani pubblicati da proporre alle playlist editoriali.</p>
            </div>
            <div class="mb-3">
                <input type="text"
                       x-model="search"
                       placeholder="Cerca per nome del brano, artista o genere..."
                       class="w-full bg-gray-900 border border-white/10 text-white placeholder-gray-500 px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
            </div>

            @if($releasedTracks->isEmpty())
                <div class="p-10 text-center bg-gray-900 rounded-2xl border border-white/5">
                    <p class="text-gray-400 text-sm">Nessun brano rilasciato ancora.</p>
                    <p class="text-gray-600 text-xs mt-1">Pubblica un singolo o un album per iniziare a inviarlo alle playlist.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach($releasedTracks as $track)
                        @php
                            $isAlbum = $track->release_type === 'album';
                            $title = $isAlbum ? ($track->album_title ?: $track->title) : $track->title;
                            $searchText = strtolower(trim(($title ?? '') . ' ' . ($track->artists ?? '') . ' ' . ($track->primary_genre ?? '')));
                        @endphp
                        <button type="button"
                                @click="selectedTrackId = {{ $track->id }}"
                                class="text-left bg-gray-900 border rounded-2xl p-4 flex flex-col gap-3 transition
                                       hover:border-purple-500/60 hover:bg-white/5
                                       {{ $loop->first ? '' : '' }}"
                                :class="selectedTrackId === {{ $track->id }} ? 'border-purple-500/80 bg-purple-900/20' : 'border-white/5'"
                                x-show="!search || $el.dataset.search.includes(search.toLowerCase())"
                                data-search="{{ $searchText }}">
                            <div class="w-full aspect-square rounded-xl bg-white/5 flex items-center justify-center mb-2 overflow-hidden">
                                @if($track->cover_art)
                                    <img src="{{ $track->cover_art_url }}" alt="" class="w-full h-full object-cover">
                                @else
                                    <span class="text-2xl text-purple-400">🎵</span>
                                @endif
                            </div>
                            <div class="space-y-0.5">
                                <p class="text-white text-sm font-semibold truncate">{{ $title }}</p>
                                <p class="text-gray-400 text-xs truncate">{{ $track->artists }}</p>
                                <p class="text-gray-500 text-xs">{{ $track->primary_genre ?? 'Unknown genre' }}</p>
                            </div>
                            <div class="mt-2 flex items-center justify-between text-xs">
                                <span class="px-2 py-0.5 rounded-full border border-white/10 text-gray-300 capitalize">
                                    {{ $track->release_type }}
                                </span>
                                <span class="text-gray-500">
                                    {{ $track->status }}
                                </span>
                            </div>
                            <div class="mt-1">
                                <span class="inline-flex items-center gap-1 text-[11px] text-purple-300"
                                      x-show="selectedTrackId === {{ $track->id }}">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Selezionato</span>
                                </span>
                            </div>
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Right: submission details -->
        <div class="space-y-4">
            <div class="bg-gray-900 rounded-2xl border border-white/5 p-5">
                <h3 class="font-semibold text-white mb-1">Proponi la tua musica</h3>
                <p class="text-gray-400 text-xs mb-4">
                    Invia i tuoi brani alle playlist editoriali e raggiungi un pubblico più ampio.
                </p>

                <form method="POST"
                      action="{{ route('dashboard.playlists.submit') }}"
                      class="space-y-3"
                      @submit="if (!canSubmit()) { $event.preventDefault(); return; } submitting = true;">
                    @csrf
                    <input type="hidden" name="track_id" :value="selectedTrackId || ''">

                    <div x-data>
                        <label class="block text-xs text-gray-400 mb-1.5">Piattaforma <span class="text-red-400">*</span></label>
                        <select id="playlist-platform" name="platform" x-model="platform" required
                                class="w-full bg-gray-800 border border-white/10 text-gray-300 px-3 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
                            <option value="">Seleziona piattaforma</option>
                            @foreach($platforms as $p)
                                <option value="{{ $p }}">{{ $p }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs text-gray-400 mb-1.5">Playlist editoriale <span class="text-red-400">*</span></label>
                        <select id="playlist-name" name="playlist_name" x-model="playlistName" required
                                class="w-full bg-gray-800 border border-white/10 text-gray-300 px-3 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
                            <option value="">Seleziona playlist editoriale</option>
                            @foreach($platforms as $platformName)
                                @foreach(($playlistsByPlatform[$platformName] ?? []) as $pl)
                                    <option value="{{ $pl['name'] }}" data-platform="{{ $platformName }}">{{ $pl['name'] }}</option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>

                    <button type="submit"
                            :disabled="!canSubmit() || submitting"
                            class="w-full mt-2 px-4 py-2.5 bg-purple-600 hover:bg-purple-700 disabled:bg-purple-900/40 disabled:cursor-not-allowed text-white text-sm font-medium rounded-xl transition flex items-center justify-center gap-2">
                        <template x-if="!submitting">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h11M9 21V3m12 7l-4-4m4 4l-4 4"/></svg>
                        </template>
                        <template x-if="submitting">
                            <svg class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                        </template>
                        <span x-text="submitting ? 'Invio...' : 'Conferma invio alla playlist editoriale'"></span>
                    </button>
                    <p class="text-[11px] text-gray-500 mt-1">
                        Seleziona prima un brano rilasciato, poi scegli piattaforma e playlist.
                    </p>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- TAB 2: Waiting / all submissions -->
    @if($tab === 'waiting')
    <div class="space-y-4">
        <div>
            <h3 class="text-white font-semibold mb-1">Proposte in attesa di revisione</h3>
            <p class="text-gray-400 text-sm">Le tue canzoni inviate alle playlist editoriali.</p>
        </div>

        @if($submissions->isEmpty())
            <div class="p-10 text-center bg-gray-900 rounded-2xl border border-white/5">
                <p class="text-gray-400 text-sm">Nessuna proposta ancora.</p>
            </div>
        @else
            <div class="bg-gray-900 rounded-2xl border border-white/5 overflow-hidden">
                <table class="w-full text-sm min-w-[760px]">
                    <thead class="bg-white/5 border-b border-white/5 text-xs text-gray-400 uppercase tracking-wide">
                        <tr>
                            <th class="px-4 py-3 text-left">Tieni traccia delle informazioni</th>
                            <th class="px-4 py-3 text-left hidden sm:table-cell">Playlist editoriale</th>
                            <th class="px-4 py-3 text-left">Stato</th>
                            <th class="px-4 py-3 text-left hidden md:table-cell">Data di invio</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($submissions as $sub)
                            @php
                                $t = $sub->track;
                                $isAlbum = $t && $t->release_type === 'album';
                                $title = $isAlbum ? ($t->album_title ?: $t->title) : ($t->title ?? 'Unknown Track');
                            @endphp
                            <tr class="hover:bg-white/5 transition">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        @if($t && $t->cover_art)
                                            <img src="{{ $t->cover_art_url }}" class="w-10 h-10 rounded-lg object-cover shrink-0">
                                        @else
                                            <div class="w-10 h-10 rounded-lg bg-purple-600/20 flex items-center justify-center shrink-0">
                                                <svg class="w-4 h-4 text-purple-300" fill="currentColor" viewBox="0 0 20 20"><path d="M18 3a1 1 0 00-1.196-.98l-10 2A1 1 0 006 5v9.114A4.369 4.369 0 005 14c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V7.82l8-1.6v5.894A4.37 4.37 0 0015 12c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V3z"/></svg>
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <p class="text-white text-sm font-medium truncate">{{ $title }}</p>
                                            <p class="text-gray-400 text-xs truncate">{{ $t->artists ?? 'Unknown Artist' }}</p>
                                            <p class="text-gray-500 text-[11px]">{{ $t->primary_genre ?? 'Unknown Genre' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 hidden sm:table-cell align-top">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-gray-200 text-sm">{{ $sub->playlist_name }}</span>
                                        <div class="flex items-center gap-2 text-xs text-gray-500">
                                            <span class="px-2 py-0.5 rounded-full border border-white/10 bg-white/5">{{ $sub->platform }}</span>
                                            @if($sub->playlist_url)
                                                <a href="{{ $sub->playlist_url }}" target="_blank" class="text-purple-400 hover:text-purple-300 inline-flex items-center gap-1">
                                                    <span>Apri</span>
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 align-top">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium
                                        {{ $sub->status === 'Published' ? 'bg-green-900/50 text-green-400' :
                                           ($sub->status === 'Rejected' ? 'bg-red-900/50 text-red-400' :
                                           ($sub->status === 'Processing' ? 'bg-yellow-900/50 text-yellow-400' : 'bg-blue-900/50 text-blue-400')) }}">
                                        {{ $sub->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-400 text-xs hidden md:table-cell align-top">
                                    {{ optional($sub->submission_date ?? $sub->created_at)->format('M d, Y') }}
                                    <span class="block">{{ optional($sub->submission_date ?? $sub->created_at)->format('h:i A') }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="p-4 border-t border-white/5">
                    {{ $submissions->withQueryString()->links() }}
                </div>
            </div>
        @endif
    </div>
    @endif

    <!-- TAB 3: Playlist Reached (Published only) -->
    @if($tab === 'reached')
    <div class="space-y-4">
        <div>
            <h3 class="text-white font-semibold mb-1">Risultati misurabili</h3>
            <p class="text-gray-400 text-sm">Monitora in tempo reale tutte le playlist che hai raggiunto e analizzane l'impatto.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            <div class="bg-gray-900 rounded-2xl border border-white/5 p-4 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-purple-600/20 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-purple-300" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3a1 1 0 00-1 1v12a1 1 0 001.514.857L10 14.101l5.486 2.756A1 1 0 0017 16V4a1 1 0 00-1-1H4z"/></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Playlist Totale</p>
                    <p class="text-lg font-semibold text-white">{{ $reachedCount }}</p>
                </div>
            </div>
            <div class="bg-gray-900 rounded-2xl border border-white/5 p-4 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-green-600/20 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2z"/></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Streams Totali</p>
                    <p class="text-lg font-semibold text-white">{{ number_format($published->sum('streams')) }}</p>
                </div>
            </div>
            <div class="bg-gray-900 rounded-2xl border border-white/5 p-4 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-600/20 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2z"/></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Listeners Totali</p>
                    <p class="text-lg font-semibold text-white">{{ number_format($published->sum('listeners')) }}</p>
                </div>
            </div>
        </div>

        @if($published->isEmpty())
            <div class="p-10 text-center bg-gray-900 rounded-2xl border border-white/5">
                <p class="text-gray-400 text-sm">Nessuna playlist raggiunta ancora.</p>
            </div>
        @else
            <div class="mt-4 bg-gray-900 rounded-2xl border border-white/5 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm min-w-[880px]">
                        <thead class="bg-white/5 border-b border-white/5 text-xs text-gray-400 uppercase tracking-wide">
                            <tr>
                                <th class="px-4 py-3 text-left">Brano</th>
                                <th class="px-4 py-3 text-left hidden sm:table-cell">Playlist</th>
                                <th class="px-4 py-3 text-left hidden md:table-cell">Piattaforma</th>
                                <th class="px-4 py-3 text-left hidden md:table-cell">Streams</th>
                                <th class="px-4 py-3 text-left hidden md:table-cell">Listeners</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @foreach($published as $sub)
                                @php
                                    $t = $sub->track;
                                    $isAlbum = $t && $t->release_type === 'album';
                                    $title = $isAlbum ? ($t->album_title ?: $t->title) : ($t->title ?? 'Unknown Track');
                                @endphp
                                <tr class="hover:bg-white/5 transition">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            @if($t && $t->cover_art)
                                                <img src="{{ $t->cover_art_url }}" class="w-10 h-10 rounded-lg object-cover shrink-0">
                                            @else
                                                <div class="w-10 h-10 rounded-lg bg-purple-600/20 flex items-center justify-center shrink-0">
                                                    <svg class="w-4 h-4 text-purple-300" fill="currentColor" viewBox="0 0 20 20"><path d="M18 3a1 1 0 00-1.196-.98l-10 2A1 1 0 006 5v9.114A4.369 4.369 0 005 14c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V7.82l8-1.6v5.894A4.37 4.37 0 0015 12c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V3z"/></svg>
                                                </div>
                                            @endif
                                            <div class="min-w-0">
                                                <p class="text-white text-sm font-medium truncate">{{ $title }}</p>
                                                <p class="text-gray-400 text-xs truncate">{{ $t->artists ?? 'Unknown Artist' }}</p>
                                                <p class="text-gray-500 text-[11px]">Playlist: {{ $sub->playlist_name }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-300 hidden sm:table-cell">
                                        <div class="flex flex-col gap-1">
                                            <span class="text-sm">{{ $sub->playlist_name }}</span>
                                            @if($sub->playlist_url)
                                                <a href="{{ $sub->playlist_url }}" target="_blank" class="text-purple-400 hover:text-purple-300 text-xs inline-flex items-center gap-1">
                                                    <span>Apri playlist</span>
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-300 hidden md:table-cell">
                                        <span class="px-2 py-0.5 rounded-full border border-white/10 bg-white/5 text-xs">{{ $sub->platform }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-300 hidden md:table-cell">
                                        {{ number_format($sub->streams ?? 0) }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-300 hidden md:table-cell">
                                        {{ number_format($sub->listeners ?? 0) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const platformSelect = document.getElementById('playlist-platform');
    const playlistSelect = document.getElementById('playlist-name');
    if (!platformSelect || !playlistSelect) return;

    const allOptions = Array.from(playlistSelect.querySelectorAll('option[data-platform]'));

    function updatePlaylists() {
        const platform = platformSelect.value;
        playlistSelect.value = '';
        playlistSelect.dispatchEvent(new Event('change', { bubbles: true }));
        allOptions.forEach(opt => {
            if (!platform) {
                opt.hidden = true;
            } else {
                opt.hidden = opt.dataset.platform !== platform;
            }
        });
        playlistSelect.disabled = !platform;
    }

    platformSelect.addEventListener('change', updatePlaylists);
    updatePlaylists();
});
</script>
@endpush
