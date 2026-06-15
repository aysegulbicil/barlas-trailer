/**
 * showcase-hotspots.js
 *
 * Reveals the product hotspots with a staggered "pop" when the stage
 * scrolls into view, and makes the callouts toggle on tap (so touch users
 * — who have no hover — can read them). Markers are visible by default, so
 * the section still works without JS.
 */

export function initHotspots() {
    const spots = Array.from(document.querySelectorAll('[data-hotspot]'));
    if (spots.length === 0) return;

    revealOnScroll(spots);
    enableTapToggle(spots);
}

function revealOnScroll(spots) {
    const stage = document.querySelector('[data-hotspots]');

    if (!stage || !('IntersectionObserver' in window)) {
        spots.forEach((s) => s.classList.add('is-visible'));
        return;
    }

    const observer = new IntersectionObserver(
        (entries, obs) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                spots.forEach((s) => {
                    const delay = Number(s.dataset.hotspotDelay || 0);
                    window.setTimeout(() => s.classList.add('is-visible'), delay);
                });
                obs.disconnect();
            });
        },
        { threshold: 0.3 }
    );

    observer.observe(stage);
}

function enableTapToggle(spots) {
    spots.forEach((spot) => {
        const marker = spot.querySelector('.hotspot__marker');
        if (!marker) return;

        marker.addEventListener('click', (e) => {
            e.preventDefault();
            spots.forEach((other) => {
                if (other !== spot) other.classList.remove('is-open');
            });
            spot.classList.toggle('is-open');
        });
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('[data-hotspot]')) {
            spots.forEach((s) => s.classList.remove('is-open'));
        }
    });
}
