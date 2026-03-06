@extends('layouts.public-clean')

@section('title', ($track->release_type === 'album' ? ($track->album_title ?: $track->title) : $track->title) . ' Pre-Save')

@section('content')
@php
    $releaseTitle = $track->release_type === 'album' ? ($track->album_title ?: $track->title) : $track->title;
    $spotifyDestination = route('presave.spotify', $track->id);
    $releaseDateLabel = $track->release_date ? $track->release_date->format('n/j/Y') : null;
@endphp

<section class="min-h-screen px-4 py-10 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-5xl">
        <div class="mb-8 text-center">
            <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl">
                Pre-save on Spotify
            </h1>
            <p class="mt-3 text-base text-slate-400 sm:text-lg">
                Get notified and save this release to your Spotify library.
            </p>
        </div>

        <div class="grid gap-8 lg:grid-cols-[minmax(0,340px)_minmax(0,360px)] lg:justify-center lg:items-start">
            <div class="mx-auto w-full max-w-[340px]">
                <div class="overflow-hidden rounded-3xl border border-cyan-500/10 bg-slate-900/70 shadow-2xl shadow-cyan-950/30">
                    <div class="aspect-square bg-slate-800">
                        <img src="{{ $track->cover_art_url }}" alt="{{ $releaseTitle }}" class="h-full w-full object-cover">
                    </div>
                </div>

                <div class="mt-5 text-center">
                    <h2 class="text-3xl font-extrabold text-white">{{ $releaseTitle }}</h2>
                    <p class="mt-2 text-xl text-slate-300">{{ $track->artists }}</p>

                    <div class="mt-4 flex flex-wrap items-center justify-center gap-3 text-sm text-slate-300">
                        <span class="rounded-full border border-cyan-500/20 bg-cyan-500/10 px-3 py-1 font-semibold text-cyan-300">
                            {{ ucfirst($track->release_type) }}
                        </span>
                        @if($track->primary_genre)
                            <span>{{ $track->primary_genre }}</span>
                        @endif
                        @if($releaseDateLabel)
                            <span>Releases {{ $releaseDateLabel }}</span>
                        @endif
                    </div>

                    <div class="mt-3 text-center">
                        <div class="text-2xl font-bold text-white">{{ number_format($preSaves) }}</div>
                        <div class="text-sm text-slate-500">Pre-saves</div>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <div class="rounded-3xl border border-cyan-500/20 bg-slate-900/80 p-6 shadow-xl shadow-cyan-950/20">
                    <h3 class="text-2xl font-bold text-white">
                        Pre-save this track
                    </h3>
                    <p class="mt-2 text-base text-slate-400">
                        Save this track to your library before it's released.
                    </p>

                    <div class="mt-6">
                        @if($alreadySaved)
                            <div class="rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-4 text-center">
                                <p class="font-semibold text-emerald-300">This release is already pre-saved.</p>
                                <p class="mt-1 text-sm text-emerald-200/80">It will be added to your Spotify library on release day.</p>
                            </div>
                        @else
                            <a href="{{ $spotifyDestination }}" target="_blank" rel="noopener noreferrer"
                                class="flex w-full items-center justify-center gap-3 rounded-2xl bg-green-500 px-5 py-4 text-black transition hover:bg-green-400">
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 0C5.372 0 0 5.372 0 12s5.372 12 12 12 12-5.372 12-12S18.628 0 12 0Zm5.493 17.296a.75.75 0 0 1-1.033.246c-2.83-1.729-6.39-2.12-10.583-1.164a.75.75 0 0 1-.334-1.462c4.586-1.047 8.51-.606 11.7 1.342a.75.75 0 0 1 .25 1.038Zm1.476-3.305a.937.937 0 0 1-1.29.307c-3.24-1.99-8.177-2.568-12.008-1.41a.937.937 0 0 1-.542-1.794c4.239-1.282 9.66-.642 13.53 1.734a.937.937 0 0 1 .31 1.163Zm.126-3.44c-3.882-2.307-10.289-2.52-13.995-1.4a1.125 1.125 0 1 1-.65-2.154c4.258-1.287 11.337-1.037 15.795 1.611a1.125 1.125 0 1 1-1.15 1.943Z"/></svg>
                                <span class="flex flex-col text-left leading-tight">
                                    <span class="text-base font-semibold">Pre-Save on Spotify</span>
                                    <span class="text-sm font-medium text-black/70">Connect with your Spotify account</span>
                                </span>
                            </a>
                        @endif
                    </div>

                    <p class="mt-4 text-sm text-slate-500">
                        By connecting, you agree to Spotify's terms of service.
                    </p>
                </div>

                <div class="rounded-3xl border border-white/10 bg-slate-900/70 p-6">
                    <h3 class="text-2xl font-bold text-white">Other Platforms</h3>
                    <p class="mt-6 text-center text-slate-500">More platforms coming soon</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
