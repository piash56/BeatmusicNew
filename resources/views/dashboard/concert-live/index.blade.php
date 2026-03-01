@extends('layouts.dashboard')

@section('title', 'Concert Live')
@section('page-title', 'Concert Live')
@section('page-subtitle', 'Request live performance slots')

@section('content')
<div class="space-y-6">

    <!-- Request Form -->
    <div class="bg-gray-900 rounded-2xl border border-white/5 p-6" x-data="{ open: false }">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-semibold text-white">Request a Live Slot</h3>
                <p class="text-gray-400 text-sm mt-0.5">Apply for a concert live performance opportunity</p>
            </div>
            <button @click="open = !open" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-xl transition flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Request Slot</span>
            </button>
        </div>

        <div x-show="open" x-cloak x-transition class="mt-6 pt-6 border-t border-white/5">
            <form method="POST" action="{{ route('dashboard.concert-live.request') }}">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-sm text-gray-400 mb-1.5">Select Concert Slot <span class="text-red-400">*</span></label>
                        <select name="concert_live_id" required class="w-full bg-gray-800 border border-white/10 text-gray-300 px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
                            <option value="">Choose available slot...</option>
                            @foreach($concerts as $slot)
                            <option value="{{ $slot->id }}">
                                {{ $slot->name }} — {{ $slot->concert_date ? $slot->concert_date->format('M d, Y') : '—' }} ({{ $slot->city }}) — {{ $slot->slots_remaining }} slots left
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1.5">Artist name <span class="text-red-400">*</span></label>
                        <input type="text" name="artist_name" required maxlength="100" placeholder="Your artist name"
                            class="w-full bg-gray-800 border border-white/10 text-white placeholder-gray-500 px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-xs text-gray-500">After you submit, an admin will review and confirm/cancel your request.</p>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-4">
                    <button type="button" @click="open = false" class="px-4 py-2 text-gray-400 hover:text-white text-sm transition">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-xl transition">Submit Request</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Upcoming Concerts -->
    @if($concerts->isNotEmpty())
    <div class="bg-gray-900 rounded-2xl border border-white/5 p-6">
        <h3 class="font-semibold text-white mb-4">Available Concert Slots</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($concerts as $slot)
            <div class="bg-gradient-to-br from-purple-900/20 to-indigo-900/20 border border-purple-500/20 rounded-xl p-4">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-white font-medium">{{ $slot->name }}</p>
                        <p class="text-gray-400 text-sm mt-0.5">📍 {{ $slot->city }}</p>
                    </div>
                    <span class="text-xs {{ $slot->slots_remaining > 0 ? 'bg-green-900/50 text-green-400' : 'bg-gray-700/50 text-gray-300' }} px-2 py-1 rounded-full">{{ $slot->slots_remaining > 0 ? 'Open' : 'Full' }}</span>
                </div>
                @if($slot->concert_date)
                <div class="mt-3 flex items-center space-x-1 text-xs text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>{{ $slot->concert_date->format('F d, Y') }}</span>
                </div>
                @endif
                <div class="mt-3 text-xs text-gray-500">Slots remaining: <span class="text-gray-300">{{ $slot->slots_remaining }}</span></div>
                <div class="h-1.5 bg-white/10 rounded-full mt-2">
                    <div class="h-full bg-purple-600 rounded-full" style="width: {{ $slot->booking_percentage }}%"></div>
                </div>
                <div class="text-xs text-gray-500 mt-1">{{ $slot->booking_percentage }}% booked</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- My Requests -->
    <div class="bg-gray-900 rounded-2xl border border-white/5 overflow-hidden">
        <div class="p-4 border-b border-white/5">
            <h3 class="font-semibold text-white">My Live Requests</h3>
        </div>
        @if($myRequests->isEmpty())
        <div class="p-12 text-center">
            <div class="w-16 h-16 bg-purple-600/10 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-purple-400/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.871v6.258a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/></svg>
            </div>
            <p class="text-gray-400 text-sm">No live requests yet.</p>
        </div>
        @else
        <div class="divide-y divide-white/5">
            @foreach($myRequests as $req)
            <div class="p-4 flex items-center space-x-4">
                <div class="flex-1 min-w-0">
                    <p class="text-white font-medium">{{ $req->concertLive->name ?? 'Unknown Event' }}</p>
                    <p class="text-gray-400 text-sm">{{ $req->artist_name }}</p>
                    @if($req->concertLive && $req->concertLive->concert_date)
                    <p class="text-gray-600 text-xs mt-0.5">{{ $req->concertLive->city }} • {{ $req->concertLive->concert_date->format('M d, Y') }}</p>
                    @endif
                    @if($req->admin_notes)
                        <p class="text-amber-200/70 text-xs mt-1">Admin: {{ $req->admin_notes }}</p>
                    @endif
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-medium flex-shrink-0
                    {{ $req->status === 'confirmed' ? 'bg-green-900/50 text-green-400' :
                       ($req->status === 'cancelled' ? 'bg-gray-700/50 text-gray-300' :
                       ($req->status === 'finished' ? 'bg-gray-800/50 text-gray-400' : 'bg-yellow-900/50 text-yellow-400')) }}">
                    {{ ucfirst($req->status) }}
                </span>
            </div>
            @endforeach
        </div>
        <div class="p-4 border-t border-white/5">{{ $myRequests->links() }}</div>
        @endif
    </div>
</div>
@endsection
