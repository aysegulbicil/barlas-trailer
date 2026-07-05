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
$footerLinks = ['home', 'products', 'services', 'markets', 'tools', 'quiz', 'wiki', 'faq', 'verify', 'games', 'news', 'blog', 'contact'];

// Social profiles — replace with the brand's real handles.
$social = [
    'linkedin'  => 'https://www.linkedin.com/company/barlastrailer/',
    'instagram' => 'https://www.instagram.com/barlastanker?igsh=MW1lcGttNjMzeWl1aw==',
    'youtube'   => 'https://youtube.com/@barlastanker?si=S83nH374D8UozOew',
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
        <!-- Dev wordmark banner'ı kaldırıldı (2026-07-03, kullanıcı isteği) -->
    </div>

    <div class="container site-footer__grid">

        <div class="site-footer__col site-footer__col--brand">
            <a class="brand brand--light" href="<?= esc(locale_url()) ?>" aria-label="<?= esc(lang('Common.site_name'), 'attr') ?>">
                <!-- Footer koyu zeminde — beyaz yazılı logo kullanılır (header ile aynı görsel) -->
                <img class="brand__img" src="<?= base_url('assets/logo.png') ?>" alt="<?= esc(lang('Common.site_name'), 'attr') ?>" width="187" height="44" loading="lazy" decoding="async">
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
            <!-- 14 bağlantı tek sütunda footer'ı aşırı uzatıyordu → 2 sütun -->
            <ul class="site-footer__list site-footer__list--2col">
                <?php foreach ($footerLinks as $key): ?>
                    <li>
                        <a href="<?= esc(locale_url($key === 'home' ? '' : $key)) ?>">
                            <?= esc(lang('Navigation.' . $key)) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- "Ürünlerimiz" sütunu kaldırıldı (2026-07-03, kullanıcı isteği) —
             kategoriler zaten Hızlı Erişim'deki Ürünler bağlantısından ulaşılır. -->

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
                <p class="site-footer__contact-item">
                    <span class="site-footer__contact-ic" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 3v10"></path>
                            <path d="m8 9 4 4 4-4"></path>
                            <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2"></path>
                        </svg>
                    </span>
                    <a href="<?= esc(base_url('downloads/barlas-urun-katalogu-2026.pdf'), 'attr') ?>" download><?= esc(lang('Common.footer_catalog')) ?></a>
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
                <li><a href="<?= esc(locale_url('kvkk')) ?>"><?= esc(lang('Common.footer_kvkk')) ?></a></li>
                <li><a href="<?= esc(locale_url('privacy')) ?>"><?= esc(lang('Common.footer_privacy')) ?></a></li>
                <li><a href="<?= esc(locale_url('cookies')) ?>"><?= esc(lang('Common.footer_cookies')) ?></a></li>
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

<?php
/* =====================================================================
   Yüzen eylem — tasarım anayasası: her sayfada TEK ana eylem.
   Eski telefon+WhatsApp FAB çifti, site geneli asistan widget'ıyla
   (partials/assistant-widget) birleşti: telefon ve WhatsApp artık
   panelin içindeki hızlı eylemlerdir. Aşağıdaki .wa-fab stilleri
   widget'ın düğmesi tarafından yeniden kullanılır.
   ===================================================================== */
?>
<?= $this->include('partials/assistant-widget') ?>

<style>
/* ===== Yüzen iletişim butonları (parlak + hareketli, imleçten bağımsız) ===== */
.fab-stack{
    position: fixed;
    inset-block-end: clamp(1.1rem, 1rem + 1.2vw, 2.1rem);
    inset-inline-end: clamp(1.1rem, 1rem + 1.2vw, 2.1rem);
    z-index: 1090;                 /* header (1000) üstünde, overlay (1100) altında */
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 18px;
    transition: opacity .2s ease, visibility .2s ease;
}
/* Mobil menü (çekmece) açıkken yüzen butonlar menünün üstüne binmesin. */
html.nav-locked .fab-stack{
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
}
.wa-fab{
    position: relative;
    width: 62px;
    height: 62px;
    display: grid;
    place-items: center;
    text-decoration: none;
    -webkit-tap-highlight-color: transparent;
    animation: wa-pop .7s .3s cubic-bezier(.34, 1.4, .64, 1) both;
}
.wa-fab--call{ animation-delay: .15s; }   /* telefon biraz önce belirir */
.wa-fab__btn{
    position: relative;
    z-index: 2;
    width: 100%;
    height: 100%;
    display: grid;
    place-items: center;
    border-radius: 999px;
    color: #fff;
    overflow: hidden;
    background: radial-gradient(125% 125% at 30% 22%, #57ec88 0%, #25d366 45%, #12a150 100%);
    box-shadow:
        0 10px 26px rgba(18, 178, 89, .45),
        0 4px 10px rgba(11, 18, 32, .30),
        inset 0 1px 1px rgba(255, 255, 255, .5);
    animation: wa-bob 3.4s ease-in-out infinite;
    transition: transform .26s cubic-bezier(.22, 1, .32, 1), box-shadow .26s ease;
}
.wa-fab__icon{
    position: relative;
    z-index: 2;
    width: 32px;
    height: 32px;
    filter: drop-shadow(0 1px 1px rgba(0, 0, 0, .22));
}
/* Parıltı süpürmesi — "parlak" his */
.wa-fab__btn::before{
    content: "";
    position: absolute;
    inset: 0;
    z-index: 1;
    border-radius: inherit;
    background: linear-gradient(120deg, transparent 32%, rgba(255, 255, 255, .6) 47%, rgba(255, 255, 255, 0) 60%);
    background-size: 250% 100%;
    background-position: 180% 0;
    animation: wa-shine 5s ease-in-out infinite;
    pointer-events: none;
}
/* Genişleyen canlı nabız halkaları */
.wa-fab__pulse{
    position: absolute;
    inset: 0;
    margin: auto;
    z-index: 1;
    width: 62px;
    height: 62px;
    border-radius: 999px;
    background: rgba(37, 211, 102, .5);
    animation: wa-pulse 2.6s cubic-bezier(.22, 1, .32, 1) infinite;
}
.wa-fab__pulse--2{ animation-delay: 1.3s; }
/* Çevrimiçi rozeti */
.wa-fab__badge{
    position: absolute;
    z-index: 3;
    inset-block-start: -1px;
    inset-inline-end: -1px;
    width: 16px;
    height: 16px;
    border-radius: 999px;
    background: #34e07a;
    border: 2px solid #fff;
    animation: wa-blink 1.8s ease-in-out infinite;
}
/* Üzerine gelince çıkan etiket */
.wa-fab__label{
    position: absolute;
    z-index: 2;
    inset-inline-end: calc(100% + 14px);
    inset-block-start: 50%;
    transform: translateY(-50%) translateX(10px) scale(.96);
    transform-origin: center right;
    white-space: nowrap;
    background: #0b1220;
    color: #eef4fc;
    font-family: var(--font-sans, 'Inter', system-ui, sans-serif);
    font-size: .86rem;
    font-weight: 600;
    letter-spacing: .01em;
    padding: .55rem .85rem;
    border-radius: 11px;
    box-shadow: 0 14px 30px rgba(11, 18, 32, .30);
    opacity: 0;
    visibility: hidden;
    transition: opacity .26s ease, transform .26s cubic-bezier(.22, 1, .32, 1);
    pointer-events: none;
}
.wa-fab__label::after{
    content: "";
    position: absolute;
    inset-inline-end: -5px;
    inset-block-start: 50%;
    width: 11px;
    height: 11px;
    background: inherit;
    transform: translateY(-50%) rotate(45deg);
}
/* Etkileşim */
.wa-fab:hover .wa-fab__btn,
.wa-fab:focus-visible .wa-fab__btn{
    transform: scale(1.09);
    box-shadow:
        0 18px 38px rgba(18, 178, 89, .55),
        0 7px 16px rgba(11, 18, 32, .34),
        inset 0 1px 1px rgba(255, 255, 255, .55);
    animation-play-state: paused;
}
.wa-fab:hover .wa-fab__label,
.wa-fab:focus-visible .wa-fab__label{
    opacity: 1;
    visibility: visible;
    transform: translateY(-50%) translateX(0) scale(1);
}
.wa-fab:focus-visible{ outline: none; }
.wa-fab:focus-visible .wa-fab__btn{ outline: 3px solid rgba(37, 211, 102, .65); outline-offset: 3px; }
.wa-fab:active .wa-fab__btn{ transform: scale(1.02); }

/* Telefon butonu — marka mavisi degrade + mavi nabız halkaları */
.wa-fab--call .wa-fab__btn{
    /* Marka mavisi skalası (yeni.css token'ları) — kopuk ara tonlar kaldırıldı */
    background: radial-gradient(125% 125% at 30% 22%, var(--blue-sky, #8FD0FF) 0%, var(--blue-bright, #4AA3E6) 45%, var(--blue, #005BAA) 100%);
    box-shadow:
        0 10px 26px rgba(0, 91, 170, .45),
        0 4px 10px rgba(11, 18, 32, .30),
        inset 0 1px 1px rgba(255, 255, 255, .5);
}
.wa-fab--call .wa-fab__pulse{ background: rgba(74, 163, 230, .5); animation-delay: .9s; }
.wa-fab--call .wa-fab__pulse--2{ animation-delay: 2.2s; }
.wa-fab--call:hover .wa-fab__btn,
.wa-fab--call:focus-visible .wa-fab__btn{
    box-shadow:
        0 18px 38px rgba(0, 91, 170, .55),
        0 7px 16px rgba(11, 18, 32, .34),
        inset 0 1px 1px rgba(255, 255, 255, .55);
}
.wa-fab--call:focus-visible .wa-fab__btn{ outline-color: rgba(74, 163, 230, .65); }

@keyframes wa-pop{
    from{ opacity: 0; transform: translateY(18px) scale(.5); }
    to{ opacity: 1; transform: none; }
}
@keyframes wa-bob{
    0%, 100%{ translate: 0 0; }
    50%{ translate: 0 -6px; }
}
@keyframes wa-pulse{
    0%{ transform: scale(1); opacity: .55; }
    75%{ opacity: 0; }
    100%{ transform: scale(2); opacity: 0; }
}
@keyframes wa-shine{
    0%, 60%{ background-position: 180% 0; }
    88%, 100%{ background-position: -120% 0; }
}
@keyframes wa-blink{
    0%, 100%{ opacity: 1; box-shadow: 0 0 0 0 rgba(52, 224, 122, .55); }
    50%{ opacity: .45; box-shadow: 0 0 0 5px rgba(52, 224, 122, 0); }
}

@media (max-width: 600px){
    .fab-stack{ inset-block-end: 1rem; inset-inline-end: 1rem; gap: 14px; }
    .wa-fab{ width: 56px; height: 56px; }
    .wa-fab__pulse{ width: 56px; height: 56px; }
    .wa-fab__icon{ width: 29px; height: 29px; }
    .wa-fab__label{ display: none; }
}
@media (prefers-reduced-motion: reduce){
    .wa-fab,
    .wa-fab__btn,
    .wa-fab__pulse,
    .wa-fab__badge,
    .wa-fab__btn::before{ animation: none !important; }
}
</style>
