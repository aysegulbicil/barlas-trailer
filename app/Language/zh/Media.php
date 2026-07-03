<?php

/**
 * Media language strings (zh)
 *
 * Tabbed media page (Photos + Videos). Content is file-based
 * (see App\Controllers\Media): images are listed from
 * public/assets/media/photos and videos from public/assets/media/videos.
 */
return [
    // SEO
    'meta_title'       => '媒体中心',
    'meta_description' => '来自 Barlas 半挂车与罐车制造现场的照片和视频——我们的工厂、生产流程以及行驶在路上的车辆。',

    // Page hero
    'page_title' => '媒体中心',
    'page_lead'  => '来自我们工厂、生产流程以及路上车辆的照片和视频。',
    'breadcrumb' => '媒体中心',

    // Tabs
    'tab_photos' => '照片',
    'tab_videos' => '视频',

    // Category filters (Photos)
    'filter_aria'              => '按分类筛选',
    'filter_all'               => '全部',
    'cat_before_after'         => '维修前 / 后',
    'before_after_empty_title' => '维修前 / 后即将上线',
    'before_after_empty'       => '前后对比图片即将添加到此分类。',

    // Photos — empty state
    'photos_empty_title' => '照片即将上线',
    'photos_empty_text'  => '生产和工厂照片即将在此发布。',
    'sample_note'        => '示例图片——在您添加自己的图片之前临时显示。',

    // Videos — empty / placeholder
    'videos_empty_title' => '视频即将上线',
    'videos_empty_text'  => '我们的宣传和生产视频正在制作中。请稍后再来查看。',
    'video_badge'        => '即将上线',
    'video_placeholders' => [
        '生产线现场',
        '工厂参观',
        '在路上——交付',
    ],

    // Accessibility
    'play'          => '播放',
    'lightbox_close' => '关闭',
    'lightbox_prev'  => '上一张',
    'lightbox_next'  => '下一张',
];
