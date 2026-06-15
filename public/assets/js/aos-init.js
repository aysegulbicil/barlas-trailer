/**
 * aos-init.js
 *
 * Initializes the AOS (Animate On Scroll) library if it loaded from the
 * CDN. Also wires a lightweight fallback for elements using [data-anim]
 * (our own scroll-reveal) via IntersectionObserver, so the page still
 * animates gracefully even if AOS is unavailable.
 */

export function initAos() {
    // AOS is exposed globally by the CDN bundle.
    if (window.AOS) {
        window.AOS.init({
            duration: 600,
            easing: 'ease-out',
            once: true,
            offset: 80,
            disable: () =>
                window.matchMedia('(prefers-reduced-motion: reduce)').matches,
        });
    }

    initDataAnim();
}

/**
 * Reveal elements marked with [data-anim] as they enter the viewport.
 * Honors an optional [data-anim-delay] in milliseconds.
 */
function initDataAnim() {
    const items = document.querySelectorAll('[data-anim]');
    if (items.length === 0) return;

    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (reduceMotion || !('IntersectionObserver' in window)) {
        items.forEach((el) => el.classList.add('is-visible'));
        return;
    }

    const observer = new IntersectionObserver(
        (entries, obs) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                const el = entry.target;
                const delay = Number(el.dataset.animDelay || 0);
                window.setTimeout(() => el.classList.add('is-visible'), delay);
                obs.unobserve(el);
            });
        },
        { threshold: 0.15 }
    );

    items.forEach((el) => observer.observe(el));
}
