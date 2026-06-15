/**
 * flow.js — Cinematic scroll orchestration (plain deferred script)
 *
 * Owns the page-level motion story on the homepage:
 *   1. Lenis smooth scroll (desktop, fine pointers) wired into GSAP's ticker
 *   2. Kinetic hero intro (masked headline lines, staggered items)
 *   3. Hero pin: the opening holds for a short beat while the headline
 *      drifts up and the 3D tanker turns toward the road (tanker-3d.js)
 *   4. Categories: canonical horizontal-pan (pin + scrub) on desktop;
 *      native scroll-snap strip everywhere else
 *   5. Reveal primitives: [data-clip-reveal], [data-flow-stagger], CTA finale
 *   6. Process line scrubbed by scroll progress
 *   7. Header compact / hide-on-scroll-down state
 *
 * Progressive enhancement: requires gsap + ScrollTrigger; skips entirely on
 * prefers-reduced-motion (CSS keeps everything visible). Emits "flow:ready"
 * so tanker-3d.js builds its ScrollTriggers AFTER the pins exist.
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
        window.setTimeout(function () {
            if (!window.gsap || !window.ScrollTrigger) return;
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
            try {
                init();
            } catch (err) {
                // Never leave content hidden behind a failed enhancement.
                document.documentElement.classList.remove('has-flow');
                if (window.console && console.error) console.error('[flow]', err);
            }
        }, 0);
    });

    function init() {
        var gsap = window.gsap;
        var ScrollTrigger = window.ScrollTrigger;
        gsap.registerPlugin(ScrollTrigger);

        var html = document.documentElement;
        html.classList.add('has-flow');

        /* ----------------------------------------------------------------
           1. Smooth scroll (Lenis) — desktop fine-pointer only
           ---------------------------------------------------------------- */
        var lenis = null;
        var finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
        if (window.Lenis && finePointer) {
            lenis = new window.Lenis({
                duration: 1.05,
                smoothWheel: true,
                wheelMultiplier: 1,
                touchMultiplier: 1.4
            });
            lenis.on('scroll', ScrollTrigger.update);
            gsap.ticker.add(function (time) { lenis.raf(time * 1000); });
            gsap.ticker.lagSmoothing(0);
        }

        /* ----------------------------------------------------------------
           2. Hero intro — masked lines rise, items follow (one-shot)
           ---------------------------------------------------------------- */
        var hero = document.querySelector('.hero--cine');
        if (hero) {
            var lines = hero.querySelectorAll('[data-hero-line]');
            var items = hero.querySelectorAll('[data-hero-item]');

            var intro = gsap.timeline({ delay: 0.12 });
            intro.to(lines, {
                y: 0,
                duration: 0.9,
                stagger: 0.09,
                ease: 'power4.out',
                onComplete: function () { gsap.set(lines, { clearProps: 'willChange' }); }
            });
            intro.to(items, {
                opacity: 1,
                y: 0,
                duration: 0.6,
                stagger: 0.07,
                ease: 'power3.out'
            }, '-=0.55');

            /* Hero pin: a short held beat. The headline drifts up & fades a
               touch while the tanker (tanker-3d.js) turns toward the road. */
            ScrollTrigger.matchMedia({
                '(min-width: 992px)': function () {
                    var content = hero.querySelector('[data-hero-content]');
                    ScrollTrigger.create({
                        trigger: hero,
                        start: 'top top',
                        end: '+=55%',
                        pin: true,
                        anticipatePin: 1,
                        scrub: true
                    });
                    gsap.to(content, {
                        yPercent: -14,
                        opacity: 0.25,
                        ease: 'none',
                        scrollTrigger: {
                            trigger: hero,
                            start: 'top top',
                            end: '+=55%',
                            scrub: 0.6
                        }
                    });
                }
            });
        }

        /* ----------------------------------------------------------------
           3. Categories — horizontal pan (canonical skeleton)
           ---------------------------------------------------------------- */
        var catSection = document.querySelector('[data-cat-section]');
        var catTrack = document.querySelector('[data-cat-track]');
        if (catSection && catTrack) {
            ScrollTrigger.matchMedia({
                '(min-width: 992px)': function () {
                    var pan = gsap.to(catTrack, {
                        x: function () {
                            return -(catTrack.scrollWidth - window.innerWidth);
                        },
                        ease: 'none',
                        scrollTrigger: {
                            trigger: catSection,
                            start: 'top top',
                            end: function () {
                                return '+=' + (catTrack.scrollWidth - window.innerWidth);
                            },
                            pin: true,
                            scrub: 1,
                            anticipatePin: 1,
                            invalidateOnRefresh: true
                        }
                    });
                    return function () { pan.scrollTrigger && pan.scrollTrigger.kill(); };
                }
            });
        }

        /* ----------------------------------------------------------------
           4. Reveal primitives
           ---------------------------------------------------------------- */

        // Clip reveal (about media): once, when 30% visible
        var clipEls = document.querySelectorAll('[data-clip-reveal]');
        if (clipEls.length && 'IntersectionObserver' in window) {
            var clipIO = new IntersectionObserver(function (entries) {
                entries.forEach(function (e) {
                    if (e.isIntersecting) {
                        e.target.classList.add('is-revealed');
                        clipIO.unobserve(e.target);
                    }
                });
            }, { threshold: 0.3 });
            clipEls.forEach(function (el) { clipIO.observe(el); });
        }

        // Staggered child reveals (references wall, etc.)
        document.querySelectorAll('[data-flow-stagger]').forEach(function (group) {
            var children = Array.prototype.slice.call(group.children);
            children.forEach(function (child, i) {
                child.style.transitionDelay = (i * 55) + 'ms';
            });
            ScrollTrigger.create({
                trigger: group,
                start: 'top 82%',
                once: true,
                onEnter: function () {
                    children.forEach(function (child) { child.classList.add('is-in'); });
                }
            });
        });

        // CTA finale content
        var ctaInner = document.querySelector('[data-cta-inner]');
        if (ctaInner) {
            ScrollTrigger.create({
                trigger: ctaInner,
                start: 'top 80%',
                once: true,
                onEnter: function () { ctaInner.classList.add('is-in'); }
            });
        }

        /* ----------------------------------------------------------------
           5. Process line — scrubbed draw
           ---------------------------------------------------------------- */
        var processFill = document.querySelector('[data-process-fill]');
        var processTrack = document.querySelector('[data-process]');
        if (processFill && processTrack) {
            gsap.fromTo(processFill,
                { scaleX: 0 },
                {
                    scaleX: 1,
                    transformOrigin: html.dir === 'rtl' ? '100% 50%' : '0 50%',
                    ease: 'none',
                    scrollTrigger: {
                        trigger: processTrack,
                        start: 'top 78%',
                        end: 'bottom 45%',
                        scrub: 0.6
                    }
                });
        }

        /* ----------------------------------------------------------------
           6. Header state — compact when scrolled, hidden on scroll down
           ---------------------------------------------------------------- */
        var header = document.querySelector('.site-header');
        if (header) {
            ScrollTrigger.create({
                start: 0,
                end: 'max',
                onUpdate: function (self) {
                    var y = self.scroll();
                    header.classList.toggle('is-scrolled', y > 24);
                    // Only hide once well past the hero; always show near top
                    if (y > window.innerHeight && self.direction === 1) {
                        header.classList.add('is-hidden');
                    } else {
                        header.classList.remove('is-hidden');
                    }
                }
            });
        }

        /* ----------------------------------------------------------------
           7. Handshake + late refresh
           ---------------------------------------------------------------- */
        window.__flowReady = true;
        window.dispatchEvent(new CustomEvent('flow:ready'));

        window.addEventListener('load', function () {
            ScrollTrigger.refresh();
        });
    }
})();
