@extends('layouts.app')

@section('title', 'Cookie Policy — Beat Music')

@section('content')
<section class="pt-32 pb-24 px-4">
    <div class="max-w-3xl mx-auto">
        <div class="mb-10">
            <h1 class="text-4xl font-bold text-white mb-2">Politica sui cookie</h1>
            <p class="text-gray-400 text-sm">Ultimo aggiornamento: 1 gennaio 2024</p>
        </div>
        <div class="glass rounded-2xl p-8 border border-white/5 space-y-8 text-gray-300 text-sm leading-relaxed">
            <div>
                <h2 class="text-white font-semibold text-base mb-2">Cosa sono i cookie?</h2>
                <p>I cookie sono piccoli file di testo memorizzati sul tuo dispositivo quando visiti un sito web. Aiutano i siti web a ricordare informazioni sulla tua visita, rendendo più semplice la tua visita successiva e il sito più utile per te.</p>
            </div>
            <div>
                <h2 class="text-white font-semibold text-base mb-2">Come utilizziamo i cookie</h2>
                <p class="mb-3">Beat Music utilizza i cookie per i seguenti scopi:</p>
                <div class="space-y-3">
                    @foreach([
                        ['Cookie essenziali', 'Necessari per il corretto funzionamento del sito web. Non possono essere disattivati. Includono i cookie di sessione per laccesso e i cookie di sicurezza.'],
                        ['Cookie analitici', 'Ci aiutano a capire come i visitatori interagiscono con il nostro sito web. Utilizziamo questi dati per migliorare il nostro servizio. Questi cookie sono anonimi.'],
                        ['Cookie di preferenza','Ricorda le tue impostazioni e preferenze, come la lingua preferita o le impostazioni di visualizzazione.'],
                        ['Cookie di marketing', 'Utilizzati per fornire annunci pubblicitari pertinenti e monitorare l'efficacia delle nostre campagne di marketing. Puoi disattivare l'utilizzo in qualsiasi momento.'],
                    ] as [$type, $desc])
                    <div class="bg-white/3 rounded-xl p-4 border border-white/5">
                        <p class="text-white text-sm font-medium mb-1">{{ $type }}</p>
                        <p class="text-gray-400 text-sm">{{ $desc }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            <div>
                <h2 class="text-white font-semibold text-base mb-2">Gestione dei cookie</h2>
                <p>Puoi controllare e gestire i cookie tramite le impostazioni del tuo browser. Tieni presente che la disattivazione di alcuni cookie potrebbe influire sulla funzionalità del nostro sito web. La maggior parte dei browser consente di visualizzare, eliminare e bloccare i cookie di siti specifici.</p>
            </div>
            <div>
                <h2 class="text-white font-semibold text-base mb-2">Cookie di terze parti</h2>
                <p>Potremmo utilizzare servizi di terze parti come Google Analytics, Stripe e piattaforme di social media che impostano i propri cookie. Non controlliamo questi cookie e sono regolati dalle informative sulla privacy di tali servizi.</p>
            </div>
            <div>
                <h2 class="text-white font-semibold text-base mb-2">Contatto</h2>
                <p>Se hai domande sulla nostra politica sui cookie, contattaci all'indirizzo privacy@beatmusic.com</p>
            </div>
        </div>
    </div>
</section>
@endsection
