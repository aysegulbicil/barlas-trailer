<?php

/**
 * Araç Kimlik Kartı — /v/{hash} (QR hedefi).
 * Kişisel veri İÇERMEZ: yalnız araç bilgisi (şasi, model, yıl, garanti).
 */
$this->extend('layouts/inner');

$rows = [
    lang('Vehicle.chassis')   => $vehicle['chassis_no'],
    lang('Vehicle.model')     => $vehicle['model'],
    lang('Vehicle.year')      => $vehicle['year'] ?: '—',
    lang('Vehicle.delivered') => $vehicle['delivered_at'] ?: '—',
];

$warrantyText = [
    'active'  => lang('Vehicle.warranty_active') . ($vehicle['warranty_until'] ? ' (' . $vehicle['warranty_until'] . ')' : ''),
    'expired' => lang('Vehicle.warranty_expired'),
    'none'    => '—',
][$warranty];
?>

<?php $this->section('bodyClass') ?>vehicle<?php $this->endSection() ?>

<?php $this->section('content') ?>

<?= view('partials/page-hero', [
    'pageTitle'   => lang('Vehicle.card_title'),
    'pageLead'    => $vehicle['model'],
    'breadcrumbs' => [
        ['label' => lang('Vehicle.card_title')],
    ],
]) ?>

<section class="vcard-section">
    <div class="container">
        <div class="vcard" data-reveal>
            <p class="vcard__badge">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3 5 6v5c0 4.4 3 7.6 7 9 4-1.4 7-4.6 7-9V6z"></path><path d="m9 12 2 2 4-4"></path></svg>
                <?= esc(lang('Vehicle.authentic')) ?>
            </p>

            <dl class="vcard__rows">
                <?php foreach ($rows as $label => $value): ?>
                    <div class="vcard__row">
                        <dt><?= esc($label) ?></dt>
                        <dd><?= esc((string) $value) ?></dd>
                    </div>
                <?php endforeach; ?>
                <div class="vcard__row">
                    <dt><?= esc(lang('Vehicle.warranty')) ?></dt>
                    <dd class="vcard__warranty vcard__warranty--<?= esc($warranty, 'attr') ?>"><?= esc($warrantyText) ?></dd>
                </div>
            </dl>

            <div class="vcard__actions">
                <a class="btn btn--primary" href="<?= esc(locale_url('services')) ?>"><?= esc(lang('Vehicle.service_cta')) ?></a>
                <a class="btn btn--outline" href="<?= esc(locale_url('contact')) ?>"><?= esc(lang('Vehicle.contact_cta')) ?></a>
            </div>
        </div>
    </div>
</section>

<?php $this->endSection() ?>

<?php $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/vehicle.css') ?>?v=<?= is_file(FCPATH . 'assets/css/vehicle.css') ? filemtime(FCPATH . 'assets/css/vehicle.css') : '1' ?>">
<?php $this->endSection() ?>
