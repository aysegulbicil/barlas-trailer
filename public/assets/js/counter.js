/**
 * counter.js
 *
 * Animates numeric stat counters ([data-count]) from 0 to their target
 * value the first time they scroll into view. Uses requestAnimationFrame
 * with an ease-out curve and locale-aware number formatting.
 */

export function initCounters() {
    const counters = document.querySelectorAll('[data-count]');
    if (counters.length === 0) return;

    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (reduceMotion || !('IntersectionObserver' in window)) {
        counters.forEach((el) => (el.textContent = formatNumber(targetOf(el))));
        return;
    }

    const observer = new IntersectionObserver(
        (entries, obs) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                animateCounter(entry.target);
                obs.unobserve(entry.target);
            });
        },
        { threshold: 0.4 }
    );

    counters.forEach((el) => observer.observe(el));
}

function targetOf(el) {
    return parseInt(el.dataset.count, 10) || 0;
}

function formatNumber(value) {
    const locale = document.documentElement.lang || 'en';
    return new Intl.NumberFormat(locale).format(value);
}

function animateCounter(el) {
    const target = targetOf(el);
    const duration = 1600;
    let startTime = null;

    // Brief scale "pop" to make the count feel more alive.
    const card = el.closest('.stat-card');
    if (card) {
        card.classList.add('is-counting');
        card.addEventListener(
            'animationend',
            () => card.classList.remove('is-counting'),
            { once: true }
        );
    }

    function step(timestamp) {
        if (startTime === null) startTime = timestamp;
        const progress = Math.min((timestamp - startTime) / duration, 1);
        // easeOutCubic
        const eased = 1 - Math.pow(1 - progress, 3);
        el.textContent = formatNumber(Math.round(target * eased));
        if (progress < 1) requestAnimationFrame(step);
    }

    requestAnimationFrame(step);
}
