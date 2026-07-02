<?php

/**
 * Şasi Doğrulama — /{locale}/verify. Şasi no ile orijinallik + garanti
 * sorgusu; sonuç aynı sayfada gösterilir.
 */
$this->extend('layouts/inner');
?>

<?php $this->section('bodyClass') ?>vehicle<?php $this->endSection() ?>

<?php $this->section('content') ?>

<?= view('partials/page-hero', [
    'pageTitle'   => lang('Vehicle.page_title'),
    'pageLead'    => lang('Vehicle.page_lead'),
    'breadcrumbs' => [
        ['label' => lang('Vehicle.breadcrumb')],
    ],
]) ?>

<section class="vcard-section">
    <div class="container">
        <form class="verify-form" method="post" action="<?= esc(locale_url('verify')) ?>">
            <?= csrf_field() ?>
            <input class="verify-form__hp" type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true">
            <label class="verify-form__label" for="chassis"><?= esc(lang('Vehicle.form_label')) ?></label>
            <div class="verify-form__row">
                <input class="verify-form__input" id="chassis" name="chassis" required maxlength="40"
                       placeholder="<?= esc(lang('Vehicle.form_placeholder'), 'attr') ?>"
                       value="<?= esc((string) old('chassis'), 'attr') ?>">
                <button class="btn btn--primary" type="submit"><?= esc(lang('Vehicle.form_submit')) ?></button>
            </div>
        </form>

        <?php if ($result === 'throttled'): ?>
            <div class="verify-result verify-result--warn" role="status">
                <p><?= esc(lang('Vehicle.throttle')) ?></p>
            </div>
        <?php elseif ($result === 'found' && $vehicle !== null): ?>
            <div class="verify-result verify-result--ok" role="status">
                <h2><?= esc(lang('Vehicle.found_title')) ?></h2>
                <p><?= esc(lang('Vehicle.found_text')) ?></p>
                <ul class="verify-result__facts">
                    <li><strong><?= esc(lang('Vehicle.model')) ?>:</strong> <?= esc($vehicle['model']) ?></li>
                    <?php if ($vehicle['year']): ?><li><strong><?= esc(lang('Vehicle.year')) ?>:</strong> <?= esc((string) $vehicle['year']) ?></li><?php endif; ?>
                    <li><strong><?= esc(lang('Vehicle.warranty')) ?>:</strong>
                        <?= esc($warranty === 'active' ? lang('Vehicle.warranty_active') : ($warranty === 'expired' ? lang('Vehicle.warranty_expired') : '—')) ?>
                    </li>
                </ul>
                <a class="btn btn--outline" href="<?= esc(base_url('v/' . $vehicle['public_hash'])) ?>"><?= esc(lang('Vehicle.view_card')) ?></a>
            </div>
        <?php elseif ($result === 'notfound'): ?>
            <div class="verify-result verify-result--warn" role="status">
                <h2><?= esc(lang('Vehicle.notfound_title')) ?></h2>
                <p><?= esc(lang('Vehicle.notfound_text')) ?></p>
                <a class="btn btn--outline" href="<?= esc(locale_url('contact')) ?>"><?= esc(lang('Vehicle.contact_cta')) ?></a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php $this->endSection() ?>

<?php $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/vehicle.css') ?>?v=<?= is_file(FCPATH . 'assets/css/vehicle.css') ? filemtime(FCPATH . 'assets/css/vehicle.css') : '1' ?>">
<?php $this->endSection() ?>
