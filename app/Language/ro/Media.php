<?php

/**
 * Media language strings (ro)
 *
 * Tabbed media page (Photos + Videos). Content is file-based
 * (see App\Controllers\Media): images are listed from
 * public/assets/media/photos and videos from public/assets/media/videos.
 */
return [
    // SEO
    'meta_title'       => 'Media',
    'meta_description' => 'Fotografii și videoclipuri din producția de semiremorci și cisterne Barlas — unitatea noastră, procesul de producție și vehiculele noastre pe drum.',

    // Page hero
    'page_title' => 'Media',
    'page_lead'  => 'Fotografii și videoclipuri din unitatea noastră, din procesul de producție și cu vehiculele noastre pe drum.',
    'breadcrumb' => 'Media',

    // Tabs
    'tab_photos' => 'Fotografii',
    'tab_videos' => 'Videoclipuri',

    // Category filters (Photos)
    'filter_aria'              => 'Filtrați după categorie',
    'filter_all'               => 'Toate',
    'cat_before_after'         => 'Înainte / După',
    'before_after_empty_title' => 'Înainte / După — în curând',
    'before_after_empty'       => 'Imaginile de tip înainte-și-după vor fi adăugate în curând în această categorie.',

    // Photos — empty state
    'photos_empty_title' => 'Fotografiile sosesc în curând',
    'photos_empty_text'  => 'Fotografiile din producție și din unitate vor fi adăugate aici în curând.',
    'sample_note'        => 'Imagini demonstrative — afișate temporar până când le adăugați pe ale dumneavoastră.',

    // Videos — empty / placeholder
    'videos_empty_title' => 'Videoclipurile sosesc în curând',
    'videos_empty_text'  => 'Videoclipurile noastre de prezentare și de producție sunt pe drum. Reveniți în curând.',
    'video_badge'        => 'În curând',
    'video_placeholders' => [
        'De pe linia de producție',
        'Tur al unității',
        'Pe drum — Livrare',
    ],

    // Accessibility
    'play'          => 'Redă',
    'lightbox_close' => 'Închide',
    'lightbox_prev'  => 'Anterior',
    'lightbox_next'  => 'Următor',
];
