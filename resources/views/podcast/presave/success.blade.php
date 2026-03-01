@extends('layouts.app')

@section('title', 'Pre-Save Successful — Beat Music')

@section('content')
<section class="min-h-screen flex items-center justify-center px-4 py-20">
    <div class="max-w-md w-full text-center">
        <div class="glass rounded-3xl p-10 border border-green-500/20">
            {{-- Success animation --}}
            <div class="text-7xl mb-6 animate-bounce">🎉</div>

            <h1 class="text-3xl font-bold text-white mb-3">You're In!</h1>
            <p class="text-gray-300 text-lg mb-2">
                @if(isset($track))
                    <strong class="text-white">{{ $track->title }}</strong> has been pre-saved to your Spotify library.
                @else
                    This release has been pre-saved to your Spotify library.
                @endif
            </p>
            <p class="text-gray-400 text-sm mb-8">
                It will automatically appear in your Spotify library and Saved Songs on release day.
                You'll also get a notification when it's live.
            </p>

            {{-- Track info --}}
            @if(isset($track))
            <div class="flex items-center space-x-4 glass rounded-2xl p-4 border border-white/5 mb-8 text-left">
                @if(!empty($track->cover_art))
                    <img src="{{ route('files.cover', $track->id) }}" class="w-14 h-14 rounded-xl object-cover flex-shrink-0">
                @else
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-purple-600 to-indigo-600 flex items-center justify-center text-2xl flex-shrink-0">🎵</div>
                @endif
                <div>
                    <p class="text-white font-medium">{{ $track->title }}</p>
                    <p class="text-gray-400 text-sm">{{ $track->artist_name }}</p>
                    @if($track->release_date)
                    <p class="text-purple-400 text-xs">{{ \Carbon\Carbon::parse($track->release_date)->format('F j, Y') }}</p>
                    @endif
                </div>
            </div>
            @endif

            {{-- Share --}}
            <div class="mb-6">
                <p class="text-gray-400 text-sm mb-3">Share with your friends</p>
                <div class="flex justify-center space-x-3">
                    @if(isset($track))
                    @php $url = route('presave.show', $track->id); $text = urlencode('Just pre-saved ' . $track->title . '! Join me 🎵'); @endphp
                    <a href="https://twitter.com/intent/tweet?text={{ $text }}&url={{ urlencode($url) }}" target="_blank"
                        class="flex items-center space-x-2 px-4 py-2 bg-white/5 hover:bg-white/10 border border-white/10 text-white rounded-xl text-sm transition">
                        <span>Share</span>
                    </a>
                    @endif
                    <button onclick="history.back()" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-xl transition">
                        View Release
                    </button>
                </div>
            </div>

            <a href="{{ route('home') }}" class="text-gray-400 hover:text-white text-sm transition">← Back to Beat Music</a>
        </div>
    </div>
</section>
@endsection
