@extends('layouts.dashboard')

@section('title', 'Profile')
@section('page-title', 'My Profile')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    @if($errors->any())
    <div class="bg-red-900/30 border border-red-500/30 text-red-400 p-4 rounded-xl text-sm">
        <ul class="space-y-1 list-disc list-inside">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <!-- Profile Picture -->
    <div class="bg-gray-900 rounded-2xl border border-white/5 p-6">
        <h3 class="font-semibold text-white mb-4">Profile Picture</h3>
        <div class="flex items-center space-x-5">
            @if(auth()->user()->profile_picture)
                <img src="{{ asset('storage/'.auth()->user()->profile_picture) }}" class="w-20 h-20 rounded-full object-cover">
            @else
                <div class="w-20 h-20 bg-gradient-to-br from-purple-600 to-indigo-600 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                    {{ strtoupper(substr(auth()->user()->full_name, 0, 2)) }}
                </div>
            @endif
            <div>
                <form method="POST" action="{{ route('dashboard.profile.picture') }}" enctype="multipart/form-data">
                    @csrf
                    <label class="cursor-pointer">
                        <input type="file" name="profile_picture" accept="image/*" class="hidden" onchange="this.form.submit()">
                        <span class="px-4 py-2 bg-white/5 hover:bg-white/10 text-gray-300 text-sm rounded-xl border border-white/10 transition">Change Photo</span>
                    </label>
                </form>
                <p class="text-xs text-gray-500 mt-2">JPG or PNG. Max 2MB.</p>
            </div>
        </div>
    </div>

    <!-- Personal Info -->
    <form method="POST" action="{{ route('dashboard.profile.update') }}" class="bg-gray-900 rounded-2xl border border-white/5 p-6 space-y-5"
        x-data="{ loading: false }" @submit="loading = true">
        @csrf @method('PUT')
        <h3 class="font-semibold text-white">Personal Information</h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm text-gray-400 mb-1.5">Full Name</label>
                <input type="text" name="full_name" value="{{ old('full_name', auth()->user()->full_name) }}"
                    class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1.5">Phone</label>
                <input type="tel" name="phone" value="{{ old('phone', auth()->user()->phone) }}"
                    class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm text-gray-400 mb-1.5">Email <span class="text-gray-600 text-xs">(Cannot be changed here)</span></label>
                <input type="email" value="{{ auth()->user()->email }}" disabled
                    class="w-full bg-gray-800/50 border border-white/5 text-gray-500 px-4 py-2.5 rounded-xl text-sm cursor-not-allowed">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1.5">Country</label>
                <input type="text" name="country" value="{{ old('country', auth()->user()->country) }}"
                    class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1.5">City</label>
                <input type="text" name="city" value="{{ old('city', auth()->user()->city) }}"
                    class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1.5">State</label>
                <input type="text" name="state" value="{{ old('state', auth()->user()->state) }}"
                    class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1.5">Zip Code</label>
                <input type="text" name="zip" value="{{ old('zip', auth()->user()->zip) }}"
                    class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm text-gray-400 mb-1.5">Bio</label>
                <textarea name="bio" rows="4" placeholder="Tell your fans about yourself..."
                    class="w-full bg-gray-800 border border-white/10 text-white placeholder-gray-500 px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm resize-none">{{ old('bio', auth()->user()->bio) }}</textarea>
            </div>
        </div>

        <div class="border-t border-white/5 pt-4">
            <h4 class="text-sm font-medium text-gray-400 mb-3">Social Links</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Facebook</label>
                    <input type="url" name="social_facebook"
                        value="{{ old('social_facebook', auth()->user()->social_facebook) }}"
                        placeholder="https://facebook.com/..."
                        class="w-full bg-gray-800 border border-white/10 text-white placeholder-gray-500 px-3 py-2 rounded-xl focus:outline-none focus:border-purple-500 text-xs">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Twitter / X</label>
                    <input type="url" name="social_twitter"
                        value="{{ old('social_twitter', auth()->user()->social_twitter) }}"
                        placeholder="https://twitter.com/..."
                        class="w-full bg-gray-800 border border-white/10 text-white placeholder-gray-500 px-3 py-2 rounded-xl focus:outline-none focus:border-purple-500 text-xs">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Instagram</label>
                    <input type="url" name="social_instagram"
                        value="{{ old('social_instagram', auth()->user()->social_instagram) }}"
                        placeholder="https://instagram.com/..."
                        class="w-full bg-gray-800 border border-white/10 text-white placeholder-gray-500 px-3 py-2 rounded-xl focus:outline-none focus:border-purple-500 text-xs">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Website</label>
                    <input type="url" name="social_website"
                        value="{{ old('social_website', auth()->user()->social_website) }}"
                        placeholder="https://your-site.com"
                        class="w-full bg-gray-800 border border-white/10 text-white placeholder-gray-500 px-3 py-2 rounded-xl focus:outline-none focus:border-purple-500 text-xs">
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit"
                :disabled="loading"
                class="px-6 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold rounded-xl transition text-sm flex items-center gap-2 disabled:opacity-60">
                <span x-show="loading" class="inline-block w-4 h-4 border-2 border-white/40 border-t-white rounded-full animate-spin"></span>
                <span x-text="loading ? 'Saving...' : 'Save Profile'"></span>
            </button>
        </div>
    </form>
</div>
@endsection
