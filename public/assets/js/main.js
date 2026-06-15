/**
 * main.js — Site-wide entry point (ES module)
 *
 * Loaded on every page. Wires the global UI modules once the DOM is ready.
 * Page-specific behaviour lives in its own module (e.g. home.js).
 */

import { initNavigation } from './navigation.js';
import { initAos } from './aos-init.js';
import { initCounters } from './counter.js';
import { initAnimations } from './animations.js';

function boot() {
    initNavigation();
    initAos();
    initCounters();
    initAnimations();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
} else {
    boot();
}
