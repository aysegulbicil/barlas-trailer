<?php

/**
 * Products — product detail
 *
 * The complete technical sheet of one product, identical layout on
 * every page. Duplicate source models are merged at the data layer
 * (one sheet per distinct model, repeated lines written once) and
 * variants without a published sheet are not exported at all.
 *
 * The sheet is folded into AT MOST three fixed master sections —
 * Teknik Ölçüler / Teknik Kapasite / Teknik Bilgi — with the source
 * document's own sub-headings shown inline inside them. Wording is
 * verbatim; only grouping and presentation are normalized
 * (ProductCatalog::masterSections).
 *
 * Header: one compact card (media + aliases + model badge + CTA), no
 * floating boxes. The media keeps the blueprint placeholder until a
 * photo is dropped at assets/images/products/{cat}-{slug}.jpg.
 */

use App\Libraries\ProductCatalog;

$this->extend('layouts/inner');

$asset = static function (string $path): string {
    $file = FCPATH . $path;
    $ver  = is_file($file) ? (string) filemtime($file) : '1';
    return base_url($path) . '?v=' . $ver;
};

/** Fixed icon per master section (presentation only). */
$masterIcons = [
    'dimensions' => 'M3 17h18 M6 17v-3 M10 17v-5 M14 17v-3 M18 17v-7',
    'capacity'   => 'M6 4v8a6 6 0 0012 0V4 M6 8h12 M12 18v3',
    'info'       => 'M6 4h12v16H6z M9 8h6 M9 12h6 M9 16h4',
];
?>

<?php $this->section('bodyClass') ?>products<?php $this->endSection() ?>

<?php $this->section('styles') ?>
<link rel="stylesheet" href="<?= $asset('assets/css/products.css') ?>">
<?php $this->endSection() ?>

<?php $this->section('content') ?>

<?= view('partials/page-hero', [
    'pageTitle'   => $product['name'],
    'pageLead'    => lang('Products.detail_lead', [count($product['variants'])]),
    'breadcrumbs' => [
        ['label' => lang('Navigation.products'), 'url' => locale_url('products')],
        ['label' => $categoryName, 'url' => locale_url('products/' . $category['slug'])],
        ['label' => $product['name']],
    ],
]) ?>

