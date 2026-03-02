@extends('layouts.dashboard')

@section('title', 'Impostazioni')
@section('page-title', 'Impostazioni Account')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Change Password -->
    <form method="POST" action="{{ route('dashboard.settings.password') }}" class="bg-gray-900 rounded-2xl border border-white/5 p-6 space-y-5"
        x-data="{
            showCurrent: false,
            showNew: false,
            showConfirm: false,
            password: '',
            confirm: '',
            passwordStrength: 0,
            updateStrength() {
                const len = this.password.length;
                this.passwordStrength = len >= 12 ? 3 : len >= 8 ? 2 : len >= 6 ? 1 : 0;
            }
        }">
        @csrf @method('PUT')
        <h3 class="font-semibold text-white">Cambiare la password</h3>
        @if($errors->any())
        <div class="bg-red-900/30 border border-red-500/30 text-red-400 p-3 rounded-xl text-sm">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
        @endif
        <div class="space-y-4">
            <div>
                <label class="block text-sm text-gray-400 mb-1.5">Current Password</label>
                <div class="relative">
                    <input :type="showCurrent ? 'text' : 'password'" name="current_password" required
                        class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 pr-10 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
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
            <div>
                <label class="block text-sm text-gray-400 mb-1.5">Nuova parola d'ordine</label>
                <div class="relative">
                    <input :type="showNew ? 'text' : 'password'" name="password" x-model="password"
                        @input="updateStrength()"
                        required minlength="8"
                        class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 pr-10 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
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
                    <div :class="passwordStrength >= 1 ? 'bg-red-500' : 'bg-gray-700'" class="h-1 flex-1 rounded-full transition-colors"></div>
                    <div :class="passwordStrength >= 2 ? 'bg-yellow-500' : 'bg-gray-700'" class="h-1 flex-1 rounded-full transition-colors"></div>
                    <div :class="passwordStrength >= 3 ? 'bg-green-500' : 'bg-gray-700'" class="h-1 flex-1 rounded-full transition-colors"></div>
                </div>
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1.5">Conferma nuova password</label>
                <div class="relative">
                    <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation" x-model="confirm" required
                        class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 pr-10 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
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
                    <span x-show="password && confirm && password === confirm">Le password corrispondono</span>
                    <span x-show="password && confirm && password !== confirm">Le password non corrispondono</span>
                </p>
            </div>
        </div>
        <div class="flex justify-end">
            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold rounded-xl transition text-sm">
                Aggiorna password
            </button>
        </div>
    </form>

    <!-- Payout Settings -->
    <div class="bg-gray-900 rounded-2xl border border-white/5 p-6">
        <h3 class="font-semibold text-white mb-1">Impostazioni di pagamento</h3>
        <p class="text-gray-400 text-sm mb-5">Configura come ricevi i tuoi guadagni</p>
        <form method="POST" action="{{ route('dashboard.settings.payout') }}">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm text-gray-400 mb-1.5">Metodo di pagamento</label>
                    <input type="text" value="PayPal" disabled
                        class="w-full bg-gray-800/60 border border-white/10 text-gray-300 px-4 py-2.5 rounded-xl text-sm cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1.5">E-mail PayPal</label>
                    <input type="email" name="paypal_email" value="{{ auth()->user()->paypal_email }}" placeholder="paypal@example.com"
                        @if(auth()->user()->paypal_email) disabled @endif
                        class="w-full bg-gray-800 border border-white/10 text-white placeholder-gray-500 px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm @if(auth()->user()->paypal_email) cursor-not-allowed opacity-70 @endif">
                    @if(auth()->user()->paypal_email)
                        <p class="text-xs text-gray-500 mt-1">Per modificare l'indirizzo email PayPal, contatta l'assistenza o il team di amministrazione.</p>
                    @endif
                </div>
            </div>
            <div class="flex justify-end mt-4">
                <button type="submit"
                    @if(auth()->user()->paypal_email) disabled @endif
                    class="px-6 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold rounded-xl transition text-sm @if(auth()->user()->paypal_email) opacity-60 cursor-not-allowed @endif">
                    Salva le impostazioni di pagamento
                </button>
            </div>
        </form>
    </div>

    <!-- Account Info -->
    <div class="bg-gray-900 rounded-2xl border border-white/5 p-6">
        <h3 class="font-semibold text-white mb-4">Informazioni sull'account</h3>
        <div class="space-y-3 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-500">Stato dell'account</span>
                <span class="px-2.5 py-1 rounded-full text-xs {{ auth()->user()->status === 'active' ? 'bg-green-900/50 text-green-400' : 'bg-red-900/50 text-red-400' }}">{{ ucfirst(auth()->user()->status) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Tipo di conto</span>
                <span class="px-2.5 py-1 rounded-full text-xs bg-purple-900/50 text-purple-400">
                    {{ auth()->user()->is_company ? 'Company' : 'Individual' }}
                </span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Membro dal</span>
                <span class="text-gray-300">{{ auth()->user()->created_at->format('M d, Y') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Ultimo accesso</span>
                <span class="text-gray-300">{{ auth()->user()->last_login_at ? \Carbon\Carbon::parse(auth()->user()->last_login_at)->diffForHumans() : 'N/A' }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
