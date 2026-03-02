@extends('layouts.app')

@section('title', 'Sign In')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-20">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="w-14 h-14 bg-gradient-to-br from-purple-600 to-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M18 3a1 1 0 00-1.196-.98l-10 2A1 1 0 006 5v9.114A4.369 4.369 0 005 14c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V7.82l8-1.6v5.894A4.37 4.37 0 0015 12c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V3z"/></svg>
            </div>
            <h1 class="text-2xl font-bold text-white">Bentornato</h1>
            <p class="text-gray-400 mt-1">Accedi al tuo account Beat Music</p>
        </div>

        <div class="glass rounded-2xl p-8">
            @if($errors->any())
                <div class="bg-red-900/30 border border-red-500/30 text-red-300 rounded-lg p-3 mb-6 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST"
                  action="{{ route('login.post') }}"
                  class="space-y-5"
                  x-data="{ loading: false }"
                  @submit="loading = true">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Indirizzo e-mail</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full bg-white/5 border border-white/10 text-white placeholder-gray-500 px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition"
                        placeholder="you@example.com">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Password</label>
                    <div x-data="{ show: false }" class="relative">
                        <input :type="show ? 'text' : 'password'" name="password" required
                            class="w-full bg-white/5 border border-white/10 text-white placeholder-gray-500 px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 pr-12 transition"
                            placeholder="La tua password">
                        <button type="button" @click="show = !show"
                            class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-200 transition">
                            <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-white/20 bg-white/5 text-purple-600">
                        <span class="text-sm text-gray-400">Ricordati di me</span>
                    </label>
                    <a href="{{ route('forgot-password') }}" class="text-sm text-purple-400 hover:text-purple-300">Ha dimenticato la password?</a>
                </div>
                <button type="submit"
                        :disabled="loading"
                        class="w-full py-3 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold rounded-xl transition-all disabled:opacity-60 flex items-center justify-center gap-2">
                    <span x-show="loading" x-cloak class="inline-block w-4 h-4 border-2 border-white/40 border-t-white rounded-full animate-spin"></span>
                    <span x-text="loading ? 'Signing In...' : 'Sign In'"></span>
                </button>
            </form>
            
            {{-- <p class="text-center text-gray-400 text-sm mt-6">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-purple-400 hover:text-purple-300 font-medium">Sign Up</a>
            </p> --}}
        </div>
    </div>
</div>
@endsection
