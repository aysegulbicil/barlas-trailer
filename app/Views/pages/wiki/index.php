<?php

/**
 * Wiki (Treyler Ansiklopedisi) list page — markdown maddelerinin kart
 * listesi. İçerik MarkdownContent::items()'tan gelir.
 */
$this->extend('layouts/inner');
?>

<?php $this->section('bodyClass') ?>wiki<?php $this->endSection() ?>

<?php $this->section('content') ?>

<?= view('partials/page-hero', [
    'pageTitle'   => lang('Wiki.page_title'),
    'pageLead'    => lang('Wiki.page_lead'),
    'breadcrumbs' => [
        ['label' => lang('Wiki.breadcrumb')],
    ],
]) ?>

<section class="wiki-section">
    <div class="container">
        <?php if ($items === []): ?>
            <p class="wiki-empty"><?= esc(lang('Wiki.empty')) ?></p>
        <?php else: ?>
            <div class="wiki-grid" data-reveal-group>
                <?php foreach ($items as $item): ?>
                    <a class="wiki-card" href="<?= esc(locale_url('wiki/' . $item['slug'])) ?>">
                        <h2 class="wiki-card__title"><?= esc($item['title']) ?></h2>
                        <p class="wiki-card__desc"><?= esc($item['description']) ?></p>
                        <span class="wiki-card__more" aria-hidden="true">→</span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php $this->endSection() ?>

<?php $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/wiki.css') ?>?v=<?= is_file(FCPATH . 'assets/css/wiki.css') ? filemtime(FCPATH . 'assets/css/wiki.css') : '1' ?>">
<?php $this->endSection() ?>
