<?php

/**
 * Basın Odası — marka varlıkları arşivi (public/assets/brand otomatik
 * taranır) + kurumsal boilerplate + basın iletişimi.
 */
$this->extend('layouts/inner');
?>

<?php $this->section('bodyClass') ?>press<?php $this->endSection() ?>

<?php $this->section('content') ?>

<?= view('partials/page-hero', [
    'pageTitle'   => lang('Press.page_title'),
    'pageLead'    => lang('Press.page_lead'),
    'breadcrumbs' => [
        ['label' => lang('Press.breadcrumb')],
    ],
]) ?>

<section class="press-section">
    <div class="container">

        <div class="press-boiler" data-reveal>
            <h2><?= esc(lang('Press.boilerplate_title')) ?></h2>
            <p><?= esc(lang('Press.boilerplate')) ?></p>
        </div>

        <h2 class="press-assets-title"><?= esc(lang('Press.assets_title')) ?></h2>

        <?php if ($assets === []): ?>
            <p class="press-empty"><?= esc(lang('Press.empty')) ?></p>
        <?php else: ?>
            <div class="press-grid" data-reveal-group>
                <?php foreach ($assets as $asset): ?>
                    <figure class="press-card">
                        <span class="press-card__preview">
                            <img src="<?= esc($asset['url']) ?>" alt="<?= esc($asset['label'], 'attr') ?>" loading="lazy" decoding="async">
                        </span>
                        <figcaption class="press-card__meta">
                            <span class="press-card__name"><?= esc($asset['label']) ?></span>
                            <span class="press-card__info"><?= esc($asset['dim']) ?> · <?= esc((string) $asset['kb']) ?> KB</span>
                        </figcaption>
                        <a class="press-card__dl" href="<?= esc($asset['url']) ?>" download>
                            <?= esc(lang('Press.download')) ?>
                        </a>
                    </figure>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="press-contact" data-reveal>
            <h2><?= esc(lang('Press.contact_title')) ?></h2>
            <p><?= esc(lang('Press.contact_text')) ?></p>
            <a class="btn btn--primary" href="mailto:<?= esc(lang('Common.email'), 'attr') ?>"><?= esc(lang('Common.email')) ?></a>
        </div>
    </div>
</section>

<?php $this->endSection() ?>

<?php $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/press.css') ?>?v=<?= is_file(FCPATH . 'assets/css/press.css') ? filemtime(FCPATH . 'assets/css/press.css') : '1' ?>">
<?php $this->endSection() ?>
