/**
 * Barlas Trailer — yeni arayüz davranış katmanı
 * CodeIgniter ana sayfası (layouts/yeni.php) tarafından yüklenir.
 *
 * A) Etkileşim (her zaman): mobil menü, hotspot kartları, AI konsol
 *    demosu, sayaçlar.
 * B) Hareket (gsap + ScrollTrigger varsa, reduced-motion kapalıysa):
 *    Lenis smooth scroll, hero girişi, reveal'lar, sticky "Neden Barlas"
 *    kart ölçeklemesi, galeri parallax'ı, süreç çizgisi, header durumu.
 *
 * Başlangıçta gizlenen tüm durumlar html.has-anim altındadır; hareket
 * katmanı çalışmazsa içerik daima görünür kalır.
 */
(function () {
    'use strict';

    function onReady(fn) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fn, { once: true });
        } else {
            fn();
        }
    }

    onReady(function () {
        initInteractions();
        window.setTimeout(function () {
            if (!window.gsap || !window.ScrollTrigger) return;
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
            try {
                initMotion();
            } catch (err) {
                document.documentElement.classList.remove('has-anim');
                if (window.console && console.error) console.error('[barlas]', err);
            }
        }, 0);
    });

    /* ==================================================================
       A) ETKİLEŞİM
       ================================================================== */
    function initInteractions() {
        initMenu();
        initSpots();
        initCounters();
        initAiConsole();
        initGalleryReveal();
        initCursor();
    }

    /* Tekerlek imleç: imleç her zaman bir treyler tekerleğidir
       (lastik + jant + jant telleri + göbek). Yatay hareket ve scroll
       ile yol alıyormuş gibi döner; link/buton üzerinde büyüyüp serbest
       döner. Dokunmatikte ve reduced-motion'da hiç kurulmaz. */
    function initCursor() {
        if (!window.matchMedia('(hover: hover) and (pointer: fine)').matches) return;
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

        var el = document.createElement('div');
        el.className = 'cursor';
        el.setAttribute('aria-hidden', 'true');
        el.innerHTML =
            '<svg class="cursor__wheel" viewBox="0 0 56 56">' +
            '<circle class="cursor__tire" cx="28" cy="28" r="24"></circle>' +
            '<circle class="cursor__rim" cx="28" cy="28" r="17.5"></circle>' +
            '<g class="cursor__spokes">' +
            '<line x1="28" y1="11" x2="28" y2="45"></line>' +
            '<line x1="13.28" y1="19.5" x2="42.72" y2="36.5"></line>' +
            '<line x1="13.28" y1="36.5" x2="42.72" y2="19.5"></line>' +
            '<circle cx="28" cy="28" r="8"></circle>' +
            '</g>' +
            '<circle class="cursor__hub" cx="28" cy="28" r="3"></circle>' +
            '</svg>';
        document.body.appendChild(el);
        document.documentElement.classList.add('has-cursor');

        var wheel = el.querySelector('.cursor__wheel');
        var seen = false;
        var hover = false;
        var angle = 0;
        var lastX = 0;
        var lastScroll = window.scrollY || 0;

        /* Tekerlek imleci anında izler (gecikme yok: hassasiyet) */
        document.addEventListener('mousemove', function (e) {
            if (!seen) {
                seen = true;
                lastX = e.clientX;
                el.classList.add('is-on');
            }
            /* Yatayda gezerken yuvarlanır: çevre = 2πr (görünür yarıçap ~17px) */
            angle += (e.clientX - lastX) * (180 / (Math.PI * 17));
            lastX = e.clientX;
            el.style.transform = 'translate(' + e.clientX + 'px,' + e.clientY + 'px)';
        }, { passive: true });

        /* Dönüş döngüsü: scroll = yol alma, hover = serbest dönüş */
        (function raf() {
            var s = window.scrollY || 0;
            angle += (s - lastScroll) * 0.45;
            lastScroll = s;
            if (hover) angle += 2.2;
            wheel.style.rotate = (angle % 360) + 'deg';
            window.requestAnimationFrame(raf);
        })();

        var HOT = 'a, button, [role="button"], [data-spot], .btn, summary, label';
        var FIELD = 'input, textarea, select, [contenteditable="true"]';

        document.addEventListener('mouseover', function (e) {
            var t = e.target;
            var overField = !!(t.closest && t.closest(FIELD));
            el.classList.toggle('is-hidden', overField);
            hover = !overField && !!(t.closest && t.closest(HOT));
            el.classList.toggle('is-hover', hover);
        });

        document.addEventListener('mousedown', function () { el.classList.add('is-down'); });
        document.addEventListener('mouseup', function () { el.classList.remove('is-down'); });

        /* Pencereden çıkınca gizle, dönünce göster */
        document.addEventListener('mouseleave', function () { el.classList.add('is-hidden'); });
        document.addEventListener('mouseenter', function () { el.classList.remove('is-hidden'); });
    }

    /* Galeri: kareler ekrana girerken hafifçe alttan/üstten kayarak ve
       solarak yerine oturur. GSAP'a bağımlı DEĞİLDİR — IntersectionObserver
       + CSS geçişiyle çalışır; kütüphaneler yüklenmese bile animasyon olur.
       Gizleme sınıfı (g-anim) yalnızca gözlemci kurulabilirse eklenir,
       bu yüzden hiçbir durumda kareler görünmez kalmaz. */
    function initGalleryReveal() {
        var items = document.querySelectorAll('.gallery__item');
        if (!items.length) return;
        if (!('IntersectionObserver' in window)) return;
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

        document.documentElement.classList.add('g-anim');

        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                io.unobserve(entry.target);
                entry.target.classList.add('is-in');
            });
        }, { rootMargin: '0px 0px -12% 0px' });

        items.forEach(function (item, i) {
            if (i % 2) item.classList.add('gallery__item--up');
            item.style.transitionDelay = (i % 3) * 90 + 'ms';
            io.observe(item);
        });
    }

    function initMenu() {
        var toggle = document.querySelector('[data-nav-toggle]');
        var nav = document.querySelector('[data-nav]');
        if (!toggle || !nav) return;

        toggle.addEventListener('click', function () {
            var open = nav.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            toggle.setAttribute('aria-label', open ? 'Menüyü kapat' : 'Menüyü aç');
        });
        nav.addEventListener('click', function (e) {
            if (e.target.closest('a')) {
                nav.classList.remove('is-open');
                toggle.setAttribute('aria-expanded', 'false');
            }
        });
    }

    function initSpots() {
        var spots = Array.prototype.slice.call(document.querySelectorAll('[data-spot]'));
        if (!spots.length) return;

        function closeAll(except) {
            spots.forEach(function (s) {
                if (s !== except) s.setAttribute('aria-expanded', 'false');
            });
        }
        spots.forEach(function (spot) {
            spot.addEventListener('click', function () {
                var open = spot.getAttribute('aria-expanded') === 'true';
                closeAll(spot);
                spot.setAttribute('aria-expanded', open ? 'false' : 'true');
            });
        });
        document.addEventListener('click', function (e) {
            if (!e.target.closest('[data-spot]')) closeAll(null);
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeAll(null);
        });
    }

    function initCounters() {
        var nums = Array.prototype.slice.call(document.querySelectorAll('[data-count]'));
        if (!nums.length) return;

        var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        var fmt = new Intl.NumberFormat('tr-TR');

        function finish(el, target) { el.textContent = fmt.format(target); }

        if (reduce || !('IntersectionObserver' in window)) {
            nums.forEach(function (el) { finish(el, Number(el.dataset.count || 0)); });
            return;
        }

        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                io.unobserve(entry.target);

                var el = entry.target;
                var target = Number(el.dataset.count || 0);
                var dur = 1400;
                var t0 = null;

                function tick(t) {
                    if (t0 === null) t0 = t;
                    var p = Math.min(1, (t - t0) / dur);
                    var eased = 1 - Math.pow(1 - p, 3); /* ease-out cubic */
                    el.textContent = fmt.format(Math.round(target * eased));
                    if (p < 1) window.requestAnimationFrame(tick);
                }
                window.requestAnimationFrame(tick);
            });
        }, { threshold: 0.5 });

        nums.forEach(function (el) { io.observe(el); });
    }

    function initAiConsole() {
        var root = document.querySelector('[data-ai]');
        if (!root) return;

        var messages = root.querySelector('[data-ai-messages]');
        var form = root.querySelector('[data-ai-form]');
        var input = root.querySelector('[data-ai-input]');
        var demoResponse = root.dataset.demoResponse || '';
        var busy = false;

        var botAvatar = '<span class="msg__avatar" aria-hidden="true">' +
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">' +
            '<rect x="5" y="7" width="14" height="11" rx="4"></rect>' +
            '<path d="M9 11h.01M15 11h.01M9 15h6M12 4v3"></path></svg></span>';

        function append(html) {
            var el = document.createElement('div');
            el.innerHTML = html;
            var node = el.firstChild;
            messages.appendChild(node);
            messages.scrollTop = messages.scrollHeight;
            return node;
        }

        function esc(text) {
            var d = document.createElement('div');
            d.textContent = text;
            return d.innerHTML;
        }

        function send(text) {
            if (busy || !text.trim()) return;
            busy = true;

            append('<div class="msg msg--user"><p>' + esc(text.trim()) + '</p></div>');
            input.value = '';

            var typing = append('<div class="msg msg--bot msg--typing">' + botAvatar +
                '<p><i></i><i></i><i></i></p></div>');

            window.setTimeout(function () {
                typing.remove();
                append('<div class="msg msg--bot">' + botAvatar + '<p>' + esc(demoResponse) + '</p></div>');
                busy = false;
            }, 1100);
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            send(input.value);
        });

        root.querySelectorAll('[data-ai-example]').forEach(function (chip) {
            chip.addEventListener('click', function () {
                input.value = chip.textContent.trim();
                input.focus();
                send(input.value);
            });
        });
    }

    /* ==================================================================
       B) HAREKET
       ================================================================== */
    function initMotion() {
        var gsap = window.gsap;
        var ScrollTrigger = window.ScrollTrigger;
        gsap.registerPlugin(ScrollTrigger);

        var html = document.documentElement;
        html.classList.add('has-anim');

        /* Lenis smooth scroll (yalnızca hassas işaretçili cihazlar) */
        var lenis = null;
        if (window.Lenis && window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
            lenis = new window.Lenis({ duration: 1.15, smoothWheel: true });
            lenis.on('scroll', ScrollTrigger.update);
            gsap.ticker.add(function (t) { lenis.raf(t * 1000); });
            gsap.ticker.lagSmoothing(0);
        }

        /* Çapa bağlantıları: yumuşak kaydırma */
        document.querySelectorAll('a[href^="#"]').forEach(function (link) {
            link.addEventListener('click', function (e) {
                var target = document.querySelector(link.getAttribute('href'));
                if (!target) return;
                e.preventDefault();
                if (lenis) {
                    lenis.scrollTo(target, { offset: -64 });
                } else {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });

        /* Header durumu */
        var header = document.querySelector('[data-header]');
        if (header) {
            ScrollTrigger.create({
                start: 8,
                end: 'max',
                onToggle: function (self) { header.classList.toggle('is-scrolled', self.isActive); },
                onUpdate: function (self) { header.classList.toggle('is-scrolled', self.scroll() > 8); }
            });
        }

        /* Hero girişi: maskeli satırlar + öğeler */
        var lines = document.querySelectorAll('[data-hero-line]');
        var items = document.querySelectorAll('[data-hero-item]');
        var intro = gsap.timeline({ delay: 0.15 });
        intro.to(lines, { y: 0, duration: 0.95, stagger: 0.1, ease: 'power4.out' });
        intro.to(items, { opacity: 1, y: 0, duration: 0.6, stagger: 0.08, ease: 'power3.out' }, '-=0.55');

        /* Reveal primitifleri — IntersectionObserver ile SAĞLAM ve tekrarlanır.
           Gerçek ekran görünürlüğüne bakar; pinlenen 3B bölümün scroll matematiğini
           kaydırmasından etkilenmez → görünen her alan kesinlikle gösterilir.
           Ekran dışına çıkınca sıfırlanır, geri gelince yeniden oynar.
           Yapışkan (sticky) öğeler gösterildikten sonra gizlenmez (titremesin). */
        var revIO = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                var el = entry.target;
                var targets = el.hasAttribute('data-reveal-group')
                    ? Array.prototype.slice.call(el.children)
                    : [el];
                if (entry.isIntersecting) {
                    targets.forEach(function (t) { t.classList.add('is-in'); });
                } else if (!el.__revKeep) {
                    targets.forEach(function (t) { t.classList.remove('is-in'); });
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -10% 0px' });

        document.querySelectorAll('[data-reveal]').forEach(function (el) {
            el.__revKeep = getComputedStyle(el).position === 'sticky';
            revIO.observe(el);
        });
        document.querySelectorAll('[data-reveal-group]').forEach(function (group) {
            Array.prototype.slice.call(group.children).forEach(function (child, i) {
                child.style.transitionDelay = (i * 60) + 'ms';
            });
            revIO.observe(group);
        });

        /* Güvenlik ağı: bir şekilde gizli kalan görünür alan olursa, sayfa
           tamamen yüklendiğinde ekrandaki tüm reveal öğelerini görünür yap. */
        window.addEventListener('load', function () {
            var vh = window.innerHeight || document.documentElement.clientHeight;
            document.querySelectorAll('[data-reveal], [data-reveal-group]').forEach(function (el) {
                var r = el.getBoundingClientRect();
                if (r.top < vh && r.bottom > 0) {
                    if (el.hasAttribute('data-reveal-group')) {
                        Array.prototype.slice.call(el.children).forEach(function (c) { c.classList.add('is-in'); });
                    } else {
                        el.classList.add('is-in');
                    }
                }
            });
        });


        /* Süreç çizgisi: scroll ile çizilir, adımlar sırayla aktifleşir */
        var fill = document.querySelector('[data-process-fill]');
        var track = document.querySelector('[data-process]');
        if (fill && track) {
            gsap.to(fill, {
                scaleX: 1,
                scaleY: 1,
                ease: 'none',
                scrollTrigger: {
                    trigger: track,
                    start: 'top 78%',
                    end: 'bottom 45%',
                    scrub: 0.6,
                    onUpdate: function (self) {
                        var steps = track.querySelectorAll('[data-step]');
                        var active = Math.floor(self.progress * steps.length + 0.25);
                        steps.forEach(function (s, i) {
                            s.classList.toggle('is-active', i < Math.max(1, active));
                        });
                    }
                }
            });
        }

        /* ----------------------------------------------------------------
           Sinematik derinlik: bölümler arası yumuşak geçiş
           - <body> içine süzülen ışık katmanları (paralaks)
           - hero ve galeri katmanlarına scroll'a bağlı yumuşak sürüklenme
           Hepsi transform/opacity; reduced-motion'da initMotion hiç çalışmaz.
           ---------------------------------------------------------------- */
        var depthBg = document.createElement('div');
        depthBg.className = 'depth-bg';
        depthBg.setAttribute('aria-hidden', 'true');
        depthBg.innerHTML =
            '<span class="depth-bg__layer depth-bg__layer--a"></span>' +
            '<span class="depth-bg__layer depth-bg__layer--b"></span>' +
            '<span class="depth-bg__layer depth-bg__layer--c"></span>';
        document.body.appendChild(depthBg);

        var depthDrift = [-70, 100, -130];
        Array.prototype.forEach.call(depthBg.children, function (layer, i) {
            gsap.to(layer, {
                y: depthDrift[i] || 0,
                ease: 'none',
                scrollTrigger: { start: 0, end: 'max', scrub: 0.8 }
            });
        });

        /* Hero: arka ışık + sahne farklı hızlarda kayar (derinlik) */
        var heroHalo = document.querySelector('.hero__halo');
        if (heroHalo) {
            gsap.to(heroHalo, {
                yPercent: 28, ease: 'none',
                scrollTrigger: { trigger: '.hero', start: 'top top', end: 'bottom top', scrub: 0.8 }
            });
        }
        var heroStage = document.querySelector('.hero__stage');
        if (heroStage) {
            gsap.to(heroStage, {
                y: 60, ease: 'none',
                scrollTrigger: { trigger: '.hero', start: 'top top', end: 'bottom top', scrub: 0.8 }
            });
        }

        /* Galeri sütunları: data-g-speed'e göre farklı hızlarda paralaks */
        gsap.utils.toArray('[data-g-col]').forEach(function (col) {
            var sp = parseFloat(col.getAttribute('data-g-speed')) || 0;
            if (!sp) return;
            gsap.to(col, {
                y: sp * 9, ease: 'none',
                scrollTrigger: { trigger: '.gallery', start: 'top bottom', end: 'bottom top', scrub: 0.9 }
            });
        });

        window.dispatchEvent(new CustomEvent('barlas:motion-ready'));
        window.addEventListener('load', function () { ScrollTrigger.refresh(); });
    }
})();
