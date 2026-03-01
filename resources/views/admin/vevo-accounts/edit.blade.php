@extends('layouts.admin')

@section('title', 'Modifica account VEVO')
@section('page-title', 'Modifica l\'account VEVO')

@section('content')
@php
    $user = $account->user;
    $accountType = $user && $user->is_company ? 'Company' : 'Individual';
    $statusLabel = $account->status === 'Approved' ? 'Approvato' : ($account->status === 'Rejected' ? 'Rifiutato' : 'In attesa');
@endphp

<div class="max-w-3xl space-y-6">
    <a href="{{ route('admin.vevo-accounts.show', $account->id) }}" class="flex items-center space-x-2 text-gray-400 hover:text-white transition text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        <span>Torna a Visualizza</span>
    </a>

    <div class="flex items-start justify-between gap-4">
        <div>
            <h2 class="text-white text-xl font-bold">Modifica l'account VEVO</h2>
            <p class="text-gray-400 text-sm mt-1">Aggiorna le informazioni e lo stato dell'account VEVO</p>
        </div>
        <span class="px-3 py-1.5 rounded-full text-xs font-medium shrink-0
            {{ $account->status === 'Approved' ? 'bg-green-900/40 text-green-300' :
               ($account->status === 'Rejected' ? 'bg-red-900/40 text-red-300' : 'bg-yellow-900/40 text-yellow-300') }}">
            {{ $statusLabel }}
        </span>
    </div>

    <!-- Informazioni sul richiedente (read-only) -->
    <div class="bg-gray-900 rounded-2xl border border-white/5 p-6">
        <h3 class="font-semibold text-white mb-1">Informazioni sul richiedente</h3>
        <p class="text-gray-500 text-xs mb-4">Questa informazione non può essere modificata</p>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
            <div>
                <dt class="text-gray-500 text-xs">Nome e cognome</dt>
                <dd class="text-gray-200 mt-0.5">{{ $user->full_name ?? 'Sconosciuto' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 text-xs">Email</dt>
                <dd class="text-gray-200 mt-0.5">{{ $user->email ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 text-xs">Tipo di conto</dt>
                <dd class="text-gray-200 mt-0.5">{{ $accountType }}</dd>
            </div>
        </dl>
    </div>

    <!-- Informazioni artista (editable) -->
    <form method="POST" action="{{ route('admin.vevo-accounts.update', $account->id) }}" class="space-y-6" x-data="{ bio: '' }" x-init="bio = $el.querySelector('textarea[name=biography]').value">
        @csrf
        @method('PUT')

        <div class="bg-gray-900 rounded-2xl border border-white/5 p-6">
            <h3 class="font-semibold text-white mb-1">Informazioni artista</h3>
            <p class="text-gray-500 text-xs mb-4">Modifica i dettagli artista e le informazioni della richiesta VEVO</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-400 mb-1.5">Nome artista <span class="text-red-400">*</span></label>
                    <input type="text" name="artist_name" value="{{ old('artist_name', $account->artist_name) }}" required maxlength="255"
                        class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm"
                        placeholder="Es. La famy">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1.5">Contatto Email <span class="text-red-400">*</span></label>
                    <input type="email" name="contact_email" value="{{ old('contact_email', $account->contact_email) }}" required
                        class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm"
                        placeholder="email@esempio.com">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1.5">Telefono</label>
                    <input type="text" name="telephone" value="{{ old('telephone', $account->telephone) }}" maxlength="50"
                        class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm"
                        placeholder="+123456789">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1.5">Nome della versione</label>
                    <input type="text" name="release_name" value="{{ old('release_name', $account->release_name) }}" maxlength="255"
                        class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm"
                        placeholder="Es. Summer Vibes">
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm text-gray-400 mb-1.5">Biografia dell'artista <span class="text-red-400">*</span></label>
                <textarea name="biography" rows="5" required minlength="50" x-model="bio"
                    class="w-full bg-gray-800 border border-white/10 text-white placeholder-gray-500 px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm resize-none"
                    placeholder="Racconta la tua storia come artista...">{{ old('biography', $account->biography) }}</textarea>
                <p class="text-gray-500 text-xs mt-1">
                    Sono richiesti almeno 50 caratteri
                    <span x-text="'( ' + (bio ? bio.length : 0) + '/50 )'"></span>
                </p>
                @error('biography')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Controlli di amministrazione -->
        <div class="bg-gray-900 rounded-2xl border border-white/5 p-6">
            <h3 class="font-semibold text-white mb-1">Controlli di amministrazione</h3>
            <p class="text-gray-500 text-xs mb-4">Aggiorna lo stato e aggiungi informazioni amministrative</p>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm text-gray-400 mb-1.5">Stato</label>
                    <select name="status" class="w-full bg-gray-800 border border-white/10 text-gray-300 px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
                        <option value="Pending" {{ old('status', $account->status) === 'Pending' ? 'selected' : '' }}>In attesa</option>
                        <option value="Approved" {{ old('status', $account->status) === 'Approved' ? 'selected' : '' }}>Approvato</option>
                        <option value="Rejected" {{ old('status', $account->status) === 'Rejected' ? 'selected' : '' }}>Rifiutato</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1.5">URL del canale VEVO</label>
                    <input type="url" name="vevo_channel_url" value="{{ old('vevo_channel_url', $account->vevo_channel_url) }}"
                        class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm"
                        placeholder="https://www.youtube.com/channel/...">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1.5">Aggiungi delle note su questa richiesta</label>
                    <textarea name="admin_notes" rows="4"
                        class="w-full bg-gray-800 border border-white/10 text-white placeholder-gray-500 px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm resize-none"
                        placeholder="Add any notes or comments about this request...">{{ old('admin_notes', $account->admin_notes) }}</textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <a href="{{ route('admin.vevo-accounts.show', $account->id) }}" class="px-5 py-2.5 bg-white/5 hover:bg-white/10 text-gray-300 rounded-xl border border-white/10 transition text-sm">
                    Cancellare
                </a>
                <button type="submit" class="px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-xl transition inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-9 4l10 10m-10-8l10-10"/></svg>
                    Salva modifiche
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
