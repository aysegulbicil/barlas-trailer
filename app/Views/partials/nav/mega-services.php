<?php

/**
 * Services mini mega menu panel
 *
 * Compact, service-oriented dropdown: a two-column grid of the ten
 * repair services from docs/PROJECT_STRUCTURE.md (icon + name + one
 * line description) and a CTA band linking to the contact page.
 * Intentionally much smaller than the Products showcase panel.
 */
$megaServices = [
    ['key' => 'trailer_repair',          'slug' => 'trailer-repair',          'icon' => 'M14.5 6.5a4 4 0 11-5 5L4 17l3 3 5.5-5.5a4 4 0 002-8z'],
    ['key' => 'frigo_repair',            'slug' => 'frigo-repair',            'icon' => 'M12 3v18 M5 7l14 10 M19 7L5 17'],
    ['key' => 'silobas_repair',          'slug' => 'silobas-repair',          'icon' => 'M5 21V9l7-5 7 5v12 M9 21v-6h6v6'],
    ['key' => 'curtain_repair',          'slug' => 'curtain-repair',          'icon' => 'M4 6h16v12H4z M8 6v12 M12 6v12 M16 6v12'],
    ['key' => 'adr_tanker_repair',       'slug' => 'adr-tanker-repair',       'icon' => 'M12 4l9 15H3z M12 10v4 M12 17h.01'],
    ['key' => 'lowbed_repair',           'slug' => 'lowbed-repair',           'icon' => 'M2 15h20 M4 15v-4h10l3 4 M6 18a1.5 1.5 0 100-3'],
    ['key' => 'aluminum_silobas_repair', 'slug' => 'aluminum-silobas-repair', 'icon' => 'M5 21V9l7-5 7 5v12 M9 13l3 3 3-3'],
    ['key' => 'fuel_center',             'slug' => 'fuel-tank-repair-center', 'icon' => 'M6 21V4h8v17 M6 21h8 M14 8h3v9a2 2 0 11-4 0'],
    ['key' => 'axle_repair',             'slug' => 'axle-repair',             'icon' => 'M2 12h20 M7 14.5a2.5 2.5 0 100-5 M17 14.5a2.5 2.5 0 100-5'],
    ['key' => 'suspension_repair',       'slug' => 'suspension-repair',       'icon' => 'M4 17c2-3 4 3 6 0s4 3 6 0 4 3 4 0 M4 7h16'],
];
?>
<div class="mega mega--services">
    <div class="mega__head mega__head--services">
        <span class="mega__tagline"><?= esc(lang('Navigation.mega_services_tagline')) ?></span>
    </div>

    <div class="mega-srv__grid">
        <?php foreach ($megaServices as $srv): ?>
            <a class="mega-srv" href="<?= esc(locale_url('services/' . $srv['slug'])) ?>">
                <span class="mega-srv__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="<?= esc($srv['icon'], 'attr') ?>"></path>
                    </svg>
                </span>
                <span class="mega-srv__body">
                    <span class="mega-srv__name"><?= esc(lang('Navigation.srv_' . $srv['key'])) ?></span>
                    <span class="mega-srv__desc"><?= esc(lang('Navigation.srv_' . $srv['key'] . '_desc')) ?></span>
                </span>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="mega-srv__cta">
        <div class="mega-srv__cta-text">
            <strong><?= esc(lang('Navigation.service_cta_title')) ?></strong>
            <span><?= esc(lang('Navigation.service_cta_text')) ?></span>
        </div>
        <a class="mega-srv__cta-btn" href="<?= esc(locale_url('contact')) ?>">
            <?= esc(lang('Navigation.service_cta_button')) ?>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M5 12h14 M13 6l6 6-6 6"></path>
            </svg>
        </a>
    </div>
</div>
