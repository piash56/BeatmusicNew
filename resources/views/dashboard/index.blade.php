@extends('layouts.dashboard')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Welcome back, ' . auth()->user()->full_name . '!')

@section('content')
<div class="space-y-6">

    <!-- Account Type Card -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 glass rounded-2xl p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center space-x-2 mb-2">
                        <span class="text-2xl">{{ $user->is_company ? '🏢' : '👤' }}</span>
                        <h3 class="text-xl font-bold text-white">Account Type</h3>
                    </div>
                    <p class="text-gray-400 text-sm">{{ $user->is_company ? 'Company' : 'Individual' }}</p>
                </div>
                <div class="flex flex-col space-y-2">
                    <a href="{{ route('dashboard.support') }}" class="px-6 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white text-sm font-semibold rounded-xl transition text-center">Upgrade Plan</a>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="glass rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4">Quick Stats</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-400 text-sm">Total Streams</span>
                    <span class="text-white font-semibold">{{ number_format($stats['total_streams']) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400 text-sm">Released Tracks</span>
                    <span class="text-white font-semibold">{{ $stats['released_tracks'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400 text-sm">Pending Review</span>
                    <span class="text-white font-semibold">{{ $stats['pending_review'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400 text-sm">Published Playlists</span>
                    <span class="text-white font-semibold">{{ $stats['playlist_published'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
        $statCards = [
            ['label' => 'Total Streams', 'value' => number_format($stats['total_streams']), 'icon' => '📊', 'color' => 'purple'],
            ['label' => 'Last 30 Days', 'value' => number_format($user->stats_last_month ?? 0), 'icon' => '📈', 'color' => 'green'],
            ['label' => 'Total Tracks', 'value' => $stats['total_tracks'], 'icon' => '🎵', 'color' => 'blue'],
            ['label' => 'Balance', 'value' => '$' . number_format($user->balance ?? 0, 2), 'icon' => '💰', 'color' => 'yellow'],
        ];
        @endphp
        @foreach($statCards as $card)
        <div class="glass rounded-xl p-5">
            <div class="text-2xl mb-2">{{ $card['icon'] }}</div>
            <div class="text-xl font-bold text-white">{{ $card['value'] }}</div>
            <div class="text-sm text-gray-400 mt-0.5">{{ $card['label'] }}</div>
        </div>
        @endforeach
    </div>

    <!-- Recent Releases + Upload CTA -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Releases -->
        <div class="lg:col-span-2 glass rounded-2xl overflow-hidden">
            <div class="p-6 border-b border-white/5 flex items-center justify-between">
                <h3 class="font-semibold text-white">Recent Releases</h3>
                <a href="{{ route('dashboard.releases.index') }}" class="text-purple-400 hover:text-purple-300 text-sm">View all →</a>
            </div>
            <div class="divide-y divide-white/5">
                @forelse($recentReleases as $track)
                <div class="flex items-center space-x-4 p-4 hover:bg-white/3 transition">
                    @if($track->cover_art)
                        <img src="{{ $track->cover_art_url }}" class="w-12 h-12 rounded-lg object-cover">
                    @else
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-600/30 to-indigo-600/30 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-400" fill="currentColor" viewBox="0 0 20 20"><path d="M18 3a1 1 0 00-1.196-.98l-10 2A1 1 0 006 5v9.114A4.369 4.369 0 005 14c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V7.82l8-1.6v5.894A4.37 4.37 0 0015 12c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V3z"/></svg>
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="text-white text-sm font-medium truncate">{{ $track->title }}</p>
                        <p class="text-gray-400 text-xs">{{ $track->artists }} • {{ ucfirst($track->release_type) }}</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="text-xs px-2 py-1 rounded-full font-medium
                            {{ $track->status === 'Released' ? 'bg-green-900/50 text-green-400' :
                               ($track->status === 'On Process' ? 'bg-yellow-900/50 text-yellow-400' :
                               ($track->status === 'Rejected' ? 'bg-red-900/50 text-red-400' : 'bg-blue-900/50 text-blue-400')) }}">
                            {{ $track->status }}
                        </span>
                        <a href="{{ route('dashboard.releases.show', $track->id) }}" class="text-gray-400 hover:text-white">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center">
                    <div class="text-4xl mb-3">🎵</div>
                    <p class="text-gray-400 text-sm">No releases yet.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Upload CTA -->
        <div class="space-y-4">
            <div class="glass rounded-2xl p-6 bg-gradient-to-br from-purple-600/10 to-indigo-600/10 border border-purple-500/20">
                <div class="text-3xl mb-3">🚀</div>
                <h3 class="font-semibold text-white mb-2">Upload New Release</h3>
                <p class="text-gray-400 text-sm mb-4">Share your music with the world. Upload your single or album today.</p>
                <a href="{{ route('dashboard.releases.create') }}?new=1" class="block w-full text-center py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white text-sm font-semibold rounded-xl transition">
                    Upload Release
                </a>
            </div>

            <div class="glass rounded-2xl p-6">
                <h3 class="font-semibold text-white mb-3 text-sm">Quick Links</h3>
                <div class="space-y-2">
                    @foreach([
                        ['label' => 'Analytics', 'route' => 'dashboard.streams', 'icon' => '📊'],
                        ['label' => 'Support', 'route' => 'dashboard.support', 'icon' => '🎧'],
                        ['label' => 'Revenue', 'route' => 'dashboard.revenue', 'icon' => '💰'],
                    ] as $link)
                    <a href="{{ route($link['route']) }}" class="flex items-center space-x-2 py-2 text-gray-400 hover:text-white text-sm transition">
                        <span>{{ $link['icon'] }}</span>
                        <span>{{ $link['label'] }}</span>
                        <svg class="w-3 h-3 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
