<?php

/**
 * Bekleme Salonu hub'ı (§8) — kahve molası ekranı: oyun kartları.
 * Skorlar yalnız ziyaretçinin cihazında tutulur (local_note).
 */
$this->extend('layouts/inner');

$cards = [
    [
        'url'   => locale_url('games/tetris'),
        'title' => lang('Games.hub_tetris_title'),
        'desc'  => lang('Games.hub_tetris_desc'),
        'icon'  => 'M4 7h6v6H4z M10 13h6v6h-6z M14 3h6v6h-6z',
    ],
    [
        'url'   => locale_url('games/daily'),
        'title' => lang('Games.hub_daily_title'),
        'desc'  => lang('Games.hub_daily_desc'),
        'icon'  => 'M12 3a9 9 0 100 18 9 9 0 000-18z M9.5 9a2.5 2.5 0 115 .5c0 1.5-2.5 2-2.5 3.5 M12 17h.01',
    ],
    [
        'url'   => locale_url('quiz'),
        'title' => lang('Games.hub_quiz_title'),
        'desc'  => lang('Games.hub_quiz_desc'),
        'icon'  => 'M3 17V7l5-3 5 3v10 M13 17h8v-6l-3-2h-5 M6.5 20a1.8 1.8 0 100-3.6 1.8 1.8 0 000 3.6z M17.5 20a1.8 1.8 0 100-3.6 1.8 1.8 0 000 3.6z',
    ],
];
?>

<?php $this->section('bodyClass') ?>games<?php $this->endSection() ?>

<?php $this->section('content') ?>

<?= view('partials/page-hero', [
    'pageTitle'   => lang('Games.page_title'),
    'pageLead'    => lang('Games.page_lead'),
    'breadcrumbs' => [
        ['label' => lang('Games.breadcrumb')],
    ],
]) ?>

<section class="games-section">
    <div class="container">
        <div class="games-grid">
            <?php foreach ($cards as $card): ?>
                <a class="game-card" href="<?= esc($card['url']) ?>">
                    <span class="game-card__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="<?= esc($card['icon'], 'attr') ?>"></path>
                        </svg>
                    </span>
                    <span class="game-card__title"><?= esc($card['title']) ?></span>
                    <span class="game-card__desc"><?= esc($card['desc']) ?></span>
                    <span class="game-card__play"><?= esc(lang('Games.play')) ?> →</span>
                </a>
            <?php endforeach; ?>
        </div>

        <p class="games-note"><?= esc(lang('Games.local_note')) ?></p>
    </div>
</section>

<?php $this->endSection() ?>

<?php $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/games.css') ?>?v=<?= is_file(FCPATH . 'assets/css/games.css') ? filemtime(FCPATH . 'assets/css/games.css') : '1' ?>">
<?php $this->endSection() ?>
