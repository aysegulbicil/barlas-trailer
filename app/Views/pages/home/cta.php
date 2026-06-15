<?php

/**
 * Home / CTA section
 *
 * Final conversion band inviting the visitor to request a quote. Uses the
 * dark/accent palette for emphasis. All copy from language files.
 */
?>
<section class="cta-band cta-band--finale" id="cta">
    <div class="container cta-band__inner" data-cta-inner>
        <div class="cta-band__text">
            <h2 class="cta-band__title"><?= esc(lang('Home.cta_title')) ?></h2>
            <p class="cta-band__lead"><?= esc(lang('Home.cta_lead')) ?></p>
        </div>
        <div class="cta-band__actions">
            <a class="btn btn--light btn--lg" href="<?= esc(locale_url('contact')) ?>">
                <?= esc(lang('Home.cta_button')) ?>
            </a>
            <a class="btn btn--ghost btn--lg" href="tel:<?= esc(lang('Common.phone_raw'), 'attr') ?>">
                <?= esc(lang('Common.phone')) ?>
            </a>
        </div>
    </div>
</section>
<?php // End of home sections ?>