@extends('layouts.app')

@section('title', ($track->title ?? 'Pre-Save') . ' — Beat Music')

@section('content')
<section class="pt-32 pb-24 px-4">
    <div class="max-w-xl mx-auto text-center">

        {{-- Cover Art --}}
        <div class="mb-8">
            @if(!empty($track->cover_art))
                <img src="{{ route('files.cover', $track->id) }}" alt="{{ $track->title }}"
                    class="w-48 h-48 sm:w-64 sm:h-64 rounded-3xl object-cover mx-auto shadow-2xl shadow-purple-900/50">
            @else
                <div class="w-48 h-48 sm:w-64 sm:h-64 rounded-3xl bg-gradient-to-br from-purple-900 to-indigo-900 flex items-center justify-center mx-auto text-6xl">
                    🎵
                </div>
            @endif
        </div>

        {{-- Release Info --}}
        <div class="mb-2">
            <span class="text-xs px-3 py-1 bg-purple-600/20 text-purple-300 rounded-full border border-purple-500/20">
                {{ $track->type === 'album' ? 'Album' : 'Single' }} · Coming Soon
            </span>
        </div>
        <h1 class="text-3xl sm:text-4xl font-bold text-white mt-4 mb-2">{{ $track->title }}</h1>
        <p class="text-xl text-gray-400 mb-1">{{ $track->artist_name }}</p>
        @if($track->release_date)
        <p class="text-sm text-gray-500 mb-8">
            Releases {{ \Carbon\Carbon::parse($track->release_date)->format('F j, Y') }}
        </p>
        @endif

        {{-- Countdown --}}
        @if(!empty($track->release_date))
        <div class="glass rounded-2xl p-6 border border-white/5 mb-8" x-data="countdown('{{ $track->release_date }}')" x-init="start()">
            <p class="text-gray-400 text-xs font-medium uppercase tracking-wider mb-4">Drops In</p>
            <div class="flex justify-center space-x-4">
                @foreach(['days' => 'Days', 'hours' => 'Hours', 'minutes' => 'Min', 'seconds' => 'Sec'] as $unit => $label)
                <div class="text-center">
                    <div class="text-3xl font-bold text-white" x-text="{{ $unit }}">00</div>
                    <div class="text-gray-400 text-xs mt-1">{{ $label }}</div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Pre-save Button --}}
        @if(!isset($alreadySaved) || !$alreadySaved)
        <div class="space-y-3">
            <a href="{{ route('presave.spotify', $track->id) }}"
                class="flex items-center justify-center space-x-3 w-full py-4 bg-green-500 hover:bg-green-600 text-black font-semibold rounded-2xl transition text-lg">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"/></svg>
                <span>Pre-Save on Spotify</span>
            </a>
            <p class="text-gray-500 text-xs">Pre-saving is free. Your Spotify library will automatically save this release on its release date.</p>
        </div>
        @else
        <div class="glass rounded-2xl p-6 border border-green-500/30 bg-green-900/10 text-center">
            <div class="text-4xl mb-2">✅</div>
            <p class="text-green-300 font-semibold">You've Pre-Saved This Release!</p>
            <p class="text-gray-400 text-sm mt-1">It will automatically appear in your Spotify library on release day.</p>
        </div>
        @endif

        {{-- Share --}}
        <div class="mt-8 glass rounded-2xl p-5 border border-white/5">
            <p class="text-gray-400 text-sm mb-3">Share with your friends</p>
            <div class="flex justify-center space-x-3">
                @php $url = url()->current(); $text = urlencode('Listen to ' . $track->title . ' by ' . $track->artist_name . ' — pre-save now!'); @endphp
                <a href="https://twitter.com/intent/tweet?text={{ $text }}&url={{ urlencode($url) }}" target="_blank"
                    class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-white text-sm">𝕏</a>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($url) }}" target="_blank"
                    class="w-10 h-10 flex items-center justify-center rounded-xl bg-blue-600/20 hover:bg-blue-600/30 border border-blue-500/20 transition text-blue-300 text-xs font-bold">f</a>
                <button onclick="navigator.clipboard.writeText('{{ $url }}').then(()=>alert('Link copied!'))"
                    class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                    <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </button>
            </div>
        </div>
    </div>
</section>

<script>
function countdown(releaseDate) {
    return {
        days: '00', hours: '00', minutes: '00', seconds: '00',
        start() {
            const update = () => {
                const now = new Date().getTime();
                const target = new Date(releaseDate).getTime();
                const diff = target - now;
                if (diff <= 0) { this.days = this.hours = this.minutes = this.seconds = '00'; return; }
                this.days = String(Math.floor(diff / 86400000)).padStart(2,'0');
                this.hours = String(Math.floor((diff % 86400000) / 3600000)).padStart(2,'0');
                this.minutes = String(Math.floor((diff % 3600000) / 60000)).padStart(2,'0');
                this.seconds = String(Math.floor((diff % 60000) / 1000)).padStart(2,'0');
            };
            update();
            setInterval(update, 1000);
        }
    };
}
</script>
@endsection
