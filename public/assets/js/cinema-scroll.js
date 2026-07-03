/**
 * cinema-scroll.js — kaydırma sineması (v3.0 addendum §2 + §5)
 * =============================================================================
 * Üç görev:
 *   A) GÜNDÜZ→GECE SÜRÜCÜSÜ — global gece değerinin TEK doğruluk kaynağı.
 *      Sayfa ilerlemesi p = scrollY / (scrollHeight - innerHeight);
 *      night = smoothstep(0.10, 0.88, p) → BarlasCinema.setNight(night).
 *      Scroll dinleyicisi yalnızca bayrak kaldırır; hesap rAF'ta birleşir.
 *      Görsel karartma (.cine-nightfall) tamamen CSS'tir (--cine-night okur).
 *   B) YOL ÇİZGİSİ OMURGASI — yalnız ana sayfa (body.has-hero-header) ve
 *      yalnız FULL katman. #main-content üzerine dikey kesikli SVG çizgi;
 *      kesikler maske hilesiyle (stroke-dashoffset = H·(1-p)) scroll'la
 *      kendini çizer. Pin-spacer'lar sayfayı uzattığı için yükseklik
 *      'load' + ScrollTrigger 'refresh' olaylarında yeniden ölçülür.
 *   C) YAŞAYAN ARKA PLAN DÜNYASI — .depth-bg kalıbında sabit katman:
 *      ufuk çizgisi + gece yükseldikçe beliren şehir ışıkları (lite+full),
 *      ara sıra ufku geçen tır silueti (yalnız full + koyu tema).
 *
 * Sayaçlar ([data-count]) yeni-app.js'te zaten var — burada TEKRAR YOK.
 * Bağımlılık: cinema-core.js (window.BarlasCinema). GSAP/ScrollTrigger
 * zorunlu değil; varsa yalnız 'refresh' olayına abone olunur.
 * Klasik IIFE + defer (hero-static.js kalıbı).
 */
