@extends('layouts.app')

@section('title', 'News — Beat Music')

@section('content')
<section class="pt-32 pb-16 px-4">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-12">
            <span class="inline-block bg-purple-600/20 text-purple-300 text-sm font-medium px-4 py-1.5 rounded-full border border-purple-500/30 mb-4">Latest Updates</span>
            <h1 class="text-4xl font-bold text-white">Beat Music News</h1>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @php
            $articles = [
                ['Beat Music Reaches 10,000 Artists Milestone','We are thrilled to announce that Beat Music has surpassed 10,000 independent artists on our platform...','Jan 15, 2024','Platform News','🎉'],
                ['New Feature: Pre-Save Campaigns','Artists can now create pre-save campaigns for upcoming releases. Build anticipation and track fan engagement before your release date.','Jan 8, 2024','Product Update','💾'],
                ['Vevo Partnership Expansion','Beat Music artists can now apply for Vevo channel verification directly through the dashboard, with faster processing times.','Dec 20, 2023','Partnerships','🎬'],
                ['Radio Promotion Network Grows to 500+ Stations','Our radio promotion network has expanded to over 500 radio stations across 40 countries.','Dec 5, 2023','Platform News','📻'],
                ['Year in Review: 2023 Artist Success','2023 was a record year for our artists. Here are the highlights: 50M streams, 10K+ releases, 45 countries reached.','Dec 1, 2023','Community','🌍'],
                ['Introducing PayPal Payouts','Artists can now receive royalty payouts directly to their PayPal accounts in addition to bank transfers.','Nov 15, 2023','Product Update','💸'],
            ];
            @endphp

            @foreach($articles as [$title, $excerpt, $date, $category, $emoji])
            <article class="glass rounded-2xl border border-white/5 overflow-hidden hover:border-purple-500/30 transition group">
                <div class="h-40 bg-gradient-to-br from-purple-900/40 to-indigo-900/40 flex items-center justify-center text-6xl">
                    {{ $emoji }}
                </div>
                <div class="p-5">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs px-2 py-0.5 bg-purple-600/20 text-purple-300 rounded-full border border-purple-500/20">{{ $category }}</span>
                        <span class="text-xs text-gray-500">{{ $date }}</span>
                    </div>
                    <h3 class="text-white font-semibold mb-2 group-hover:text-purple-300 transition">{{ $title }}</h3>
                    <p class="text-gray-400 text-sm line-clamp-2">{{ $excerpt }}</p>
                    <a href="#" class="inline-flex items-center space-x-1 text-purple-400 hover:text-purple-300 text-xs mt-3 transition">
                        <span>Read more</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </article>
            @endforeach
        </div>
    </div>
</section>
@endsection
