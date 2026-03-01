@extends('layouts.admin')

@section('title', 'Live Requests')
@section('page-title', 'Concert Live Requests')

@section('content')
<div class="space-y-4"
     x-data="{
        search: '{{ addslashes(request('search', '')) }}',
        statusModalOpen: false,
        statusTargetId: null,
        statusCurrent: '',
        statusNew: '',
        statusAdminNotes: '',
        statusSubmitting: false,
        statusUrlTemplate: @js(route('admin.live-requests.update', 0)),
        buildStatusUrl(id) {
            if (!id) return this.statusUrlTemplate;
            // route looks like /admin/live-requests/0, replace trailing 0 with actual id
            return this.statusUrlTemplate.replace(/0$/, id);
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
    <form method="GET" class="flex flex-wrap gap-2">
        <input type="text" name="search" value="{{ request('search') }}" x-model="search" placeholder="Artist, concert, user..."
            class="bg-gray-800 border border-white/10 text-white placeholder-gray-500 px-3 py-2 rounded-lg text-sm w-56">
        <select name="status" class="bg-gray-800 border border-white/10 text-gray-300 px-3 py-2 rounded-lg text-sm">
            <option value="">All statuses</option>
            @foreach(['pending','confirmed','cancelled','finished'] as $s)
            <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-lg transition">Filter</button>
    </form>
    <div class="bg-gray-900 rounded-xl border border-white/5 overflow-hidden">
        <table class="w-full text-sm min-w-[760px]">
            <thead class="bg-gray-800/50 border-b border-white/5">
                <tr>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium">Artist</th>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium hidden sm:table-cell">Concert</th>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium hidden md:table-cell">User</th>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium">Status</th>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium hidden lg:table-cell">Submitted</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/3">
                @forelse($requests as $req)
                @php
                    $concert = $req->concertLive;
                    $searchText = strtolower(trim(
                        ($req->artist_name ?? '') . ' ' .
                        ($req->user->full_name ?? '') . ' ' .
                        ($req->user->email ?? '') . ' ' .
                        ($concert->name ?? '') . ' ' .
                        ($concert->city ?? '') . ' ' .
                        ($req->status ?? '')
                    ));
                @endphp
                <tr class="hover:bg-white/2 transition"
                    x-show="!search || $el.dataset.search?.includes(search.toLowerCase())"
                    data-search="{{ $searchText }}">
                    <td class="px-4 py-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gradient-to-br from-purple-600 to-indigo-600 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                {{ strtoupper(substr($req->user->full_name ?? 'U', 0, 2)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-white font-medium truncate">{{ $req->artist_name }}</p>
                                <p class="text-gray-500 text-xs truncate">{{ $req->user->email ?? '' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-300 hidden sm:table-cell">
                        <div class="text-white/90">{{ $concert->name ?? 'Unknown concert' }}</div>
                        <div class="text-xs text-gray-500">
                            {{ $concert?->city }} • {{ $concert?->concert_date?->format('M d, Y') }}
                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-400 hidden md:table-cell">{{ $req->user->full_name ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <button type="button"
                                class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full text-xs font-medium border border-white/10
                                    {{ $req->status === 'confirmed' ? 'bg-green-900/40 text-green-300' :
                                       ($req->status === 'cancelled' ? 'bg-red-900/40 text-red-300' :
                                       ($req->status === 'finished' ? 'bg-gray-800/60 text-gray-300' : 'bg-yellow-900/40 text-yellow-300')) }}"
                                @click="openStatusModal({ id: {{ $req->id }}, current: @js($req->status), admin_notes: @js($req->admin_notes) })">
                            <span>{{ ucfirst($req->status) }}</span>
                            <svg class="w-3 h-3 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </td>
                    <td class="px-4 py-3 text-gray-400 text-xs hidden lg:table-cell">
                        {{ $req->created_at->format('M d, Y') }}
                        <span class="block">{{ $req->created_at->format('h:i A') }}</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-12 text-center text-gray-500">No live requests found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div>{{ $requests->withQueryString()->links() }}</div>

    <!-- Status confirm modal -->
    <div x-show="statusModalOpen" x-cloak class="fixed inset-0 z-40 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/70" @click="statusModalOpen = false"></div>
        <div class="relative w-full max-w-md bg-gray-900 border border-white/10 rounded-2xl p-5">
            <h3 class="text-white font-semibold text-base mb-1">Aggiorna stato richiesta live</h3>
            <p class="text-gray-400 text-xs mb-4">Scegli il nuovo stato e aggiungi eventuali note amministrative.</p>

            <form method="POST" :action="buildStatusUrl(statusTargetId)" class="space-y-3"
                  @submit.prevent="statusSubmitting = true; $event.target.submit();">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-xs text-gray-400 mb-1.5">Nuovo stato</label>
                    <select name="status" x-model="statusNew" class="w-full bg-gray-800 border border-white/10 text-gray-200 px-3 py-2 rounded-xl text-sm">
                        @foreach(['pending','confirmed','cancelled','finished'] as $s)
                            <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs text-gray-400 mb-1.5">Note amministratore (opzionale)</label>
                    <textarea name="admin_notes" rows="3" x-model="statusAdminNotes"
                              class="w-full bg-gray-800 border border-white/10 text-gray-200 px-3 py-2 rounded-xl text-sm"></textarea>
                </div>

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
