@extends('layouts.admin')

@section('title', 'Dettagli account VEVO')
@section('page-title', 'Dettagli account VEVO')

@section('content')
@php
    $user = $account->user;
    $accountType = $user && $user->is_company ? 'Company' : 'Individual';
@endphp

<div class="space-y-6 max-w-3xl">
    <a href="{{ route('admin.vevo-accounts') }}" class="flex items-center space-x-2 text-gray-400 hover:text-white transition text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        <span>Torna agli account VEVO</span>
    </a>

    <div class="bg-gray-900 rounded-2xl border border-white/5 p-6">
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-red-600/20 rounded-xl flex items-center justify-center text-3xl font-black text-red-400">
                    V
                </div>
                <div>
                    <h2 class="text-white text-xl font-bold">{{ $account->artist_name }}</h2>
                    <p class="text-gray-400 text-sm">
                        {{ $user->full_name ?? 'Sconosciuto' }} ·
                        <span class="text-gray-300">{{ $accountType }}</span>
                    </p>
                    @if($account->vevo_channel_url)
                        <a href="{{ $account->vevo_channel_url }}" target="_blank" rel="noopener"
                           class="text-red-400 hover:text-red-300 text-xs mt-1 inline-flex items-center gap-1">
                            <span>{{ $account->vevo_channel_url }}</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full text-xs font-medium
                    {{ $account->status === 'Approved' ? 'bg-green-900/40 text-green-300' :
                       ($account->status === 'Rejected' ? 'bg-red-900/40 text-red-300' : 'bg-yellow-900/40 text-yellow-300') }}">
                    {{ $account->status }}
                </span>
                <a href="{{ route('admin.vevo-accounts.edit', $account->id) }}"
                   class="px-3 py-1.5 bg-white/5 hover:bg-white/10 text-gray-200 text-xs rounded-lg border border-white/10 transition">
                    Modifica
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-6 text-sm">
            <div class="bg-white/5 rounded-xl p-3">
                <p class="text-gray-500 text-xs">Tipo di account</p>
                <p class="text-white font-medium mt-1">{{ $accountType }}</p>
            </div>
            <div class="bg-white/5 rounded-xl p-3">
                <p class="text-gray-500 text-xs">Creato</p>
                <p class="text-white font-medium mt-1">
                    {{ $account->created_at->format('d/m/Y') }}
                    <span class="text-gray-400 text-xs">· {{ $account->created_at->format('H:i') }}</span>
                </p>
            </div>
            @if($account->approved_at)
                <div class="bg-white/5 rounded-xl p-3">
                    <p class="text-gray-500 text-xs">Approvato il</p>
                    <p class="text-white font-medium mt-1">
                        {{ $account->approved_at->format('d/m/Y') }}
                        <span class="text-gray-400 text-xs">· {{ $account->approved_at->format('H:i') }}</span>
                    </p>
                </div>
            @elseif($account->rejected_at)
                <div class="bg-white/5 rounded-xl p-3">
                    <p class="text-gray-500 text-xs">Rifiutato il</p>
                    <p class="text-white font-medium mt-1">
                        {{ $account->rejected_at->format('d/m/Y') }}
                        <span class="text-gray-400 text-xs">· {{ $account->rejected_at->format('H:i') }}</span>
                    </p>
                </div>
            @endif
        </div>
    </div>

    <div class="bg-gray-900 rounded-2xl border border-white/5 p-6">
        <h3 class="font-semibold text-white mb-4">Informazioni account</h3>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-3 text-sm">
            <div>
                <dt class="text-gray-500 text-xs">Richiedente</dt>
                <dd class="text-gray-200 mt-0.5">{{ $user->full_name ?? 'Sconosciuto' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 text-xs">Email account</dt>
                <dd class="text-gray-200 mt-0.5">{{ $user->email ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 text-xs">Nome artista</dt>
                <dd class="text-gray-200 mt-0.5">{{ $account->artist_name }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 text-xs">Contatto email</dt>
                <dd class="text-gray-200 mt-0.5">{{ $account->contact_email }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 text-xs">Telefono</dt>
                <dd class="text-gray-200 mt-0.5">{{ $account->telephone ?: '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 text-xs">Pubblicazione</dt>
                <dd class="text-gray-200 mt-0.5">{{ $account->release_name ?: '—' }}</dd>
            </div>
        </dl>

        <div class="mt-5">
            <p class="text-gray-500 text-xs mb-1">Biografia artista</p>
            <p class="text-gray-200 text-sm whitespace-pre-line">{{ $account->biography }}</p>
        </div>

        @if($account->admin_notes)
            <div class="mt-4">
                <p class="text-gray-500 text-xs mb-1">Note amministratore</p>
                <p class="text-amber-200 text-sm whitespace-pre-line">{{ $account->admin_notes }}</p>
            </div>
        @endif
    </div>

    <!-- Status Toggle -->
    <div class="bg-gray-900 rounded-2xl border border-white/5 p-6">
        <h3 class="font-semibold text-white mb-3">Aggiorna stato account</h3>
        <form method="POST" action="{{ route('admin.vevo-accounts.status', $account->id) }}" class="space-y-3">
            @csrf
            @method('PATCH')
            <div class="flex flex-col md:flex-row md:items-center gap-3">
                <select name="status" class="bg-gray-800 border border-white/10 text-gray-300 px-3 py-2 rounded-lg text-sm md:flex-1">
                    @foreach(['Pending','Approved','Rejected'] as $s)
                        <option value="{{ $s }}" {{ $account->status === $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
                <input type="url"
                       name="vevo_channel_url"
                       value="{{ old('vevo_channel_url', $account->vevo_channel_url) }}"
                       placeholder="URL canale VEVO (opzionale)"
                       class="bg-gray-800 border border-white/10 text-gray-200 px-3 py-2 rounded-lg text-sm flex-1">
            </div>
            <div>
                <label class="block text-xs text-gray-400 mb-1.5">Note amministratore (opzionale)</label>
                <textarea name="admin_notes" rows="3" class="w-full bg-gray-800 border border-white/10 text-gray-200 px-3 py-2 rounded-lg text-sm">{{ old('admin_notes', $account->admin_notes) }}</textarea>
            </div>
            <div class="flex items-center justify-end gap-2">
                <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-lg transition">
                    Salva stato
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
