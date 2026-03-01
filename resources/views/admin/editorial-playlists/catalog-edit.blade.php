@extends('layouts.admin')

@section('title', 'Edit Playlist')
@section('page-title', 'Edit Playlist')

@section('content')
<div class="space-y-6 max-w-2xl">
    <a href="{{ route('admin.editorial-playlists.catalog') }}" class="inline-flex items-center gap-2 text-gray-400 hover:text-white text-sm">← Back to catalog</a>

    @if($errors->any())
    <div class="p-3 rounded-xl bg-red-900/20 border border-red-500/30 text-red-400 text-sm">
        <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.editorial-playlists.catalog.update', $playlist->id) }}" class="bg-gray-900 rounded-xl border border-white/5 p-6 space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-sm text-gray-400 mb-1">Platform</label>
            <select name="platform" required class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-lg">
                @foreach(['Spotify','Apple Music','Amazon Music'] as $p)
                <option value="{{ $p }}" {{ old('platform', $playlist->platform) === $p ? 'selected' : '' }}>{{ $p }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm text-gray-400 mb-1">Name</label>
            <input type="text" name="name" required maxlength="255" value="{{ old('name', $playlist->name) }}" class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-lg">
        </div>
        <div>
            <label class="block text-sm text-gray-400 mb-1">URL</label>
            <input type="url" name="url" required value="{{ old('url', $playlist->url) }}" class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-lg">
        </div>
        <div>
            <label class="block text-sm text-gray-400 mb-1">Sort order</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $playlist->sort_order) }}" min="0" class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-lg">
        </div>
        <label class="flex items-center gap-2 text-gray-400 text-sm">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $playlist->is_active) ? 'checked' : '' }} class="rounded border-white/20 text-purple-600"> Active
        </label>
        <div class="flex justify-end gap-2 pt-2">
            <a href="{{ route('admin.editorial-playlists.catalog') }}" class="px-4 py-2 text-gray-400 hover:text-white text-sm">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-lg transition">Save</button>
        </div>
    </form>
</div>
@endsection
