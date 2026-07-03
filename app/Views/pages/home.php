<?php

/**
 * Ana sayfa — "çocuk testi" düzeni (tasarım anayasası)
 *
 * Buzdağı ilkesi: yüzeyde TEK ana eylem (hero CTA) + en fazla 4 büyük
 * seçenek. Akış: statik hero → 4 büyük seçenek kartı → 3D konvoy
 * (korunur, geç yüklenir) → AI asistan konsolu → referans bandı.
 *
 * Eski bölümler (ticker / stats / why / process / gallery) yüzeyden
 * kaldırıldı; partial dosyaları pages/home/ altında referans olarak
 * duruyor — tek satır include ile geri alınabilir.
 */
$this->extend('layouts/yeni');

$hotspots = lang('Home.showcase_hotspots');
$hotspots = is_array($hotspots) ? $hotspots : [];

$aiExamples = lang('Home.ai_examples');
$aiExamples = is_array($aiExamples) ? $aiExamples : [];

/* ---------------------------------------------------------------------
   Referans logoları — public/assets/images/refs/ klasörünü otomatik tarar.
   YENİ LOGO EKLEMEK: görseli bu klasöre at, adın başına sıra no koy
   (örn. 08-firma.png). Bant kendiliğinden güncellenir, kod değişmez.
   Her logo bantta TEK TİP chip üzerinde gösterilir (yeni.css .refs__logo):
   varsayılan chip BEYAZ; logo açık renkliyse (beyaz/gümüş çizim) dosya
   adına "-dark" ekle (örn. 08-firma-dark.png) → koyu chip alır.
   Klasör boşsa eski yer tutucu logo gösterilir.
--------------------------------------------------------------------- */
$refLogos = [];
$refDir   = FCPATH . 'assets/images/refs';
if (is_dir($refDir)) {
    foreach (glob($refDir . '/*.{png,jpg,jpeg,webp,svg,gif}', GLOB_BRACE) ?: [] as $logoPath) {
        $refLogos[] = 'refs/' . basename($logoPath);
    }
    natcasesort($refLogos);
    $refLogos = array_values($refLogos);
}

/* 4 büyük seçenek — her kart TEK hedefe gider (maks 3 tık kuralı). */
$options = [
    [
        'url'   => locale_url('products'),
        'title' => lang('Home.opt_products_title'),
        'desc'  => lang('Home.opt_products_desc'),
        'icon'  => 'M3 17V7l5-3 5 3v10 M13 17h8v-6l-3-2h-5 M6.5 20a1.8 1.8 0 100-3.6 1.8 1.8 0 000 3.6z M17.5 20a1.8 1.8 0 100-3.6 1.8 1.8 0 000 3.6z',
    ],
    [
        'url'     => locale_url('teklif'),
        'title'   => lang('Home.opt_quote_title'),
        'desc'    => lang('Home.opt_quote_desc'),
        'icon'    => 'M8 3h8l4 4v12a2 2 0 01-2 2H6a2 2 0 01-2-2V5a2 2 0 012-2z M15 3v5h5 M9 13h6 M9 17h4',
        'primary' => true,
    ],
    [
        'url'   => locale_url('quiz'),
        'title' => lang('Home.opt_quiz_title'),
        'desc'  => lang('Home.opt_quiz_desc'),
        'icon'  => 'M12 3a9 9 0 100 18 9 9 0 000-18z M9.5 9a2.5 2.5 0 115 .5c0 1.5-2.5 2-2.5 3.5 M12 17h.01',
    ],
    [
        'url'   => locale_url('services'),
        'title' => lang('Home.opt_services_title'),
        'desc'  => lang('Home.opt_services_desc'),
        'icon'  => 'M14.5 6.5a4 4 0 11-5 5L4 17l3 3 5.5-5.5a4 4 0 002-8z',
    ],
];
?>

<?= $this->section('content') ?>

<!-- Header artık layout'tan gelir (partials/header — her sayfada aynı) -->

<main>

