/**
 * cinema-scrub.js — scroll-scrub video / kare-dizisi motoru (v3.x, referans stil)
 * =============================================================================
 * "Apple ürün sayfası" kalıbı: bölüm pinlenir, scroll ilerledikçe önceden
 * üretilmiş sinematik görüntü (MP4 ya da WebP kare dizisi) İLERİ/GERİ sarılır.
 * Ürün sahnede sabit kalır, etrafındaki HTML metin katmanları faz faz görünür.
 *
 * KULLANIM — video modu:
 *   <section class="cine-scrub" data-cine-scrub data-cine-scrub-length="160">
 *     <div class="cine-scrub__media">
 *       <video data-cine-scrub-video muted playsinline preload="none"
 *              src="<?= $asset('assets/video/tanker-hero.mp4') ?>"
 *              poster="<?= $asset('assets/video/tanker-hero-poster.webp') ?>"></video>
 *     </div>
 *     <div class="cine-scrub__copy">
 *       <div class="cine-scrub__phase" data-phase="0.00 0.28"> ...başlık 1... </div>
 *       <div class="cine-scrub__phase" data-phase="0.30 0.62"> ...başlık 2... </div>
 *       <div class="cine-scrub__phase" data-phase="0.66 1.00"> ...CTA... </div>
 *     </div>
 *   </section>
 *
 * KULLANIM — kare dizisi modu (daha akıcı scrub, iri MP4 seek'inden bağımsız):
 *   <section data-cine-scrub data-cine-scrub-frames="assets/seq/tanker/f_%04d.webp"
 *            data-cine-scrub-count="120"> <canvas data-cine-scrub-canvas></canvas> ...
 *
 * Notlar:
 *   - data-cine-scrub-length: pin uzunluğu, viewport yüzdesi (varsayılan 150).
 *   - Video H.264, sık keyframe'li encode edilmeli (ffmpeg: -g 12) — geri sarma
 *     ancak sık keyframe'le pürüzsüz olur. Ses zaten kullanılmaz (muted).
 *   - Tier 'off' (reduced-motion): pin YOK, poster/ilk kare statik durur,
 *     tüm metin fazları görünür (içerik kaybı yok).
 *   - Sekme gizlenince döngü durur (BarlasCinema.loop). Metadata, bölüm
 *     görünüme yaklaşınca yüklenir (lazy) — ilk boyaya maliyeti sıfır.
 *   - Bölüm 'barlas:scrub-progress' olayı yayınlar (detail.progress) — başka
 *     katmanlar (ör. sayaçlar) senkron olabilir.
 */
