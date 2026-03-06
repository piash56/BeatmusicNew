@extends('layouts.public-clean')

@section('title', 'Pre-Save Success')

@section('content')
<section class="min-h-screen px-4 py-20 sm:px-6 lg:px-8">
    <div class="mx-auto flex max-w-xl items-center justify-center pt-16">
        <div class="w-full rounded-3xl border border-emerald-500/20 bg-slate-900/80 p-8 text-center shadow-2xl shadow-emerald-950/20">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-500/15 text-emerald-300">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 13 4 4L19 7"/></svg>
            </div>

            <h1 class="mt-6 text-3xl font-bold text-white">Pre-save completed</h1>
            <p class="mt-3 text-slate-400">
                @if($track)
                    {{ $track->title }} has been connected to your Spotify account.
                @else
                    Your Spotify pre-save was completed successfully.
                @endif
            </p>

            @if($track)
                <div class="mt-6 rounded-2xl border border-white/10 bg-slate-950/60 p-4 text-left">
                    <div class="flex items-center gap-4">
                        <img src="{{ $track->cover_art_url }}" alt="{{ $track->title }}" class="h-16 w-16 rounded-2xl object-cover">
                        <div class="min-w-0">
                            <p class="truncate font-semibold text-white">{{ $track->release_type === 'album' ? ($track->album_title ?: $track->title) : $track->title }}</p>
                            <p class="truncate text-sm text-slate-400">{{ $track->artists }}</p>
                            @if($track->release_date)
                                <p class="mt-1 text-xs text-cyan-300">Release date: {{ $track->release_date->format('n/j/Y') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
                @if($track)
                    <a href="{{ route('presave.show', $track->id) }}" class="rounded-2xl bg-cyan-500 px-5 py-3 font-semibold text-slate-950 transition hover:bg-cyan-400">
                        Back to pre-save page
                    </a>
                @endif
                <a href="{{ route('home') }}" class="rounded-2xl border border-white/10 px-5 py-3 font-semibold text-white transition hover:bg-white/5">
                    Back to website
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
