/**
 * swiper-init.js
 *
 * Generic Swiper initializer. Any element marked with [data-swiper] is
 * turned into a slider. Per-instance options can be passed as JSON via the
 * data-swiper attribute, e.g. data-swiper='{"slidesPerView": 3, "loop": true}'.
 * Safe to call on pages with no sliders (it simply does nothing).
 */

export function initSwipers() {
    if (!window.Swiper) return;

    const sliders = document.querySelectorAll('[data-swiper]');
    sliders.forEach((el) => {
        let options = {};
        const raw = el.getAttribute('data-swiper');
        if (raw) {
            try {
                options = JSON.parse(raw);
            } catch (err) {
                // Invalid JSON: fall back to sensible defaults.
                options = {};
            }
        }

        const defaults = {
            slidesPerView: 1,
            spaceBetween: 24,
            grabCursor: true,
            speed: 600,
            a11y: { enabled: true },
        };

        // eslint-disable-next-line no-new
        new window.Swiper(el, { ...defaults, ...options });
    });
}
