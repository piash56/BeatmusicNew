@extends('layouts.admin')

@section('title', 'Album Submissions')
@section('page-title', 'Album Submissions')

@section('content')
@push('scripts')
<script>
document.addEventListener('alpine:init', function() {
    Alpine.data('upcModal', function() {
        return {
            upcModalOpen: false,
            upcTrackId: null,
            upcValue: '',
            upcSaving: false,
            upcMessage: '',
            openUpcModal: function(ev) {
                var btn = ev.currentTarget;
                this.upcTrackId = btn.getAttribute('data-track-id');
                this.upcValue = btn.getAttribute('data-upc') || '';
                this.upcMessage = '';
                this.upcModalOpen = true;
            },
            showActualUpc: function() {
                var sel = '[data-upc-for="' + this.upcTrackId + '"]';
                var el = document.querySelector(sel);
                this.upcValue = el ? (el.getAttribute('data-upc') || '') : this.upcValue;
            },
            saveUpc: function() {
                var self = this;
                if (self.upcSaving) return;
                self.upcSaving = true;
                self.upcMessage = '';
                var fd = new FormData();
                fd.append('upc', self.upcValue);
                fd.append('_token', document.querySelector('meta[name=csrf-token]').content);
                fd.append('_method', 'PUT');
                var base = document.querySelector('[data-upc-base]').getAttribute('data-upc-base');
                fetch(base + '/' + self.upcTrackId + '/upc', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                    body: fd
                }).then(function(r) { return r.json(); }).then(function(data) {
                    if (data.upc !== undefined) {
                        var cell = document.querySelector('[data-upc-for="' + self.upcTrackId + '"]');
                        if (cell) cell.setAttribute('data-upc', data.upc);
                        self.upcValue = data.upc;
                        self.upcMessage = data.message || 'UPC updated.';
                        self.upcModalOpen = false;
                    }
                    self.upcSaving = false;
                }).catch(function() {
                    self.upcMessage = 'Failed to save.';
                    self.upcSaving = false;
                });
            },
            closeUpcModal: function() { this.upcModalOpen = false; },
            statusConfirmOpen: false,
            statusConfirmNew: null,
            statusConfirmCurrent: null,
            pendingStatusForm: null,
            pendingStatusSelect: null,
            openStatusConfirm: function(form, selectEl, newStatus, currentStatus) {
                if (newStatus === currentStatus) return;
                this.pendingStatusForm = form;
                this.pendingStatusSelect = selectEl;
                this.statusConfirmNew = newStatus;
                this.statusConfirmCurrent = currentStatus;
                this.statusConfirmOpen = true;
            },
            confirmStatusChange: function() {
                if (this.pendingStatusForm) this.pendingStatusForm.submit();
                this.statusConfirmOpen = false;
                this.pendingStatusForm = null;
                this.pendingStatusSelect = null;
            },
            cancelStatusChange: function() {
                if (this.pendingStatusSelect && this.statusConfirmCurrent !== null)
                    this.pendingStatusSelect.value = this.statusConfirmCurrent;
                this.statusConfirmOpen = false;
                this.pendingStatusForm = null;
                this.pendingStatusSelect = null;
            }
        };
    });
});
</script>
@endpush
<div class="space-y-4" x-data="upcModal()" data-upc-base="{{ url('/admin/track-submissions') }}">
    <form method="GET" class="flex flex-col sm:flex-row gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title or artist..."
            class="bg-gray-800 border border-white/10 text-white placeholder-gray-500 px-3 py-2 rounded-lg text-sm focus:outline-none focus:border-purple-500 flex-1 sm:max-w-xs">
        <select name="status" class="bg-gray-800 border border-white/10 text-gray-300 px-3 py-2 rounded-lg text-sm">
            <option value="">All Status</option>
            @foreach(['Draft','On Request','On Process','Released','Rejected','Modify Pending','Modify Process','Modify Released','Modify Rejected'] as $s)
            <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-lg transition">Filter</button>
    </form>

    <div class="bg-gray-900 rounded-xl border border-white/5 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-800/50 border-b border-white/5">
                <tr>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium">Album</th>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium hidden sm:table-cell">Artist</th>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium hidden md:table-cell">Tracks</th>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium">Status</th>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium hidden lg:table-cell">Submitted</th>
                    <th class="text-right px-4 py-3 text-gray-400 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/3">
                @forelse($tracks as $track)
                <tr class="hover:bg-white/2 transition">
                    <td class="px-4 py-3">
                        <div class="flex items-center space-x-3">
                            @if($track->cover_art)
                                <img src="{{ $track->cover_art_url }}" loading="lazy" class="w-9 h-9 rounded-lg object-cover flex-shrink-0">
                            @else
                                <div class="w-9 h-9 bg-indigo-600/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2z"/></svg>
                                </div>
                            @endif
                            <p class="text-white font-medium truncate max-w-[150px]">{{ $track->album_title ?? $track->title }}</p>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-300 hidden sm:table-cell">{{ $track->user->full_name ?? 'Unknown' }}</td>
                    <td class="px-4 py-3 text-gray-400 hidden md:table-cell">{{ is_array($track->album_tracks) ? count($track->album_tracks) : 0 }} tracks</td>
                    <td class="px-4 py-3">
                        <form method="POST" action="{{ route('admin.track-submissions.status', $track->id) }}">
                            @csrf @method('PUT')
                            <select name="status" @change="openStatusConfirm($event.target.form, $event.target, $event.target.value, '{{ addslashes($track->status) }}')" class="bg-gray-800 border border-white/10 text-xs px-2 py-1 rounded-lg
                                {{ $track->status === 'Released' ? 'text-green-400' :
                                   ($track->status === 'On Request' ? 'text-blue-400' :
                                   ($track->status === 'On Process' ? 'text-yellow-400' :
                                   ($track->status === 'Rejected' ? 'text-red-400' : 'text-gray-400'))) }}">
                                @foreach(['Draft','On Request','On Process','Released','Rejected','Modify Pending','Modify Process','Modify Released','Modify Rejected'] as $s)
                                <option value="{{ $s }}" {{ $track->status === $s ? 'selected' : '' }}>{{ $s }}</option>
                                @endforeach
                            </select>
                        </form>
                    </td>
                    <td class="px-4 py-3 text-gray-400 text-xs hidden lg:table-cell">{{ $track->created_at->format('M d, Y') }}</td>
                    <td class="px-4 py-3 text-right">
                        <button type="button" @click="openUpcModal($event)" class="p-1.5 text-gray-400 hover:text-amber-400 hover:bg-white/10 rounded-lg transition inline-flex" title="Edit UPC" data-track-id="{{ $track->id }}" data-upc-for="{{ $track->id }}" data-upc="{{ e($track->upc ?? '') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        <a href="{{ route('admin.album-submissions.view', $track->id) }}" class="p-1.5 text-gray-400 hover:text-white hover:bg-white/10 rounded-lg transition inline-flex" title="View">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-12 text-center text-gray-500">No album submissions found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div>{{ $tracks->withQueryString()->links() }}</div>

    <!-- UPC Modal -->
    <template x-teleport="body">
        <div x-show="upcModalOpen" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/70" @keydown.escape.window="closeUpcModal()" @click.self="closeUpcModal()">
            <div x-show="upcModalOpen" x-transition class="bg-gray-900 rounded-2xl border border-white/10 shadow-xl max-w-md w-full p-6" @click.stop>
                <h3 class="text-lg font-semibold text-white mb-4">UPC Code</h3>
                <p class="text-gray-400 text-sm mb-3">Edit and update the UPC code for this album.</p>
                <input type="text" x-model="upcValue" placeholder="UPC code" class="w-full bg-gray-800 border border-white/10 text-white px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500 mb-3">
                <div class="flex flex-wrap gap-2">
                    <button type="button" @click="saveUpc()" :disabled="upcSaving" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-lg transition disabled:opacity-70">
                        <span x-show="!upcSaving">Save</span>
                        <span x-show="upcSaving" x-cloak>Saving...</span>
                    </button>
                    <button type="button" @click="closeUpcModal()" class="px-4 py-2 text-gray-400 hover:text-white text-sm">Cancel</button>
                </div>
                <p x-show="upcMessage" x-text="upcMessage" class="mt-3 text-sm text-green-400"></p>
            </div>
        </div>
    </template>

    <!-- Status change confirm modal -->
    <template x-teleport="body">
        <div x-show="statusConfirmOpen" x-cloak x-transition class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/70" @keydown.escape.window="cancelStatusChange()" @click.self="cancelStatusChange()">
            <div x-show="statusConfirmOpen" x-transition class="bg-gray-900 rounded-2xl border border-white/10 shadow-xl max-w-md w-full p-6" @click.stop>
                <h3 class="text-lg font-semibold text-white mb-2">Confirm status change</h3>
                <p class="text-gray-400 text-sm mb-4">Are you sure you want to change the status to <strong class="text-white" x-text="statusConfirmNew"></strong>?</p>
                <div class="flex flex-wrap gap-2">
                    <button type="button" @click="confirmStatusChange()" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-lg transition">Confirm</button>
                    <button type="button" @click="cancelStatusChange()" class="px-4 py-2 bg-white/10 hover:bg-white/15 text-gray-300 text-sm rounded-lg transition">Cancel</button>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection
