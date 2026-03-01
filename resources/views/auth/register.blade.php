@extends('layouts.app')

@section('title', 'Sign Up')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-20">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="w-14 h-14 bg-gradient-to-br from-purple-600 to-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M18 3a1 1 0 00-1.196-.98l-10 2A1 1 0 006 5v9.114A4.369 4.369 0 005 14c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V7.82l8-1.6v5.894A4.37 4.37 0 0015 12c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V3z"/></svg>
            </div>
            <h1 class="text-2xl font-bold text-white">Create Your Account</h1>
            <p class="text-gray-400 mt-1">Start distributing your music for free</p>
        </div>

        <div class="glass rounded-2xl p-8">
            @if($errors->any())
                <div class="bg-red-900/30 border border-red-500/30 text-red-300 rounded-lg p-3 mb-6 text-sm space-y-1">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register.post') }}" class="space-y-5" x-data="{ passwordStrength: 0, password: '' }">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Full Name</label>
                    <input type="text" name="full_name" value="{{ old('full_name') }}" required
                        class="w-full bg-white/5 border border-white/10 text-white placeholder-gray-500 px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500 transition"
                        placeholder="Your full name">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full bg-white/5 border border-white/10 text-white placeholder-gray-500 px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500 transition"
                        placeholder="you@example.com">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Country</label>
                    <select name="country" required class="w-full bg-gray-800 border border-white/10 text-white px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500 transition">
                        <option value="">Select your country</option>
                        @php
                        $countries = ['Afghanistan','Albania','Algeria','Argentina','Australia','Austria','Belgium','Brazil','Canada','Chile','China','Colombia','Croatia','Czech Republic','Denmark','Egypt','Ethiopia','Finland','France','Germany','Ghana','Greece','Hungary','India','Indonesia','Iran','Iraq','Ireland','Israel','Italy','Japan','Jordan','Kenya','Kuwait','Lebanon','Libya','Malaysia','Mexico','Morocco','Netherlands','New Zealand','Nigeria','Norway','Pakistan','Peru','Philippines','Poland','Portugal','Qatar','Romania','Russia','Saudi Arabia','Senegal','Singapore','South Africa','South Korea','Spain','Sudan','Sweden','Switzerland','Syria','Taiwan','Tanzania','Thailand','Tunisia','Turkey','UAE','Uganda','Ukraine','United Kingdom','United States','Uruguay','Venezuela','Vietnam','Yemen','Zimbabwe'];
                        @endphp
                        @foreach($countries as $country)
                            <option value="{{ $country }}" {{ old('country') == $country ? 'selected' : '' }}>{{ $country }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Password</label>
                    <div x-data="{ show: false }" class="relative">
                        <input :type="show ? 'text' : 'password'" name="password" x-model="password"
                            @input="passwordStrength = password.length >= 12 ? 3 : password.length >= 8 ? 2 : password.length >= 6 ? 1 : 0"
                            required minlength="8"
                            class="w-full bg-white/5 border border-white/10 text-white placeholder-gray-500 px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500 pr-12 transition"
                            placeholder="Min 8 characters">
                        <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                    <!-- Strength Indicator -->
                    <div class="flex space-x-1 mt-2">
                        <div :class="passwordStrength >= 1 ? 'bg-red-500' : 'bg-gray-700'" class="h-1 flex-1 rounded-full transition-colors"></div>
                        <div :class="passwordStrength >= 2 ? 'bg-yellow-500' : 'bg-gray-700'" class="h-1 flex-1 rounded-full transition-colors"></div>
                        <div :class="passwordStrength >= 3 ? 'bg-green-500' : 'bg-gray-700'" class="h-1 flex-1 rounded-full transition-colors"></div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Confirm Password</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full bg-white/5 border border-white/10 text-white placeholder-gray-500 px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500 transition"
                        placeholder="Repeat password">
                </div>
                <button type="submit" class="w-full py-3 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold rounded-xl transition-all">
                    Create Account
                </button>

                <p class="text-center text-xs text-gray-500">
                    By signing up, you agree to our <a href="{{ route('terms') }}" class="text-purple-400">Terms of Service</a> and <a href="{{ route('privacy') }}" class="text-purple-400">Privacy Policy</a>.
                </p>
            </form>

            <p class="text-center text-gray-400 text-sm mt-6">
                Already have an account?
                <a href="{{ route('login') }}" class="text-purple-400 hover:text-purple-300 font-medium">Sign In</a>
            </p>
        </div>
    </div>
</div>
@endsection
