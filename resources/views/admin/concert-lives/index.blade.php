@extends('layouts.admin')

@section('title', 'Concert Lives')
@section('page-title', 'Concert Live Slots')

@section('content')
 <div class="space-y-6" x-data="{ showForm: false, editId: null, creating: false }">
    @if(session('success'))
        <div class="p-3 rounded-xl bg-green-900/30 border border-green-500/30 text-green-300 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="p-3 rounded-xl bg-red-900/30 border border-red-500/30 text-red-300 text-sm">{{ session('error') }}</div>
    @endif

    <div class="flex justify-end">
        <button @click="showForm = !showForm; editId = null"
            class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-xl transition flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Add Live Slot</span>
        </button>
    </div>

    <!-- Add Form -->
    <div x-show="showForm" x-cloak x-transition class="bg-gray-900 rounded-2xl border border-white/5 p-6">
        <h3 class="font-semibold text-white mb-4">Add Concert Live Slot</h3>
        <form method="POST" action="{{ route('admin.concert-lives.store') }}"
              @submit.prevent="creating = true; $event.target.submit();">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-400 mb-1.5">Event Name <span class="text-red-400">*</span></label>
                    <input type="text" name="name" required class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1.5">Concert date <span class="text-red-400">*</span></label>
                    <input type="date" name="concert_date" required class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1.5">City <span class="text-red-400">*</span></label>
                    <input type="text" name="city" required maxlength="100" class="w-full bg-gray-800 border border-white/10 text-white placeholder-gray-500 px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1.5">Slots available <span class="text-red-400">*</span></label>
                    <input type="number" name="slots_available" min="1" max="1000" required class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
                </div>
                <label class="sm:col-span-2 flex items-center gap-2 text-gray-400 text-sm">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded border-white/20 text-purple-600"> Active
                </label>
            </div>
            <div class="flex justify-end space-x-3 mt-4">
                <button type="button" @click="showForm = false" class="px-4 py-2 text-gray-400 hover:text-white text-sm transition">Cancel</button>
                <button type="submit"
                        :disabled="creating"
                        class="px-5 py-2 bg-purple-600 hover:bg-purple-700 disabled:bg-purple-900/40 disabled:cursor-not-allowed text-white text-sm rounded-xl transition inline-flex items-center gap-2">
                    <svg x-show="!creating" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <svg x-show="creating" class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    <span x-text="creating ? 'Adding...' : 'Add Slot'"></span>
                </button>
            </div>
        </form>
    </div>

    <div class="bg-gray-900 rounded-xl border border-white/5 overflow-hidden">
        <table class="w-full text-sm min-w-[760px]">
            <thead class="bg-gray-800/50 border-b border-white/5">
                <tr>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium">Concert</th>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium hidden md:table-cell">Slots</th>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium">Active</th>
                    <th class="text-right px-4 py-3 text-gray-400 font-medium">Actions</th>
                </tr>
            </thead>
            @forelse($concerts as $c)
            <tbody class="divide-y divide-white/3" x-data="{ open: false }">
                <tr class="hover:bg-white/2 transition">
                    <td class="px-4 py-3">
                        <div class="text-white font-semibold">{{ $c->name }}</div>
                        <div class="text-gray-400 text-xs">{{ $c->city }} • {{ $c->concert_date ? $c->concert_date->format('M d, Y') : '—' }}</div>
                    </td>
                    <td class="px-4 py-3 text-gray-300 hidden md:table-cell">
                        <div class="text-xs text-gray-400">Booked {{ $c->slots_booked }} / {{ $c->slots_available }} • Remaining {{ $c->slots_remaining }}</div>
                        <div class="h-1.5 bg-white/10 rounded-full mt-2">
                            <div class="h-full bg-purple-600 rounded-full" style="width: {{ $c->booking_percentage }}%"></div>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">{{ $c->booking_percentage }}%</div>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $c->is_active ? 'bg-green-900/50 text-green-400' : 'bg-gray-700/50 text-gray-300' }}">
                            {{ $c->is_active ? 'Yes' : 'No' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <button type="button" @click="open = !open" class="px-3 py-1.5 bg-white/10 hover:bg-white/15 text-gray-300 text-xs rounded-lg transition">Edit</button>
                        <form method="POST" action="{{ route('admin.concert-lives.destroy', $c->id) }}" class="inline" onsubmit="return confirm('Delete this concert?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 bg-red-900/30 hover:bg-red-900/50 text-red-300 text-xs rounded-lg transition">Delete</button>
                        </form>
                    </td>
                </tr>
                <tr x-show="open" x-cloak>
                    <td colspan="4" class="px-4 py-4 bg-black/20">
                        <form method="POST" action="{{ route('admin.concert-lives.update', $c->id) }}" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
                            @csrf @method('PUT')
                            <div class="md:col-span-2">
                                <label class="block text-xs text-gray-400 mb-1">Name</label>
                                <input type="text" name="name" value="{{ $c->name }}" required class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-400 mb-1">City</label>
                                <input type="text" name="city" value="{{ $c->city }}" required class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-400 mb-1">Date</label>
                                <input type="date" name="concert_date" value="{{ $c->concert_date ? $c->concert_date->format('Y-m-d') : '' }}" required class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-400 mb-1">Slots</label>
                                <input type="number" name="slots_available" value="{{ $c->slots_available }}" min="1" max="1000" required class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-lg text-sm">
                            </div>
                            <label class="flex items-center gap-2 text-gray-300 text-sm md:col-span-5">
                                <input type="checkbox" name="is_active" value="1" {{ $c->is_active ? 'checked' : '' }} class="rounded border-white/20 text-purple-600"> Active
                            </label>
                            <div class="md:col-span-5 flex justify-end gap-2">
                                <button type="button" @click="open = false" class="px-4 py-2 bg-white/10 hover:bg-white/15 text-gray-300 text-sm rounded-lg transition">Cancel</button>
                                <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-lg transition">Save</button>
                            </div>
                        </form>
                    </td>
                </tr>
            </tbody>
            @empty
            <tbody><tr><td colspan="4" class="px-4 py-12 text-center text-gray-500">No concerts created yet.</td></tr></tbody>
            @endforelse
        </table>
    </div>

    <div>{{ $concerts->withQueryString()->links() }}</div>
</div>
@endsection
