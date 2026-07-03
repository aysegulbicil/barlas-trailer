<?php

/**
 * Blog detail page
 *
 * Breadcrumb hero with the post title, publication date + category,
 * article body and a 3-card "related posts" section. Text-only posts
 * (no cover images); no author, comments, tags or share buttons.
 */
$this->extend('layouts/inner');
?>

<?php $this->section('bodyClass') ?>blog-detail<?php $this->endSection() ?>

<?php $this->section('content') ?>

<?= view('partials/page-hero', [
    'pageTitle'   => $post['title'],
    'breadcrumbs' => [
        ['label' => lang('Navigation.blog'), 'url' => locale_url('blog')],
        ['label' => $post['title']],
    ],
]) ?>

<article class="blog-article">
    <div class="container blog-article__inner">

        <!-- Publication date + category -->
        <div class="blog-article__meta" data-reveal>
            <time class="blog-article__date" datetime="<?= esc($post['date'], 'attr') ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                    <path d="M3 10h18M8 3v4M16 3v4"></path>
                </svg>
                <?= esc($post['dateFormatted']) ?>
            </time>
            <a class="blog-article__category" href="<?= esc(locale_url('blog') . '?category=' . $post['category']) ?>">
                <?= esc($post['categoryLabel']) ?>
            </a>
        </div>

        <?= view('partials/listen-button', ['target' => '.blog-article__content']) ?>

        <!-- Article body (trusted developer-authored HTML from language files) -->
        <div class="blog-article__content" data-reveal>
            <?= $post['content'] ?>
        </div>

        <div class="blog-article__footer" data-reveal>
            <a class="btn btn--outline" href="<?= esc(locale_url('blog')) ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M19 12H5M11 18l-6-6 6-6"></path>
                </svg>
                <?= esc(lang('Blog.back_to_blog')) ?>
            </a>
        </div>

    </div>
</article>

<?php if ($related !== []): ?>
    <section class="blog-related">
        <div class="container">
            <div class="section-head" data-reveal="down">
                <span class="section-head__eyebrow"><?= esc(lang('Blog.related_eyebrow')) ?></span>
                <h2 class="section-head__title"><?= esc(lang('Blog.related_title')) ?></h2>
            </div>

            <div class="blog-related__grid" data-reveal-group>
                <?php foreach ($related as $relatedPost): ?>
                    <div>
                        <?= view('partials/blog-card', ['post' => $relatedPost]) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "BlogPosting",
    "headline": <?= json_encode($post['title'], JSON_UNESCAPED_UNICODE) ?>,
    "description": <?= json_encode($post['excerpt'], JSON_UNESCAPED_UNICODE) ?>,
    "datePublished": <?= json_encode($post['date']) ?>,
    "inLanguage": <?= json_encode(current_locale()) ?>,
    "mainEntityOfPage": <?= json_encode(locale_url('blog/' . $post['slug'])) ?>,
    "publisher": {
        "@type": "Organization",
        "name": <?= json_encode(lang('Common.site_name'), JSON_UNESCAPED_UNICODE) ?>
    }
}
</script>

<?php $this->endSection() ?>
