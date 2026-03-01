@extends('layouts.admin')

@section('title', 'Radio Networks')
@section('page-title', 'Radio Networks')

@section('content')
<div class="space-y-6" x-data="{ showForm: false, editId: null, editName: '', editIsActive: true }">

    <div class="flex justify-end">
        <button @click="showForm = !showForm; editId = null; editName = ''; editIsActive = true"
            class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-xl transition flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Add Network</span>
        </button>
    </div>

    <!-- Add/Edit Form -->
    <div x-show="showForm" x-cloak x-transition class="bg-gray-900 rounded-2xl border border-white/5 p-6">
        <h3 class="font-semibold text-white mb-4" x-text="editId ? 'Modifica network radio' : 'Aggiungi network radio'"></h3>
        <form :action="editId ? `/admin/radio-networks/${editId}` : '{{ route('admin.radio-networks.store') }}'" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" :value="editId ? 'PUT' : 'POST'">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-400 mb-1.5">Nome network <span class="text-red-400">*</span></label>
                    <input type="text" name="name" x-model="editName" required
                        class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm"
                        placeholder="Es. Radio Hits Network">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1.5">Logo / immagine</label>
                    <input type="file" name="cover_image"
                        class="w-full text-sm text-gray-300 file:mr-3 file:px-3 file:py-2.5 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-purple-600 file:text-white hover:file:bg-purple-700 bg-gray-800 border border-white/10 rounded-xl">
                    <p class="text-gray-500 text-xs mt-1">JPEG/PNG, massimo 5MB.</p>
                </div>
                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" id="is_active" name="is_active" x-model="editIsActive"
                           class="h-4 w-4 rounded border-white/20 bg-gray-800 text-purple-500 focus:ring-purple-500">
                    <label for="is_active" class="text-sm text-gray-300">Network attivo</label>
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-4">
                <button type="button" @click="showForm = false" class="px-4 py-2 text-gray-400 hover:text-white text-sm transition">Annulla</button>
                <button type="submit" class="px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-xl transition">
                    <span x-text="editId ? 'Salva modifiche' : 'Crea network'"></span>
                </button>
            </div>
        </form>
    </div>

    <!-- Networks Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($networks as $network)
        <div class="bg-gray-900 rounded-2xl border border-white/5 p-5 flex flex-col gap-3">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-12 h-12 rounded-xl overflow-hidden bg-white/5 flex items-center justify-center shrink-0">
                        <img src="{{ $network->cover_image_url }}" alt="{{ $network->name }}" class="w-full h-full object-cover">
                    </div>
                    <div class="min-w-0">
                        <p class="text-white font-semibold truncate">{{ $network->name }}</p>
                        <p class="text-gray-500 text-xs mt-0.5 truncate">{{ $network->promotions_count }} campagne collegate</p>
                    </div>
                </div>
                <div class="flex flex-col items-end gap-1 shrink-0">
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-medium
                        {{ $network->is_active ? 'bg-green-900/40 text-green-300' : 'bg-gray-800 text-gray-300' }}">
                        {{ $network->is_active ? 'Attivo' : 'Disabilitato' }}
                    </span>
                    <div class="flex items-center space-x-1">
                        <button
                            @click="showForm = true; editId = {{ $network->id }}; editName = @js($network->name); editIsActive = {{ $network->is_active ? 'true' : 'false' }}"
                            class="p-1.5 text-gray-400 hover:text-white hover:bg-white/10 rounded-lg transition"
                            title="Modifica network">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        <form method="POST" action="{{ route('admin.radio-networks.destroy', $network->id) }}" onsubmit="return confirm('Delete this network?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1.5 text-gray-400 hover:text-red-400 hover:bg-white/10 rounded-lg transition" title="Elimina network">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="sm:col-span-2 lg:col-span-3 py-12 text-center text-gray-500">No radio networks yet.</div>
        @endforelse
    </div>
</div>
@endsection
