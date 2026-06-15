<?php

/**
 * Main layout
 *
 * The single HTML shell every page extends. Pages provide:
 *   - $this->section('title'), 'meta', 'styles'  (head extras, optional)
 *   - $this->section('content')                  (page body, required)
 *   - $this->section('scripts')                  (page-specific JS, optional)
 *
 * No literal copy lives here — all visible text comes from language files.
 */
$locale = current_locale();
?>
<!DOCTYPE html>
<html lang="<?= esc($locale) ?>" dir="<?= locale_direction($locale) ?>">
<head>
    <?= $this->include('partials/meta') ?>

    <!-- Premium type: Sora (display) + Inter (body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@500;600;700;800&display=swap">

    <!-- Core styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

    <!-- Project design system (order matters: variables first) -->
    <link rel="stylesheet" href="<?= base_url('assets/css/main.css') ?>">
    <!-- Ortak koyu header kaplaması: header her sayfada yeni temayla aynı -->
    <link rel="stylesheet" href="<?= base_url('assets/css/yeni-header.css') . '?v=' . @filemtime(FCPATH . 'assets/css/yeni-header.css') ?>">

    <?= $this->renderSection('styles') ?>
</head>
<body class="page page--<?= esc($this->renderSection('bodyClass') ?: 'default') ?>">

    <?= $this->include('partials/header') ?>

    <main id="main-content" role="main">
        <?= $this->renderSection('content') ?>
    </main>

    <?= $this->include('partials/footer') ?>

    <!-- Core libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js" defer></script>

    <!-- Project scripts (modular ES modules; deferred by default) -->
    <script type="module" src="<?= base_url('assets/js/main.js') ?>"></script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
