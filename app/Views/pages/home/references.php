<?php

/**
 * Home / References (brand partners) — static logo wall
 *
 * Was an auto-scrolling marquee; the page already has its one marquee
 * (the trust strip), so partners now sit in a calm hairline grid.
 * Real partner logos drop into assets/images/partners/ (see README);
 * until then neutral placeholder marks keep the wall intentional.
 * Logos only — no category labels (skill rule).
 */
$placeholderCount = 8;
?>
<section class="references" id="references">
    <div class="container">
        <div class="section-head" data-aos="fade-up">
            <h2 class="section-head__title"><?= esc(lang('Home.references_title')) ?></h2>
        </div>

        <ul class="references__wall" data-flow-stagger>
            <?php for ($n = 1; $n <= $placeholderCount; $n++): ?>
                <li class="references__logo" data-logo="partner-<?= esc((string) $n, 'attr') ?>">
                    <svg viewBox="0 0 120 40" fill="none" aria-hidden="true">
                        <rect x="2" y="10" width="20" height="20" rx="4" fill="currentColor" opacity="0.6"></rect>
                        <rect x="30" y="14" width="60" height="6" rx="3" fill="currentColor" opacity="0.45"></rect>
                        <rect x="30" y="24" width="40" height="5" rx="2.5" fill="currentColor" opacity="0.3"></rect>
                    </svg>
                </li>
            <?php endfor; ?>
        </ul>
    </div>
</section>
