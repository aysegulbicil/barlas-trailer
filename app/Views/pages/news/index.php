<?php

/**
 * News list — üretim haberleri akışı (§7.6). Wiki ile aynı kart düzenini
 * ve wiki.css'i bilinçli paylaşır (ortak "madde listesi" deseni).
 */
$this->extend('layouts/inner');
?>

<?php $this->section('bodyClass') ?>news<?php $this->endSection() ?>

<?php $this->section('content') ?>

<?= view('partials/page-hero', [
    'pageTitle'   => lang('News.page_title'),
    'pageLead'    => lang('News.page_lead'),
    'breadcrumbs' => [
        ['label' => lang('News.breadcrumb')],
    ],
]) ?>

<section class="wiki-section">
    <div class="container">
        <?php if ($items === []): ?>
            <p class="wiki-empty"><?= esc(lang('News.empty')) ?></p>
        <?php else: ?>
            <div class="wiki-grid" data-reveal-group>
                <?php foreach ($items as $item): ?>
                    <a class="wiki-card" href="<?= esc(locale_url('news/' . $item['slug'])) ?>">
                        <h2 class="wiki-card__title"><?= esc($item['title']) ?></h2>
                        <p class="wiki-card__desc"><?= esc($item['description']) ?></p>
                        <?php if ($item['updated'] !== ''): ?>
                            <span class="wiki-card__desc"><?= esc(lang('News.published')) ?>: <?= esc($item['updated']) ?></span>
                        <?php endif; ?>
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
