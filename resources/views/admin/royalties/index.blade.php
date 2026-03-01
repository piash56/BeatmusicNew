@extends('layouts.admin')

@section('title', 'Update Royalties')
@section('page-title', 'Update Royalties')

@section('content')
<div class="space-y-4" x-data="royaltiesPage()">
    <p class="text-gray-400 text-sm">Manually add royalty earnings to artist accounts.</p>

    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <input type="text" x-model="search"
                placeholder="Search by name or email..."
                class="bg-gray-900 border border-white/10 text-white placeholder-gray-500 px-3 py-2 rounded-lg text-sm focus:outline-none focus:border-purple-500 w-full sm:w-72">
        </div>
    </div>

    <div class="bg-gray-900 rounded-xl border border-white/5 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-800/50 border-b border-white/5">
                <tr>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium">User</th>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium hidden sm:table-cell">Account Type</th>
                    <th class="text-right px-4 py-3 text-gray-400 font-medium hidden md:table-cell">Current Balance</th>
                    <th class="text-left px-4 py-3 text-gray-400 font-medium hidden lg:table-cell">PayPal Email</th>
                    <th class="text-right px-4 py-3 text-gray-400 font-medium">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/3">
                @forelse($users as $user)
                @php
                    $searchText = strtolower(trim(($user->full_name ?? '') . ' ' . ($user->email ?? '')));
                @endphp
                <tr class="hover:bg-white/2 transition"
                    x-show="!search || '{{ $searchText }}'.includes(search.toLowerCase())">
                    <td class="px-4 py-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 bg-gradient-to-br from-purple-600 to-indigo-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                {{ strtoupper(substr($user->full_name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="text-white font-medium">{{ $user->full_name }}</p>
                                <p class="text-gray-400 text-xs">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 hidden sm:table-cell">
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $user->is_company ? 'bg-indigo-900/50 text-indigo-400' : 'bg-gray-700/50 text-gray-400' }}">
                            {{ $user->is_company ? 'Company' : 'Individual' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right hidden md:table-cell text-green-400 font-semibold">
                        ${{ number_format($user->balance ?? 0, 2) }}
                    </td>
                    <td class="px-4 py-3 hidden lg:table-cell text-gray-300 text-sm">
                        {{ $user->paypal_email ?? 'Not set' }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        <button type="button"
                            @click="openModal({ id: {{ $user->id }}, name: '{{ addslashes($user->full_name) }}', balance: '{{ number_format($user->balance ?? 0, 2) }}', paypal: '{{ addslashes($user->paypal_email ?? 'Not set') }}' })"
                            class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white text-xs rounded-lg transition whitespace-nowrap">
                            Update Royalties
                        </button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-12 text-center text-gray-500">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div>{{ $users->links() }}</div>

    <!-- Update royalties modal -->
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-black/70" @click="if (!loading) modalOpen = false"></div>
        <div class="relative bg-gray-900 border border-white/10 rounded-2xl shadow-xl max-w-md w-full p-6" @click.stop>
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-white" x-text="'Update royalties for ' + userName"></h3>
                    <p class="text-gray-400 text-xs mt-1">Enter the amount to add to the user&apos;s current balance.</p>
                </div>
                <button type="button" @click="if (!loading) modalOpen = false" class="text-gray-400 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form :action="formAction" method="POST" x-ref="royaltyForm" @submit="loading = true">
                @csrf
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm text-gray-300 mb-1.5">Amount ({{ config('app.currency', 'USD') }})</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs">$</span>
                            <input type="number" name="amount" min="0.01" step="0.01" required placeholder="0.00"
                                class="w-full bg-gray-800 border border-white/10 text-white text-sm pl-7 pr-3 py-2.5 rounded-xl focus:outline-none focus:border-purple-500">
                        </div>
                    </div>
                    <p class="text-xs text-gray-400">
                        <span>Current balance: <span class="text-green-400" x-text="'$' + currentBalance"></span></span><br>
                        <span>PayPal Email: <span class="text-gray-200 text-sm" x-text="paypalEmail"></span></span>
                    </p>
                </div>
                <div class="flex justify-end gap-2 mt-5">
                    <button type="button" @click="if (!loading) modalOpen = false"
                        class="px-4 py-2 bg-white/5 hover:bg-white/10 text-gray-300 rounded-lg border border-white/10 text-sm transition">
                        Cancel
                    </button>
                    <button type="submit"
                        :disabled="loading"
                        class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm flex items-center gap-2 min-w-[130px] justify-center disabled:opacity-60">
                        <span x-show="loading" class="inline-block w-4 h-4 border-2 border-white/40 border-t-white rounded-full animate-spin"></span>
                        <span x-text="loading ? 'Updating...' : 'Update Royalties'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function royaltiesPage() {
        const actionTemplate = '{{ route('admin.update-royalties.add', ['userId' => '__ID__']) }}';
        return {
            search: '',
            modalOpen: false,
            loading: false,
            userId: null,
            userName: '',
            currentBalance: '0.00',
            paypalEmail: 'Not set',
            formAction: '',
            openModal(payload) {
                this.userId = payload.id;
                this.userName = payload.name;
                this.currentBalance = payload.balance;
                this.paypalEmail = payload.paypal;
                this.formAction = actionTemplate.replace('__ID__', payload.id);
                this.loading = false;
                this.modalOpen = true;
            }
        }
    }
</script>
@endsection
