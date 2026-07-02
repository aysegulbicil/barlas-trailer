<?php

/**
 * Media teksty językowe (pl)
 *
 * Strona mediów z zakładkami (zdjęcia + wideo). Treść jest plikowa
 * (zob. App\Controllers\Media): obrazy z public/assets/media/photos,
 * wideo z public/assets/media/videos.
 */
return [
    // SEO
    'meta_title'       => 'Media',
    'meta_description' => 'Zdjęcia i filmy z produkcji naczep i cystern Barlas — nasz zakład, proces produkcji i pojazdy w trasie.',

    // Hero strony
    'page_title' => 'Media',
    'page_lead'  => 'Zdjęcia i filmy z naszego zakładu, procesu produkcji i pojazdów w trasie.',
    'breadcrumb' => 'Media',

    // Zakładki
    'tab_photos' => 'Zdjęcia',
    'tab_videos' => 'Wideo',

    // Filtry kategorii (zdjęcia)
    'filter_aria'              => 'Filtruj według kategorii',
    'filter_all'               => 'Wszystkie',
    'cat_before_after'         => 'Przed / po',
    'before_after_empty_title' => 'Przed / po już wkrótce',
    'before_after_empty'       => 'Zdjęcia przed-i-po zostaną wkrótce dodane do tej kategorii.',

    // Zdjęcia — stan pusty
    'photos_empty_title' => 'Zdjęcia już wkrótce',
    'photos_empty_text'  => 'Zdjęcia z produkcji i zakładu pojawią się tu wkrótce.',
    'sample_note'        => 'Przykładowe obrazy — wyświetlane tymczasowo, dopóki nie dodasz własnych.',

    // Wideo — puste / zaślepki
    'videos_empty_title' => 'Filmy już wkrótce',
    'videos_empty_text'  => 'Nasze filmy promocyjne i produkcyjne są w drodze. Zajrzyj ponownie wkrótce.',
    'video_badge'        => 'Wkrótce',
    'video_placeholders' => [
        'Z linii produkcyjnej',
        'Zwiedzanie zakładu',
        'W trasie — dostawa',
    ],

    // Dostępność
    'play'          => 'Odtwórz',
    'lightbox_close' => 'Zamknij',
    'lightbox_prev'  => 'Poprzednie',
    'lightbox_next'  => 'Następne',
];
