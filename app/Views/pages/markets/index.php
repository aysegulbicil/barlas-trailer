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
