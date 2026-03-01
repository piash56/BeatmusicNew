@extends('layouts.admin')

@section('title', 'Edit Article')
@section('page-title', 'Edit Article')

@section('content')
<div class="max-w-3xl space-y-6">
    <a href="{{ route('admin.knowledge-base') }}" class="flex items-center space-x-2 text-gray-400 hover:text-white transition text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        <span>Back</span>
    </a>

    <form method="POST" action="{{ route('admin.knowledge-base.update', $article->id) }}" class="bg-gray-900 rounded-2xl border border-white/5 p-6 space-y-5">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-sm text-gray-400 mb-1.5">Title <span class="text-red-400">*</span></label>
                <input type="text" name="title" value="{{ old('title', $article->title) }}" required
                    class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1.5">Category</label>
                <input type="text" name="category" value="{{ old('category', $article->category) }}"
                    class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1.5">Tags</label>
                <input type="text" name="tags" value="{{ old('tags', is_array($article->tags) ? implode(', ', $article->tags) : $article->tags) }}"
                    class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm text-gray-400 mb-1.5">Excerpt</label>
                <textarea name="excerpt" rows="2"
                    class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm resize-none">{{ old('excerpt', $article->excerpt) }}</textarea>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm text-gray-400 mb-1.5">Content <span class="text-red-400">*</span></label>
                <textarea name="content" rows="16" required
                    class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm resize-none font-mono">{{ old('content', $article->content) }}</textarea>
            </div>
            <div class="sm:col-span-2">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="status" value="active" {{ old('status', $article->status) === 'active' ? 'checked' : '' }} class="w-4 h-4 rounded border-white/20 bg-gray-800 text-purple-600">
                    <span class="text-sm text-gray-300">Published</span>
                </label>
            </div>
        </div>
        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.knowledge-base') }}" class="px-5 py-2 bg-white/5 hover:bg-white/10 text-gray-300 rounded-xl border border-white/10 transition text-sm">Cancel</a>
            <button type="submit" class="px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-xl transition">Save Changes</button>
        </div>
    </form>
</div>
@endsection
