<?php

/**
 * Media testi lingua (it)
 *
 * Pagina media a schede (foto + video). I contenuti sono basati su file
 * (vedi App\Controllers\Media): immagini da public/assets/media/photos,
 * video da public/assets/media/videos.
 */
return [
    // SEO
    'meta_title'       => 'Media',
    'meta_description' => 'Foto e video dalla produzione di rimorchi e cisterne Barlas — il nostro stabilimento, il processo produttivo e i veicoli su strada.',

    // Hero di pagina
    'page_title' => 'Media',
    'page_lead'  => 'Foto e video del nostro stabilimento, del processo produttivo e dei nostri veicoli su strada.',
    'breadcrumb' => 'Media',

    // Schede
    'tab_photos' => 'Foto',
    'tab_videos' => 'Video',

    // Filtri categoria (foto)
    'filter_aria'              => 'Filtra per categoria',
    'filter_all'               => 'Tutte',
    'cat_before_after'         => 'Prima / dopo',
    'before_after_empty_title' => 'Prima / dopo in arrivo',
    'before_after_empty'       => 'Le immagini prima-e-dopo verranno presto aggiunte a questa categoria.',

    // Foto — stato vuoto
    'photos_empty_title' => 'Foto in arrivo',
    'photos_empty_text'  => 'Le foto di produzione e dello stabilimento verranno aggiunte presto.',
    'sample_note'        => 'Immagini di esempio — mostrate temporaneamente finché non aggiungerete le vostre.',

    // Video — vuoto / segnaposto
    'videos_empty_title' => 'Video in arrivo',
    'videos_empty_text'  => 'I nostri video promozionali e di produzione stanno arrivando. Torna a trovarci presto.',
    'video_badge'        => 'In arrivo',
    'video_placeholders' => [
        'Dalla linea di produzione',
        'Tour dello stabilimento',
        'Su strada — consegna',
    ],

    // Accessibilità
    'play'          => 'Riproduci',
    'lightbox_close' => 'Chiudi',
    'lightbox_prev'  => 'Precedente',
    'lightbox_next'  => 'Successivo',
];
