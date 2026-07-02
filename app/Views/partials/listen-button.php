<?php

/**
 * "Bu yazıyı dinle" düğmesi (§7.7 sesli blog) — Web Speech API ile
 * istemci tarafında TTS; sunucu/API maliyeti yok. Kullanım:
 *   <?= view('partials/listen-button', ['target' => '.blog-article__content']) ?>
 * $target: okunacak makale gövdesinin CSS seçicisi. Tarayıcı
 * speechSynthesis desteklemiyorsa düğme gizli kalır (article-tts.js açar).
 */
$target = $target ?? '.wiki-article__body';
?>
<button type="button" class="listen-btn" hidden data-listen-btn
        data-listen-target="<?= esc($target, 'attr') ?>"
        data-label-start="<?= esc(lang('Navigation.listen_start'), 'attr') ?>"
        data-label-stop="<?= esc(lang('Navigation.listen_stop'), 'attr') ?>"
        aria-pressed="false">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M11 5 6 9H3v6h3l5 4V5z"></path>
        <path d="M15.5 8.5a5 5 0 0 1 0 7"></path>
        <path d="M18.5 5.5a9 9 0 0 1 0 13"></path>
    </svg>
    <span data-listen-label><?= esc(lang('Navigation.listen_start')) ?></span>
</button>

<style>
.listen-btn{
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    min-height: 48px;
    padding: 0.55rem 1.1rem;
    margin-block-end: 1.2rem;
    border: 1px solid var(--line-2, rgba(127, 152, 190, 0.35));
    border-radius: 999px;
    background: var(--bg-2, #101A2E);
    color: var(--ink-1, #F0F5FC);
    font: inherit;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transition: border-color 0.2s ease, color 0.2s ease;
}
.listen-btn svg{ width: 20px; height: 20px; }
.listen-btn:hover,
.listen-btn:focus-visible{ border-color: var(--blue-bright, #4AA3E6); color: var(--blue-bright, #4AA3E6); }
.listen-btn:focus-visible{ outline: 2px solid var(--blue-bright, #4AA3E6); outline-offset: 2px; }
.listen-btn.is-on{
    border-color: var(--blue-bright, #4AA3E6);
    color: var(--blue-bright, #4AA3E6);
    box-shadow: 0 0 0 3px rgba(74, 163, 230, 0.2);
}
</style>
<script src="<?= base_url('assets/js/article-tts.js') ?>?v=<?= is_file(FCPATH . 'assets/js/article-tts.js') ? filemtime(FCPATH . 'assets/js/article-tts.js') : '1' ?>" defer></script>
