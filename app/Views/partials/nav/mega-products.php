<?php

/**
 * Products mega menu panel
 *
 * Showcase-style panel: a numbered category rail on the inline-start
 * side, and a dynamic preview pane (description, featured product
 * links, CTA and a visual) for the active category.
 *
 * The 11 main categories and their featured products come straight from
 * the master catalogue (App\Libraries\ProductCatalog — same data as the
 * /products pages), so menu links always match real product pages.
 * Featured chips are the category's products with the most
 * manufacturer/model variants, shown in catalogue order.
 *
 * Visual hooks:
 *  - pane media:  assets/images/category-{slug}.jpg
 *  - chip hover:  assets/images/products/{cat}-{product}.jpg
 * Hovering/focusing a featured chip crossfades the pane media to the
 * product's visual with the product name (navigation.js, .mega-preview).
 * Photos are optional — until a file exists the blueprint placeholder
 * and the animated name reveal still work.
 *
 * Desktop: hover/focus on a rail row activates its pane (navigation.js).
 * Mobile: each row becomes an accordion with its pane below.
 */

use App\Libraries\ProductCatalog;

$megaIcons = [
    'tankers'    => 'M3 13h18l-2 6H5z M4 13V8h12l3 5',
    'silos'      => 'M5 21V9l7-5 7 5v12 M9 21v-6h6v6',
    'tippers'    => 'M3 17h13l3-7H7z M16 17a2 2 0 104 0',
    'curtain'    => 'M4 6h16v12H4z M8 6v12 M12 6v12 M16 6v12',
    'reefer'     => 'M4 6h16v12H4z M12 9v6 M9 12h6',
    'dry-cargo'  => 'M4 7h16v10H4z M4 7l8-3 8 3',
    'containers' => 'M3 8h18v9H3z M7 8v9 M11 8v9 M15 8v9',
    'lowbed'     => 'M2 15h20 M4 15v-4h10l3 4 M6 18a1.5 1.5 0 100-3',
    'platform'   => 'M3 14h18 M5 14v-2h14v2 M7 17.5a1.5 1.5 0 100-3 M17 17.5a1.5 1.5 0 100-3',
    'trailers'   => 'M3 7h13v8H3z M16 11h5 M6 18a1.5 1.5 0 100-3 M13 18a1.5 1.5 0 100-3',
    'special'    => 'M12 6v2 M12 16v2 M6 12h2 M16 12h2 M12 9a3 3 0 100 6 3 3 0 000-6',
];

$megaCategories = ProductCatalog::categories();

$megaCoverStyle = static function (array $category): string {
    $slug = $category['slug'];
    $categoryPath = 'assets/images/category-' . $slug . '.jpg';
    if (is_file(FCPATH . $categoryPath)) {
        return '';
    }

    foreach ($category['products'] ?? [] as $product) {
        $productPath = 'assets/images/products/' . $slug . '-' . $product['slug'] . '.jpg';
        if (is_file(FCPATH . $productPath)) {
            return 'background-image: url(\'' . base_url($productPath) . '?v=' . filemtime(FCPATH . $productPath) . '\'), var(--blueprint), var(--placeholder-dark); background-size: cover, var(--blueprint-size), var(--blueprint-size); background-position: center;';
        }
    }

    return '';
};

