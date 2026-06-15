/**
 * Continuous product-category carousel. Swiper's looped duplicate slides keep
 * the rightward motion seamless while preserving touch dragging.
 */
export function initCategoriesCarousel() {
    if (!window.Swiper) return;

    const el = document.querySelector('[data-categories-carousel]');
    if (!el) return;

    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const swiper = new window.Swiper(el, {
        slidesPerView: 1,
        spaceBetween: 16,
        loop: true,
        loopAdditionalSlides: 4,
        speed: 6500,
        grabCursor: true,
        allowTouchMove: true,
        freeMode: {
            enabled: true,
            momentum: false,
        },
        autoplay: reduceMotion ? false : {
            delay: 0,
            disableOnInteraction: false,
            pauseOnMouseEnter: true,
            reverseDirection: true,
            waitForTransition: false,
        },
        breakpoints: {
            600: { slidesPerView: 2, spaceBetween: 20 },
            900: { slidesPerView: 3, spaceBetween: 20 },
            1200: { slidesPerView: 4, spaceBetween: 20 },
        },
        a11y: { enabled: true },
    });

    if (reduceMotion) swiper.setProgress(0, 0);
}
