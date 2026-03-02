@extends('layouts.app')

@section('title', ($article->title ?? 'Article') . ' — Beat Music')

@section('content')
<section class="pt-32 pb-24 px-4">
    <div class="max-w-3xl mx-auto">
        <a href="{{ route('knowledge-base') }}" class="flex items-center space-x-2 text-gray-400 hover:text-white transition text-sm mb-8">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            <span>Torna alla base di conoscenza</span>
        </a>

        @if(isset($article->category))
        <span class="text-xs px-3 py-1 bg-purple-600/20 text-purple-300 rounded-full border border-purple-500/20 mb-4 inline-block">{{ ucfirst(str_replace('-',' ',$article->category)) }}</span>
        @endif

        <h1 class="text-3xl sm:text-4xl font-bold text-white mb-4">{{ $article->title }}</h1>
        <p class="text-gray-400 text-sm mb-8">Ultimo aggiornamento {{ $article->updated_at->format('M d, Y') }}</p>

        <div class="glass rounded-2xl p-8 border border-white/5 prose prose-invert prose-sm max-w-none
            prose-headings:text-white prose-p:text-gray-300 prose-a:text-purple-400 prose-strong:text-white
            prose-code:text-purple-300 prose-pre:bg-gray-800 prose-blockquote:border-purple-500 prose-blockquote:text-gray-400">
            {!! nl2br(e($article->content)) !!}
        </div>

        {{-- Tags --}}
        @if(!empty($article->tags))
        <div class="flex flex-wrap gap-2 mt-6">
            @foreach(is_array($article->tags) ? $article->tags : explode(',', $article->tags) as $tag)
            <span class="text-xs px-2 py-0.5 bg-white/5 text-gray-400 rounded-full border border-white/10">{{ trim($tag) }}</span>
            @endforeach
        </div>
        @endif

        {{-- Was this helpful? --}}
        <div class="mt-10 glass rounded-2xl p-6 border border-white/5 text-center">
            <p class="text-white font-medium mb-3">Questo articolo ti è stato utile?</p>
            <div class="flex justify-center gap-4">
                <button class="px-5 py-2 bg-green-600/20 hover:bg-green-600/30 text-green-300 text-sm rounded-xl border border-green-500/20 transition">👍 SÌ</button>
                <button class="px-5 py-2 bg-red-600/20 hover:bg-red-600/30 text-red-300 text-sm rounded-xl border border-red-500/20 transition">👎 No</button>
            </div>
        </div>

        {{-- Related --}}
        @if(!empty($relatedArticles) && $relatedArticles->count())
        <div class="mt-10">
            <h3 class="text-white font-semibold mb-4">Articoli correlati</h3>
            <div class="space-y-2">
                @foreach($relatedArticles as $rel)
                <a href="{{ route('knowledge-base.article', $rel->id) }}" class="flex items-center justify-between glass rounded-xl px-4 py-3 border border-white/5 hover:border-purple-500/30 transition group">
                    <span class="text-gray-300 group-hover:text-white text-sm">{{ $rel->title }}</span>
                    <svg class="w-4 h-4 text-gray-500 group-hover:text-purple-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>
@endsection
