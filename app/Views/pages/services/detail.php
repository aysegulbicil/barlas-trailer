<?php

/**
 * Service detail page
 *
 * Inner-page hero (breadcrumb + service name) followed by a split intro
 * (cover image area + general copy and a "what we do" point list), a CTA
 * band and a small "related services" rail. Deliberately concise: a
 * general description of the service, not a long-form article.
 */
$this->extend('layouts/inner');
?>

<?php $this->section('bodyClass') ?>service-detail<?php $this->endSection() ?>

<?php $this->section('content') ?>

<?= view('partials/page-hero', [
    'pageTitle'   => $service['name'],
    'pageLead'    => $service['lead'],
    'breadcrumbs' => [
        ['label' => lang('Navigation.services'), 'url' => locale_url('services')],
        ['label' => $service['name']],
    ],
]) ?>

<article class="service-detail">
    <div class="container service-detail__intro">

        <!-- Cover image area (placeholder until the real photo is added) -->
        <div class="service-detail__media media-frame media-frame--tall"
             data-img="services/<?= esc($service['image'], 'attr') ?>" data-reveal>
            <span class="media-frame__placeholder" aria-hidden="true">
                <svg viewBox="0 0 80 80" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 58h52M18 58V34l14-10 14 10v24M50 58V30h12v28"></path>
                    <path d="M25 58v-9h8v9"></path>
                </svg>
            </span>
        </div>

        <div class="service-detail__copy" data-reveal>
            <div class="service-detail__body">
                <?= $service['body'] ?>
            </div>

            <?php if ($service['points'] !== []): ?>
                <div class="service-points">
                    <h2 class="service-points__title"><?= esc(lang('Services.whatwedo_title')) ?></h2>
                    <ul class="service-points__list">
                        <?php foreach ($service['points'] as $point): ?>
                            <li>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M20 6L9 17l-5-5"></path>
                                </svg>
                                <?= esc($point) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="service-detail__actions">
                <a class="btn btn--primary" href="<?= esc(locale_url('contact')) ?>">
                    <?= esc(lang('Navigation.get_quote')) ?>
                </a>
                <a class="btn btn--outline" href="tel:<?= esc(lang('Common.phone_raw'), 'attr') ?>">
                    <?= esc(lang('Services.call_button')) ?>
                </a>
                <a class="service-detail__back" href="<?= esc(locale_url('services')) ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M19 12H5M11 18l-6-6 6-6"></path>
                    </svg>
                    <?= esc(lang('Services.back_to_services')) ?>
                </a>
            </div>
        </div>
    </div>
</article>

<?php if ($related !== []): ?>
    <section class="services-related">
        <div class="container">
            <div class="section-head" data-reveal>
                <span class="section-head__eyebrow"><?= esc(lang('Services.detail_eyebrow')) ?></span>
                <h2 class="section-head__title"><?= esc(lang('Services.related_title')) ?></h2>
            </div>
            <div class="services-grid" data-reveal-group>
                <?php foreach ($related as $service): ?>
                    <?= view('partials/service-card', ['service' => $service]) ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Service",
    "name": <?= json_encode($service['name'], JSON_UNESCAPED_UNICODE) ?>,
    "description": <?= json_encode($service['lead'] !== '' ? $service['lead'] : $service['desc'], JSON_UNESCAPED_UNICODE) ?>,
    "areaServed": "TR",
    "provider": {
        "@type": "Organization",
        "name": <?= json_encode(lang('Common.site_name'), JSON_UNESCAPED_UNICODE) ?>
    }
}
</script>

<?php $this->endSection() ?>
