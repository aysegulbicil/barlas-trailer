/**
 * home.js - Home page entry point (ES module)
 *
 * Wires all home-specific enhancements once the DOM is ready. Each module
 * degrades gracefully on its own, so a missing library never breaks the page.
 */

import { initHeroSlider } from './hero-slider.js';
import { initProgressBars } from './progress-bars.js';
import { initHotspots } from './showcase-hotspots.js';
import { initProcessTimeline } from './process-timeline.js';
import { initSwipers } from './swiper-init.js';
import { initCategoriesCarousel } from './categories-carousel.js';
import { initAiAssistant } from './ai-assistant.js';

function boot() {
    initHeroSlider();
    initProgressBars();
    initHotspots();
    initProcessTimeline();
    initSwipers();
    initCategoriesCarousel();
    initAiAssistant();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
} else {
    boot();
}
