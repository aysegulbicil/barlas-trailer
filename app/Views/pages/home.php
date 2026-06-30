<?php

/**
 * Ana sayfa — yeni arayüz (layouts/yeni.php)
 *
 * autoklasa.pl akışından uyarlanan, sıfırdan tasarlanmış koyu tema.
 * Eski bölüm partial'ları (pages/home/*) artık kullanılmıyor ancak
 * referans olarak yerinde duruyor. Tüm metinler dil dosyalarından,
 * tüm bağlantılar locale_url() üzerinden gelir.
 */
$this->extend('layouts/yeni');

$marquee = lang('Home.marquee');
$marquee = is_array($marquee) ? $marquee : [];

$hotspots = lang('Home.showcase_hotspots');
$hotspots = is_array($hotspots) ? $hotspots : [];
$spotPos  = [['x' => 28, 'y' => 38], ['x' => 52, 'y' => 58], ['x' => 72, 'y' => 34], ['x' => 86, 'y' => 62]];

$whyPoints = lang('Home.why_points');
$whyPoints = is_array($whyPoints) ? $whyPoints : [];
// Her değer maddesine eşlik eden çizgi-ikon (sıra why_points ile aynı):
// kalkan+tik = sertifika, dişli = mühendislik, küre = global servis, saat = uzun ömür.
$whyIcons = [
    'M12 3 5 6v5c0 4.4 3 7.6 7 9 4-1.4 7-4.6 7-9V6z M9 12l2 2 4-4',
    'M12 8.5a3.5 3.5 0 100 7 3.5 3.5 0 000-7z M12 2.5v3 M12 18.5v3 M2.5 12h3 M18.5 12h3 M5.1 5.1l2.1 2.1 M16.8 16.8l2.1 2.1 M18.9 5.1l-2.1 2.1 M7.2 16.8l-2.1 2.1',
    'M12 3a9 9 0 100 18 9 9 0 000-18z M3 12h18 M12 3c2.6 2.7 2.6 15.3 0 18 M12 3c-2.6 2.7-2.6 15.3 0 18',
    'M12 3a9 9 0 100 18 9 9 0 000-18z M12 7.5V12l3 2',
];

$steps = lang('Home.process_steps');
$steps = is_array($steps) ? $steps : [];

$aiExamples = lang('Home.ai_examples');
$aiExamples = is_array($aiExamples) ? $aiExamples : [];

$galleryCols = [
    ['speed' => -8,  'items' => [
        ['img' => 'products/tankers-gida-tankeri.webp',                      'name' => 'Gıda Tankeri',                     'url' => 'products/tankers/gida-tankeri'],
        ['img' => 'products/silos-damperli-silobas.webp',                    'name' => 'Damperli Silobas',                 'url' => 'products/silos/damperli-silobas'],
    ]],
    ['speed' => 7,   'items' => [
        ['img' => 'products/silos-v-tipi-silobas.webp',                      'name' => 'V Tipi Silobas',                   'url' => 'products/silos/v-tipi-silobas'],
        ['img' => 'products/tippers-akordiyon-hububat-tipi-damper.webp',     'name' => 'Akordiyon Hububat Tipi Damper',    'url' => 'products/tippers/akordiyon-hububat-tipi-damper'],
    ]],
    ['speed' => -12, 'items' => [
        ['img' => 'products/dry-cargo-kapali-kasa-celik-kargo-tasiyici.webp', 'name' => 'Kapalı Kasa Çelik Kargo Taşıyıcı', 'url' => 'products/dry-cargo/kapali-kasa-celik-kargo-tasiyici'],
        ['img' => 'products/trailers-dingilli-kamyon-tasiyici.webp',         'name' => 'Dingilli Kamyon Taşıyıcı',         'url' => 'products/trailers/dingilli-kamyon-tasiyici'],
    ]],
];

/* Footer hızlı bağlantıları (header artık partials/header'dan gelir) */
$stats = [
    ['value' => 35,    'label' => lang('Home.stat_years')],
    ['value' => 120,   'label' => lang('Home.stat_models')],
    ['value' => 40,    'label' => lang('Home.stat_countries')],
    ['value' => 12000, 'label' => lang('Home.stat_delivered')],
];

