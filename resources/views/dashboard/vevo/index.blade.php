@extends('layouts.dashboard')

@section('title', 'Account VEVO')
@section('page-title', 'Crea un account VEVO')
@section('page-subtitle', 'Crea il tuo canale VEVO ufficiale per le tue uscite musicali')

@section('content')
<div class="space-y-6">
    @if(session('success'))
    <div class="p-3 rounded-xl bg-green-900/30 border border-green-500/30 text-green-300 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="p-3 rounded-xl bg-red-900/30 border border-red-500/30 text-red-300 text-sm">{{ session('error') }}</div>
    @endif

    @php
        $canSubmit = auth()->user()->is_company || $accounts->isEmpty();
    @endphp

    <!-- Main VEVO layout -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
        <!-- Left: Request form -->
        <div class="bg-gray-900 rounded-2xl border border-white/5 p-6">
            <div class="flex items-start gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-red-600/20 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h16v4H4zm2 6h10v4H6zm0 6h6v4H6z"/></svg>
                </div>
            </div>

            @if($canSubmit)
                <div class="space-y-1 mb-5">
                    <h3 class="text-white font-semibold text-lg">Richiedi un account VEVO</h3>
                    <p class="text-gray-400 text-sm">
                        Compila il modulo per richiedere la creazione di un account VEVO ufficiale.
                    </p>
                </div>

                <form method="POST" action="{{ route('dashboard.vevo.submit') }}" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-300 mb-1.5">Nome Artista <span class="text-red-400">*</span></label>
                            <input type="text"
                                   name="artist_name"
                                   value="{{ old('artist_name', auth()->user()->full_name) }}"
                                   placeholder="Es. Marco Rossi"
                                   required
                                   maxlength="255"
                                   class="w-full bg-gray-800 border border-white/10 text-white placeholder-gray-500 px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
                            <p class="text-gray-500 text-xs mt-1">Nome che vuoi che appaia su VEVO.</p>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-300 mb-1.5">Contatto email <span class="text-red-400">*</span></label>
                            <input type="email"
                                   name="contact_email"
                                   value="{{ old('contact_email', auth()->user()->email) }}"
                                   placeholder="marco@rossi.com"
                                   required
                                   class="w-full bg-gray-800 border border-white/10 text-white placeholder-gray-500 px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-300 mb-1.5">Telefono</label>
                            <input type="text"
                                   name="telephone"
                                   value="{{ old('telephone') }}"
                                   placeholder="+123456789"
                                   maxlength="50"
                                   class="w-full bg-gray-800 border border-white/10 text-white placeholder-gray-500 px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-300 mb-1.5">Nome della versione</label>
                            <input type="text"
                                   name="release_name"
                                   value="{{ old('release_name') }}"
                                   placeholder="Es. Summer Vibes"
                                   maxlength="255"
                                   class="w-full bg-gray-800 border border-white/10 text-white placeholder-gray-500 px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-300 mb-1.5">Biografia Artista <span class="text-red-400">*</span></label>
                        <textarea
                            name="biography"
                            rows="5"
                            required
                            minlength="50"
                            placeholder="Racconta la tua storia come artista..."
                            class="w-full bg-gray-800 border border-white/10 text-white placeholder-gray-500 px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm resize-none">{{ old('biography') }}</textarea>
                        <p class="text-gray-500 text-xs mt-1">
                            Scrivi una biografia completa per il tuo profilo VEVO (minimo 50 caratteri).
                        </p>
                        @error('biography')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                            class="w-full mt-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition">
                        Richiedi Account VEVO
                    </button>
                </form>
            @else
                <div class="space-y-3">
                    <h3 class="text-white font-semibold text-lg">Richiedi un account VEVO</h3>
                    <p class="text-gray-400 text-sm">
                        Hai già una richiesta di account VEVO attiva. Gli utenti non aziendali possono inviare una sola richiesta.
                    </p>
                    <p class="text-amber-200 text-xs">
                        Se hai bisogno di apportare modifiche, contatta il supporto indicando i dettagli della tua richiesta esistente.
                    </p>
                </div>
            @endif
        </div>

        <!-- Right: About VEVO -->
        <div class="bg-gray-900 rounded-2xl border border-white/5 p-6 space-y-5">
            <div>
                <h3 class="text-white font-semibold text-lg">A proposito di VEVO</h3>
                <p class="text-gray-400 text-sm mt-1">
                    Tutto quello che devi sapere sui canali VEVO.
                </p>
            </div>

            <div class="space-y-2">
                <h4 class="text-white font-semibold text-sm">Che cosa è VEVO?</h4>
                <p class="text-gray-400 text-sm">
                    VEVO è una piattaforma di hosting e distribuzione di video musicali che collabora con le principali
                    etichette discografiche. Avere un canale VEVO ufficiale aumenta la credibilità e la visibilità dei tuoi video musicali.
                </p>
            </div>

            <div class="space-y-2">
                <h4 class="text-white font-semibold text-sm">Vantaggi di un canale VEVO</h4>
                <ul class="space-y-1 text-sm text-gray-300">
                    <li class="flex items-start gap-2">
                        <span class="text-green-400 mt-0.5">✔</span>
                        <span>Maggiore visibilità su YouTube e altre piattaforme.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-green-400 mt-0.5">✔</span>
                        <span>Distintivo ufficiale di verifica e autenticità.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-green-400 mt-0.5">✔</span>
                        <span>Le migliori opportunità di monetizzazione.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-green-400 mt-0.5">✔</span>
                        <span>Accesso a statistiche dettagliate sulle performance dei tuoi video.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-green-400 mt-0.5">✔</span>
                        <span>Possibilità di essere inclusi nelle playlist editoriali VEVO.</span>
                    </li>
                </ul>
            </div>

            <div class="space-y-2">
                <h4 class="text-white font-semibold text-sm">Requisiti per un account VEVO</h4>
                <ul class="space-y-1 text-sm text-gray-300">
                    <li class="flex items-start gap-2">
                        <span class="text-green-400 mt-0.5">✔</span>
                        <span>Essere un artista con contenuti musicali originali.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-green-400 mt-0.5">✔</span>
                        <span>Avere un videoclip musicale professionale.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-green-400 mt-0.5">✔</span>
                        <span>Distribuire la tua musica tramite etichette riconosciute o distributori digitali.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-green-400 mt-0.5">✔</span>
                        <span>Fornire informazioni complete e accurate sull'artista.</span>
                    </li>
                </ul>
            </div>

            <div class="mt-2 space-y-3">
                <p class="text-gray-400 text-xs">
                    Riceverai una risposta alla tua richiesta entro <span class="text-gray-200 font-medium">5–7 giorni lavorativi</span>.
                </p>
                <a href="https://www.vevo.com" target="_blank" rel="noopener"
                   class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-white text-gray-900 text-sm font-medium hover:bg-gray-100 transition">
                    Scopri di più su VEVO
                </a>
            </div>
        </div>
    </div>

    <!-- I tuoi account VEVO -->
    <div class="bg-gray-900 rounded-2xl border border-white/5 overflow-hidden">
        <div class="p-4 border-b border-white/5">
            <h3 class="font-semibold text-white">I tuoi account VEVO</h3>
            <p class="text-gray-400 text-sm mt-0.5">Visualizza e gestisci i tuoi canali VEVO esistenti</p>
        </div>
        @if($accounts->isEmpty())
        <div class="p-12 text-center">
            <p class="text-gray-500 text-sm">Nessun account VEVO ancora.</p>
        </div>
        @else
        <div class="divide-y divide-white/5">
            @foreach($accounts as $acc)
            <div class="p-5">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-start gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0
                            {{ $acc->status === 'Approved' ? 'bg-green-600/30 text-green-400' :
                               ($acc->status === 'Rejected' ? 'bg-red-600/30 text-red-400' : 'bg-yellow-600/30 text-yellow-400') }}">
                            @if($acc->status === 'Approved')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            @elseif($acc->status === 'Rejected')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <p class="text-white font-semibold">{{ $acc->artist_name }}</p>
                            <p class="text-gray-500 text-sm">{{ $acc->contact_email }}</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-medium shrink-0
                        {{ $acc->status === 'Approved' ? 'bg-green-900/50 text-green-400' :
                           ($acc->status === 'Rejected' ? 'bg-red-900/50 text-red-400' : 'bg-yellow-900/50 text-yellow-400') }}">
                        {{ $acc->status === 'Approved' ? 'Approvato' : ($acc->status === 'Rejected' ? 'Rifiutato' : 'In attesa') }}
                    </span>
                </div>

                <p class="text-gray-400 text-sm mt-3 whitespace-pre-line">{{ $acc->biography }}</p>

                @if($acc->admin_notes)
                <p class="text-amber-200/90 text-xs mt-2"><span class="text-gray-500">Note:</span> {{ $acc->admin_notes }}</p>
                @endif
                @if($acc->vevo_channel_url)
                <a href="{{ $acc->vevo_channel_url }}" target="_blank" rel="noopener" class="text-red-400 hover:text-red-300 text-sm mt-2 inline-flex items-center gap-1">View channel →</a>
                @endif

                <div class="flex items-center justify-between gap-4 mt-4 pt-3 border-t border-white/5 text-gray-500 text-xs">
                    <span>Richiesto: {{ $acc->created_at->format('n/j/Y') }}</span>
                    @if($acc->status === 'Approved' && $acc->approved_at)
                        <span>Approvato: {{ $acc->approved_at->format('n/j/Y') }}</span>
                    @elseif($acc->status === 'Rejected' && $acc->rejected_at)
                        <span>Rifiutato: {{ $acc->rejected_at->format('n/j/Y') }}</span>
                    @else
                        <span></span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