<!-- ===================== HERO (statik sinematik görsel + "ikiye ayrılma") ===================== -->
<!-- Hero, referans kompozisyonunu içeren tek görseldir (araç + ışık dikişi +
     alt-orta içe-açılan neon çukur GÖMÜLÜ). Scroll'da görsel iki yarıya ayrılıp
     arkadaki katmanı açar (assets/js/hero-static.js + assets/css/hero-static.css).
     GERİ ALMAK İÇİN: 'hero-static' satırını yorumla, istediğin yedeği aç. -->
<?= $this->include('pages/home/hero-static') ?>
<?php /* Yedek hero'lar (tek satır geri dönüş):
<?= $this->include('pages/home/hero-cinematic') ?>   // canlı 3D sahne
<?= $this->include('pages/home/tanker-showcase') ?>  // eski showcase
*/ ?>

<!-- ===================== 4 BÜYÜK SEÇENEK (çocuk testi) ===================== -->
<section class="options" id="secenekler" aria-labelledby="options-title">
    <div class="shell">
        <header class="sec-head" data-reveal="down">
            <h2 class="sec-head__title" id="options-title"><?= esc(lang('Home.options_title')) ?></h2>
            <p class="sec-head__lead"><?= esc(lang('Home.options_lead')) ?></p>
        </header>

        <div class="options__grid" data-reveal-group>
            <?php foreach ($options as $opt): ?>
                <a class="option-card<?= ! empty($opt['primary']) ? ' option-card--primary' : '' ?>"
                   href="<?= esc($opt['url']) ?>">
                    <span class="option-card__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="<?= esc($opt['icon'], 'attr') ?>"></path>
                        </svg>
                    </span>
                    <span class="option-card__body">
                        <span class="option-card__title"><?= esc($opt['title']) ?></span>
                        <span class="option-card__desc"><?= esc($opt['desc']) ?></span>
                    </span>
                    <span class="option-card__arrow" aria-hidden="true">→</span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ===================== TANITIM: TANKER YOLDA (pinli 3D sahne — korunur) ===================== -->
<section class="road" id="tanitim" data-road>
    <div class="road__frame">
        <div class="road__stage" data-road-stage aria-hidden="true">
            <!-- 3D yoksa / mobilde: statik görsel -->
            <img class="road__fallback" src="<?= base_url('assets/images/hero-2.webp') ?>" alt="" loading="lazy" decoding="async">
        </div>

        <div class="shell road__head" data-reveal="down">
            <h2 class="sec-head__title"><?= esc(lang('Home.road_title')) ?></h2>
            <p class="sec-head__lead"><?= esc(lang('Home.road_lead')) ?></p>
        </div>

        <div class="shell road__caps">
            <?php foreach (array_slice($hotspots, 0, 3) as $hs): ?>
                <p class="road__cap" data-road-cap>
                    <strong><?= esc($hs['title'] ?? '') ?></strong>
                    <?= esc($hs['desc'] ?? '') ?>
                </p>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ===================== YAPAY ZEKA ASİSTANI ===================== -->
