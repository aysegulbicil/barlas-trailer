<?php

/**
 * Blog testi lingua (it)
 *
 * Testi UI + testi degli articoli. Gli articoli sono indicizzati per slug;
 * slug, data, categoria e copertina stanno nel registro del controller
 * Blog, così gli URL restano identici in tutte le lingue.
 */
return [
    // SEO
    'meta_title'       => 'Blog',
    'meta_description' => 'Notizie di settore, consigli di manutenzione, articoli di tecnologia e guide esperte dal mondo dei rimorchi e delle cisterne.',

    // Hero di pagina
    'page_title' => 'Blog',
    'page_lead'  => 'Notizie di settore, consigli di manutenzione e guide a guida ingegneristica.',

    // Barra laterale
    'search_title'       => 'Cerca',
    'search_placeholder' => 'Cerca negli articoli...',
    'search_submit'      => 'Cerca',
    'categories_title'   => 'Categorie',
    'all_posts'          => 'Tutti gli articoli',

    // Schede e stati elenco
    'read_more'        => 'Leggi di più',
    'results_found'    => 'articoli trovati',
    'no_results_title' => 'Nessun risultato',
    'no_results_text'  => 'Nessun articolo corrisponde alla ricerca. Prova un\'altra parola chiave o sfoglia tutti gli articoli.',
    'clear_filters'    => 'Mostra tutti gli articoli',

    // Pagina di dettaglio
    'related_title'   => 'Articoli correlati',
    'related_eyebrow' => 'Continua a leggere',
    'back_to_blog'    => 'Torna a tutti gli articoli',

    // Categorie
    'cat_industry'    => 'Settore',
    'cat_maintenance' => 'Manutenzione e assistenza',
    'cat_safety'      => 'Sicurezza e ADR',
    'cat_technology'  => 'Tecnologia',

    // Formattazione data
    'months' => ['gennaio', 'febbraio', 'marzo', 'aprile', 'maggio', 'giugno', 'luglio', 'agosto', 'settembre', 'ottobre', 'novembre', 'dicembre'],

    // Articoli (slug => testi)
    'posts' => [
        'adr-tanker-safety-standards' => [
            'title'   => 'Standard di sicurezza nelle cisterne certificate ADR',
            'excerpt' => 'Come le norme ADR plasmano la progettazione delle cisterne per il trasporto di merci pericolose, e le pratiche di sicurezza che ogni operatore deve seguire.',
            'content' => '
                <p>Il trasporto su strada di merci pericolose è rigorosamente disciplinato dall\'accordo internazionale ADR. Per le cisterne che trasportano carburante, GPL o prodotti chimici, ogni fase — dalla progettazione e produzione fino ai collaudi e alle ispezioni periodiche — si svolge entro questo quadro normativo.</p>
                <h2>Come l\'ADR plasma la progettazione della cisterna</h2>
                <p>In una cisterna certificata ADR, materiale del serbatoio, spessore delle pareti, numero di scomparti e disposizione dei frangiflutti sono calcolati in base alla classe del prodotto trasportato. Resistenza al ribaltamento, messa a terra statica, valvole di chiusura di emergenza e sistemi anti-troppopieno sono parte integrante del progetto. Dopo la produzione, le prove di pressione e tenuta sono verificate da organismi di ispezione indipendenti.</p>
                <h2>Cosa devono presidiare gli operatori</h2>
                <p>La certificazione da sola non basta; la sicurezza si mantiene con l\'uso corretto e le ispezioni regolari. Le priorità principali per gli operatori sono:</p>
                <ul>
                    <li>Tenere sotto controllo le scadenze delle ispezioni intermedie e periodiche</li>
                    <li>Controllare regolarmente valvole, guarnizioni e dispositivi di sicurezza</li>
                    <li>Rispettare integralmente le procedure di carico e scarico</li>
                    <li>Mantenere aggiornati i certificati di formazione ADR degli autisti</li>
                </ul>
                <p>Quando una cisterna ben progettata incontra un team operativo ben formato, il trasporto di merci pericolose può essere svolto in modo sicuro ed efficiente.</p>
            ',
        ],

        'silo-trailer-maintenance-tips' => [
            'title'   => '7 consigli di manutenzione che allungano la vita del semirimorchio silo',
            'excerpt' => 'I passaggi di manutenzione essenziali per far lavorare più a lungo e con efficienza i silo che trasportano carichi in polvere e granuli.',
            'content' => '
                <p>I semirimorchi silo lavorano in condizioni impegnative, trasportando cemento, farina, PVC e prodotti simili in polvere o granuli. Senza manutenzione regolare, il sistema di scarico pressurizzato e le linee dell\'aria diventano la prima fonte di perdita di efficienza e guasti.</p>
                <h2>Perché conta la manutenzione programmata</h2>
                <p>La manutenzione programmata non riduce solo il rischio di guasto: accorcia i tempi di scarico, abbassa i consumi e protegge il valore dell\'usato. I seguenti passaggi prevengono la maggior parte dei problemi sul campo:</p>
                <ul>
                    <li>Verifica di linee dell\'aria e soffietti prima di ogni viaggio</li>
                    <li>Controllo regolare di livello olio e filtri del compressore</li>
                    <li>Monitoraggio dell\'usura delle guarnizioni delle valvole di scarico</li>
                    <li>Prova di chiusure e tenute dei boccaporti di carico superiori</li>
                    <li>Pulizia dell\'interno del serbatoio a ogni cambio prodotto</li>
                    <li>Collaudo della valvola di sicurezza almeno una volta l\'anno</li>
                    <li>Ripresa periodica dei serraggi di telaio e assali</li>
                </ul>
                <h2>Supporto di assistenza professionale</h2>
                <p>I controlli quotidiani possono farli gli operatori; impianti in pressione, valvole di sicurezza e ispezioni strutturali spettano a team di assistenza specializzati. Usare ricambi approvati dal costruttore è il modo più affidabile di preservare le prestazioni originali del sistema.</p>
            ',
        ],

        'lowbed-heavy-transport-guide' => [
            'title'   => 'Scegliere il pianale ribassato giusto per i trasporti pesanti',
            'excerpt' => 'I criteri da considerare per definire la configurazione giusta di un ribassato per il trasporto di macchine da cantiere e attrezzature pesanti.',
            'content' => '
                <p>Spostare macchine da cantiere, generatori, trasformatori e impianti industriali richiede soluzioni oltre i limiti dei rimorchi standard. Con l\'altezza di carico ridotta e l\'elevata portata, i pianali ribassati sono lo strumento decisivo di questo segmento.</p>
                <h2>Definire la configurazione giusta</h2>
                <p>La prima domanda nella scelta di un ribassato è sempre la stessa: cosa si trasporta? Peso, dimensioni e baricentro del carico determinano ogni decisione tecnica — dal numero di assi alla lunghezza del piano, dal tipo di rampe alla necessità di allargamento. Accanto ai modelli fissi a due, tre e quattro assi esistono anche soluzioni modulari e a piano estensibile.</p>
                <h2>Criteri chiave di scelta</h2>
                <ul>
                    <li>Portata massima e peso per asse</li>
                    <li>Altezza di carico e lunghezza del piano ribassato</li>
                    <li>Opzioni di rampe idrauliche o assistite a molla</li>
                    <li>Necessità di assi autosterzanti</li>
                    <li>Restrizioni di ponti e gallerie lungo il percorso</li>
                </ul>
                <p>Un ribassato configurato correttamente non si limita a portare il carico: migliora la sicurezza operativa, semplifica i permessi di trasporto e riduce i costi di esercizio nel lungo periodo. Lavorare con il team di ingegneria del costruttore nella fase di scelta dà il risultato migliore.</p>
            ',
        ],

        'lightweight-chassis-technology' => [
            'title'   => 'Tecnologia dei telai leggeri: meno peso, più portata',
            'excerpt' => 'I vantaggi operativi della riduzione della tara con acciai ad alta resistenza e un design del telaio ottimizzato.',
            'content' => '
                <p>Con i limiti di massa complessiva fissati per legge nel trasporto su strada, il guadagno arriva dalla tara del rimorchio. Ogni chilogrammo risparmiato sul telaio si converte direttamente in portata utile — e in maggiori ricavi per viaggio.</p>
                <h2>Il ruolo degli acciai ad alta resistenza</h2>
                <p>Nella produzione moderna di rimorchi, gli acciai ad alta resistenza stanno sostituendo sempre più i gradi strutturali standard. Questi materiali offrono resistenza pari o superiore con sezioni più sottili. Abbinati a procedure di saldatura corrette e a tecniche di produzione precise, consentono una riduzione significativa del peso del telaio preservando la vita a fatica.</p>
                <h2>Ottimizzazione del progetto</h2>
                <p>Il materiale da solo non basta. L\'analisi agli elementi finiti (FEA) modella la distribuzione delle tensioni sul telaio, così il materiale viene usato solo dove serve. Geometria dei longheroni, posizione delle traverse e dettagli dei collegamenti vengono ottimizzati sulla base di queste analisi.</p>
                <p>Il risultato: meno consumi, più portata e minori emissioni di CO₂. La riduzione del peso è oggi uno dei temi più importanti dell\'ingegneria dei rimorchi — sul piano economico e ambientale.</p>
            ',
        ],

        'reefer-cold-chain-logistics' => [
            'title'   => 'La tecnologia dei semirimorchi frigo nella logistica della catena del freddo',
            'excerpt' => 'Le moderne soluzioni frigorifere che mantengono ininterrotta la catena del freddo nel trasporto alimentare e farmaceutico.',
            'content' => '
                <p>Nel trasporto di prodotti sensibili alla temperatura come alimenti e farmaci, la minima deviazione può causare perdita di prodotto e seri rischi per la salute. I semirimorchi frigoriferi — pietra angolare della logistica del freddo — mantengono questa operazione delicata senza interruzioni.</p>
                <h2>La qualità dell\'isolamento viene prima</h2>
                <p>Le prestazioni di una cassa frigorifera dipendono dalla qualità dell\'isolamento prima ancora che dal gruppo frigo. I pannelli riempiti di poliuretano ad alta densità, uniti a dettagli di giunzione che minimizzano i ponti termici, fanno lavorare meno il gruppo e riducono i consumi. Anche guarnizioni delle porte e costruzione del pavimento incidono in modo decisivo sulle dispersioni totali.</p>
                <h2>Monitoraggio e registrazione dei dati</h2>
                <p>Nelle moderne operazioni della catena del freddo, il controllo della temperatura non si limita più al display in cabina. I sistemi di telemetria in tempo reale permettono di monitorare a distanza temperatura della cassa, aperture delle porte e prestazioni del frigo. Questi dati contano sempre di più sia per la conformità normativa sia come prova di qualità verso i clienti.</p>
                <p>Quando isolamento corretto, gruppo frigo affidabile e monitoraggio continuo si combinano, la catena del freddo resta protetta anche sulle rotte più lunghe.</p>
            ',
        ],

        'trailer-winter-maintenance' => [
            'title'   => 'Prepararsi all\'inverno: la checklist di manutenzione del rimorchio',
            'excerpt' => 'Una checklist completa per preparare la flotta a basse temperature, sale stradale e condizioni invernali difficili.',
            'content' => '
                <p>I mesi invernali — con basse temperature, sale stradale e fondi difficili — sono il periodo più duro dell\'anno per i rimorchi. Una preparazione pianificata prima della stagione evita i fermi su strada in pieno inverno.</p>
                <h2>Freni e impianti pneumatici</h2>
                <p>L\'essiccatore d\'aria diventa critico col freddo: se la cartuccia non viene sostituita in tempo, l\'umidità accumulata nelle linee può gelare e bloccare l\'impianto frenante. Pastiglie, dischi e registri automatici vanno controllati prima della stagione.</p>
                <h2>Checklist pre-stagione</h2>
                <ul>
                    <li>Sostituire la cartuccia dell\'essiccatore e spurgare i serbatoi</li>
                    <li>Controllare lo spessore delle pastiglie e le superfici dei dischi</li>
                    <li>Misurare profondità del battistrada e pressioni</li>
                    <li>Ispezionare luci e collegamenti elettrici</li>
                    <li>Lavare il telaio e applicare la protezione anticorrosione dal sale</li>
                    <li>Ingrassare cerniere e serrature delle porte</li>
                </ul>
                <p>La preparazione invernale regolare è il modo più economico di aumentare la disponibilità della flotta e allungare la vita dei veicoli. La nostra rete di assistenza autorizzata è pronta con ispezioni stagionali complete.</p>
            ',
        ],
    ],
];
