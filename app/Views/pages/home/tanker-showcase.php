<?php

/**
 * Home / Tanker Showcase — "spatial product showcase"
 *
 * autoklasa.pl tarzı "spatial-product-showcase" React bileşeninin sıfır-
 * bağımlılıkla, kendi stack'imize (PHP view + vanilla JS + CSS) uyarlanmış
 * hâli. Yeşil yerine Barlas mavisi.
 *
 * Tek ürün: hero ile AYNI 3D tankerimiz (tanker-3.glb). Solda tanıtım metni,
 * sağda dairesel orb içinde yavaşça dönen 3D tanker (canvas → data-tshow-stage).
 * 3D yoksa/kapalıysa altta statik görsel (hero-1.webp) yedek kalır.
 *
 * 3D sahnesi: assets/js/yeni-tanker.js → initShowcase()
 * Stil:       assets/css/yeni.css      → "Tanker Showcase" bloğu
 */
$ts = lang('Home.tanker_showcase');
$ts = is_array($ts) ? $ts : [];
?>
<section class="tshow tshow--hero" id="hero" data-tshow>
    <span class="tshow__bg" aria-hidden="true"></span>

    <div class="shell">

        <div class="tshow__grid">

            <!-- Sol: tanıtım metni (ana hero metni) -->
            <div class="tshow__panel" data-reveal>
                <?php $ht1 = lang('Home.hero_title_1'); $ht3 = lang('Home.hero_title_3'); ?>
                <h1 class="tshow__title tshow__title--hero">
                    <?php if ($ht1 !== '' && $ht1 !== 'Home.hero_title_1'): ?>
                        <span class="tshow__line"><?= esc($ht1) ?></span>
                    <?php endif; ?>
                    <span class="tshow__line tshow__line--accent"><?= esc(lang('Home.hero_title_2')) ?></span>
                    <?php if ($ht3 !== '' && $ht3 !== 'Home.hero_title_3'): ?>
                        <span class="tshow__line"><?= esc($ht3) ?></span>
                    <?php endif; ?>
                </h1>
                <p class="tshow__desc"><?= esc(lang('Home.hero_subtitle')) ?></p>

                <div class="tshow__actions">
                    <a class="btn btn--primary btn--lg" href="<?= esc(locale_url('products')) ?>"><?= esc(lang('Home.hero_cta_primary')) ?></a>
                    <a class="btn btn--ghost btn--lg" href="<?= esc(locale_url('teklif')) ?>"><?= esc(lang('Home.hero_cta_secondary')) ?></a>
                </div>
            </div>

            <!-- Sağ: dairesel 3D sahne (canvas data-tshow-stage'e eklenir) -->
            <div class="tshow__stage" data-reveal="zoom" aria-hidden="true">
                <span class="tshow__ring"></span>
                <span class="tshow__ring tshow__ring--inner"></span>
                <span class="tshow__orb"></span>
                <span class="tshow__floor"></span>
                <div class="tshow__viewport" data-tshow-stage>
                    <!-- Statik yedek: VARSAYILAN GİZLİ — başta PNG flaş'ı olmasın.
                         Yalnızca 3D çalışamazsa (WebGL/THREE yok ya da reduced-motion)
                         JS .tshow__stage.is-static ekleyerek bunu gösterir. -->
                    <img
                        class="tshow__img"
                        src="<?= base_url('assets/images/hero-1.webp') ?>"
                        alt="<?= esc(lang('Common.site_name'), 'attr') ?>"
                        loading="lazy"
                        decoding="async"
                    >
                </div>
                <span class="tshow__status">
                    <span class="tshow__status-dot"></span>
                    <?= esc(lang('Common.site_name')) ?>
                </span>
            </div>

        </div>
    </div>
</section>
