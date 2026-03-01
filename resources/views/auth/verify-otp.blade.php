@extends('layouts.app')

@section('title', 'Verify Email')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-20">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-gradient-to-br from-purple-600 to-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <h1 class="text-2xl font-bold text-white">Verify Your Email</h1>
            <p class="text-gray-400 mt-2">We sent a 6-digit code to<br><span class="text-purple-400 font-medium">{{ $user->email }}</span></p>
        </div>

        <div class="glass rounded-2xl p-8">
            @if($errors->any())
                <div class="bg-red-900/30 border border-red-500/30 text-red-300 rounded-lg p-3 mb-6 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif
            @if(session('success'))
                <div class="bg-green-900/30 border border-green-500/30 text-green-300 rounded-lg p-3 mb-6 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('verify-otp.post') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="user_id" value="{{ $user->id }}">

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-3 text-center">Enter your 6-digit code</label>
                    <input type="text" name="otp" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" autofocus required
                        class="w-full bg-white/5 border border-white/10 text-white text-center text-3xl tracking-widest font-bold px-4 py-4 rounded-xl focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition"
                        placeholder="000000">
                    <p class="text-xs text-gray-500 text-center mt-2">Code expires in 5 minutes</p>
                </div>

                <button type="submit" class="w-full py-3 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold rounded-xl transition-all">
                    Verify & Continue
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-gray-400 text-sm">Didn't receive the code?</p>
                <form method="POST" action="{{ route('resend-otp') }}" class="mt-2">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                    <button type="submit" class="text-purple-400 hover:text-purple-300 text-sm font-medium">
                        Resend OTP
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
