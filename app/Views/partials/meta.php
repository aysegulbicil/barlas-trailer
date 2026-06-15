<?php

/**
 * Meta partial
 *
 * Centralizes all <head> SEO metadata: title, description, canonical,
 * hreflang alternates, Open Graph and Twitter cards. Pages can override
 * the defaults by passing $metaTitle / $metaDescription / $metaImage to
 * the view, otherwise sensible localized fallbacks are used.
 */
$siteName    = lang('Common.site_name');
$metaTitle   = $metaTitle   ?? lang('Common.meta_title');
$metaDesc    = $metaDescription ?? lang('Common.meta_description');
$metaImage   = $metaImage   ?? base_url('assets/images/og-default.jpg');
$currentLoc  = current_locale();
$canonical   = current_url();
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">

<title><?= esc($metaTitle) ?> | <?= esc($siteName) ?></title>
<meta name="description" content="<?= esc($metaDesc) ?>">
<meta name="theme-color" content="#005BAA">

<!-- Canonical & language alternates (SEO) -->
<link rel="canonical" href="<?= esc($canonical, 'attr') ?>">
<?php foreach (supported_locales() as $loc): ?>
    <link rel="alternate" hreflang="<?= esc($loc, 'attr') ?>" href="<?= esc(switch_locale_url($loc), 'attr') ?>">
<?php endforeach; ?>
<link rel="alternate" hreflang="x-default" href="<?= esc(switch_locale_url(default_locale()), 'attr') ?>">

<!-- Open Graph -->
<meta property="og:type" content="website">
<meta property="og:site_name" content="<?= esc($siteName, 'attr') ?>">
<meta property="og:title" content="<?= esc($metaTitle, 'attr') ?>">
<meta property="og:description" content="<?= esc($metaDesc, 'attr') ?>">
<meta property="og:url" content="<?= esc($canonical, 'attr') ?>">
<meta property="og:image" content="<?= esc($metaImage, 'attr') ?>">
<meta property="og:locale" content="<?= esc($currentLoc, 'attr') ?>">

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= esc($metaTitle, 'attr') ?>">
<meta name="twitter:description" content="<?= esc($metaDesc, 'attr') ?>">
<meta name="twitter:image" content="<?= esc($metaImage, 'attr') ?>">

<!-- Favicons -->
<link rel="icon" href="<?= base_url('favicon.ico') ?>" sizes="any">

<!-- Preconnect to CDN for faster asset loading -->
<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
