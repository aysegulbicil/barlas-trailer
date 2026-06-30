/**
 * products.js — Ürünlerimiz sayfaları davranışları
 *
 * Bağımlılıksız, küçük modüller:
 *  - Sayaçlar  : [data-countup] görünüme girince 0'dan değere sayar.
 *  - Filtre    : kategori sayfasında canlı metin araması; kartlar
 *                data-prd-name üzerinden istemci tarafında süzülür
 *                (yeniden yükleme yok).
 *  - Sekmeler  : ürün detayında firma/model varyant panelleri.
 *  - Akordeon  : spec gruplarını açıp kapatır (grid-rows animasyonu
 *                CSS'te; burada yalnızca durum sınıfı yönetilir).
 *  - Spec nav  : hızlı erişim chip'i kapalı grubu açıp yumuşak kaydırır.
 *
 * prefers-reduced-motion: sayaçlar anında yazılır, kaydırma 'auto' olur.
 */
(function () {
    'use strict';

    var REDUCE = window.matchMedia &&
        window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function ready(fn) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fn, { once: true });
        } else {
            fn();
        }
    }

    // assets/ kökü: bu script'in kendi URL'sinden türetilir.
    var ASSETS_BASE = (function () {
        var s = document.currentScript;
        return s ? s.src.replace(/assets\/js\/products\.js.*$/, '') : '';
    })();

    /* --------------------------- Foto kancaları ---------------------- */
    /* Ürün görselleri (assets/images/products/*.jpg) için dosya varsa
       yer tutucunun üzerine boyanır; yoksa mavi ızgara deseni kalır.
       Kategori görselleri CSS kancalarıyla zaten çalışır. */
    function initMediaHooks() {
        if (!ASSETS_BASE) return;
        var nodes = Array.prototype.slice.call(
            document.querySelectorAll('[data-img^="products/"]')
        );
        nodes.forEach(function (el) {
            var orig = ASSETS_BASE + 'assets/images/' + el.getAttribute('data-img');
            // Optimizasyon: önce .webp dene (çok daha küçük); yoksa orijinale düş.
            var webp = orig.replace(/\.(jpe?g|png)$/i, '.webp');

            function paint(src) {
                el.style.backgroundImage = 'url("' + src + '")';
                el.style.backgroundSize = 'cover';
                el.style.backgroundPosition = 'center';
            }

            var probe = new Image();
            probe.onload = function () { paint(webp); };
            probe.onerror = function () {
                var f = new Image();
                f.onload = function () { paint(orig); };
                f.src = orig;
            };
            probe.src = webp;
        });
    }

    /* ------------------------------ Sayaçlar ------------------------- */
    function initCountUps() {
        var nodes = Array.prototype.slice.call(document.querySelectorAll('[data-countup]'));
        if (!nodes.length) return;

        var write = function (el, value) { el.textContent = String(value); };

        if (REDUCE || !('IntersectionObserver' in window)) {
            nodes.forEach(function (el) { write(el, el.getAttribute('data-countup')); });
            return;
        }

        var animate = function (el) {
            var target = parseInt(el.getAttribute('data-countup'), 10) || 0;
            var dur = 1100;
            var start = null;

            function frame(now) {
                if (start === null) start = now;
                var p = Math.min((now - start) / dur, 1);
                var eased = 1 - Math.pow(1 - p, 3); // easeOutCubic
                write(el, Math.round(target * eased));
                if (p < 1) requestAnimationFrame(frame);
            }
            requestAnimationFrame(frame);
        };

        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    io.unobserve(entry.target);
                    animate(entry.target);
                }
            });
        }, { threshold: 0.4 });

        nodes.forEach(function (el) { io.observe(el); });
    }

    /* ------------------------------ Filtre --------------------------- */
    function initFilter() {
        var grid = document.querySelector('[data-prd-grid]');
        if (!grid) return;

        var cards = Array.prototype.slice.call(grid.querySelectorAll('.prd-card'));
        var search = document.querySelector('[data-prd-search]');
        var empty = document.querySelector('[data-prd-empty]');

        var term = '';

        function apply() {
            var visible = 0;
            cards.forEach(function (card) {
                var name = card.getAttribute('data-prd-name') || '';
                var show = term === '' || name.indexOf(term) !== -1;
                card.classList.toggle('is-hidden', !show);
                if (show) visible++;
            });
            if (empty) empty.hidden = visible !== 0;
        }

        if (search) {
            search.addEventListener('input', function () {
                term = search.value.trim().toLocaleLowerCase('tr');
                apply();
            });
        }
    }

    /* ------------------------------ Sekmeler ------------------------- */
    function initTabs() {
        var tablist = document.querySelector('[data-prd-tabs]');
        if (!tablist) return;

        var tabs = Array.prototype.slice.call(tablist.querySelectorAll('[data-prd-tab]'));
        var panels = Array.prototype.slice.call(document.querySelectorAll('[data-prd-panel]'));

        function activate(index, focus) {
            tabs.forEach(function (tab) {
                var on = tab.getAttribute('data-prd-tab') === String(index);
                tab.classList.toggle('is-active', on);
                tab.setAttribute('aria-selected', on ? 'true' : 'false');
                tab.tabIndex = on ? 0 : -1;
                if (on && focus) tab.focus();
            });
            panels.forEach(function (panel) {
                var on = panel.getAttribute('data-prd-panel') === String(index);
                panel.hidden = !on;
                panel.classList.toggle('is-active', on);
            });
        }

        tabs.forEach(function (tab, i) {
            tab.addEventListener('click', function () { activate(i, false); });
            tab.addEventListener('keydown', function (e) {
                var dir = 0;
                if (e.key === 'ArrowRight') dir = 1;
                else if (e.key === 'ArrowLeft') dir = -1;
                else if (e.key === 'Home') { e.preventDefault(); activate(0, true); return; }
                else if (e.key === 'End') { e.preventDefault(); activate(tabs.length - 1, true); return; }
                if (dir !== 0) {
                    e.preventDefault();
                    activate((i + dir + tabs.length) % tabs.length, true);
                }
            });
        });
    }

    /* ------------------------------ Akordeon ------------------------- */
    function initAccordions() {
        var groups = Array.prototype.slice.call(document.querySelectorAll('[data-prd-acc]'));
        if (!groups.length) return;

        groups.forEach(function (group) {
            var toggle = group.querySelector('[data-prd-acc-toggle]');
            if (!toggle) return;

            toggle.addEventListener('click', function () {
                var open = group.classList.toggle('is-open');
                toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            });
        });

        // Hızlı erişim chip'leri: hedef grup kapalıysa açıp kaydır.
        Array.prototype.slice.call(document.querySelectorAll('[data-prd-spec-link]')).forEach(function (link) {
            link.addEventListener('click', function (e) {
                var id = (link.getAttribute('href') || '').slice(1);
                var target = id && document.getElementById(id);
                if (!target) return;
                e.preventDefault();

                if (!target.classList.contains('is-open')) {
                    target.classList.add('is-open');
                    var t = target.querySelector('[data-prd-acc-toggle]');
                    if (t) t.setAttribute('aria-expanded', 'true');
                }
                target.scrollIntoView({
                    behavior: REDUCE ? 'auto' : 'smooth',
                    block: 'start'
                });
            });
        });
    }

    ready(function () {
        initMediaHooks();
        initCountUps();
        initFilter();
        initTabs();
        initAccordions();
    });
})();