<section class="prd-section prd-section--detail">
    <div class="container">

        <div class="prd-detail-card" data-reveal>
            <div class="prd-detail-card__media"
                 data-img="products/<?= esc($category['slug'], 'attr') ?>-<?= esc($product['slug'], 'attr') ?>.jpg"
                 aria-hidden="true">
                <span class="prd-cat-card__corner prd-cat-card__corner--tl"></span>
                <span class="prd-cat-card__corner prd-cat-card__corner--br"></span>
            </div>

            <div class="prd-detail-card__info">
                <div class="prd-detail-card__badges">
                    <span class="prd-badge prd-badge--accent">
                        <?= count($product['variants']) ?> <?= esc(lang('Products.models_label')) ?>
                    </span>
                    <a class="prd-badge" href="<?= esc(locale_url('products/' . $category['slug'])) ?>">
                        <?= esc($categoryName) ?>
                    </a>
                </div>

                <?php if ($product['aliases'] !== []): ?>
                    <span class="prd-detail-card__label"><?= esc(lang('Products.aliases_label')) ?></span>
                    <div class="prd-detail-card__aliases">
                        <?php foreach ($product['aliases'] as $alias): ?>
                            <span class="prd-alias-chip"><?= esc($alias) ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="prd-detail-card__actions">
                    <a class="btn btn--primary" href="<?= esc(locale_url('contact')) ?>">
                        <?= esc(lang('Navigation.get_quote')) ?>
                    </a>
                </div>
            </div>
        </div>

        <h2 class="prd-specs-title" data-reveal><?= esc(lang('Products.specs_title')) ?></h2>

        <?php if (count($product['variants']) > 1): ?>
            <div class="prd-tabs" role="tablist" aria-label="<?= esc(lang('Products.variants_tab_label'), 'attr') ?>" data-reveal data-prd-tabs>
                <?php foreach ($product['variants'] as $vi => $variant): ?>
                    <button class="prd-tab<?= $vi === 0 ? ' is-active' : '' ?>" type="button" role="tab"
                            id="prd-tab-<?= esc($variant['slug'], 'attr') ?>"
                            aria-controls="prd-panel-<?= esc($variant['slug'], 'attr') ?>"
                            aria-selected="<?= $vi === 0 ? 'true' : 'false' ?>"
                            data-prd-tab="<?= esc((string) $vi, 'attr') ?>">
                        <span class="prd-tab__firm"><?= esc(lang('Products.variant_label', [$vi + 1])) ?></span>
                        <span class="prd-tab__model"><?= esc($variant['model']) ?></span>
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php foreach ($product['variants'] as $vi => $variant): ?>
            <?php $sections = ProductCatalog::masterSections($variant); ?>
            <div class="prd-variant<?= $vi === 0 ? ' is-active' : '' ?>" role="tabpanel"
                 id="prd-panel-<?= esc($variant['slug'], 'attr') ?>"
                 aria-labelledby="prd-tab-<?= esc($variant['slug'], 'attr') ?>"
                 data-prd-panel="<?= esc((string) $vi, 'attr') ?>"
                 <?= $vi !== 0 ? 'hidden' : '' ?>>

                <?php if (count($product['variants']) === 1 && $variant['model'] !== $product['name']): ?>
                    <p class="prd-variant__single"><?= esc($variant['model']) ?></p>
                <?php endif; ?>

                <?php if ($sections === []): ?>
                    <div class="prd-nospec">
                        <p><?= esc(lang('Products.contact_for_specs')) ?></p>
                    </div>
                <?php else: ?>

                    <div class="prd-specs">
                        <?php foreach ($sections as $section): ?>
                            <section class="prd-spec-group is-open" data-prd-acc>
                                <button class="prd-spec-group__head" type="button" data-prd-acc-toggle aria-expanded="true">
                                    <svg class="prd-spec-group__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                         stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="<?= esc($masterIcons[$section['key']], 'attr') ?>"></path>
                                    </svg>
                                    <span class="prd-spec-group__title"><?= esc(lang('Products.specs_' . $section['key'])) ?></span>
                                    <svg class="prd-spec-group__chev" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M6 9l6 6 6-6"></path>
                                    </svg>
                                </button>

                                <div class="prd-spec-group__body" data-prd-acc-body>
                                    <div class="prd-spec-group__inner">
                                    <?php
                                    // Uniform blocks (ProductCatalog::masterSections):
                                    // sub → inline sub-heading, kv → aligned rows,
                                    // list → bullets, text → paragraph. Verbatim.
                                    foreach ($section['blocks'] as $block) {
                                        if ($block[0] === 'sub') {
                                            echo '<h3 class="prd-spec-sub">' . esc($block[1]) . '</h3>';
                                        } elseif ($block[0] === 'kv') {
                                            echo '<dl class="prd-kv">';
                                            foreach ($block[1] as $row) {
                                                echo '<div class="prd-kv__row">'
                                                    . '<dt class="prd-kv__key">' . esc($row[0]) . '</dt>'
                                                    . '<dd class="prd-kv__val">' . esc($row[1]) . '</dd>'
                                                    . '</div>';
                                            }
                                            echo '</dl>';
                                        } elseif ($block[0] === 'list') {
                                            echo '<ul class="prd-spec-list">';
                                            foreach ($block[1] as $item) {
                                                echo '<li>' . esc($item) . '</li>';
                                            }
                                            echo '</ul>';
                                        } else {
                                            echo '<p class="prd-spec-text">' . esc($block[1]) . '</p>';
                                        }
                                    }
                                    ?>
                                    </div>
                                </div>
                            </section>
                        <?php endforeach; ?>
                    </div>

                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <?php if ($related !== []): ?>
            <div class="prd-related" data-reveal>
                <h2 class="prd-related__title"><?= esc(lang('Products.related_title')) ?></h2>
                <div class="prd-related__grid">
                    <?php foreach ($related as $rel): ?>
                        <a class="prd-related-card" href="<?= esc(locale_url('products/' . $category['slug'] . '/' . $rel['slug'])) ?>">
                            <span class="prd-related-card__media"
                                  data-img="products/<?= esc($category['slug'], 'attr') ?>-<?= esc($rel['slug'], 'attr') ?>.jpg"
                                  aria-hidden="true"></span>
                            <span class="prd-related-card__body">
                                <span class="prd-related-card__name"><?= esc($rel['name']) ?></span>
                                <span class="prd-related-card__count">
                                    <?= count($rel['variants']) ?> <?= esc(lang('Products.models_label')) ?>
                                </span>
                            </span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="prd-cta" data-reveal>
            <div class="prd-cta__text">
                <strong><?= esc(lang('Products.cta_title')) ?></strong>
                <span><?= esc(lang('Products.cta_product_text', [$product['name']])) ?></span>
            </div>
            <div class="prd-cta__actions">
                <a class="btn btn--primary" href="<?= esc(locale_url('contact')) ?>">
                    <?= esc(lang('Navigation.get_quote')) ?>
                </a>
                <a class="btn btn--outline" href="<?= esc(locale_url('products/' . $category['slug'])) ?>">
                    <?= esc(lang('Products.back_to_category')) ?>
                </a>
            </div>
        </div>

    </div>
</section>

<?php $this->endSection() ?>

<?php $this->section('scripts') ?>
<script src="<?= $asset('assets/js/products.js') ?>" defer></script>
<?php $this->endSection() ?>
