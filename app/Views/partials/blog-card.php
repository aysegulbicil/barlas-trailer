<?php

/**
 * Blog card partial
 *
 * Single post card used on the blog list grid and in the "related posts"
 * section. Deliberately minimal: cover image, publication date, title,
 * excerpt and a read-more link — nothing else.
 *
 * Render with view('partials/blog-card', ['post' => $post]) where $post
 * carries: slug, image, title, excerpt, date, dateFormatted.
 */
$detailUrl = locale_url('blog/' . $post['slug']);
?>
<article class="blog-card">
    <a class="blog-card__media media-frame" href="<?= esc($detailUrl) ?>"
       data-img="blog/<?= esc($post['image'], 'attr') ?>" tabindex="-1" aria-hidden="true">
        <span class="media-frame__placeholder" aria-hidden="true">
            <svg viewBox="0 0 80 80" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 58h52M18 58V34l14-10 14 10v24M50 58V30h12v28"></path>
                <path d="M25 58v-9h8v9"></path>
            </svg>
        </span>
    </a>

    <div class="blog-card__body">
        <time class="blog-card__date" datetime="<?= esc($post['date'], 'attr') ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                <path d="M3 10h18M8 3v4M16 3v4"></path>
            </svg>
            <?= esc($post['dateFormatted']) ?>
        </time>

        <h3 class="blog-card__title">
            <a href="<?= esc($detailUrl) ?>"><?= esc($post['title']) ?></a>
        </h3>

        <p class="blog-card__excerpt"><?= esc($post['excerpt']) ?></p>

        <a class="blog-card__more" href="<?= esc($detailUrl) ?>">
            <?= esc(lang('Blog.read_more')) ?>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M5 12h14M13 6l6 6-6 6"></path>
            </svg>
        </a>
    </div>
</article>
