@extends('layouts.dashboard')

@section('title', 'Promozione radiofonica')
@section('page-title', 'Promozione radiofonica')
@section('page-subtitle', 'Promuovi la tua musica sulle reti radiofoniche')

@section('content')
<div class="space-y-6">

    @if(session('success'))
    <div class="p-3 rounded-xl bg-green-900/30 border border-green-500/30 text-green-300 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="p-3 rounded-xl bg-red-900/30 border border-red-500/30 text-red-300 text-sm">{{ session('error') }}</div>
    @endif

    <!-- Hero + Submit Form -->
    <div class="bg-gray-900 rounded-2xl border border-white/5 p-6" x-data="{
        open: false,
        type: 'single',
        singleId: '',
        albumId: '',
        albumTracks: [],
        albumTrackIndex: '',
        loadingAlbumTracks: false,
        submitting: false,
        async loadAlbumTracks() {
            this.albumTracks = [];
            this.albumTrackIndex = '';
            if (!this.albumId) return;
            this.loadingAlbumTracks = true;
            try {
                const r = await fetch('{{ route('dashboard.radio-promotion.album-tracks', 0) }}'.replace('/0/', '/' + this.albumId + '/'), { headers: { 'Accept': 'application/json' } });
                const data = await r.json();
                this.albumTracks = data.tracks || [];
            } catch (e) {}
            this.loadingAlbumTracks = false;
        },
        get trackId() { return this.type === 'single' ? this.singleId : this.albumId; }
    }">
        <div class="flex flex-col md:flex-row gap-6 items-stretch">
            <div class="md:w-1/3">
                <div class="h-full rounded-2xl overflow-hidden bg-white/5 flex items-center justify-center">
                    <img src="{{ asset('images/radio-italia.jpg') }}" alt="Radio studio" class="w-full h-full object-cover">
                </div>
            </div>
            <div class="flex-1 flex flex-col justify-between gap-4">
                <div class="space-y-2">
                    <h3 class="font-semibold text-white text-lg">Alla conquista della radio italiana</h3>
                    <p class="text-gray-400 text-sm">
                        Amplifica la tua musica sulle onde radio nazionali. Il nostro team di promoter collabora con
                        emittenti nazionali e locali per garantire la massima visibilità ai tuoi brani.
                    </p>
                    <p class="text-gray-400 text-sm">
                        Ogni campagna include rotazioni dedicate, report dettagliati e la possibilità di accedere alle
                        playlist giornaliere più seguite.
                    </p>
                    <div class="flex flex-wrap gap-2 mt-2 text-[11px]">
                        <span class="px-2.5 py-1 rounded-full bg-white/5 border border-white/10 text-gray-200">Reti Nazionali</span>
                        <span class="px-2.5 py-1 rounded-full bg-white/5 border border-white/10 text-gray-200">Radio Locali</span>
                        <span class="px-2.5 py-1 rounded-full bg-white/5 border border-white/10 text-gray-200">Webradio</span>
                    </div>
                </div>
                <div class="mt-2">
                    <button @click="open = !open"
                            class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Richiedi promozione radiofonica</span>
                    </button>
                </div>
            </div>
        </div>

        <div x-show="open" x-cloak x-transition class="mt-6 pt-6 border-t border-white/5">
            <form method="POST" action="{{ route('dashboard.radio-promotion.submit') }}"
                  @submit.prevent="
                    if (!trackId || (type === 'album' && !albumTrackIndex)) { return; }
                    submitting = true;
                    $event.target.submit();
                  ">
                @csrf
                <input type="hidden" name="track_id" :value="trackId">
                <input type="hidden" name="track_index" :disabled="type !== 'album'" :value="albumTrackIndex">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-400 mb-1.5">Seleziona Tipo <span class="text-red-400">*</span></label>
                        <select x-model="type" class="w-full bg-gray-800 border border-white/10 text-gray-300 px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
                            <option value="single">Singolo</option>
                            <option value="album">Album (scegli una traccia)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1.5">Rete radiofonica (facoltativo)</label>
                        <select name="radio_network_id" class="w-full bg-gray-800 border border-white/10 text-gray-300 px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
                            <option value="">Scegli una rete...</option>
                            @foreach($radioNetworks as $network)
                            <option value="{{ $network->id }}">{{ $network->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div x-show="type === 'single'" class="sm:col-span-2" x-cloak>
                        <label class="block text-sm text-gray-400 mb-1.5">Traccia unica <span class="text-red-400">*</span></label>
                        <select x-model="singleId" :required="type === 'single'" class="w-full bg-gray-800 border border-white/10 text-gray-300 px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
                            <option value="">Scegli un singolo pubblicato...</option>
                            @foreach($releasedSingles as $t)
                                <option value="{{ $t->id }}">{{ $t->title }} — {{ $t->artists }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div x-show="type === 'album'" class="sm:col-span-2" x-cloak>
                        <label class="block text-sm text-gray-400 mb-1.5">Album <span class="text-red-400">*</span></label>
                        <select x-model="albumId" @change="loadAlbumTracks()" :required="type === 'album'" class="w-full bg-gray-800 border border-white/10 text-gray-300 px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
                            <option value="">Scegli un album pubblicato...</option>
                            @foreach($releasedAlbums as $a)
                                <option value="{{ $a->id }}">{{ $a->album_title ?: $a->title }} — {{ $a->artists }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div x-show="type === 'album'" class="sm:col-span-2" x-cloak>
                        <label class="block text-sm text-gray-400 mb-1.5">Album track <span class="text-red-400">*</span></label>
                        <select x-model="albumTrackIndex" :disabled="!albumId || loadingAlbumTracks" :required="type === 'album'" class="w-full bg-gray-800 border border-white/10 text-gray-300 px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
                            <option value="" x-text="loadingAlbumTracks ? 'Loading tracks...' : 'Choose a track...'"></option>
                            <template x-for="t in albumTracks" :key="t.track_index">
                                <option :value="t.track_index" x-text="(t.track_index + 1) + '. ' + t.title"></option>
                            </template>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-xs text-gray-500">Una volta pubblicata, la campagna dura 28 giorni.</p>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-4">
                    <button type="button" @click="open = false" class="px-4 py-2 text-gray-400 hover:text-white text-sm transition">Cancellare</button>
                    <button type="submit"
                            :disabled="submitting"
                            class="px-5 py-2 bg-purple-600 hover:bg-purple-700 disabled:bg-purple-900/40 disabled:cursor-not-allowed text-white text-sm rounded-xl transition flex items-center gap-2">
                        <svg x-show="!submitting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h11M9 21V3m12 7l-4-4m4 4l-4 4"/></svg>
                        <svg x-show="submitting" class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        <span x-text="submitting ? 'Submitting...' : 'Submit'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Available Networks -->
    @if($radioNetworks->isNotEmpty())
    <div class="bg-gray-900 rounded-2xl border border-white/5 p-6">
        <h3 class="font-semibold text-white mb-4">Reti disponibili</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($radioNetworks as $network)
            <div class="bg-white/3 rounded-xl p-3 border border-white/5 flex items-center gap-3">
                @if($network->cover_image)
                    <img src="{{ asset('storage/'.$network->cover_image) }}" class="w-10 h-10 rounded-lg object-cover shrink-0" alt="">
                @else
                    <div class="w-10 h-10 rounded-lg bg-purple-600/20 flex items-center justify-center shrink-0">📻</div>
                @endif
                <div class="min-w-0">
                    <p class="text-white text-sm font-medium truncate">{{ $network->name }}</p>
                    <p class="text-gray-500 text-xs">Rete attiva</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Active Promotions -->
    @if($promotions->isNotEmpty())
    <div class="bg-gray-900 rounded-2xl border border-white/5 overflow-hidden">
        <div class="p-4 border-b border-white/5 flex items-center justify-between">
            <h3 class="font-semibold text-white">Le mie promozioni radiofoniche</h3>
        </div>
        <div class="divide-y divide-white/5">
            @foreach($promotions as $promo)
                @php
                    $t = $promo->track;
                    $isAlbum = $t && $t->release_type === 'album';
                    $albumTitle = $isAlbum ? ($t->album_title ?: $t->title) : null;
                    $albumTracks = $isAlbum ? ($t->album_tracks ?? []) : [];
                    $albumTrackTitle = $isAlbum && $promo->track_index !== null && isset($albumTracks[$promo->track_index]['title'])
                        ? $albumTracks[$promo->track_index]['title']
                        : ($isAlbum && $promo->track_index !== null ? 'Track '.($promo->track_index + 1) : null);
                    $singleTitle = !$isAlbum ? ($t->title ?? 'Unknown Track') : null;
                @endphp
            <div class="p-4 flex items-start space-x-4">
                @if($t && $t->cover_art)
                <img src="{{ $t->cover_art_url }}" class="w-12 h-12 rounded-xl object-cover flex-shrink-0">
                @else
                <div class="w-12 h-12 bg-purple-600/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                </div>
                @endif
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <div class="min-w-0">
                            @if($isAlbum)
                                <p class="text-white font-medium truncate">Album: {{ $albumTitle }}</p>
                                <p class="text-gray-200 text-sm truncate">Traccia: {{ $albumTrackTitle }}</p>
                            @else
                                <p class="text-white font-medium truncate">Traccia: {{ $singleTitle }}</p>
                            @endif
                            @if($t)
                                <p class="text-gray-400 text-xs mt-0.5 truncate">{{ $t->artists ?? 'Unknown artist' }}</p>
                            @endif
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium
                            {{ $promo->status === 'published' ? 'bg-green-900/50 text-green-400' :
                               ($promo->status === 'rejected' ? 'bg-red-900/50 text-red-400' :
                               ($promo->status === 'finished' ? 'bg-gray-800/50 text-gray-400' : 'bg-yellow-900/50 text-yellow-400')) }}">
                            {{ ucfirst($promo->status) }}
                        </span>
                    </div>
                    <p class="text-gray-400 text-sm mt-0.5">{{ $promo->radioNetwork->name ?? 'Any network' }}</p>
                    <div class="flex items-center space-x-4 mt-2 text-xs text-gray-500">
                        <span>Submitted {{ $promo->created_at->diffForHumans() }}</span>
                        @if($promo->published_date && $promo->finish_date)
                        <span>{{ $promo->published_date->format('M d') }} – {{ $promo->finish_date->format('M d, Y') }}</span>
                        @endif
                    </div>
                    @if($promo->status === 'published' && $promo->progress_percentage !== null)
                        <div class="mt-3">
                            <div class="flex justify-between text-xs text-gray-500 mb-1">
                                <span>{{ $promo->days_remaining }} giorni rimanenti</span>
                                <span>{{ $promo->progress_percentage }}%</span>
                            </div>
                            <div class="h-2 bg-white/10 rounded-full">
                                <div class="h-full bg-purple-600 rounded-full" style="width: {{ $promo->progress_percentage }}%"></div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        <div class="p-4 border-t border-white/5">
            {{ $promotions->links() }}
        </div>
    </div>
    @else
    <div class="bg-gray-900 rounded-2xl border border-white/5 p-12 text-center">
        <div class="w-16 h-16 bg-purple-600/10 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-purple-400/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
        </div>
        <p class="text-gray-400">Nessuna promozione radiofonica ancora.</p>
        <p class="text-gray-600 text-sm mt-1">Invia la tua traccia qui sopra per iniziare.</p>
    </div>
    @endif
</div>
@endsection
