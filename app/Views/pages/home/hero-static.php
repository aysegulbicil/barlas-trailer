<?php

/**
 * Home / Statik Sinematik Hero — hazır render görseli + CSS "ikiye ayrılma"
 * =============================================================================
 * 3D YOK. Hero, referans kompozisyonunu içeren TEK bir görseldir
 * (assets/images/cfa5531d...png): kırmızı çekici + mavi açık kasa araç, dikey
 * mavi ışık dikişi ve alt-orta İÇE-AÇILAN neon çukur GÖRSELE GÖMÜLÜDÜR.
 *
 * Açılma efekti: görsel iki ÖZDEŞ yarıya (clip-path) bölünür. Dinlenmede yarılar
 * birleşik → tam görsel (dikiş + çukur tam ortada). Scroll'da hero PIN'lenir,
 * yarılar yana kayar; ortadaki çukur açılır ve boşluktan arkadaki ".hs__reveal"
 * katmanı görünür — 3D shader yolundaki "ikiye ayrılma" ile aynı his, WebGL'siz.
 *
 * Motor: assets/js/hero-static.js (gsap + ScrollTrigger, layout'ta zaten yüklü).
 * reduced-motion / JS yok: .hs--static → hero okunur tam-ekran görsel kalır.
 *
 * Geri dönüş: home.php'de include tek satırla hero-cinematic'e (3D) döner.
 */

// Önbellek kırıcılı URL — layout'taki $asset() preload'u ile BİREBİR eşleşmeli
// (yoksa preload boşa gider, görsel iki kez inerdi).
$heroRel  = 'assets/images/cfa5531d-3c63-4571-936f-a964650bfef4.png';
$heroFile = FCPATH . $heroRel;
$heroImg  = base_url($heroRel) . '?v=' . (is_file($heroFile) ? (string) filemtime($heroFile) : '1');
?>
<section class="hs" id="hero" data-hero-static
         aria-label="<?= esc(lang('Home.hero_eyebrow'), 'attr') ?>"
         style="--hs-img:url('<?= esc($heroImg, 'attr') ?>')">
    <div class="hs__pin" data-hs-pin>

        <!-- Kapı arkası: açılan boşluktan görünen "giriş" katmanı -->
        <div class="hs__reveal" data-hs-reveal aria-hidden="true">
            <div class="hs__reveal-inner">
                <span class="hs__reveal-eyebrow"><?= esc(lang('Common.site_name')) ?></span>
                <h2 class="hs__reveal-title"><?= esc(lang('Home.references_title')) ?></h2>
                <p class="hs__reveal-sub"><?= esc(lang('Home.why_lead')) ?></p>
                <!-- Dekoratif "kapı arkası" katmanı (aria-hidden). Odak sırasından
                     çıkar: tabindex=-1 → gizli/erişilemez linke klavyeyle gidilmez. -->
                <a class="btn btn--primary btn--lg" tabindex="-1" href="<?= esc(locale_url('products')) ?>">
                    <?= esc(lang('Home.hero_cta_primary')) ?>
                </a>
            </div>
        </div>

        <!-- Sahne: AYNI görselin iki yarısı. Scroll'da yana kayıp ortayı açar. -->
        <div class="hs__scene" data-hs-scene aria-hidden="true">
            <span class="hs__half hs__half--l" data-hs-left></span>
            <span class="hs__half hs__half--r" data-hs-right></span>
        </div>

        <!-- Metin / CTA overlay -->
        <div class="hs__overlay" data-hs-overlay>
            <div class="hs__inner">
                <span class="hs__eyebrow">
                    <span class="hs__eyebrow-dot"></span>
                    <?= esc(lang('Home.hero_eyebrow')) ?>
                </span>

                <?php
                // Başlık 3 satır: sade / VURGU (mavi) / sade. Dile göre 1. satır boş
                // olabilir (en/ru'da güç kelimesi başta) → boşsa satır atlanır.
                $ht1 = lang('Home.hero_title_1');
                $ht3 = lang('Home.hero_title_3');
                ?>
                <h1 class="hs__title">
                    <?php if ($ht1 !== '' && $ht1 !== 'Home.hero_title_1'): ?>
                        <span class="hs__line"><?= esc($ht1) ?></span>
                    <?php endif; ?>
                    <span class="hs__line hs__line--accent"><?= esc(lang('Home.hero_title_2')) ?></span>
                    <?php if ($ht3 !== '' && $ht3 !== 'Home.hero_title_3'): ?>
                        <span class="hs__line"><?= esc($ht3) ?></span>
                    <?php endif; ?>
                </h1>

                <p class="hs__sub"><?= esc(lang('Home.hero_subtitle')) ?></p>

                <div class="hs__actions">
                    <a class="btn btn--primary btn--lg" href="<?= esc(locale_url('products')) ?>">
                        <?= esc(lang('Home.hero_cta_primary')) ?>
                    </a>
                </div>
            </div>

            <span class="hs__hint" aria-hidden="true">
                <span class="hs__hint-label"><?= esc(lang('Home.scroll_down')) ?></span>
                <span class="hs__hint-arrow"></span>
            </span>
        </div>

    </div>
</section>
