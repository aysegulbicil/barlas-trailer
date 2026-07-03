<?php

/**
 * Home / Rakamlarla Barlas — istatistik paneli + ihracat ışık haritası + odometre
 * =============================================================================
 * Sinematik veri vitrini (v3.0 addendum). Üç blok:
 *   1) İstatistik ızgarası — data-count sayaçları (motor: yeni-app.js
 *      initCounters). Rakam kaynağı app/Data/numbers.json + products.json'dan
 *      CANLI sayılan varyant adedi. RAKAM UYDURMA YASAK: numbers.json'daki
 *      değerler önceki site metninden (stats.php) gelir; netleşince orayı
 *      güncellemek yeter.
 *   2) İhracat ışık haritası — inline SVG. Eşdikdörtgen (equirectangular)
 *      projeksiyon: lon∈[-14,78], lat∈[16,62] → viewBox 0 0 920 520. Ülke
 *      düğümleri app/Data/markets.json'dan; hub = Konya. Yaylar hub'dan ülkeye
 *      quadratic bezier (kontrol noktası dikey kaldırılır). pathLength="1"
 *      sayesinde JS stroke-dashoffset'i tekdüze animasyona sokar.
 *   3) Odometre — filo yıllık km TAHMİNİ (delivered × avgKmPerVehicleYear).
 *      Veri eksikse (null) blok HİÇ basılmaz (dürüst rakam kuralı).
 *
 * Aşamalı varsayılan: CSS'te yaylar/düğümler ÇİZİLİ, odometre statik toplamda
 * durur. "Çizilmemiş" durumu yalnızca JS (cinema-showcase.js) animasyondan
 * hemen önce ekler → JS yok / cine-off iken hiçbir şey gizli kalmaz.
 *
 * RTL (ar/he): harita coğrafidir, sarmalayıcı dir="ltr"; kalan akış mantıksal
 * özelliklerle kendiliğinden döner. Stil: cinema-showcase.css, davranış:
 * cinema-showcase.js (aşağıda styles/scripts section'larıyla yüklenir).
 */

/* ---------- Veri: numbers.json (tek doğruluk kaynağı) ---------- */
$numbersFile = APPPATH . 'Data/numbers.json';
$numbersCfg  = is_file($numbersFile) ? (json_decode((string) file_get_contents($numbersFile), true) ?? []) : [];

$years     = $numbersCfg['years']     ?? null;
$countries = $numbersCfg['countries'] ?? null;
$delivered = $numbersCfg['delivered'] ?? null;
$avgKm     = $numbersCfg['avgKmPerVehicleYear'] ?? null;

/* ---------- Veri: products.json → gerçek varyant sayısı ---------- */
$variantCount = null;
$productsFile = APPPATH . 'Data/products.json';
if (is_file($productsFile)) {
    $catalog = json_decode((string) file_get_contents($productsFile), true);
    if (is_array($catalog['categories'] ?? null)) {
        $variantCount = 0;
        foreach ($catalog['categories'] as $cat) {
            foreach ($cat['products'] ?? [] as $prod) {
                $variantCount += count($prod['variants'] ?? []);
            }
        }
    }
}

/* İstatistik kartları — değeri olmayan kart basılmaz (yanlış rakam uydurma yok). */
$stats = [];
if (is_numeric($years))        { $stats[] = ['value' => (int) $years,     'suffix' => '+', 'label' => lang('Home.stat_years')]; }
if (is_numeric($countries))    { $stats[] = ['value' => (int) $countries, 'suffix' => '+', 'label' => lang('Home.stat_countries')]; }
if (is_numeric($delivered))    { $stats[] = ['value' => (int) $delivered, 'suffix' => '+', 'label' => lang('Home.stat_delivered')]; }
if (is_numeric($variantCount)) { $stats[] = ['value' => (int) $variantCount, 'suffix' => '', 'label' => lang('Home.numbers_stat_variants')]; }

/* ---------- Veri: markets.json → harita düğümleri ---------- */
$marketsFile = APPPATH . 'Data/markets.json';
$markets     = is_file($marketsFile) ? (json_decode((string) file_get_contents($marketsFile), true) ?? []) : [];
$mapNodes    = is_array($markets['countries'] ?? null) ? $markets['countries'] : [];
$hubGeo      = $markets['hub'] ?? ['lat' => 37.9, 'lon' => 32.5];

