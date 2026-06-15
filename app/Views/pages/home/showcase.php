<?php

/**
 * Home / Showcase (engineering & capability)
 *
 * A large featured product stage with animated technical "hotspots"
 * (pulsing markers whose callouts draw in on scroll) plus a column of
 * animated capability bars. Uses a transparent product render
 * (showcase.png) when supplied, otherwise an SVG tanker line-art so the
 * stage never looks empty. Hotspot/bar data come from language arrays.
 */
$hotspots = lang('Home.showcase_hotspots');
$hotspots = is_array($hotspots) ? $hotspots : [];
$bars = lang('Home.showcase_bars');
$bars = is_array($bars) ? $bars : [];

// Fixed positions (% of stage) for up to four hotspots.
$positions = [
    ['x' => 30, 'y' => 38],
    ['x' => 54, 'y' => 60],
    ['x' => 72, 'y' => 34],
    ['x' => 86, 'y' => 64],
];
?>
<section class="showcase" id="showcase">
    <span class="section-decor section-decor--gear" aria-hidden="true"></span>

    <div class="container">
        <div class="section-head section-head--start" data-aos="fade-up">
            <h2 class="section-head__title"><?= esc(lang('Home.showcase_title')) ?></h2>
            <p class="section-head__lead"><?= esc(lang('Home.showcase_lead')) ?></p>
        </div>

        <div class="showcase__grid">
            <!-- Product stage with hotspots -->
            <div class="showcase__stage media-frame media-frame--dark" data-img="showcase.png" data-hotspots>
                <span class="showcase__art" aria-hidden="true">
                    <svg viewBox="0 0 520 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <line x1="20" y1="172" x2="500" y2="172" stroke="rgba(255,255,255,0.16)" stroke-width="2" stroke-dasharray="16 12"></line>
                        <path d="M70,150 H470" stroke="rgba(255,255,255,0.30)" stroke-width="3" stroke-linecap="round"></path>
                        <path d="M28,150 V92 Q28,82 38,82 H78 L104,112 V150 Z" fill="rgba(0,91,170,0.30)" stroke="rgba(255,255,255,0.45)" stroke-width="2"></path>
                        <rect x="120" y="84" width="350" height="66" rx="33" fill="rgba(0,91,170,0.22)" stroke="rgba(255,255,255,0.45)" stroke-width="2"></rect>
                        <line x1="200" y1="84" x2="200" y2="150" stroke="rgba(255,255,255,0.28)" stroke-width="1.5"></line>
                        <line x1="300" y1="84" x2="300" y2="150" stroke="rgba(255,255,255,0.28)" stroke-width="1.5"></line>
                        <line x1="400" y1="84" x2="400" y2="150" stroke="rgba(255,255,255,0.28)" stroke-width="1.5"></line>
                        <circle cx="78" cy="160" r="16" fill="rgba(17,24,39,0.7)" stroke="rgba(255,255,255,0.5)" stroke-width="2"></circle>
                        <circle cx="360" cy="160" r="16" fill="rgba(17,24,39,0.7)" stroke="rgba(255,255,255,0.5)" stroke-width="2"></circle>
                        <circle cx="410" cy="160" r="16" fill="rgba(17,24,39,0.7)" stroke="rgba(255,255,255,0.5)" stroke-width="2"></circle>
                    </svg>
                </span>

                <?php foreach ($hotspots as $i => $hs): ?>
                    <?php $pos = $positions[$i] ?? ['x' => 50, 'y' => 50]; ?>
                    <div class="hotspot" style="--x: <?= esc((string) $pos['x'], 'attr') ?>%; --y: <?= esc((string) $pos['y'], 'attr') ?>%;"
                         data-hotspot data-hotspot-delay="<?= esc((string) ($i * 180), 'attr') ?>">
                        <button class="hotspot__marker" type="button" aria-label="<?= esc($hs['title'] ?? '', 'attr') ?>">
                            <span class="hotspot__num"><?= esc((string) ($i + 1)) ?></span>
                        </button>
                        <div class="hotspot__callout">
                            <strong class="hotspot__title"><?= esc($hs['title'] ?? '') ?></strong>
                            <span class="hotspot__desc"><?= esc($hs['desc'] ?? '') ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Capability bars -->
            <div class="showcase__bars">
                <?php foreach ($bars as $bar): ?>
                    <?php $value = (int) ($bar['value'] ?? 0); ?>
                    <div class="cap-bar" data-aos="fade-up">
                        <div class="cap-bar__head">
                            <span class="cap-bar__label"><?= esc($bar['label'] ?? '') ?></span>
                            <span class="cap-bar__value" data-count="<?= esc((string) $value, 'attr') ?>">0</span><span class="cap-bar__pct">%</span>
                        </div>
                        <div class="cap-bar__track">
                            <span class="cap-bar__fill" data-bar="<?= esc((string) $value, 'attr') ?>" style="--target: <?= esc((string) $value, 'attr') ?>%;"></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
