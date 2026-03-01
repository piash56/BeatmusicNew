@extends('layouts.admin')

@section('title', 'Add User')
@section('page-title', 'Add New User')

@section('content')
<div class="max-w-2xl space-y-6">
    <a href="{{ route('admin.users') }}" class="flex items-center space-x-2 text-gray-400 hover:text-white transition text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        <span>Back to Users</span>
    </a>

    @if($errors->any())
    <div class="bg-red-900/30 border border-red-500/30 text-red-400 p-4 rounded-xl text-sm">
        <ul class="space-y-1 list-disc list-inside">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST"
          action="{{ route('admin.users.store') }}"
          class="bg-gray-900 rounded-2xl border border-white/5 p-6 space-y-5"
          x-data="{ loading: false }"
          @submit="loading = true">
        @csrf
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-sm text-gray-400 mb-1.5">Full Name <span class="text-red-400">*</span></label>
                <input type="text" name="full_name" value="{{ old('full_name') }}" required
                    class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm text-gray-400 mb-1.5">Email <span class="text-red-400">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm text-gray-400 mb-1.5">Artist Type <span class="text-red-400">*</span></label>
                <select name="artist_type" required class="w-full bg-gray-800 border border-white/10 text-gray-300 px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
                    <option value="individual" {{ old('artist_type', 'individual') === 'individual' ? 'selected' : '' }}>Individual</option>
                    <option value="company" {{ old('artist_type') === 'company' ? 'selected' : '' }}>Company</option>
                </select>
            </div>
        </div>
        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.users') }}" class="px-5 py-2 bg-white/5 hover:bg-white/10 text-gray-300 rounded-xl border border-white/10 transition text-sm">Cancel</a>
            <button type="submit"
                    :disabled="loading"
                    class="px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-xl transition disabled:opacity-60 flex items-center gap-2">
                <span x-show="loading" x-cloak class="inline-block w-4 h-4 border-2 border-white/40 border-t-white rounded-full animate-spin"></span>
                <span x-text="loading ? 'Creating...' : 'Create User'"></span>
            </button>
        </div>
    </form>
</div>
@endsection
