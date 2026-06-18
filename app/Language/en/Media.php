<?php

/**
 * Media language strings (en)
 *
 * Tabbed media page (Photos + Videos). Content is file-based
 * (see App\Controllers\Media): images are listed from
 * public/assets/media/photos and videos from public/assets/media/videos.
 */
return [
    // SEO
    'meta_title'       => 'Media',
    'meta_description' => 'Photos and videos from Barlas trailer and tanker manufacturing — our facility, our production process and our vehicles out on the road.',

    // Page hero
    'page_title' => 'Media',
    'page_lead'  => 'Photos and videos from our facility, our production process and our vehicles out on the road.',
    'breadcrumb' => 'Media',

    // Tabs
    'tab_photos' => 'Photos',
    'tab_videos' => 'Videos',

    // Category filters (Photos)
    'filter_aria'              => 'Filter by category',
    'filter_all'               => 'All',
    'cat_before_after'         => 'Before / After',
    'before_after_empty_title' => 'Before / After coming soon',
    'before_after_empty'       => 'Before-and-after images will be added to this category soon.',

    // Photos — empty state
    'photos_empty_title' => 'Photos coming soon',
    'photos_empty_text'  => 'Production and facility photos will be added here soon.',
    'sample_note'        => 'Sample images — shown temporarily until you add your own.',

    // Videos — empty / placeholder
    'videos_empty_title' => 'Videos coming soon',
    'videos_empty_text'  => 'Our promotional and production videos are on the way. Check back shortly.',
    'video_badge'        => 'Coming soon',
    'video_placeholders' => [
        'From the Production Line',
        'Facility Tour',
        'On the Road — Delivery',
    ],

    // Accessibility
    'play'          => 'Play',
    'lightbox_close' => 'Close',
    'lightbox_prev'  => 'Previous',
    'lightbox_next'  => 'Next',
];
