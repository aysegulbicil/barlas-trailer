<?php

/**
 * Home / Hero section — cinematic single hero
 *
 * One strong message instead of a 3-slide carousel (the slides repeated the
 * same value prop; their copy keys remain in the language files, unused).
 * Structure: kinetic masked headline + subtitle + two CTAs on the left,
 * the 3D tanker actor (assets/js/tanker-3d.js) on the right with a static
 * product render as the no-WebGL/mobile fallback.
 *
 * The headline lines sit inside overflow-hidden masks; flow.js raises them
 * with a stagger on load and the whole hero pins briefly on scroll while
 * the tanker turns toward the road (see assets/js/flow.js).
 */
?>
<section class="hero hero--cine" data-hero id="top">

    <!-- Decorative layer: blueprint grid + logistics routes (under content) -->
    <div class="hero__decor" aria-hidden="true">
        <span class="hero__grid"></span>

        <svg class="hero__network" viewBox="0 0 1200 640" preserveAspectRatio="xMidYMid slice"
             fill="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="routeGrad" x1="0" y1="0" x2="1" y2="0">
                    <stop offset="0" stop-color="var(--route-line)"></stop>
                    <stop offset="1" stop-color="var(--route-line-strong)"></stop>
                </linearGradient>
            </defs>
            <path class="hero__route" d="M120,470 C 330,300 560,300 760,400" stroke="url(#routeGrad)" stroke-width="2"></path>
            <path class="hero__route hero__route--2" d="M200,520 C 480,520 700,260 980,300" stroke="url(#routeGrad)" stroke-width="2"></path>
            <g class="hero__nodes">
                <circle class="hero__node" cx="120" cy="470" r="4"></circle>
                <circle class="hero__node" cx="760" cy="400" r="4"></circle>
                <circle class="hero__node hero__node--live" cx="980" cy="300" r="5"></circle>
            </g>
        </svg>
    </div>

    <div class="container hero__frame" data-hero-frame>
        <div class="hero__layout">

            <div class="hero__content" data-hero-content>
                <span class="hero__eyebrow" data-hero-item>
                    <span class="hero__eyebrow-dot"></span>
                    <?= esc(lang('Home.hero_eyebrow')) ?>
                </span>

                <h2 class="hero__title">
                    <span class="hero__mask"><span class="hero__line" data-hero-line><?= esc(lang('Home.hero_title_1')) ?></span></span>
                    <span class="hero__mask"><span class="hero__line hero__line--accent" data-hero-line><?= esc(lang('Home.hero_title_2')) ?></span></span>
                </h2>

                <p class="hero__subtitle" data-hero-item><?= esc(lang('Home.hero_subtitle')) ?></p>

                <div class="hero__actions" data-hero-item>
                    <a class="btn btn--primary btn--lg" href="<?= esc(locale_url('products')) ?>">
                        <?= esc(lang('Home.hero_cta_primary')) ?>
                    </a>
                    <a class="btn btn--ghost btn--lg" href="<?= esc(locale_url('teklif')) ?>">
                        <?= esc(lang('Home.hero_cta_secondary')) ?>
                    </a>
                </div>
            </div>

            <div class="hero__visual" data-hero-visual aria-hidden="true">
                <!-- Static fallback; hidden while the 3D actor is live -->
                <div class="hero__product">
                    <span class="hero__product-shadow"></span>
                    <img
                        class="hero__product-image"
                        src="<?= base_url('assets/images/hero-1.webp') ?>"
                        alt=""
                        loading="eager"
                        fetchpriority="high"
                        decoding="async"
                    >
                </div>
            </div>

        </div>
    </div>
</section>
