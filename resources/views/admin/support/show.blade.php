@extends('layouts.admin')

@section('title', 'Ticket #' . $ticket->id)
@section('page-title', 'Support Ticket')

@section('content')
<div class="max-w-3xl space-y-6" x-data="{ deleteOpen: false, deleteLoading: false }">
    <a href="{{ route('admin.support') }}" class="flex items-center space-x-2 text-gray-400 hover:text-white transition text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        <span>Back to Tickets</span>
    </a>

    @if(session('success'))
        <div class="bg-green-900/30 border border-green-500/30 text-green-300 rounded-xl p-3 text-sm">{{ session('success') }}</div>
    @endif

    {{-- Ticket Header --}}
    <div class="bg-gray-900 rounded-2xl border border-white/5 p-6">
        <div class="flex flex-wrap items-start justify-between gap-3 mb-4">
            <div>
                <h2 class="text-white font-semibold text-lg">{{ $ticket->subject }}</h2>
                <p class="text-gray-400 text-sm mt-0.5">
                    By <span class="text-white">{{ $ticket->user->name ?? 'Unknown' }}</span>
                    ({{ $ticket->user->email ?? '' }}) · {{ $ticket->created_at->format('M d, Y H:i') }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <form method="POST" action="{{ route('admin.support.status', $ticket->id) }}" class="flex items-center space-x-2">
                    @csrf @method('PATCH')
                    <select name="status" class="bg-gray-800 border border-white/10 text-white px-3 py-1.5 rounded-xl text-sm focus:outline-none">
                        @foreach(['open','in_progress','resolved','closed'] as $s)
                            <option value="{{ $s }}" {{ $ticket->status==$s?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-xl transition">Update</button>
                </form>
                <button type="button"
                        @click="deleteOpen = true; deleteLoading = false"
                        class="px-3 py-1.5 bg-red-600/20 hover:bg-red-600/30 text-red-300 text-sm rounded-xl border border-red-500/20 transition inline-flex items-center gap-2">
                    Delete
                </button>
            </div>
        </div>
        <div class="bg-gray-800/50 rounded-xl p-4">
            <p class="text-gray-300 text-sm leading-relaxed whitespace-pre-line">{{ $ticket->message }}</p>
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

    {{-- Replies --}}
    <div class="space-y-4">
        @foreach($ticket->replies as $reply)
        <div class="flex {{ $reply->is_admin_reply ? 'justify-end' : 'justify-start' }}">
            <div class="max-w-xl w-full {{ $reply->is_admin_reply ? 'bg-purple-900/30 border-purple-500/20' : 'bg-gray-900 border-white/5' }} rounded-2xl border p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium {{ $reply->is_admin_reply ? 'text-purple-300' : 'text-white' }}">
                        {{ $reply->is_admin_reply ? '🛡 Admin' : ($reply->user->name ?? 'User') }}
                    </span>
                    <span class="text-xs text-gray-500">{{ $reply->created_at->diffForHumans() }}</span>
                </div>
                <p class="text-gray-300 text-sm leading-relaxed whitespace-pre-line">{{ $reply->message }}</p>
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

    {{-- Reply Form --}}
    @if($ticket->status !== 'closed')
    <div class="bg-gray-900 rounded-2xl border border-white/5 p-6">
        <h3 class="text-white font-medium mb-4">Reply</h3>
        <form method="POST" action="{{ route('admin.support.reply', $ticket->id) }}" enctype="multipart/form-data"
            class="space-y-4" x-data="{ loading: false }" @submit="loading = true">
            @csrf
            <textarea name="message" rows="5" required placeholder="Write your reply..."
                class="w-full bg-gray-800 border border-white/10 text-white px-4 py-3 rounded-xl text-sm focus:outline-none focus:border-purple-500 resize-none"></textarea>
            <div>
                <input type="file" name="attachment" accept="image/*,.pdf,.doc,.docx,.txt"
                    class="w-full text-sm text-gray-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-purple-600/20 file:text-purple-400 hover:file:bg-purple-600/30 cursor-pointer">
            </div>
            <div class="flex justify-end">
                <button type="submit"
                    :disabled="loading"
                    class="px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-xl transition inline-flex items-center gap-2 disabled:opacity-60">
                    <span x-show="loading" class="inline-block w-4 h-4 border-2 border-white/40 border-t-white rounded-full animate-spin"></span>
                    <span x-text="loading ? 'Sending...' : 'Send Reply'"></span>
                </button>
            </div>
        </form>
    </div>
    @else
    <div class="bg-gray-900 rounded-2xl border border-white/5 p-6 text-center">
        <p class="text-gray-400 text-sm">This ticket is closed. Reopen it to reply.</p>
        <form method="POST" action="{{ route('admin.support.status', $ticket->id) }}" class="mt-3 inline-block">
            @csrf @method('PATCH')
            <input type="hidden" name="status" value="open">
            <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-xl transition">Reopen Ticket</button>
        </form>
    </div>
    @endif

    {{-- Delete confirmation modal --}}
    <div x-show="deleteOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/70" @click="if (!deleteLoading) deleteOpen = false"></div>
        <div class="relative bg-gray-900 border border-white/10 rounded-2xl shadow-xl max-w-sm w-full p-6" @click.stop>
            <h3 class="text-lg font-semibold text-white mb-2">Delete ticket?</h3>
            <p class="text-gray-400 text-sm mb-4">
                This ticket and all its replies will be permanently deleted. This action cannot be undone.
            </p>
            <form method="POST" action="{{ route('admin.support.destroy', $ticket->id) }}"
                  @submit="deleteLoading = true" class="flex justify-end gap-2">
                @csrf @method('DELETE')
                <button type="button"
                        @click="if (!deleteLoading) deleteOpen = false"
                        class="px-4 py-2 bg-white/5 hover:bg-white/10 text-gray-300 rounded-lg border border-white/10 text-sm transition">
                    Cancel
                </button>
                <button type="submit"
                        :disabled="deleteLoading"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm inline-flex items-center gap-2 disabled:opacity-60">
                    <span x-show="deleteLoading" class="inline-block w-4 h-4 border-2 border-red-200/40 border-t-red-200 rounded-full animate-spin"></span>
                    <span x-text="deleteLoading ? 'Deleting...' : 'Delete ticket'"></span>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
