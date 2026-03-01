@extends('layouts.admin')

@section('title', 'FAQs')
@section('page-title', 'Frequently Asked Questions')

@section('content')
<div class="space-y-6" x-data="{ showForm: false, editId: null, editQuestion: '', editAnswer: '', editCategory: '' }">

    <div class="flex justify-end">
        <button @click="showForm = !showForm; editId = null; editQuestion = ''; editAnswer = ''; editCategory = ''"
            class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-xl transition flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Add FAQ</span>
        </button>
    </div>

    <!-- Add/Edit Form -->
    <div x-show="showForm" x-cloak x-transition class="bg-gray-900 rounded-2xl border border-white/5 p-6">
        <h3 class="font-semibold text-white mb-4" x-text="editId ? 'Edit FAQ' : 'Add FAQ'"></h3>
        <form :action="editId ? `/admin/faqs/${editId}` : '{{ route('admin.faqs.store') }}'" method="POST">
            @csrf
            <input type="hidden" name="_method" :value="editId ? 'PUT' : 'POST'">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm text-gray-400 mb-1.5">Category</label>
                    <input type="text" name="category" :value="editCategory" placeholder="e.g. Distribution, Account, Payment..."
                        class="w-full bg-gray-800 border border-white/10 text-white placeholder-gray-500 px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1.5">Question <span class="text-red-400">*</span></label>
                    <input type="text" name="question" :value="editQuestion" required
                        class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1.5">Answer <span class="text-red-400">*</span></label>
                    <textarea name="answer" :value="editAnswer" rows="4" required
                        class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm resize-none"></textarea>
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-4">
                <button type="button" @click="showForm = false" class="px-4 py-2 text-gray-400 hover:text-white text-sm transition">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-xl transition">Save FAQ</button>
            </div>
        </form>
    </div>

    <!-- FAQ List -->
    <div class="space-y-3">
        @forelse($faqs as $faq)
        <div class="bg-gray-900 rounded-xl border border-white/5 p-5" x-data="{ open: false }">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0 cursor-pointer" @click="open = !open">
                    <div class="flex items-center space-x-2 mb-1">
                        @if($faq->category)
                        <span class="text-xs bg-purple-600/20 text-purple-400 px-2 py-0.5 rounded-full">{{ $faq->category }}</span>
                        @endif
                    </div>
                    <p class="text-white font-medium">{{ $faq->question }}</p>
                    <p x-show="open" x-cloak x-transition class="text-gray-400 text-sm mt-2 leading-relaxed">{{ $faq->answer }}</p>
                </div>
                <div class="flex items-center space-x-1 ml-3 flex-shrink-0">
                    <button @click="showForm = true; editId = {{ $faq->id }}; editQuestion = {{ json_encode($faq->question) }}; editAnswer = {{ json_encode($faq->answer) }}; editCategory = {{ json_encode($faq->category) }}"
                        class="p-1.5 text-gray-400 hover:text-white hover:bg-white/10 rounded-lg transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <form method="POST" action="{{ route('admin.faqs.destroy', $faq->id) }}" onsubmit="return confirm('Delete this FAQ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-1.5 text-gray-400 hover:text-red-400 hover:bg-white/10 rounded-lg transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="py-12 text-center text-gray-500">No FAQs yet. Add your first FAQ above.</div>
        @endforelse
    </div>
</div>
@endsection
