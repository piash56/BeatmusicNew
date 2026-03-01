@extends('layouts.app')

@section('title', 'Careers — Beat Music')

@section('content')
<section class="pt-32 pb-16 px-4 text-center">
    <div class="max-w-4xl mx-auto">
        <span class="inline-block bg-purple-600/20 text-purple-300 text-sm font-medium px-4 py-1.5 rounded-full border border-purple-500/30 mb-6">We're Hiring</span>
        <h1 class="text-4xl sm:text-5xl font-bold text-white mb-6">Join the Beat Music Team</h1>
        <p class="text-xl text-gray-400 max-w-2xl mx-auto">
            Help us build the future of independent music. We're a passionate team of music lovers and tech builders working remotely across the globe.
        </p>
    </div>
</section>

{{-- Why Beat Music --}}
<section class="pb-16 px-4">
    <div class="max-w-5xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-16">
            @foreach([
                ['🌍','Remote First','Work from anywhere in the world. We hire globally and believe in async collaboration.'],
                ['🎵','Music-Driven','Every decision we make is guided by a passion for music and artists.'],
                ['🚀','Fast Growth','Join a fast-growing startup with real impact. Your work will directly affect thousands of artists.'],
                ['💰','Competitive Pay','Competitive salaries, equity options, and performance bonuses.'],
                ['🏥','Great Benefits','Health insurance, wellness budget, home office stipend, and generous PTO.'],
                ['📚','Learning Budget','$1,500 annual learning budget for courses, books, conferences, and tools.'],
            ] as [$icon, $title, $desc])
            <div class="glass rounded-2xl p-5 border border-white/5 text-center">
                <div class="text-3xl mb-2">{{ $icon }}</div>
                <h3 class="text-white font-semibold mb-1">{{ $title }}</h3>
                <p class="text-gray-400 text-sm">{{ $desc }}</p>
            </div>
            @endforeach
        </div>

        {{-- Open Roles --}}
        <h2 class="text-2xl font-bold text-white mb-6">Open Positions</h2>
        <div class="space-y-3">
            @foreach([
                ['Senior Full Stack Engineer','Engineering','Remote','Full-time'],
                ['Product Manager','Product','Remote','Full-time'],
                ['Artist Relations Manager','Artists','Remote','Full-time'],
                ['Data Analyst','Analytics','Remote','Full-time'],
                ['Customer Support Specialist','Support','Remote','Full-time / Part-time'],
                ['Marketing Manager','Marketing','Remote','Full-time'],
            ] as [$title, $dept, $location, $type])
            <div class="glass rounded-xl p-5 border border-white/5 flex flex-wrap items-center justify-between gap-3 hover:border-purple-500/30 transition">
                <div>
                    <h3 class="text-white font-medium">{{ $title }}</h3>
                    <div class="flex items-center space-x-3 mt-1">
                        <span class="text-xs text-gray-400">{{ $dept }}</span>
                        <span class="text-xs text-gray-600">·</span>
                        <span class="text-xs text-gray-400">{{ $location }}</span>
                        <span class="text-xs text-gray-600">·</span>
                        <span class="text-xs text-gray-400">{{ $type }}</span>
                    </div>
                </div>
                <a href="mailto:careers@beatmusic.com?subject=Application: {{ urlencode($title) }}"
                    class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-xl transition">
                    Apply Now
                </a>
            </div>
            @endforeach
        </div>

        {{-- No perfect fit? --}}
        <div class="mt-10 glass rounded-2xl p-8 border border-white/5 text-center">
            <h3 class="text-white font-semibold mb-2">Don't see your perfect role?</h3>
            <p class="text-gray-400 text-sm mb-4">We're always looking for talented people. Send us your CV and we'll reach out when the right opportunity arises.</p>
            <a href="mailto:careers@beatmusic.com" class="inline-block px-6 py-2.5 bg-white/10 hover:bg-white/15 text-white text-sm rounded-xl border border-white/10 transition">
                Send Open Application
            </a>
        </div>
    </div>
</section>
@endsection
