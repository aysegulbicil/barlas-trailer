/**
 * cinema-atmosphere.js — atmosfer modülü (v3.0 §4 + §5 hız çizgileri + §2 far)
 * =============================================================================
 * Dört bağımsız süsleme (görünümler cinema-atmosphere.css'te):
 *   A) .cine-aurora     — nefes alan aurora arka planı (saf CSS keyframe;
 *                         JS yalnız katmanı ekler). Katman: lite + full.
 *   B) .cine-dust       — seyrek toz/ışık zerreleri kanvası + scroll hız
 *                         çizgileri (çizgiler yalnız full). BarlasCinema.loop
 *                         ile döner → sekme gizlenince otomatik durur.
 *                         DPR tavanı: full 2 / lite 1.25.
 *   C) .cine-spot       — imleç spot ışığı (yalnız full + ince işaretçi;
 *                         açık temada CSS gizler). Çark imlecine (.cursor)
 *                         DOKUNMAZ — yalnız altına katkı parlaması.
 *   D) .cine-headlights — ana sayfa hero'suna ([data-hero-static]) TEK
 *                         SEFERLİK far süpürmesi. [data-cine-intro] varsa
 *                         'cinema:intro:done' olayını bekler (8 sn emniyet
 *                         zamanlayıcısı ile). Katman: lite + full.
 *
 * Katman kuralı: sabit arka plan katmanları .depth-bg kalıbını AYNEN kopyalar
 * (position:fixed; inset:0; z-index:-1; pointer-events:none). Aynı z'deki
 * kardeşlerde boyama sırasını DOM ekleme sırası belirler → toz kanvası
 * auroradan SONRA eklenir, üstünde boyanır.
 *
 * Bağımlılık: cinema-core.js (window.BarlasCinema). tier 'off' → HİÇBİR görsel
 * eklenmez (reduced-motion = tam statik geri dönüş).
 * Klasik IIFE + defer (hero-static.js ile aynı kalıp).
 */
