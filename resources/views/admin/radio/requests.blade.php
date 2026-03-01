@extends('layouts.admin')

@section('title', 'Radio Requests')
@section('page-title', 'Radio Promotion Requests')

@section('content')
<div class="space-y-4"
     x-data="{
        statusModalOpen: false,
        statusTargetId: null,
        statusCurrent: '',
        statusNew: '',
        statusAdminNotes: '',
        statusSubmitting: false,
        search: '{{ addslashes(request('search', '')) }}',
        statusUrlTemplate: @js(route('admin.radio-requests.status', 0)),
        buildStatusUrl(id) {
            if (!id) return this.statusUrlTemplate;
            return this.statusUrlTemplate.replace('/0/', '/' + id + '/');
        },
        openStatusModal(payload) {
            this.statusTargetId = payload.id;
            this.statusCurrent = payload.current || '';
            this.statusNew = payload.current || '';
            this.statusAdminNotes = payload.admin_notes || '';
            this.statusSubmitting = false;
            this.statusModalOpen = true;
        }
     }">
    <div class="flex flex-wrap gap-3">
        <form method="GET" class="flex flex-wrap gap-2">
            <input type="text" name="search" value="{{ request('search') }}" x-model="search" placeholder="User name or email..."
                class="bg-gray-800 border border-white/10 text-white placeholder-gray-500 px-3 py-2 rounded-lg text-sm w-48">
            <select name="status" class="bg-gray-800 border border-white/10 text-gray-300 px-3 py-2 rounded-lg text-sm">
                <option value="">All statuses</option>
                @foreach(['pending','published','rejected','finished'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-lg transition">Filter</button>
        </form>
    </div>
    <div class="bg-gray-900 rounded-xl border border-white/5 overflow-hidden">
        <table class="w-full text-sm min-w-[760px]">
            <thead class="bg-gray-800/50 border-b border-white/5">
                <tr>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium">Track</th>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium hidden sm:table-cell">User</th>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium hidden md:table-cell">Network</th>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium">Status</th>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium hidden lg:table-cell">Submitted</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/3">
                @forelse($promotions as $promo)
                @php
                    $track = $promo->track;
                    $isAlbum = $track && $track->release_type === 'album';
                    $albumTitle = $isAlbum ? ($track->album_title ?: $track->title) : null;
                    $albumTracks = $isAlbum ? ($track->album_tracks ?? []) : [];
                    $albumTrackTitle = $isAlbum && $promo->track_index !== null && isset($albumTracks[$promo->track_index]['title'])
                        ? $albumTracks[$promo->track_index]['title']
                        : ($isAlbum && $promo->track_index !== null ? 'Track '.($promo->track_index + 1) : null);
                    $singleTitle = !$isAlbum ? ($track->title ?? 'Unknown track') : null;
                    $searchText = strtolower(trim(
                        ($singleTitle ?? '') . ' ' .
                        ($albumTitle ?? '') . ' ' .
                        ($albumTrackTitle ?? '') . ' ' .
                        ($promo->user->full_name ?? '') . ' ' .
                        ($promo->user->email ?? '') . ' ' .
                        ($promo->radioNetwork->name ?? '') . ' ' .
                        ($promo->status ?? '')
                    ));
                @endphp
                <tr class="hover:bg-white/2 transition"
                    x-show="!search || $el.dataset.search?.includes(search.toLowerCase())"
                    data-search="{{ $searchText }}">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            @if($track && $track->cover_art)
                                <img src="{{ $track->cover_art_url }}" class="w-10 h-10 rounded-lg object-cover shrink-0">
                            @else
                                <div class="w-10 h-10 bg-purple-600/20 rounded-lg flex items-center justify-center text-sm shrink-0">📻</div>
                            @endif
                            <div class="min-w-0 text-sm">
                                @if($isAlbum)
                                    <p class="text-white font-semibold truncate">Album: {{ $albumTitle }}</p>
                                    <p class="text-gray-200 truncate">Track: {{ $albumTrackTitle }}</p>
                                @else
                                    <p class="text-white font-semibold truncate">Track: {{ $singleTitle }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-300 hidden sm:table-cell">
                        <div class="min-w-0">
                            <p class="text-sm">{{ $promo->user->full_name ?? 'Unknown' }}</p>
                            <p class="text-gray-500 text-xs">{{ $promo->user->email ?? '' }}</p>
                            @if($track)
                                <p class="text-gray-500 text-[11px] mt-1">
                                    {{ $track->artists ?? 'Unknown artist' }} • {{ $track->primary_genre ?? 'Unknown genre' }}
                                </p>
                            @endif
                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-400 hidden md:table-cell">
                        {{ $promo->radioNetwork->name ?? '—' }}
                    </td>
                    <td class="px-4 py-3">
                        <button type="button"
                                class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full text-xs font-medium border border-white/10
                                    {{ $promo->status === 'published' ? 'bg-green-900/40 text-green-300' :
                                       ($promo->status === 'rejected' ? 'bg-red-900/40 text-red-300' :
                                       ($promo->status === 'finished' ? 'bg-gray-800/60 text-gray-300' : 'bg-yellow-900/40 text-yellow-300')) }}"
                                @click="openStatusModal({ id: {{ $promo->id }}, current: @js($promo->status), admin_notes: @js($promo->admin_notes) })">
                            <span>{{ ucfirst($promo->status) }}</span>
                            <svg class="w-3 h-3 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </td>
                    <td class="px-4 py-3 text-gray-400 text-xs hidden lg:table-cell">
                        {{ $promo->created_at->format('M d, Y') }}
                        <span class="block">{{ $promo->created_at->format('h:i A') }}</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-12 text-center text-gray-500">No radio requests found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div>{{ $promotions->withQueryString()->links() }}</div>

    <!-- Status confirm modal -->
    <div x-show="statusModalOpen" x-cloak class="fixed inset-0 z-40 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/70" @click="statusModalOpen = false"></div>
        <div class="relative w-full max-w-md bg-gray-900 border border-white/10 rounded-2xl p-5">
            <h3 class="text-white font-semibold text-base mb-1">Aggiorna stato promozione radio</h3>
            <p class="text-gray-400 text-xs mb-4">Scegli il nuovo stato e conferma per applicare la modifica.</p>

            <form method="POST" :action="buildStatusUrl(statusTargetId)" class="space-y-3"
                  @submit.prevent="statusSubmitting = true; $event.target.submit();">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-xs text-gray-400 mb-1.5">Nuovo stato</label>
                    <select name="status" x-model="statusNew" class="w-full bg-gray-800 border border-white/10 text-gray-200 px-3 py-2 rounded-xl text-sm">
                        @foreach(['pending','published','rejected','finished'] as $s)
                            <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>

                <input type="hidden" name="admin_notes" :value="statusAdminNotes">

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="statusModalOpen = false" class="px-4 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-gray-200 text-sm transition">Annulla</button>
                    <button type="submit"
                            class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 disabled:bg-purple-900/40 disabled:cursor-not-allowed text-white text-sm font-medium transition inline-flex items-center gap-2"
                            :disabled="!statusTargetId || !statusNew || statusNew === statusCurrent || statusSubmitting">
                        <svg x-show="!statusSubmitting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <svg x-show="statusSubmitting" class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        <span x-text="statusSubmitting ? 'Aggiornamento...' : 'Conferma aggiornamento'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
