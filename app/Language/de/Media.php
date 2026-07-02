<?php

/**
 * Media Sprachtexte (de)
 *
 * Medienseite mit Tabs (Fotos + Videos). Inhalte sind dateibasiert
 * (siehe App\Controllers\Media): Bilder aus public/assets/media/photos,
 * Videos aus public/assets/media/videos.
 */
return [
    // SEO
    'meta_title'       => 'Medien',
    'meta_description' => 'Fotos und Videos aus der Barlas-Trailer- und Tankauflieger-Fertigung — unser Werk, unsere Produktion und unsere Fahrzeuge unterwegs.',

    // Seiten-Hero
    'page_title' => 'Medien',
    'page_lead'  => 'Fotos und Videos aus unserem Werk, unserer Produktion und von unseren Fahrzeugen unterwegs.',
    'breadcrumb' => 'Medien',

    // Tabs
    'tab_photos' => 'Fotos',
    'tab_videos' => 'Videos',

    // Kategoriefilter (Fotos)
    'filter_aria'              => 'Nach Kategorie filtern',
    'filter_all'               => 'Alle',
    'cat_before_after'         => 'Vorher / Nachher',
    'before_after_empty_title' => 'Vorher / Nachher folgt in Kürze',
    'before_after_empty'       => 'Vorher-Nachher-Bilder werden dieser Kategorie bald hinzugefügt.',

    // Fotos — leerer Zustand
    'photos_empty_title' => 'Fotos folgen in Kürze',
    'photos_empty_text'  => 'Produktions- und Werksfotos werden hier bald ergänzt.',
    'sample_note'        => 'Beispielbilder — vorübergehend, bis eigene Bilder eingefügt werden.',

    // Videos — leer / Platzhalter
    'videos_empty_title' => 'Videos folgen in Kürze',
    'videos_empty_text'  => 'Unsere Image- und Produktionsvideos sind unterwegs. Schauen Sie bald wieder vorbei.',
    'video_badge'        => 'Demnächst',
    'video_placeholders' => [
        'Von der Produktionslinie',
        'Werksrundgang',
        'Unterwegs — Auslieferung',
    ],

    // Barrierefreiheit
    'play'          => 'Abspielen',
    'lightbox_close' => 'Schließen',
    'lightbox_prev'  => 'Zurück',
    'lightbox_next'  => 'Weiter',
];
