/**
 * mouse-follower.js
 *
 * Decorative cursor that trails the pointer with smooth lerping and grows
 * over interactive elements. Purely an enhancement: it is disabled on
 * touch devices and when the user prefers reduced motion.
 */

export function initMouseFollower() {
    const follower = document.querySelector('.cursor-follower');
    if (!follower) return;

    const isTouch = window.matchMedia('(hover: none)').matches;
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (isTouch || reduceMotion) return;

    let mouseX = 0;
    let mouseY = 0;
    let currentX = 0;
    let currentY = 0;
    let active = false;

    window.addEventListener('mousemove', (e) => {
        mouseX = e.clientX;
        mouseY = e.clientY;
        if (!active) {
            active = true;
            follower.classList.add('is-active');
        }
    });

    // Grow the cursor over links and buttons.
    const interactiveSelector = 'a, button, [data-cursor-hover]';
    document.addEventListener('mouseover', (e) => {
        if (e.target.closest(interactiveSelector)) follower.classList.add('is-hover');
    });
    document.addEventListener('mouseout', (e) => {
        if (e.target.closest(interactiveSelector)) follower.classList.remove('is-hover');
    });

    // Smooth follow loop.
    function render() {
        currentX += (mouseX - currentX) * 0.18;
        currentY += (mouseY - currentY) * 0.18;
        follower.style.transform = `translate3d(${currentX - 18}px, ${currentY - 18}px, 0)`;
        requestAnimationFrame(render);
    }

    requestAnimationFrame(render);
}
