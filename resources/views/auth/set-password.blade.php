@extends('layouts.app')
@section('title', 'Set Your Password')
@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-20">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-white">Set Your Password</h1>
            <p class="text-gray-400 mt-1">Create a password for your Beat Music artist account</p>
        </div>
        <div class="glass rounded-2xl p-8">
            @if($errors->any())
                <div class="bg-red-900/30 border border-red-500/30 text-red-300 rounded-lg p-3 mb-6 text-sm">{{ $errors->first() }}</div>
            @endif
            <form method="POST"
                  action="{{ route('set-password.post') }}"
                  class="space-y-5"
                  x-data="{ passwordStrength: 0, password: '', loading: false }"
                  @submit="loading = true">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">New Password</label>
                    <div x-data="{ show: false }" class="relative">
                        <input :type="show ? 'text' : 'password'" name="password" x-model="password"
                            @input="passwordStrength = password.length >= 12 ? 3 : password.length >= 8 ? 2 : password.length >= 6 ? 1 : 0"
                            required minlength="8"
                            class="w-full bg-white/5 border border-white/10 text-white placeholder-gray-500 px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500 pr-12 transition"
                            placeholder="Min 8 characters">
                        <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-200" tabindex="-1" aria-label="Toggle password visibility">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                    <div class="flex space-x-1 mt-2">
                        <div :class="passwordStrength >= 1 ? 'bg-red-500' : 'bg-gray-700'" class="h-1 flex-1 rounded-full transition-colors"></div>
                        <div :class="passwordStrength >= 2 ? 'bg-yellow-500' : 'bg-gray-700'" class="h-1 flex-1 rounded-full transition-colors"></div>
                        <div :class="passwordStrength >= 3 ? 'bg-green-500' : 'bg-gray-700'" class="h-1 flex-1 rounded-full transition-colors"></div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Confirm Password</label>
                    <div x-data="{ showConfirm: false }" class="relative">
                        <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation" required
                            class="w-full bg-white/5 border border-white/10 text-white placeholder-gray-500 px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500 pr-12 transition"
                            placeholder="Repeat password">
                        <button type="button" @click="showConfirm = !showConfirm" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-200" tabindex="-1" aria-label="Toggle password visibility">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                </div>
                <button type="submit"
                        :disabled="loading"
                        class="w-full py-3 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold rounded-xl transition disabled:opacity-60 flex items-center justify-center gap-2">
                    <span x-show="loading" x-cloak class="inline-block w-4 h-4 border-2 border-white/40 border-t-white rounded-full animate-spin"></span>
                    <span x-text="loading ? 'Setting Password...' : 'Set Password'"></span>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
