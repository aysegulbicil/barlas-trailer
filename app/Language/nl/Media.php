<?php

/**
 * Media taalteksten (nl)
 *
 * Mediapagina met tabbladen (foto's + video's). Inhoud is bestandgestuurd
 * (zie App\Controllers\Media): afbeeldingen uit public/assets/media/photos,
 * video's uit public/assets/media/videos.
 */
return [
    // SEO
    'meta_title'       => 'Media',
    'meta_description' => 'Foto\'s en video\'s uit de trailer- en tankproductie van Barlas — onze fabriek, ons productieproces en onze voertuigen onderweg.',

    // Paginahero
    'page_title' => 'Media',
    'page_lead'  => 'Foto\'s en video\'s van onze fabriek, ons productieproces en onze voertuigen onderweg.',
    'breadcrumb' => 'Media',

    // Tabbladen
    'tab_photos' => 'Foto\'s',
    'tab_videos' => 'Video\'s',

    // Categoriefilters (foto's)
    'filter_aria'              => 'Filteren op categorie',
    'filter_all'               => 'Alle',
    'cat_before_after'         => 'Voor / na',
    'before_after_empty_title' => 'Voor / na komt binnenkort',
    'before_after_empty'       => 'Voor-en-na-beelden worden binnenkort aan deze categorie toegevoegd.',

    // Foto's — lege staat
    'photos_empty_title' => 'Foto\'s komen binnenkort',
    'photos_empty_text'  => 'Productie- en fabrieksfoto\'s worden hier binnenkort toegevoegd.',
    'sample_note'        => 'Voorbeeldafbeeldingen — tijdelijk, totdat eigen beelden zijn toegevoegd.',

    // Video's — leeg / placeholder
    'videos_empty_title' => 'Video\'s komen binnenkort',
    'videos_empty_text'  => 'Onze promotie- en productievideo\'s zijn onderweg. Kom snel terug.',
    'video_badge'        => 'Binnenkort',
    'video_placeholders' => [
        'Vanaf de productielijn',
        'Rondleiding door de fabriek',
        'Onderweg — levering',
    ],

    // Toegankelijkheid
    'play'          => 'Afspelen',
    'lightbox_close' => 'Sluiten',
    'lightbox_prev'  => 'Vorige',
    'lightbox_next'  => 'Volgende',
];
