<?php

/**
 * Service card partial
 *
 * Single service card used on the services list grid and in the detail
 * page "related services" rail. Icon + name + one-line description +
 * read-more link. Render with view('partials/service-card', ['service' => $s])
 * where $service carries: slug, icon, name, desc.
 */
$detailUrl = locale_url('services/' . $service['slug']);
?>
<a class="service-card" href="<?= esc($detailUrl) ?>" id="srv-<?= esc($service['slug'], 'attr') ?>">
    <span class="service-card__icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
             stroke-linecap="round" stroke-linejoin="round">
            <path d="<?= esc($service['icon'], 'attr') ?>"></path>
        </svg>
    </span>
    <span class="service-card__body">
        <span class="service-card__name"><?= esc($service['name']) ?></span>
        <span class="service-card__desc"><?= esc($service['desc']) ?></span>
        <span class="service-card__more">
            <?= esc(lang('Services.card_more')) ?>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M5 12h14M13 6l6 6-6 6"></path>
            </svg>
        </span>
    </span>
</a>
