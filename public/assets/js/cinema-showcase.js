/**
 * cinema-showcase.js — "Rakamlarla Barlas" veri vitrini davranışı (v3.0)
 * =============================================================================
 * İki lazy efekt (BarlasCinema.lazy → görünüme yaklaşınca kurulur, fold altı
 * bölüm için bedava başlangıç):
 *
 *   1) İhracat ışık haritası — full: yaylar sırayla çizilir (stroke-dashoffset
 *      1→0, ~90ms arayla ~700ms; geçişler CSS'te, sıra --arc-i değişkeninde),
 *      yay ucu varınca düğüm "yanar" (.is-lit → nabız + kod çipi). lite: dash
 *      oyunu yok, hepsi birlikte yumuşak belirir. off: JS HİÇBİR ŞEY gizlemez
 *      (CSS varsayılanı zaten tam çizili).
 *
 *   2) Odometre — görünüme girince haneler 0'dan toplama ~1.6s'de yuvarlanır
 *      (translateY + CSS geçişi), ardından CANLI TIK: km/sn oranı
 *      BarlasCinema.loop ile birikir (sekme gizlenince otomatik durur), tam
 *      sayı değişince YALNIZ değişen hane kolonlarına yazılır (ucuz DOM).
 *
 * İstatistik sayaçları bu dosyanın işi DEĞİL — mevcut motor yeni-app.js
 * initCounters ([data-count]) onları zaten saydırıyor.
 *
 * Çekirdek yoksa (cinema-core.js yüklenmemişse) hiçbir şey yapılmaz: sunucudan
 * gelen işaretleme nihai değerlerle statik görünür kalır (emniyetli düşüş).
 * Kalıp: klasik IIFE + defer (hero-static.js ile aynı).
 */
(function () {
    'use strict';

    function boot() {
        var section = document.querySelector('[data-numbers]');
        if (!section) return;

        var cinema = window.BarlasCinema;
        /* Çekirdek yok ya da hareket kapalı → statik varsayılan yeter. */
        if (!cinema || !cinema.motionOK) return;

        var map = section.querySelector('[data-numbers-map]');
        if (map) {
            cinema.lazy(map, function () {
                if (cinema.full) { drawMap(map); } else { fadeMap(map); }
            }, '15% 0px');
        }

        var odo = section.querySelector('[data-odometer]');
        if (odo) {
            cinema.lazy(odo, function () {
                initOdometer(odo, cinema);
            }, '10% 0px');
        }
    }

    /* ==================================================================
       Harita — full profil: sıralı yay çizimi + düğüm yakma
       ================================================================== */
    function drawMap(map) {
        var svg = map.querySelector('svg');
        if (!svg) return;

        var arcs = Array.prototype.slice.call(svg.querySelectorAll('[data-arc]'));

        /* Hub hemen yanar; yaylar oradan doğar. */
        var hub = svg.querySelector('[data-hub]');
        if (hub) hub.classList.add('is-lit');

        /* "Çizilmemiş" durum yalnızca ŞİMDİ eklenir (aşamalı varsayılan). */
        map.classList.add('map--prep');
        void map.getBoundingClientRect(); /* reflow → geçiş başlangıcı kilitlensin */
        map.classList.add('map--draw');

        /* Yay ucu hedefe yaklaşınca (i×90 + ~620ms) düğümü yak. transitionend
           yerine zamanlayıcı: sekme gizliyken kısılsa bile enter-once efekt. */
        arcs.forEach(function (arc, i) {
            var slug = arc.getAttribute('data-arc');
            var node = slug ? svg.querySelector('[data-node="' + slug + '"]') : null;
            if (!node) return;
            window.setTimeout(function () {
                node.classList.add('is-lit');
            }, i * 90 + 620);
        });
    }

    /* Harita — lite profil: topluca yumuşak beliriş, sıra/nabız yok. */
    function fadeMap(map) {
        var svg = map.querySelector('svg');
        if (!svg) return;

        map.classList.add('map--prep-lite');
        void map.getBoundingClientRect();
        map.classList.add('map--fade');

        var hub = svg.querySelector('[data-hub]');
        if (hub) hub.classList.add('is-lit');

        window.setTimeout(function () {
            Array.prototype.slice.call(svg.querySelectorAll('[data-node]')).forEach(function (node) {
                node.classList.add('is-lit');
            });
        }, 480);
    }

    /* ==================================================================
       Odometre — açılış yuvarlanması + canlı tık
       ================================================================== */
    function initOdometer(odo, cinema) {
        var cols = Array.prototype.slice.call(odo.querySelectorAll('[data-odo-col]'));
        if (!cols.length) return;

        var total = parseFloat(odo.getAttribute('data-odo-total')) || 0;
        var rate  = parseFloat(odo.getAttribute('data-odo-rate-per-sec')) || 0;

        /* Ekranda duran haneler (PHP nihai değeri basmıştı). */
        var digits = cols.map(function (col) {
            return parseInt(col.getAttribute('data-digit'), 10) || 0;
        });

        /* Değişen kolonlara yaz — DOM dokunuşu yalnız fark eden hanelere. */
        function render(value) {
            var s = String(Math.floor(value));
            var shift = s.length - cols.length; /* kapasite aşımı pratikte imkânsız; baş haneler korunur */
            for (var i = cols.length - 1; i >= 0; i--) {
                var idx = i + shift;
                var d = idx >= 0 ? (s.charCodeAt(idx) - 48) : digits[i];
                if (d !== digits[i]) {
                    digits[i] = d;
                    cols[i].style.setProperty('--d', String(d));
                }
            }
        }

        /* Canlı tık: kesirli km birikir, tam sayı artınca ekrana yansır.
           BarlasCinema.loop sekme gizlenince kendiliğinden durur; dönüşteki
           büyük dt bilinçli olarak sayılır (filo o sürede de yol yaptı). */
        var value = total;
        function startLiveTick() {
            odo.classList.remove('odo--roll');
            odo.classList.add('odo--live');
            if (rate <= 0) return;

            var last = null;
            var acc  = 0;
            cinema.loop(function (t) {
                if (last === null) { last = t; return; }
                var dt = (t - last) / 1000;
                last = t;
                if (dt <= 0) return;
                acc += rate * dt;
                if (acc >= 1) {
                    var inc = Math.floor(acc);
                    acc -= inc;
                    value += inc;
                    render(value);
                }
            });
        }

        if (cinema.full) {
            /* 1) Geçişsiz sıfıra çek, 2) reflow, 3) uzun geçişle hedefe yuvarlan. */
            odo.classList.add('odo--reset');
            cols.forEach(function (col) { col.style.setProperty('--d', '0'); });
            void odo.getBoundingClientRect();
            odo.classList.remove('odo--reset');
            odo.classList.add('odo--roll');

            cols.forEach(function (col, i) {
                var reel = col.querySelector('.odo__reel');
                if (reel) reel.style.transitionDelay = (i * 45) + 'ms';
                col.style.setProperty('--d', col.getAttribute('data-digit') || '0');
            });

            window.setTimeout(function () {
                cols.forEach(function (col) {
                    var reel = col.querySelector('.odo__reel');
                    if (reel) reel.style.transitionDelay = '';
                });
                startLiveTick();
            }, 1600 + cols.length * 45 + 120);
        } else {
            /* lite: açılış yuvarlanması yok (haneler zaten nihai değerde), tık başlar. */
            startLiveTick();
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot, { once: true });
    } else {
        boot();
    }
})();
