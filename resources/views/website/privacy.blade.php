@extends('layouts.app')

@section('title', 'Privacy Policy — Beat Music')

@section('content')
<section class="pt-32 pb-24 px-4">
    <div class="max-w-3xl mx-auto">
        <div class="mb-10">
            <h1 class="text-4xl font-bold text-white mb-2">politica sulla riservatezza</h1>
            <p class="text-gray-400 text-sm">Ultimo aggiornamento: 1 gennaio 2024</p>
        </div>
        <div class="glass rounded-2xl p-8 border border-white/5 space-y-8 text-gray-300 text-sm leading-relaxed">
            @foreach([
                ['1. Informazioni che raccogliamo', 'Raccogliamo le informazioni che fornisci direttamente, come nome, indirizzo email, informazioni di pagamento e contenuti caricati. Raccogliamo inoltre dati di utilizzo, informazioni sul dispositivo e cookie per migliorare il nostro servizio.'],
                ['2. Come utilizziamo le tue informazioni','Utilizziamo le tue informazioni per fornire e migliorare i nostri servizi, elaborare pagamenti, inviare notifiche importanti, personalizzare la tua esperienza e rispettare gli obblighi di legge. Non vendiamo i tuoi dati personali a terzi.'],
                ['3. Condivisione dei dati','Condividiamo le tue informazioni con servizi di terze parti affidabili necessari per gestire la nostra piattaforma, inclusi processori di pagamento (Stripe, PayPal), fornitori di archiviazione cloud e piattaforme di streaming digitale per scopi di distribuzione.'],
                ['4. Sicurezza dei dati','Implementiamo misure di sicurezza standard del settore, tra cui crittografia SSL, archiviazione sicura dei dati e controlli di sicurezza regolari per proteggere le tue informazioni personali.'],
                ['5. Cookie','Utilizziamo cookie e tecnologie di tracciamento simili per migliorare la tua esperienza. Puoi controllare le impostazioni dei cookie tramite le preferenze del tuo browser. La disabilitazione dei cookie potrebbe influire su alcune funzionalità.'],
                ['6. I tuoi diritti', 'Hai il diritto di accedere, correggere o cancellare i tuoi dati personali. Puoi richiedere una copia dei tuoi dati, annullare liscrizione alle comunicazioni di marketing e richiedere la portabilità dei dati. Contattaci per esercitare questi diritti.'],
                ['7. Conservazione dei dati','Conserviamo i tuoi dati per tutto il tempo in cui il tuo account è attivo e come richiesto dalla legge. Puoi richiedere la cancellazione del tuo account e dei dati associati in qualsiasi momento.'],
                ['8. Privacy dei bambini','Il nostro servizio non è rivolto a bambini di età inferiore a 13 anni. Non raccogliamo consapevolmente informazioni personali da bambini di età inferiore a 13 anni.'],
                ['9. Trasferimenti internazionali','I tuoi dati potrebbero essere trasferiti ed elaborati in Paesi diversi dal tuo. Garantiamo che siano in atto adeguate garanzie per tali trasferimenti.'],
                ['10. Modifiche alla presente Informativa','Potremmo aggiornare periodicamente la presente informativa sulla privacy. Ti informeremo di eventuali modifiche significative tramite e-mail o tramite un avviso ben visibile sul nostro sito web.'],
                ['11. Contattaci','Per domande o richieste relative alla privacy, contatta il nostro responsabile della protezione dei dati allindirizzo privacy@beatmusic.com'],
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
