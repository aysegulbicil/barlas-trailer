/**
 * hero-static.js — tam ekran statik sinematik hero (hazır render görseli)
 * =============================================================================
 * 3D/WebGL YOK. Hero, referans kompozisyonunu içeren tek görseldir (araç + dikey
 * ışık dikişi + alt-orta içe-açılan neon çukur GÖMÜLÜ). Görsel iki özdeş yarıya
 * (CSS clip-path) bölünmüştür.
 *
 * Scroll: hero PIN'lenir; ilerlemeye göre iki yarı yana kayar → ortadaki çukur
 * açılır, açılan boşluktan arkadaki ".hs__reveal" katmanı görünür. Overlay söner.
 * Bu, eski 3D shader yolundaki "ikiye ayrılma" ile AYNI his — ama tamamen CSS
 * transform (bedava, mobil dahil her yerde çalışır).
 *
 * İdle'da HAREKET YOK (kullanıcı isteği): yarılar yalnızca scroll ilerlerken kayar.
 * reduced-motion / gsap yok: .hs--static → açılma yok, tam-ekran statik görsel.
 *
 * Bağımlılık: gsap + ScrollTrigger (layout'ta defer ile zaten yüklü). Yeni yok.
 */
(function () {
    'use strict';

    function prefersReduced() {
        try { return window.matchMedia('(prefers-reduced-motion: reduce)').matches; }
        catch (e) { return false; }
    }
    function clamp01(v) { return v < 0 ? 0 : (v > 1 ? 1 : v); }
    function smooth(a, b, x) { var t = clamp01((x - a) / (b - a)); return t * t * (3 - 2 * t); }
    function lerp(a, b, t) { return a + (b - a) * t; }

    /* Açılmada her yarının en fazla kayacağı miktar (kendi genişliğinin %'si).
       >50% → yarılar ekrandan TAMAMEN çıkar (açılan görsel tümüyle kaybolur,
       geriye yalnızca arkadaki .hs__reveal katmanı kalır). */
    var MAX_SHIFT = 56;

    function boot() {
        var section = document.querySelector('[data-hero-static]');
        if (!section) return;

        var left    = section.querySelector('[data-hs-left]');
        var right   = section.querySelector('[data-hs-right]');
        var overlay = section.querySelector('[data-hs-overlay]');
        var reveal  = section.querySelector('[data-hs-reveal]');
        var pinEl   = section.querySelector('[data-hs-pin]') || section;

        var gsap = window.gsap, ST = window.ScrollTrigger;

        /* Statik yol: hareket istenmiyor ya da gsap yoksa — tam-ekran görsel kalır,
           yarılar birleşik (açılma yok), metin okunur. */
        if (prefersReduced() || !gsap || !ST) {
            section.classList.add('hs--static');
            return;
        }

        /* p: 0..1 sinematik ilerleme → yarıları aç, overlay'i söndür, reveal'i getir. */
        function apply(p) {
            var s = smooth(0.06, 0.82, p);
            var shift = s * MAX_SHIFT;
            if (left)  left.style.transform  = 'translate3d(' + (-shift).toFixed(2) + '%,0,0)';
            if (right) right.style.transform = 'translate3d(' + ( shift).toFixed(2) + '%,0,0)';

            if (overlay) {
                var o = smooth(0.02, 0.26, p);
                overlay.style.opacity = (1 - o).toFixed(3);
                overlay.style.transform = 'translateY(' + (-o * 40).toFixed(1) + 'px)';
            }
            if (reveal) {
                var r = smooth(0.16, 0.64, p);
                reveal.style.opacity = r.toFixed(3);
                reveal.style.transform = 'scale(' + lerp(1.06, 1, r).toFixed(4) + ')';
            }
        }

        section.classList.add('hs--ready');
        apply(0);

        gsap.registerPlugin(ST);
        ST.create({
            trigger: section,
            start: 'top top',
            end: '+=150%',
            pin: pinEl,
            anticipatePin: 1,
            scrub: true,
            onUpdate: function (self) { apply(self.progress); },
            onEnter: function () { section.classList.add('hs--pinned'); },
            onLeaveBack: function () { section.classList.remove('hs--pinned'); }
        });

        /* Pin sayfaya boşluk ekler; alt tetikleyiciler yeniden hizalansın. */
        ST.refresh();
        window.addEventListener('load', function () { ST.refresh(); });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot, { once: true });
    } else {
        boot();
    }
})();
