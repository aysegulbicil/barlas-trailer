<?php

/**
 * Home / Stats band
 *
 * Dark industrial band with animated counters. Background uses stats-bg.jpg
 * when present (with a dark overlay for contrast) and a gradient + blueprint
 * placeholder otherwise. A decorative parallax layer adds depth.
 */
$stats = [
    ['value' => 35,    'suffix' => '+', 'label' => lang('Home.stat_years')],
    ['value' => 120,   'suffix' => '+', 'label' => lang('Home.stat_models')],
    ['value' => 40,    'suffix' => '+', 'label' => lang('Home.stat_countries')],
    ['value' => 12000, 'suffix' => '+', 'label' => lang('Home.stat_delivered')],
];
?>
<section class="stats" id="stats" data-img="stats-bg.jpg">
    <div class="stats__overlay" aria-hidden="true"></div>
    <span class="section-decor section-decor--blueprint section-decor--light" aria-hidden="true" data-parallax="20"></span>

    <div class="container">
        <div class="stats__grid">
            <?php foreach ($stats as $i => $stat): ?>
                <div class="stat-card" data-aos="fade-up" data-aos-delay="<?= esc((string) ($i * 90), 'attr') ?>">
                    <div class="stat-card__value">
                        <span class="stat-card__number" data-count="<?= esc((string) $stat['value'], 'attr') ?>">0</span><span class="stat-card__suffix"><?= esc($stat['suffix']) ?></span>
                    </div>
                    <div class="stat-card__label"><?= esc($stat['label']) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
