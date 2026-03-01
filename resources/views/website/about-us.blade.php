@extends('layouts.app')

@section('title', 'About Us — Beat Music')

@section('content')
{{-- Hero --}}
<section class="pt-32 pb-20 px-4 text-center">
    <div class="max-w-4xl mx-auto">
        <span class="inline-block bg-cyan-500/10 border border-cyan-500/20 text-cyan-300 text-xs font-semibold px-5 py-2 rounded-full mb-6 uppercase tracking-wider">Our Story</span>
        <h1 class="text-4xl sm:text-6xl font-bold text-white leading-tight mb-6">
            Built by Artists, <span class="gradient-text">for Artists</span>
        </h1>
        <p class="text-xl text-gray-400 leading-relaxed max-w-2xl mx-auto">
            Beat Music was founded with one mission: to give independent artists the same tools and opportunities as major label artists — without the gatekeepers.
        </p>
    </div>
</section>

{{-- Mission --}}
<section class="py-16 px-4">
    <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
        <div>
            <h2 class="text-3xl font-bold text-white mb-4">Our Mission</h2>
            <p class="text-gray-400 leading-relaxed mb-4">
                The music industry has long been dominated by a few powerful labels that control who gets heard and who doesn't. Beat Music is changing that by providing a world-class distribution platform that puts artists first.
            </p>
            <p class="text-gray-400 leading-relaxed">
                We believe every artist deserves fair royalties, global reach, and the tools to build a sustainable music career. That's why we keep our fees transparent and our technology cutting-edge.
            </p>
        </div>
        <div class="grid grid-cols-2 gap-4">
            @foreach(['10K+' => 'Artists Worldwide', '150+' => 'Platforms', '50M+' => 'Monthly Streams', '98%' => 'Satisfaction Rate'] as $num => $label)
            <div class="glass rounded-2xl p-6 text-center border border-white/5">
                <p class="text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-indigo-400">{{ $num }}</p>
                <p class="text-gray-400 text-sm mt-1">{{ $label }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Values --}}
<section class="py-20 px-4 bg-white/2">
    <div class="max-w-5xl mx-auto">
        <h2 class="text-3xl font-bold text-white text-center mb-12">Our Values</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @php
            $values = [
                ['icon'=>'🎯','title'=>'Artist First','desc'=>'Every decision we make is guided by what\'s best for our artists. Your success is our success.'],
                ['icon'=>'🔍','title'=>'Transparency','desc'=>'No hidden fees. No surprises. Clear royalty statements and honest communication always.'],
                ['icon'=>'🚀','title'=>'Innovation','desc'=>'We constantly improve our platform so you always have access to the latest distribution technology.'],
            ];
            @endphp
            @foreach($values as $v)
            <div class="glass rounded-2xl p-6 border border-slate-700/30 hover:border-cyan-500/30 transition-all duration-300 card-hover text-center">
                <div class="text-4xl mb-3">{{ $v['icon'] }}</div>
                <h3 class="text-white font-semibold text-lg mb-2">{{ $v['title'] }}</h3>
                <p class="text-gray-400 text-sm">{{ $v['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Team --}}
<section class="py-20 px-4">
    <div class="max-w-5xl mx-auto text-center">
        <h2 class="text-3xl font-bold text-white mb-4">The Team</h2>
        <p class="text-gray-400 mb-12">Passionate music lovers and technology experts working together to serve artists.</p>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6">
            @php
            $team = [
                ['name'=>'Alex Rivera','role'=>'CEO & Co-founder'],['name'=>'Maya Chen','role'=>'CTO'],
                ['name'=>'Jordan Lee','role'=>'Head of Artist Relations'],['name'=>'Sam Okafor','role'=>'Lead Engineer'],
                ['name'=>'Priya Sharma','role'=>'Marketing Director'],['name'=>'Chris Bolton','role'=>'Product Manager'],
                ['name'=>'Fatima Al-Zahra','role'=>'Community Manager'],['name'=>'Liam Park','role'=>'Data Analyst'],
            ];
            @endphp
            @foreach($team as $member)
            <div class="glass rounded-2xl p-4 border border-white/5">
                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-purple-600 to-indigo-600 flex items-center justify-center text-white font-bold text-xl mx-auto mb-3">
                    {{ strtoupper(substr($member['name'], 0, 1)) }}
                </div>
                <p class="text-white font-medium text-sm">{{ $member['name'] }}</p>
                <p class="text-gray-400 text-xs">{{ $member['role'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="py-24 px-4">
    <div class="max-w-3xl mx-auto text-center glass rounded-3xl p-12 border border-white/5">
        <h2 class="text-3xl font-bold text-white mb-4">Join the Beat Music Family</h2>
        <p class="text-gray-400 mb-8">Start your journey as an independent artist today.</p>
        <a href="{{ route('login') }}" class="group px-8 py-4 bg-gradient-to-r from-cyan-500 to-teal-600 hover:from-cyan-600 hover:to-teal-700 text-white font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-xl shadow-cyan-500/25 hover:shadow-cyan-500/40">
            Sign In
            <span class="inline-block ml-2 transition-transform duration-300 group-hover:translate-x-1">→</span>
        </a>
    </div>
</section>
@endsection
