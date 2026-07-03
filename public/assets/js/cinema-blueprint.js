/**
 * cinema-blueprint.js — "çizimden yola" mavikopya montaj katmanı
 * =============================================================================
 * Yol (konvoy) bölümünün ilk evresinde, 3D sahnenin üstünde bir teknik resim
 * paftası belirir: tanker yarı römorku mühendislik çizgi çizimi olarak parça
 * parça KENDİNİ ÇİZER (şasi → dingiller → tekerler → tank → kapaklar →
 * merdiven), her parça bitince ölçü oku + numara etiketi belirir, çizim kısa
 * bir parıltıyla "onaylanır" ve katman sönerek yerini gerçek 3D konvoya
 * bırakır. Kavram: "From blueprint to road."
 *
 * İlerleme sözleşmesi: yeni-tanker.js bölüm ilerlemesini 'barlas:road-progress'
 * olayı (+ --road-p değişkeni) ile yayınlar (≥0.002 adım). Bu modül KENDİ
 * ScrollTrigger'ını KURMAZ; tüm görsel durumlar p'nin SAF fonksiyonudur —
 * geri sarma bedavadır (tek seferlik görsel bayrak yok).
 *
 * Yazım stratejisi: olaylar rAF'a bayrakla toplanır (schedule). Gerekçe:
 * 3D yokken devreye giren scroll-yedeği ham 'scroll' olaylarıyla beslenir ve
 * bunlar kare hızından sık gelebilir; tek boru hattı ikisini de aynı ucuza
 * bağlar. ScrollTrigger olayları için eklenen gecikme ≤1 kare (scrub 0.5'te
 * görünmez).
 *
 * Katmanlar (cinema-core):
 *   off  → hiç enjekte edilmez.
 *   lite → sadeleştirilmiş: TEK grup halinde çizim (sıralı pencere yok),
 *          detay çizgileri (kaynak dikişleri, süspansiyon körükleri, örgü
 *          basamaklar vb.) atlanır, etiket/ölçü/kaynak-noktası/parıltı yok;
 *          pafta + ana hatlar + aynı sönüş korunur.
 *   full → tam koreografi.
 *
 * Tembel kurulum: SVG yalnızca bölüm yaklaşınca (BarlasCinema.lazy, %50
 * marj) inşa edilir; ilk boyamaya maliyeti sıfırdır.
 *
 * 3D'siz yedek: yeni-tanker 3D kuramadıysa (.road--3d yok, olay da yok)
 * ilerleme, bölümün görüş alanından geçişinden hesaplanır (rect tabanlı);
 * çizim statik yedek görselin üzerinde de çalışır.
 *
 * Klasik IIFE + defer (hero-static.js kalıbı). Bağımlılık: cinema-core.js.
 */
