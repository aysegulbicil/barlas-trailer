<?php

/**
 * Footer partial
 *
 * Industrial footer on a fixed dark surface (renders dark in both themes).
 * A decorative route/map motif — echoing the hero — sits behind a
 * four-column grid: brand intro with social links, quick links,
 * product highlights and a contact block. A legal bar carries copyright,
 * the slogan, legal links and a back-to-top control. All visible text
 * comes from language files; the year renders dynamically.
 *
 * NOTE: $social URLs and the legal-link hrefs (#) are placeholders —
 * replace them with the brand's real profiles / legal pages.
 */
$footerLinks = ['home', 'products', 'services', 'blog', 'contact'];

// Social profiles — replace with the brand's real handles.
$social = [
    'linkedin'  => 'https://www.linkedin.com/company/barlastrailer',
    'instagram' => 'https://www.instagram.com/barlastrailer',
    'youtube'   => 'https://www.youtube.com/@barlastrailer',
];
?>
<footer class="site-footer site-footer--routes">

    <!-- Decorative layer: blueprint grid + logistics routes + glow + wordmark -->
    <div class="site-footer__decor" aria-hidden="true">
        <span class="site-footer__bp"></span>
        <svg class="site-footer__network" viewBox="0 0 1280 440" preserveAspectRatio="xMidYMid slice"
             fill="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="footRouteGrad" x1="0" y1="0" x2="1" y2="0">
                    <stop offset="0" stop-color="rgba(74, 163, 230, 0.04)"></stop>
                    <stop offset="0.55" stop-color="rgba(120, 178, 235, 0.55)"></stop>
                    <stop offset="1" stop-color="rgba(150, 205, 245, 0.92)"></stop>
                </linearGradient>
            </defs>
            <path class="site-footer__route" d="M-60,300 C 240,150 470,160 700,250 S 1080,360 1340,200"
                  stroke="url(#footRouteGrad)" stroke-width="2"></path>
            <path class="site-footer__route site-footer__route--2" d="M-60,380 C 300,380 520,120 820,150 S 1180,110 1340,70"
                  stroke="url(#footRouteGrad)" stroke-width="2"></path>
            <path class="site-footer__route site-footer__route--3" d="M-60,210 C 220,250 430,360 700,330 S 1120,210 1340,300"
                  stroke="url(#footRouteGrad)" stroke-width="1.5"></path>
            <path class="site-footer__route site-footer__route--dash" d="M240,178 700,250 820,150 1080,300"
                  stroke="rgba(120, 178, 235, 0.42)" stroke-width="1"></path>
            <g class="site-footer__nodes">
                <circle class="site-footer__halo" cx="240" cy="178" r="9"></circle>
                <circle class="site-footer__node" cx="240" cy="178" r="3.5"></circle>
                <circle class="site-footer__node" cx="700" cy="250" r="3.5"></circle>
                <circle class="site-footer__halo" cx="820" cy="150" r="9"></circle>
                <circle class="site-footer__node" cx="820" cy="150" r="3.5"></circle>
                <circle class="site-footer__halo site-footer__halo--live" cx="1080" cy="300" r="11"></circle>
                <circle class="site-footer__node site-footer__node--live" cx="1080" cy="300" r="4.5"></circle>
            </g>
        </svg>
        <span class="site-footer__glow"></span>
        <span class="site-footer__wordmark"><?= esc(lang('Common.site_name')) ?></span>
    </div>

    <div class="container site-footer__grid">

        <div class="site-footer__col site-footer__col--brand">
            <a class="brand brand--light" href="<?= esc(locale_url()) ?>">
                <span class="brand__mark">B</span>
                <span class="brand__text">
                    <span class="brand__name">Barlas</span>
                    <span class="brand__sub"><?= esc(lang('Common.brand_tagline')) ?></span>
                </span>
            </a>
            <p class="site-footer__about"><?= esc(lang('Common.footer_about')) ?></p>

            <div class="site-footer__follow">
                <span class="site-footer__follow-label"><?= esc(lang('Common.footer_follow_title')) ?></span>
                <ul class="site-footer__social">
                    <li>
                        <a class="site-footer__social-link" href="<?= esc($social['linkedin']) ?>"
                           target="_blank" rel="noopener" aria-label="LinkedIn">
                            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M4.98 3.5A2.5 2.5 0 1 1 0 3.5a2.5 2.5 0 0 1 4.98 0zM.25 8.25h4.5V24H.25V8.25zM8.5 8.25h4.31v2.15h.06c.6-1.14 2.07-2.34 4.26-2.34 4.56 0 5.4 3 5.4 6.9V24h-4.5v-6.99c0-1.67-.03-3.82-2.33-3.82-2.33 0-2.69 1.82-2.69 3.7V24H8.5V8.25z"></path>
                            </svg>
                        </a>
                    </li>
                    <li>
                        <a class="site-footer__social-link" href="<?= esc($social['instagram']) ?>"
                           target="_blank" rel="noopener" aria-label="Instagram">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                <rect x="3" y="3" width="18" height="18" rx="5"></rect>
                                <circle cx="12" cy="12" r="4"></circle>
                                <circle cx="17.4" cy="6.6" r="1.1" fill="currentColor" stroke="none"></circle>
                            </svg>
                        </a>
                    </li>
                    <li>
                        <a class="site-footer__social-link" href="<?= esc($social['youtube']) ?>"
                           target="_blank" rel="noopener" aria-label="YouTube">
                            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M23.5 6.2a3 3 0 0 0-2.1-2.12C19.5 3.55 12 3.55 12 3.55s-7.5 0-9.4.53A3 3 0 0 0 .5 6.2 31.3 31.3 0 0 0 0 12a31.3 31.3 0 0 0 .5 5.8 3 3 0 0 0 2.1 2.12c1.9.53 9.4.53 9.4.53s7.5 0 9.4-.53a3 3 0 0 0 2.1-2.12A31.3 31.3 0 0 0 24 12a31.3 31.3 0 0 0-.5-5.8zM9.6 15.6V8.4l6.25 3.6L9.6 15.6z"></path>
                            </svg>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="site-footer__col">
            <h3 class="site-footer__title"><?= esc(lang('Navigation.quick_links')) ?></h3>
            <ul class="site-footer__list">
                <?php foreach ($footerLinks as $key): ?>
                    <li>
                        <a href="<?= esc(locale_url($key === 'home' ? '' : $key)) ?>">
                            <?= esc(lang('Navigation.' . $key)) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="site-footer__col">
            <h3 class="site-footer__title"><?= esc(lang('Common.footer_products_title')) ?></h3>
            <ul class="site-footer__list">
                <li><a href="<?= esc(locale_url('products')) ?>"><?= esc(lang('Home.cat_tankers')) ?></a></li>
                <li><a href="<?= esc(locale_url('products')) ?>"><?= esc(lang('Home.cat_silos')) ?></a></li>
                <li><a href="<?= esc(locale_url('products')) ?>"><?= esc(lang('Home.cat_tippers')) ?></a></li>
                <li><a href="<?= esc(locale_url('products')) ?>"><?= esc(lang('Home.cat_lowbed')) ?></a></li>
            </ul>
        </div>

        <div class="site-footer__col">
            <h3 class="site-footer__title"><?= esc(lang('Common.footer_contact_title')) ?></h3>
            <address class="site-footer__contact">
                <p class="site-footer__contact-item">
                    <span class="site-footer__contact-ic" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 21s-7-5.6-7-11a7 7 0 0 1 14 0c0 5.4-7 11-7 11z"></path>
                            <circle cx="12" cy="10" r="2.6"></circle>
                        </svg>
                    </span>
                    <span><?= nl2br(esc(lang('Common.address'))) ?></span>
                </p>
                <p class="site-footer__contact-item">
                    <span class="site-footer__contact-ic" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.3 1.8.6 2.7a2 2 0 0 1-.5 2.1L8.1 9.6a16 16 0 0 0 6 6l1.1-1.1a2 2 0 0 1 2.1-.5c.9.3 1.8.5 2.7.6a2 2 0 0 1 1.7 2z"></path>
                        </svg>
                    </span>
                    <a href="tel:<?= esc(lang('Common.phone_raw'), 'attr') ?>"><?= esc(lang('Common.phone')) ?></a>
                </p>
                <p class="site-footer__contact-item">
                    <span class="site-footer__contact-ic" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                            <path d="m3 7 9 6 9-6"></path>
                        </svg>
                    </span>
                    <a href="mailto:<?= esc(lang('Common.email'), 'attr') ?>"><?= esc(lang('Common.email')) ?></a>
                </p>
            </address>
        </div>
    </div>

    <div class="site-footer__bar">
        <div class="container site-footer__bar-inner">
            <div class="site-footer__bar-meta">
                <p class="site-footer__copy">&copy; <?= esc(date('Y')) ?> <?= esc(lang('Common.site_name')) ?>. <?= esc(lang('Common.rights_reserved')) ?></p>
                <p class="site-footer__credit"><?= esc(lang('Common.footer_slogan')) ?></p>
            </div>

            <ul class="site-footer__legal">
                <li><a href="#"><?= esc(lang('Common.footer_kvkk')) ?></a></li>
                <li><a href="#"><?= esc(lang('Common.footer_privacy')) ?></a></li>
                <li><a href="#"><?= esc(lang('Common.footer_cookies')) ?></a></li>
            </ul>

            <button class="site-footer__top" type="button" data-footer-top
                    aria-label="<?= esc(lang('Common.footer_back_to_top'), 'attr') ?>"
                    title="<?= esc(lang('Common.footer_back_to_top'), 'attr') ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 19V5M5 12l7-7 7 7"></path>
                </svg>
            </button>
        </div>
    </div>

    <script>
        (function () {
            var btn = document.querySelector('[data-footer-top]');
            if (!btn) return;
            btn.addEventListener('click', function () {
                var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                window.scrollTo({ top: 0, behavior: reduce ? 'auto' : 'smooth' });
            });
        })();
    </script>
</footer>
