@extends('layouts.app')

@section('title', 'Press — Beat Music')

@section('content')
<section class="pt-32 pb-16 px-4 text-center">
    <div class="max-w-4xl mx-auto">
        <span class="inline-block bg-purple-600/20 text-purple-300 text-sm font-medium px-4 py-1.5 rounded-full border border-purple-500/30 mb-6">Press Room</span>
        <h1 class="text-4xl sm:text-5xl font-bold text-white mb-6">Press & Media</h1>
        <p class="text-xl text-gray-400 max-w-2xl mx-auto">
            Resources for journalists, bloggers, and media professionals covering the music industry.
        </p>
    </div>
</section>

<section class="pb-24 px-4">
    <div class="max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Press Kit --}}
        <div class="glass rounded-2xl p-6 border border-white/5 text-center">
            <div class="text-4xl mb-3">📦</div>
            <h3 class="text-white font-semibold mb-2">Press Kit</h3>
            <p class="text-gray-400 text-sm mb-4">Download our logos, brand guidelines, and company fact sheet.</p>
            <a href="#" class="inline-block px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-xl transition">Download Kit</a>
        </div>
        <div class="glass rounded-2xl p-6 border border-white/5 text-center">
            <div class="text-4xl mb-3">📸</div>
            <h3 class="text-white font-semibold mb-2">Media Gallery</h3>
            <p class="text-gray-400 text-sm mb-4">High-resolution images and screenshots for editorial use.</p>
            <a href="#" class="inline-block px-5 py-2 bg-white/10 hover:bg-white/15 text-white text-sm rounded-xl border border-white/10 transition">Browse Gallery</a>
        </div>
        <div class="glass rounded-2xl p-6 border border-white/5 text-center">
            <div class="text-4xl mb-3">✉️</div>
            <h3 class="text-white font-semibold mb-2">Press Inquiries</h3>
            <p class="text-gray-400 text-sm mb-4">For interviews, features, and press coverage requests.</p>
            <a href="mailto:press@beatmusic.com" class="inline-block px-5 py-2 bg-white/10 hover:bg-white/15 text-white text-sm rounded-xl border border-white/10 transition">Contact Press Team</a>
        </div>
    </div>

    {{-- Company Stats --}}
    <div class="max-w-5xl mx-auto mt-12">
        <h2 class="text-2xl font-bold text-white mb-6">Company Facts</h2>
        <div class="glass rounded-2xl p-8 border border-white/5 grid grid-cols-2 sm:grid-cols-4 gap-6 text-center">
            @foreach(['10,000+'=>'Artists','150+'=>'Platforms','50M+'=>'Monthly Streams','2020'=>'Founded'] as $num => $label)
            <div>
                <p class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-indigo-400">{{ $num }}</p>
                <p class="text-gray-400 text-sm mt-1">{{ $label }}</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Recent Coverage --}}
    <div class="max-w-5xl mx-auto mt-12">
        <h2 class="text-2xl font-bold text-white mb-6">As Seen In</h2>
        <div class="glass rounded-2xl p-8 border border-white/5">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-6">
                @foreach(['TechCrunch','Billboard','Rolling Stone','Forbes','Variety','Pitchfork','Wired','The Verge'] as $outlet)
                <div class="text-center py-3 px-4 bg-white/3 rounded-xl border border-white/5">
                    <p class="text-gray-400 text-sm font-medium">{{ $outlet }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endsection
