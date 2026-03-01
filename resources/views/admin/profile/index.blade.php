@extends('layouts.admin')

@section('title', 'Admin Profile')
@section('page-title', 'My Profile')

@section('content')
<div class="max-w-3xl space-y-6">

    @if(session('success'))
        <div class="bg-green-900/30 border border-green-500/30 text-green-300 rounded-xl p-3 text-sm">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="bg-red-900/30 border border-red-500/30 text-red-300 rounded-xl p-3 text-sm">{{ $errors->first() }}</div>
    @endif

    {{-- Profile Info --}}
    <div class="bg-gray-900 rounded-2xl border border-white/5 p-6">
        <h2 class="text-white font-semibold mb-5">Profile Information</h2>
        <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data"
              class="space-y-5"
              x-data="{ loading: false }"
              @submit="loading = true">
            @csrf @method('PUT')
            <div class="flex items-center space-x-5">
                @if($admin->profile_picture)
                    <img src="{{ Storage::url($admin->profile_picture) }}" class="w-16 h-16 rounded-full object-cover">
                @else
                    <div class="w-16 h-16 rounded-full bg-red-600 flex items-center justify-center text-white font-bold text-xl">
                        {{ strtoupper(substr($admin->full_name, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <label class="text-xs text-gray-400 mb-1 block">Profile Picture</label>
                    <input type="file" name="profile_picture" accept="image/*" class="text-sm text-gray-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-purple-600 file:text-white hover:file:bg-purple-700">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-gray-400 mb-1.5 block">Full Name</label>
                    <input type="text" name="full_name" value="{{ old('full_name', $admin->full_name) }}" required
                        class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl text-sm focus:outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="text-sm text-gray-400 mb-1.5 block">Email</label>
                    <input type="email" name="email" value="{{ old('email', $admin->email) }}" required
                        class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl text-sm focus:outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="text-sm text-gray-400 mb-1.5 block">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $admin->phone) }}"
                        class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl text-sm focus:outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="text-sm text-gray-400 mb-1.5 block">Country</label>
                    <input type="text" name="country" value="{{ old('country', $admin->country) }}"
                        class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl text-sm focus:outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="text-sm text-gray-400 mb-1.5 block">City</label>
                    <input type="text" name="city" value="{{ old('city', $admin->city) }}"
                        class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl text-sm focus:outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="text-sm text-gray-400 mb-1.5 block">State</label>
                    <input type="text" name="state" value="{{ old('state', $admin->state) }}"
                        class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl text-sm focus:outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="text-sm text-gray-400 mb-1.5 block">Zip Code</label>
                    <input type="text" name="zip" value="{{ old('zip', $admin->zip) }}"
                        class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl text-sm focus:outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="text-sm text-gray-400 mb-1.5 block">Address</label>
                    <input type="text" name="address" value="{{ old('address', $admin->address) }}"
                        class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl text-sm focus:outline-none focus:border-purple-500">
                </div>
            </div>
            <div>
                <label class="text-sm text-gray-400 mb-1.5 block">Bio</label>
                <textarea name="bio" rows="3"
                    class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl text-sm focus:outline-none focus:border-purple-500 resize-none"
                    placeholder="Tell artists and team who you are...">{{ old('bio', $admin->bio) }}</textarea>
            </div>
            <div>
                <label class="text-sm text-gray-400 mb-1.5 block">Social Links</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <span class="block text-xs text-gray-500 mb-1">Facebook</span>
                        <input type="url" name="social_facebook" value="{{ old('social_facebook', $admin->social_facebook) }}"
                            placeholder="https://facebook.com/..."
                            class="w-full bg-gray-800 border border-white/10 text-white placeholder-gray-500 px-3 py-2 rounded-xl text-xs focus:outline-none focus:border-purple-500">
                    </div>
                    <div>
                        <span class="block text-xs text-gray-500 mb-1">Twitter / X</span>
                        <input type="url" name="social_twitter" value="{{ old('social_twitter', $admin->social_twitter) }}"
                            placeholder="https://twitter.com/..."
                            class="w-full bg-gray-800 border border-white/10 text-white placeholder-gray-500 px-3 py-2 rounded-xl text-xs focus:outline-none focus:border-purple-500">
                    </div>
                    <div>
                        <span class="block text-xs text-gray-500 mb-1">Instagram</span>
                        <input type="url" name="social_instagram" value="{{ old('social_instagram', $admin->social_instagram) }}"
                            placeholder="https://instagram.com/..."
                            class="w-full bg-gray-800 border border-white/10 text-white placeholder-gray-500 px-3 py-2 rounded-xl text-xs focus:outline-none focus:border-purple-500">
                    </div>
                    <div>
                        <span class="block text-xs text-gray-500 mb-1">Website</span>
                        <input type="url" name="social_website" value="{{ old('social_website', $admin->social_website) }}"
                            placeholder="https://your-site.com"
                            class="w-full bg-gray-800 border border-white/10 text-white placeholder-gray-500 px-3 py-2 rounded-xl text-xs focus:outline-none focus:border-purple-500">
                    </div>
                </div>
            </div>
            <div class="flex justify-end">
                <button type="submit"
                    :disabled="loading"
                    class="px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-xl transition flex items-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed">
                    <span x-show="loading" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                    <span x-text="loading ? 'Saving...' : 'Save Profile'"></span>
                </button>
            </div>
        </form>
    </div>

    {{-- Change Password --}}
    <div class="bg-gray-900 rounded-2xl border border-white/5 p-6"
         x-data="{
            showCurrent: false,
            showNew: false,
            showConfirm: false,
            password: '',
            confirm: '',
            strength: 0,
            updateStrength() {
                const l = this.password.length;
                this.strength = l >= 12 ? 3 : l >= 8 ? 2 : l >= 6 ? 1 : 0;
            }
         }">
        <h2 class="text-white font-semibold mb-5">Change Password</h2>
        <form method="POST" action="{{ route('admin.profile.password') }}" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="text-sm text-gray-400 mb-1.5 block">Current Password</label>
                <div class="relative">
                    <input :type="showCurrent ? 'text' : 'password'" name="current_password" required
                        class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 pr-10 rounded-xl text-sm focus:outline-none focus:border-purple-500">
                    <button type="button" @click="showCurrent = !showCurrent"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-200">
                        <svg x-show="!showCurrent" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="showCurrent" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 012.233-3.592M6.223 6.223A9.956 9.956 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.05 10.05 0 01-4.132 5.411M9.88 9.88a3 3 0 104.24 4.24" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-gray-400 mb-1.5 block">New Password</label>
                    <div class="relative">
                        <input :type="showNew ? 'text' : 'password'" name="password" x-model="password"
                            @input="updateStrength()"
                            required minlength="8"
                            class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 pr-10 rounded-xl text-sm focus:outline-none focus:border-purple-500">
                        <button type="button" @click="showNew = !showNew"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-200">
                            <svg x-show="!showNew" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="showNew" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 012.233-3.592M6.223 6.223A9.956 9.956 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.05 10.05 0 01-4.132 5.411M9.88 9.88a3 3 0 104.24 4.24" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                    <div class="flex space-x-1 mt-2">
                        <div :class="strength >= 1 ? 'bg-red-500' : 'bg-gray-700'" class="h-1 flex-1 rounded-full transition-colors"></div>
                        <div :class="strength >= 2 ? 'bg-yellow-500' : 'bg-gray-700'" class="h-1 flex-1 rounded-full transition-colors"></div>
                        <div :class="strength >= 3 ? 'bg-green-500' : 'bg-gray-700'" class="h-1 flex-1 rounded-full transition-colors"></div>
                    </div>
                </div>
                <div>
                    <label class="text-sm text-gray-400 mb-1.5 block">Confirm Password</label>
                    <div class="relative">
                        <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation" x-model="confirm" required
                            class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 pr-10 rounded-xl text-sm focus:outline-none focus:border-purple-500">
                        <button type="button" @click="showConfirm = !showConfirm"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-200">
                            <svg x-show="!showConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="showConfirm" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 012.233-3.592M6.223 6.223A9.956 9.956 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.05 10.05 0 01-4.132 5.411M9.88 9.88a3 3 0 104.24 4.24" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                    <p x-show="confirm.length" class="text-xs mt-1"
                       :class="password && confirm && password === confirm ? 'text-green-400' : 'text-red-400'">
                        <span x-show="password && confirm && password === confirm">Passwords match</span>
                        <span x-show="password && confirm && password !== confirm">Passwords do not match</span>
                    </p>
                </div>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-xl transition">
                    Update Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
