@extends('layouts.app')

@section('title', 'Pricing — Beat Music')

@section('content')
<section class="pt-32 pb-16 px-4 text-center relative overflow-hidden">
    <div class="absolute inset-0 hero-glow pointer-events-none opacity-50"></div>
    <div class="max-w-4xl mx-auto relative z-10 animate-fade-in-up">
        <span class="inline-block bg-cyan-500/10 border border-cyan-500/20 text-cyan-300 text-xs font-semibold px-5 py-2 rounded-full mb-6 uppercase tracking-wider">Simple Pricing</span>
        <h1 class="text-4xl sm:text-6xl font-bold text-white leading-tight mb-6">
            Plans That <span class="gradient-text">Grow With You</span>
        </h1>
        <p class="text-xl text-slate-400 max-w-2xl mx-auto leading-relaxed">Start free and upgrade as your career takes off. All plans include global distribution.</p>

        {{-- Billing toggle --}}
        <div class="flex items-center justify-center space-x-4 mt-10" x-data="{yearly: false}">
            <span :class="!yearly ? 'text-white font-medium' : 'text-slate-500'" class="text-sm transition-colors duration-200">Monthly</span>
            <button @click="yearly = !yearly" class="relative w-14 h-7 rounded-full bg-slate-800 border border-slate-700 transition-colors duration-200" :class="yearly ? 'bg-gradient-to-r from-cyan-500 to-teal-600' : ''">
                <span :class="yearly ? 'translate-x-7' : 'translate-x-1'" class="inline-block w-5 h-5 bg-white rounded-full transition-transform duration-300 transform mt-1 shadow-lg"></span>
            </button>
            <span :class="yearly ? 'text-white font-medium' : 'text-slate-500'" class="text-sm transition-colors duration-200">
                Yearly 
                <span class="text-emerald-400 text-xs font-semibold ml-1 bg-emerald-500/10 px-2 py-0.5 rounded">Save 20%</span>
            </span>
        </div>
    </div>
</section>

<section class="pb-24 px-4" x-data="{yearly: false }">
    <div class="max-w-6xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
            @if(isset($plans) && $plans->count())
                @foreach($plans as $index => $plan)
                <div class="relative glass rounded-3xl p-8 border flex flex-col card-hover transition-all duration-300"
                     :class="$plan->is_popular ? 'border-cyan-500/50 shadow-xl shadow-cyan-500/10 scale-105' : 'border-slate-700/30 hover:border-cyan-500/30'"
                     x-data="{ inView: false }"
                     x-intersect="inView = true"
                     :class="inView ? 'animate-fade-in-up' : 'opacity-0'"
                     style="animation-delay: {{ $index * 0.15 }}s;">
                    @if($plan->is_popular)
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-gradient-to-r from-cyan-500 to-teal-600 text-white text-xs font-bold px-5 py-1.5 rounded-full shadow-lg shadow-cyan-500/30 uppercase tracking-wider">
                        Most Popular
                    </div>
                    @endif
                    <div class="mb-6">
                        <h3 class="text-white font-bold text-2xl mb-2">{{ $plan->name }}</h3>
                        <p class="text-slate-400 text-sm leading-relaxed">{{ $plan->description }}</p>
                    </div>
                    <div class="mb-8">
                        <div x-show="!yearly" class="flex items-baseline">
                            <span class="text-5xl font-extrabold text-white">${{ number_format($plan->price_monthly, 0) }}</span>
                            <span class="text-slate-400 text-lg ml-2">/mo</span>
                        </div>
                        <div x-show="yearly" class="flex items-baseline">
                            <span class="text-5xl font-extrabold text-white">${{ number_format($plan->price_yearly / 12, 0) }}</span>
                            <span class="text-slate-400 text-lg ml-2">/mo</span>
                        </div>
                        <div x-show="yearly" class="mt-2">
                            <p class="text-emerald-400 text-sm font-medium">${{ number_format($plan->price_yearly, 0) }} billed yearly</p>
                        </div>
                    </div>
                    <ul class="space-y-3.5 mb-8 flex-1">
                        @php $features = is_array($plan->features) ? $plan->features : json_decode($plan->features, true); @endphp
                        @if(is_array($features))
                        @foreach($features as $feature)
                        <li class="flex items-start space-x-3 text-sm">
                            <svg class="w-5 h-5 text-emerald-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span class="text-slate-300 leading-relaxed">{{ $feature }}</span>
                        </li>
                        @endforeach
                        @endif
                    </ul>
                    <a href="{{ route('checkout') }}?plan={{ $plan->id }}"
                        class="w-full py-3.5 text-center text-sm font-bold rounded-xl transition-all duration-300 transform hover:scale-105 {{ $plan->is_popular ? 'bg-gradient-to-r from-cyan-500 to-teal-600 text-white hover:shadow-xl hover:shadow-cyan-500/30' : 'bg-slate-800/50 hover:bg-slate-800 text-white border border-slate-700/50 hover:border-cyan-500/50' }}">
                        Get Started
                    </a>
                </div>
                @endforeach
            @else
                {{-- Fallback static plans --}}
                @php
                $fallbackPlans = [
                    ['Free','Perfect for getting started','0','0',['1 release per month','Basic distribution','Standard analytics','Email support'],false],
                    ['Pro','For serious independent artists','9.99','99',['Unlimited releases','Priority distribution','Advanced analytics','Radio promotion access','Editorial playlist submissions','Priority support'],true],
                    ['Label','For labels & heavy hitters','24.99','249',['Everything in Pro','Up to 10 artists','White-label releases','Dedicated account manager','Custom ISRC/UPC','Revenue split management'],false],
                ];
                @endphp
                @foreach($fallbackPlans as $index => [$name,$desc,$monthly,$yearly,$features,$popular])
                <div class="relative glass rounded-3xl p-8 border flex flex-col card-hover transition-all duration-300"
                     :class="$popular ? 'border-cyan-500/50 shadow-xl shadow-cyan-500/10 scale-105' : 'border-slate-700/30 hover:border-cyan-500/30'"
                     x-data="{ inView: false }"
                     x-intersect="inView = true"
                     :class="inView ? 'animate-fade-in-up' : 'opacity-0'"
                     style="animation-delay: {{ $index * 0.15 }}s;">
                    @if($popular)
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-gradient-to-r from-cyan-500 to-teal-600 text-white text-xs font-bold px-5 py-1.5 rounded-full shadow-lg shadow-cyan-500/30 uppercase tracking-wider">
                        Most Popular
                    </div>
                    @endif
                    <div class="mb-6">
                        <h3 class="text-white font-bold text-2xl mb-2">{{ $name }}</h3>
                        <p class="text-slate-400 text-sm leading-relaxed">{{ $desc }}</p>
                    </div>
                    <div class="mb-8">
                        <div class="flex items-baseline">
                            <span class="text-5xl font-extrabold text-white">${{ $monthly }}</span>
                            <span class="text-slate-400 text-lg ml-2">/mo</span>
                        </div>
                    </div>
                    <ul class="space-y-3.5 mb-8 flex-1">
                        @foreach($features as $f)
                        <li class="flex items-start space-x-3 text-sm">
                            <svg class="w-5 h-5 text-emerald-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span class="text-slate-300 leading-relaxed">{{ $f }}</span>
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('register') }}" 
                       class="w-full py-3.5 text-center text-sm font-bold rounded-xl transition-all duration-300 transform hover:scale-105 {{ $popular ? 'bg-gradient-to-r from-cyan-500 to-teal-600 text-white hover:shadow-xl hover:shadow-cyan-500/30' : 'bg-slate-800/50 hover:bg-slate-800 text-white border border-slate-700/50 hover:border-cyan-500/50' }}">
                        Get Started
                    </a>
                </div>
                @endforeach
            @endif
        </div>
    </div>
