@extends('layouts.app')

@section('title', 'Knowledge Base — Beat Music')

@section('content')
<section class="pt-32 pb-16 px-4">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-white mb-4">Base di conoscenza</h1>
            <p class="text-gray-400">Sfoglia tutti gli articoli della guida e le guide</p>
        </div>

        {{-- Search & Filter --}}
        <form method="GET" class="flex flex-wrap gap-3 mb-8">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search articles..."
                class="flex-1 min-w-48 bg-white/5 border border-white/10 text-white px-4 py-2.5 rounded-xl text-sm focus:outline-none focus:border-purple-500">
            <select name="category" class="bg-white/5 border border-white/10 text-white px-4 py-2.5 rounded-xl text-sm focus:outline-none">
                <option value="">Tutte le categorie</option>
                @foreach(['getting-started'=>'Getting Started','distribution'=>'Distribution','payments'=>'Payments & Royalties','promotion'=>'Radio & Promotion','vevo'=>'Vevo & Video','account'=>'Account & Settings'] as $val => $label)
                    <option value="{{ $val }}" {{ request('category')==$val?'selected':'' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2.5 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-xl transition">Ricerca</button>
        </form>

        {{-- Articles --}}
        @if(isset($articles) && $articles->count())
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($articles as $article)
            <a href="{{ route('knowledge-base.article', $article->id) }}" class="glass rounded-xl p-5 border border-white/5 hover:border-purple-500/30 transition group">
                @if($article->category)
                <span class="text-xs px-2 py-0.5 bg-purple-600/20 text-purple-300 rounded-full border border-purple-500/20 mb-2 inline-block">{{ ucfirst(str_replace('-',' ',$article->category)) }}</span>
                @endif
                <h3 class="text-white font-semibold group-hover:text-purple-300 transition mb-1">{{ $article->title }}</h3>
                @if($article->excerpt)
                <p class="text-gray-400 text-sm line-clamp-2">{{ $article->excerpt }}</p>
                @endif
            </a>
            @endforeach
        </div>
        <div class="mt-6">{{ $articles->withQueryString()->links() }}</div>
        @else
        <div class="text-center py-16 glass rounded-2xl border border-white/5">
            <p class="text-gray-400">Nessun articolo trovato. Prova una ricerca diversa.</p>
        </div>
        @endif
    </div>
</section>
@endsection
