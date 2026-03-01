@extends('layouts.dashboard')

@section('title', 'New Support Ticket')
@section('page-title', 'New Support Ticket')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <a href="{{ route('dashboard.support') }}" class="flex items-center space-x-2 text-gray-400 hover:text-white transition text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        <span>Back to Support</span>
    </a>

    @if($errors->any())
    <div class="bg-red-900/30 border border-red-500/30 text-red-400 p-4 rounded-xl text-sm">
        <ul class="space-y-1 list-disc list-inside">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('dashboard.support.store') }}" enctype="multipart/form-data"
        class="bg-gray-900 rounded-2xl border border-white/5 p-6 space-y-5"
        x-data="{ loading: false }"
        @submit="loading = true">
        @csrf

        <div>
            <label class="block text-sm text-gray-400 mb-1.5">Subject <span class="text-red-400">*</span></label>
            <input type="text" name="subject" value="{{ old('subject') }}" required placeholder="Briefly describe your issue"
                class="w-full bg-gray-800 border border-white/10 text-white placeholder-gray-500 px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm text-gray-400 mb-1.5">Category <span class="text-red-400">*</span></label>
                <select name="category" required class="w-full bg-gray-800 border border-white/10 text-gray-300 px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
                    <option value="">Select category...</option>
                    @foreach(['Distribution', 'Account', 'Payment', 'Technical', 'Royalties', 'Radio', 'Vevo', 'Other'] as $cat)
                    <option value="{{ strtolower($cat) }}" {{ old('category') === strtolower($cat) ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1.5">Priority</label>
                <select name="priority" class="w-full bg-gray-800 border border-white/10 text-gray-300 px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
                    <option value="low" {{ old('priority','low') === 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm text-gray-400 mb-1.5">Message <span class="text-red-400">*</span></label>
            <textarea name="message" rows="6" required placeholder="Describe your issue in detail. Include any relevant track IDs, dates, or error messages..."
                class="w-full bg-gray-800 border border-white/10 text-white placeholder-gray-500 px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm resize-none">{{ old('message') }}</textarea>
        </div>

        <div>
            <label class="block text-sm text-gray-400 mb-1.5">Attachment (optional)</label>
            <input type="file" name="attachment" accept="image/*,.pdf,.doc,.docx,.txt"
                class="w-full text-sm text-gray-400 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:bg-purple-600/20 file:text-purple-400 hover:file:bg-purple-600/30 cursor-pointer">
            <p class="text-xs text-gray-500 mt-1">Images, PDF, or documents. Max 5MB.</p>
        </div>

        <div class="flex justify-end space-x-3 pt-2">
            <a href="{{ route('dashboard.support') }}" class="px-5 py-2.5 bg-white/5 hover:bg-white/10 text-gray-300 rounded-xl border border-white/10 transition text-sm">Cancel</a>
            <button type="submit"
                :disabled="loading"
                class="px-6 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold rounded-xl transition text-sm inline-flex items-center gap-2 disabled:opacity-60">
                <span x-show="loading" class="inline-block w-4 h-4 border-2 border-white/40 border-t-white rounded-full animate-spin"></span>
                <span x-text="loading ? 'Submitting...' : 'Submit Ticket'"></span>
            </button>
        </div>
    </form>
</div>
@endsection