</section>

{{-- FAQ --}}
<section class="pb-24 px-4 bg-slate-900/30 py-20">
    <div class="max-w-3xl mx-auto">
        <h2 class="text-3xl sm:text-4xl font-bold text-white text-center mb-12 animate-fade-in-up">Pricing FAQ</h2>
        <div class="space-y-4" x-data="{open:null}">
            @foreach([
                ['Can I change my plan?','Yes, you can upgrade or downgrade your plan at any time from your billing dashboard.'],
                ['Is there a free trial?','All new accounts start with 30 days of Pro features for free. No credit card required.'],
                ['What payment methods do you accept?','We accept Stripe (credit/debit cards) and PayPal for all subscription payments.'],
                ['Can I cancel anytime?','Yes, there are no contracts. Cancel your subscription at any time, no questions asked.'],
                ['Do I keep my music if I cancel?','Yes, your releases stay live on all platforms even if you cancel your subscription.'],
            ] as $i => [$q,$a])
            <div class="glass rounded-xl border border-slate-700/30 hover:border-cyan-500/30 transition-all duration-200 overflow-hidden"
                 x-data="{ inView: false }"
                 x-intersect="inView = true"
                 :class="inView ? 'animate-fade-in-up' : 'opacity-0'"
                 style="animation-delay: {{ $i * 0.1 }}s;">
                <button @click="open=open==={{$i}}?null:{{$i}}" 
                        class="w-full flex justify-between items-center px-6 py-5 text-left hover:bg-slate-800/30 transition-colors duration-200">
                    <span class="text-white font-semibold text-sm">{{ $q }}</span>
                    <svg :class="open==={{$i}}?'rotate-180 text-cyan-400':'text-slate-400'" 
                         class="w-5 h-5 transition-all duration-300 flex-shrink-0" 
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open==={{$i}}" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 max-h-0"
                     x-transition:enter-end="opacity-100 max-h-96"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 max-h-96"
                     x-transition:leave-end="opacity-0 max-h-0"
                     class="px-6 pb-5">
                    <p class="text-slate-400 text-sm leading-relaxed">{{ $a }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
