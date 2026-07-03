<?php

/**
 * Media language strings (bg)
 *
 * Tabbed media page (Photos + Videos). Content is file-based
 * (see App\Controllers\Media): images are listed from
 * public/assets/media/photos and videos from public/assets/media/videos.
 */
return [
    // SEO
    'meta_title'       => 'Медия',
    'meta_description' => 'Снимки и видеа от производството на полуремаркета и цистерни Barlas — нашата база, производственият ни процес и машините ни на пътя.',

    // Page hero
    'page_title' => 'Медия',
    'page_lead'  => 'Снимки и видеа от нашата база, производствения ни процес и машините ни на пътя.',
    'breadcrumb' => 'Медия',

    // Tabs
    'tab_photos' => 'Снимки',
    'tab_videos' => 'Видеа',

    // Category filters (Photos)
    'filter_aria'              => 'Филтриране по категория',
    'filter_all'               => 'Всички',
    'cat_before_after'         => 'Преди / След',
    'before_after_empty_title' => 'Преди / След — очаквайте скоро',
    'before_after_empty'       => 'Скоро в тази категория ще бъдат добавени снимки „преди и след“.',

    // Photos — empty state
    'photos_empty_title' => 'Снимките идват скоро',
    'photos_empty_text'  => 'Снимки от производството и базата ще бъдат добавени тук скоро.',
    'sample_note'        => 'Примерни изображения — показват се временно, докато добавите вашите.',

    // Videos — empty / placeholder
    'videos_empty_title' => 'Видеата идват скоро',
    'videos_empty_text'  => 'Нашите рекламни и производствени видеа са на път. Проверете отново скоро.',
    'video_badge'        => 'Очаквайте скоро',
    'video_placeholders' => [
        'От производствената линия',
        'Обиколка на базата',
        'На пътя — доставка',
    ],

    // Accessibility
    'play'          => 'Пусни',
    'lightbox_close' => 'Затвори',
    'lightbox_prev'  => 'Предишна',
    'lightbox_next'  => 'Следваща',
];
