@extends('layouts.admin')

@section('title', 'Playlist Catalog')
@section('page-title', 'Editorial Playlist Catalog')

@section('content')
<div class="space-y-6" x-data="{}">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('admin.editorial-playlists') }}" class="text-gray-400 hover:text-white text-sm">← Submissions</a>
        <button type="button" @click="$refs.addModal.showModal()" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-lg transition">Add playlist</button>
    </div>

    <form method="GET" class="flex gap-2">
        <select name="platform" class="bg-gray-800 border border-white/10 text-gray-300 px-3 py-2 rounded-lg text-sm">
            <option value="">All platforms</option>
            @foreach(['Spotify','Apple Music','Amazon Music'] as $p)
            <option value="{{ $p }}" {{ request('platform') === $p ? 'selected' : '' }}>{{ $p }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 bg-white/10 hover:bg-white/15 text-white text-sm rounded-lg transition">Filter</button>
    </form>

    <div class="bg-gray-900 rounded-xl border border-white/5 overflow-hidden">
        <table class="w-full text-sm min-w-[640px]">
            <thead class="bg-gray-800/50 border-b border-white/5">
                <tr>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium">Platform</th>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium">Name</th>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium hidden md:table-cell">URL</th>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium w-20">Order</th>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium w-20">Active</th>
                    <th class="text-right px-4 py-3 text-gray-400 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/3">
                @forelse($playlists as $p)
                <tr class="hover:bg-white/2 transition">
                    <td class="px-4 py-3 text-gray-300">{{ $p->platform }}</td>
                    <td class="px-4 py-3 text-white font-medium">{{ $p->name }}</td>
                    <td class="px-4 py-3 text-gray-400 hidden md:table-cell truncate max-w-xs"><a href="{{ $p->url }}" target="_blank" rel="noopener" class="text-purple-400 hover:underline">{{ \Illuminate\Support\Str::limit($p->url, 50) }}</a></td>
                    <td class="px-4 py-3 text-gray-400">{{ $p->sort_order }}</td>
                    <td class="px-4 py-3">{{ $p->is_active ? 'Yes' : 'No' }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.editorial-playlists.catalog.edit', $p) }}" class="text-gray-400 hover:text-white text-sm mr-2">Edit</a>
                        <form method="POST" action="{{ route('admin.editorial-playlists.catalog.destroy', $p->id) }}" class="inline" onsubmit="return confirm('Delete this playlist from catalog? Existing submissions keep their data.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-300 text-sm">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-12 text-center text-gray-500">No playlists. Run the seeder or add one.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $playlists->withQueryString()->links() }}

    <!-- Add modal -->
    <dialog x-ref="addModal" class="bg-gray-900 rounded-2xl border border-white/10 shadow-xl max-w-md w-full p-6 backdrop:bg-black/70" @click.self="$refs.addModal.close()">
        <form method="POST" action="{{ route('admin.editorial-playlists.catalog.store') }}">
            @csrf
            <h3 class="text-lg font-semibold text-white mb-4">Add playlist</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Platform</label>
                    <select name="platform" required class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-lg">
                        @foreach(['Spotify','Apple Music','Amazon Music'] as $p)
                        <option value="{{ $p }}">{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Name</label>
                    <input type="text" name="name" required maxlength="255" class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-lg" placeholder="Playlist display name">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">URL</label>
                    <input type="url" name="url" required class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-lg" placeholder="https://...">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Sort order</label>
                    <input type="number" name="sort_order" value="0" min="0" class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-lg">
                </div>
                <label class="flex items-center gap-2 text-gray-400 text-sm">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded border-white/20 text-purple-600"> Active
                </label>
            </div>
            <div class="flex justify-end gap-2 mt-4">
                <button type="button" onclick="this.closest('dialog').close()" class="px-4 py-2 text-gray-400 hover:text-white text-sm">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-lg transition">Add</button>
            </div>
        </form>
    </dialog>
</div>
@endsection
