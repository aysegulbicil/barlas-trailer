<?php

/**
 * Blog taalteksten (nl)
 *
 * UI-teksten + artikelteksten. Artikelteksten zijn op slug geïndexeerd;
 * slug, datum, categorie en omslagfoto staan in het Blog-controller-
 * register zodat URL's in alle talen identiek blijven.
 */
return [
    // SEO
    'meta_title'       => 'Blog',
    'meta_description' => 'Sectornieuws, onderhoudstips, technologie-artikelen en expertgidsen uit de wereld van trailers en tankopleggers.',

    // Paginahero
    'page_title' => 'Blog',
    'page_lead'  => 'Sectornieuws, onderhoudstips en engineering-gedreven gidsen.',

    // Zijbalk
    'search_title'       => 'Zoeken',
    'search_placeholder' => 'Blogberichten zoeken...',
    'search_submit'      => 'Zoeken',
    'categories_title'   => 'Categorieën',
    'all_posts'          => 'Alle berichten',

    // Kaarten en lijststatussen
    'read_more'        => 'Lees verder',
    'results_found'    => 'berichten gevonden',
    'no_results_title' => 'Geen resultaten',
    'no_results_text'  => 'Geen berichten voldoen aan uw zoekopdracht. Probeer een ander trefwoord of bekijk alle berichten.',
    'clear_filters'    => 'Alle berichten tonen',

    // Detailpagina
    'related_title'   => 'Gerelateerde berichten',
    'related_eyebrow' => 'Lees verder',
    'back_to_blog'    => 'Terug naar alle berichten',

    // Categorieën
    'cat_industry'    => 'Sector',
    'cat_maintenance' => 'Onderhoud & service',
    'cat_safety'      => 'Veiligheid & ADR',
    'cat_technology'  => 'Technologie',

    // Datumnotatie
    'months' => ['januari', 'februari', 'maart', 'april', 'mei', 'juni', 'juli', 'augustus', 'september', 'oktober', 'november', 'december'],

    // Berichten (slug => teksten)
    'posts' => [
        'adr-tanker-safety-standards' => [
            'title'   => 'Veiligheidsnormen bij ADR-gecertificeerde tankopleggers',
            'excerpt' => 'Hoe de ADR-regels het tankontwerp voor gevaarlijkgoedvervoer bepalen, en de belangrijkste veiligheidspraktijken voor elke vervoerder.',
            'content' => '
                <p>Het wegvervoer van gevaarlijke stoffen wordt strikt geregeld door het internationale ADR-verdrag. Bij tankopleggers voor brandstof, LPG of chemicaliën verloopt elke fase — van ontwerp en productie tot testen en periodieke keuring — binnen dit regelgevend kader.</p>
                <h2>Hoe ADR het tankontwerp bepaalt</h2>
                <p>Bij een ADR-gecertificeerde tank worden het tankmateriaal, de wanddikte, het aantal compartimenten en de indeling van de slingerschotten berekend op basis van de klasse van het vervoerde product. Kantelweerstand, statische aarding, noodafsluiters en overvulbeveiliging zijn integrale onderdelen van het ontwerp. Na productie worden druk- en lekdichtheidstests geverifieerd door onafhankelijke keuringsinstanties.</p>
                <h2>Waar vervoerders op moeten letten</h2>
                <p>Certificering alleen is niet genoeg; veiligheid blijft behouden door juist gebruik en regelmatige keuring. De belangrijkste prioriteiten voor vervoerders:</p>
                <ul>
                    <li>Tussentijdse en periodieke keuringstermijnen bewaken</li>
                    <li>Kleppen, pakkingen en veiligheidsuitrusting regelmatig controleren</li>
                    <li>Laad- en losprocedures volledig naleven</li>
                    <li>ADR-opleidingscertificaten van chauffeurs actueel houden</li>
                </ul>
                <p>Wanneer een goed ontworpen tank samenkomt met een goed opgeleid operationeel team, kan gevaarlijkgoedvervoer veilig én efficiënt verlopen.</p>
            ',
        ],

        'silo-trailer-maintenance-tips' => [
            'title'   => '7 onderhoudstips die de levensduur van uw silo-oplegger verlengen',
            'excerpt' => 'De essentiële onderhoudsstappen die silo-opleggers voor poeder- en granulaatladingen langer efficiënt laten draaien.',
            'content' => '
                <p>Silo-opleggers werken onder zware omstandigheden met cement, meel, pvc en vergelijkbare poeder- of granulaatproducten. Zonder regelmatig onderhoud worden het druklossysteem en de luchtleidingen de eerste bron van efficiëntieverlies en storingen.</p>
                <h2>Waarom gepland onderhoud loont</h2>
                <p>Gepland onderhoud doet meer dan het storingsrisico verlagen: het verkort de lostijden, verlaagt het brandstofverbruik en beschermt de restwaarde. De volgende stappen voorkomen de meeste problemen in de praktijk:</p>
                <ul>
                    <li>Controleer luchtleidingen en balgen vóór elke rit op lekkage</li>
                    <li>Inspecteer oliepeil en filters van de compressor regelmatig</li>
                    <li>Houd de pakkingen van de loskleppen in de gaten</li>
                    <li>Test de sluitingen en afdichtingen van de vulluiken</li>
                    <li>Reinig het tankinterieur bij elke productwissel</li>
                    <li>Laat het veiligheidsventiel minstens één keer per jaar testen</li>
                    <li>Trek chassis- en asverbindingen periodiek na</li>
                </ul>
                <h2>Professionele serviceondersteuning</h2>
                <p>Dagelijkse controles kan de vervoerder zelf uitvoeren; druksystemen, veiligheidsventielen en structurele inspecties horen bij gespecialiseerde serviceteams. Fabrieksgoedgekeurde onderdelen zijn de betrouwbaarste manier om de oorspronkelijke prestaties te behouden.</p>
            ',
        ],

        'lowbed-heavy-transport-guide' => [
            'title'   => 'De juiste dieplader kiezen voor zwaar transport',
            'excerpt' => 'De criteria voor de juiste diepladerconfiguratie bij het vervoer van bouwmachines en zwaar materieel.',
            'content' => '
                <p>Het verplaatsen van bouwmachines, generatoren, transformatoren en industriële installaties vraagt om oplossingen die de grenzen van standaardtrailers overstijgen. Met hun lage laadhoogte en hoge laadcapaciteit zijn diepladers het cruciale gereedschap in dit segment.</p>
                <h2>De juiste configuratie bepalen</h2>
                <p>De eerste vraag bij diepladerkeuze is altijd dezelfde: wat wordt er vervoerd? Gewicht, afmetingen en zwaartepunt van de lading bepalen elke technische keuze — van het aantal assen tot de platformlengte, het rampetype en de behoefte aan verbreding. Naast vaste twee-, drie- en vierassers bestaan er ook modulaire en uitschuifbare platformoplossingen.</p>
                <h2>Belangrijkste selectiecriteria</h2>
                <ul>
                    <li>Maximale laadcapaciteit en gewicht per as</li>
                    <li>Laadhoogte en lengte van het laadbed</li>
                    <li>Hydraulische of veerondersteunde rijplaten</li>
                    <li>Behoefte aan zelfsturende assen</li>
                    <li>Brug- en tunnelbeperkingen op de route</li>
                </ul>
                <p>Een juist geconfigureerde dieplader draagt niet alleen de lading: hij verbetert de operationele veiligheid, vereenvoudigt vergunningstrajecten en verlaagt op termijn de exploitatiekosten. Samenwerken met het engineeringteam van de fabrikant levert het beste resultaat.</p>
            ',
        ],

        'lightweight-chassis-technology' => [
            'title'   => 'Lichtgewicht chassistechnologie: minder gewicht, meer laadvermogen',
            'excerpt' => 'De operationele voordelen van een lager leeggewicht dankzij hogesterktestaal en een geoptimaliseerd chassisontwerp.',
            'content' => '
                <p>Omdat de wettelijke totaalgewichten in het wegvervoer vastliggen, zit de winst in het leeggewicht van de trailer. Elke kilogram die op het chassis wordt bespaard, wordt direct bruikbaar laadvermogen — en meer omzet per rit.</p>
                <h2>De rol van hogesterktestaal</h2>
                <p>In de moderne trailerbouw vervangt hogesterktestaal steeds vaker de standaard constructiestaalsoorten. Deze materialen leveren gelijke of betere sterkte met dunnere secties. Gecombineerd met juiste lasprocedures en precieze productietechnieken maken ze een aanzienlijke gewichtsbesparing mogelijk met behoud van de vermoeiingslevensduur.</p>
                <h2>Ontwerpoptimalisatie</h2>
                <p>Materiaal alleen is niet genoeg. Eindige-elementenanalyse (FEA) modelleert de spanningsverdeling in het chassis, zodat materiaal alleen wordt gebruikt waar het nodig is. De geometrie van de hoofdliggers, de plaatsing van dwarsbalken en de verbindingsdetails worden op basis van deze analyses geoptimaliseerd.</p>
                <p>Het resultaat: lager brandstofverbruik, meer laadvermogen en minder CO₂-uitstoot. Gewichtsreductie is vandaag — economisch én ecologisch — een van de belangrijkste thema\'s in trailer-engineering.</p>
            ',
        ],

        'reefer-cold-chain-logistics' => [
            'title'   => 'Koeltrailertechnologie in de koelketenlogistiek',
            'excerpt' => 'Moderne koeloplegger-oplossingen die de koelketen in voedsel- en farmatransport ononderbroken houden.',
            'content' => '
                <p>Bij het vervoer van temperatuurgevoelige producten zoals voeding en farmaceutica kan de kleinste afwijking leiden tot productverlies en ernstige gezondheidsrisico\'s. Koelopleggers — de hoeksteen van de koelketenlogistiek — houden deze gevoelige operatie zonder onderbreking draaiende.</p>
                <h2>Isolatiekwaliteit eerst</h2>
                <p>De prestaties van een koelopbouw worden vóór het koelaggregaat bepaald door de isolatiekwaliteit. Panelen met hoogwaardige polyurethaanvulling, gecombineerd met naaddetails die koudebruggen minimaliseren, laten het aggregaat minder werken en verlagen het brandstofverbruik. Ook deurrubbers en vloeropbouw spelen een beslissende rol in het totale warmteverlies.</p>
                <h2>Monitoring en datalogging</h2>
                <p>In moderne koelketenoperaties beperkt temperatuurbewaking zich niet meer tot het display in de cabine. Realtime telemetrie maakt het mogelijk om opbouwtemperatuur, deuropeningen en koelprestaties op afstand te volgen. Deze data wordt steeds belangrijker voor zowel regelgeving als kwaliteitsbewijs richting klanten.</p>
                <p>Wanneer goede isolatie, een betrouwbaar koelaggregaat en continue monitoring samenkomen, blijft de koelketen ook op de langste routes beschermd.</p>
            ',
        ],

        'trailer-winter-maintenance' => [
            'title'   => 'Klaar voor de winter: een onderhoudschecklist voor trailers',
            'excerpt' => 'Een complete checklist om uw trailervloot voor te bereiden op lage temperaturen, strooizout en barre winteromstandigheden.',
            'content' => '
                <p>De wintermaanden — met lage temperaturen, strooizout en lastige wegdekken — zijn de zwaarste periode van het jaar voor trailers. Een geplande voorbereiding vóór het seizoen voorkomt pech langs de weg midden in de winter.</p>
                <h2>Remmen en luchtsystemen</h2>
                <p>De luchtdroger wordt bij koud weer kritiek: wordt het patroon niet op tijd vervangen, dan kan vocht in de leidingen bevriezen en het remsysteem blokkeren. Remblokken, schijven en automatische remstellers moeten vóór het seizoen worden gecontroleerd.</p>
                <h2>Checklist vóór het seizoen</h2>
                <ul>
                    <li>Vervang het luchtdrogerpatroon en tap de ketels af</li>
                    <li>Controleer de remblokdikte en schijfoppervlakken</li>
                    <li>Meet profieldiepte en bandenspanning</li>
                    <li>Inspecteer verlichting en elektrische aansluitingen</li>
                    <li>Was het chassis en breng bescherming tegen zoutcorrosie aan</li>
                    <li>Smeer deurscharnieren en sloten</li>
                </ul>
                <p>Regelmatige wintervoorbereiding is de goedkoopste manier om de inzetbaarheid van de vloot te verhogen en de levensduur te verlengen. Ons erkende servicenetwerk staat klaar met uitgebreide seizoensinspecties.</p>
            ',
        ],
    ],
];