/** Localized category strings with catalogue fallback. */
$megaLang = static function (string $slug, string $suffix, string $fallback): string {
    $key   = 'Navigation.cat_' . str_replace('-', '_', $slug) . $suffix;
    $label = lang($key);
    return $label === $key ? $fallback : $label;
};
?>
<div class="mega mega--products" data-assets-base="<?= esc(base_url('assets/images'), 'attr') ?>">
    <!-- Mobil: sade kategori bağlantı listesi (masaüstünde gizli). -->
    <ul class="mega-mlist">
        <li><a class="mega-mlist__link mega-mlist__link--all" href="<?= esc(locale_url('products')) ?>"><?= esc(lang('Navigation.all_products')) ?></a></li>
        <?php foreach ($megaCategories as $cat): ?>
            <li><a class="mega-mlist__link" href="<?= esc(locale_url('products/' . $cat['slug'])) ?>"><?= esc($megaLang($cat['slug'], '', $cat['name'])) ?></a></li>
        <?php endforeach; ?>
    </ul>

    <div class="mega__head">
        <span class="mega__tagline"><?= esc(lang('Navigation.mega_products_tagline')) ?></span>
        <a class="mega__all" href="<?= esc(locale_url('products')) ?>">
            <?= esc(lang('Navigation.all_products')) ?>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M5 12h14 M13 6l6 6-6 6"></path>
            </svg>
        </a>
    </div>

    <div class="mega__body">
        <?php foreach ($megaCategories as $i => $cat): ?>
            <?php
            $slug   = $cat['slug'];
            $num    = str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT);
            $paneId = 'mega-pane-' . $slug;
            $name   = $megaLang($slug, '', $cat['name']);
            $desc   = $megaLang($slug, '_desc', '');
            $cta    = $megaLang($slug, '_cta', lang('Navigation.view_category'));
            $count  = ProductCatalog::variantCount($cat);
            $icon   = $megaIcons[$slug] ?? 'M4 6h16v12H4z';
            ?>
            <div class="mega-cat<?= $i === 0 ? ' is-active' : '' ?>" data-mega-cat>
                <div class="mega-cat__row">
                    <a class="mega-cat__link" href="<?= esc(locale_url('products/' . $slug)) ?>" data-mega-cat-link>
                        <span class="mega-cat__num" aria-hidden="true"><?= esc($num) ?></span>
                        <span class="mega-cat__name"><?= esc($name) ?></span>
                        <svg class="mega-cat__chev" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M9 6l6 6-6 6"></path>
                        </svg>
                    </a>
                    <button class="mega-cat__toggle" type="button" data-mega-cat-toggle
                            aria-expanded="false" aria-controls="<?= esc($paneId, 'attr') ?>"
                            aria-label="<?= esc(lang('Navigation.open_submenu') . ': ' . $name, 'attr') ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M6 9l6 6 6-6"></path>
                        </svg>
                    </button>
                </div>

                <div class="mega-cat__pane" id="<?= esc($paneId, 'attr') ?>">
                    <div class="mega-pane__inner">
                        <div class="mega-pane__info">
                            <p class="mega-pane__title"><?= esc($name) ?></p>
                            <?php if ($desc !== ''): ?>
                                <p class="mega-pane__desc"><?= esc($desc) ?></p>
                            <?php endif; ?>

                            <span class="mega-pane__label"><?= esc(lang('Navigation.featured_models')) ?></span>
                            <div class="mega-pane__chips">
                                <?php foreach (ProductCatalog::featured($cat) as $product): ?>
                                    <a class="mega-chip"
                                       href="<?= esc(locale_url('products/' . $slug . '/' . $product['slug'])) ?>"
                                       data-chip-img="products/<?= esc($slug, 'attr') ?>-<?= esc($product['slug'], 'attr') ?>.jpg"
                                       data-chip-name="<?= esc($product['name'], 'attr') ?>">
                                        <?= esc($product['name']) ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>

                            <div class="mega-pane__footer">
                                <span class="mega-pane__count">
                                    <span class="mega-pane__count-num"><?= esc((string) $count) ?></span>
                                    <span class="mega-pane__count-label"><?= esc(lang('Navigation.models_label')) ?></span>
                                </span>
                                <a class="mega-pane__cta" href="<?= esc(locale_url('products/' . $slug)) ?>">
                                    <?= esc($cta) ?>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M5 12h14 M13 6l6 6-6 6"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>

                        <div class="mega-pane__media" data-img="category-<?= esc($slug, 'attr') ?>.jpg" style="<?= esc($megaCoverStyle($cat), 'attr') ?>"
                             data-mega-media aria-hidden="true">
                            <svg class="mega-pane__silhouette" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="<?= esc($icon, 'attr') ?>"></path>
                            </svg>
                            <span class="mega-pane__corner mega-pane__corner--tl"></span>
                            <span class="mega-pane__corner mega-pane__corner--br"></span>
                            <span class="mega-pane__num"><?= esc($num) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
