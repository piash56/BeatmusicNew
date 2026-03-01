@extends('layouts.admin')

@section('title', 'Streams Management')
@section('page-title', 'Streams Management')

@section('content')
<div class="space-y-6"
     x-data="{
        search: '',
        modalOpen: false,
        modalSubmitting: false,
        modalTrackId: null,
        modalTitle: '',
        modalArtist: '',
        modalUser: '',
        modalEmail: '',
        modalCurrentTotal: 0,
        modalLastIncrement: 0,
        modalIncrement: 0,
        modalUrlTemplate: @js(route('admin.streams.update', 0)),
        importModalOpen: false,
        importSubmitting: false,
        buildUrl(id) {
            if (!id) return this.modalUrlTemplate;
            return this.modalUrlTemplate.replace(/0$/, id);
        },
        openModal(payload) {
            this.modalTrackId = payload.id;
            this.modalTitle = payload.title || '';
            this.modalArtist = payload.artist || '';
            this.modalUser = payload.user || '';
            this.modalEmail = payload.email || '';
            this.modalCurrentTotal = payload.total || 0;
            this.modalLastIncrement = payload.new_streams || 0;
            this.modalIncrement = 0;
            this.modalSubmitting = false;
            this.modalOpen = true;
        }
     }">

    <!-- Tracks Table -->
    <div class="bg-gray-900 rounded-xl border border-white/5 overflow-hidden">
        <div class="p-4 border-b border-white/5 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h3 class="font-semibold text-white">Gestione dei flussi</h3>
                <p class="text-gray-400 text-xs">Gestisci i flussi per tutti i brani e gli album pubblicati.</p>
            </div>
            <div class="flex items-center gap-2 w-full md:w-auto">
                <form method="GET" class="flex items-center gap-2 w-full">
                    <div class="relative flex-1 md:w-80">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Cerca tracce, album, artisti, UPC o utente..."
                               class="w-full bg-gray-800 border border-white/10 text-white placeholder-gray-500 px-9 py-2 rounded-xl text-sm focus:outline-none focus:border-purple-500">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z"/></svg>
                        </span>
                    </div>
                    <button type="submit"
                            class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs rounded-xl transition whitespace-nowrap">
                        Filter
                    </button>
                </form>
                <button type="button"
                        @click="importModalOpen = true"
                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs rounded-xl transition whitespace-nowrap">
                    Import Bulk Streams
                </button>
            </div>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-800/50 border-b border-white/5">
                <tr>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium">Nome traccia/album</th>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium hidden lg:table-cell">Tipo</th>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium hidden md:table-cell">Artista</th>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium hidden md:table-cell">Utente</th>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium">Flussi</th>
                    <th class="text-right px-4 py-3 text-gray-400 font-medium">Azioni</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/3">
                @forelse($tracks as $track)
                    @php
                        $isAlbum = $track->release_type === 'album';
                        $title = $isAlbum ? ($track->album_title ?: $track->title) : $track->title;
                    @endphp
                <tr class="hover:bg-white/2 transition">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            @if($track->cover_art)
                                <img src="{{ $track->cover_art_url }}" loading="lazy" class="w-10 h-10 rounded-lg object-cover shrink-0">
                            @else
                                <div class="w-10 h-10 bg-purple-600/20 rounded-lg flex items-center justify-center text-xs shrink-0">🎵</div>
                            @endif
                            <div class="min-w-0">
                                <p class="text-white font-medium truncate">{{ $title }}</p>
                                <p class="text-gray-400 text-xs truncate">
                                    UPC: {{ $track->upc ?? '—' }} • ID: #{{ $track->id }}
                                </p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-300 hidden lg:table-cell">
                        <span class="px-2.5 py-0.5 rounded-full border border-white/10 bg-white/5 text-xs capitalize">
                            {{ $track->release_type ?? 'single' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-300 hidden md:table-cell">
                        <div class="min-w-0">
                            <p class="text-sm truncate">{{ $track->artists ?: 'Unknown artist' }}</p>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-300 hidden md:table-cell">
                        <div class="min-w-0">
                            <p class="text-gray-400 text-xs truncate">{{ $track->user->full_name ?? 'Unknown user' }}</p>
                            <p class="text-gray-500 text-[11px] truncate">{{ $track->user->email ?? '' }}</p>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-200">
                        <div class="flex flex-col gap-0.5">
                            <span class="text-xs text-gray-400">Nuovo: <span class="text-red-400 font-semibold">{{ number_format($track->new_streams ?? 0) }}</span></span>
                            <span class="text-xs text-gray-400">Totale: <span class="text-white font-semibold">{{ number_format($track->total_streams ?? 0) }}</span></span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <button type="button"
                                class="px-4 py-1.5 bg-purple-600 hover:bg-purple-700 text-white text-xs rounded-lg transition"
                                @click="openModal({
                                    id: {{ $track->id }},
                                    title: @js($title),
                                    artist: @js($track->artists ?? ''),
                                    user: @js($track->user->full_name ?? ''),
                                    email: @js($track->user->email ?? ''),
                                    total: {{ (int) ($track->total_streams ?? 0) }},
                                    new_streams: {{ (int) ($track->new_streams ?? 0) }}
                                })">
                            Aggiornamento
                        </button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-12 text-center text-gray-500">No released tracks found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-white/5">{{ $tracks->links() }}</div>
    </div>

    <!-- Import Bulk Streams modal -->
    <div x-show="importModalOpen" x-cloak class="fixed inset-0 z-40 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/70" @click="importModalOpen = false"></div>
        <div class="relative w-full max-w-lg bg-gray-900 border border-white/10 rounded-2xl p-6">
            <div class="flex items-start justify-between gap-3 mb-3">
                <div>
                    <h3 class="text-white font-semibold text-base">Importa flussi in blocco</h3>
                    <p class="text-gray-400 text-xs mt-1">
                        Carica un file <span class="font-semibold text-gray-200">.xlsx</span> con le colonne
                        <span class="text-gray-200">UPC</span> e <span class="text-gray-200">Update Streams</span>.
                    </p>
                    <p class="text-gray-500 text-[11px] mt-1">
                        Se hai un file CSV, convertilo prima in formato <span class="text-gray-200">.xlsx</span> per poterlo caricare.
                    </p>
                </div>
                <button type="button" class="text-gray-400 hover:text-white" @click="importModalOpen = false">✕</button>
            </div>

            <form method="POST" action="{{ route('admin.streams.import') }}" enctype="multipart/form-data"
                  x-ref="importForm"
                  @submit.prevent="
                    importSubmitting = true;
                    setTimeout(() => {
                        importModalOpen = false;
                        $refs.importForm.submit();
                    }, 1000);
                  "
                  class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5">File Streams (.xlsx)</label>
                    <input type="file" name="streams_file" accept=".xlsx"
                           required
                           class="w-full text-sm text-gray-300 file:mr-3 file:px-4 file:py-2.5 file:rounded-lg file:border-0 file:text-sm file:bg-emerald-600 file:text-white hover:file:bg-emerald-700 bg-gray-800 border border-white/10 rounded-xl">
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="importModalOpen = false" class="px-4 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-gray-200 text-sm transition">Annulla</button>
                    <button type="submit"
                            class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 disabled:bg-emerald-900/40 disabled:cursor-not-allowed text-white text-sm font-medium transition inline-flex items-center gap-2"
                            :disabled="importSubmitting">
                        <svg x-show="!importSubmitting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 4v11"/></svg>
                        <svg x-show="importSubmitting" class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        <span x-text="importSubmitting ? 'Importazione...' : 'Importa Streams'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Update streams modal -->
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-40 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/70" @click="modalOpen = false"></div>
        <div class="relative w-full max-w-lg bg-gray-900 border border-white/10 rounded-2xl p-6">
            <div class="flex items-start justify-between gap-3 mb-3">
                <div>
                    <h3 class="text-white font-semibold text-base" x-text="`Aggiorna flussi per ` + modalTitle"></h3>
                    <p class="text-gray-400 text-xs mt-1">Inserisci il nuovo numero di flussi da aggiungere per questo brano o album.</p>
                </div>
                <button type="button" class="text-gray-400 hover:text-white" @click="modalOpen = false">✕</button>
            </div>

            <div class="space-y-3 text-sm">
                <div>
                    <p class="text-gray-400 text-xs">Titolo:</p>
                    <p class="text-gray-200" x-text="modalTitle"></p>
                    <p class="text-gray-400 text-xs mt-1">
                        di: <span class="text-gray-200" x-text="modalArtist || '—'"></span>
                    </p>
                    <p class="text-gray-500 text-xs">
                        Utente: <span class="text-gray-300" x-text="modalUser"></span>
                        <span class="text-gray-500" x-text="modalEmail ? ' • ' + modalEmail : ''"></span>
                    </p>
                </div>

                <form method="POST" :action="buildUrl(modalTrackId)"
                      @submit.prevent="modalSubmitting = true; $event.target.submit();"
                      class="space-y-3 mt-2">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs text-gray-400 mb-1.5">Nuovi flussi da aggiungere</label>
                        <input type="number" name="streams" min="0" x-model.number="modalIncrement"
                               class="w-full bg-gray-800 border border-white/10 text-gray-200 px-3 py-2 rounded-xl text-sm">
                    </div>

                    <p class="text-xs text-gray-400">
                        Flussi totali:
                        <span class="text-white font-medium" x-text="modalCurrentTotal"></span>
                    </p>
                    <p class="text-xs text-gray-400">
                        Ultimi flussi di aggiornamento:
                        <span class="text-white font-medium" x-text="modalLastIncrement"></span>
                    </p>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="modalOpen = false" class="px-4 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-gray-200 text-sm transition">Annulla</button>
                        <button type="submit"
                                class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 disabled:bg-purple-900/40 disabled:cursor-not-allowed text-white text-sm font-medium transition inline-flex items-center gap-2"
                                :disabled="modalSubmitting">
                            <svg x-show="!modalSubmitting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <svg x-show="modalSubmitting" class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            <span x-text="modalSubmitting ? 'Aggiorno flussi...' : 'Aggiorna flussi'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@if(session('streams_import_download'))
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var url = @json(session('streams_import_download'));
        if (!url) return;
        var a = document.createElement('a');
        a.href = url;
        a.download = '';
        document.body.appendChild(a);
        a.click();
        a.remove();
    });
    </script>
    @endpush
@endif