(function () {
    'use strict';

    var SVG_NS = 'http://www.w3.org/2000/svg';

    /* ---------------- küçük yardımcılar ---------------- */

    function clamp01(v) { return v < 0 ? 0 : (v > 1 ? 1 : v); }
    function smooth(a, b, x) { var t = clamp01((x - a) / (b - a)); return t * t * (3 - 2 * t); }

    /* SVG öğesi üret: nitelikler + sınıf. */
    function make(name, attrs, cls) {
        var el = document.createElementNS(SVG_NS, name);
        if (attrs) {
            for (var k in attrs) {
                if (Object.prototype.hasOwnProperty.call(attrs, k)) {
                    el.setAttribute(k, attrs[k]);
                }
            }
        }
        if (cls) el.setAttribute('class', cls);
        return el;
    }

    /* Çizilen şekil: pathLength=1 → dashoffset ile tekdüze çizim; ölçekten
       bağımsız kalın çizgi için non-scaling-stroke. */
    function makeDrawable(spec) {
        var el;
        if (spec.k === 'c') {
            el = make('circle', { cx: spec.cx, cy: spec.cy, r: spec.r });
        } else if (spec.k === 'r') {
            el = make('rect', { x: spec.x, y: spec.y, width: spec.w, height: spec.h, rx: spec.rx || 0 });
        } else {
            el = make('path', { d: spec.d });
        }
        el.setAttribute('pathLength', '1');
        el.setAttribute('vector-effect', 'non-scaling-stroke');
        return el;
    }

    /* ---------------- geometri: tanker yarı römorku, yandan ---------------- */
    /* viewBox 0 0 960 420; ön (king pim) SOLDA. f:1 işaretli şekiller yalnız
       'full' katmanında kurulur (lite'ta detay yok). lead: kaynak noktasının
       izlediği "baş" şeklin dizindeki sırası. */

    var PARTS = [
        { /* 01 — şasi kirişleri + king pim + destek ayağı */
            w: 1.6, lead: 0, shapes: [
                { k: 'p', d: 'M 120 262 H 878' },
                { k: 'p', d: 'M 120 276 H 878' },
                { k: 'p', d: 'M 120 262 V 276' },
                { k: 'p', d: 'M 168 276 H 232' },
                { k: 'p', d: 'M 196 276 V 294' },
                { k: 'p', d: 'M 204 276 V 294' },
                { k: 'c', cx: 200, cy: 300, r: 6 },
                { k: 'p', d: 'M 326 276 V 344' },
                { k: 'p', d: 'M 340 276 V 344' },
                { k: 'p', d: 'M 314 344 H 352' },
                { k: 'p', d: 'M 340 300 L 396 276', f: 1 }
            ]
        },
        { /* 02 — dingil grubu: aks kirişi + 3 poyra + süspansiyon körükleri */
            w: 1.4, lead: 0, shapes: [
                { k: 'p', d: 'M 640 330 H 800' },
                { k: 'c', cx: 640, cy: 330, r: 9 },
                { k: 'c', cx: 720, cy: 330, r: 9 },
                { k: 'c', cx: 800, cy: 330, r: 9 },
                { k: 'r', x: 624, y: 296, w: 32, h: 16, rx: 3, f: 1 },
                { k: 'r', x: 704, y: 296, w: 32, h: 16, rx: 3, f: 1 },
                { k: 'r', x: 784, y: 296, w: 32, h: 16, rx: 3, f: 1 },
                { k: 'p', d: 'M 640 276 V 296', f: 1 },
                { k: 'p', d: 'M 720 276 V 296', f: 1 },
                { k: 'p', d: 'M 800 276 V 296', f: 1 }
            ]
        },
        { /* 03 — tekerlekler (çift çember) */
            w: 1.6, lead: 1, shapes: [
                { k: 'c', cx: 640, cy: 330, r: 42 },
                { k: 'c', cx: 720, cy: 330, r: 42 },
                { k: 'c', cx: 800, cy: 330, r: 42 },
                { k: 'c', cx: 640, cy: 330, r: 26 },
                { k: 'c', cx: 720, cy: 330, r: 26 },
                { k: 'c', cx: 800, cy: 330, r: 26 }
            ]
        },
        { /* 04 — tank silindiri: gövde + bombe dikişleri + kaynak dikişleri */
            w: 1.8, lead: 0, shapes: [
                { k: 'r', x: 100, y: 96, w: 780, h: 156, rx: 78 },
                { k: 'p', d: 'M 192 102 Q 176 174 192 246' },
                { k: 'p', d: 'M 788 102 Q 804 174 788 246' },
                { k: 'p', d: 'M 330 100 V 248', f: 1 },
                { k: 'p', d: 'M 470 100 V 248', f: 1 },
                { k: 'p', d: 'M 610 100 V 248', f: 1 }
            ]
        },
        { /* 05 — üstte adam giriş kapakları + vanalar, altta boşaltım */
            w: 1.3, lead: 0, shapes: [
                { k: 'p', d: 'M 272 96 A 28 18 0 0 1 328 96' },
                { k: 'p', d: 'M 288 80 H 312', f: 1 },
                { k: 'p', d: 'M 452 96 A 28 18 0 0 1 508 96' },
                { k: 'p', d: 'M 468 80 H 492', f: 1 },
                { k: 'p', d: 'M 654 96 V 74' },
                { k: 'p', d: 'M 666 96 V 74' },
                { k: 'c', cx: 660, cy: 70, r: 7 },
                { k: 'p', d: 'M 646 70 H 674', f: 1 },
                { k: 'p', d: 'M 754 252 V 268', f: 1 },
                { k: 'p', d: 'M 766 252 V 268', f: 1 },
                { k: 'c', cx: 760, cy: 272, r: 5, f: 1 }
            ]
        },
        { /* 06 — merdiven + arka tampon + çamurluklar */
            w: 1.3, lead: 11, shapes: [
                { k: 'p', d: 'M 846 148 V 264' },
                { k: 'p', d: 'M 864 148 V 264' },
                { k: 'p', d: 'M 846 164 H 864' },
                { k: 'p', d: 'M 846 188 H 864', f: 1 },
                { k: 'p', d: 'M 846 212 H 864' },
                { k: 'p', d: 'M 846 236 H 864', f: 1 },
                { k: 'p', d: 'M 846 256 H 864' },
                { k: 'p', d: 'M 884 300 V 356' },
                { k: 'p', d: 'M 852 356 H 902' },
                { k: 'p', d: 'M 884 316 L 852 356', f: 1 },
                { k: 'p', d: 'M 594 302 A 50 50 0 0 1 686 302' },
                { k: 'p', d: 'M 754 302 A 50 50 0 0 1 846 302' }
            ]
        }
    ];

    /* Ölçü çizgileri + numara etiketleri: dizin PARTS ile eşleşir; her grup
       kendi parçası çizimi bitince belirir. Metinler bilinçli olarak SAYI
       ("01".."06") — i18n gerektirmez. */
    var DIMS = [
        { d: ['M 250 270 L 210 322 L 186 322'], lx: 158, ly: 326, t: '01' },
        { d: ['M 720 342 L 640 398 L 616 398'], lx: 588, ly: 402, t: '02' },
        { d: ['M 772 362 L 736 402 L 712 402'], lx: 684, ly: 406, t: '03' },
        { d: [
            'M 100 88 V 60', 'M 880 88 V 60', 'M 100 66 H 880',
            'M 112 60 L 100 66 L 112 72', 'M 868 60 L 880 66 L 868 72',
            'M 92 96 H 64', 'M 92 252 H 64', 'M 70 96 V 252',
            'M 64 108 L 70 96 L 76 108', 'M 64 240 L 70 252 L 76 240'
        ], lx: 466, ly: 60, t: '04' },
        { d: ['M 480 76 L 524 42 L 548 42'], lx: 554, ly: 46, t: '05' },
        { d: ['M 888 292 L 916 264 L 934 264'], lx: 908, ly: 258, t: '06' }
    ];

    /* ---------------- koreografi pencereleri (p: 0..1) ---------------- */

    var SHEET_IN_END = 0.06;  /* pafta (ızgara + antet) belirir           */
    var DRAW_START   = 0.06;  /* parçalar sırayla çizilmeye başlar        */
    var DRAW_STRIDE  = 0.055; /* ardışık grup başlangıçları arası         */
    var DRAW_WIN     = 0.085; /* tek grubun çizim penceresi (hafif binme) */
    var DRAW_END     = 0.42;  /* lite'ta tek grubun bitişi                */
    var PULSE_A      = 0.42;  /* tamamlanma parıltısı başlangıcı          */
    var PULSE_B      = 0.55;
    var FADE_A       = 0.55;  /* katman sönmeye başlar (3D devralır)      */
    var FADE_B       = 0.72;  /* bu noktadan sonra katman tamamen gizli   */

    /* ---------------- pafta (drawing sheet) kurulumu ---------------- */

    function buildSheet(svg) {
        var defs = make('defs');
        var pat = make('pattern', {
            id: 'bp-grid-pat', width: 48, height: 48, patternUnits: 'userSpaceOnUse'
        });
        pat.appendChild(make('path', { d: 'M 48 0 H 0 V 48', 'stroke-width': 1 }, 'bp-grid-line'));
        defs.appendChild(pat);
        svg.appendChild(defs);

        var sheet = make('g', { 'stroke-width': 1 }, 'bp-sheet');

        /* Izgara dolgusu inline style ile — .bp-sheet rect{fill:none} kuralını
           inline stil her zaman yener. */
        var grid = make('rect', { x: 14, y: 10, width: 932, height: 400, stroke: 'none' });
        grid.style.fill = 'url(#bp-grid-pat)';
        sheet.appendChild(grid);

        /* Pafta çerçevesi + zemin çizgisi + tank eksen çizgisi (dash-dot) */
        sheet.appendChild(make('rect', { x: 14, y: 10, width: 932, height: 400 }));
        sheet.appendChild(make('path', { d: 'M 60 372 H 906', 'stroke-dasharray': '7 5' }));
        sheet.appendChild(make('path', { d: 'M 84 174 H 902', 'stroke-dasharray': '12 5 2 5' }));

        /* Antet (title block) — sağ üst köşe, gerçek pafta gibi bölmeli. */
        sheet.appendChild(make('rect', { x: 702, y: 14, width: 236, height: 42 }));
        sheet.appendChild(make('path', { d: 'M 702 36 H 938' }));
        sheet.appendChild(make('path', { d: 'M 838 14 V 56' }));

        var t1 = make('text', { x: 714, y: 31 }, 'bp-title');
        t1.textContent = 'BARLAS';
        var t2 = make('text', { x: 714, y: 50 }, 'bp-small');
        t2.textContent = '076-136';
        var t3 = make('text', { x: 850, y: 31 }, 'bp-small');
        t3.textContent = '1:50';
        var t4 = make('text', { x: 850, y: 50 }, 'bp-small');
        t4.textContent = '2026';
        sheet.appendChild(t1); sheet.appendChild(t2);
        sheet.appendChild(t3); sheet.appendChild(t4);

        svg.appendChild(sheet);
    }

    /* ---------------- katman kurulumu ---------------- */

    function build(section, frame, C) {
        var full = C.full;

        var layer = document.createElement('div');
        layer.className = 'bp-layer';
        layer.setAttribute('aria-hidden', 'true');

        var svg = make('svg', { viewBox: '0 0 960 420', focusable: 'false' });
        buildSheet(svg);

        /* Çizilen gruplar: full'de 6 sıralı parça; lite'ta tüm ana hatlar
           TEK grupta (detaysız) tek pencerede çizilir. */
        var groups = [];
        var i, j, spec, g, meta;

        if (full) {
            for (i = 0; i < PARTS.length; i++) {
                g = make('g', { 'stroke-width': PARTS[i].w }, 'bp-part');
                meta = {
                    el: g,
                    a: DRAW_START + i * DRAW_STRIDE,
                    b: DRAW_START + i * DRAW_STRIDE + DRAW_WIN,
                    lead: null, leadLen: 0, lastOff: ''
                };
                for (j = 0; j < PARTS[i].shapes.length; j++) {
                    spec = PARTS[i].shapes[j];
                    var shapeEl = makeDrawable(spec);
                    g.appendChild(shapeEl);
                    if (j === PARTS[i].lead) meta.lead = shapeEl;
                }
                svg.appendChild(g);
                groups.push(meta);
            }
        } else {
            g = make('g', { 'stroke-width': 1.5 }, 'bp-part');
            for (i = 0; i < PARTS.length; i++) {
                for (j = 0; j < PARTS[i].shapes.length; j++) {
                    spec = PARTS[i].shapes[j];
                    if (spec.f) continue; /* detay çizgileri lite'ta yok */
                    g.appendChild(makeDrawable(spec));
                }
            }
            svg.appendChild(g);
            groups.push({ el: g, a: DRAW_START, b: DRAW_END, lead: null, leadLen: 0, lastOff: '' });
        }

        /* Ölçüler + etiketler + kaynak noktası: yalnız full. */
        var dims = [];
        var ember = null;
        if (full) {
            for (i = 0; i < DIMS.length; i++) {
                g = make('g', { 'stroke-width': 1 }, 'bp-dimg');
                for (j = 0; j < DIMS[i].d.length; j++) {
                    g.appendChild(make('path', { d: DIMS[i].d[j], 'vector-effect': 'non-scaling-stroke' }));
                }
                var label = make('text', { x: DIMS[i].lx, y: DIMS[i].ly }, 'bp-label');
                label.textContent = DIMS[i].t;
                g.appendChild(label);
                g.style.opacity = '0';
                svg.appendChild(g);
                /* Etiket, kendi parçasının çizimi biterken belirir. */
                dims.push({ el: g, a: groups[i].b - 0.015, b: groups[i].b + 0.025, last: '' });
            }

            ember = make('g');
            ember.appendChild(make('circle', { cx: 0, cy: 0, r: 8 }, 'bp-ember-halo'));
            ember.appendChild(make('circle', { cx: 0, cy: 0, r: 3.5 }, 'bp-ember-core'));
            ember.style.opacity = '0';
            svg.appendChild(ember);
        }

        layer.appendChild(svg);

        /* .road__head'den ÖNCE ekle: eşit z-index (2) durumunda DOM'da sonra
           gelen başlık/altyazılar çizimin üstünde boyanır. */
        frame.insertBefore(layer, frame.querySelector('.road__head'));

        /* Parıltı rengi bir kez okunur (tema değişiminde eski kalabilir —
           kabul edilen küçük taviz, her karede stil okumaktan ucuz). */
        var glowColor = '#4f8dff';
        try {
            var gc = getComputedStyle(document.documentElement).getPropertyValue('--cine-glow').trim();
            if (gc) glowColor = gc;
        } catch (e) { /* varsayılan kalsın */ }

        /* ---------------- durum uygulayıcı: p'nin saf fonksiyonu ---------------- */
        /* Önbellekler (lastOff, lastVis vb.) yalnız DOM yazımını kısar; görsel
           durumun kendisi her zaman p'den yeniden türetilir → geri sarma doğru. */

        var lastVis = null, lastOpacity = '', lastScale = '', lastFilter = null;

        function apply(p) {
            p = clamp01(p);

            var vis = p > 0.0005 && p < FADE_B;
            if (vis !== lastVis) {
                layer.style.visibility = vis ? 'visible' : 'hidden';
                lastVis = vis;
            }
            if (!vis) return; /* gizliyken pin'in kalanı bedava */

            var fade = smooth(FADE_A, FADE_B, p);

            var op = (smooth(0, SHEET_IN_END, p) * (1 - fade)).toFixed(3);
            if (op !== lastOpacity) { layer.style.opacity = op; lastOpacity = op; }

            var sc = (1 + 0.04 * fade).toFixed(4);
            if (sc !== lastScale) { svg.style.transform = 'scale(' + sc + ')'; lastScale = sc; }

            /* Parça çizimleri: dashoffset 1.02→0 (0.02 payı, yuvarlak uçlu
               dash'in başlangıçta nokta bırakmasını önler). */
            var activeIdx = -1, activeT = 0;
            for (var k = 0; k < groups.length; k++) {
                var t = smooth(groups[k].a, groups[k].b, p);
                var off = ((1 - t) * 1.02).toFixed(4);
                if (off !== groups[k].lastOff) {
                    groups[k].el.style.strokeDashoffset = off;
                    groups[k].lastOff = off;
                }
                if (t > 0.001 && t < 0.999) { activeIdx = k; activeT = t; }
            }

            /* Etiket + ölçü belirmesi: opaklık + 2px yükselme. */
            for (var m = 0; m < dims.length; m++) {
                var lt = smooth(dims[m].a, dims[m].b, p);
                var key = lt.toFixed(3);
                if (key !== dims[m].last) {
                    dims[m].el.style.opacity = key;
                    dims[m].el.style.transform = 'translateY(' + ((1 - lt) * 2).toFixed(2) + 'px)';
                    dims[m].last = key;
                }
            }

            /* Kaynak noktası: aktif çizilen grubun baş şekli üzerinde ilerler.
               Olay yayını zaten ≥0.002 adım — ek kısma gerekmez. */
            if (ember) {
                if (activeIdx >= 0 && groups[activeIdx].lead &&
                        typeof groups[activeIdx].lead.getTotalLength === 'function') {
                    try {
                        if (!groups[activeIdx].leadLen) {
                            groups[activeIdx].leadLen = groups[activeIdx].lead.getTotalLength();
                        }
                        var pt = groups[activeIdx].lead.getPointAtLength(activeT * groups[activeIdx].leadLen);
                        ember.setAttribute('transform',
                            'translate(' + pt.x.toFixed(1) + ' ' + pt.y.toFixed(1) + ')');
                        ember.style.opacity = '1';
                    } catch (e) {
                        ember.style.opacity = '0';
                    }
                } else {
                    ember.style.opacity = '0';
                }
            }

            /* Tamamlanma parıltısı: 0.42–0.55 arasında tek yumuşak nabız. */
            if (full) {
                var pulse = Math.sin(Math.PI * smooth(PULSE_A, PULSE_B, p));
                var filt = pulse > 0.02
                    ? 'drop-shadow(0 0 ' + (3 + pulse * 11).toFixed(1) + 'px ' + glowColor + ')'
                    : '';
                if (filt !== lastFilter) { svg.style.filter = filt; lastFilter = filt; }
            }
        }

        /* ---------------- ilerleme sürücüleri ---------------- */
        /* Birincil: 'barlas:road-progress' (3D + pin yolunda garanti).
           Yedek: 3D kurulamadıysa olay hiç gelmez → bölümün görüş alanından
           geçişi ilerleme sayılır. İlk gerçek olay geldiği anda yedek kalıcı
           olarak devreden çıkar (bu bir SÜRÜCÜ seçimi bayrağıdır, görsel
           durum bayrağı değil — görsel her zaman p'den türetilir). */

        var pending = 0, dirty = false, eventSeen = false;

        function schedule(p) {
            pending = p;
            if (dirty) return;
            dirty = true;
            window.requestAnimationFrame(function () {
                dirty = false;
                apply(pending);
            });
        }

        function rectProgress() {
            var r = section.getBoundingClientRect();
            var vh = window.innerHeight || 1;
            return clamp01((vh - r.top) / (vh + Math.max(1, r.height)));
        }

        function onScrollFallback() {
            if (eventSeen) return;
            schedule(rectProgress());
        }

        section.addEventListener('barlas:road-progress', function (e) {
            if (!eventSeen) {
                eventSeen = true;
                window.removeEventListener('scroll', onScrollFallback);
                window.removeEventListener('resize', onScrollFallback);
            }
            schedule(e.detail && typeof e.detail.progress === 'number' ? e.detail.progress : 0);
        });

        /* 3D yolu bekleniyorsa (road--3d sınıfı ya da gerekli kütüphaneler
           mevcutsa) rect yedeğini HİÇ kurma: pin sırasında rect ilerlemesi
           yanlış ölçek verir ve olaylarla çakışırdı. */
        var expectEvents = section.classList.contains('road--3d') ||
            !!(window.gsap && window.ScrollTrigger && window.THREE);
        if (!expectEvents) {
            window.addEventListener('scroll', onScrollFallback, { passive: true });
            window.addEventListener('resize', onScrollFallback, { passive: true });
        }

        /* Başlangıç durumu: sayfa pin ortasında yenilendiyse --road-p inline
           stilde hazırdır; yoksa yedek/0 ile başla. */
        var p0 = parseFloat(section.style.getPropertyValue('--road-p'));
        if (!isFinite(p0)) p0 = expectEvents ? 0 : rectProgress();
        apply(p0);
    }

    /* ---------------- önyükleme ---------------- */

    function boot() {
        var C = window.BarlasCinema;
        if (!C) return;
        if (C.tier === 'off') return; /* reduced-motion: hiç enjekte etme */

        var section = document.querySelector('[data-road]');
        if (!section) return;
        var frame = section.querySelector('.road__frame');
        if (!frame) return;

        /* Bölüm yaklaşınca kur — ilk boyama maliyeti sıfır. */
        C.lazy(section, function () {
            try { build(section, frame, C); }
            catch (err) {
                if (window.console && console.error) console.error('[bp-blueprint]', err);
            }
        }, '50% 0px');
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot, { once: true });
    } else {
        boot();
    }
})();
