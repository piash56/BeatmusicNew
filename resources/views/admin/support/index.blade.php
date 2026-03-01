@extends('layouts.admin')

@section('title', 'Support Tickets')
@section('page-title', 'Support Tickets')

@section('content')
<div class="space-y-6" x-data="{ searchQuery: '' }">
    {{-- Filters --}}
    <form method="GET" class="flex flex-wrap gap-3">
        <input type="text" x-model="searchQuery" placeholder="Search by subject or user..."
            class="flex-1 min-w-48 bg-gray-900 border border-white/10 text-white px-4 py-2 rounded-xl text-sm focus:outline-none focus:border-purple-500">
        <select name="status" class="bg-gray-900 border border-white/10 text-white px-4 py-2 rounded-xl text-sm focus:outline-none">
            <option value="">All Status</option>
            @foreach(['open','in_progress','resolved','closed'] as $s)
                <option value="{{ $s }}" {{ request('status')==$s?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
            @endforeach
        </select>
        <select name="priority" class="bg-gray-900 border border-white/10 text-white px-4 py-2 rounded-xl text-sm focus:outline-none">
            <option value="">All Priority</option>
            @foreach(['low','medium','high','urgent'] as $p)
                <option value="{{ $p }}" {{ request('priority')==$p?'selected':'' }}>{{ ucfirst($p) }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-xl transition">Filter</button>
        <a href="{{ route('admin.support') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-gray-300 text-sm rounded-xl border border-white/10 transition">Reset</a>
    </form>

    @if(session('success'))
        <div class="bg-green-900/30 border border-green-500/30 text-green-300 rounded-xl p-3 text-sm">{{ session('success') }}</div>
    @endif

    <div class="bg-gray-900 rounded-2xl border border-white/5 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-white/5 text-gray-400">
                <tr>
                    <th class="px-4 py-3 text-left">#</th>
                    <th class="px-4 py-3 text-left">User</th>
                    <th class="px-4 py-3 text-left">Subject</th>
                    <th class="px-4 py-3 text-left">Priority</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Replies</th>
                    <th class="px-4 py-3 text-left">Created</th>
                    <th class="px-4 py-3 text-left">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($tickets as $ticket)
                @php
                    $searchText = strtolower(trim(
                        ($ticket->subject ?? '') . ' ' .
                        ($ticket->user->full_name ?? $ticket->user->name ?? '') . ' ' .
                        ($ticket->user->email ?? '') . ' ' .
                        ($ticket->id ?? '')
                    ));
                @endphp
                <tr class="hover:bg-white/2"
                    x-show="!searchQuery || '{{ $searchText }}'.includes(searchQuery.toLowerCase())">
                    <td class="px-4 py-3 text-gray-400">{{ $ticket->id }}</td>
                    <td class="px-4 py-3">
                        <div class="text-white font-medium">{{ $ticket->user->name ?? 'N/A' }}</div>
                        <div class="text-gray-400 text-xs">{{ $ticket->user->email ?? '' }}</div>
                    </td>
                    <td class="px-4 py-3 text-gray-300 max-w-xs truncate">{{ $ticket->subject }}</td>
                    <td class="px-4 py-3">
                        @php
                            $pc = ['low'=>'bg-blue-600/20 text-blue-300','medium'=>'bg-yellow-600/20 text-yellow-300','high'=>'bg-orange-600/20 text-orange-300','urgent'=>'bg-red-600/20 text-red-300'];
                        @endphp
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $pc[$ticket->priority] ?? 'bg-white/5 text-gray-400' }}">{{ ucfirst($ticket->priority) }}</span>
                    </td>
                    <td class="px-4 py-3">
                        @php
                            $sc = [
                                'open'=>'bg-green-600/20 text-green-300',
                                'in_progress'=>'bg-blue-600/20 text-blue-300',
                                'resolved'=>'bg-purple-600/20 text-purple-300',
                                'closed'=>'bg-white/10 text-gray-400'
                            ];
                        @endphp
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $sc[$ticket->status] ?? 'bg-white/5 text-gray-400' }}">{{ ucfirst(str_replace('_',' ',$ticket->status)) }}</span>
                    </td>
                    <td class="px-4 py-3 text-gray-400">{{ $ticket->replies_count ?? 0 }}</td>
                    <td class="px-4 py-3 text-gray-400">{{ $ticket->created_at->diffForHumans() }}</td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.support.show', $ticket->id) }}" class="text-purple-400 hover:text-purple-300 transition text-xs">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-4 py-12 text-center text-gray-400">No tickets found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div>{{ $tickets->withQueryString()->links() }}</div>
</div>
@endsection
