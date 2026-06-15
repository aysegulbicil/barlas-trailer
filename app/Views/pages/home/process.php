<?php

/**
 * Home / Production process timeline
 *
 * Five-step horizontal (vertical on mobile) timeline. A connecting line is
 * drawn progressively as the section scrolls into view (process-timeline.js),
 * and each step reveals in sequence. Step data come from Home.process_steps.
 */
$steps = lang('Home.process_steps');
$steps = is_array($steps) ? $steps : [];
?>
<section class="process" id="process">
    <div class="container">
        <div class="section-head" data-aos="fade-up">
            <span class="section-head__eyebrow"><?= esc(lang('Home.process_eyebrow')) ?></span>
            <h2 class="section-head__title"><?= esc(lang('Home.process_title')) ?></h2>
            <p class="section-head__lead"><?= esc(lang('Home.process_lead')) ?></p>
        </div>

        <ol class="process__track" data-process>
            <span class="process__line" aria-hidden="true"><span class="process__line-fill" data-process-fill></span></span>

            <?php foreach ($steps as $i => $step): ?>
                <li class="process-step" data-process-step data-step-delay="<?= esc((string) ($i * 140), 'attr') ?>">
                    <span class="process-step__dot" aria-hidden="true">
                        <span class="process-step__num"><?= esc(str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT)) ?></span>
                    </span>
                    <div class="process-step__body">
                        <h3 class="process-step__title"><?= esc($step['title'] ?? '') ?></h3>
                        <p class="process-step__desc"><?= esc($step['desc'] ?? '') ?></p>
                    </div>
                </li>
            <?php endforeach; ?>
        </ol>
    </div>
</section>
