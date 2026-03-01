@extends('layouts.app')

@section('title', 'Features — Beat Music')

@section('content')
{{-- Hero --}}
<section class="pt-32 pb-20 px-4 text-center relative overflow-hidden">
    <div class="absolute inset-0 hero-glow pointer-events-none opacity-50"></div>
    <div class="max-w-4xl mx-auto relative z-10 animate-fade-in-up">
        <span class="inline-block bg-cyan-500/10 border border-cyan-500/20 text-cyan-300 text-xs font-semibold px-5 py-2 rounded-full mb-6 uppercase tracking-wider">Everything You Need</span>
        <h1 class="text-4xl sm:text-6xl font-bold text-white leading-tight mb-6">
            Powerful Features for <span class="gradient-text">Independent Artists</span>
        </h1>
        <p class="text-xl text-slate-400 leading-relaxed max-w-2xl mx-auto">
            From distribution to analytics, radio promotion to Vevo verification — Beat Music gives you all the tools to grow your music career.
        </p>
    </div>
</section>

{{-- Features Grid --}}
<section class="py-20 px-4 bg-slate-900/30 relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-b from-transparent via-slate-900/20 to-transparent"></div>
    <div class="max-w-6xl mx-auto relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            @php
            $features = [
                ['icon'=>'🎵','title'=>'Global Distribution','desc'=>'Distribute your music to 150+ streaming platforms including Spotify, Apple Music, YouTube Music, Tidal, and more.'],
                ['icon'=>'📊','title'=>'Real-Time Analytics','desc'=>'Track your streams, listeners, and revenue across all platforms from one beautiful dashboard.'],
                ['icon'=>'📻','title'=>'Radio Promotion','desc'=>'Get your music played on radio stations worldwide through our extensive network of radio partners.'],
                ['icon'=>'🎬','title'=>'Vevo Verification','desc'=>'Apply for a verified Vevo channel and get your music videos distributed on YouTube with the Vevo brand.'],
                ['icon'=>'🎭','title'=>'Concert Live Slots','desc'=>'Request live performance slots at events and festivals managed through the Beat Music network.'],
                ['icon'=>'💿','title'=>'Singles & Albums','desc'=>'Upload singles with a single track or full albums with up to 20 tracks. Support for all major audio formats.'],
                ['icon'=>'💾','title'=>'Pre-Save Campaigns','desc'=>'Create pre-save campaigns so your fans can save your upcoming releases before they drop.'],
                ['icon'=>'🎼','title'=>'Editorial Playlists','desc'=>'Submit your tracks for consideration on curated editorial playlists on major platforms.'],
                ['icon'=>'💰','title'=>'Revenue & Royalties','desc'=>'Earn royalties from every stream. Transparent reporting and easy payout requests to your bank or PayPal.'],
                ['icon'=>'🏷️','title'=>'White Label UPC/ISRC','desc'=>'Every release gets its own UPC barcode and ISRC codes automatically assigned.'],
                ['icon'=>'🎙️','title'=>'Artist Profile','desc'=>'Build your artist profile with bio, social links, and a portfolio of all your releases.'],
                ['icon'=>'🛡️','title'=>'24/7 Support','desc'=>'Our support team is always available via the help center, knowledge base, and ticketing system.'],
            ];
            @endphp

            @foreach($features as $index => $feature)
            <div class="glass rounded-2xl p-6 border border-slate-700/30 hover:border-cyan-500/30 transition-all duration-500 card-hover group opacity-100"
                 style="animation: fadeInUp 0.6s ease-out {{ $index * 0.05 }}s both;">
                <div class="text-4xl mb-4 transform transition-transform duration-300 group-hover:scale-110">{{ $feature['icon'] }}</div>
                <h3 class="text-white font-semibold text-lg mb-2 group-hover:text-cyan-400 transition-colors duration-200">{{ $feature['title'] }}</h3>
                <p class="text-slate-400 text-sm leading-relaxed">{{ $feature['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="py-24 px-4">
    <div class="max-w-3xl mx-auto text-center glass rounded-3xl p-12 border border-slate-700/30 bg-gradient-to-br from-cyan-500/10 to-teal-600/10 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-cyan-500/5 to-transparent"></div>
        <div class="relative z-10 animate-fade-in-up">
            <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4">Ready to Get Started?</h2>
            <p class="text-slate-400 mb-8 leading-relaxed">Join thousands of independent artists already using Beat Music to grow their careers.</p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('login') }}" class="group px-8 py-4 bg-gradient-to-r from-cyan-500 to-teal-600 hover:from-cyan-600 hover:to-teal-700 text-white font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-xl shadow-cyan-500/25 hover:shadow-cyan-500/40">
                    Sign In
                    <span class="inline-block ml-2 transition-transform duration-300 group-hover:translate-x-1">→</span>
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
