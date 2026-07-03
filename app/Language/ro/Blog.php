<?php

/**
 * Blog language strings (ro)
 *
 * UI strings + post texts. Post texts are keyed by slug; the slug, date,
 * category and cover image live in the Blog controller registry so URLs
 * stay identical across locales.
 */
return [
    // SEO
    'meta_title'       => 'Blog',
    'meta_description' => 'Noutăți din industrie, sfaturi de întreținere, articole despre tehnologie și ghiduri de specialitate din lumea semiremorcilor și a cisternelor.',

    // Page hero
    'page_title' => 'Blog',
    'page_lead'  => 'Noutăți din industrie, sfaturi de întreținere și ghiduri cu fundament ingineresc.',

    // Sidebar
    'search_title'       => 'Căutare',
    'search_placeholder' => 'Caută articole pe blog...',
    'search_submit'      => 'Caută',
    'categories_title'   => 'Categorii',
    'all_posts'          => 'Toate articolele',

    // Cards and list states
    'read_more'        => 'Citește mai mult',
    'results_found'    => 'articole găsite',
    'no_results_title' => 'Niciun rezultat găsit',
    'no_results_text'  => 'Niciun articol nu corespunde criteriilor dumneavoastră de căutare. Încercați un alt cuvânt-cheie sau răsfoiți toate articolele.',
    'clear_filters'    => 'Afișează toate articolele',

    // Detail page
    'related_title'   => 'Articole similare',
    'related_eyebrow' => 'Continuați lectura',
    'back_to_blog'    => 'Înapoi la toate articolele',

    // Categories
    'cat_industry'    => 'Industrie',
    'cat_maintenance' => 'Întreținere și service',
    'cat_safety'      => 'Siguranță și ADR',
    'cat_technology'  => 'Tehnologie',

    // Date formatting
    'months' => ['ianuarie', 'februarie', 'martie', 'aprilie', 'mai', 'iunie', 'iulie', 'august', 'septembrie', 'octombrie', 'noiembrie', 'decembrie'],

    // Posts (slug => texts)
    'posts' => [
        'adr-tanker-safety-standards' => [
            'title'   => 'Standarde de siguranță la cisternele certificate ADR',
            'excerpt' => 'Cum modelează reglementările ADR proiectarea cisternelor pentru transportul mărfurilor periculoase și practicile esențiale de siguranță pe care fiecare operator ar trebui să le respecte.',
            'content' => '
                <p>Transportul rutier al mărfurilor periculoase este reglementat strict de acordul internațional ADR. Pentru cisternele care transportă carburanți, LPG sau produse chimice, fiecare etapă — de la proiectare și fabricație până la testare și inspecții periodice — se desfășoară în acest cadru de reglementare.</p>
                <h2>Cum modelează ADR proiectarea cisternelor</h2>
                <p>La o cisternă certificată ADR, materialul virolei, grosimea pereților, numărul de compartimente și dispunerea spargevalurilor se calculează în funcție de clasa produsului transportat. Rezistența la răsturnare, împământarea statică, vanele de închidere de urgență și sistemele de prevenire a supraumplerii sunt părți integrante ale proiectului. După producție, testele de presiune și de etanșeitate sunt verificate de organisme de inspecție independente.</p>
                <h2>La ce trebuie să fie atenți operatorii</h2>
                <p>Certificarea singură nu este suficientă; siguranța se menține prin utilizare corectă și inspecții regulate. Principalele priorități pentru operatori sunt:</p>
                <ul>
                    <li>Urmărirea termenelor inspecțiilor intermediare și periodice</li>
                    <li>Verificarea regulată a vanelor, garniturilor și echipamentelor de siguranță</li>
                    <li>Respectarea integrală a procedurilor de încărcare și descărcare</li>
                    <li>Menținerea la zi a certificatelor de pregătire ADR ale șoferilor</li>
                </ul>
                <p>Atunci când o cisternă bine proiectată se întâlnește cu o echipă de operare bine pregătită, transportul mărfurilor periculoase se poate desfășura atât în siguranță, cât și eficient.</p>
            ',
        ],

        'silo-trailer-maintenance-tips' => [
            'title'   => '7 sfaturi de întreținere care prelungesc viața semiremorcii siloz',
            'excerpt' => 'Pașii esențiali de întreținere care mențin semiremorcile siloz pentru mărfuri pulverulente și granulare funcționând eficient mai mult timp.',
            'content' => '
                <p>Semiremorcile siloz lucrează în condiții solicitante, transportând ciment, făină, PVC și produse pulverulente sau granulare similare. Fără o întreținere regulată, sistemul de descărcare sub presiune și conductele de aer devin prima sursă de pierderi de eficiență și de defecțiuni.</p>
                <h2>De ce contează întreținerea planificată</h2>
                <p>Întreținerea planificată face mai mult decât să reducă riscul de defectare: scurtează timpii de descărcare, reduce consumul de carburant și protejează valoarea de revânzare. Pașii de mai jos previn majoritatea problemelor întâlnite în exploatare:</p>
                <ul>
                    <li>Verificați conductele de aer și burdufurile pentru scurgeri înainte de fiecare cursă</li>
                    <li>Controlați regulat nivelul de ulei al compresorului și filtrele</li>
                    <li>Monitorizați uzura garniturilor vanelor de descărcare</li>
                    <li>Testați închizătorile și garniturile gurilor de umplere superioare</li>
                    <li>Curățați interiorul rezervorului la fiecare schimbare de produs</li>
                    <li>Solicitați testarea supapei de siguranță cel puțin o dată pe an</li>
                    <li>Restrângeți periodic la cuplul prescris îmbinările șasiului și ale osiilor</li>
                </ul>
                <h2>Asistență service profesionistă</h2>
                <p>Dacă verificările zilnice pot fi făcute de operator, sistemele sub presiune, supapele de siguranță și inspecțiile structurale trebuie efectuate de echipe de service specializate. Utilizarea pieselor de schimb aprobate de producător este cea mai sigură cale de a păstra performanța originală a sistemului.</p>
            ',
        ],

        'lowbed-heavy-transport-guide' => [
            'title'   => 'Alegerea semiremorcii coborâte potrivite pentru transportul greu',
            'excerpt' => 'Criteriile de care trebuie să țineți cont atunci când configurați semiremorca coborâtă (lowbed) potrivită pentru transportul utilajelor de construcții și al echipamentelor grele.',
            'content' => '
                <p>Deplasarea utilajelor de construcții, a generatoarelor, transformatoarelor și echipamentelor industriale cere soluții dincolo de limitele semiremorcilor standard. Cu înălțimea lor redusă de încărcare și capacitatea mare de sarcină utilă, semiremorcile coborâte (lowbed) sunt instrumentul critic al acestui segment.</p>
                <h2>Definirea configurației potrivite</h2>
                <p>Prima întrebare în alegerea unui lowbed este mereu aceeași: ce se va transporta? Greutatea, dimensiunile și centrul de greutate al mărfii determină fiecare decizie tehnică — de la numărul de osii la lungimea platformei, tipul rampelor și necesitatea lărgirii. Alături de modelele fixe cu două, trei și patru osii, sunt disponibile și soluții modulare, cu platformă extensibilă.</p>
                <h2>Criterii esențiale de selecție</h2>
                <ul>
                    <li>Sarcina utilă maximă și greutatea pe osie</li>
                    <li>Înălțimea de încărcare și lungimea patului coborât</li>
                    <li>Opțiuni de rampe hidraulice sau cu arc</li>
                    <li>Necesitatea osiilor autodirectoare</li>
                    <li>Restricțiile de poduri și tuneluri de pe traseu</li>
                </ul>
                <p>Un lowbed configurat corect face mai mult decât să transporte sarcina: îmbunătățește siguranța operațională, simplifică procedurile de autorizare a transportului și reduce pe termen lung costurile de exploatare. Colaborarea cu echipa de inginerie a producătorului în etapa de selecție oferă cel mai bun rezultat.</p>
            ',
        ],

        'lightweight-chassis-technology' => [
            'title'   => 'Tehnologia șasiurilor ușoare: greutate mai mică, sarcină utilă mai mare',
            'excerpt' => 'Avantajele operaționale ale reducerii masei proprii cu oțeluri de înaltă rezistență și un șasiu cu proiectare optimizată.',
            'content' => '
                <p>Cu limitele legale de masă totală fixe în transportul rutier, câștigul vine din masa proprie a semiremorcii. Fiecare kilogram economisit la șasiu se transformă direct în sarcină utilă disponibilă — și în venituri mai mari pe cursă.</p>
                <h2>Rolul oțelurilor de înaltă rezistență</h2>
                <p>În fabricația modernă de semiremorci, oțelurile de înaltă rezistență înlocuiesc tot mai mult mărcile structurale standard. Aceste materiale oferă o rezistență egală sau superioară cu secțiuni mai subțiri. Combinate cu proceduri de sudură corecte și tehnici de producție precise, ele permit o reducere semnificativă a greutății șasiului, păstrând în același timp durata de viață la oboseală.</p>
                <h2>Optimizarea proiectării</h2>
                <p>Materialul singur nu este suficient. Analiza cu element finit (FEA) modelează distribuția tensiunilor în șasiu, astfel încât materialul să fie folosit doar acolo unde este necesar. Geometria lonjeroanelor principale, poziționarea traverselor și detaliile îmbinărilor sunt toate optimizate pe baza acestor analize.</p>
                <p>Rezultatul este un consum mai mic de carburant, mai multă sarcină utilă și emisii de carbon reduse. Reducerea greutății este unul dintre cele mai importante subiecte ale ingineriei de semiremorci de astăzi — atât economic, cât și ecologic.</p>
            ',
        ],

        'reefer-cold-chain-logistics' => [
            'title'   => 'Tehnologia semiremorcilor frigorifice în logistica lanțului de frig',
            'excerpt' => 'Soluții moderne de semiremorci frigorifice care mențin lanțul de frig neîntrerupt în transportul de alimente și produse farmaceutice.',
            'content' => '
                <p>La transportul produselor sensibile la temperatură, precum alimentele și medicamentele, cea mai mică abatere poate duce la pierderea produsului și la riscuri serioase pentru sănătate. Semiremorcile frigorifice — piatra de temelie a logisticii lanțului de frig — mențin această operațiune sensibilă în funcțiune fără întrerupere.</p>
                <h2>Calitatea izolației este pe primul loc</h2>
                <p>Performanța unei caroserii frigorifice este determinată de calitatea izolației înaintea agregatului de răcire. Sistemele de panouri umplute cu poliuretan de înaltă densitate, combinate cu detalii de îmbinare care reduc la minimum punțile termice, permit agregatului să lucreze mai puțin și reduc consumul de carburant. Garniturile ușilor și construcția podelei joacă de asemenea un rol decisiv în pierderile totale de căldură.</p>
                <h2>Monitorizare și înregistrarea datelor</h2>
                <p>În operațiunile moderne de lanț de frig, urmărirea temperaturii nu se mai limitează la afișajul din cabină. Sistemele de telemetrie în timp real fac posibilă monitorizarea de la distanță a temperaturii caroseriei, a deschiderilor de uși și a performanței de refrigerare. Aceste date devin tot mai importante atât pentru conformitatea cu reglementările, cât și ca dovadă de calitate pentru clienți.</p>
                <p>Atunci când izolația corespunzătoare, un agregat de răcire fiabil și monitorizarea continuă se întâlnesc, lanțul de frig rămâne protejat chiar și pe cele mai lungi trasee.</p>
            ',
        ],

        'trailer-winter-maintenance' => [
            'title'   => 'Pregătirea pentru iarnă: lista de verificare pentru întreținerea semiremorcii',
            'excerpt' => 'O listă de verificare cuprinzătoare pentru pregătirea flotei de semiremorci împotriva temperaturilor scăzute, a sării de pe drumuri și a condițiilor aspre de iarnă.',
            'content' => '
                <p>Lunile de iarnă — cu temperaturi scăzute, sare pe carosabil și suprafețe dificile — sunt perioada cea mai solicitantă a anului pentru semiremorci. O pregătire planificată înainte de începerea sezonului previne imobilizările pe marginea drumului în plină iarnă.</p>
                <h2>Frânele și sistemele de aer</h2>
                <p>Uscătorul de aer devine critic pe vreme rece: dacă filtrul-cartuș nu este înlocuit la timp, umezeala acumulată în conducte poate îngheța și bloca sistemul de frânare. Plăcuțele de frână, discurile și dispozitivele automate de reglare a jocului trebuie verificate înainte de sezon.</p>
                <h2>Lista de verificare înainte de sezon</h2>
                <ul>
                    <li>Înlocuiți cartușul uscătorului de aer și goliți rezervoarele</li>
                    <li>Verificați grosimea plăcuțelor de frână și suprafețele discurilor</li>
                    <li>Măsurați adâncimea profilului anvelopelor și presiunile</li>
                    <li>Inspectați iluminatul și conexiunile electrice</li>
                    <li>Spălați șasiul și aplicați protecție împotriva coroziunii cauzate de sare</li>
                    <li>Gresați balamalele ușilor și încuietorile</li>
                </ul>
                <p>Pregătirea regulată pentru iarnă este cea mai ieftină cale de a crește disponibilitatea flotei și de a prelungi durata de viață a vehiculelor. Rețeaua noastră de service autorizat vă stă la dispoziție cu inspecții sezoniere complete.</p>
            ',
        ],
    ],
];