/* ---------------------------------------------------------------------
   Referans logoları — public/assets/images/refs/ klasörünü otomatik tarar.
   YENİ LOGO EKLEMEK: temizlenmiş görseli (beyaz/şeffaf zemin) bu klasöre at,
   adın başına sıra no koy (örn. 08-firma.png). Bant kendiliğinden güncellenir,
   kod değişikliği gerekmez. Klasör boşsa eski yer tutucu logo gösterilir.
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
?>

<?= $this->section('content') ?>

<!-- Header artık layout'tan gelir (partials/header — her sayfada aynı) -->

<main>

<!-- ===================== HERO / SLIDE (tanker spatial showcase) ===================== -->
<!-- Eski hero bölümü kaldırıldı; ana sahne (slide) artık 3D tanker sergisi. -->
<?= $this->include('pages/home/tanker-showcase') ?>

<!-- ===================== GÜVEN BANDI ===================== -->
<section class="ticker" aria-label="<?= esc(lang('Home.references_eyebrow'), 'attr') ?>">
    <div class="ticker__track">
        <?php for ($pass = 0; $pass < 2; $pass++): ?>
            <ul class="ticker__group" <?= $pass === 1 ? 'aria-hidden="true"' : '' ?>>
                <?php foreach ($marquee as $item): ?>
                    <li><?= esc($item) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endfor; ?>
    </div>
</section>

<!-- ===================== İSTATİSTİKLER ===================== -->
<section class="stats" id="rakamlar">
    <div class="shell stats__grid" data-reveal-group="frag">
        <?php foreach ($stats as $stat): ?>
            <div class="stat">
                <span class="stat__num"><span data-count="<?= esc((string) $stat['value'], 'attr') ?>">0</span>+</span>
                <span class="stat__label"><?= esc($stat['label']) ?></span>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- ===================== TANITIM 1: TANKER YOLDA (pinli 3D sahne) ===================== -->
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

<!-- ===================== NEDEN BARLAS ===================== -->
<section class="why" id="neden">
    <div class="shell why__head" data-reveal>
        <span class="sec-head__eyebrow"><?= esc(lang('Home.why_eyebrow')) ?></span>
        <h2 class="sec-head__title"><?= esc(lang('Home.why_title')) ?></h2>
        <p class="sec-head__lead"><?= esc(lang('Home.why_lead')) ?></p>
    </div>

    <div class="shell why__layout">
        <div class="why__cards" data-reveal-group>
            <?php foreach ($whyPoints as $i => $point): ?>
                <article class="why-card">
                    <span class="why-card__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="<?= esc($whyIcons[$i] ?? $whyIcons[0], 'attr') ?>"></path>
                        </svg>
                    </span>
                    <h3 class="why-card__title"><?= esc($point['title'] ?? '') ?></h3>
                    <p class="why-card__desc"><?= esc($point['desc'] ?? '') ?></p>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="why__feature" data-reveal="zoom">
            <img src="<?= base_url('assets/images/hero-2.webp') ?>" alt="" loading="lazy" decoding="async">
            <div class="why__metric">
                <span class="why__metric-value"><span data-count="40">0</span><span class="why__metric-plus">+</span></span>
                <span class="why__metric-label"><?= esc(lang('Home.why_metric_label')) ?></span>
            </div>
        </div>
    </div>
</section>

<!-- ===================== TANITIM 2: ÜRETİM SÜRECİ ===================== -->
<section class="process" id="surec">
    <div class="shell">
        <header class="sec-head" data-reveal="down">
            <span class="sec-head__eyebrow"><?= esc(lang('Home.process_eyebrow')) ?></span>
            <h2 class="sec-head__title"><?= esc(lang('Home.process_title')) ?></h2>
            <p class="sec-head__lead"><?= esc(lang('Home.process_lead')) ?></p>
        </header>

        <ol class="process__track" data-process>
            <span class="process__line" aria-hidden="true"><span class="process__fill" data-process-fill></span></span>
            <?php foreach ($steps as $i => $step): ?>
                <li class="step" data-step>
                    <span class="step__dot"><?= esc(str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT)) ?></span>
                    <h3 class="step__title"><?= esc($step['title'] ?? '') ?></h3>
                    <p class="step__desc"><?= esc($step['desc'] ?? '') ?></p>
                </li>
            <?php endforeach; ?>
        </ol>
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

<!-- ===================== GALERİ ===================== -->
<section class="gallery" id="galeri">
    <div class="shell">
        <header class="sec-head" data-reveal="down">
            <h2 class="sec-head__title"><?= esc(lang('Home.gallery_title')) ?></h2>
            <p class="sec-head__lead"><?= esc(lang('Home.gallery_lead')) ?></p>
        </header>
    </div>

    <div class="shell gallery__grid">
        <?php foreach ($galleryCols as $col): ?>
            <div class="gallery__col" data-g-col data-g-speed="<?= esc((string) $col['speed'], 'attr') ?>">
                <?php foreach ($col['items'] as $item): ?>
                    <figure class="gallery__item">
                        <a href="<?= esc(locale_url($item['url'])) ?>" style="display:block" aria-label="<?= esc($item['name'], 'attr') ?>">
                            <img src="<?= base_url('assets/images/' . $item['img']) ?>" alt="<?= esc($item['name'], 'attr') ?>" loading="lazy" decoding="async">
                        </a>
                    </figure>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="shell gallery__more" data-reveal="zoom">
        <a class="btn btn--primary btn--lg" href="<?= esc(locale_url('media')) ?>"><?= esc(lang('Home.gallery_cta')) ?></a>
    </div>
</section>

</main>

<?= $this->endSection() ?>
