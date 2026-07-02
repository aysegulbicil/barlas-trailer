<?php

/**
 * Günün Sorusu (§8) — soru bankasından tarihe göre deterministik seçim
 * (herkes aynı gün aynı soruyu görür). Cevap ve gün serisi (streak)
 * yalnız localStorage'da tutulur. Motor: public/assets/js/daily.js.
 */
$this->extend('layouts/inner');
?>

<?php $this->section('bodyClass') ?>daily<?php $this->endSection() ?>

<?php $this->section('content') ?>

<?= view('partials/page-hero', [
    'pageTitle'   => lang('Games.d_title'),
    'pageLead'    => lang('Games.d_lead'),
    'breadcrumbs' => [
        ['label' => lang('Games.breadcrumb'), 'url' => locale_url('games')],
        ['label' => lang('Games.d_title')],
    ],
]) ?>

<section class="daily-section">
    <div class="container">
        <div class="daily" data-daily
             data-str-correct="<?= esc(lang('Games.d_correct'), 'attr') ?>"
             data-str-wrong="<?= esc(lang('Games.d_wrong'), 'attr') ?>"
             data-str-answered="<?= esc(lang('Games.d_answered'), 'attr') ?>">

            <script type="application/json" data-daily-bank><?= json_encode($bank, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>

            <p class="daily__meta">
                <span data-daily-date></span>
                <span class="daily__streak"><?= esc(lang('Games.d_streak')) ?>: <b data-daily-streak>0</b> 🔥</span>
            </p>

            <h2 class="daily__q" data-daily-q></h2>

            <div class="daily__opts" data-daily-opts role="group"></div>

            <div class="daily__result" data-daily-result hidden>
                <p class="daily__verdict" data-daily-verdict></p>
                <p class="daily__why" data-daily-why></p>
                <p class="daily__tomorrow"><?= esc(lang('Games.d_tomorrow')) ?></p>
            </div>

            <a class="daily__back" href="<?= esc(locale_url('games')) ?>">← <?= esc(lang('Games.back_to_games')) ?></a>
        </div>
    </div>
</section>

<?php $this->endSection() ?>

<?php $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/games.css') ?>?v=<?= is_file(FCPATH . 'assets/css/games.css') ? filemtime(FCPATH . 'assets/css/games.css') : '1' ?>">
<?php $this->endSection() ?>

<?php $this->section('scripts') ?>
<script src="<?= base_url('assets/js/daily.js') ?>?v=<?= is_file(FCPATH . 'assets/js/daily.js') ? filemtime(FCPATH . 'assets/js/daily.js') : '1' ?>" defer></script>
<?php $this->endSection() ?>
