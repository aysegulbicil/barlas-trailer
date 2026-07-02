<?php

/**
 * FAQ page — native <details> akordeonu (JS'siz, erişilebilir) +
 * FAQPage JSON-LD (SEO). İçerik: app/Language/{locale}/Faq.php.
 */
$this->extend('layouts/inner');

$ld = [
    '@context'   => 'https://schema.org',
    '@type'      => 'FAQPage',
    'mainEntity' => array_map(static fn (array $i): array => [
        '@type'          => 'Question',
        'name'           => (string) ($i['q'] ?? ''),
        'acceptedAnswer' => [
            '@type' => 'Answer',
            'text'  => (string) ($i['a'] ?? ''),
        ],
    ], $items),
];
?>

<?php $this->section('bodyClass') ?>faq<?php $this->endSection() ?>

<?php $this->section('content') ?>

<?= view('partials/page-hero', [
    'pageTitle'   => lang('Faq.page_title'),
    'pageLead'    => lang('Faq.page_lead'),
    'breadcrumbs' => [
        ['label' => lang('Navigation.faq')],
    ],
]) ?>

<section class="faq-section">
    <div class="container faq-list">
        <?php foreach ($items as $i => $item): ?>
            <details class="faq-item" <?= $i === 0 ? 'open' : '' ?>>
                <summary class="faq-item__q">
                    <span><?= esc($item['q'] ?? '') ?></span>
                    <svg class="faq-item__chev" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M6 9l6 6 6-6"></path>
                    </svg>
                </summary>
                <p class="faq-item__a"><?= esc($item['a'] ?? '') ?></p>
            </details>
        <?php endforeach; ?>

        <div class="faq-cta">
            <p><?= esc(lang('Faq.cta_text')) ?></p>
            <div class="faq-cta__actions">
                <a class="btn btn--primary" href="<?= esc(locale_url('contact')) ?>"><?= esc(lang('Navigation.contact')) ?></a>
                <a class="btn btn--outline" href="<?= esc(locale_url('teklif')) ?>"><?= esc(lang('Navigation.get_quote')) ?></a>
            </div>
        </div>
    </div>
</section>

<script type="application/ld+json"><?= json_encode($ld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>

<?php $this->endSection() ?>

<?php $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/faq-legal.css') ?>?v=<?= is_file(FCPATH . 'assets/css/faq-legal.css') ? filemtime(FCPATH . 'assets/css/faq-legal.css') : '1' ?>">
<?php $this->endSection() ?>
