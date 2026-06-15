<?php

/**
 * Home / Product categories — horizontal scroll pan
 *
 * The 8 image-led product cards now live on a single horizontal track.
 * On desktop the section pins and vertical scroll pans the track
 * (GSAP ScrollTrigger in flow.js, canonical horizontal-pan skeleton);
 * on mobile / reduced-motion the track degrades to a native
 * scroll-snap strip. Card markup (and its hover reveal) is unchanged.
 */
$categories = [
    ['slug' => 'tankers',    'key' => 'tankers',    'icon' => 'M3 13h18l-2 6H5z M4 13V8h12l3 5'],
    ['slug' => 'silos',      'key' => 'silos',      'icon' => 'M5 21V9l7-5 7 5v12 M9 21v-6h6v6'],
    ['slug' => 'tippers',    'key' => 'tippers',    'icon' => 'M3 17h13l3-7H7z M16 17a2 2 0 104 0'],
    ['slug' => 'dry-cargo',  'key' => 'dry_cargo',  'icon' => 'M4 7h16v10H4z M4 7l8-3 8 3'],
    ['slug' => 'curtain',    'key' => 'curtain',    'icon' => 'M4 6h16v12H4z M8 6v12 M12 6v12 M16 6v12'],
    ['slug' => 'reefer',     'key' => 'reefer',     'icon' => 'M4 6h16v12H4z M12 9v6 M9 12h6'],
    ['slug' => 'lowbed',     'key' => 'lowbed',     'icon' => 'M2 15h20 M4 15v-4h10l3 4 M6 18a1.5 1.5 0 100-3'],
    ['slug' => 'containers', 'key' => 'containers', 'icon' => 'M3 8h18v9H3z M7 8v9 M11 8v9 M15 8v9'],
];
?>
<section class="categories" id="products" data-cat-section>
    <div class="container">
        <div class="section-head" data-aos="fade-up">
            <span class="section-head__eyebrow"><?= esc(lang('Home.categories_eyebrow')) ?></span>
            <h2 class="section-head__title"><?= esc(lang('Home.categories_title')) ?></h2>
            <p class="section-head__lead"><?= esc(lang('Home.categories_lead')) ?></p>
        </div>
    </div>

    <div class="categories__viewport" data-cat-viewport>
        <div class="categories__track" data-cat-track role="list">
            <?php foreach ($categories as $i => $cat): ?>
                <a class="product-card" role="listitem" href="<?= esc(locale_url('products/' . $cat['slug'])) ?>">
                    <span class="product-card__num"><?= esc(str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT)) ?></span>

                    <span class="product-card__media" data-img="category-<?= esc($cat['slug'], 'attr') ?>.jpg" aria-hidden="true">
                        <span class="product-card__silhouette">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="<?= esc($cat['icon'], 'attr') ?>"></path>
                            </svg>
                        </span>
                        <span class="product-card__overlay"></span>
                    </span>

                    <span class="product-card__body">
                        <span class="product-card__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="<?= esc($cat['icon'], 'attr') ?>"></path>
                            </svg>
                        </span>
                        <span class="product-card__title"><?= esc(lang('Home.cat_' . $cat['key'])) ?></span>
                        <span class="product-card__desc"><?= esc(lang('Home.cat_' . $cat['key'] . '_desc')) ?></span>
                        <span class="product-card__more"><?= esc(lang('Home.discover')) ?> &rarr;</span>
                    </span>
                </a>
            <?php endforeach; ?>

            <!-- End cap: the single "all products" exit of the strip -->
            <a class="categories__endcap" role="listitem" href="<?= esc(locale_url('products')) ?>">
                <span class="categories__endcap-count"><?= esc(str_pad((string) count($categories), 2, '0', STR_PAD_LEFT)) ?>+</span>
                <span class="categories__endcap-label"><?= esc(lang('Home.view_all_products')) ?></span>
                <span class="categories__endcap-arrow" aria-hidden="true">&rarr;</span>
            </a>
        </div>
    </div>
</section>