(function () {
    'use strict';

    function clamp01(v) { return v < 0 ? 0 : (v > 1 ? 1 : v); }
    function lerp(a, b, t) { return a + (b - a) * t; }

    function boot() {
        var C = window.BarlasCinema;
        if (!C) return;

        var sections = Array.prototype.slice.call(document.querySelectorAll('[data-cine-scrub]'));
        if (!sections.length) return;

        var gsap = window.gsap, ST = window.ScrollTrigger;

        sections.forEach(function (section) {
            /* Statik yol: hareket yok ya da GSAP eksik → poster + tüm metinler */
            if (C.tier === 'off' || !gsap || !ST) {
                section.classList.add('cine-scrub--static');
                return;
            }
            gsap.registerPlugin(ST);
            initSection(section, C, gsap, ST);
        });
    }

    function initSection(section, C, gsap, ST) {
        var video  = section.querySelector('[data-cine-scrub-video]');
        var canvas = section.querySelector('[data-cine-scrub-canvas]');
        var frameTpl   = section.getAttribute('data-cine-scrub-frames');
        var frameCount = parseInt(section.getAttribute('data-cine-scrub-count') || '0', 10);
        var length     = parseInt(section.getAttribute('data-cine-scrub-length') || '150', 10);

        var mode = (canvas && frameTpl && frameCount > 0) ? 'frames'
                 : (video ? 'video' : null);
        if (!mode) { section.classList.add('cine-scrub--static'); return; }

        /* ---------------- metin fazları ---------------- */
        var phases = Array.prototype.slice.call(section.querySelectorAll('[data-phase]'))
            .map(function (el) {
                var p = (el.getAttribute('data-phase') || '0 1').trim().split(/\s+/);
                return { el: el, a: parseFloat(p[0]) || 0, b: parseFloat(p[1]) || 1 };
            });
        function updatePhases(p) {
            for (var i = 0; i < phases.length; i++) {
                var ph = phases[i];
                ph.el.classList.toggle('is-on', p >= ph.a && p <= ph.b);
            }
        }

        /* ---------------- ilerleme (pin + scrub) ---------------- */
        var target = 0, current = 0, lastEmit = -1;
        section.classList.add('cine-scrub--ready');
        ST.create({
            trigger: section,
            start: 'top top',
            end: '+=' + length + '%',
            pin: true,
            anticipatePin: 1,
            scrub: true,
            onUpdate: function (self) { target = self.progress; }
        });
        ST.refresh();

        /* Bölüm ekranda mı? (pin payından bağımsız gerçek görünürlük) */
        var active = false;
        if ('IntersectionObserver' in window) {
            new IntersectionObserver(function (es) {
                active = es[0].isIntersecting;
            }, { threshold: 0 }).observe(section);
        } else { active = true; }

        /* ---------------- medya sürücüleri ---------------- */
        var driver = (mode === 'video')
            ? makeVideoDriver(video, C)
            : makeFramesDriver(canvas, frameTpl, frameCount, C);

        /* Medya yüklemesi bölüme yaklaşınca başlar (ilk boyaya maliyet yok) */
        C.lazy(section, function () { driver.load(); }, '80% 0px');

        /* Ana döngü: hedefe yumuşak yaklaş (lerp) → seek maliyeti düzleşir */
        C.loop(function () {
            if (!active) return;
            current = lerp(current, target, 0.16);
            if (Math.abs(current - target) < 0.0005) current = target;

            driver.seek(clamp01(current));
            updatePhases(current);

            if (Math.abs(current - lastEmit) > 0.004) {
                lastEmit = current;
                section.dispatchEvent(new CustomEvent('barlas:scrub-progress', {
                    bubbles: true, detail: { progress: current }
                }));
            }
        });
    }

    /* ---------------- video sürücüsü ---------------- */
    function makeVideoDriver(video, C) {
        var duration = 0, ready = false, pending = false;
        var MIN_STEP = 1 / 30; /* sn — bundan küçük seek'ler atlanır */

        video.muted = true;
        video.playsInline = true;

        function load() {
            if (ready || pending) return;
            pending = true;
            video.preload = 'auto';
            if (video.readyState >= 1 && video.duration) {
                duration = video.duration; ready = true; return;
            }
            video.addEventListener('loadedmetadata', function () {
                duration = video.duration || 0; ready = true;
            }, { once: true });
            try { video.load(); } catch (e) { /* zaten yükleniyor olabilir */ }
        }

        function seek(p) {
            if (!ready || !duration) return;
            /* seek hâlâ sürüyorsa üstüne binme (özellikle Safari) */
            if (video.seeking) return;
            var t = p * Math.max(0, duration - 0.05);
            if (Math.abs(t - video.currentTime) < MIN_STEP) return;
            try { video.currentTime = t; } catch (e) { /* metadata henüz yoksa */ }
        }

        return { load: load, seek: seek };
    }

    /* ---------------- kare dizisi sürücüsü ---------------- */
    function makeFramesDriver(canvas, tpl, count, C) {
        var ctx = canvas.getContext('2d');
        var frames = new Array(count);   /* Image ya da undefined */
        var loadedFlags = new Array(count);
        var started = false, lastDrawn = -1;

        function urlFor(i) {
            /* %04d benzeri kalıbı doldur (1 tabanlı kare numarası) */
            return tpl.replace(/%0(\d)d/, function (_, w) {
                return String(i + 1).padStart(parseInt(w, 10), '0');
            });
        }

        function loadFrame(i, cb) {
            if (loadedFlags[i]) { if (cb) cb(); return; }
            var img = new Image();
            img.decoding = 'async';
            img.onload = function () { frames[i] = img; loadedFlags[i] = true; if (cb) cb(); };
            img.src = urlFor(i);
        }

        function load() {
            if (started) return;
            started = true;
            fitCanvas();
            /* Önce iskelet (her 6. kare) → ilk kare çizilir; sonra araları doldur */
            var step = 6;
            for (var i = 0; i < count; i += step) loadFrame(i, i === 0 ? function () { draw(0); } : null);
            window.setTimeout(function () {
                for (var j = 0; j < count; j++) loadFrame(j, null);
            }, 800);
            window.addEventListener('resize', fitCanvas);
        }

        function fitCanvas() {
            var r = canvas.getBoundingClientRect();
            var dpr = Math.min(window.devicePixelRatio || 1, C.tier === 'lite' ? 1.25 : 2);
            canvas.width  = Math.max(1, Math.round(r.width * dpr));
            canvas.height = Math.max(1, Math.round(r.height * dpr));
            lastDrawn = -1; /* yeniden çizmeye zorla */
        }

        /* p'ye en yakın YÜKLÜ kareyi bul (yüklenmemişse geriye tara) */
        function nearestLoaded(idx) {
            for (var d = 0; d < count; d++) {
                if (idx - d >= 0 && loadedFlags[idx - d]) return idx - d;
                if (idx + d < count && loadedFlags[idx + d]) return idx + d;
            }
            return -1;
        }

        function draw(idx) {
            var img = frames[idx];
            if (!img) return;
            /* object-fit: cover eşdeğeri */
            var cw = canvas.width, ch = canvas.height;
            var s = Math.max(cw / img.naturalWidth, ch / img.naturalHeight);
            var w = img.naturalWidth * s, h = img.naturalHeight * s;
            ctx.clearRect(0, 0, cw, ch);
            ctx.drawImage(img, (cw - w) / 2, (ch - h) / 2, w, h);
            lastDrawn = idx;
        }

        function seek(p) {
            if (!started) return;
            var idx = nearestLoaded(Math.round(p * (count - 1)));
            if (idx >= 0 && idx !== lastDrawn) draw(idx);
        }

        return { load: load, seek: seek };
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot, { once: true });
    } else {
        boot();
    }
})();
