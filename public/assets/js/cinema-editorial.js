/**
 * cinema-editorial.js — editoryal katman: bölüm numaratörü + dev kontur kelime
 * =============================================================================
 * Ödül sitesi estetiği ("// 01 THE LINE-UP" numaralı bölümler, hero nesnesinin
 * arkasında dev kontur tipografi). İki bağımsız iş:
 *
 *   1) BÖLÜM NUMARATÖRÜ — ana sayfadaki başlıklı (.sec-head / .road__head)
 *      her üst düzey bölüme DOM sırasıyla "// 01".."// NN" etiketi enjekte
 *      eder + arka plana dev hayalet rakam (hayalet yalnız cine-full).
 *      Salt rakam/dekor olduğundan i18n GEREKMEZ; mutlak konumlu olduğundan
 *      düzeni İTMEZ. Görünüme girişte tek seferlik yumuşak reveal (IO).
 *
 *   2) KONTUR KELİME — .road__frame'in İLK çocuğu olarak dev "BARLAS" kontur
 *      yazısı: kanvas/stage DOM'da sonra geldiği için kelime ARKADA boyanır
 *      (blueprint montaj katmanı z:2'nin çok altında, z:0 bölgesi).
 *      'barlas:road-progress' olayıyla paralaks + opaklık rampası
 *      (ilk %20'de 0→.5 açılır, %80'den sonra söner). Yalnız cine-full.
 *
 * Katman sözleşmesi: window.BarlasCinema yoksa HİÇ dokunma; cine-off'ta
 * numaratör statik görünür (hareket yok), hayalet/kontur hiç kurulmaz.
 * Stil: cinema-editorial.css. Klasik IIFE + defer (hero-static.js kalıbı).
 */
(function () {
    'use strict';

    function clamp01(v) { return v < 0 ? 0 : (v > 1 ? 1 : v); }

    /* ---------------- 1) Bölüm numaratörü ---------------- */

    function initChapters(cine) {
        var sections = document.querySelectorAll('main > section');
        var hosts = [];
        var no = 0;

        for (var i = 0; i < sections.length; i++) {
            /* Başlıksız bölümler (hero, marquee) numara ALMAZ — sayaç akmaz. */
            var head = sections[i].querySelector('.sec-head, .road__head');
            if (!head) continue;

            no++;
            var label = no < 10 ? '0' + no : String(no);
            head.classList.add('cine-chap-host');

            /* Küçük numaratör: "// 01" + inline-end'e uzayan ince çizgi
               (çizgi CSS ::after'da — ek düğüm yok). */
            var chap = document.createElement('span');
            chap.className = 'cine-chap';
            chap.setAttribute('aria-hidden', 'true');
            var noEl = document.createElement('span');
            noEl.className = 'cine-chap__no';
            noEl.textContent = '// ' + label;
            chap.appendChild(noEl);
            head.appendChild(chap);

            /* Dev hayalet rakam — yalnız tam katman (masaüstü). Kırpıcı
               sarmalayıcı rakamı başlık kutusuna hapseder; z:-1 metnin
               ARKASINA düşer (host kendi stacking context'ini kurar). */
            if (cine.full) {
                var ghost = document.createElement('span');
                ghost.className = 'cine-chap-ghost';
                ghost.setAttribute('aria-hidden', 'true');
                var big = document.createElement('b');
                big.textContent = label;
                ghost.appendChild(big);
                head.appendChild(ghost);
            }

            hosts.push(head);
        }

        if (!hosts.length) return;

        /* Hareket kapalı ya da IO yok → anında görünür (içerik gizli kalmaz). */
        if (!cine.motionOK || !('IntersectionObserver' in window)) {
            for (var j = 0; j < hosts.length; j++) hosts[j].classList.add('is-on');
            return;
        }

        /* Görünüme girince TEK seferlik reveal — sonra gözlem bırakılır. */
        var io = new IntersectionObserver(function (entries) {
            for (var k = 0; k < entries.length; k++) {
                if (entries[k].isIntersecting) {
                    entries[k].target.classList.add('is-on');
                    io.unobserve(entries[k].target);
                }
            }
        }, { threshold: 0.2, rootMargin: '0px 0px -8% 0px' });

        for (var m = 0; m < hosts.length; m++) io.observe(hosts[m]);
    }

    /* ---------------- 2) Kontur kelime (yol sahnesinin arkası) ---------------- */

    function initOutlineWord(cine) {
        if (!cine.full) return; // lite/off: hiç kurulmaz (CSS de kapılı)

        var road  = document.querySelector('section.road[data-road]');
        var frame = road ? road.querySelector('.road__frame') : null;
        if (!frame) return;

        /* Marka adı yerelden BAĞIMSIZDIR (i18n gerekmez). JS'te lang() yok:
           footer wordmark'ı (Common.site_name) DOM kaynağıdır; yoksa belge
           başlığının ilk kelimesi; o da uymazsa literal marka. */
        var brand = 'BARLAS';
        var wm    = document.querySelector('.site-footer__wordmark');
        var src   = ((wm && wm.textContent) || document.title || '').trim();
        var first = src.split(/\s+/)[0] || '';
        if (/^[A-Za-z][A-Za-z0-9-]{2,11}$/.test(first)) brand = first.toUpperCase();

        var layer = document.createElement('div');
        layer.className = 'cine-word';
        layer.setAttribute('aria-hidden', 'true');
        var word = document.createElement('span');
        word.className = 'cine-word__text';
        word.textContent = brand;
        layer.appendChild(word);

        /* İLK çocuk olarak gir → stage/kanvas DOM'da sonra boyanır, kelime
           arkada kalır (blueprint z:2 ile çakışma yok). */
        frame.insertBefore(layer, frame.firstChild);

        /* Paralaks + opaklık. Yayıncı (yeni-tanker.js) olayı zaten seyreltir
           (Δ>0.002) — rAF kuyruğuna gerek yok, doğrudan handler'da yaz. */
        function apply(p) {
            p = clamp01(p);
            var y = 70 - p * 150; // px: sahne ilerledikçe yukarı süzülür
            var o;
            if (p < 0.2)      { o = (p / 0.2) * 0.5; }        // ilk %20: 0 → .5
            else if (p > 0.8) { o = ((1 - p) / 0.2) * 0.5; }  // son %20: .5 → 0
            else              { o = 0.5; }
            word.style.transform = 'translate3d(0,' + y.toFixed(1) + 'px,0)';
            word.style.opacity   = o.toFixed(3);
        }

        road.addEventListener('barlas:road-progress', function (ev) {
            var p = (ev && ev.detail && typeof ev.detail.progress === 'number')
                ? ev.detail.progress : 0;
            apply(p);
        });

        /* Sayfa sahne ortasında açılırsa ilk değeri CSS değişkeninden al. */
        var cssP = parseFloat(getComputedStyle(road).getPropertyValue('--road-p'));
        apply(isNaN(cssP) ? 0 : cssP);
    }

    /* ---------------- önyükleme ---------------- */

    function boot() {
        var cine = window.BarlasCinema;
        if (!cine) return; // sinematik çekirdek yoksa editoryal katman da yok

        /* Çift yükleme sigortası: partial section'ı + ileride layout merkezî
           yüklerse script iki kez koşabilir — ikinci koşu hiçbir şey yapmaz. */
        var html = document.documentElement;
        if (html.hasAttribute('data-cine-editorial')) return;
        html.setAttribute('data-cine-editorial', '1');

        initChapters(cine);
        initOutlineWord(cine);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot, { once: true });
    } else {
        boot();
    }
})();
