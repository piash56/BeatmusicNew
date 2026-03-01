@extends('layouts.app')
@section('title', 'Forgot Password')
@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-20">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-white">Forgot Password</h1>
            <p class="text-gray-400 mt-1">Enter your email to receive a reset link</p>
        </div>
        <div class="glass rounded-2xl p-8">
            @if(session('success'))
                <div class="bg-green-900/30 border border-green-500/30 text-green-300 rounded-lg p-3 mb-6 text-sm">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="bg-red-900/30 border border-red-500/30 text-red-300 rounded-lg p-3 mb-6 text-sm">{{ $errors->first() }}</div>
            @endif
            <form method="POST"
                  action="{{ route('forgot-password.post') }}"
                  class="space-y-5"
                  x-data="{ loading: false }"
                  @submit="loading = true">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Email Address</label>
                    <input type="email" name="email" required class="w-full bg-white/5 border border-white/10 text-white placeholder-gray-500 px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500 transition" placeholder="you@example.com">
                </div>
                <button type="submit"
                        :disabled="loading"
                        class="w-full py-3 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold rounded-xl transition disabled:opacity-60 flex items-center justify-center gap-2">
                    <span x-show="loading" x-cloak class="inline-block w-4 h-4 border-2 border-white/40 border-t-white rounded-full animate-spin"></span>
                    <span x-text="loading ? 'Sending...' : 'Send Reset Link'"></span>
                </button>
            </form>
            <p class="text-center text-gray-400 text-sm mt-6"><a href="{{ route('login') }}" class="text-purple-400 hover:text-purple-300">← Back to Sign In</a></p>
        </div>
    </div>
</div>
@endsection
