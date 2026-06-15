/**
 * progress-bars.js
 *
 * Fills capability bars ([data-bar]) to their target width the first time
 * they scroll into view. The width target is set via the CSS custom
 * property --target; here we just toggle the .is-filled class so CSS
 * transitions the width. Graceful fallback fills instantly.
 */

export function initProgressBars() {
    const fills = document.querySelectorAll('[data-bar]');
    if (fills.length === 0) return;

    if (!('IntersectionObserver' in window)) {
        fills.forEach((f) => f.classList.add('is-filled'));
        return;
    }

    const observer = new IntersectionObserver(
        (entries, obs) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('is-filled');
                obs.unobserve(entry.target);
            });
        },
        { threshold: 0.4 }
    );

    fills.forEach((f) => observer.observe(f));
}
