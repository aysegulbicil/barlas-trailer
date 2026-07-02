<?php

/**
 * News article page — render edilmiş markdown gövde (§7.6). Wiki madde
 * tipografisini ve wiki.css'i bilinçli paylaşır. $item['html']
 * MarkdownContent tarafından güvenli üretilir (html_input strip).
 */
$this->extend('layouts/inner');
?>

<?php $this->section('bodyClass') ?>news news-detail<?php $this->endSection() ?>

<?php $this->section('content') ?>

<?= view('partials/page-hero', [
    'pageTitle'   => $item['title'],
    'pageLead'    => $item['description'],
    'breadcrumbs' => [
        ['label' => lang('News.breadcrumb'), 'url' => locale_url('news')],
        ['label' => $item['title']],
    ],
]) ?>

<section class="wiki-section">
    <div class="container">
        <article class="wiki-article">
            <?php if ($item['updated'] !== ''): ?>
                <p class="wiki-article__meta"><?= esc(lang('News.published')) ?>: <?= esc($item['updated']) ?></p>
            <?php endif; ?>

            <?= view('partials/listen-button', ['target' => '.wiki-article__body']) ?>

            <div class="wiki-article__body">
                <?= $item['html'] ?>
            </div>

            <p class="wiki-article__back">
                <a href="<?= esc(locale_url('news')) ?>">← <?= esc(lang('News.all_news')) ?></a>
            </p>
        </article>
    </div>
</section>

<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "NewsArticle",
    "headline": <?= json_encode($item['title'], JSON_UNESCAPED_UNICODE) ?>,
    "description": <?= json_encode($item['description'], JSON_UNESCAPED_UNICODE) ?>,
    "datePublished": <?= json_encode($item['updated']) ?>,
    "inLanguage": <?= json_encode(current_locale()) ?>,
    "publisher": { "@type": "Organization", "name": <?= json_encode(lang('Common.site_name'), JSON_UNESCAPED_UNICODE) ?> }
}
</script>

<?php $this->endSection() ?>

<?php $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/wiki.css') ?>?v=<?= is_file(FCPATH . 'assets/css/wiki.css') ? filemtime(FCPATH . 'assets/css/wiki.css') : '1' ?>">
<?php $this->endSection() ?>