<section class="ai" id="asistan">
    <div class="shell ai__inner">
        <header class="sec-head" data-reveal>
            <span class="ai__badge">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="4"></circle><path d="M12 3v3M12 18v3M3 12h3M18 12h3M5.6 5.6l2.1 2.1M16.3 16.3l2.1 2.1M18.4 5.6l-2.1 2.1M7.7 16.3l-2.1 2.1"></path></svg>
                <?= esc(lang('Home.ai_eyebrow')) ?>
            </span>
            <h2 class="sec-head__title"><?= esc(lang('Home.ai_title')) ?></h2>
            <p class="sec-head__lead"><?= esc(lang('Home.ai_lead')) ?></p>
        </header>

        <div class="console" data-reveal="zoom" data-ai
             data-endpoint="<?= esc(locale_url('ai/ask'), 'attr') ?>"
             data-demo-response="<?= esc(lang('Home.ai_demo_response'), 'attr') ?>"
             data-error-message="<?= esc(lang('Home.ai_error'), 'attr') ?>">
            <aside class="console__side">
                <span class="console__side-title"><?= esc(lang('Home.ai_examples_title')) ?></span>
                <?php foreach ($aiExamples as $example): ?>
                    <button class="console__chip" type="button" data-ai-example><?= esc($example) ?></button>
                <?php endforeach; ?>
                <p class="console__note"><?= esc(lang('Home.ai_note')) ?></p>
            </aside>

            <div class="console__chat">
                <div class="console__messages" data-ai-messages aria-live="polite">
                    <div class="msg msg--bot">
                        <span class="msg__avatar" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="5" y="7" width="14" height="11" rx="4"></rect><path d="M9 11h.01M15 11h.01M9 15h6M12 4v3"></path></svg>
                        </span>
                        <p><?= esc(lang('Home.ai_welcome')) ?></p>
                    </div>
                </div>

                <form class="console__form" data-ai-form>
                    <label class="sr-only" for="ai-input"><?= esc(lang('Home.ai_input_label')) ?></label>
                    <!-- Bal kabı: botlar doldurur, gerçek kullanıcılar görmez. -->
                    <input class="console__hp" type="text" name="website" data-ai-hp tabindex="-1" autocomplete="off" aria-hidden="true">
                    <input id="ai-input" type="text" autocomplete="off" data-ai-input
                           placeholder="<?= esc(lang('Home.ai_placeholder'), 'attr') ?>">
                    <button class="console__send" type="submit" aria-label="<?= esc(lang('Home.ai_send'), 'attr') ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 2 11 13"></path><path d="m22 2-7 20-4-9-9-4z"></path></svg>
                    </button>
                </form>
            </div>
        </div>

        <p class="ai__disclaimer"><?= esc(lang('Home.ai_disclaimer')) ?></p>
    </div>
</section>

<!-- ===================== REFERANSLAR (oto-kayan bant) ===================== -->
<section class="refs" id="referanslar">
    <div class="shell">
        <header class="sec-head" data-reveal="down">
            <h2 class="sec-head__title"><?= esc(lang('Home.references_title')) ?></h2>
        </header>
    </div>

    <?php
    // Logolar $refLogos'tan gelir (refs klasörü). Yoksa eski yer tutucu.
    // Az logoda boşluk kalmasın diye set en az ~16 yuvaya ulaşana dek tekrarlanır.
    $usePlaceholder = empty($refLogos);
    $beltCount      = $usePlaceholder ? 8 : count($refLogos);
    $reps           = max(2, (int) ceil(16 / max(1, $beltCount)));
    ?>

    <?php for ($belt = 0; $belt < 2; $belt++): ?>
    <div class="refs__belt<?= $belt === 1 ? ' refs__belt--reverse' : '' ?>" data-reveal>
        <div class="refs__belt-track">
            <?php for ($pass = 0; $pass < 2; $pass++): ?>
                <ul class="refs__belt-group"<?= $pass === 1 ? ' aria-hidden="true"' : '' ?>>
                    <?php for ($r = 0; $r < $reps; $r++): ?>
                        <?php if ($usePlaceholder): ?>
                            <li class="refs__logo">
                                <img src="<?= base_url('assets/images/logo.png') ?>" alt="<?= esc(lang('Common.site_name'), 'attr') ?>" loading="lazy" decoding="async">
                            </li>
                        <?php else: foreach ($refLogos as $logo): ?>
                            <?php
                            $logoSlug = pathinfo($logo, PATHINFO_FILENAME);
                            $logoName = preg_replace('/^\d+[-_]/', '', $logoSlug);
                            $logoName = ucwords(str_replace(['-', '_'], ' ', $logoName));
                            ?>
                            <li class="refs__logo" data-logo="<?= esc($logoSlug, 'attr') ?>">
                                <img src="<?= base_url('assets/images/' . $logo) ?>" alt="<?= esc($logoName, 'attr') ?>" loading="lazy" decoding="async">
                            </li>
                        <?php endforeach; endif; ?>
                    <?php endfor; ?>
                </ul>
            <?php endfor; ?>
        </div>
    </div>
    <?php endfor; ?>
</section>

</main>

<?= $this->endSection() ?>
