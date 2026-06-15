<?php

/**
 * Home / Marquee trust strip
 *
 * Infinite horizontal ticker of trust signals (certifications, export
 * reach, heritage). Pure CSS scroll; the track is duplicated so the loop
 * is seamless. Pauses on hover. Items come from the Home.marquee array.
 */
$items = lang('Home.marquee');
$items = is_array($items) ? $items : [];
?>
<section class="marquee" aria-label="<?= esc(lang('Home.references_eyebrow'), 'attr') ?>">
    <div class="marquee__track" data-marquee>
        <?php for ($pass = 0; $pass < 2; $pass++): ?>
            <ul class="marquee__group" <?= $pass === 1 ? 'aria-hidden="true"' : '' ?>>
                <?php foreach ($items as $item): ?>
                    <li class="marquee__item">
                        <span class="marquee__star" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 3l2.5 5.5L20 11l-5.5 2.5L12 19l-2.5-5.5L4 11l5.5-2.5z"></path>
                            </svg>
                        </span>
                        <span class="marquee__text"><?= esc($item) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endfor; ?>
    </div>
</section>
