<?php

/**
 * Treyler Tetris (§8) — yükleri dorseye verimli yerleştirme oyunu.
 * Motor: public/assets/js/tetris.js (canvas, klavye + dokunmatik).
 * Skor/rekor yalnız localStorage'da tutulur.
 */
$this->extend('layouts/inner');
?>

<?php $this->section('bodyClass') ?>tetris<?php $this->endSection() ?>

<?php $this->section('content') ?>

<?= view('partials/page-hero', [
    'pageTitle'   => lang('Games.t_title'),
    'pageLead'    => lang('Games.t_lead'),
    'breadcrumbs' => [
        ['label' => lang('Games.breadcrumb'), 'url' => locale_url('games')],
        ['label' => lang('Games.t_title')],
    ],
]) ?>

<section class="tetris-section">
    <div class="container">
        <div class="tetris" data-tetris
             data-msg-gameover="<?= esc(lang('Games.t_game_over'), 'attr') ?>"
             data-msg-newbest="<?= esc(lang('Games.t_new_best'), 'attr') ?>"
             data-msg-start="<?= esc(lang('Games.t_start'), 'attr') ?>"
             data-msg-resume="<?= esc(lang('Games.t_resume'), 'attr') ?>">

            <div class="tetris__board">
                <canvas class="tetris__canvas" data-t-canvas width="300" height="480"
                        aria-label="<?= esc(lang('Games.t_title'), 'attr') ?>"></canvas>
                <div class="tetris__overlay" data-t-overlay>
                    <p class="tetris__overlay-text" data-t-overlay-text><?= esc(lang('Games.t_lead')) ?></p>
                    <button class="btn btn--primary btn--lg" type="button" data-t-start><?= esc(lang('Games.t_start')) ?></button>
                </div>
            </div>

            <aside class="tetris__side">
                <dl class="tetris__stats">
                    <div><dt><?= esc(lang('Games.t_score')) ?></dt><dd data-t-score>0</dd></div>
                    <div><dt><?= esc(lang('Games.t_best')) ?></dt><dd data-t-best>0</dd></div>
                    <div><dt><?= esc(lang('Games.t_level')) ?></dt><dd data-t-level>1</dd></div>
                    <div><dt><?= esc(lang('Games.t_lines')) ?></dt><dd data-t-lines>0</dd></div>
                </dl>

                <div class="tetris__actions">
                    <button class="btn btn--outline" type="button" data-t-pause hidden
                            data-label-pause="<?= esc(lang('Games.t_pause'), 'attr') ?>"
                            data-label-resume="<?= esc(lang('Games.t_resume'), 'attr') ?>"><?= esc(lang('Games.t_pause')) ?></button>
                    <button class="btn btn--outline" type="button" data-t-restart hidden><?= esc(lang('Games.t_restart')) ?></button>
                </div>

                <p class="tetris__hint"><?= esc(lang('Games.t_controls_hint')) ?></p>

                <div class="tetris__pad" aria-hidden="false">
                    <button class="tetris__pad-btn" type="button" data-t-btn="left" aria-label="<?= esc(lang('Games.t_left'), 'attr') ?>">◄</button>
                    <button class="tetris__pad-btn" type="button" data-t-btn="rotate" aria-label="<?= esc(lang('Games.t_rotate'), 'attr') ?>">⟳</button>
                    <button class="tetris__pad-btn" type="button" data-t-btn="right" aria-label="<?= esc(lang('Games.t_right'), 'attr') ?>">►</button>
                    <button class="tetris__pad-btn" type="button" data-t-btn="down" aria-label="<?= esc(lang('Games.t_down'), 'attr') ?>">▼</button>
                    <button class="tetris__pad-btn tetris__pad-btn--wide" type="button" data-t-btn="drop" aria-label="<?= esc(lang('Games.t_drop'), 'attr') ?>">⤓</button>
                </div>

                <a class="tetris__back" href="<?= esc(locale_url('games')) ?>">← <?= esc(lang('Games.back_to_games')) ?></a>
            </aside>
        </div>
    </div>
</section>

<?php $this->endSection() ?>

<?php $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/games.css') ?>?v=<?= is_file(FCPATH . 'assets/css/games.css') ? filemtime(FCPATH . 'assets/css/games.css') : '1' ?>">
<?php $this->endSection() ?>

<?php $this->section('scripts') ?>
<script src="<?= base_url('assets/js/tetris.js') ?>?v=<?= is_file(FCPATH . 'assets/js/tetris.js') ? filemtime(FCPATH . 'assets/js/tetris.js') : '1' ?>" defer></script>
<?php $this->endSection() ?>
