<?php

/**
 * Page hero partial
 *
 * Reusable inner-page header: breadcrumb trail + H1 title + optional lead.
 * Render with view('partials/page-hero', [...]) and pass:
 *   - string $pageTitle    page H1 (required)
 *   - string $pageLead     short subtitle under the title (optional)
 *   - array  $breadcrumbs  trail items as ['label' => ..., 'url' => ...];
 *                          the last item is the current page (no url needed).
 *
 * The "home" crumb is always prepended automatically.
 */
$pageLead    ??= '';
$breadcrumbs ??= [];
?>
<section class="page-hero">
    <div class="container page-hero__inner">
        <nav class="page-hero__breadcrumb" aria-label="Breadcrumb">
            <ol class="breadcrumb-trail" itemscope itemtype="https://schema.org/BreadcrumbList">
                <li class="breadcrumb-trail__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a href="<?= esc(locale_url()) ?>" itemprop="item">
                        <span itemprop="name"><?= esc(lang('Navigation.home')) ?></span>
                    </a>
                    <meta itemprop="position" content="1">
                </li>
                <?php foreach ($breadcrumbs as $i => $crumb): ?>
                    <li class="breadcrumb-trail__item<?= empty($crumb['url']) ? ' is-current' : '' ?>"
                        itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"
                        <?= empty($crumb['url']) ? 'aria-current="page"' : '' ?>>
                        <?php if (! empty($crumb['url'])): ?>
                            <a href="<?= esc($crumb['url']) ?>" itemprop="item">
                                <span itemprop="name"><?= esc($crumb['label']) ?></span>
                            </a>
                        <?php else: ?>
                            <span itemprop="name"><?= esc($crumb['label']) ?></span>
                        <?php endif; ?>
                        <meta itemprop="position" content="<?= esc((string) ($i + 2), 'attr') ?>">
                    </li>
                <?php endforeach; ?>
            </ol>
        </nav>

        <h1 class="page-hero__title"><?= esc($pageTitle) ?></h1>

        <?php if ($pageLead !== ''): ?>
            <p class="page-hero__lead"><?= esc($pageLead) ?></p>
        <?php endif; ?>
    </div>
</section>
