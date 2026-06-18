<?php

/**
 * Quote — ürün seçtirme (picker) sayfası
 *
 * Ürünü belli olmayan "Teklif Al" butonları (header, ana sayfa CTA, ürün/hizmet
 * liste CTA'ları) buraya gelir. Kullanıcı bir ürün seçer; her ürün doğrudan
 * Quote::start rotasına bağlıdır ve oradan WhatsApp'a yönlendirilir.
 *
 * Hafif bir istemci-tarafı filtre (ad + eş anlamlılar üzerinde) uzun listeyi
 * aranabilir kılar. Yeni CSS/JS dosyası eklemeden, sayfaya özel stil ve script
 * bölümleriyle tamamen kendi içinde çalışır.
 */

$this->extend('layouts/inner');

/** Yerelleştirilmiş kategori adı (katalog adına düşer). */
$catName = static function (array $category): string {
    $key   = 'Navigation.cat_' . str_replace('-', '_', $category['slug']);
    $label = lang($key);
    return $label === $key ? $category['name'] : $label;
};
?>

<?php $this->section('bodyClass') ?>quote<?php $this->endSection() ?>

<?php $this->section('styles') ?>
<style>
    .quote-pick { padding: var(--space-7) 0 var(--space-9); }

    .quote-pick__bar {
        display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between;
        gap: var(--space-4); margin-bottom: var(--space-7);
    }
    .quote-pick__note {
        display: inline-flex; align-items: center; gap: var(--space-3);
        margin: 0; color: var(--text-muted); font-size: var(--fs-300); max-width: 60ch;
    }
    .quote-pick__note svg { flex: none; width: 22px; height: 22px; color: #25D366; }

    .quote-pick__search {
        flex: 1 1 240px; max-width: 360px;
        padding: .7rem 1rem; border: 1px solid var(--border-color);
        border-radius: var(--radius-pill); background: var(--bg-surface);
        color: var(--text-base); font: inherit; font-size: var(--fs-300);
        transition: border-color var(--dur-base) var(--ease-out), box-shadow var(--dur-base) var(--ease-out);
    }
    .quote-pick__search:focus { outline: none; border-color: var(--primary); box-shadow: var(--shadow-ring); }

    .quote-cat { margin-bottom: var(--space-7); }
    .quote-cat__title {
        font-family: var(--font-heading); font-size: var(--fs-500); font-weight: var(--fw-semibold);
        margin: 0 0 var(--space-4); padding-bottom: var(--space-2);
        border-bottom: 1px solid var(--border-color);
    }
    .quote-cat__grid {
        display: grid; gap: var(--space-3);
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    }

    .quote-item {
        display: flex; align-items: center; justify-content: space-between; gap: var(--space-3);
        padding: var(--space-3) var(--space-4);
        border: 1px solid var(--border-color); border-radius: var(--radius-md);
        background: var(--bg-surface); color: var(--text-base); text-decoration: none;
        transition: border-color var(--dur-base) var(--ease-out),
                    transform var(--dur-base) var(--ease-out),
                    box-shadow var(--dur-base) var(--ease-out);
    }
    .quote-item:hover {
        border-color: var(--primary); transform: translateY(-2px); box-shadow: var(--shadow-md);
    }
    .quote-item__name { font-weight: var(--fw-medium); line-height: var(--lh-snug); }
    .quote-item__cta {
        flex: none; display: inline-flex; align-items: center; gap: 6px;
        color: var(--primary); font-size: var(--fs-300); font-weight: var(--fw-semibold); white-space: nowrap;
    }
    .quote-item__cta svg { width: 16px; height: 16px; }

    .quote-pick__empty { color: var(--text-muted); text-align: center; padding: var(--space-6) 0; }
    .quote-pick__alt { margin-top: var(--space-7); color: var(--text-muted); font-size: var(--fs-300); }
    .quote-pick__alt a { color: var(--primary); }

    [dir="rtl"] .quote-item__cta svg { transform: scaleX(-1); }
</style>
<?php $this->endSection() ?>

<?php $this->section('content') ?>

<?= view('partials/page-hero', [
    'pageTitle'   => lang('Quote.page_title'),
    'pageLead'    => lang('Quote.page_lead'),
    'breadcrumbs' => [
        ['label' => lang('Quote.page_title')],
    ],
]) ?>

<section class="quote-pick">
    <div class="container">

        <div class="quote-pick__bar">
            <p class="quote-pick__note">
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M19.05 4.91A9.82 9.82 0 0 0 12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38a9.9 9.9 0 0 0 4.79 1.22h.004c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.02zm-7.01 15.24h-.003a8.23 8.23 0 0 1-4.19-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.2 8.2 0 0 1-1.26-4.38c0-4.54 3.7-8.23 8.24-8.23a8.2 8.2 0 0 1 5.82 2.41 8.18 8.18 0 0 1 2.41 5.83c0 4.54-3.7 8.23-8.24 8.23zm4.52-6.16c-.25-.12-1.47-.72-1.69-.81-.23-.08-.39-.12-.56.12-.16.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.12-1.05-.39-1.99-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.01-.38.11-.5.11-.11.25-.29.37-.43.13-.14.17-.25.25-.41.08-.17.04-.31-.02-.43-.06-.12-.56-1.34-.76-1.84-.2-.48-.41-.42-.56-.43l-.48-.01c-.17 0-.43.06-.66.31-.22.25-.86.85-.86 2.07 0 1.22.89 2.4 1.01 2.56.12.17 1.75 2.67 4.23 3.74.59.26 1.05.41 1.41.52.59.19 1.13.16 1.56.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.14-1.18-.06-.1-.22-.16-.47-.28z"></path>
                </svg>
                <?= esc(lang('Quote.wa_note')) ?>
            </p>
            <input type="search" class="quote-pick__search" data-quote-search
                   placeholder="<?= esc(lang('Quote.search_placeholder'), 'attr') ?>"
                   aria-label="<?= esc(lang('Quote.search_placeholder'), 'attr') ?>">
        </div>

        <?php foreach ($categories as $category): ?>
            <div class="quote-cat" data-quote-cat>
                <h2 class="quote-cat__title"><?= esc($catName($category)) ?></h2>
                <div class="quote-cat__grid">
                    <?php foreach ($category['products'] as $product): ?>
                        <?php
                        $search = mb_strtolower($product['name'] . ' ' . implode(' ', $product['aliases'] ?? []));
                        $href   = locale_url('teklif/' . $category['slug'] . '/' . $product['slug']);
                        ?>
                        <a class="quote-item" data-quote-item data-name="<?= esc($search, 'attr') ?>" href="<?= esc($href) ?>">
                            <span class="quote-item__name"><?= esc($product['name']) ?></span>
                            <span class="quote-item__cta">
                                <?= esc(lang('Navigation.get_quote')) ?>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M5 12h14 M13 6l6 6-6 6"></path>
                                </svg>
                            </span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <p class="quote-pick__empty" data-quote-empty hidden><?= esc(lang('Quote.no_results')) ?></p>

        <p class="quote-pick__alt">
            <?= esc(lang('Quote.form_alt_text')) ?>
            <a href="<?= esc(locale_url('contact')) ?>"><?= esc(lang('Quote.form_alt_link')) ?></a>
        </p>

    </div>
</section>

<?php $this->endSection() ?>

<?php $this->section('scripts') ?>
<script>
    (function () {
        var input = document.querySelector('[data-quote-search]');
        if (!input) { return; }

        var items = Array.prototype.slice.call(document.querySelectorAll('[data-quote-item]'));
        var cats  = Array.prototype.slice.call(document.querySelectorAll('[data-quote-cat]'));
        var empty = document.querySelector('[data-quote-empty]');

        function apply() {
            var q = input.value.trim().toLowerCase();
            var anyVisible = false;

            items.forEach(function (el) {
                var match = el.getAttribute('data-name').indexOf(q) !== -1;
                el.hidden = q !== '' && !match;
                if (!el.hidden) { anyVisible = true; }
            });

            cats.forEach(function (cat) {
                cat.hidden = cat.querySelectorAll('[data-quote-item]:not([hidden])').length === 0;
            });

            if (empty) { empty.hidden = anyVisible; }
        }

        input.addEventListener('input', apply);
    })();
</script>
<?php $this->endSection() ?>
