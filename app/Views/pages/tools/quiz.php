<?php

/**
 * "Which trailer suits me?" quiz page.
 *
 * The controller passes a fully localized decision tree ($payload);
 * public/assets/js/quiz.js renders it as a step-by-step wizard. Results
 * link to the real category pages and the quote flow.
 */
$this->extend('layouts/inner');
?>

<?php $this->section('bodyClass') ?>quiz<?php $this->endSection() ?>

<?php $this->section('content') ?>

<?= view('partials/page-hero', [
    'pageTitle'   => lang('Quiz.page_title'),
    'pageLead'    => lang('Quiz.page_lead'),
    'breadcrumbs' => [
        ['label' => lang('Quiz.breadcrumb')],
    ],
]) ?>

<section class="quiz-section">
    <div class="container">
        <div class="quiz" data-quiz
             data-progress-template="<?= esc(lang('Quiz.progress'), 'attr') ?>"
             data-share-copied="<?= esc(lang('Quiz.share_copied'), 'attr') ?>">

            <script type="application/json" data-quiz-tree><?= json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>

            <div class="quiz__step" data-quiz-step>
                <p class="quiz__progress" data-quiz-progress aria-live="polite"></p>
                <h2 class="quiz__question" data-quiz-question></h2>
                <div class="quiz__options" data-quiz-options role="group"></div>
            </div>

            <div class="quiz__result" data-quiz-result hidden>
                <p class="quiz__result-eyebrow"><?= esc(lang('Quiz.result_title')) ?></p>
                <h2 class="quiz__result-name" data-quiz-cat-name></h2>
                <p class="quiz__reason" data-quiz-reason></p>

                <div class="quiz__actions">
                    <a class="btn btn--primary" data-quiz-cat-link href="#"><?= esc(lang('Quiz.view_category')) ?></a>
                    <a class="btn btn--outline" href="<?= esc(locale_url('teklif')) ?>"><?= esc(lang('Quiz.get_quote')) ?></a>
                </div>

                <p class="quiz__alt" data-quiz-alt hidden>
                    <?= esc(lang('Quiz.result_alt')) ?>
                    <a data-quiz-alt-link href="#"></a>
                </p>

                <button type="button" class="quiz__share" data-quiz-share><?= esc(lang('Quiz.share')) ?></button>
            </div>

            <div class="quiz__nav">
                <button type="button" class="btn btn--outline" data-quiz-back hidden><?= esc(lang('Quiz.back')) ?></button>
                <button type="button" class="btn btn--outline" data-quiz-restart hidden><?= esc(lang('Quiz.restart')) ?></button>
            </div>
        </div>

        <p class="quiz__tools-link">
            <a href="<?= esc(locale_url('tools')) ?>"><?= esc(lang('Tools.page_title')) ?> →</a>
        </p>
    </div>
</section>

<?php $this->endSection() ?>

<?php $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/tools.css') ?>?v=<?= is_file(FCPATH . 'assets/css/tools.css') ? filemtime(FCPATH . 'assets/css/tools.css') : '1' ?>">
<?php $this->endSection() ?>

<?php $this->section('scripts') ?>
<script src="<?= base_url('assets/js/quiz.js') ?>?v=<?= is_file(FCPATH . 'assets/js/quiz.js') ? filemtime(FCPATH . 'assets/js/quiz.js') : '1' ?>" defer></script>
<?php $this->endSection() ?>
