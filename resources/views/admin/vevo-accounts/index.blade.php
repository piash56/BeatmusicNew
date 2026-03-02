@extends('layouts.admin')

@section('title', 'Conto VEVO')
@section('page-title', 'Conto VEVO')

@section('content')
<div class="space-y-4"
     x-data="{
        search: '{{ addslashes(request('search','')) }}'
     }">

    <div class="bg-gray-900 rounded-2xl border border-white/5 overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 p-4 border-b border-white/5">
            <div>
                <h3 class="text-white font-semibold text-base">Conto VEVO</h3>
                <p class="text-gray-400 text-xs">Gestire e rivedere le richieste di account VEVO da parte degli artisti.</p>
            </div>
            <form method="GET" class="flex items-center gap-2 w-full md:w-auto">
                <div class="relative flex-1 md:w-80">
                    <input
                        type="text"
                        name="search"
                        x-model="search"
                        value="{{ request('search') }}"
                        placeholder="Cerca per nome dell'artista, nome utente o e-mail..."
                        class="w-full bg-gray-800 border border-white/10 text-white placeholder-gray-500 px-9 py-2 rounded-xl text-sm focus:outline-none focus:border-purple-500">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z"/></svg>
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <select name="status" class="bg-gray-800 border border-white/10 text-gray-300 px-3 py-2 rounded-xl text-xs">
                        <option value="">Stato</option>
                        @foreach(['Pending','Approved','Rejected'] as $s)
                            <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs rounded-xl transition">
                        Filtra
                    </button>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[900px]">
                <thead class="bg-gray-800/50 border-b border-white/5 text-xs text-gray-400">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium">Nome del richiedente</th>
                        <th class="px-4 py-3 text-left font-medium">Nome dell'artista</th>
                        <th class="px-4 py-3 text-left font-medium">Contatto Email</th>
                        <th class="px-4 py-3 text-left font-medium hidden sm:table-cell">Pubblicazione</th>
                        <th class="px-4 py-3 text-left font-medium">Stato</th>
                        <th class="px-4 py-3 text-left font-medium">Inviato</th>
                        <th class="px-4 py-3 text-right font-medium">Azioni</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($accounts as $account)
                        @php
                            $requester = $account->user;
                            $requesterName = $requester->full_name ?? 'Sconosciuto';
                            $searchText = strtolower(trim(
                                ($requesterName ?? '') . ' ' .
                                ($account->artist_name ?? '') . ' ' .
                                ($account->contact_email ?? '') . ' ' .
                                ($account->release_name ?? '') . ' ' .
                                ($account->status ?? '')
                            ));
                        @endphp
                        <tr class="hover:bg-white/5 transition"
                            x-show="!search || '{{ $searchText }}'.includes(search.toLowerCase())">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-white/5 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A4 4 0 019 16h6a4 4 0 013.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-white text-sm font-medium truncate">{{ $requesterName }}</p>
                                        <p class="text-gray-500 text-xs truncate">{{ $requester->email ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-white text-sm font-medium truncate">{{ $account->artist_name }}</p>
                            </td>
                            <td class="px-4 py-3 text-gray-300">
                                <p class="truncate">{{ $account->contact_email }}</p>
                            </td>
                            <td class="px-4 py-3 text-gray-300 hidden sm:table-cell">
                                {{ $account->release_name ?: '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                    {{ $account->status === 'Approved' ? 'bg-green-900/40 text-green-300' :
                                       ($account->status === 'Rejected' ? 'bg-red-900/40 text-red-300' : 'bg-yellow-900/40 text-yellow-300') }}">
                                    {{ $account->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-300 text-sm">
                                {{ $account->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end space-x-1">
                                    <a href="{{ route('admin.vevo-accounts.show', $account->id) }}" class="p-1.5 text-gray-400 hover:text-white hover:bg-white/10 rounded-lg transition" title="View">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('admin.vevo-accounts.edit', $account->id) }}" class="p-1.5 text-gray-400 hover:text-white hover:bg-white/10 rounded-lg transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-gray-500 text-sm">Nessun account VEVO trovato.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 border-t border-white/5">
            {{ $accounts->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
