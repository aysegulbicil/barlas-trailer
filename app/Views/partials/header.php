<?php

/**
 * Header partial
 *
 * Sticky top bar + primary navigation. All labels come from the
 * Navigation language file. Menu items are defined as a small data
 * array; items with a "mega" entry render a mega-menu panel partial
 * (desktop flyout / mobile accordion). Open-close behaviour lives in
 * assets/js/navigation.js, styling in assets/css/navigation.css.
 */
$navItems = [
    ['key' => 'home',     'path' => ''],
    ['key' => 'products', 'path' => 'products', 'mega' => 'partials/nav/mega-products'],
    ['key' => 'services', 'path' => 'services', 'mega' => 'partials/nav/mega-services'],
    ['key' => 'blog',     'path' => 'blog'],
    ['key' => 'contact',  'path' => 'contact'],
];

$currentPath = current_path();
?>
<header class="site-header" data-header>
    <!-- Utility top bar -->
    <div class="topbar">
        <div class="container topbar__inner">
            <div class="topbar__contact">
                <a href="tel:<?= esc(lang('Common.phone_raw'), 'attr') ?>" class="topbar__link">
                    <span class="topbar__icon" aria-hidden="true">&#9742;</span>
                    <?= esc(lang('Common.phone')) ?>
                </a>
                <a href="mailto:<?= esc(lang('Common.email'), 'attr') ?>" class="topbar__link topbar__link--hide-sm">
                    <span class="topbar__icon" aria-hidden="true">&#9993;</span>
                    <?= esc(lang('Common.email')) ?>
                </a>
            </div>
            <div class="topbar__actions">
                <button class="theme-toggle" type="button" data-theme-toggle
                        aria-label="<?= esc(lang('Navigation.theme_toggle'), 'attr') ?>"
                        title="<?= esc(lang('Navigation.theme_toggle'), 'attr') ?>">
                    <span class="sr-only" data-theme-label-dark><?= esc(lang('Navigation.theme_dark')) ?></span>
                    <span class="sr-only" data-theme-label-light><?= esc(lang('Navigation.theme_light')) ?></span>
                    <svg class="theme-toggle__icon theme-toggle__icon--moon" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"></path>
                    </svg>
                    <svg class="theme-toggle__icon theme-toggle__icon--sun" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="4.2"></circle>
                        <path d="M12 2v2.6M12 19.4V22M4.2 4.2l1.8 1.8M18 18l1.8 1.8M2 12h2.6M19.4 12H22M4.2 19.8 6 18M18 6l1.8-1.8"></path>
                    </svg>
                </button>
                <div class="topbar__lang">
                    <?= $this->include('partials/language-switcher') ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Primary navigation -->
    <nav class="navbar-main" aria-label="<?= esc(lang('Navigation.aria_primary'), 'attr') ?>">
        <div class="container navbar-main__inner">
            <a class="brand" href="<?= esc(locale_url()) ?>" aria-label="<?= esc(lang('Common.site_name'), 'attr') ?>">
                <img class="brand__img" src="<?= base_url('assets/logo.png') ?>" alt="<?= esc(lang('Common.site_name'), 'attr') ?>" width="200" height="47" decoding="async">
            </a>

            <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="primary-menu"
                    aria-label="<?= esc(lang('Navigation.toggle_menu'), 'attr') ?>" data-nav-toggle>
                <span></span><span></span><span></span>
            </button>

            <ul class="nav-menu" id="primary-menu" data-nav-menu>
                <?php foreach ($navItems as $item): ?>
                    <?php
                    $active  = ($currentPath === $item['path']) || ($item['path'] !== '' && str_starts_with($currentPath, $item['path']));
                    $hasMega = isset($item['mega']);
                    $panelId = 'mega-' . $item['key'];
                    $label   = lang('Navigation.' . $item['key']);
                    ?>
                    <li class="nav-menu__item<?= $hasMega ? ' nav-menu__item--mega' : '' ?>"<?= $hasMega ? ' data-mega-item' : '' ?>>
                        <div class="nav-menu__entry">
                            <a class="nav-menu__link<?= $active ? ' is-active' : '' ?>"
                               href="<?= esc(locale_url($item['path'])) ?>"
                               <?= $hasMega ? 'data-mega-link aria-haspopup="true"' : '' ?>
                               <?= $active ? 'aria-current="page"' : '' ?>>
                                <?= esc($label) ?>
                            </a>
                            <?php if ($hasMega): ?>
                                <button class="nav-menu__caret" type="button" data-mega-caret
                                        aria-expanded="false" aria-controls="<?= esc($panelId, 'attr') ?>"
                                        aria-label="<?= esc(lang('Navigation.open_submenu') . ': ' . $label, 'attr') ?>">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M6 9l6 6 6-6"></path>
                                    </svg>
                                </button>
                            <?php endif; ?>
                        </div>

                        <?php if ($hasMega): ?>
                            <div class="mega-panel<?= $item['key'] === 'services' ? ' mega-panel--compact' : '' ?>"
                                 id="<?= esc($panelId, 'attr') ?>" data-mega-panel>
                                <?= $this->include($item['mega']) ?>
                            </div>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>

            <a class="btn btn--primary nav-cta" href="<?= esc(locale_url('contact')) ?>">
                <?= esc(lang('Navigation.get_quote')) ?>
            </a>
        </div>
    </nav>
</header>