(function () {
    'use strict';

    function clamp01(v) { return v < 0 ? 0 : (v > 1 ? 1 : v); }

    /* --cine-glow token'ından rgb üçlüsü çıkar (kanvas renkleri için).
       Token okunamazsa cinema.css varsayılanına (#4f8dff) düşer. */
    function glowRgb() {
        var raw = '';
        try {
            raw = getComputedStyle(document.documentElement)
                .getPropertyValue('--cine-glow').trim();
        } catch (e) { /* yut */ }
        var m = /^#([0-9a-f]{6})$/i.exec(raw);
        if (!m) return [79, 141, 255];
        var n = parseInt(m[1], 16);
        return [(n >> 16) & 255, (n >> 8) & 255, n & 255];
    }

    /* ---------------------------------------------------------------------
       A) NEFES ALAN AURORA — katmanı body sonuna ekle; sürüklenme tamamen
       CSS keyframe'lerinde (transform-only, 60–90 sn). Gece kayması da saf
       CSS (--cine-night → opaklık), JS'e iş düşmez.
       --------------------------------------------------------------------- */
    function initAurora() {
        var el = document.createElement('div');
        el.className = 'cine-aurora';
        el.setAttribute('aria-hidden', 'true');
        el.innerHTML =
            '<span class="cine-aurora__blob cine-aurora__blob--a"></span>' +
            '<span class="cine-aurora__blob cine-aurora__blob--b"></span>' +
            '<span class="cine-aurora__blob cine-aurora__blob--c"></span>';
        document.body.appendChild(el);
    }

    /* ---------------------------------------------------------------------
       B) PARTİKÜL ALANI (yol tozu) + HIZ ÇİZGİLERİ — tek sabit kanvas.
       Zerreler yavaşça yukarı süzülür, hafif yana salınır, kenarlardan sarar.
       Full katmanda scroll hızı eşik aşınca kısa dikey izler de çizilir;
       hız üstel sönümlü → scroll durunca izler kendiliğinden kaybolur.
       --------------------------------------------------------------------- */
    function initDust(C) {
        var canvas = document.createElement('canvas');
        canvas.className = 'cine-dust';
        canvas.setAttribute('aria-hidden', 'true');
        document.body.appendChild(canvas);

        var ctx = canvas.getContext('2d');
        if (!ctx) { canvas.parentNode.removeChild(canvas); return; }

        var FULL = C.full;
        var COUNT = FULL ? 48 : 14;
        var DPR_CAP = FULL ? 2 : 1.25;

        /* Zerre rengi: --cine-glow beyaza doğru açılır → koyu fonda okunur,
           yine de göz batmaz. Alfa çizim anında eklenir. */
        var g = glowRgb();
        var tint = 'rgba(' +
            Math.round(g[0] + (255 - g[0]) * 0.40) + ',' +
            Math.round(g[1] + (255 - g[1]) * 0.35) + ',' +
            Math.round(g[2] + (255 - g[2]) * 0.20) + ',';

        var w = 0, h = 0;
        function resize() {
            var dpr = Math.min(window.devicePixelRatio || 1, DPR_CAP);
            w = window.innerWidth;
            h = window.innerHeight;
            canvas.width = Math.round(w * dpr);
            canvas.height = Math.round(h * dpr);
            ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        }
        resize();
        window.addEventListener('resize', resize);

        /* ------- toz zerreleri ------- */
        var motes = [];
        for (var i = 0; i < COUNT; i++) {
            motes.push({
                x: Math.random() * w,
                y: Math.random() * h,
                r: 0.6 + Math.random() * 1.4,        /* 0.6–2 px yarıçap */
                a: 0.12 + Math.random() * 0.30,      /* alfa ≤ .5 */
                vy: 6 + Math.random() * 12,          /* px/sn yukarı süzülme */
                sway: 4 + Math.random() * 9,         /* yana salınım genliği */
                ph: Math.random() * Math.PI * 2,     /* salınım fazı */
                sp: 0.2 + Math.random() * 0.5        /* salınım hızı (rad/sn) */
            });
        }

        /* ------- hız çizgisi havuzu (yalnız full) ------- */
        var streaks = [];
        if (FULL) {
            for (var s = 0; s < 12; s++) {
                streaks.push({
                    x: Math.random() * w,
                    y: Math.random() * h,
                    len: 36 + Math.random() * 96,
                    tw: 0.45 + Math.random() * 0.55  /* parlaklık çeşitliliği */
                });
            }
        }
        var lastScroll = window.pageYOffset || 0;
        var vel = 0; /* yumuşatılmış px/kare — girdi kesilince üstel söner */

        var last = 0;
        function step(t) {
            /* Sekme dönüşünde dev dt sıçramasını kırp (maks ~4 kare). */
            var dt = last ? Math.min((t - last) / 1000, 0.064) : 0.016;
            last = t;

            ctx.clearRect(0, 0, w, h);

            /* toz zerreleri */
            for (var i = 0; i < motes.length; i++) {
                var m = motes[i];
                m.ph += m.sp * dt;
                m.y -= m.vy * dt;
                var x = m.x + Math.sin(m.ph) * m.sway;
                if (m.y < -4) { m.y = h + 4; m.x = Math.random() * w; }
                if (x < -4) { x += w + 8; }
                else if (x > w + 4) { x -= w + 8; }
                ctx.beginPath();
                ctx.arc(x, m.y, m.r, 0, Math.PI * 2);
                ctx.fillStyle = tint + m.a.toFixed(3) + ')';
                ctx.fill();
            }

            /* hız çizgileri */
            if (FULL) {
                var sy = window.pageYOffset || 0;
                var dy = sy - lastScroll;
                lastScroll = sy;
                vel += (dy - vel) * 0.14; /* dy=0 iken üstel sönüm */

                var strength = clamp01((Math.abs(vel) - 6) / 26);
                if (strength > 0.01) {
                    var dir = vel > 0 ? 1 : -1;
                    ctx.lineWidth = 1.2;
                    ctx.lineCap = 'round';
                    for (var k = 0; k < streaks.length; k++) {
                        var q = streaks[k];
                        /* İzler scroll'un TERSİNE akar (hareket izlenimi). */
                        q.y -= dir * Math.abs(vel) * 1.6 * (0.6 + q.tw * 0.6);
                        if (q.y < -q.len) { q.y = h + q.len; q.x = Math.random() * w; }
                        else if (q.y > h + q.len) { q.y = -q.len; q.x = Math.random() * w; }
                        ctx.strokeStyle = tint + (strength * 0.3 * q.tw).toFixed(3) + ')';
                        ctx.beginPath();
                        ctx.moveTo(q.x, q.y);
                        ctx.lineTo(q.x, q.y + q.len * (0.4 + strength * 0.8) * dir);
                        ctx.stroke();
                    }
                }
            }
        }
        C.loop(step);
    }

    /* ---------------------------------------------------------------------
       C) İMLEÇ SPOT IŞIĞI — fareyi lerp (~0.12) ile izleyen, screen karışımlı
       yumuşak parlama. Yalnız full katman + ince işaretçi; açık temada CSS
       tamamen gizler. İlk mousemove'da belirir, işaretçi pencereden çıkınca
       söner. Çark imleci (.cursor) ile İLİŞKİSİ YOK.
       --------------------------------------------------------------------- */
    function initSpotlight(C) {
        if (!C.full) return;
        var fine = false;
        try { fine = window.matchMedia('(hover: hover) and (pointer: fine)').matches; }
        catch (e) { /* yut */ }
        if (!fine) return;

        var el = document.createElement('div');
        el.className = 'cine-spot';
        el.setAttribute('aria-hidden', 'true');
        document.body.appendChild(el);

        var HALF = 260; /* 520 px çapın yarısı — CSS ile eş */
        var tx = window.innerWidth / 2, ty = window.innerHeight / 2;
        var x = tx, y = ty, seen = false;

        document.addEventListener('mousemove', function (e) {
            tx = e.clientX;
            ty = e.clientY;
            if (!seen) {
                seen = true;
                x = tx; y = ty; /* ilk karede merkezden kayma olmasın */
                el.classList.add('is-on');
            }
        }, { passive: true });

        /* relatedTarget yok = işaretçi pencereyi terk etti. */
        document.addEventListener('mouseout', function (e) {
            if (!e.relatedTarget) el.classList.remove('is-on');
        });
        document.addEventListener('mouseover', function (e) {
            if (seen && !e.relatedTarget) el.classList.add('is-on');
        });

        C.loop(function () {
            x += (tx - x) * 0.12;
            y += (ty - y) * 0.12;
            el.style.transform = 'translate3d(' + (x - HALF).toFixed(1) + 'px,' +
                (y - HALF).toFixed(1) + 'px,0)';
        });
    }

    /* ---------------------------------------------------------------------
       D) HERO FAR SÜPÜRMESİ — yalnız ana sayfa ([data-hero-static]). İki far
       parlaması hero üzerinden BİR KEZ geçer (~1.4 sn, saf CSS animasyonu);
       sonra .is-done ile katman uykuya geçer.

       Zamanlama sözleşmesi: sayfada [data-cine-intro] (intro perdesi) varsa
       far, document üzerindeki 'cinema:intro:done' olayını bekler; olay 8 sn
       içinde gelmezse emniyet zamanlayıcısı devreye girer. Perde yoksa hero
       görünür olur olmaz oynar.
       --------------------------------------------------------------------- */
    function initHeadlights(C) {
        var hero = document.querySelector('[data-hero-static]');
        if (!hero) return;

        /* Pin katmanı tam-ekran ve overflow:hidden → far dışarı taşmaz. */
        var host = hero.querySelector('[data-hs-pin]') || hero;
        var box = document.createElement('div');
        box.className = 'cine-headlights';
        box.setAttribute('aria-hidden', 'true');
        box.innerHTML =
            '<span class="cine-headlights__beam cine-headlights__beam--a"></span>' +
            '<span class="cine-headlights__beam cine-headlights__beam--b"></span>';
        host.appendChild(box);

        var played = false;
        function play() {
            if (played) return;
            played = true;
            box.classList.add('is-play');
            /* Süpürme ~1.4 sn; küçük payla katmanı tamamen kapat. */
            window.setTimeout(function () { box.classList.add('is-done'); }, 1700);
        }
        function armWhenVisible() {
            C.lazy(hero, function () { play(); }, '0px');
        }

        if (document.querySelector('[data-cine-intro]')) {
            var safety = window.setTimeout(armWhenVisible, 8000);
            document.addEventListener('cinema:intro:done', function () {
                window.clearTimeout(safety);
                armWhenVisible();
            }, { once: true });
        } else {
            armWhenVisible();
        }
    }

    /* ------------------------------ önyükleme ------------------------------ */

    function boot() {
        var C = window.BarlasCinema;
        if (!C) return;
        if (C.tier === 'off') return; /* reduced-motion: hiçbir görsel ekleme */

        initAurora();
        initDust(C);       /* auroradan SONRA → z:-1 kardeşler içinde üstte boyanır */
        initSpotlight(C);
        initHeadlights(C);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot, { once: true });
    } else {
        boot();
    }
})();
