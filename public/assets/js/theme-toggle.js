/**
 * theme-toggle.js — Aydınlık / koyu tema değiştirici (site geneli)
 *
 * Tema kaynağı önceliği:
 *   1) Kullanıcının açık seçimi (localStorage: 'barlas-theme' = 'light' | 'dark')
 *   2) Yoksa işletim sistemi tercihi (prefers-color-scheme)
 *
 * İlk boya öncesi tema, layout <head> içindeki satır içi betikle
 * <html data-theme="..."> olarak ayarlanır (FOUC yok). Bu dosya yalnızca
 * header'daki düğmeyi bağlar, seçimi saklar ve sistem değişimini dinler.
 */
(function () {
    'use strict';

    var STORAGE_KEY = 'barlas-theme';
    var root = document.documentElement;

    function stored() {
        try {
            var v = localStorage.getItem(STORAGE_KEY);
            return v === 'light' || v === 'dark' ? v : null;
        } catch (e) {
            return null;
        }
    }

    function systemTheme() {
        return window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches
            ? 'light'
            : 'dark';
    }

    function currentTheme() {
        return root.getAttribute('data-theme') === 'light' ? 'light' : 'dark';
    }

    function apply(theme) {
        root.setAttribute('data-theme', theme);
        syncButtons(theme);
    }

    function syncButtons(theme) {
        var buttons = document.querySelectorAll('[data-theme-toggle]');
        for (var i = 0; i < buttons.length; i++) {
            // aria-pressed: koyu temada düğme "aydınlığa geçir" eylemini temsil eder
            buttons[i].setAttribute('aria-pressed', theme === 'light' ? 'true' : 'false');
        }
    }

    function persist(theme) {
        try {
            localStorage.setItem(STORAGE_KEY, theme);
        } catch (e) {
            /* özel mod / kota: sessizce yoksay */
        }
    }

    function toggle() {
        var next = currentTheme() === 'light' ? 'dark' : 'light';
        apply(next);
        persist(next);
    }

    function init() {
        syncButtons(currentTheme());

        var buttons = document.querySelectorAll('[data-theme-toggle]');
        for (var i = 0; i < buttons.length; i++) {
            buttons[i].addEventListener('click', toggle);
        }

        // Kullanıcı açık seçim yapmadıysa, sistem temasını canlı takip et.
        if (window.matchMedia) {
            var mq = window.matchMedia('(prefers-color-scheme: light)');
            var onChange = function () {
                if (!stored()) apply(systemTheme());
            };
            if (mq.addEventListener) {
                mq.addEventListener('change', onChange);
            } else if (mq.addListener) {
                mq.addListener(onChange);
            }
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init, { once: true });
    } else {
        init();
    }
})();
