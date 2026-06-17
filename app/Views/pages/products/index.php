<?php

/**
 * Products — category index
 *
 * All 11 main product groups from the master catalogue as large,
 * numbered showcase cards. Card visuals reuse the homepage/mega-menu
 * `category-{slug}.jpg` photo hooks (blueprint placeholder until real
 * photos are dropped into assets/images). Counters in the hero strip
 * count up on first view (products.js).
 */

use App\Libraries\ProductCatalog;

$this->extend('layouts/inner');

$asset = static function (string $path): string {
    $file = FCPATH . $path;
    $ver  = is_file($file) ? (string) filemtime($file) : '1';
    return base_url($path) . '?v=' . $ver;
};

/** Localized category name with catalogue fallback. */
$catName = static function (array $category): string {
    $key   = 'Navigation.cat_' . str_replace('-', '_', $category['slug']);
    $label = lang($key);
    return $label === $key ? $category['name'] : $label;
};
$catDesc = static function (array $category): string {
    $key   = 'Navigation.cat_' . str_replace('-', '_', $category['slug']) . '_desc';
    $label = lang($key);
    return $label === $key ? '' : $label;
};
$catCoverStyle = static function (array $category): string {
    $slug = $category['slug'];
    $categoryPath = 'assets/images/category-' . $slug . '.jpg';
    if (is_file(FCPATH . $categoryPath)) {
        return '';
    }

    foreach ($category['products'] ?? [] as $product) {
        $productPath = 'assets/images/products/' . $slug . '-' . $product['slug'] . '.jpg';
        if (is_file(FCPATH . $productPath)) {
            return 'background-image: url(\'' . base_url($productPath) . '?v=' . filemtime(FCPATH . $productPath) . '\'); background-size: cover; background-position: center;';
        }
    }

    return '';
};
?>

<?php $this->section('bodyClass') ?>products<?php $this->endSection() ?>

<?php $this->section('styles') ?>
<link rel="stylesheet" href="<?= $asset('assets/css/products.css') ?>">
<?php $this->endSection() ?>

<?php $this->section('content') ?>

<?= view('partials/page-hero', [
    'pageTitle'   => lang('Products.page_title'),
    'pageLead'    => lang('Products.page_lead'),
    'breadcrumbs' => [
        ['label' => lang('Navigation.products')],
    ],
]) ?>

<section class="prd-section">
    <div class="container">

        <div class="prd-stats" data-reveal>
            <div class="prd-stat">
                <span class="prd-stat__num" data-countup="<?= count($categories) ?>">0</span>
                <span class="prd-stat__label"><?= esc(lang('Products.stat_categories')) ?></span>
            </div>
            <div class="prd-stat">
                <span class="prd-stat__num" data-countup="<?= esc((string) $totalProducts, 'attr') ?>">0</span>
                <span class="prd-stat__label"><?= esc(lang('Products.stat_products')) ?></span>
            </div>
            <div class="prd-stat">
                <span class="prd-stat__num" data-countup="<?= esc((string) $totalVariants, 'attr') ?>">0</span>
                <span class="prd-stat__label"><?= esc(lang('Products.stat_variants')) ?></span>
            </div>
        </div>

        <div class="prd-cat-grid" data-reveal-group>
            <?php foreach ($categories as $i => $category): ?>
                <?php
                $num      = str_pad((string) $category['no'], 2, '0', STR_PAD_LEFT);
                $url      = locale_url('products/' . $category['slug']);
                $desc     = $catDesc($category);
                $variants = ProductCatalog::variantCount($category);
                $preview  = array_slice($category['products'], 0, 3);
                ?>
                <a class="prd-cat-card" href="<?= esc($url) ?>">
                    <div class="prd-cat-card__media" data-img="category-<?= esc($category['slug'], 'attr') ?>.jpg" style="<?= esc($catCoverStyle($category), 'attr') ?>" aria-hidden="true">
                        <span class="prd-cat-card__num"><?= esc($num) ?></span>
                        <span class="prd-cat-card__corner prd-cat-card__corner--tl"></span>
                        <span class="prd-cat-card__corner prd-cat-card__corner--br"></span>
                    </div>
                    <div class="prd-cat-card__body">
                        <h2 class="prd-cat-card__title"><?= esc($catName($category)) ?></h2>
                        <?php if ($desc !== ''): ?>
                            <p class="prd-cat-card__desc"><?= esc($desc) ?></p>
                        <?php endif; ?>

                        <ul class="prd-cat-card__preview">
                            <?php foreach ($preview as $product): ?>
                                <li><?= esc($product['name']) ?></li>
                            <?php endforeach; ?>
                        </ul>

                        <div class="prd-cat-card__footer">
                            <span class="prd-cat-card__count">
                                <strong><?= count($category['products']) ?></strong> <?= esc(lang('Products.products_label')) ?>
                                <span class="prd-cat-card__dot" aria-hidden="true"></span>
                                <strong><?= esc((string) $variants) ?></strong> <?= esc(lang('Products.variants_label')) ?>
                            </span>
                            <span class="prd-cat-card__cta">
                                <?= esc(lang('Products.view_category')) ?>
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