(function () {
    'use strict';

    function clamp01(v) { return v < 0 ? 0 : (v > 1 ? 1 : v); }
    function smooth(a, b, x) { var t = clamp01((x - a) / (b - a)); return t * t * (3 - 2 * t); }

    /* Tır silueti: tank dorsesi + çekici + tekerler + kehribar far (≈90px). */
    var TRUCK_SVG =
        '<svg viewBox="0 0 92 32" xmlns="http://www.w3.org/2000/svg" focusable="false" aria-hidden="true">' +
            '<g fill="currentColor">' +
                '<rect x="0" y="5" width="66" height="14" rx="7"/>' +
                '<rect x="2" y="20" width="64" height="2"/>' +
                '<path d="M70 22 V9 h10 l9 8 v5 z"/>' +
                '<circle cx="10" cy="26" r="4"/><circle cx="20" cy="26" r="4"/>' +
                '<circle cx="50" cy="26" r="4"/><circle cx="60" cy="26" r="4"/>' +
                '<circle cx="75" cy="26" r="4"/><circle cx="86" cy="26" r="4"/>' +
            '</g>' +
            '<circle class="cine-world__lamp" cx="88.5" cy="18.5" r="1.6"/>' +
        '</svg>';

    function boot() {
        var C = window.BarlasCinema;
        if (!C) return;

        var html = document.documentElement;
        var body = document.body;

        /* 'off' katmanı: görsel hiçbir şey yok; CSS gündüzde sabitlensin. */
        if (!C.motionOK) {
            C.setNight(0);
            return;
        }

        /* ScrollTrigger kullanılıyorsa (yalnız 'refresh' aboneliği) kaydı garantile. */
        var gsap = window.gsap, ST = window.ScrollTrigger;
        if (gsap && ST) {
            try { gsap.registerPlugin(ST); } catch (e) { /* tekrar kayıt zararsız */ }
        }

        /* ================================================================
           A) GÜNDÜZ→GECE SÜRÜCÜSÜ (lite + full)
           ================================================================ */

        var docH = 1;           /* kaydırılabilir mesafe (px); 0'a bölünme yok */
        var rafPending = false; /* kirli bayrak: scroll başına en çok 1 rAF   */
        var spineUpdate = null; /* omurga kurulursa aynı rAF'tan beslenir     */
        var spineRebuild = null;

        function measure() {
            var page = Math.max(
                html.scrollHeight || 0,
                body ? (body.scrollHeight || 0) : 0
            );
            docH = Math.max(1, page - window.innerHeight);
        }

        function update() {
            rafPending = false;
            var y = window.scrollY || window.pageYOffset || 0;
            var p = clamp01(y / docH);
            /* Tepede kısa bir gündüz platosu, footer'dan önce tam gece. */
            C.setNight(smooth(0.10, 0.88, p));
            if (spineUpdate) spineUpdate(p);
        }

        function schedule() {
            if (rafPending) return;
            rafPending = true;
            window.requestAnimationFrame(update);
        }

        /* Pin-spacer'lar / yeniden boyutlama sayfa yüksekliğini değiştirir. */
        function remeasure() {
            measure();
            if (spineRebuild) spineRebuild();
            schedule();
        }

        window.addEventListener('scroll', schedule, { passive: true });
        window.addEventListener('resize', remeasure);
        window.addEventListener('load', remeasure);
        if (ST && ST.addEventListener) ST.addEventListener('refresh', remeasure);

        /* Gece perdesi + vinyet: saf CSS, --cine-night'tan beslenir.
           İçeriğin ÜSTÜNDE (z-index:40) ama header (100) ve imleç (12000) altında. */
        var shade = document.createElement('div');
        shade.className = 'cine-nightfall';
        shade.setAttribute('aria-hidden', 'true');
        body.appendChild(shade);

        var vig = document.createElement('div');
        vig.className = 'cine-vignette';
        vig.setAttribute('aria-hidden', 'true');
        body.appendChild(vig);

        /* ================================================================
           B) YOL ÇİZGİSİ OMURGASI (yalnız ana sayfa + FULL katman)
           ================================================================ */

        (function initSpine() {
            if (!C.full) return;
            if (!body.classList.contains('has-hero-header')) return;
            var host = document.getElementById('main-content');
            if (!host) return;

            host.classList.add('cine-spine-host'); /* position:relative kancası */

            var wrap = document.createElement('div');
            wrap.className = 'cine-spine';
            wrap.setAttribute('aria-hidden', 'true');
            wrap.innerHTML =
                '<svg class="cine-spine__svg" viewBox="0 0 28 100" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" focusable="false">' +
                    '<defs>' +
                        /* Maske hilesi: tek parça beyaz çizgi dasharray=H ile
                           tepeden aşağı "çizilir"; altındaki kesikli desen
                           böylece kesik kesik ortaya çıkar. */
                        '<mask id="cineSpineMask" maskUnits="userSpaceOnUse" x="0" y="0" width="28" height="100">' +
                            '<line class="js-spine-mask" x1="14" y1="0" x2="14" y2="100" stroke="#fff" stroke-width="28"/>' +
                        '</mask>' +
                    '</defs>' +
                    /* Önümüzdeki yol: çok soluk, her zaman görünür. */
                    '<line class="cine-spine__bg" x1="14" y1="0" x2="14" y2="100" stroke="currentColor" stroke-width="2" stroke-dasharray="10 18" opacity=".18"/>' +
                    '<g mask="url(#cineSpineMask)">' +
                        '<line class="cine-spine__dash js-spine-dash" x1="14" y1="0" x2="14" y2="100" stroke="currentColor" stroke-width="2" stroke-dasharray="10 18"/>' +
                        '<path class="cine-spine__chev js-spine-chev" d="M0 0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" opacity=".8"/>' +
                    '</g>' +
                '</svg>';
            host.appendChild(wrap);

            var svg      = wrap.querySelector('.cine-spine__svg');
            var maskEl   = wrap.querySelector('#cineSpineMask');
            var maskLine = wrap.querySelector('.js-spine-mask');
            var bgLine   = wrap.querySelector('.cine-spine__bg');
            var dashLine = wrap.querySelector('.js-spine-dash');
            var chevPath = wrap.querySelector('.js-spine-chev');
            var spineH   = 0;
            var lastP    = -1;

            /* Her ~460px'te bir aşağı bakan şerit oku (ikinci "yol işareti"). */
            function buildChevrons(h) {
                var d = '', y;
                for (y = 460; y < h - 140; y += 460) {
                    d += 'M9 ' + y + ' L14 ' + (y + 8) + ' L19 ' + y + ' ';
                }
                return d || 'M0 0';
            }

            spineRebuild = function () {
                var h = Math.max(600, host.offsetHeight | 0);
                if (h === spineH) return;
                spineH = h;
                svg.setAttribute('viewBox', '0 0 28 ' + h);
                maskEl.setAttribute('height', h);
                maskLine.setAttribute('y2', h);
                maskLine.setAttribute('stroke-dasharray', String(h));
                bgLine.setAttribute('y2', h);
                dashLine.setAttribute('y2', h);
                chevPath.setAttribute('d', buildChevrons(h));
                lastP = -1; /* offset'i zorla tazele */
            };

            spineUpdate = function (p) {
                if (p === lastP) return;
                lastP = p;
                maskLine.setAttribute('stroke-dashoffset', (spineH * (1 - p)).toFixed(1));
            };

            spineRebuild();
        })();

        /* ================================================================
           C) YAŞAYAN ARKA PLAN DÜNYASI (lite: ışıklar; full: + tır)
           ================================================================ */

        var world = document.createElement('div');
        world.className = 'cine-world';
        world.setAttribute('aria-hidden', 'true');

        /* Ufuk çizgisi — gece yükseldikçe belirir (opaklık CSS'te). */
        var horizon = document.createElement('div');
        horizon.className = 'cine-world__horizon';
        world.appendChild(horizon);

        /* Uzak şehir ışıkları: 7–9 minik nokta, rastgele konum/faz. */
        var lights = document.createElement('div');
        lights.className = 'cine-world__lights';
        var dotCount = 7 + Math.floor(Math.random() * 3);
        var i, dot;
        for (i = 0; i < dotCount; i++) {
            dot = document.createElement('span');
            dot.className = 'cine-world__dot' + (Math.random() < 0.22 ? ' cine-world__dot--ember' : '');
            dot.style.setProperty('inset-inline-start', (4 + Math.random() * 92).toFixed(1) + '%');
            dot.style.bottom = (16.2 + Math.random() * 3.4).toFixed(2) + '%';
            dot.style.animationDuration = (3.6 + Math.random() * 3.6).toFixed(2) + 's';
            /* Negatif gecikme: her nokta farklı fazdan başlar, koro etkisi yok. */
            dot.style.animationDelay = (-Math.random() * 6).toFixed(2) + 's';
            lights.appendChild(dot);
        }
        world.appendChild(lights);

        /* Tır silueti — yalnız FULL katman; tema kontrolü doğum anında. */
        (function initTruck() {
            if (!C.full) return;

            var truck = document.createElement('div');
            truck.className = 'cine-world__truck';
            truck.innerHTML = TRUCK_SVG;
            world.appendChild(truck);

            var truckTimer = 0;      /* bekleyen randevu            */
            var truckBusy = false;   /* geçiş sürüyor mu            */
            var truckFallback = 0;   /* animationend emniyet kemeri */
            var truckDirLeft = false;

            function truckDone() {
                if (truckFallback) { window.clearTimeout(truckFallback); truckFallback = 0; }
                truck.classList.remove('cine-world__truck--go', 'cine-world__truck--ltr', 'cine-world__truck--rtl');
                truck.style.animationDuration = '';
                truckBusy = false;
                scheduleTruck();
            }

            function spawnTruck() {
                if (truckBusy) return;
                /* Açık temada siluet anlamsız → bu turu atla, sıradakini kur. */
                if (html.getAttribute('data-theme') === 'light') { scheduleTruck(); return; }
                truckBusy = true;
                truckDirLeft = !truckDirLeft; /* her geçişte yön değişir */
                var dur = 26 + Math.random() * 14; /* 26–40 sn */
                truck.classList.toggle('cine-world__truck--flip', truckDirLeft);
                truck.style.animationDuration = dur.toFixed(1) + 's';
                truck.classList.add('cine-world__truck--go', truckDirLeft ? 'cine-world__truck--rtl' : 'cine-world__truck--ltr');
                /* animationend kaçarsa (arka plan kısması vb.) yine topla. */
                truckFallback = window.setTimeout(truckDone, (dur + 3) * 1000);
            }

            function scheduleTruck() {
                if (truckTimer || truckBusy || !C.visible()) return;
                truckTimer = window.setTimeout(function () {
                    truckTimer = 0;
                    spawnTruck();
                }, 45000 + Math.random() * 45000); /* 45–90 sn'de bir */
            }

            truck.addEventListener('animationend', truckDone);

            /* Sekme gizliyken randevu KURULMAZ; dönünce takvim yeniden işler. */
            C.onVisibility(function (v) {
                if (v) {
                    scheduleTruck();
                } else if (truckTimer) {
                    window.clearTimeout(truckTimer);
                    truckTimer = 0;
                }
            });
        })();

        body.appendChild(world);

        /* İlk kare: ölç + mevcut scroll konumuna göre geceyi yaz. */
        remeasure();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot, { once: true });
    } else {
        boot();
    }
})();
