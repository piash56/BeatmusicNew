@extends('layouts.admin')

@section('title', 'Knowledge Base')
@section('page-title', 'Knowledge Base')

@section('content')
<div class="space-y-4">
    <div class="flex justify-end">
        <a href="{{ route('admin.knowledge-base.create') }}" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-xl transition flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>New Article</span>
        </a>
    </div>

    <div class="bg-gray-900 rounded-xl border border-white/5 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-800/50 border-b border-white/5">
                <tr>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium">Title</th>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium hidden sm:table-cell">Category</th>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium hidden md:table-cell">Published</th>
                    <th class="text-right px-4 py-3 text-gray-400 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/3">
                @forelse($articles as $article)
                <tr class="hover:bg-white/2 transition">
                    <td class="px-4 py-3">
                        <p class="text-white font-medium">{{ $article->title }}</p>
                        <p class="text-gray-500 text-xs line-clamp-1">{{ $article->excerpt ?? Str::limit(strip_tags($article->content ?? ''), 80) }}</p>
                    </td>
                    <td class="px-4 py-3 text-gray-400 hidden sm:table-cell">{{ $article->category ?? '—' }}</td>
                    <td class="px-4 py-3 hidden md:table-cell">
                        <span class="px-2 py-1 rounded-full text-xs {{ $article->status === 'active' ? 'bg-green-900/50 text-green-400' : 'bg-gray-700/50 text-gray-400' }}">
                            {{ $article->status === 'active' ? 'Published' : 'Draft' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end space-x-1">
                            <a href="{{ route('admin.knowledge-base.edit', $article->id) }}" class="p-1.5 text-gray-400 hover:text-white hover:bg-white/10 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form method="POST" action="{{ route('admin.knowledge-base.destroy', $article->id) }}" onsubmit="return confirm('Delete this article?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-gray-400 hover:text-red-400 hover:bg-white/10 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-4 py-12 text-center text-gray-500">No articles yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div>{{ $articles->links() }}</div>
</div>
@endsection
