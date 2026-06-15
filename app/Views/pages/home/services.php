<?php

/**
 * Home / Services
 *
 * Split layout: an intro column with a workshop image (services-workshop.jpg
 * placeholder until supplied) and a grid of richer service cards — numbered,
 * iconed, with an animated bottom accent and hover lift. Data from language
 * files.
 */
$services = [
    ['key' => 'service_repair',  'icon' => 'M14 7l3 3-7 7-3-3z M5 19l2-2 M16 4l4 4-2 2-4-4z'],
    ['key' => 'service_reefer',  'icon' => 'M12 3v18 M3 12h18 M6 6l12 12 M18 6L6 18'],
    ['key' => 'service_silo',    'icon' => 'M6 21V8l6-4 6 4v13 M10 21v-5h4v5'],
    ['key' => 'service_curtain', 'icon' => 'M4 5h16v14H4z M8 5v14 M12 5v14 M16 5v14'],
    ['key' => 'service_adr',     'icon' => 'M12 2l9 5v6c0 5-4 8-9 10-5-2-9-5-9-10V7z M9 12l2 2 4-4'],
    ['key' => 'service_lowbed',  'icon' => 'M2 16h20 M4 16v-5h11l4 5 M7 19a2 2 0 100-4 M17 19a2 2 0 100-4'],
];
?>
<section class="services" id="services">
    <div class="container services__inner">
        <div class="services__intro" data-aos="fade-right">
            <h2 class="section-head__title"><?= esc(lang('Home.services_title')) ?></h2>
            <p class="section-head__lead"><?= esc(lang('Home.services_lead')) ?></p>
            <a class="btn btn--outline" href="<?= esc(locale_url('services')) ?>">
                <?= esc(lang('Home.services_cta')) ?>
            </a>

            <div class="services__media media-frame" data-img="services-workshop.jpg">
                <span class="media-frame__placeholder" aria-hidden="true">
                    <svg viewBox="0 0 80 80" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M46 24l10 10-7 7-10-10z"></path>
                        <path d="M18 62l8-8 M54 18l10 10-6 6-10-10z"></path>
                    </svg>
                </span>
            </div>
        </div>

        <ul class="services__list">
            <?php foreach ($services as $i => $service): ?>
                <li class="service-card" data-aos="fade-up" data-aos-delay="<?= esc((string) (($i % 2) * 90), 'attr') ?>">
                    <span class="service-card__num"><?= esc(str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT)) ?></span>
                    <span class="service-card__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="<?= esc($service['icon'], 'attr') ?>"></path>
                        </svg>
                    </span>
                    <h3 class="service-card__title"><?= esc(lang('Home.' . $service['key'])) ?></h3>
                    <p class="service-card__desc"><?= esc(lang('Home.' . $service['key'] . '_desc')) ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
