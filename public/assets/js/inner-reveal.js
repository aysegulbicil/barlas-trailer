/**
 * inner-reveal.js — İç sayfalar için hafif "scroll'da beliriş" animasyonu
 *
 * Ana sayfanın ağır hareket katmanı (gsap/three) yerine küçük bir
 * IntersectionObserver. [data-reveal] ve [data-reveal-group] > * öğeleri
 * görünüme girince .is-in alır; başlangıç gizli durumları yeni.css'teki
 * "html.has-anim [data-reveal]" kurallarıyla gelir.
 *
 * No-JS / reduced-motion / IO yoksa: has-anim eklenmez, içerik baştan
 * görünür kalır (güvenli düşüş).
 */
(function () {
    'use strict';

    var root = document.documentElement;

    function ready(fn) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fn, { once: true });
        } else {
            fn();
        }
    }

    ready(function () {
        var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (reduce || !('IntersectionObserver' in window)) return;

        var singles = Array.prototype.slice.call(document.querySelectorAll('[data-reveal]'));
        var groups  = Array.prototype.slice.call(document.querySelectorAll('[data-reveal-group]'));
        var groupKids = [];
        groups.forEach(function (g) {
            Array.prototype.slice.call(g.children).forEach(function (child, i) {
                // Hafif kademeli giriş (ana sayfayla aynı ritim: 60ms adım)
                child.style.transitionDelay = Math.min(i * 60, 360) + 'ms';
                groupKids.push(child);
            });
        });

        var targets = singles.concat(groupKids);
        if (targets.length === 0) return;

        // Gizli başlangıç durumlarını etkinleştir.
        root.classList.add('has-anim');

        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-in');
                    io.unobserve(entry.target);
                }
            });
        }, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });

        targets.forEach(function (el) { io.observe(el); });
    });
})();
