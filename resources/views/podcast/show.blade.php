@extends('layouts.app')

@section('title', ($promotion->track->title ?? 'Podcast') . ' — Beat Music')

@section('content')
@php
    $track = $promotion->track;
    $coverUrl = $track && $track->cover_art ? asset('storage/' . \App\Models\Track::normalizeStoragePath($track->cover_art)) : null;
    $audioUrl = null;
    $trackTitle = $track?->title ?? 'Track';

    if ($track && $track->release_type === 'album' && $promotion->track_index !== null) {
        $albumTrack = $track->album_tracks[$promotion->track_index] ?? null;
        $trackTitle = $albumTrack['title'] ?? ($trackTitle . ' (Track ' . ($promotion->track_index + 1) . ')');
        if ($albumTrack && isset($albumTrack['audio_file'])) {
            $audioUrl = route('files.album-track', [$track->id, $promotion->track_index]);
        }
    } else {
        if ($track && $track->audio_file) {
            $audioUrl = route('files.audio', $track->id);
        }
    }
@endphp

<section class="pt-32 pb-24 px-4">
    <div class="max-w-4xl mx-auto">
        <div class="flex flex-col sm:flex-row gap-6 mb-8">
            <div class="flex-shrink-0">
                @if($coverUrl)
                    <img src="{{ $coverUrl }}" alt="{{ $trackTitle }}" class="w-40 h-40 sm:w-48 sm:h-48 rounded-2xl object-cover shadow-xl">
                @else
                    <div class="w-40 h-40 sm:w-48 sm:h-48 rounded-2xl bg-gradient-to-br from-purple-900 to-indigo-900 flex items-center justify-center text-5xl">🎙️</div>
                @endif
            </div>

            <div class="flex-1">
                <span class="text-xs px-2 py-0.5 bg-purple-600/20 text-purple-300 rounded-full border border-purple-500/20 mb-2 inline-block">Podcast</span>
                <h1 class="text-2xl sm:text-3xl font-bold text-white mb-2">{{ $trackTitle }}</h1>
                <p class="text-gray-400 mb-3">{{ $track->artists ?? '' }}</p>
                <p class="text-gray-500 text-sm">Network: <span class="text-gray-300">{{ $promotion->radioNetwork->name ?? '—' }}</span></p>

                <div class="flex flex-wrap items-center gap-3 mt-4">
                    <button type="button" id="likeBtn" class="flex items-center gap-2 text-xs text-gray-300 hover:text-red-300 transition">
                        <svg id="likeIcon" class="w-4 h-4" fill="{{ $isLiked ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        <span id="likeCount">{{ number_format($promotion->likes ?? 0) }}</span>
                    </button>
                </div>
            </div>
        </div>

        @if($audioUrl)
        <div class="glass rounded-2xl p-6 border border-white/5 mb-8">
            <audio controls class="w-full" style="accent-color: #7c3aed;">
                <source src="{{ $audioUrl }}" type="audio/mpeg">
                Your browser does not support audio playback.
            </audio>
        </div>
        @endif
    </div>
</section>

<script>
(() => {
  const btn = document.getElementById('likeBtn');
  const icon = document.getElementById('likeIcon');
  const countEl = document.getElementById('likeCount');
  if (!btn || !icon || !countEl) return;

  function getGuestUuid() {
    const key = 'beatmusic_guest_uuid';
    let id = localStorage.getItem(key);
    if (!id) {
      id = (crypto && crypto.randomUUID) ? crypto.randomUUID() : (Date.now().toString(36) + Math.random().toString(36).slice(2));
      localStorage.setItem(key, id);
    }
    return id;
  }

  btn.addEventListener('click', async () => {
    try {
      const res = await fetch('{{ route('podcast.like', $promotion->id) }}', {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || ''
        },
        body: JSON.stringify({ guest_uuid: getGuestUuid() })
      });
      const data = await res.json();
      if (data && typeof data.likes !== 'undefined') {
        countEl.textContent = String(data.likes);
        icon.setAttribute('fill', data.liked ? 'currentColor' : 'none');
      }
    } catch (e) {}
  });
})();
</script>
@endsection
