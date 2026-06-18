/**
 * service-gallery.js — Servis detay sayfası görsel galerisi (slider)
 *
 * Bağımsız, kütüphanesiz vanilla JS. Sayfadaki her [data-gallery] öğesini
 * bağımsız bir slider olarak başlatır:
 *   - Önceki / sonraki okları
 *   - Alt noktalar (tıkla-git)
 *   - Sol/sağ klavye okları (galeri odaktayken)
 *   - Dokunmatik kaydırma (swipe)
 *   - Opsiyonel otomatik geçiş (data-autoplay="ms"), fareyle/odakla durur,
 *     sekme arka plandayken ve "hareketi azalt" tercihinde devre dışı.
 *
 * Tek slayt varsa hiçbir şey yapmaz (kontroller zaten gizli).
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

    function initGallery(root) {
        var track = root.querySelector('.service-gallery__track');
        var slides = root.querySelectorAll('.service-gallery__slide');
        if (!track || slides.length < 2) return;

        var dots = Array.prototype.slice.call(
            root.querySelectorAll('.service-gallery__dot')
        );
        var countEl = root.querySelector('.service-gallery__count-current');
        var prevBtn = root.querySelector('.service-gallery__nav--prev');
        var nextBtn = root.querySelector('.service-gallery__nav--next');

        var total = slides.length;
        var index = 0;
        var autoplay = parseInt(root.getAttribute('data-autoplay'), 10) || 0;
        var timer = null;

        function render() {
            track.style.transform = 'translateX(' + (-index * 100) + '%)';
            for (var d = 0; d < dots.length; d++) {
                var on = d === index;
                dots[d].classList.toggle('is-active', on);
                dots[d].setAttribute('aria-selected', on ? 'true' : 'false');
            }
            if (countEl) countEl.textContent = String(index + 1);
        }

        function go(i) {
            index = (i % total + total) % total;
            render();
        }
        function next() { go(index + 1); }
        function prev() { go(index - 1); }

        /* --- Otomatik geçiş --- */
        function start() {
            if (!autoplay || REDUCE) return;
            stop();
            timer = window.setInterval(next, autoplay);
        }
        function stop() {
            if (timer) { window.clearInterval(timer); timer = null; }
        }
        function restart() { stop(); start(); }

        /* --- Oklar / noktalar --- */
        if (prevBtn) prevBtn.addEventListener('click', function () { prev(); restart(); });
        if (nextBtn) nextBtn.addEventListener('click', function () { next(); restart(); });
        dots.forEach(function (dot, i) {
            dot.addEventListener('click', function () { go(i); restart(); });
        });

        /* --- Klavye (galeri içi odakta) --- */
        root.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowLeft') { prev(); restart(); }
            else if (e.key === 'ArrowRight') { next(); restart(); }
        });

        /* --- Dokunmatik kaydırma --- */
        var startX = null;
        root.addEventListener('touchstart', function (e) {
            startX = e.touches[0].clientX;
            stop();
        }, { passive: true });
        root.addEventListener('touchend', function (e) {
            if (startX === null) return;
            var dx = e.changedTouches[0].clientX - startX;
            if (Math.abs(dx) > 40) {
                if (dx < 0) next(); else prev();
            }
            startX = null;
            start();
        }, { passive: true });

        /* --- Otomatik geçişi duraklat --- */
        root.addEventListener('mouseenter', stop);
        root.addEventListener('mouseleave', start);
        root.addEventListener('focusin', stop);
        root.addEventListener('focusout', start);
        document.addEventListener('visibilitychange', function () {
            if (document.hidden) stop(); else start();
        });

        render();
        start();
    }

    ready(function () {
        var galleries = Array.prototype.slice.call(
            document.querySelectorAll('[data-gallery]')
        );
        galleries.forEach(initGallery);
    });
})();
