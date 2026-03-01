@extends('layouts.app')
@section('title','Admin Password Reset')
@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-20">
<div class="w-full max-w-md">
<div class="glass rounded-2xl p-8">
<h1 class="text-xl font-bold text-white mb-2">Set New Password</h1>
<p class="text-gray-400 text-sm mb-6">Enter your new admin password below.</p>
@if($errors->any())<div class="bg-red-900/30 border border-red-500/30 text-red-300 rounded-lg p-3 mb-4 text-sm">{{ $errors->first() }}</div>@endif
<form method="POST" action="{{ route('admin.reset-password.post') }}" class="space-y-4">
@csrf
<input type="hidden" name="token" value="{{ $token ?? request('token') }}">
<input type="hidden" name="email" value="{{ $email ?? request('email') }}">
<div>
    <label class="text-sm text-gray-400 mb-1.5 block">New Password</label>
    <input type="password" name="password" required class="w-full bg-white/5 border border-white/10 text-white px-4 py-3 rounded-xl focus:outline-none focus:border-red-500" placeholder="Min 8 characters">
</div>
<div>
    <label class="text-sm text-gray-400 mb-1.5 block">Confirm Password</label>
    <input type="password" name="password_confirmation" required class="w-full bg-white/5 border border-white/10 text-white px-4 py-3 rounded-xl focus:outline-none focus:border-red-500" placeholder="Repeat password">
</div>
<button type="submit" class="w-full py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition">Reset Password</button>
</form>
<p class="text-center text-gray-400 text-sm mt-4"><a href="{{ route('admin.login') }}" class="text-red-400 hover:text-red-300">← Back to Admin Login</a></p>
</div></div></div>
@endsection
