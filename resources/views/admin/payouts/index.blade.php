@extends('layouts.admin')

@section('title', 'Richieste di pagamento')
@section('page-title', 'Richieste di pagamento degli artisti')

@section('content')
<div class="space-y-4" x-data="payoutsPage()">
    <div class="bg-gray-900 rounded-xl border border-white/5 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-800/50 border-b border-white/5">
                <tr>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium">Artista</th>
                    <th class="text-right px-4 py-3 text-gray-400 font-medium">Quantità</th>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium hidden sm:table-cell">Metodo</th>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium hidden md:table-cell">E-mail</th>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium">Stato</th>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium hidden lg:table-cell">Richiesto</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/3">
                @forelse($payouts as $payout)
                <tr class="hover:bg-white/2 transition">
                    <td class="px-4 py-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gradient-to-br from-purple-600 to-indigo-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                {{ strtoupper(substr($payout->user->full_name ?? 'U', 0, 2)) }}
                            </div>
                            <div>
                                <p class="text-white font-medium">{{ $payout->user->full_name ?? 'Unknown' }}</p>
                                <p class="text-gray-500 text-xs">{{ $payout->user->email ?? '' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-right font-semibold text-green-400">${{ number_format($payout->amount, 2) }}</td>
                    <td class="px-4 py-3 text-gray-400 hidden sm:table-cell capitalize">{{ $payout->method ?? 'PayPal' }}</td>
                    <td class="px-4 py-3 text-gray-400 text-xs hidden md:table-cell">{{ $payout->paypal_email ?? $payout->payout_email ?? 'N/A' }}</td>
                    <td class="px-4 py-3">
                        <button type="button"
                            @click="openModal({
                                id: {{ $payout->id }},
                                status: '{{ $payout->status ?? 'pending' }}',
                                amount: '{{ number_format($payout->amount, 2) }}',
                                user: '{{ addslashes($payout->user->full_name ?? 'Unknown') }}',
                                email: '{{ addslashes($payout->user->email ?? '') }}',
                            })"
                            class="px-2.5 py-1 rounded-lg text-xs border border-white/10
                                {{ $payout->status === 'paid' ? 'bg-green-900/40 text-green-300' :
                                   ($payout->status === 'rejected' ? 'bg-red-900/40 text-red-300' : 'bg-yellow-900/30 text-yellow-300') }}">
                            {{ ucfirst($payout->status ?? 'pending') }}
                        </button>
                    </td>
                    <td class="px-4 py-3 text-gray-400 text-xs hidden lg:table-cell">
                        <div>{{ $payout->created_at->format('M d, Y') }}</div>
                        <div class="text-[11px] text-gray-500">{{ $payout->created_at->format('H:i') }}</div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-12 text-center text-gray-500">Nessuna richiesta di pagamento trovata.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div>{{ $payouts->links() }}</div>

    <!-- Update status modal -->
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/70" @click="if (!loading) modalOpen = false"></div>
        <div class="relative bg-gray-900 border border-white/10 rounded-2xl shadow-xl max-w-md w-full p-6" @click.stop>
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-white">Aggiorna lo stato del pagamento</h3>
                    <p class="text-gray-400 text-xs mt-1">
                        Artista: <span class="text-gray-200" x-text="userName"></span>
                        <span class="text-gray-500" x-text="userEmail ? ' • ' + userEmail : ''"></span>
                    </p>
                    <p class="text-gray-400 text-xs">
                        Quantità: <span class="text-green-400" x-text="'$' + amount"></span>
                    </p>
                </div>
                <button type="button" @click="if (!loading) modalOpen = false" class="text-gray-400 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form :action="formAction" method="POST" @submit="loading = true">
                @csrf
                @method('PATCH')
                <div class="space-y-3 text-sm">
                    <div>
                        <label class="block text-gray-300 mb-1.5">Stato</label>
                        <select name="status" x-model="status"
                            class="w-full bg-gray-800 border border-white/10 text-gray-200 px-3 py-2 rounded-xl text-sm focus:outline-none focus:border-purple-500">
                            <option value="pending">In attesa di</option>
                            <option value="paid">Pagato</option>
                            <option value="rejected">Respinto</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-5">
                    <button type="button" @click="if (!loading) modalOpen = false"
                        class="px-4 py-2 bg-white/5 hover:bg-white/10 text-gray-300 rounded-lg border border-white/10 text-sm transition">
                        Cancellare
                    </button>
                    <button type="submit"
                        :disabled="loading"
                        class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm flex items-center gap-2 min-w-[120px] justify-center disabled:opacity-60">
                        <span x-show="loading" class="inline-block w-4 h-4 border-2 border-white/40 border-t-white rounded-full animate-spin"></span>
                        <span x-text="loading ? 'Updating...' : 'Update Status'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function payoutsPage() {
        const actionTemplate = '{{ route('admin.payout-requests.status', ['id' => '__ID__']) }}';
        return {
            modalOpen: false,
            loading: false,
            formAction: '',
            status: 'pending',
            userName: '',
            userEmail: '',
            amount: '0.00',
            openModal(payload) {
                this.formAction = actionTemplate.replace('__ID__', payload.id);
                this.status = payload.status || 'pending';
                this.userName = payload.user || '';
                this.userEmail = payload.email || '';
                this.amount = payload.amount || '0.00';
                this.loading = false;
                this.modalOpen = true;
            }
        };
    }
</script>
@endsection
