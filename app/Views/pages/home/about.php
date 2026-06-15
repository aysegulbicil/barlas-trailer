<?php

/**
 * Home / About (corporate intro)
 *
 * Two-column layout: an image column (with a floating experience-counter
 * badge and a blueprint placeholder until about.jpg is supplied) and a
 * text column with intro, checklist and signature. Decorative shapes add
 * industrial depth. All copy from language files.
 */
$points = ['about_point_1', 'about_point_2', 'about_point_3'];
?>
<section class="about" id="about">
    <span class="section-decor section-decor--blueprint" aria-hidden="true"></span>

    <div class="container about__inner">
        <div class="about__media" data-aos="fade-right">
            <div class="about__image media-frame" data-img="about.jpg" data-clip-reveal></div>
            <div class="about__badge" data-aos="zoom-in" data-aos-delay="200">
                <span class="about__badge-num" data-count="<?= esc((string) lang('Home.about_exp_value'), 'attr') ?>">0</span><span class="about__badge-plus">+</span>
                <span class="about__badge-label"><?= esc(lang('Home.about_exp_label')) ?></span>
            </div>
        </div>

        <div class="about__content" data-aos="fade-left">
            <h2 class="about__title"><?= esc(lang('Home.about_title')) ?></h2>
            <p class="about__lead"><?= esc(lang('Home.about_lead')) ?></p>
            <p class="about__text"><?= esc(lang('Home.about_text')) ?></p>

            <ul class="about__points">
                <?php foreach ($points as $key): ?>
                    <li class="about__point">
                        <span class="about__check" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
                        </span>
                        <?= esc(lang('Home.' . $key)) ?>
                    </li>
                <?php endforeach; ?>
            </ul>

            <div class="about__footer">
                <a class="btn btn--primary" href="<?= esc(locale_url('about')) ?>"><?= esc(lang('Home.about_cta')) ?></a>
                <span class="about__sign"><?= esc(lang('Home.about_sign_name')) ?></span>
            </div>
        </div>
    </div>
</section>
