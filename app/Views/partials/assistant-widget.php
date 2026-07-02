<?php

/**
 * Site geneli asistan widget'ı — "Dijital Satış Şefi" kaptan köşkü.
 *
 * Her sayfada TEK yüzen eylem (tasarım anayasası): asistan düğmesi.
 * Panel; sohbet (ana sayfa konsoluyla aynı POST {locale}/ai/ask ucunu
 * kullanır) + telefon ve WhatsApp hızlı eylemlerini içerir. Eski iki
 * FAB'ın (telefon + WhatsApp) yerini alır; .fab-stack/.wa-fab konum ve
 * görünüm stilleri footer'daki mevcut bloktan yeniden kullanılır.
 */
$waHref   = 'https://wa.me/905525002000?text=' . rawurlencode(lang('Common.whatsapp_message'));
$callHref = 'tel:+905525002000';
$awCss    = 'assets/css/assistant-widget.css';
$awJs     = 'assets/js/assistant-widget.js';
?>
<link rel="stylesheet" href="<?= base_url($awCss) ?>?v=<?= is_file(FCPATH . $awCss) ? filemtime(FCPATH . $awCss) : '1' ?>">

<div class="fab-stack aw" data-aw
     data-endpoint="<?= esc(locale_url('ai/ask'), 'attr') ?>"
     data-error="<?= esc(lang('Home.ai_error'), 'attr') ?>">

    <section class="aw__panel" data-aw-panel hidden
             aria-label="<?= esc(lang('Common.assistant_title'), 'attr') ?>">
        <header class="aw__head">
            <span class="aw__title"><?= esc(lang('Common.assistant_title')) ?></span>
            <button class="aw__close" type="button" data-aw-close
                    aria-label="<?= esc(lang('Common.assistant_close'), 'attr') ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"></path></svg>
            </button>
        </header>

        <div class="aw__quick">
            <a class="aw__quick-btn" href="<?= esc($callHref, 'attr') ?>">
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M6.62 10.79c1.44 2.83 3.76 5.15 6.59 6.59l2.2-2.2c.28-.28.67-.36 1.02-.25 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
                <?= esc(lang('Common.footer_call')) ?>
            </a>
            <a class="aw__quick-btn aw__quick-btn--wa" href="<?= esc($waHref, 'attr') ?>" target="_blank" rel="noopener">
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
                <?= esc(lang('Common.footer_whatsapp')) ?>
            </a>
        </div>

        <div class="aw__messages" data-aw-messages aria-live="polite">
            <p class="aw__msg aw__msg--bot"><?= esc(lang('Home.ai_welcome')) ?></p>
        </div>

        <form class="aw__form" data-aw-form>
            <input class="aw__hp" type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true">
            <label class="sr-only" for="aw-input"><?= esc(lang('Home.ai_input_label')) ?></label>
            <input id="aw-input" class="aw__input" type="text" autocomplete="off" data-aw-input
                   placeholder="<?= esc(lang('Home.ai_placeholder'), 'attr') ?>">
            <button class="aw__send" type="submit" aria-label="<?= esc(lang('Home.ai_send'), 'attr') ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 2 11 13"></path><path d="m22 2-7 20-4-9-9-4z"></path></svg>
            </button>
        </form>
    </section>

    <button class="wa-fab wa-fab--call aw__fab" type="button" data-aw-toggle aria-expanded="false"
            aria-label="<?= esc(lang('Common.assistant_fab'), 'attr') ?>">
        <span class="wa-fab__pulse" aria-hidden="true"></span>
        <span class="wa-fab__pulse wa-fab__pulse--2" aria-hidden="true"></span>
        <span class="wa-fab__btn">
            <svg class="wa-fab__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"
                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"></path>
                <path d="M8.5 11.5h.01M12 11.5h.01M15.5 11.5h.01"></path>
            </svg>
        </span>
        <span class="wa-fab__badge" aria-hidden="true"></span>
        <span class="wa-fab__label"><?= esc(lang('Common.assistant_fab')) ?></span>
    </button>
</div>

<script src="<?= base_url($awJs) ?>?v=<?= is_file(FCPATH . $awJs) ? filemtime(FCPATH . $awJs) : '1' ?>" defer></script>
