@extends('layouts.dashboard')

@section('title', 'Not Eligible')
@section('page-title', 'Feature Not Available')

@section('content')
<div class="flex items-center justify-center min-h-[60vh]">
    <div class="text-center max-w-md">
        <div class="w-20 h-20 bg-yellow-600/20 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-white mb-3">Plan Upgrade Required</h2>
        <p class="text-gray-400 mb-8">This feature requires a higher subscription plan. Upgrade your plan to access all premium features including radio promotion, concert live slots, Vevo, and more.</p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('dashboard.billing') }}" class="px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold rounded-xl transition">
                Upgrade Plan
            </a>
            <a href="{{ route('dashboard.home') }}" class="px-6 py-3 bg-white/5 hover:bg-white/10 text-gray-300 font-semibold rounded-xl border border-white/10 transition">
                Back to Dashboard
            </a>
        </div>

        <div class="mt-10 grid grid-cols-1 gap-3 text-left">
            @foreach([
                ['icon' => '📻', 'name' => 'Radio Promotion', 'desc' => 'Get your music on top radio networks'],
                ['icon' => '🎤', 'name' => 'Concert Live', 'desc' => 'Book live performance slots'],
                ['icon' => '▶️', 'name' => 'Vevo Account', 'desc' => 'Get your official Vevo channel'],
                ['icon' => '📋', 'name' => 'Editorial Playlists', 'desc' => 'Submit to curated playlists'],
            ] as $feature)
            <div class="flex items-center space-x-3 p-3 bg-white/3 rounded-xl border border-white/5">
                <span class="text-2xl">{{ $feature['icon'] }}</span>
                <div>
                    <p class="text-white text-sm font-medium">{{ $feature['name'] }}</p>
                    <p class="text-gray-500 text-xs">{{ $feature['desc'] }}</p>
                </div>
                <div class="ml-auto">
                    <span class="text-xs bg-purple-600/20 text-purple-400 px-2 py-1 rounded-full">Premium+</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
