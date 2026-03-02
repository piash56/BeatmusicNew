@extends('layouts.app')

@section('title', 'Terms of Service — Beat Music')

@section('content')
<section class="pt-32 pb-24 px-4">
    <div class="max-w-3xl mx-auto">
        <div class="mb-10">
            <h1 class="text-4xl font-bold text-white mb-2">Termini di servizio</h1>
            <p class="text-gray-400 text-sm">Ultimo aggiornamento: 1 gennaio 2024</p>
        </div>
        <div class="glass rounded-2xl p-8 border border-white/5 space-y-8 text-gray-300 text-sm leading-relaxed">
            @foreach([
                ['1. Accettazione dei Termini','Accedendo e utilizzando Beat Music, accetti di essere vincolato dai presenti Termini di Servizio. Se non accetti questi termini, ti preghiamo di non utilizzare il nostro servizio.'],
                ['2. Descrizione del Servizio','Beat Music è una piattaforma di distribuzione musicale che consente agli artisti di caricare e distribuire la propria musica su piattaforme di streaming digitale in tutto il mondo. Forniamo strumenti per la promozione musicale, lanalisi e la gestione dei ricavi.'],
                ['3. Account Utente','Devi creare un account per utilizzare i nostri servizi. Sei responsabile della sicurezza delle credenziali del tuo account e di tutte le attività che si verificano tramite il tuo account. Devi avere almeno 18 anni per creare un account.'],
                ['4. Proprietà dei Contenuti','Conservi tutti i diritti di proprietà sulla tua musica e sui contenuti caricati su Beat Music. Caricando contenuti, concedi a Beat Music una licenza non esclusiva per distribuire e promuovere i tuoi contenuti per tuo conto sulle piattaforme da te selezionate.'],
                ['5. Utilizzo accettabile','Accetti di non caricare contenuti che violino copyright, marchi commerciali o altri diritti di proprietà intellettuale di terzi. Non puoi caricare contenuti espliciti senza unetichetta appropriata, codice dannoso o contenuti che violino le leggi applicabili.'],
                ['6. Ricavi e royalty','Beat Music distribuisce le royalty raccolte dalle piattaforme di streaming agli artisti. Le percentuali di royalty e le tempistiche di pagamento sono definite nel tuo piano di abbonamento. I pagamenti vengono elaborati entro 30 giorni dalla riscossione.'],
                ['7. Abbonamenti e pagamenti','Le quote di abbonamento vengono addebitate in base al piano selezionato. Puoi annullare labbonamento in qualsiasi momento. I rimborsi vengono forniti caso per caso a nostra discrezione.'],
                ['8. Risoluzione','Beat Music si riserva il diritto di chiudere o sospendere gli account che violano i presenti termini. In caso di risoluzione, la tua musica continuerà a essere distribuita per qualsiasi periodo di abbonamento rimanente.'],
                ['9. Esclusione di garanzie','Beat Music viene fornito "così comè" senza garanzie di alcun tipo. Non garantiamo numeri specifici di streaming, importi di fatturato o posizionamento su alcuna piattaforma.'],
                ['10. Modifiche ai Termini','Potremmo aggiornare i presenti termini in qualsiasi momento. Lutilizzo continuato del servizio dopo le modifiche costituisce accettazione dei nuovi termini. Informeremo gli utenti di eventuali modifiche significative via e-mail.'],
                ['11. Contatti','Per qualsiasi domanda sui presenti termini, contattaci allindirizzo legal@beatmusic.com'],
            ] as [$title, $content])
            <div>
                <h2 class="text-white font-semibold text-base mb-2">{{ $title }}</h2>
                <p>{{ $content }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
