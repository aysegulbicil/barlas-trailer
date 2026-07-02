<?php

/**
 * Wiki article page — render edilmiş markdown gövde + ilgili maddeler.
 * $item['html'] MarkdownContent tarafından güvenli üretilir (html_input
 * strip, unsafe link yok) — bu yüzden esc'siz basılır.
 */
$this->extend('layouts/inner');
?>

<?php $this->section('bodyClass') ?>wiki<?php $this->endSection() ?>

<?php $this->section('content') ?>

<?= view('partials/page-hero', [
    'pageTitle'   => $item['title'],
    'pageLead'    => $item['description'],
    'breadcrumbs' => [
        ['label' => lang('Wiki.breadcrumb'), 'url' => locale_url('wiki')],
        ['label' => $item['title']],
    ],
]) ?>

<section class="wiki-section">
    <div class="container wiki-layout">
        <article class="wiki-article">
            <?php if ($item['updated'] !== ''): ?>
                <p class="wiki-article__meta"><?= esc(lang('Wiki.updated')) ?>: <?= esc($item['updated']) ?></p>
            <?php endif; ?>

            <?= view('partials/listen-button', ['target' => '.wiki-article__body']) ?>

            <div class="wiki-article__body">
                <?= $item['html'] ?>
            </div>

            <p class="wiki-article__back">
                <a href="<?= esc(locale_url('wiki')) ?>">← <?= esc(lang('Wiki.all_articles')) ?></a>
            </p>
        </article>

        <?php if ($related !== []): ?>
            <aside class="wiki-related" aria-label="<?= esc(lang('Wiki.all_articles'), 'attr') ?>">
                <?php foreach ($related as $rel): ?>
                    <a class="wiki-card wiki-card--small" href="<?= esc(locale_url('wiki/' . $rel['slug'])) ?>">
                        <h2 class="wiki-card__title"><?= esc($rel['title']) ?></h2>
                        <p class="wiki-card__desc"><?= esc(mb_substr($rel['description'], 0, 90, 'UTF-8')) ?>…</p>
                    </a>
                <?php endforeach; ?>
            </aside>
        <?php endif; ?>
    </div>
</section>

<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Article",
    "headline": <?= json_encode($item['title'], JSON_UNESCAPED_UNICODE) ?>,
    "description": <?= json_encode($item['description'], JSON_UNESCAPED_UNICODE) ?>,
    "dateModified": <?= json_encode($item['updated']) ?>,
    "inLanguage": <?= json_encode(current_locale()) ?>,
    "author": { "@type": "Organization", "name": <?= json_encode(lang('Common.site_name'), JSON_UNESCAPED_UNICODE) ?> }
}
</script>

<?php $this->endSection() ?>

<?php $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/wiki.css') ?>?v=<?= is_file(FCPATH . 'assets/css/wiki.css') ? filemtime(FCPATH . 'assets/css/wiki.css') : '1' ?>">
<?php $this->endSection() ?>
