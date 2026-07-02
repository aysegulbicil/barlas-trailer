<?php

/**
 * Legal page template — KVKK / Gizlilik / Çerez (tek şablon).
 * $page = ['title', 'intro', 'sections' => [['h', 'p' => [...]]]]
 *
 * NOT: Metinler bilgilendirme amaçlı taslaktır; canlıya çıkmadan önce
 * hukuk danışmanı onayından geçirilmelidir.
 */
$this->extend('layouts/inner');
?>

<?php $this->section('bodyClass') ?>legal<?php $this->endSection() ?>

<?php $this->section('content') ?>

<?= view('partials/page-hero', [
    'pageTitle'   => (string) ($page['title'] ?? ''),
    'pageLead'    => (string) ($page['intro'] ?? ''),
    'breadcrumbs' => [
        ['label' => (string) ($page['title'] ?? '')],
    ],
]) ?>

<section class="legal-section">
    <div class="container legal-body">
        <?php foreach (($page['sections'] ?? []) as $section): ?>
            <section class="legal-block">
                <h2 class="legal-block__title"><?= esc($section['h'] ?? '') ?></h2>
                <?php foreach (($section['p'] ?? []) as $paragraph): ?>
                    <p class="legal-block__text"><?= esc($paragraph) ?></p>
                <?php endforeach; ?>
            </section>
        <?php endforeach; ?>

        <nav class="legal-links" aria-label="Legal">
            <a href="<?= esc(locale_url('kvkk')) ?>"><?= esc(lang('Common.footer_kvkk')) ?></a>
            <a href="<?= esc(locale_url('privacy')) ?>"><?= esc(lang('Common.footer_privacy')) ?></a>
            <a href="<?= esc(locale_url('cookies')) ?>"><?= esc(lang('Common.footer_cookies')) ?></a>
            <a href="<?= esc(locale_url('contact')) ?>"><?= esc(lang('Navigation.contact')) ?></a>
        </nav>
    </div>
</section>

<?php $this->endSection() ?>

<?php $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/faq-legal.css') ?>?v=<?= is_file(FCPATH . 'assets/css/faq-legal.css') ? filemtime(FCPATH . 'assets/css/faq-legal.css') : '1' ?>">
<?php $this->endSection() ?>
