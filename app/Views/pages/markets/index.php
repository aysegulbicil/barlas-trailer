<?php

/**
 * Markets index — ihracat pazarları, bölgeye göre gruplu ülke kartları.
 * Bayrak emojisi KULLANILMAZ (Windows'ta render edilmez); ISO kod rozeti
 * ile ülke adı gösterilir.
 */
$this->extend('layouts/inner');
?>

<?php $this->section('bodyClass') ?>markets<?php $this->endSection() ?>

<?php $this->section('content') ?>

<?= view('partials/page-hero', [
    'pageTitle'   => lang('Markets.page_title'),
    'pageLead'    => lang('Markets.page_lead'),
    'breadcrumbs' => [
        ['label' => lang('Markets.breadcrumb')],
    ],
]) ?>

<section class="markets-section">
    <div class="container">

        <?php
        /*
         * Pazar haritası (§7.12) — dekoratif rota ağı: fabrika (hub)
         * noktasından her pazara eğri. Ekvirektangüler projeksiyon;
         * viewBox nokta bulutundan dinamik hesaplanır, yeni ülke
         * eklenince harita kendini genişletir. Kıta çizimi yok —
         * footer'daki rota motifiyle aynı soyut dil.
         */
        $proj = static fn (float $lat, float $lon): array => [
            round(($lon + 180) / 360 * 1000, 1),
            round((90 - $lat) / 180 * 500, 1),
        ];

        [$hx, $hy] = $proj((float) $hub['lat'], (float) $hub['lon']);

        $dots = [];
        $minX = $maxX = $hx;
        $minY = $maxY = $hy;

        foreach ($grouped as $countries) {
            foreach ($countries as $country) {
                [$x, $y] = $proj((float) $country['lat'], (float) $country['lon']);
                $dots[]  = ['x' => $x, 'y' => $y, 'name' => $country['name']];
                $minX    = min($minX, $x);
                $maxX    = max($maxX, $x);
                $minY    = min($minY, $y);
                $maxY    = max($maxY, $y);
            }
        }

        $pad     = 30;
        $viewBox = sprintf(
            '%.1f %.1f %.1f %.1f',
            $minX - $pad,
            $minY - $pad,
            max($maxX - $minX + 2 * $pad, 120),
            max($maxY - $minY + 2 * $pad, 80)
        );
        ?>
        <div class="markets-map" aria-hidden="true" data-reveal>
            <svg viewBox="<?= esc($viewBox, 'attr') ?>" xmlns="http://www.w3.org/2000/svg" role="presentation" focusable="false">
                <?php foreach ($dots as $dot): ?>
                    <path class="markets-map__route"
                          d="M<?= $hx ?>,<?= $hy ?> Q<?= round(($hx + $dot['x']) / 2, 1) ?>,<?= round(min($hy, $dot['y']) - 16, 1) ?> <?= $dot['x'] ?>,<?= $dot['y'] ?>"
                          fill="none"></path>
                <?php endforeach; ?>
                <?php foreach ($dots as $dot): ?>
                    <circle class="markets-map__halo" cx="<?= $dot['x'] ?>" cy="<?= $dot['y'] ?>" r="7"></circle>
                    <circle class="markets-map__dot" cx="<?= $dot['x'] ?>" cy="<?= $dot['y'] ?>" r="3">
                        <title><?= esc($dot['name']) ?></title>
                    </circle>
                <?php endforeach; ?>
                <circle class="markets-map__hub" cx="<?= $hx ?>" cy="<?= $hy ?>" r="4.5"></circle>
            </svg>
        </div>

        <ul class="markets-stats" data-reveal-group>
            <li class="markets-stats__item">
                <strong><?= esc((string) $stats['countries']) ?></strong>
                <span><?= esc(lang('Markets.stats_countries')) ?></span>
            </li>
            <li class="markets-stats__item">
                <strong><?= esc((string) $stats['regions']) ?></strong>
                <span><?= esc(lang('Markets.stats_regions')) ?></span>
            </li>
            <li class="markets-stats__item">
                <strong><?= esc((string) $stats['categories']) ?></strong>
                <span><?= esc(lang('Markets.stats_categories')) ?></span>
            </li>
            <li class="markets-stats__item">
                <strong><?= esc((string) $stats['languages']) ?></strong>
                <span><?= esc(lang('Markets.stats_languages')) ?></span>
            </li>
        </ul>

        <?php foreach ($grouped as $region => $countries): ?>
            <?php if ($countries === []) { continue; } ?>
            <h2 class="markets-region"><?= esc(lang('Markets.region_' . $region)) ?></h2>
            <div class="markets-grid" data-reveal-group>
                <?php foreach ($countries as $country): ?>
                    <a class="markets-card" href="<?= esc(locale_url('markets/' . $country['slug'])) ?>">
                        <span class="markets-card__code" aria-hidden="true"><?= esc($country['code']) ?></span>
                        <span class="markets-card__body">
                            <h3 class="markets-card__title"><?= esc($country['name']) ?></h3>
                            <span class="markets-card__count">
                                <?= esc(sprintf(lang('Markets.card_categories'), count($country['categories']))) ?>
                            </span>
                        </span>
                        <span class="markets-card__more" aria-hidden="true">→</span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php $this->endSection() ?>

<?php $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/markets.css') ?>?v=<?= is_file(FCPATH . 'assets/css/markets.css') ? filemtime(FCPATH . 'assets/css/markets.css') : '1' ?>">
<?php $this->endSection() ?>
