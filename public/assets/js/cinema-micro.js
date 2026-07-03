/**
 * cinema-micro.js — mikro etkileşim katmanı (sinematik deneyim v3.0, §6)
 * =============================================================================
 * Küçük ama "pahalı hissettiren" dokunuşlar. Tümü cinema-core.js'in katman
 * kararına saygılıdır; DOM'a YALNIZCA seçici ile bağlanır, view dosyalarına
 * dokunmaz. Stiller: cinema-micro.css.
 *
 *   A) Mıknatıs butonlar (full)        — CTA imlece doğru hafifçe çekilir,
 *      bırakınca yay gibi geri döner. Hedefler: .hs__actions .btn,
 *      .option-card--primary, [data-magnetic]. Dokunmatikte asla.
 *   B) 3D tilt + parlama (full)        — kart imleç altında ~6° eğilir,
 *      radyal bir parlama imleci izler. Hedefler: .option-card, [data-tilt].
 *      Pinlenen .road sahnesindekiler atlanır. Mevcut hover stilleri korunur
 *      (kalkış cinema-micro.css'te transform'a yeniden eklenir).
 *   C) Kinetik tipografi (lite+full)   — .sec-head__title kelime kelime girer
 *      (tek sefer, IO eşiği .35). Alt öğesi olan / boşluksuz (zh) / hero
 *      içindeki başlıklar atlanır. Gizleme sınıfı gözlemden HEMEN önce
 *      eklenir → JS/IO yoksa metin daima görünür (aşamalı iyileştirme).
 *   D) Kenar ışını (lite+full)         — yalnız CSS; cinema-micro.css'te.
 *   E) Sayfa geçiş perdesi (full)      — site içi linkte branda çekilir gibi
 *      perde iner, içinde kendini çizen şasi (kaynak) yükleyicisi döner,
 *      sonra location.assign. bfcache: pageshow'da perde zorla gizlenir.
 *   F) Footer paskalya yumurtası (lite+full) — footer görünür olunca minik
 *      bir çekici-tanker siluet kenardan gelip %12 inline-end'e park eder,
 *      dörtlüleri tam iki kez yanıp söner. Sayfa başına bir kez.
 *
 * Performans: yalnız transform/opacity; pointermove işleri rAF'e ertelenir;
 * ortak döngü sakinleşince kendini durdurur (BarlasCinema.loop üstünden
 * sekme gizlenince de otomatik susar). Katman 'off' → hiçbir şey kurulmaz.
 *
 * Bağımlılık: cinema-core.js (window.BarlasCinema). GSAP gerekmez.
 * Klasik IIFE + defer (hero-static.js ile aynı kalıp).
 */
