<?php

/**
 * Market detail — tek ülke/pazar sayfası: öne çıkan ürün grupları
 * (gerçek katalog kategorilerine link), ihracat güvenceleri ve teklif
 * CTA'sı. Uydurma ihracat rakamı yok.
 */
$this->extend('layouts/inner');
?>

<?php $this->section('bodyClass') ?>markets markets-detail<?php $this->endSection() ?>

<?php $this->section('content') ?>

<?= view('partials/page-hero', [
    'pageTitle'   => $name,
    'pageLead'    => sprintf(lang('Markets.detail_lead'), $name),
    'breadcrumbs' => [
        ['label' => lang('Markets.breadcrumb'), 'url' => locale_url('markets')],
        ['label' => $name],
    ],
]) ?>

<section class="markets-section">
    <div class="container">

        <p class="markets-detail__region">
            <span class="markets-card__code" aria-hidden="true"><?= esc($country['code']) ?></span>
            <?= esc($regionLabel) ?>
        </p>

        <h2 class="markets-region"><?= esc(sprintf(lang('Markets.section_products'), $name)) ?></h2>
        <p class="markets-lead"><?= esc(lang('Markets.section_products_lead')) ?></p>

        <div class="markets-grid" data-reveal-group>
            <?php foreach ($categories as $category): ?>
                <a class="markets-card markets-card--product" href="<?= esc(locale_url('products/' . $category['slug'])) ?>">
                    <span class="markets-card__body">
                        <h3 class="markets-card__title"><?= esc($category['title']) ?></h3>
                        <span class="markets-card__count"><?= esc($category['desc']) ?></span>
                    </span>
                    <span class="markets-card__more" aria-hidden="true">→</span>
                </a>
            <?php endforeach; ?>
        </div>

        <h2 class="markets-region"><?= esc(lang('Markets.section_why')) ?></h2>
        <ul class="markets-why" data-reveal-group>
            <li><?= esc(lang('Markets.why_standards')) ?></li>
            <li><?= esc(lang('Markets.why_docs')) ?></li>
            <li><?= esc(lang('Markets.why_support')) ?></li>
        </ul>

        <div class="markets-cta" data-reveal>
            <h2 class="markets-cta__title"><?= esc(lang('Markets.cta_title')) ?></h2>
            <p class="markets-cta__text"><?= esc(sprintf(lang('Markets.cta_text'), $name)) ?></p>
            <div class="markets-cta__actions">
                <a class="btn btn--primary" href="<?= esc(locale_url('teklif')) ?>"><?= esc(lang('Markets.cta_quote')) ?></a>
                <a class="btn btn--outline" href="<?= esc(locale_url('contact')) ?>"><?= esc(lang('Markets.cta_contact')) ?></a>
            </div>
        </div>

        <p class="markets-back">
            <a href="<?= esc(locale_url('markets')) ?>">← <?= esc(lang('Markets.back_all')) ?></a>
        </p>
    </div>
</section>

<?php $this->endSection() ?>

<?php $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/markets.css') ?>?v=<?= is_file(FCPATH . 'assets/css/markets.css') ? filemtime(FCPATH . 'assets/css/markets.css') : '1' ?>">
<?php $this->endSection() ?>
