<?php

/**
 * Blog list page
 *
 * Left sidebar (search + categories) and a two-column post card grid.
 * Filtering is server-side via GET params (?q=, ?category=) so the page
 * stays crawlable and works without JavaScript.
 */
$this->extend('layouts/inner');

$isFiltered = $searchQuery !== '' || $activeCategory !== '';
$totalCount = array_sum($categoryCounts);
?>

<?php $this->section('bodyClass') ?>blog<?php $this->endSection() ?>

<?php $this->section('content') ?>

<?= view('partials/page-hero', [
    'pageTitle'   => lang('Blog.page_title'),
    'pageLead'    => lang('Blog.page_lead'),
    'breadcrumbs' => [
        ['label' => lang('Navigation.blog')],
    ],
]) ?>

<section class="blog-section">
    <div class="container blog-layout">

        <!-- Sidebar: search + categories only -->
        <aside class="blog-sidebar" aria-label="<?= esc(lang('Blog.search_title') . ' / ' . lang('Blog.categories_title'), 'attr') ?>">

            <div class="sidebar-widget" data-aos="fade-up">
                <h2 class="sidebar-widget__title"><?= esc(lang('Blog.search_title')) ?></h2>
                <form class="sidebar-search" action="<?= esc(locale_url('blog')) ?>" method="get" role="search">
                    <?php if ($activeCategory !== ''): ?>
                        <input type="hidden" name="category" value="<?= esc($activeCategory, 'attr') ?>">
                    <?php endif; ?>
                    <input class="sidebar-search__input" type="search" name="q"
                           value="<?= esc($searchQuery, 'attr') ?>"
                           placeholder="<?= esc(lang('Blog.search_placeholder'), 'attr') ?>"
                           aria-label="<?= esc(lang('Blog.search_placeholder'), 'attr') ?>">
                    <button class="sidebar-search__submit" type="submit" aria-label="<?= esc(lang('Blog.search_submit'), 'attr') ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="M21 21l-4.3-4.3"></path>
                        </svg>
                    </button>
                </form>
            </div>

            <div class="sidebar-widget" data-aos="fade-up" data-aos-delay="90">
                <h2 class="sidebar-widget__title"><?= esc(lang('Blog.categories_title')) ?></h2>
                <ul class="sidebar-categories">
                    <li>
                        <a class="sidebar-categories__link<?= $activeCategory === '' ? ' is-active' : '' ?>"
                           href="<?= esc(locale_url('blog')) ?>"
                           <?= $activeCategory === '' ? 'aria-current="true"' : '' ?>>
                            <span><?= esc(lang('Blog.all_posts')) ?></span>
                            <span class="sidebar-categories__count"><?= esc((string) $totalCount) ?></span>
                        </a>
                    </li>
                    <?php foreach ($categories as $category): ?>
                        <li>
                            <a class="sidebar-categories__link<?= $activeCategory === $category ? ' is-active' : '' ?>"
                               href="<?= esc(locale_url('blog') . '?category=' . $category) ?>"
                               <?= $activeCategory === $category ? 'aria-current="true"' : '' ?>>
                                <span><?= esc(lang('Blog.cat_' . $category)) ?></span>
                                <span class="sidebar-categories__count"><?= esc((string) ($categoryCounts[$category] ?? 0)) ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

        </aside>

        <!-- Post grid -->
        <div class="blog-main">

            <?php if ($isFiltered): ?>
                <div class="blog-filterbar" data-aos="fade-up">
                    <p class="blog-filterbar__info">
                        <strong><?= esc((string) count($posts)) ?></strong> <?= esc(lang('Blog.results_found')) ?>
                        <?php if ($searchQuery !== ''): ?>
                            &mdash; &ldquo;<?= esc($searchQuery) ?>&rdquo;
                        <?php endif; ?>
                    </p>
                    <a class="blog-filterbar__clear" href="<?= esc(locale_url('blog')) ?>">
                        <?= esc(lang('Blog.clear_filters')) ?>
                    </a>
                </div>
            <?php endif; ?>

            <?php if ($posts === []): ?>
                <div class="blog-empty" data-aos="fade-up">
                    <span class="blog-empty__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="M21 21l-4.3-4.3M8 11h6"></path>
                        </svg>
                    </span>
                    <h2 class="blog-empty__title"><?= esc(lang('Blog.no_results_title')) ?></h2>
                    <p class="blog-empty__text"><?= esc(lang('Blog.no_results_text')) ?></p>
                    <a class="btn btn--outline" href="<?= esc(locale_url('blog')) ?>"><?= esc(lang('Blog.clear_filters')) ?></a>
                </div>
            <?php else: ?>
                <div class="blog-grid">
                    <?php foreach ($posts as $i => $post): ?>
                        <div data-aos="fade-up" data-aos-delay="<?= esc((string) (($i % 2) * 90), 'attr') ?>">
                            <?= view('partials/blog-card', ['post' => $post]) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>

    </div>
</section>

<?php $this->endSection() ?>