/* Eşdikdörtgen projeksiyon: lon/lat → viewBox koordinatı. */
$mapW = 920.0;
$mapH = 520.0;
$lonMin = -14.0; $lonMax = 78.0;   // Cezayir'den Kazakistan'a
$latMin = 16.0;  $latMax = 62.0;   // Arabistan'dan Rusya'ya
$project = static function (float $lat, float $lon) use ($mapW, $mapH, $lonMin, $lonMax, $latMin, $latMax): array {
    return [
        round(($lon - $lonMin) / ($lonMax - $lonMin) * $mapW, 1),
        round(($latMax - $lat) / ($latMax - $latMin) * $mapH, 1),
    ];
};

[$hubX, $hubY] = $project((float) ($hubGeo['lat'] ?? 37.9), (float) ($hubGeo['lon'] ?? 32.5));

/* Yay kontrol noktası: orta noktadan dike (yukarı bakan tarafa) kaldır. */
$arcControl = static function (float $x1, float $y1, float $x2, float $y2): array {
    $dx = $x2 - $x1; $dy = $y2 - $y1;
    $len = max(1.0, sqrt($dx * $dx + $dy * $dy));
    $px = -$dy / $len; $py = $dx / $len;         // birim dik vektör
    if ($py > 0) { $px = -$px; $py = -$py; }     // hep yukarı (küçük y) kavis
    $lift = min(85.0, $len * 0.22);
    return [
        round(($x1 + $x2) / 2 + $px * $lift, 1),
        round(($y1 + $y2) / 2 + $py * $lift, 1),
    ];
};

/* Bölge vurgu renkleri (koyu tema; açık tema CSS'te koyulaştırır). */
$regionColors = ['europe' => '#4f8dff', 'cis' => '#8b7bff', 'mena' => '#ffb454'];

/* Düğüm koordinatlarını önceden hesapla (gradyan + yol + düğüm aynı değeri kullanır). */
$plotted = [];
foreach ($mapNodes as $node) {
    if (! isset($node['slug'], $node['lat'], $node['lon'])) {
        continue;
    }
    [$nx, $ny] = $project((float) $node['lat'], (float) $node['lon']);
    [$cx, $cy] = $arcControl((float) $hubX, (float) $hubY, (float) $nx, (float) $ny);
    $plotted[] = [
        'slug'   => (string) $node['slug'],
        'code'   => (string) ($node['code'] ?? ''),
        'region' => (string) ($node['region'] ?? 'europe'),
        'x' => $nx, 'y' => $ny, 'cx' => $cx, 'cy' => $cy,
    ];
}

/* ---------- Odometre: yıllık filo km tahmini ---------- */
$odoTotal = null;
$odoRate  = null;
if (is_numeric($delivered) && is_numeric($avgKm)) {
    $odoTotal = (int) round(((float) $delivered) * ((float) $avgKm)); // km/yıl
    $odoRate  = round($odoTotal / 31557600, 4);                       // km/sn (Jülyen yılı)
}