(function () {
    'use strict';

    function qa(sel, root) {
        return Array.prototype.slice.call((root || document).querySelectorAll(sel));
    }
    function clamp(v, a, b) { return v < a ? a : (v > b ? b : v); }

    /* =========================================================================
       A + B) MIKNATIS + 3D TILT — tek paylaşılan rAF motoru
       -------------------------------------------------------------------------
       pointermove yalnızca HEDEF değerleri günceller (ucuz); yazma işini
       (CSS değişkenleri) rAF döngüsü yapar. Her şey sıfıra oturunca döngü
       kendini duraklatır, ilk imleç hareketinde uyanır.
       ===================================================================== */
    function initPointerFx(C) {
        var MAG_RADIUS = 90;   // px — eleman sınırlarının ötesindeki çekim alanı
        var MAG_MAX    = 10;   // px — azami çekilme
        var MAG_PULL   = 0.12; // imleç ofseti → çekim katsayısı
        var TILT_MAX   = 6;    // derece
        var CALM_LIMIT = 30;   // bu kadar kare hareketsizlik → döngü uyur

        var mags = [], tilts = [];
        var mx = -1e4, my = -1e4; // imleç (viewport) — başta çok uzakta
        var engine = null, calm = 0;

        /* --- mıknatıs hedefleri: hero CTA + birincil kart + opt-in --- */
        qa('.hs__actions .btn, .option-card--primary, [data-magnetic]').forEach(function (el) {
            // hs__reveal içindeki dekoratif CTA kopyası gibi gizli öğeler hariç
            if (el.closest('[aria-hidden="true"]')) return;
            el.classList.add('cine-mag');
            mags.push({ el: el, x: 0, y: 0, vx: 0, vy: 0, tx: 0, ty: 0, on: false });
        });

        /* --- tilt hedefleri: seçenek kartları + opt-in (pinli .road hariç) --- */
        qa('.option-card, [data-tilt]').forEach(function (el) {
            if (el.closest('.road')) return; // pinlenen sahneyle çakışma
            el.classList.add('cine-tilt');

            var it = { el: el, rx: 0, ry: 0, trx: 0, tryy: 0, sx: 50, sy: 50, on: false, rect: null };

            el.addEventListener('pointerenter', function () {
                it.rect = el.getBoundingClientRect();
                it.on = true;
                el.classList.add('is-tilting');
                wake();
            });
            el.addEventListener('pointermove', function (e) {
                if (!it.rect) return; // yalnız hedef güncelle — yazma rAF'te
                var r = it.rect;
                var nx = clamp((e.clientX - (r.left + r.width / 2)) / (r.width / 2), -1, 1);
                var ny = clamp((e.clientY - (r.top + r.height / 2)) / (r.height / 2), -1, 1);
                it.tryy = nx * TILT_MAX;        // yatay ofset → Y ekseni dönüşü
                it.trx  = -ny * TILT_MAX;       // dikey ofset → X ekseni dönüşü
                it.sx = clamp(((e.clientX - r.left) / r.width) * 100, 0, 100);
                it.sy = clamp(((e.clientY - r.top) / r.height) * 100, 0, 100);
                wake();
            }, { passive: true });
            el.addEventListener('pointerleave', function () {
                it.on = false;
                it.trx = 0; it.tryy = 0; // yumuşakça sıfıra süzülür
                wake();
            });

            tilts.push(it);
        });

        if (!mags.length && !tilts.length) return;

        document.addEventListener('pointermove', function (e) {
            mx = e.clientX; my = e.clientY;
            wake();
        }, { passive: true });

        function wake() {
            calm = 0;
            if (engine) engine.resume();
        }

        function step() {
            var busy = false, i, it, s;

            /* --- mıknatıslar: yay entegrasyonu (hafif esneyerek döner) --- */
            for (i = 0; i < mags.length; i++) {
                it = mags[i];
                var r = it.el.getBoundingClientRect();
                if (r.width > 0) {
                    var near = mx > r.left - MAG_RADIUS && mx < r.right + MAG_RADIUS &&
                               my > r.top - MAG_RADIUS && my < r.bottom + MAG_RADIUS;
                    if (near) {
                        it.tx = clamp((mx - (r.left + r.width / 2)) * MAG_PULL, -MAG_MAX, MAG_MAX);
                        it.ty = clamp((my - (r.top + r.height / 2)) * MAG_PULL, -MAG_MAX, MAG_MAX);
                    } else {
                        it.tx = 0; it.ty = 0;
                    }
                } else {
                    it.tx = 0; it.ty = 0; // görünmez öğe çekim yapmaz
                }

                it.vx = (it.vx + (it.tx - it.x) * 0.16) * 0.74;
                it.vy = (it.vy + (it.ty - it.y) * 0.16) * 0.74;
                it.x += it.vx;
                it.y += it.vy;

                var still = it.tx === 0 && it.ty === 0 &&
                            Math.abs(it.x) < 0.05 && Math.abs(it.y) < 0.05 &&
                            Math.abs(it.vx) < 0.05 && Math.abs(it.vy) < 0.05;
                if (still) {
                    if (it.on) {
                        it.on = false;
                        it.x = 0; it.y = 0; it.vx = 0; it.vy = 0;
                        s = it.el.style;
                        s.setProperty('--mag-x', '0px');
                        s.setProperty('--mag-y', '0px');
                        it.el.classList.remove('is-magging'); // orijinal CSS geri gelir
                    }
                } else {
                    if (!it.on) {
                        it.on = true;
                        it.el.classList.add('is-magging');
                    }
                    s = it.el.style;
                    s.setProperty('--mag-x', it.x.toFixed(2) + 'px');
                    s.setProperty('--mag-y', it.y.toFixed(2) + 'px');
                    busy = true;
                }
            }

            /* --- tilt kartları: hedefe lerp --- */
            for (i = 0; i < tilts.length; i++) {
                it = tilts[i];
                it.rx += (it.trx - it.rx) * 0.18;
                it.ry += (it.tryy - it.ry) * 0.18;

                var settled = !it.on && Math.abs(it.rx) < 0.04 && Math.abs(it.ry) < 0.04;
                if (settled) {
                    if (it.el.classList.contains('is-tilting')) {
                        it.rx = 0; it.ry = 0;
                        s = it.el.style;
                        s.setProperty('--tilt-rx', '0deg');
                        s.setProperty('--tilt-ry', '0deg');
                        it.el.classList.remove('is-tilting');
                    }
                } else {
                    s = it.el.style;
                    s.setProperty('--tilt-rx', it.rx.toFixed(2) + 'deg');
                    s.setProperty('--tilt-ry', it.ry.toFixed(2) + 'deg');
                    s.setProperty('--tilt-mx', it.sx.toFixed(1) + '%');
                    s.setProperty('--tilt-my', it.sy.toFixed(1) + '%');
                    busy = true;
                }
            }

            /* --- sakinlik: her şey oturduysa döngüyü uyut --- */
            if (busy) {
                calm = 0;
            } else {
                calm++;
                if (calm > CALM_LIMIT && engine) engine.pause();
            }
        }

        engine = C.loop(step);
    }

    /* =========================================================================
       C) KİNETİK TİPOGRAFİ — bölüm başlıkları kelime kelime girer (tek sefer)
       -------------------------------------------------------------------------
       XSS-güvenli parçalama: yalnızca createTextNode/createElement; hiçbir
       yerde innerHTML'e kullanıcı metni girmez. Gizleme sınıfı (.kinetic-ready)
       gözlemden hemen önce eklenir; IO yoksa hiç eklenmez → metin görünür.
       ===================================================================== */
    function initKineticType() {
        if (!('IntersectionObserver' in window)) return; // aşamalı iyileştirme

        var titles = qa('.sec-head__title');
        if (!titles.length) return;

        var io = new IntersectionObserver(function (entries) {
            for (var i = 0; i < entries.length; i++) {
                if (entries[i].isIntersecting) {
                    entries[i].target.classList.add('kinetic-in');
                    io.unobserve(entries[i].target);
                }
            }
        }, { threshold: 0.35 });

        titles.forEach(function (el) {
            if (el.children.length) return;      // içinde element var → dokunma
            if (el.closest('.hs')) return;       // hero'nun kendi introsu var

            var text = el.textContent;
            if (!/\s/.test(text.trim())) return; // boşluksuz (örn. zh) → dokunma

            var parts = text.split(/(\s+)/);
            var frag = document.createDocumentFragment();
            var idx = 0;

            parts.forEach(function (p) {
                if (!p) return;
                if (/^\s+$/.test(p)) {
                    frag.appendChild(document.createTextNode(' '));
                    return;
                }
                var mask = document.createElement('span');
                mask.className = 'kword';
                var word = document.createElement('span');
                word.appendChild(document.createTextNode(p));
                word.style.setProperty('--kw-d', (idx * 70) + 'ms');
                mask.appendChild(word);
                frag.appendChild(mask);
                idx++;
            });

            el.textContent = '';
            el.appendChild(frag);
            el.classList.add('kinetic-ready'); // gizli durum ancak ŞİMDİ başlar
            io.observe(el);
        });
    }

    /* =========================================================================
       E) SAYFA GEÇİŞ PERDESİ + KAYNAK YÜKLEYİCİ (yalnız full katman)
       -------------------------------------------------------------------------
       Site içi tıklamada perde branda gibi çekilir (~380ms), içinde şasi
       konturu kendini çizer (kaynak kıvılcımı uçlu), sonra gerçek gezinme.
       bfcache dönüşünde ve 4sn emniyet zamanında perde zorla gizlenir.
       İşaretleme statiktir (kullanıcı verisi yok) → innerHTML güvenli.
       ===================================================================== */
    function initCurtain() {
        var NAV_DELAY = 400;  // perde geçişi 380ms — payıyla
        var SAFETY    = 4000; // gezinme takılırsa perdeyi kaldır

        var curtain = document.createElement('div');
        curtain.className = 'cine-curtain';
        curtain.setAttribute('aria-hidden', 'true');
        curtain.innerHTML =
            '<div class="cine-curtain__stage">' +
                '<svg class="cine-weld" viewBox="0 0 260 120" xmlns="http://www.w3.org/2000/svg" fill="none">' +
                    // Lowboy şasi konturu: sol rampa → boyun → yatak → arka + alt ray + destek ayağı
                    '<path class="cine-weld__path" pathLength="1" d="M12 74 H64 L80 56 H206 V74 H248 M248 74 V86 H12 V74 M44 86 V102"/>' +
                    '<circle class="cine-weld__wheel" pathLength="1" cx="188" cy="96" r="10"/>' +
                    '<circle class="cine-weld__wheel" pathLength="1" cx="216" cy="96" r="10"/>' +
                    // Kaynak kıvılcımı — çizim ucunu offset-path ile izler
                    '<g class="cine-weld__spark">' +
                        '<circle class="cine-weld__halo" r="6"/>' +
                        '<circle class="cine-weld__core" r="2.6"/>' +
                    '</g>' +
                '</svg>' +
            '</div>';
        document.body.appendChild(curtain);

        var busy = false;
        var safetyTimer = 0;

        function hide() {
            busy = false;
            if (safetyTimer) { window.clearTimeout(safetyTimer); safetyTimer = 0; }
            curtain.classList.remove('is-active');
        }

        /* bfcache: geri tuşuyla dönen kullanıcı asla takılı perde görmesin */
        window.addEventListener('pageshow', function () { hide(); });

        document.addEventListener('click', function (e) {
            if (busy || e.defaultPrevented) return;
            if (e.button !== 0 || e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) return;

            var a = e.target && e.target.closest ? e.target.closest('a[href]') : null;
            if (!a) return;
            if (a.closest('[data-no-curtain]')) return;

            var target = a.getAttribute('target');
            if (target && target !== '_self') return;      // yeni sekme → perde yok
            if (a.hasAttribute('download')) return;

            var raw = a.getAttribute('href');
            if (!raw || raw.charAt(0) === '#') return;      // sayfa içi çıpa

            var url;
            try { url = new URL(raw, window.location.href); } catch (err) { return; }

            // mailto:, tel:, javascript: vb. protokoller + farklı origin elenir
            if (url.protocol !== 'http:' && url.protocol !== 'https:') return;
            if (url.origin !== window.location.origin) return;

            // aynı sayfada yalnız hash değişiyorsa (yumuşak kaydırma) karışma
            if (url.pathname === window.location.pathname &&
                url.search === window.location.search && url.hash) return;

            e.preventDefault();
            busy = true;
            curtain.classList.add('is-active');
            window.setTimeout(function () { window.location.assign(url.href); }, NAV_DELAY);
            safetyTimer = window.setTimeout(hide, SAFETY);
        });
    }

    /* =========================================================================
       F) FOOTER PASKALYA YUMURTASI — minik çekici footer'a park eder
       -------------------------------------------------------------------------
       Footer ilk kez görünüme girince (BarlasCinema.lazy) inline-start
       kenarından süzülür, inline-end %12'ye park eder, dörtlüleri TAM İKİ KEZ
       yanar (CSS animasyonu, iteration-count: 2) ve öylece kalır. Footer'ın
       overflow:hidden'ı giriş anını doğal biçimde maskeler. Süsleme öğesi:
       aria-hidden, pointer-events yok. İşaretleme statik → innerHTML güvenli.
       ===================================================================== */
    function initFooterHauler(C) {
        var footer = document.querySelector('footer.site-footer');
        if (!footer) return;

        C.lazy(footer, function () {
            var rtl = (document.documentElement.getAttribute('dir') || '').toLowerCase() === 'rtl';

            var wrap = document.createElement('div');
            wrap.className = 'cine-hauler';
            wrap.setAttribute('aria-hidden', 'true');
            wrap.innerHTML =
                '<svg viewBox="0 0 120 60" xmlns="http://www.w3.org/2000/svg">' +
                    '<g fill="currentColor">' +
                        '<rect x="4" y="20" width="66" height="20" rx="10"/>' +   // tank
                        '<rect x="4" y="42" width="74" height="3" rx="1.5"/>' +   // şasi
                        '<path d="M80 45 V26 H94 L105 33 V45 Z"/>' +              // kabin
                        '<circle cx="16" cy="49" r="6"/>' +
                        '<circle cx="30" cy="49" r="6"/>' +
                        '<circle cx="58" cy="49" r="6"/>' +
                        '<circle cx="86" cy="49" r="6"/>' +
                        '<circle cx="100" cy="49" r="6"/>' +
                    '</g>' +
                    '<circle class="cine-hauler__blink" cx="6" cy="16" r="2.5"/>' +
                    '<circle class="cine-hauler__blink" cx="107" cy="30" r="2.5"/>' +
                '</svg>';
            footer.appendChild(wrap);

            /* Başlangıç: park yerinden inline-start kenarının DIŞINA taşı.
               (offsetLeft fizikseldir; RTL'de inline-start = sağ kenar.) */
            var fw = footer.clientWidth || 1200;
            var startX = rtl
                ? (fw - wrap.offsetLeft) + 40
                : -(wrap.offsetLeft + wrap.offsetWidth + 40);

            wrap.style.transform = 'translateX(' + startX + 'px)';
            void wrap.offsetWidth; // reflow — geçiş başlangıcı sabitlensin
            wrap.classList.add('is-driving');
            wrap.style.transform = 'translateX(0px)';

            /* Sürüş 2.8s (CSS) → park → dörtlüler iki kez yanar */
            window.setTimeout(function () {
                wrap.classList.add('is-parked');
            }, 2950);
        }, '0px');
    }

    /* ------------------------------- önyükleme ------------------------------ */
    function boot() {
        var C = window.BarlasCinema;
        if (!C || !C.motionOK) return; // katman 'off' → hiç hareket yok

        initKineticType();     // lite + full
        initFooterHauler(C);   // lite + full

        if (C.full) {          // yalnız ince işaretçili masaüstü
            initPointerFx(C);  // mıknatıs + tilt + parlama
            initCurtain();     // sayfa geçiş perdesi
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot, { once: true });
    } else {
        boot();
    }
})();
