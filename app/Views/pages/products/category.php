<?php

/**
 * Products — category page
 *
 * Filterable grid of one category's products with a live text search
 * (products.js, client side, no reload). Every card links to the
 * product detail page that carries the complete technical sheets.
 */
$this->extend('layouts/inner');

$asset = static function (string $path): string {
    $file = FCPATH . $path;
    $ver  = is_file($file) ? (string) filemtime($file) : '1';
    return base_url($path) . '?v=' . $ver;
};
?>

<?php $this->section('bodyClass') ?>products<?php $this->endSection() ?>

<?php $this->section('styles') ?>
<link rel="stylesheet" href="<?= $asset('assets/css/products.css') ?>">
<?php $this->endSection() ?>

<?php $this->section('content') ?>

<?= view('partials/page-hero', [
    'pageTitle'   => $categoryName,
    'pageLead'    => lang('Products.category_lead', [count($category['products']), $variantCount]),
    'breadcrumbs' => [
        ['label' => lang('Navigation.products'), 'url' => locale_url('products')],
        ['label' => $categoryName],
    ],
]) ?>

<section class="prd-section">
    <div class="container">

        <div class="prd-filter" data-reveal>
            <label class="prd-filter__search">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="11" cy="11" r="7"></circle>
                    <path d="M21 21l-4.3-4.3"></path>
                </svg>
                <input type="search" data-prd-search
                       placeholder="<?= esc(lang('Products.search_placeholder'), 'attr') ?>"
                       aria-label="<?= esc(lang('Products.search_placeholder'), 'attr') ?>">
            </label>
        </div>

        <div class="prd-grid" data-reveal-group data-prd-grid>
            <?php foreach ($category['products'] as $product): ?>
                <?php
                $url      = locale_url('products/' . $category['slug'] . '/' . $product['slug']);
                $haystack = mb_strtolower($product['name'] . ' ' . implode(' ', $product['aliases']));
                ?>
                <a class="prd-card" href="<?= esc($url) ?>"
                   data-prd-name="<?= esc($haystack, 'attr') ?>">
                    <div class="prd-card__media"
                         data-img="products/<?= esc($category['slug'], 'attr') ?>-<?= esc($product['slug'], 'attr') ?>.jpg"
                         aria-hidden="true">
                    </div>
                    <div class="prd-card__body">
                        <h2 class="prd-card__title"><?= esc($product['name']) ?></h2>

                        <?php if ($product['aliases'] !== []): ?>
                            <p class="prd-card__firms">
                                <?= esc(implode(' • ', array_slice($product['aliases'], 0, 2))) ?>
                            </p>
                        <?php endif; ?>

                        <div class="prd-card__footer">
                            <span class="prd-card__count">
                                <strong><?= count($product['variants']) ?></strong>
                                <?= esc(lang('Products.models_label')) ?>
                            </span>
                            <span class="prd-card__cta">
                                <?= esc(lang('Products.view_product')) ?>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M5 12h14 M13 6l6 6-6 6"></path>
                                </svg>
                            </span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <p class="prd-empty" data-prd-empty hidden><?= esc(lang('Products.no_results')) ?></p>

        <div class="prd-cta" data-reveal>
            <div class="prd-cta__text">
                <strong><?= esc(lang('Products.cta_title')) ?></strong>
                <span><?= esc(lang('Products.cta_text')) ?></span>
            </div>
            <a class="btn btn--primary" href="<?= esc(locale_url('contact')) ?>">
                <?= esc(lang('Navigation.get_quote')) ?>
            </a>
        </div>

    </div>
</section>

<?php $this->endSection() ?>

<?php $this->section('scripts') ?>
<script src="<?= $asset('assets/js/products.js') ?>" defer></script>
<?php $this->endSection() ?>
