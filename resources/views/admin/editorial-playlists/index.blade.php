@extends('layouts.admin')

@section('title', 'Editorial Playlists')
@section('page-title', 'Editorial Playlist Submissions')

@section('content')
<div class="space-y-4"
     x-data="{
        statusModalOpen: false,
        streamsModalOpen: false,
        statusTargetId: null,
        statusCurrent: '',
        statusNew: '',
        statusTrackTitle: '',
        statusUserLabel: '',
        reviewNote: '',
        streamsTargetId: null,
        streamsTrackTitle: '',
        streamsUserLabel: '',
        currentStreams: 0,
        currentListeners: 0,
        streamsIncrement: 0,
        listenersIncrement: 0,
        statusUrlTemplate: @js(route('admin.editorial-playlists.status', 0)),
        streamsUrlTemplate: @js(route('admin.editorial-playlists.streams', 0)),
        buildUrl(template, id) {
            if (!id) return template;
            return template.replace(/\/0(\/|$)/, '/' + id + '$1');
        },
        openStatusModal(payload) {
            this.statusTargetId = payload.id;
            this.statusCurrent = payload.current || '';
            this.statusNew = payload.current || '';
            this.statusTrackTitle = payload.trackTitle || '';
            this.statusUserLabel = payload.userLabel || '';
            this.reviewNote = '';
            this.statusModalOpen = true;
        },
        openStreamsModal(payload) {
            this.streamsTargetId = payload.id;
            this.streamsTrackTitle = payload.trackTitle || '';
            this.streamsUserLabel = payload.userLabel || '';
            this.currentStreams = payload.streams ?? 0;
            this.currentListeners = payload.listeners ?? 0;
            this.streamsIncrement = 0;
            this.listenersIncrement = 0;
            this.streamsModalOpen = true;
        }
     }">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('admin.editorial-playlists.catalog') }}" class="text-purple-400 hover:text-purple-300 text-sm">Manage Playlist Catalog →</a>
        <form method="GET" class="flex flex-wrap gap-2">
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   x-model="search"
                   placeholder="Track, artist, playlist, user..."
                   class="bg-gray-800 border border-white/10 text-white placeholder-gray-500 px-3 py-2 rounded-lg text-sm w-48">
            <select name="platform" class="bg-gray-800 border border-white/10 text-gray-300 px-3 py-2 rounded-lg text-sm">
                <option value="">All platforms</option>
                @foreach(['Spotify','Apple Music','Amazon Music'] as $p)
                <option value="{{ $p }}" {{ request('platform') === $p ? 'selected' : '' }}>{{ $p }}</option>
                @endforeach
            </select>
            <select name="status" class="bg-gray-800 border border-white/10 text-gray-300 px-3 py-2 rounded-lg text-sm">
                <option value="">All statuses</option>
                @foreach(['Waiting','Processing','Published','Rejected'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-lg transition">Filter</button>
        </form>
    </div>
    <div class="bg-gray-900 rounded-xl border border-white/5 overflow-hidden">
        <table class="w-full text-sm min-w-[980px]">
            <thead class="bg-gray-800/50 border-b border-white/5">
                <tr>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium">Track</th>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium">User</th>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium">Playlist</th>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium">Status</th>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium">Submitted Date</th>
                    <th class="text-right px-4 py-3 text-gray-400 font-medium">Streams / Listeners</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/3">
                @forelse($submissions as $sub)
                @php
                    $t = $sub->track;
                    $isAlbum = $t && $t->release_type === 'album';
                    $title = $isAlbum ? ($t->album_title ?: $t->title) : ($t->title ?? 'Unknown Track');
                    $artist = $t->artists ?? 'Unknown Artist';
                    $userName = $sub->user->full_name ?? 'Unknown';
                    $userEmail = $sub->user->email ?? '';
                    $userLabel = trim($userName . ($userEmail ? " ({$userEmail})" : ''));
                    $submittedAt = $sub->submission_date ?? $sub->created_at;
                @endphp
                <tr class="hover:bg-white/2 transition">

                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            @if($t && $t->cover_art)
                                <img src="{{ $t->cover_art_url }}" class="w-12 h-12 rounded-xl object-cover shrink-0">
                            @else
                                <div class="w-12 h-12 bg-purple-600/20 rounded-xl flex items-center justify-center shrink-0">🎵</div>
                            @endif
                            <div class="min-w-0">
                                <div class="text-white font-medium truncate">{{ $title }}</div>
                                <div class="text-gray-400 text-xs truncate">{{ $artist }}</div>
                            </div>
                        </div>
                    </td>

                    <td class="px-4 py-3">
                        <div class="min-w-0">
                            <div class="text-gray-200 font-medium truncate">{{ $userName }}</div>
                            <div class="text-gray-500 text-xs truncate">{{ $userEmail }}</div>
                        </div>
                    </td>

                    <td class="px-4 py-3">
                        <div class="flex flex-col gap-1">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="text-gray-200 font-medium truncate">{{ $sub->playlist_name }}</span>
                                @if($sub->playlist_url)
                                    <a href="{{ $sub->playlist_url }}" target="_blank" class="text-purple-400 hover:text-purple-300 text-xs inline-flex items-center gap-1 shrink-0">
                                        <span>Open</span>
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                @endif
                            </div>
                            <div class="text-gray-500 text-xs">{{ $sub->platform }}</div>
                        </div>
                    </td>

                    <td class="px-4 py-3">
                        <button type="button"
                                class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full text-xs font-medium border border-white/10
                                    {{ $sub->status === 'Published' ? 'bg-green-900/40 text-green-300' :
                                       ($sub->status === 'Rejected' ? 'bg-red-900/40 text-red-300' :
                                       ($sub->status === 'Processing' ? 'bg-yellow-900/40 text-yellow-300' : 'bg-blue-900/40 text-blue-300')) }}"
                                @click="openStatusModal({ id: {{ $sub->id }}, current: @js($sub->status), trackTitle: @js($title), userLabel: @js($userLabel) })"
                                title="Click to change status">
                            <span>{{ $sub->status }}</span>
                            <svg class="w-3.5 h-3.5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </td>

                    <td class="px-4 py-3 text-gray-300">
                        <div class="text-sm">{{ optional($submittedAt)->format('M d, Y') }}</div>
                        <div class="text-xs text-gray-500">{{ optional($submittedAt)->format('h:i A') }}</div>
                    </td>

                    <td class="px-4 py-3 text-right align-top">
                        @if($sub->status === 'Published')
                            <div class="space-y-1 text-xs text-gray-300 inline-block text-left">
                                <div class="flex items-center justify-end gap-1">
                                    <span class="text-gray-400">Streams</span>
                                    <span class="text-white font-medium">{{ number_format($sub->streams ?? 0) }}</span>
                                    <button type="button"
                                            class="text-purple-400 hover:text-purple-300"
                                            @click="openStreamsModal({ id: {{ $sub->id }}, trackTitle: @js($title), userLabel: @js($userLabel), streams: {{ (int)($sub->streams ?? 0) }}, listeners: {{ (int)($sub->listeners ?? 0) }} })"
                                            title="Edit streams">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                </div>
                                <div class="flex items-center justify-end gap-1">
                                    <span class="text-gray-400">Listeners</span>
                                    <span class="text-white font-medium">{{ number_format($sub->listeners ?? 0) }}</span>
                                    <button type="button"
                                            class="text-purple-400 hover:text-purple-300"
                                            @click="openStreamsModal({ id: {{ $sub->id }}, trackTitle: @js($title), userLabel: @js($userLabel), streams: {{ (int)($sub->streams ?? 0) }}, listeners: {{ (int)($sub->listeners ?? 0) }} })"
                                            title="Edit listeners">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                </div>
                            </div>
                        @else
                            <span class="text-gray-600 text-xs">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-12 text-center text-gray-500">No playlist submissions found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div>{{ $submissions->withQueryString()->links() }}</div>

    <!-- Status confirm modal -->
    <div x-show="statusModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/70" @click="statusModalOpen = false"></div>
        <div class="relative w-full max-w-lg bg-gray-900 border border-white/10 rounded-2xl p-5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h3 class="text-white font-semibold">Update submission status</h3>
                    <p class="text-gray-400 text-xs mt-1">
                        Track: <span class="text-gray-200" x-text="statusTrackTitle"></span>
                        <span class="text-gray-600">•</span>
                        <span class="text-gray-300" x-text="statusUserLabel"></span>
                    </p>
                </div>
                <button type="button" class="text-gray-400 hover:text-white" @click="statusModalOpen = false" aria-label="Close">✕</button>
            </div>

            <form method="POST" :action="buildUrl(statusUrlTemplate, statusTargetId)" class="mt-4 space-y-3">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-xs text-gray-400 mb-1.5">New status</label>
                    <select name="status" x-model="statusNew" class="w-full bg-gray-800 border border-white/10 text-gray-200 px-3 py-2 rounded-xl text-sm">
                        @foreach(['Waiting','Processing','Published','Rejected'] as $s)
                            <option value="{{ $s }}">{{ $s }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs text-gray-400 mb-1.5">Review note (optional)</label>
                    <textarea name="review_note" x-model="reviewNote" rows="3" class="w-full bg-gray-800 border border-white/10 text-gray-200 px-3 py-2 rounded-xl text-sm" placeholder="Optional note..."></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="statusModalOpen = false" class="px-4 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-gray-200 text-sm transition">Cancel</button>
                    <button type="submit"
                            class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium transition"
                            :disabled="!statusTargetId || !statusNew || statusNew === statusCurrent">
                        Confirm update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Streams/listeners modal -->
    <div x-show="streamsModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/70" @click="streamsModalOpen = false"></div>
        <div class="relative w-full max-w-lg bg-gray-900 border border-white/10 rounded-2xl p-5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h3 class="text-white font-semibold">Update streams / listeners</h3>
                    <p class="text-gray-400 text-xs mt-1">
                        Track: <span class="text-gray-200" x-text="streamsTrackTitle"></span>
                        <span class="text-gray-600">•</span>
                        <span class="text-gray-300" x-text="streamsUserLabel"></span>
                    </p>
                    <p class="text-gray-500 text-[11px] mt-1">
                        Current totals —
                        <span class="text-gray-300">Streams:</span>
                        <span class="text-gray-200" x-text="currentStreams"></span>
                        <span class="text-gray-300 ml-2">Listeners:</span>
                        <span class="text-gray-200" x-text="currentListeners"></span>
                    </p>
                    <p class="text-gray-600 text-[11px] mt-1">Enter how many to <span class="text-gray-300">add</span>; new totals will be old + added.</p>
                </div>
                <button type="button" class="text-gray-400 hover:text-white" @click="streamsModalOpen = false" aria-label="Close">✕</button>
            </div>

            <form method="POST" :action="buildUrl(streamsUrlTemplate, streamsTargetId)" class="mt-4 space-y-3">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-gray-400 mb-1.5">Add streams</label>
                        <input type="number" min="0" name="streams" x-model.number="streamsIncrement"
                               class="w-full bg-gray-800 border border-white/10 text-gray-200 px-3 py-2 rounded-xl text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-400 mb-1.5">Add listeners</label>
                        <input type="number" min="0" name="listeners" x-model.number="listenersIncrement"
                               class="w-full bg-gray-800 border border-white/10 text-gray-200 px-3 py-2 rounded-xl text-sm">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="streamsModalOpen = false" class="px-4 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-gray-200 text-sm transition">Cancel</button>
                    <button type="submit"
                            class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium transition"
                            :disabled="!streamsTargetId">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
