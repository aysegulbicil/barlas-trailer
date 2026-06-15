/**
 * hero-slider.js
 *
 * Auto-playing hero Swiper with crossfade + parallax. On every slide change
 * the active slide's content is animated in with a GSAP stagger for a
 * premium feel. Degrades gracefully: without Swiper the first slide simply
 * shows; without GSAP the content is visible (no hiding in CSS); with
 * reduced-motion, autoplay and entrance animations are disabled.
 */

export function initHeroSlider() {
    const el = document.querySelector('[data-hero-slider]');
    if (!el) return;

    hideMissingProductImages(el);
    if (!window.Swiper) return;

    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const progressBar = el.querySelector('[data-hero-progress]');

    const swiper = new window.Swiper(el, {
        effect: 'slide',
        parallax: false,
        speed: 800,
        watchSlidesProgress: true,
        preventInteractionOnTransition: true,
        loop: true,
        autoplay: reduceMotion ? false : { delay: 6000, disableOnInteraction: false },
        pagination: {
            el: el.querySelector('.hero__pagination'),
            clickable: true,
        },
        a11y: { enabled: true },
        on: {
            init: (sw) => {
                resetInactiveSlides(sw);
                animateActiveSlide(sw, reduceMotion);
            },
            slideChangeTransitionStart: (sw) => {
                resetInactiveSlides(sw);
                animateActiveSlide(sw, reduceMotion);
            },
            // Drive the autoplay progress bar (progress runs 1 -> 0).
            autoplayTimeLeft: (sw, time, progress) => {
                if (progressBar) progressBar.style.transform = `scaleX(${1 - progress})`;
            },
        },
    });

    initProductParallax(el, swiper, reduceMotion);
}

function initProductParallax(hero, swiper, reduceMotion) {
    if (reduceMotion || !window.matchMedia('(pointer: fine)').matches) return;

    const updateParallax = (event) => {
        const visual = swiper.slides[swiper.activeIndex]?.querySelector('[data-hero-visual]');
        if (!visual || visual.classList.contains('is-missing')) return;

        const bounds = hero.getBoundingClientRect();
        const x = ((event.clientX - bounds.left) / bounds.width - 0.5) * 14;
        const y = ((event.clientY - bounds.top) / bounds.height - 0.5) * 10;

        visual.style.setProperty('--hero-parallax-x', `${x.toFixed(2)}px`);
        visual.style.setProperty('--hero-parallax-y', `${y.toFixed(2)}px`);
    };

    const resetParallax = () => {
        hero.querySelectorAll('[data-hero-visual]').forEach((visual) => {
            visual.style.setProperty('--hero-parallax-x', '0px');
            visual.style.setProperty('--hero-parallax-y', '0px');
        });
    };

    hero.addEventListener('pointermove', updateParallax, { passive: true });
    hero.addEventListener('pointerleave', resetParallax);
    swiper.on('slideChangeTransitionStart', resetParallax);
}

function hideMissingProductImages(hero) {
    hero.querySelectorAll('.hero__product-image').forEach((image) => {
        if (image.complete && image.naturalWidth === 0) {
            image.closest('[data-hero-visual]')?.classList.add('is-missing');
        }

        image.addEventListener('error', () => {
            image.closest('[data-hero-visual]')?.classList.add('is-missing');
        });
    });
}

function animateActiveSlide(swiper, reduceMotion) {
    // No GSAP / reduced motion: CSS leaves the product, headline, description,
    // CTA and accent letters all visible at rest, so nothing stays hidden.
    if (reduceMotion || !window.gsap) return;

    const active = swiper.slides[swiper.activeIndex];
    if (!active) return;

    const gsap = window.gsap;
    const textItems = active.querySelectorAll('[data-hero-anim]');
    const product = active.querySelector('.hero__product');

    // A single timeline so the product image, headline, description and CTA all
    // START at the same instant (position 0) — no entry delay on the product.
    // Only the accent letters carry an internal left-to-right per-letter
    // stagger (~55ms); the first letters are pre-visible (the --lead class).
    const tl = gsap.timeline({ defaults: { ease: 'power3.out', overwrite: 'auto' } });

    if (product) {
        tl.fromTo(
            product,
            { opacity: 0, y: 14, scale: 0.975 },
            { opacity: 1, y: 0, scale: 1, duration: 0.68, clearProps: 'opacity,transform' },
            0
        );
    }
    if (textItems.length) {
        tl.fromTo(
            textItems,
            { opacity: 0, y: 26 },
            { opacity: 1, y: 0, duration: 0.68, clearProps: 'opacity,transform' },
            0
        );
    }
}

function resetInactiveSlides(swiper) {
    const gsap = window.gsap;

    swiper.slides.forEach((slide, index) => {
        if (index === swiper.activeIndex) return;

        // Clear any inline styles GSAP left so the slide returns to its CSS
        // resting (visible) state and re-animates cleanly when it becomes
        // active again.
        const nodes = slide.querySelectorAll('[data-hero-anim], .hero__product');
        if (gsap) {
            gsap.set(nodes, { clearProps: 'opacity,transform' });
        } else {
            nodes.forEach((n) => {
                n.style.opacity = '';
                n.style.transform = '';
            });
        }

        const visual = slide.querySelector('[data-hero-visual]');
        visual?.style.setProperty('--hero-parallax-x', '0px');
        visual?.style.setProperty('--hero-parallax-y', '0px');
    });
}
