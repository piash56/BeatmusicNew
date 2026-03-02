@extends('layouts.dashboard')

@section('title', 'Reddito')
@section('page-title', 'Entrate e pagamenti')
@section('page-subtitle', 'La cronologia dei tuoi guadagni e pagamenti')

@section('content')
<div class="space-y-6">

    <!-- Balance Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-gradient-to-br from-green-900/30 to-emerald-900/30 rounded-2xl border border-green-500/20 p-5">
            <div class="text-3xl mb-2">💰</div>
            <div class="text-3xl font-bold text-white">${{ number_format($balance['available'] ?? 0, 2) }}</div>
            <div class="text-sm text-green-400 mt-1">Saldo disponibile</div>
        </div>
        <div class="bg-gray-900 rounded-2xl border border-white/5 p-5">
            <div class="text-3xl mb-2">📊</div>
            <div class="text-3xl font-bold text-white">${{ number_format($balance['total_earned'] ?? 0, 2) }}</div>
            <div class="text-sm text-gray-400 mt-1">Totale guadagnato (pagamenti pagati)</div>
        </div>
        <div class="bg-gray-900 rounded-2xl border border-white/5 p-5">
            <div class="text-3xl mb-2">📤</div>
            <div class="text-3xl font-bold text-white">${{ number_format($balance['last_paid_out'] ?? 0, 2) }}</div>
            <div class="text-sm text-gray-400 mt-1">Ultimo pagamento</div>
        </div>
    </div>

    <!-- Request Payout -->
    @if(($balance['available'] ?? 0) >= 50 && empty($balance['has_pending']))
    <div class="bg-gray-900 rounded-2xl border border-white/5 p-6" x-data="{ open: false }">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-semibold text-white">Richiedi pagamento</h3>
                <p class="text-gray-400 text-sm mt-0.5">L'importo minimo del pagamento è di $ 50</p>
            </div>
            <button @click="open = !open" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-xl transition">
                Richiedi pagamento
            </button>
        </div>

        <div x-show="open" x-cloak x-transition class="mt-6 pt-6 border-t border-white/5">
            <form method="POST" action="{{ route('dashboard.revenue.payout') }}">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-400 mb-1.5">Quantità <span class="text-red-400">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                            <input type="number" name="amount" min="50" max="{{ $balance['available'] ?? 0 }}" step="0.01"
                                value="{{ $balance['available'] ?? 0 }}" required
                                class="w-full bg-gray-800 border border-white/10 text-white pl-7 pr-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
                        </div>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm text-gray-400 mb-1.5">Metodo di pagamento</label>
                        <div class="w-full bg-gray-800 border border-white/10 text-gray-300 px-4 py-2.5 rounded-xl text-sm flex items-center justify-between">
                            <span>PayPal</span>
                            <span class="text-xs text-gray-400">E-mail: {{ auth()->user()->paypal_email ?? 'Not set' }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-4">
                    <button type="button" @click="open = false" class="px-4 py-2 text-gray-400 hover:text-white text-sm transition">Cancellare</button>
                    <button type="submit" class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-xl transition">Richiedi pagamento</button>
                </div>
            </form>
        </div>
    </div>
    @else
    <div class="bg-yellow-900/20 rounded-2xl border border-yellow-500/20 p-5">
        <div class="flex items-center space-x-3">
            <svg class="w-5 h-5 text-yellow-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-yellow-400 text-sm">Ti serve almeno <strong>$50.00</strong> per richiedere un pagamento. Saldo attuale: <strong>${{ number_format($balance['available'] ?? 0, 2) }}</strong></p>
        </div>
    </div>
    @endif

    <!-- Payout History -->
    <div class="bg-gray-900 rounded-2xl border border-white/5 overflow-hidden">
        <div class="p-4 border-b border-white/5">
            <h3 class="font-semibold text-white">Cronologia dei pagamenti</h3>
        </div>
        @if($payouts->isEmpty())
        <div class="p-12 text-center">
            <p class="text-gray-500 text-sm">Nessuna richiesta di pagamento ancora.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-800/50 border-b border-white/5">
                    <tr>
                        <th class="text-left px-4 py-3 text-gray-400 font-medium">Data</th>
                        <th class="text-left px-4 py-3 text-gray-400 font-medium">Quantità</th>
                        <th class="text-left px-4 py-3 text-gray-400 font-medium hidden sm:table-cell">Metodo</th>
                        <th class="text-left px-4 py-3 text-gray-400 font-medium">Stato</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/3">
                    @foreach($payouts as $payout)
                    <tr class="hover:bg-white/2 transition">
                        <td class="px-4 py-3 text-gray-300">
                            <div>{{ $payout->created_at->format('M d, Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $payout->created_at->format('H:i') }}</div>
                        </td>
                        <td class="px-4 py-3 font-semibold text-green-400">${{ number_format($payout->amount, 2) }}</td>
                        <td class="px-4 py-3 text-gray-400 hidden sm:table-cell capitalize">{{ $payout->method ?? 'PayPal' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium
                                {{ $payout->status === 'paid' ? 'bg-green-900/50 text-green-400' :
                                   ($payout->status === 'rejected' ? 'bg-red-900/50 text-red-400' : 'bg-yellow-900/50 text-yellow-400') }}">
                                {{ ucfirst($payout->status ?? 'pending') }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-white/5">
            {{ $payouts->links() }}
        </div>
        @endif
    </div>

    <!-- Top Earning Tracks -->
    @if(isset($topTracks) && $topTracks->isNotEmpty())
    <div class="bg-gray-900 rounded-2xl border border-white/5 overflow-hidden">
        <div class="p-4 border-b border-white/5">
            <h3 class="font-semibold text-white">Tracce più redditizie</h3>
        </div>
        <div class="divide-y divide-white/5">
            @foreach($topTracks as $i => $track)
            <div class="p-4 flex items-center space-x-4">
                <span class="text-gray-600 w-5 text-center text-sm">{{ $i + 1 }}</span>
                @if($track->cover_art)
                <img src="{{ $track->cover_art_url }}" class="w-10 h-10 rounded-lg object-cover">
                @else
                <div class="w-10 h-10 bg-purple-600/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-400" fill="currentColor" viewBox="0 0 20 20"><path d="M18 3a1 1 0 00-1.196-.98l-10 2A1 1 0 006 5v9.114A4.369 4.369 0 005 14c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V7.82l8-1.6v5.894A4.37 4.37 0 0015 12c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V3z"/></svg>
                </div>
                @endif
                <div class="flex-1 min-w-0">
                    <p class="text-white font-medium truncate">{{ $track->title }}</p>
                    <p class="text-gray-500 text-xs">{{ number_format($track->total_streams ?? 0) }} streams</p>
                </div>
                <span class="text-green-400 font-semibold">${{ number_format($track->total_revenue ?? 0, 2) }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
