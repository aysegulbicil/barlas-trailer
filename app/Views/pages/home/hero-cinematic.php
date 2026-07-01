<?php

/**
 * Home / Sinematik 3D Hero — tam ekran canlı 3D canvas
 * =============================================================================
 * Kullanıcı sayfayı açtığında doğrudan 3D sahnenin içine girer. Arka planda
 * görsel yoktur; görünen her şey 3D sahnenin (assets/js/hero-cinematic.js →
 * initScene) parçasıdır. Metin/CTA overlay olarak sahnenin üzerinde durur.
 *
 * Scroll: hero PIN'lenir; sahne ortadan ikiye ayrılır (fiziksel kapı yok) ve
 * açılan boşluktan bu dosyadaki "giriş" katmanı (.hc__reveal) görünür.
 *
 * Mevcut yapıyı BOZMAZ: eski showcase hero'su (pages/home/tanker-showcase.php)
 * yerinde durur; home.php'de include tek satırla geri alınabilir.
 *
 * 3D yoksa / reduced-motion: JS section'a .hc--static ekler → hero okunur kalır,
 * "giriş" katmanı normal akışta altında görünür (CSS ile).
 */
?>
<section class="hc" id="hero" data-hero-cine aria-label="<?= esc(lang('Home.hero_eyebrow'), 'attr') ?>">
    <div class="hc__pin" data-hc-pin>

        <!-- Kapı arkası: sinematik "giriş" bölümü. Shader yolunda açılan boşluktan
             görünür; statik yolda normal akışta altta durur. -->
        <div class="hc__reveal" data-hc-reveal aria-hidden="true">
            <div class="hc__reveal-inner">
                <span class="hc__reveal-eyebrow"><?= esc(lang('Common.site_name')) ?></span>
                <h2 class="hc__reveal-title"><?= esc(lang('Home.references_title')) ?></h2>
                <p class="hc__reveal-sub"><?= esc(lang('Home.why_lead')) ?></p>
                <a class="btn btn--primary btn--lg" href="<?= esc(locale_url('products')) ?>">
                    <?= esc(lang('Home.hero_cta_primary')) ?>
                </a>
            </div>
        </div>

        <!-- Canlı 3D canvas buraya eklenir (renderer.domElement) -->
        <div class="hc__stage" data-hc-stage aria-hidden="true"></div>

        <!-- Düşük-güç (mobil) yolunda yarılmayı düşündüren ışık çizgisi -->
        <span class="hc__seam" data-hc-seam aria-hidden="true"></span>

        <!-- Metin / CTA overlay — 3D'den bağımsız normal HTML -->
        <div class="hc__overlay" data-hc-overlay>
            <div class="hc__inner">
                <span class="hc__eyebrow">
                    <span class="hc__eyebrow-dot"></span>
                    <?= esc(lang('Home.hero_eyebrow')) ?>
                </span>

                <?php
                // Başlık 3 satır: sade / VURGU (mavi) / sade. Dile göre 1. satır boş
                // olabilir (örn. en/ru'da güç kelimesi başta) → boşsa satır atlanır.
                $ht1 = lang('Home.hero_title_1');
                $ht3 = lang('Home.hero_title_3');
                ?>
                <h1 class="hc__title">
                    <?php if ($ht1 !== '' && $ht1 !== 'Home.hero_title_1'): ?>
                        <span class="hc__line"><?= esc($ht1) ?></span>
                    <?php endif; ?>
                    <span class="hc__line hc__line--accent"><?= esc(lang('Home.hero_title_2')) ?></span>
                    <?php if ($ht3 !== '' && $ht3 !== 'Home.hero_title_3'): ?>
                        <span class="hc__line"><?= esc($ht3) ?></span>
                    <?php endif; ?>
                </h1>

                <p class="hc__sub"><?= esc(lang('Home.hero_subtitle')) ?></p>

                <div class="hc__actions">
                    <a class="btn btn--primary btn--lg" href="<?= esc(locale_url('products')) ?>">
                        <?= esc(lang('Home.hero_cta_primary')) ?>
                    </a>
                </div>
            </div>

            <span class="hc__hint" aria-hidden="true">
                <span class="hc__hint-label"><?= esc(lang('Home.scroll_down')) ?></span>
                <span class="hc__hint-arrow"></span>
            </span>
        </div>

    </div>
</section>
