@extends('layouts.dashboard')

@section('title', $ticket->subject)
@section('page-title', 'Support Ticket')
@section('page-subtitle', '#' . $ticket->id . ' · ' . $ticket->subject)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('dashboard.support') }}" class="flex items-center space-x-2 text-gray-400 hover:text-white transition text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            <span>Back to Support</span>
        </a>
        <div class="flex items-center space-x-2">
            <span class="px-2.5 py-1 rounded-full text-xs font-medium
                {{ $ticket->status === 'open' ? 'bg-green-900/50 text-green-400' :
                   ($ticket->status === 'in_progress' ? 'bg-blue-900/50 text-blue-400' :
                   ($ticket->status === 'closed' ? 'bg-gray-700/50 text-gray-400' : 'bg-yellow-900/50 text-yellow-400')) }}">
                {{ ucfirst(str_replace('_', ' ', $ticket->status ?? 'open')) }}
            </span>
            <span class="px-2.5 py-1 rounded-full text-xs font-medium
                {{ $ticket->priority === 'high' ? 'bg-red-900/50 text-red-400' :
                   ($ticket->priority === 'medium' ? 'bg-yellow-900/50 text-yellow-400' : 'bg-gray-700/50 text-gray-400') }}">
                {{ ucfirst($ticket->priority ?? 'low') }} Priority
            </span>
        </div>
    </div>

    <!-- Ticket Details -->
    <div class="bg-gray-900 rounded-2xl border border-white/5 p-5">
        <div class="flex items-start space-x-3">
            <div class="w-9 h-9 bg-gradient-to-br from-purple-600 to-indigo-600 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                {{ strtoupper(substr(auth()->user()->full_name, 0, 2)) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                    <p class="text-white font-medium text-sm">{{ auth()->user()->full_name }}</p>
                    <span class="text-gray-500 text-xs">{{ $ticket->created_at->format('M d, Y · H:i') }}</span>
                </div>
                <p class="text-gray-400 text-sm mt-2 leading-relaxed">{{ $ticket->message }}</p>
                @if($ticket->attachments)
                <div class="mt-3 flex flex-wrap gap-3">
                    @foreach($ticket->attachments as $file)
                        @php
                            $isImage = isset($file['type']) && str_starts_with($file['type'], 'image/');
                            $url = asset('storage/'.$file['path']);
                        @endphp
                        <a href="{{ $url }}" target="_blank" download
                           class="group inline-flex items-center space-x-2 text-purple-400 hover:text-purple-300 text-xs">
                            @if($isImage)
                                <img src="{{ $url }}" alt="{{ $file['name'] ?? 'Attachment' }}" class="w-12 h-12 rounded-lg object-cover border border-white/10">
                            @else
                                <div class="w-10 h-10 rounded-lg bg-purple-600/20 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                </div>
                            @endif
                            <span class="underline">{{ $file['name'] ?? 'Attachment' }}</span>
                        </a>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Replies -->
    @if($ticket->replies && $ticket->replies->isNotEmpty())
    <div class="space-y-4">
        @foreach($ticket->replies as $reply)
        <div class="flex items-start space-x-3 {{ $reply->is_admin_reply ? 'flex-row-reverse space-x-reverse' : '' }}">
            <div class="w-9 h-9 {{ $reply->is_admin_reply ? 'bg-red-600/50' : 'bg-gradient-to-br from-purple-600 to-indigo-600' }} rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                {{ $reply->is_admin_reply ? 'A' : strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div class="flex-1 min-w-0 {{ $reply->is_admin_reply ? 'bg-blue-900/20 border-blue-500/20' : 'bg-gray-900 border-white/5' }} border rounded-2xl p-4">
                <div class="flex items-center justify-between">
                    <p class="text-white font-medium text-sm">{{ $reply->is_admin_reply ? 'Support Team' : auth()->user()->name }}</p>
                    <span class="text-gray-500 text-xs">{{ $reply->created_at->format('M d, Y · H:i') }}</span>
                </div>
                <p class="text-gray-300 text-sm mt-2 leading-relaxed">{{ $reply->message }}</p>
                @if($reply->attachments)
                <div class="mt-3 flex flex-wrap gap-3">
                    @foreach($reply->attachments as $file)
                        @php
                            $isImage = isset($file['type']) && str_starts_with($file['type'], 'image/');
                            $url = asset('storage/'.$file['path']);
                        @endphp
                        <a href="{{ $url }}" target="_blank" download
                           class="group inline-flex items-center space-x-2 text-purple-400 hover:text-purple-300 text-xs">
                            @if($isImage)
                                <img src="{{ $url }}" alt="{{ $file['name'] ?? 'Attachment' }}" class="w-12 h-12 rounded-lg object-cover border border-white/10">
                            @else
                                <div class="w-10 h-10 rounded-lg bg-purple-600/20 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                </div>
                            @endif
                            <span class="underline">{{ $file['name'] ?? 'Attachment' }}</span>
                        </a>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Reply Form -->
    @if(!in_array($ticket->status, ['closed', 'resolved'], true))
    <form method="POST" action="{{ route('dashboard.support.reply', $ticket->id) }}" enctype="multipart/form-data"
        class="bg-gray-900 rounded-2xl border border-white/5 p-5 space-y-4"
        x-data="{ loading: false }"
        @submit="loading = true">
        @csrf
        <h4 class="text-white font-medium text-sm">Add Reply</h4>
        <textarea name="message" rows="4" required placeholder="Type your reply..."
            class="w-full bg-gray-800 border border-white/10 text-white placeholder-gray-500 px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm resize-none"></textarea>
        <div>
            <input type="file" name="attachment" accept="image/*,.pdf,.doc,.docx"
                class="w-full text-sm text-gray-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-purple-600/20 file:text-purple-400 hover:file:bg-purple-600/30 cursor-pointer">
        </div>
        <div class="flex justify-end">
            <button type="submit"
                :disabled="loading"
                class="px-5 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold rounded-xl transition text-sm inline-flex items-center gap-2 disabled:opacity-60">
                <span x-show="loading" class="inline-block w-4 h-4 border-2 border-white/40 border-t-white rounded-full animate-spin"></span>
                <span x-text="loading ? 'Sending...' : 'Send Reply'"></span>
            </button>
        </div>
    </form>
    @else
    <div class="bg-gray-800/50 rounded-2xl border border-white/5 p-4 text-center">
        <p class="text-gray-500 text-sm">This ticket has been closed.</p>
    </div>
    @endif
</div>
@endsection
