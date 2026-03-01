@extends('layouts.app')
@section('title', 'Reset Password')
@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-20">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-white">Reset Password</h1>
            <p class="text-gray-400 mt-1">Create a new password for your account</p>
        </div>
        <div class="glass rounded-2xl p-8">
            @if($errors->any())
                <div class="bg-red-900/30 border border-red-500/30 text-red-300 rounded-lg p-3 mb-6 text-sm">{{ $errors->first() }}</div>
            @endif
            <form method="POST" action="{{ route('reset-password.post') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">New Password</label>
                    <input type="password" name="password" required minlength="8" class="w-full bg-white/5 border border-white/10 text-white placeholder-gray-500 px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500 transition" placeholder="Min 8 characters">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Confirm Password</label>
                    <input type="password" name="password_confirmation" required class="w-full bg-white/5 border border-white/10 text-white placeholder-gray-500 px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500 transition" placeholder="Repeat password">
                </div>
                <button type="submit" class="w-full py-3 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold rounded-xl transition">Reset Password</button>
            </form>
        </div>
    </div>
</div>
@endsection
