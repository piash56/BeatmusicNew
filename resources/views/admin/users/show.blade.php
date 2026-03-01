@extends('layouts.admin')

@section('title', $user->full_name)
@section('page-title', 'User Profile')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.users') }}" class="flex items-center space-x-2 text-gray-400 hover:text-white transition text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            <span>Back to Users</span>
        </a>
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.users.edit', $user->id) }}" class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-lg transition">Edit User</a>
            <form method="POST" action="{{ route('admin.users.toggle-suspension', $user->id) }}" onsubmit="return confirm('{{ $user->status === 'active' ? 'Suspend' : 'Activate' }} this user?')">
                @csrf
                <button type="submit" class="px-3 py-1.5 {{ $user->status === 'active' ? 'bg-red-600/20 text-red-400 hover:bg-red-600/40' : 'bg-green-600/20 text-green-400 hover:bg-green-600/40' }} text-sm rounded-lg border border-white/10 transition">
                    {{ $user->status === 'active' ? 'Suspend' : 'Activate' }}
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Profile Card -->
        <div class="bg-gray-900 rounded-2xl border border-white/5 p-6 text-center">
            @if($user->profile_picture)
                <img src="{{ asset('storage/'.$user->profile_picture) }}" class="w-24 h-24 rounded-full object-cover mx-auto">
            @else
                <div class="w-24 h-24 bg-gradient-to-br from-purple-600 to-indigo-600 rounded-full flex items-center justify-center text-white text-3xl font-bold mx-auto">
                    {{ strtoupper(substr($user->full_name, 0, 2)) }}
                </div>
            @endif
            <h2 class="text-white font-bold text-lg mt-4">{{ $user->full_name }}</h2>
            <p class="text-gray-400 text-sm">{{ $user->email }}</p>
            <div class="flex items-center justify-center space-x-2 mt-3">
                <span class="px-2.5 py-1 rounded-full text-xs {{ $user->is_company ? 'bg-indigo-900/50 text-indigo-400' : 'bg-gray-700/50 text-gray-400' }}">{{ $user->is_company ? 'Company' : 'Individual' }}</span>
                <span class="px-2.5 py-1 rounded-full text-xs {{ $user->status === 'active' ? 'bg-green-900/50 text-green-400' : 'bg-red-900/50 text-red-400' }}">{{ ucfirst($user->status) }}</span>
                @if($user->is_verified)
                    <span class="inline-flex items-center text-green-400" title="Verified"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg></span>
                @else
                    <span class="inline-flex items-center text-amber-400" title="Unverified"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg></span>
                @endif
            </div>
            @if(!$user->is_admin && !$user->is_verified)
            <div class="mt-4">
                <form method="POST" action="{{ route('admin.users.resend-set-password', $user->id) }}" class="inline">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 bg-amber-600/20 hover:bg-amber-600/40 text-amber-400 text-sm rounded-lg border border-amber-500/30 transition">Resend set password email</button>
                </form>
                <p class="text-xs text-gray-500 mt-1">Link valid 24 hours. User must set password to become verified.</p>
            </div>
            @endif
            <div class="mt-5 space-y-2 text-sm text-left">
                <div class="flex justify-between"><span class="text-gray-500">Joined</span><span class="text-gray-300">{{ $user->created_at->format('M d, Y') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Last Login</span><span class="text-gray-300">{{ $user->last_login_time ? $user->last_login_time->diffForHumans() : 'N/A' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Verified</span><span class="text-gray-300">{{ $user->is_verified ? 'Yes (password set)' : 'No' }}</span></div>
            </div>
            <div class="mt-5 border-t border-white/5 pt-4 text-left text-sm">
                <h3 class="font-semibold text-white mb-3">Profile & Social</h3>
                <div class="space-y-4">
                    <div>
                        <div class="text-gray-500 text-xs mb-1">Bio</div>
                        <p class="text-gray-300 whitespace-pre-line min-h-[1.5rem]">{{ $user->bio ?? '—' }}</p>
                    </div>
                    <div class="grid grid-cols-1 gap-3">
                        <div>
                            <div class="text-gray-500 text-xs mb-1">Facebook</div>
                            <p class="text-gray-300 break-all">{{ $user->social_facebook ?? '—' }}</p>
                        </div>
                        <div>
                            <div class="text-gray-500 text-xs mb-1">Twitter / X</div>
                            <p class="text-gray-300 break-all">{{ $user->social_twitter ?? '—' }}</p>
                        </div>
                        <div>
                            <div class="text-gray-500 text-xs mb-1">Instagram</div>
                            <p class="text-gray-300 break-all">{{ $user->social_instagram ?? '—' }}</p>
                        </div>
                        <div>
                            <div class="text-gray-500 text-xs mb-1">Website</div>
                            <p class="text-gray-300 break-all">{{ $user->social_website ?? '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-4">

            <!-- Stats -->
            @php
                $tracks = $user->tracks;
                $totalStreams = $tracks->sum('total_streams');
                $releasedCount = $tracks->whereIn('status', ['Released', 'Modify Released'])->count();
                $onRequestCount = $tracks->whereIn('status', ['On Request', 'Modify Pending'])->count();
                $totalTracks = $tracks->count();
            @endphp
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                @foreach([
                    ['Total Streams', number_format($totalStreams), 'text-purple-400'],
                    ['Released', $releasedCount, 'text-green-400'],
                    ['On Request', $onRequestCount, 'text-blue-400'],
                    ['Total Tracks', $totalTracks, 'text-gray-300'],
                ] as [$label, $value, $color])
                <div class="bg-gray-900 rounded-xl border border-white/5 p-4 text-center">
                    <div class="text-xl font-bold {{ $color }}">{{ $value }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">{{ $label }}</div>
                </div>
                @endforeach
            </div>

            <!-- Financials -->
            <div class="bg-gray-900 rounded-2xl border border-white/5 p-5">
                <h3 class="font-semibold text-white mb-3">Financials</h3>
                <div class="grid grid-cols-3 gap-3 text-sm">
                    <div><span class="text-gray-500 text-xs block">Available Balance</span><p class="text-green-400 font-semibold">${{ number_format($user->balance ?? 0, 2) }}</p></div>
                    <div><span class="text-gray-500 text-xs block">Total Earned</span><p class="text-gray-200">${{ number_format($user->total_revenue ?? 0, 2) }}</p></div>
                    <div><span class="text-gray-500 text-xs block">Total Paid</span><p class="text-gray-200">${{ number_format($user->total_paid ?? 0, 2) }}</p></div>
                </div>
            </div>

            <!-- Account & Contact Details -->
            <div class="bg-gray-900 rounded-2xl border border-white/5 p-5">
                <h3 class="font-semibold text-white mb-3">Account & Contact</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                    <div>
                        <dt class="text-gray-500">Full Name</dt>
                        <dd class="text-white">{{ $user->full_name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Email</dt>
                        <dd class="text-white">{{ $user->email ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Phone</dt>
                        <dd class="text-white">{{ $user->phone ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Country</dt>
                        <dd class="text-white">{{ $user->country ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">City</dt>
                        <dd class="text-white">{{ $user->city ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">State</dt>
                        <dd class="text-white">{{ $user->state ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Zip Code</dt>
                        <dd class="text-white">{{ $user->zip ?? '—' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-gray-500">Address</dt>
                        <dd class="text-white">{{ $user->address ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Account Type</dt>
                        <dd class="text-white">{{ $user->is_company ? 'Company' : 'Individual' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Subscription</dt>
                        <dd class="text-white">{{ $user->subscription ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Can Upload Tracks</dt>
                        <dd class="text-white">{{ $user->can_upload_tracks ? 'Yes' : 'No' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Payout Method</dt>
                        <dd class="text-white">{{ $user->payout_method ? ucfirst($user->payout_method) : '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">PayPal Email</dt>
                        <dd class="text-white">{{ $user->paypal_email ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Billing Info -->
            <div class="bg-gray-900 rounded-2xl border border-white/5 p-5">
                <h3 class="font-semibold text-white mb-3">Billing Information</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                    <div>
                        <dt class="text-gray-500">Billing Name</dt>
                        <dd class="text-white">{{ $user->billing_full_name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Billing Email</dt>
                        <dd class="text-white">{{ $user->billing_email ?? '—' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-gray-500">Billing Address</dt>
                        <dd class="text-white">{{ $user->billing_address ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">City</dt>
                        <dd class="text-white">{{ $user->billing_city ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">State</dt>
                        <dd class="text-white">{{ $user->billing_state ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Zip Code</dt>
                        <dd class="text-white">{{ $user->billing_zip_code ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Country</dt>
                        <dd class="text-white">{{ $user->billing_country ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

        </div>
    </div>
</div>
@endsection
