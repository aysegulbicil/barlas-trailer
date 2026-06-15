/**
 * animations.js
 *
 * GSAP-powered ambient + scroll effects that complement the hero slider
 * (which owns its own per-slide entrance animation in hero-slider.js):
 *   - parallax drift on the hero glow and network map while scrolling
 *   - subtle parallax on the tanker silhouette
 *
 * Fully optional: bails out cleanly without GSAP/ScrollTrigger or when the
 * user prefers reduced motion. CSS already handles the looping ambient
 * animations (routes, pulses, wheels).
 */

export function initAnimations() {
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (reduceMotion || !window.gsap || !window.ScrollTrigger) return;

    const gsap = window.gsap;
    gsap.registerPlugin(window.ScrollTrigger);

    initHeroParallax(gsap);
    initDecorParallax(gsap);
}

/**
 * Generic parallax for any element marked [data-parallax="N"] (N = drift in
 * percent). Used by decorative blueprint/shape layers across sections to add
 * depth on scroll.
 */
function initDecorParallax(gsap) {
    document.querySelectorAll('[data-parallax]').forEach((node) => {
        const amount = Number(node.getAttribute('data-parallax')) || 16;
        gsap.to(node, {
            yPercent: amount,
            ease: 'none',
            scrollTrigger: {
                trigger: node.closest('section') || node,
                start: 'top bottom',
                end: 'bottom top',
                scrub: true,
            },
        });
    });
}

/**
 * Layered parallax: background elements drift at different speeds as the
 * hero scrolls out of view, adding depth without distracting motion.
 */
function initHeroParallax(gsap) {
    const hero = document.querySelector('[data-hero]');
    if (!hero) return;

    const layers = [
        { el: '.hero__glow', y: 26 },
        { el: '.hero__network', y: 14 },
        { el: '.hero__vehicle', y: -18 },
    ];

    layers.forEach(({ el, y }) => {
        const node = hero.querySelector(el);
        if (!node) return;

        gsap.to(node, {
            yPercent: y,
            ease: 'none',
            scrollTrigger: {
                trigger: hero,
                start: 'top top',
                end: 'bottom top',
                scrub: true,
            },
        });
    });
}
