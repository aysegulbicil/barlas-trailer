<?php

/**
 * Blog Sprachtexte (de)
 *
 * UI-Texte + Beitragstexte. Beitragstexte sind nach Slug indiziert; Slug,
 * Datum, Kategorie und Titelbild liegen im Blog-Controller-Register,
 * damit die URLs in allen Sprachen identisch bleiben.
 */
return [
    // SEO
    'meta_title'       => 'Blog',
    'meta_description' => 'Branchennews, Wartungstipps, Technologieartikel und Expertenleitfäden aus der Welt der Trailer und Tankauflieger.',

    // Seiten-Hero
    'page_title' => 'Blog',
    'page_lead'  => 'Branchennews, Wartungstipps und ingenieurgetriebene Leitfäden.',

    // Seitenleiste
    'search_title'       => 'Suche',
    'search_placeholder' => 'Blogbeiträge durchsuchen...',
    'search_submit'      => 'Suchen',
    'categories_title'   => 'Kategorien',
    'all_posts'          => 'Alle Beiträge',

    // Karten und Listenzustände
    'read_more'        => 'Weiterlesen',
    'results_found'    => 'Beiträge gefunden',
    'no_results_title' => 'Keine Ergebnisse',
    'no_results_text'  => 'Keine Beiträge entsprechen Ihrer Suche. Versuchen Sie ein anderes Stichwort oder stöbern Sie in allen Beiträgen.',
    'clear_filters'    => 'Alle Beiträge anzeigen',

    // Detailseite
    'related_title'   => 'Ähnliche Beiträge',
    'related_eyebrow' => 'Weiterlesen',
    'back_to_blog'    => 'Zurück zu allen Beiträgen',

    // Kategorien
    'cat_industry'    => 'Branche',
    'cat_maintenance' => 'Wartung & Service',
    'cat_safety'      => 'Sicherheit & ADR',
    'cat_technology'  => 'Technologie',

    // Datumsformatierung
    'months' => ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'],

    // Beiträge (Slug => Texte)
    'posts' => [
        'adr-tanker-safety-standards' => [
            'title'   => 'Sicherheitsstandards bei ADR-zertifizierten Tankaufliegern',
            'excerpt' => 'Wie die ADR-Vorschriften das Tankdesign für Gefahrguttransporte prägen — und welche Sicherheitspraktiken jeder Betreiber kennen sollte.',
            'content' => '
                <p>Der Straßentransport gefährlicher Güter unterliegt streng dem internationalen ADR-Übereinkommen. Bei Tankaufliegern für Kraftstoff, LPG oder Chemikalien läuft jede Phase — von Konstruktion und Fertigung bis zu Prüfungen und periodischen Inspektionen — innerhalb dieses Regelwerks ab.</p>
                <h2>Wie ADR das Tankdesign prägt</h2>
                <p>Bei einem ADR-zertifizierten Tanker werden Behälterwerkstoff, Wandstärke, Kammeranzahl und Schwallwand-Anordnung nach der Klasse des transportierten Produkts berechnet. Überrollfestigkeit, statische Erdung, Not-Absperrventile und Überfüllsicherungen sind integrale Bestandteile des Designs. Nach der Fertigung werden Druck- und Dichtheitsprüfungen von unabhängigen Prüfstellen verifiziert.</p>
                <h2>Worauf Betreiber achten müssen</h2>
                <p>Die Zertifizierung allein genügt nicht; Sicherheit wird durch richtigen Einsatz und regelmäßige Inspektion erhalten. Die wichtigsten Prioritäten für Betreiber sind:</p>
                <ul>
                    <li>Zwischen- und Hauptprüffristen im Blick behalten</li>
                    <li>Ventile, Dichtungen und Sicherheitsausrüstung regelmäßig prüfen</li>
                    <li>Belade- und Entladeverfahren vollständig einhalten</li>
                    <li>ADR-Schulungsbescheinigungen der Fahrer aktuell halten</li>
                </ul>
                <p>Wenn ein gut konstruierter Tanker auf ein gut geschultes Betriebsteam trifft, lässt sich Gefahrguttransport sicher und effizient zugleich durchführen.</p>
            ',
        ],

        'silo-trailer-maintenance-tips' => [
            'title'   => '7 Wartungstipps für ein längeres Silo-Auflieger-Leben',
            'excerpt' => 'Die wichtigsten Wartungsschritte, damit Silo-Auflieger für Pulver- und Granulatladungen länger effizient laufen.',
            'content' => '
                <p>Silo-Auflieger arbeiten unter fordernden Bedingungen und transportieren Zement, Mehl, PVC und ähnliche Pulver- oder Granulatprodukte. Ohne regelmäßige Wartung werden das Druckentladesystem und die Luftleitungen zur ersten Quelle von Effizienzverlust und Ausfällen.</p>
                <h2>Warum geplante Wartung zählt</h2>
                <p>Geplante Wartung senkt nicht nur das Ausfallrisiko: Sie verkürzt Entladezeiten, senkt den Kraftstoffverbrauch und schützt den Wiederverkaufswert. Die folgenden Schritte verhindern die meisten Probleme im Einsatz:</p>
                <ul>
                    <li>Luftleitungen und Bälge vor jeder Fahrt auf Lecks prüfen</li>
                    <li>Ölstand und Filter des Kompressors regelmäßig kontrollieren</li>
                    <li>Dichtungen der Entladeventile auf Verschleiß überwachen</li>
                    <li>Verschlüsse und Dichtungen der oberen Befüll-Luken testen</li>
                    <li>Tankinneres bei jedem Produktwechsel reinigen</li>
                    <li>Sicherheitsventil mindestens einmal jährlich prüfen lassen</li>
                    <li>Chassis- und Achsverbindungen periodisch nachziehen</li>
                </ul>
                <h2>Professioneller Service-Support</h2>
                <p>Tägliche Kontrollen kann der Betreiber selbst übernehmen; Drucksysteme, Sicherheitsventile und strukturelle Inspektionen gehören in die Hände spezialisierter Serviceteams. Herstellerfreigegebene Ersatzteile sind der verlässlichste Weg, die Originalleistung des Systems zu erhalten.</p>
            ',
        ],

        'lowbed-heavy-transport-guide' => [
            'title'   => 'Den richtigen Tieflader für den Schwertransport wählen',
            'excerpt' => 'Die Kriterien für die richtige Tieflader-Konfiguration beim Transport von Baumaschinen und schwerem Gerät.',
            'content' => '
                <p>Der Transport von Baumaschinen, Generatoren, Transformatoren und Industrieanlagen verlangt Lösungen jenseits der Grenzen von Standard-Trailern. Mit niedriger Ladehöhe und hoher Nutzlast sind Tieflader das entscheidende Werkzeug in diesem Segment.</p>
                <h2>Die richtige Konfiguration definieren</h2>
                <p>Die erste Frage bei der Tieflader-Auswahl ist immer dieselbe: Was wird transportiert? Gewicht, Abmessungen und Schwerpunkt der Ladung bestimmen jede technische Entscheidung — von der Achszahl über die Plattformlänge bis zu Rampentyp und Verbreiterungsbedarf. Neben festen Zwei-, Drei- und Vierachsmodellen stehen auch modulare und ausziehbare Plattformlösungen bereit.</p>
                <h2>Wichtige Auswahlkriterien</h2>
                <ul>
                    <li>Maximale Nutzlast und Gewicht pro Achse</li>
                    <li>Ladehöhe und Länge des Ladebetts</li>
                    <li>Hydraulische oder federunterstützte Rampenoptionen</li>
                    <li>Bedarf an selbstlenkenden Achsen</li>
                    <li>Brücken- und Tunnelbeschränkungen entlang der Route</li>
                </ul>
                <p>Ein richtig konfigurierter Tieflader trägt nicht nur die Last: Er erhöht die Betriebssicherheit, vereinfacht Genehmigungsprozesse und senkt langfristig die Betriebskosten. Wer bei der Auswahl mit dem Engineering-Team des Herstellers arbeitet, erzielt das beste Ergebnis.</p>
            ',
        ],

        'lightweight-chassis-technology' => [
            'title'   => 'Leichtbau-Chassis-Technologie: Weniger Gewicht, mehr Nutzlast',
            'excerpt' => 'Die operativen Vorteile eines geringeren Leergewichts durch hochfeste Stähle und optimiertes Chassis-Design.',
            'content' => '
                <p>Da die gesetzlichen Gesamtgewichtsgrenzen im Straßentransport fixiert sind, entsteht der Gewinn beim Leergewicht des Trailers. Jedes am Chassis gesparte Kilogramm wird direkt zu nutzbarer Ladung — und zu mehr Umsatz pro Fahrt.</p>
                <h2>Die Rolle hochfester Stähle</h2>
                <p>Im modernen Trailerbau ersetzen hochfeste Stähle zunehmend die Standard-Baustähle. Diese Werkstoffe liefern gleiche oder bessere Festigkeit bei dünneren Querschnitten. In Kombination mit korrekten Schweißverfahren und präziser Fertigung ermöglichen sie eine deutliche Reduktion des Chassisgewichts bei erhaltener Dauerfestigkeit.</p>
                <h2>Design-Optimierung</h2>
                <p>Der Werkstoff allein genügt nicht. Die Finite-Elemente-Analyse (FEA) modelliert die Spannungsverteilung im Chassis, sodass Material nur dort eingesetzt wird, wo es gebraucht wird. Hauptträgergeometrie, Querträgerplatzierung und Verbindungsdetails werden auf Basis dieser Analysen optimiert.</p>
                <p>Das Ergebnis: geringerer Kraftstoffverbrauch, mehr Nutzlast und weniger CO₂-Emissionen. Gewichtsreduktion ist heute — ökonomisch wie ökologisch — eines der wichtigsten Themen im Trailer-Engineering.</p>
            ',
        ],

        'reefer-cold-chain-logistics' => [
            'title'   => 'Kühlauflieger-Technologie in der Kühlkettenlogistik',
            'excerpt' => 'Moderne Kühlauflieger-Lösungen, die die Kühlkette im Lebensmittel- und Pharmatransport lückenlos halten.',
            'content' => '
                <p>Beim Transport temperatursensibler Produkte wie Lebensmittel und Pharmazeutika kann die kleinste Abweichung zu Produktverlust und ernsten Gesundheitsrisiken führen. Kühlauflieger — das Rückgrat der Kühlkettenlogistik — halten diesen sensiblen Betrieb ohne Unterbrechung am Laufen.</p>
                <h2>Isolationsqualität kommt zuerst</h2>
                <p>Die Leistung eines Kühlaufbaus wird vor dem Kühlaggregat von der Isolationsqualität bestimmt. Hochdichte, polyurethangefüllte Panelsysteme, kombiniert mit Fugendetails, die Wärmebrücken minimieren, lassen das Aggregat weniger arbeiten und senken den Kraftstoffverbrauch. Auch Türdichtungen und Bodenaufbau spielen für die Gesamtwärmeleckage eine entscheidende Rolle.</p>
                <h2>Monitoring und Datenaufzeichnung</h2>
                <p>Im modernen Kühlkettenbetrieb beschränkt sich die Temperaturverfolgung nicht mehr auf das Display in der Kabine. Echtzeit-Telemetrie ermöglicht die Fernüberwachung von Aufbautemperatur, Türöffnungen und Kühlleistung. Diese Daten werden sowohl für die Regelkonformität als auch als Qualitätsnachweis gegenüber Kunden immer wichtiger.</p>
                <p>Wenn richtige Isolierung, ein zuverlässiges Kühlaggregat und kontinuierliches Monitoring zusammenkommen, bleibt die Kühlkette auch auf den längsten Routen geschützt.</p>
            ',
        ],

        'trailer-winter-maintenance' => [
            'title'   => 'Bereit für den Winter: Die Trailer-Wartungscheckliste',
            'excerpt' => 'Eine umfassende Checkliste, um Ihre Trailer-Flotte auf tiefe Temperaturen, Streusalz und harte Winterbedingungen vorzubereiten.',
            'content' => '
                <p>Die Wintermonate — mit tiefen Temperaturen, Streusalz und schwierigen Fahrbahnen — sind die härteste Zeit des Jahres für Trailer. Eine geplante Vorbereitung vor Saisonbeginn verhindert Pannen am Straßenrand mitten im Winter.</p>
                <h2>Bremsen und Luftsysteme</h2>
                <p>Der Lufttrockner wird bei Kälte kritisch: Wird die Kartusche nicht rechtzeitig getauscht, kann sich in den Leitungen sammelnde Feuchtigkeit gefrieren und die Bremsanlage blockieren. Bremsbeläge, Scheiben und automatische Gestängesteller müssen vor der Saison geprüft werden.</p>
                <h2>Checkliste vor der Saison</h2>
                <ul>
                    <li>Lufttrockner-Kartusche tauschen und Kessel entwässern</li>
                    <li>Belagstärke und Scheibenoberflächen prüfen</li>
                    <li>Profiltiefe und Reifendrücke messen</li>
                    <li>Beleuchtung und elektrische Verbindungen inspizieren</li>
                    <li>Chassis waschen und Salzschutz auftragen</li>
                    <li>Türscharniere und Schlösser fetten</li>
                </ul>
                <p>Regelmäßige Wintervorbereitung ist der kostengünstigste Weg zu höherer Flottenverfügbarkeit und längerer Fahrzeuglebensdauer. Unser autorisiertes Servicenetz unterstützt Sie mit umfassenden Saisoninspektionen.</p>
            ',
        ],
    ],
];
