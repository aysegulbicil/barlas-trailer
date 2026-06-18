<?php

/**
 * Services list page
 *
 * Inner-page hero + a responsive card grid of every service. Each card
 * links to its detail page (services/<slug>). Service names, icons and
 * one-line descriptions are shared with the header mega menu.
 */
$this->extend('layouts/inner');
?>

<?php $this->section('bodyClass') ?>services<?php $this->endSection() ?>

<?php $this->section('content') ?>

<?= view('partials/page-hero', [
    'pageTitle'   => lang('Services.page_title'),
    'pageLead'    => lang('Services.page_lead'),
    'breadcrumbs' => [
        ['label' => lang('Navigation.services')],
    ],
]) ?>

<section class="services-section">
    <div class="container">
        <div class="services-grid" data-reveal-group>
            <?php foreach ($services as $service): ?>
                <?= view('partials/service-card', ['service' => $service]) ?>
            <?php endforeach; ?>
        </div>

        <div class="services-cta" data-reveal>
            <div class="services-cta__text">
                <strong><?= esc(lang('Services.cta_title')) ?></strong>
                <span><?= esc(lang('Services.cta_text')) ?></span>
            </div>
            <div class="services-cta__actions">
                <a class="btn btn--primary" href="<?= esc(locale_url('teklif')) ?>">
                    <?= esc(lang('Navigation.get_quote')) ?>
                </a>
                <a class="btn btn--outline" href="tel:<?= esc(lang('Common.phone_raw'), 'attr') ?>">
                    <?= esc(lang('Services.call_button')) ?>
                </a>
            </div>
        </div>
    </div>
</section>

<?php $this->endSection() ?>
