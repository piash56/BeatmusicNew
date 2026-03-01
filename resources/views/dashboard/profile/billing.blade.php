@extends('layouts.dashboard')

@section('title', 'Billing')
@section('page-title', 'Billing & Subscription')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Current Plan -->
    <div class="bg-gradient-to-br from-purple-900/30 to-indigo-900/30 rounded-2xl border border-purple-500/20 p-6">
        <div class="flex items-start justify-between">
            <div>
                <h3 class="text-white font-bold text-lg">{{ ucfirst(auth()->user()->subscription ?? 'Free') }} Plan</h3>
                <p class="text-gray-400 text-sm mt-1">Current subscription</p>
                @if($subscription && $subscription->ends_at)
                <p class="text-purple-400 text-sm mt-2">
                    @if($subscription->status === 'active')
                    Renews on {{ \Carbon\Carbon::parse($subscription->ends_at)->format('M d, Y') }}
                    @else
                    Expired on {{ \Carbon\Carbon::parse($subscription->ends_at)->format('M d, Y') }}
                    @endif
                </p>
                @endif
            </div>
            <span class="px-3 py-1.5 rounded-full text-sm font-medium {{ ($subscription && $subscription->status === 'active') ? 'bg-green-900/50 text-green-400' : 'bg-gray-700/50 text-gray-400' }}">
                {{ ($subscription && $subscription->status === 'active') ? 'Active' : 'Inactive' }}
            </span>
        </div>
        <div class="mt-4 flex items-center space-x-3">
            <a href="{{ route('pricing') }}" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-xl transition">
                Upgrade Plan
            </a>
            @if($subscription && $subscription->status === 'active')
            <button onclick="confirm('Cancel your subscription? You will retain access until the billing period ends.')"
                class="px-4 py-2 bg-white/5 hover:bg-white/10 text-gray-400 text-sm rounded-xl border border-white/10 transition">
                Cancel Subscription
            </button>
            @endif
        </div>
    </div>

    <!-- Subscription History -->
    <div class="bg-gray-900 rounded-2xl border border-white/5 overflow-hidden">
        <div class="p-4 border-b border-white/5">
            <h3 class="font-semibold text-white">Billing History</h3>
        </div>
        @if(isset($subscriptions) && $subscriptions->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-800/50 border-b border-white/5">
                    <tr>
                        <th class="text-left px-4 py-3 text-gray-400 font-medium">Plan</th>
                        <th class="text-left px-4 py-3 text-gray-400 font-medium">Amount</th>
                        <th class="text-left px-4 py-3 text-gray-400 font-medium hidden sm:table-cell">Period</th>
                        <th class="text-left px-4 py-3 text-gray-400 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/3">
                    @foreach($subscriptions as $sub)
                    <tr class="hover:bg-white/2 transition">
                        <td class="px-4 py-3 text-white">{{ $sub->pricingPlan->name ?? ucfirst($sub->plan) }}</td>
                        <td class="px-4 py-3 text-green-400 font-semibold">${{ number_format($sub->amount ?? 0, 2) }}</td>
                        <td class="px-4 py-3 text-gray-400 hidden sm:table-cell">
                            {{ $sub->starts_at ? \Carbon\Carbon::parse($sub->starts_at)->format('M d, Y') : 'N/A' }}
                            —
                            {{ $sub->ends_at ? \Carbon\Carbon::parse($sub->ends_at)->format('M d, Y') : 'N/A' }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium
                                {{ $sub->status === 'active' ? 'bg-green-900/50 text-green-400' :
                                   ($sub->status === 'cancelled' ? 'bg-red-900/50 text-red-400' : 'bg-gray-700/50 text-gray-400') }}">
                                {{ ucfirst($sub->status ?? 'inactive') }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-12 text-center">
            <p class="text-gray-500 text-sm">No billing history yet.</p>
        </div>
        @endif
    </div>

    <!-- Available Plans -->
    <div class="bg-gray-900 rounded-2xl border border-white/5 p-6">
        <h3 class="font-semibold text-white mb-4">Available Plans</h3>
        <div class="grid grid-cols-1 gap-3">
            @foreach($plans ?? [] as $plan)
            <div class="flex items-center justify-between p-4 rounded-xl border {{ auth()->user()->subscription === $plan->name ? 'border-purple-500/50 bg-purple-900/10' : 'border-white/5 bg-white/2' }}">
                <div>
                    <p class="text-white font-medium">{{ $plan->name }}</p>
                    <p class="text-gray-400 text-sm mt-0.5">{{ $plan->description }}</p>
                    @if($plan->features)
                    <div class="flex flex-wrap gap-1 mt-2">
                        @foreach(array_slice($plan->features, 0, 3) as $feature)
                        <span class="text-xs text-gray-500 bg-white/3 px-2 py-0.5 rounded-full">{{ $feature }}</span>
                        @endforeach
                    </div>
                    @endif
                </div>
                <div class="text-right ml-4 flex-shrink-0">
                    <p class="text-white font-bold text-lg">${{ number_format($plan->price, 0) }}<span class="text-xs text-gray-500">/mo</span></p>
                    @if(auth()->user()->subscription !== $plan->name)
                    <a href="{{ route('checkout', ['plan' => $plan->id]) }}" class="mt-2 block px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white text-xs rounded-lg transition">
                        Select
                    </a>
                    @else
                    <span class="text-xs text-purple-400 font-medium">Current</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
