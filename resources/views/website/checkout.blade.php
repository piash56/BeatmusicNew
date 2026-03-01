@extends('layouts.app')

@section('title', 'Checkout — Beat Music')

@section('content')
<section class="pt-32 pb-24 px-4">
    <div class="max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-8">

        {{-- Order Summary --}}
        <div class="order-2 lg:order-1">
            <div class="glass rounded-2xl p-6 border border-white/5 sticky top-24">
                <h2 class="text-white font-semibold text-lg mb-5">Order Summary</h2>
                @if(isset($plan))
                <div class="flex justify-between items-center mb-3">
                    <div>
                        <p class="text-white font-medium">{{ $plan->name }} Plan</p>
                        <p class="text-gray-400 text-sm">{{ $billingCycle === 'yearly' ? 'Annual' : 'Monthly' }} subscription</p>
                    </div>
                    <p class="text-white font-semibold">
                        ${{ $billingCycle === 'yearly' ? number_format($plan->price_yearly, 2) : number_format($plan->price_monthly, 2) }}
                    </p>
                </div>
                <div class="border-t border-white/10 pt-3 mt-3">
                    @if($billingCycle === 'yearly')
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-400">Monthly equivalent</span>
                        <span class="text-gray-300">${{ number_format($plan->price_yearly / 12, 2) }}/mo</span>
                    </div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-green-400">You save</span>
                        <span class="text-green-400">${{ number_format(($plan->price_monthly * 12) - $plan->price_yearly, 2) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between font-semibold mt-2 text-lg">
                        <span class="text-white">Total Today</span>
                        <span class="text-white">${{ $billingCycle === 'yearly' ? number_format($plan->price_yearly, 2) : number_format($plan->price_monthly, 2) }}</span>
                    </div>
                </div>
                @else
                <p class="text-gray-400 text-sm">No plan selected. <a href="{{ route('pricing') }}" class="text-purple-400 hover:text-purple-300">Choose a plan</a></p>
                @endif

                {{-- Features --}}
                @if(isset($plan) && !empty($plan->features))
                <div class="mt-5 pt-5 border-t border-white/10">
                    <p class="text-gray-400 text-xs font-medium uppercase tracking-wider mb-3">Included</p>
                    @php $features = is_array($plan->features) ? $plan->features : json_decode($plan->features, true); @endphp
                    <ul class="space-y-1.5">
                        @foreach(($features ?? []) as $f)
                        <li class="flex items-center space-x-2 text-xs text-gray-300">
                            <svg class="w-3.5 h-3.5 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>{{ $f }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>

        {{-- Checkout Form --}}
        <div class="order-1 lg:order-2">
            <h1 class="text-2xl font-bold text-white mb-6">Complete Your Order</h1>

            @if($errors->any())
            <div class="bg-red-900/30 border border-red-500/30 text-red-300 rounded-xl p-3 text-sm mb-5">{{ $errors->first() }}</div>
            @endif

            {{-- Voucher --}}
            <div class="glass rounded-2xl p-5 border border-white/5 mb-5" x-data="{showVoucher: false}">
                <button @click="showVoucher = !showVoucher" class="flex items-center space-x-2 text-purple-400 hover:text-purple-300 text-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    <span>Have a voucher code?</span>
                </button>
                <div x-show="showVoucher" class="mt-3 flex gap-2">
                    <input type="text" id="voucherCode" placeholder="Enter code" class="flex-1 bg-gray-800 border border-white/10 text-white px-4 py-2 rounded-xl text-sm focus:outline-none focus:border-purple-500">
                    <button type="button" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-xl transition">Apply</button>
                </div>
            </div>

            {{-- Payment Method --}}
            <div class="glass rounded-2xl p-5 border border-white/5 mb-5" x-data="{method:'stripe'}">
                <p class="text-white font-medium mb-4">Payment Method</p>
                <div class="flex gap-3 mb-5">
                    <label class="flex-1 flex items-center justify-center space-x-2 py-3 rounded-xl border cursor-pointer transition"
                        :class="method==='stripe' ? 'border-purple-500 bg-purple-600/10' : 'border-white/10 bg-white/2'">
                        <input type="radio" x-model="method" value="stripe" class="sr-only">
                        <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor"><path d="M2 7a2 2 0 012-2h16a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V7z" opacity=".2"/><path d="M2 10h20v3H2z"/></svg>
                        <span class="text-sm text-white">Card</span>
                    </label>
                    <label class="flex-1 flex items-center justify-center space-x-2 py-3 rounded-xl border cursor-pointer transition"
                        :class="method==='paypal' ? 'border-blue-500 bg-blue-600/10' : 'border-white/10 bg-white/2'">
                        <input type="radio" x-model="method" value="paypal" class="sr-only">
                        <span class="text-sm font-bold"><span class="text-blue-400">Pay</span><span class="text-indigo-400">Pal</span></span>
                    </label>
                </div>

                {{-- Stripe Card Form --}}
                <form method="POST" action="{{ route('checkout') }}" x-show="method==='stripe'" id="payment-form">
                    @csrf
                    <input type="hidden" name="payment_method" value="stripe">
                    <input type="hidden" name="plan_id" value="{{ $plan->id ?? '' }}">
                    <input type="hidden" name="billing_cycle" value="{{ $billingCycle ?? 'monthly' }}">
                    <div id="card-element" class="bg-gray-800 border border-white/10 rounded-xl p-4 mb-4 min-h-[50px]">
                        {{-- Stripe Elements will mount here --}}
                        <p class="text-gray-500 text-sm">Card input (Stripe Elements)</p>
                    </div>
                    <div id="card-errors" class="text-red-400 text-sm mb-3" role="alert"></div>
                    <button type="submit" class="w-full py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-xl hover:opacity-90 transition">
                        Subscribe Now
                    </button>
                </form>

                {{-- PayPal --}}
                <div x-show="method==='paypal'">
                    <form method="POST" action="{{ route('checkout') }}">
                        @csrf
                        <input type="hidden" name="payment_method" value="paypal">
                        <input type="hidden" name="plan_id" value="{{ $plan->id ?? '' }}">
                        <input type="hidden" name="billing_cycle" value="{{ $billingCycle ?? 'monthly' }}">
                        <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition">
                            Pay with PayPal
                        </button>
                    </form>
                </div>
            </div>

            <p class="text-gray-500 text-xs text-center">
                By subscribing, you agree to our
                <a href="{{ route('terms') }}" class="text-purple-400 hover:underline">Terms of Service</a> and
                <a href="{{ route('privacy') }}" class="text-purple-400 hover:underline">Privacy Policy</a>.
                You can cancel anytime.
            </p>
        </div>
    </div>
</section>
@endsection
