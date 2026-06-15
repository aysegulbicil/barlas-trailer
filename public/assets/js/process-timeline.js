/**
 * process-timeline.js
 *
 * When the production-process timeline scrolls into view, the connecting
 * line "draws" itself (height on mobile / width on desktop) and each step
 * activates in sequence. Reduced-motion and no-IntersectionObserver users
 * get the fully-drawn end state immediately.
 */

export function initProcessTimeline() {
    const track = document.querySelector('[data-process]');
    if (!track) return;

    const steps = Array.from(track.querySelectorAll('[data-process-step]'));
    // When the cinematic flow layer is active (flow.js), it scrubs the line
    // with ScrollTrigger; this module then only handles step activation.
    const flowActive = document.documentElement.classList.contains('has-flow');
    const fill = flowActive ? null : track.querySelector('[data-process-fill]');
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const finish = () => {
        steps.forEach((s) => s.classList.add('is-active'));
        if (fill) {
            fill.style.height = '100%';
            fill.style.width = '100%';
        }
    };

    if (reduceMotion || !('IntersectionObserver' in window)) {
        finish();
        return;
    }

    const observer = new IntersectionObserver(
        (entries, obs) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;

                steps.forEach((s, i) => {
                    const delay = Number(s.dataset.stepDelay || i * 140);
                    window.setTimeout(() => s.classList.add('is-active'), delay);
                });

                if (fill) {
                    window.setTimeout(() => {
                        fill.style.height = '100%';
                        fill.style.width = '100%';
                    }, 120);
                }

                obs.disconnect();
            });
        },
        { threshold: 0.25 }
    );

    observer.observe(track);
}
