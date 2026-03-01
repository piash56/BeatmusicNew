@extends('layouts.app')

@section('title', 'Help Center — Beat Music')

@section('content')
<section class="pt-32 pb-16 px-4 text-center">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-4xl font-bold text-white mb-4">How can we help you?</h1>
        <p class="text-gray-400 mb-8">Search our help center or browse topics below</p>
        <div class="relative max-w-xl mx-auto">
            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" placeholder="Search help articles..." id="helpSearch"
                class="w-full bg-white/5 border border-white/10 text-white pl-12 pr-4 py-4 rounded-2xl focus:outline-none focus:border-purple-500 text-lg">
        </div>
    </div>
</section>

{{-- Quick actions --}}
<section class="pb-10 px-4">
    <div class="max-w-5xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            {{-- Live chat --}}
            <div class="glass rounded-2xl p-6 border border-white/5 text-left flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-full bg-purple-600/20 flex items-center justify-center mb-3">
                        <span class="text-lg">💬</span>
                    </div>
                    <h3 class="text-white font-semibold text-lg mb-1">Live chat</h3>
                    <p class="text-gray-400 text-sm">Chat with our support team in real time during business hours.</p>
                </div>
                <div class="mt-5">
                    @auth
                    <a href="{{ route('dashboard.support.create') }}"
                       class="inline-flex items-center justify-center px-4 py-2.5 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-xl transition w-full md:w-auto">
                        Start chat
                    </a>
                    @else
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center justify-center px-4 py-2.5 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-xl transition w-full md:w-auto">
                        Login to chat
                    </a>
                    @endauth
                </div>
            </div>

            {{-- Submit ticket --}}
            <div class="glass rounded-2xl p-6 border border-white/5 text-left flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-full bg-blue-600/20 flex items-center justify-center mb-3">
                        <span class="text-lg">✉️</span>
                    </div>
                    <h3 class="text-white font-semibold text-lg mb-1">Submit a ticket</h3>
                    <p class="text-gray-400 text-sm">Open a support ticket and we&apos;ll get back to you as soon as possible.</p>
                </div>
                <div class="mt-5">
                    @auth
                    <a href="{{ route('dashboard.support.create') }}"
                       class="inline-flex items-center justify-center px-4 py-2.5 bg-white/10 hover:bg-white/15 text-white text-sm rounded-xl border border-white/10 transition w-full md:w-auto">
                        Submit ticket
                    </a>
                    @else
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center justify-center px-4 py-2.5 bg-white/10 hover:bg-white/15 text-white text-sm rounded-xl border border-white/10 transition w-full md:w-auto">
                        Login to submit ticket
                    </a>
                    @endauth
                </div>
            </div>

            {{-- Knowledge base / documentation --}}
            <div class="glass rounded-2xl p-6 border border-white/5 text-left flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-full bg-cyan-500/20 flex items-center justify-center mb-3">
                        <span class="text-lg">📚</span>
                    </div>
                    <h3 class="text-white font-semibold text-lg mb-1">Knowledge base</h3>
                    <p class="text-gray-400 text-sm">Browse our documentation and step‑by‑step guides for common questions.</p>
                </div>
                <div class="mt-5">
                    {{-- Reload the same Help Center page --}}
                    <a href="{{ url()->current() }}"
                       class="inline-flex items-center justify-center px-4 py-2.5 bg-white/10 hover:bg-white/15 text-white text-sm rounded-xl border border-white/10 transition w-full md:w-auto">
                        Browse articles
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Categories --}}
<section class="pb-16 px-4">
    <div class="max-w-5xl mx-auto">
        {{-- <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5 mb-16">
            @php
            $categories = [
                ['🎵','Getting Started','Learn how to set up your artist account and upload your first release.','getting-started'],
                ['📦','Distribution','How to distribute your music to 150+ platforms.','distribution'],
                ['💰','Payments & Royalties','Understanding your earnings, payouts, and royalty statements.','payments'],
                ['📻','Radio & Promotion','Submitting your music for radio play and editorial playlists.','promotion'],
                ['🎬','Vevo & Video','How to apply for and manage your Vevo channel.','vevo'],
                ['🛠️','Account & Settings','Managing your profile, subscription, and account settings.','account'],
            ];
            @endphp

            @foreach($categories as [$icon, $title, $desc, $slug])
            <a href="{{ route('knowledge-base') }}?category={{ $slug }}" class="glass rounded-2xl p-6 border border-white/5 hover:border-purple-500/30 transition group">
                <div class="text-3xl mb-3">{{ $icon }}</div>
                <h3 class="text-white font-semibold mb-1 group-hover:text-purple-300 transition">{{ $title }}</h3>
                <p class="text-gray-400 text-sm">{{ $desc }}</p>
            </a>
            @endforeach
        </div> --}}

        {{-- FAQs --}}
        {{-- <h2 class="text-2xl font-bold text-white mb-8">Frequently Asked Questions</h2> --}}
        @if(isset($faqs) && $faqs->count())
        {{-- <div class="space-y-3" x-data="{open: null}">
            @foreach($faqs as $faq)
            <div class="glass rounded-xl border border-white/5 overflow-hidden">
                <button @click="open = open === {{ $loop->index }} ? null : {{ $loop->index }}"
                    class="w-full flex items-center justify-between px-6 py-4 text-left">
                    <span class="text-white font-medium">{{ $faq->question }}</span>
                    <svg :class="open === {{ $loop->index }} ? 'rotate-180' : ''" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open === {{ $loop->index }}" x-collapse class="px-6 pb-4">
                    <p class="text-gray-400 text-sm leading-relaxed">{{ $faq->answer }}</p>
                </div>
            </div>
            @endforeach
        </div> --}}
        @else
        {{-- <p class="text-gray-400 text-sm">No FAQs available yet. Check back soon.</p> --}}
        @endif
    </div>
</section>

{{-- Still need help? --}}
<section class="pb-24 px-4">
    <div class="max-w-3xl mx-auto text-center glass rounded-3xl p-10 border border-white/5">
        <h2 class="text-2xl font-bold text-white mb-3">Still need help?</h2>
        <p class="text-gray-400 mb-6">Our support team is ready to assist you.</p>
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="{{ url()->current() }}" class="px-6 py-2.5 bg-white/10 hover:bg-white/15 text-white text-sm rounded-xl border border-white/10 transition">Browse Knowledge Base</a>
            @auth
            <a href="{{ route('dashboard.support.create') }}" class="px-6 py-2.5 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-xl transition">Submit a Ticket</a>
            @else
            <a href="{{ route('login') }}" class="px-6 py-2.5 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-xl transition">Login to Submit Ticket</a>
            @endauth
        </div>
    </div>
</section>
@endsection
