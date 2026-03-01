@extends('layouts.admin')

@section('title', 'User Management')
@section('page-title', 'User Management')

@section('content')
<div class="space-y-4" x-data="userList()">
    <!-- Filters & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <form method="GET" id="user-filter-form" class="flex flex-col sm:flex-row gap-3 flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..."
                class="bg-gray-800 border border-white/10 text-white placeholder-gray-500 px-3 py-2 rounded-lg text-sm focus:outline-none focus:border-purple-500 w-full sm:w-64">
            <select name="status" class="bg-gray-800 border border-white/10 text-gray-300 px-3 py-2 rounded-lg text-sm">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
            </select>
            <select name="artist_type" class="bg-gray-800 border border-white/10 text-gray-300 px-3 py-2 rounded-lg text-sm">
                <option value="">All Types</option>
                <option value="individual" {{ request('artist_type') == 'individual' ? 'selected' : '' }}>Individual</option>
                <option value="company" {{ request('artist_type') == 'company' ? 'selected' : '' }}>Company</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-lg transition">Filter</button>
        </form>
        <a href="{{ route('admin.users.create') }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg transition flex items-center space-x-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Add User</span>
        </a>
    </div>

    <div class="bg-gray-900 rounded-xl border border-white/5 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-800/50 border-b border-white/5">
                <tr>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium">User</th>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium hidden sm:table-cell">Type</th>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium hidden lg:table-cell">Joined</th>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium">Verified</th>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium">Status</th>
                    <th class="text-right px-4 py-3 text-gray-400 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/3">
                @forelse($users as $user)
                <tr class="hover:bg-white/2 transition">
                    <td class="px-4 py-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 bg-gradient-to-br from-purple-600 to-indigo-600 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                {{ strtoupper(substr($user->full_name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="text-white font-medium">{{ $user->full_name }}</p>
                                <p class="text-gray-400 text-xs">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 hidden sm:table-cell">
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $user->is_company ? 'bg-indigo-900/50 text-indigo-400' : 'bg-gray-700/50 text-gray-400' }}">{{ $user->is_company ? 'Company' : 'Individual' }}</span>
                    </td>
                    <td class="px-4 py-3 text-gray-400 text-xs hidden lg:table-cell">{{ $user->created_at->format('M d, Y') }}</td>
                    <td class="px-4 py-3">
                        @if($user->is_verified)
                            <span class="inline-flex items-center text-green-400" title="Verified"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg></span>
                        @else
                            <span class="inline-flex items-center text-amber-400" title="Unverified - password not set"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg></span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $user->status === 'active' ? 'bg-green-900/50 text-green-400' : 'bg-red-900/50 text-red-400' }}">{{ ucfirst($user->status) }}</span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end space-x-1">
                            <a href="{{ route('admin.users.show', $user->id) }}" class="p-1.5 text-gray-400 hover:text-white hover:bg-white/10 rounded-lg transition" title="View">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="p-1.5 text-gray-400 hover:text-white hover:bg-white/10 rounded-lg transition" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <button type="button"
                                @click="openToggleModal({{ $user->id }}, '{{ addslashes($user->full_name) }}', '{{ $user->status }}')"
                                class="p-1.5 text-gray-400 hover:{{ $user->status === 'active' ? 'text-red-400' : 'text-green-400' }} hover:bg-white/10 rounded-lg transition"
                                title="{{ $user->status === 'active' ? 'Suspend' : 'Activate' }}">
                                @if($user->status === 'active')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @endif
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-12 text-center text-gray-500">No users found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $users->withQueryString()->links() }}</div>

    <!-- Ban/Unban confirm modal -->
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-black/70" @click="if (!confirmLoading) modalOpen = false"></div>
        <div class="relative bg-gray-900 border border-white/10 rounded-xl shadow-xl max-w-sm w-full p-6" @click.stop>
            <h3 class="text-lg font-semibold text-white mb-2" x-text="isBan ? 'Suspend user?' : 'Activate user?'"></h3>
            <p class="text-gray-400 text-sm mb-4">
                <span x-text="isBan ? 'This user will be suspended and cannot sign in.' : 'This user will be activated and can sign in again.'"></span>
                <span class="block mt-1 font-medium text-white" x-show="userName" x-text="'User: ' + userName"></span>
            </p>
            <form :action="toggleAction" method="POST" id="toggle-suspension-form" @submit="confirmLoading = true">
                @csrf
            </form>
            <div class="flex justify-end gap-2">
                <button type="button" @click="if (!confirmLoading) modalOpen = false" :disabled="confirmLoading" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-gray-300 rounded-lg border border-white/10 text-sm transition disabled:opacity-50">Cancel</button>
                <button type="submit" form="toggle-suspension-form" :disabled="confirmLoading" class="px-4 py-2 rounded-lg text-sm transition flex items-center gap-2 min-w-[100px] justify-center disabled:opacity-70"
                    :class="isBan ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-green-600 hover:bg-green-700 text-white'">
                    <span x-show="confirmLoading" class="inline-block w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                    <span x-text="confirmLoading ? 'Please wait...' : (isBan ? 'Suspend' : 'Activate')"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function userList() {
        const toggleUrlTemplate = '{{ route("admin.users.toggle-suspension", ["id" => "__ID__"]) }}';
    return {
        modalOpen: false,
        userName: '',
        userId: null,
        isBan: true,
        confirmLoading: false,
        toggleAction: '',
        openToggleModal(id, name, status) {
            this.userId = id;
            this.userName = name;
            this.isBan = status === 'active';
            this.toggleAction = toggleUrlTemplate.replace('__ID__', id);
            this.confirmLoading = false;
            this.modalOpen = true;
        }
    };
}
</script>
@endsection