/* Odometre hane kolonları: rakamlar + binlik ayraç konumları (tr-TR biçimi). */
$odoCells = [];
if ($odoTotal !== null) {
    $digitsStr = (string) $odoTotal;
    $len = strlen($digitsStr);
    for ($i = 0; $i < $len; $i++) {
        if ($i > 0 && (($len - $i) % 3) === 0) {
            $odoCells[] = ['sep' => true];
        }
        $odoCells[] = ['sep' => false, 'digit' => (int) $digitsStr[$i]];
    }
}
?>
<section class="numbers" id="rakamlar" aria-labelledby="numbers-title" data-numbers>
    <div class="shell">
        <header class="sec-head" data-reveal="down">
            <span class="sec-head__eyebrow"><?= esc(lang('Home.numbers_eyebrow')) ?></span>
            <h2 class="sec-head__title" id="numbers-title"><?= esc(lang('Home.numbers_title')) ?></h2>
            <p class="sec-head__lead"><?= esc(lang('Home.numbers_lead')) ?></p>
        </header>

        <!-- İstatistik ızgarası — sayaç motoru yeni-app.js initCounters ([data-count]).
             İlk içerik NİHAİ değerdir (JS yoksa doğru rakam görünür); motor görünüme
             girince kendisi 0'dan saydırır. -->
        <?php if ($stats !== []): ?>
        <div class="numbers__stats" data-reveal-group>
            <?php foreach ($stats as $stat): ?>
                <article class="glass-panel numbers__stat">
                    <p class="numbers__stat-value">
                        <span class="numbers__stat-num" data-count="<?= esc((string) $stat['value'], 'attr') ?>"><?= esc(number_format($stat['value'], 0, ',', '.')) ?></span><?php if ($stat['suffix'] !== ''): ?><span class="numbers__stat-suffix"><?= esc($stat['suffix']) ?></span><?php endif; ?>
                    </p>
                    <p class="numbers__stat-label"><?= esc($stat['label']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- İhracat ışık haritası — koordinatlar PHP'de hesaplanır (markets.json).
             Harita coğrafidir → sarmalayıcı dir="ltr"; RTL'de çevresi mantıksal akar. -->
        <?php if ($plotted !== []): ?>
        <div class="glass-panel numbers__map" data-numbers-map data-reveal dir="ltr">
            <svg class="numbers-map" viewBox="0 0 <?= esc((string) (int) $mapW, 'attr') ?> <?= esc((string) (int) $mapH, 'attr') ?>"
                 xmlns="http://www.w3.org/2000/svg" role="img"
                 aria-label="<?= esc(lang('Home.numbers_map_aria'), 'attr') ?>">
                <defs>
                    <!-- Noktalı gratikül dokusu (ucuz: tek pattern + tek rect) -->
                    <pattern id="num-grid" width="26" height="26" patternUnits="userSpaceOnUse">
                        <circle class="numbers-map__grid-dot" cx="1.3" cy="1.3" r="1.3"></circle>
                    </pattern>
                    <?php foreach ($plotted as $p): ?>
                    <?php $hex = $regionColors[$p['region']] ?? $regionColors['europe']; ?>
                    <!-- Yay gradyanı: hub ucunda saydam kuyruk → ülke ucunda parlak baş -->
                    <linearGradient id="num-arc-<?= esc($p['code'], 'attr') ?>" class="numbers-map__grad"
                                    data-region="<?= esc($p['region'], 'attr') ?>" gradientUnits="userSpaceOnUse"
                                    x1="<?= esc((string) $hubX, 'attr') ?>" y1="<?= esc((string) $hubY, 'attr') ?>"
                                    x2="<?= esc((string) $p['x'], 'attr') ?>" y2="<?= esc((string) $p['y'], 'attr') ?>">
                        <stop class="numbers-map__grad-tail" offset="0" stop-color="<?= esc($hex, 'attr') ?>" stop-opacity="0.07"></stop>
                        <stop class="numbers-map__grad-mid" offset="0.55" stop-color="<?= esc($hex, 'attr') ?>" stop-opacity="0.45"></stop>
                        <stop class="numbers-map__grad-head" offset="1" stop-color="<?= esc($hex, 'attr') ?>" stop-opacity="0.95"></stop>
                    </linearGradient>
                    <?php endforeach; ?>
                </defs>

                <rect class="numbers-map__grid" x="0" y="0" width="<?= esc((string) (int) $mapW, 'attr') ?>" height="<?= esc((string) (int) $mapH, 'attr') ?>" fill="url(#num-grid)"></rect>

                <!-- Yaylar: hub → ülke. pathLength=1 → dashoffset animasyonu tekdüze. -->
                <g class="numbers-map__arcs" fill="none" stroke-linecap="round">
                    <?php foreach ($plotted as $i => $p): ?>
                    <path class="numbers-map__arc" data-arc="<?= esc($p['slug'], 'attr') ?>"
                          data-region="<?= esc($p['region'], 'attr') ?>" pathLength="1"
                          style="--arc-i:<?= esc((string) $i, 'attr') ?>"
                          stroke="url(#num-arc-<?= esc($p['code'], 'attr') ?>)"
                          d="M <?= esc((string) $hubX, 'attr') ?> <?= esc((string) $hubY, 'attr') ?> Q <?= esc((string) $p['cx'], 'attr') ?> <?= esc((string) $p['cy'], 'attr') ?> <?= esc((string) $p['x'], 'attr') ?> <?= esc((string) $p['y'], 'attr') ?>"></path>
                    <?php endforeach; ?>
                </g>

                <!-- Ülke düğümleri: nokta + nabız halkası + ISO kod çipi + tam ad (<title>) -->
                <g class="numbers-map__nodes">
                    <?php foreach ($plotted as $p): ?>
                    <g class="numbers-map__node" data-node="<?= esc($p['slug'], 'attr') ?>"
                       data-region="<?= esc($p['region'], 'attr') ?>"
                       transform="translate(<?= esc((string) $p['x'], 'attr') ?>,<?= esc((string) $p['y'], 'attr') ?>)">
                        <title><?= esc(lang('Markets.country_' . $p['slug'])) ?></title>
                        <circle class="numbers-map__node-pulse" r="9"></circle>
                        <circle class="numbers-map__node-dot" r="4"></circle>
                        <g class="numbers-map__chip" transform="translate(0,-13)">
                            <rect x="-17" y="-12" width="34" height="17" rx="8.5"></rect>
                            <text y="0.5" text-anchor="middle"><?= esc($p['code']) ?></text>
                        </g>
                    </g>
                    <?php endforeach; ?>
                </g>

                <!-- Üretim üssü (hub) — nabız halkalı işaret + etiket -->
                <g class="numbers-map__hub" data-hub
                   transform="translate(<?= esc((string) $hubX, 'attr') ?>,<?= esc((string) $hubY, 'attr') ?>)">
                    <circle class="numbers-map__hub-halo" r="17"></circle>
                    <circle class="numbers-map__hub-ring" r="10"></circle>
                    <circle class="numbers-map__hub-dot" r="4.6"></circle>
                    <text class="numbers-map__hub-label" y="34" text-anchor="middle"><?= esc(lang('Home.numbers_hub_label')) ?></text>
                </g>
            </svg>
        </div>
        <?php endif; ?>

        <!-- Odometre — filo yıllık km TAHMİNİ. Veri yoksa hiç basılmaz.
             Statik varsayılan: haneler PHP'den nihai değerde gelir (--d);
             JS varsa 0'dan yuvarlanır, sonra canlı tık km ekler. -->
        <?php if ($odoTotal !== null && $odoRate !== null): ?>
        <div class="glass-panel glass-panel--beam numbers__odometer" data-odometer data-reveal
             data-odo-total="<?= esc((string) $odoTotal, 'attr') ?>"
             data-odo-rate-per-sec="<?= esc((string) $odoRate, 'attr') ?>">
            <div class="numbers__odometer-head">
                <p class="numbers__odometer-title"><?= esc(lang('Home.numbers_odometer_title')) ?></p>
                <p class="numbers__odometer-note"><?= esc(lang('Home.numbers_odometer_note')) ?></p>
            </div>
            <p class="numbers__odometer-display">
                <!-- Ekran okuyucu için statik toplam; yuvarlanan haneler dekoratif. -->
                <span class="sr-only"><?= esc(number_format($odoTotal, 0, ',', '.')) ?> km</span>
                <span class="odo" dir="ltr" aria-hidden="true">
                    <?php foreach ($odoCells as $cell): ?>
                        <?php if ($cell['sep']): ?>
                            <span class="odo__sep">.</span>
                        <?php else: ?>
                            <span class="odo__col" data-odo-col data-digit="<?= esc((string) $cell['digit'], 'attr') ?>"
                                  style="--d:<?= esc((string) $cell['digit'], 'attr') ?>">
                                <span class="odo__reel"><span>0</span><span>1</span><span>2</span><span>3</span><span>4</span><span>5</span><span>6</span><span>7</span><span>8</span><span>9</span></span>
                            </span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </span>
                <span class="odo__unit">km</span>
            </p>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php /* Bölüme özel stil + davranış — layout'un styles/scripts section'larına eklenir
         (tools/index.php ile aynı kalıp). cinema.css cam panel token'larını taşır;
         layout ileride kendisi yüklerse aynı URL tarayıcı önbelleğinden gelir. */ ?>
<?php $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/cinema.css') ?>?v=<?= is_file(FCPATH . 'assets/css/cinema.css') ? filemtime(FCPATH . 'assets/css/cinema.css') : '1' ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/cinema-showcase.css') ?>?v=<?= is_file(FCPATH . 'assets/css/cinema-showcase.css') ? filemtime(FCPATH . 'assets/css/cinema-showcase.css') : '1' ?>">
<?php $this->endSection() ?>

<?php $this->section('scripts') ?>
<script src="<?= base_url('assets/js/cinema-showcase.js') ?>?v=<?= is_file(FCPATH . 'assets/js/cinema-showcase.js') ? filemtime(FCPATH . 'assets/js/cinema-showcase.js') : '1' ?>" defer></script>
<?php $this->endSection() ?>
