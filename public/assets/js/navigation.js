/**
 * navigation.js
 *
 * Header behaviour: mobile drawer, language-switcher dropdown, a
 * scrolled-state class on the header, and the mega menus (Products
 * showcase panel + compact Services panel).
 *
 * Mega menu interaction model:
 *  - Desktop pointer: hover-intent open (small delay both ways).
 *  - Desktop touch / keyboard: the caret button toggles; on touch the
 *    first tap on the trigger link opens instead of navigating.
 *  - Mobile (<992px): the same panels behave as two-level accordions
 *    inside the drawer.
 * All interactions are keyboard-, Escape- and outside-click aware.
 */

const DESKTOP_MQ = window.matchMedia('(min-width: 992px)');
const HOVER_MQ = window.matchMedia('(hover: hover)');

const OPEN_DELAY = 120;
const CLOSE_DELAY = 200;

export function initNavigation() {
    initHeaderOffset();
    initMobileMenu();
    initLangSwitcher();
    initStickyHeader();
    initMegaMenus();
}

/**
 * Expose the real header height (topbar + nav row) as a CSS variable so
 * the mobile drawer and the mega panels' max-height track it exactly.
 */
function initHeaderOffset() {
    const header = document.querySelector('[data-header]');
    if (!header) return;

    const update = () => {
        document.documentElement.style.setProperty('--header-offset', `${header.offsetHeight}px`);
    };
    update();
    window.addEventListener('resize', update);
}

function initMobileMenu() {
    const toggle = document.querySelector('[data-nav-toggle]');
    const menu = document.querySelector('[data-nav-menu]');
    if (!toggle || !menu) return;

    const close = () => {
        toggle.setAttribute('aria-expanded', 'false');
        menu.classList.remove('is-open');
        document.documentElement.classList.remove('nav-locked');
    };

    toggle.addEventListener('click', () => {
        const open = toggle.getAttribute('aria-expanded') === 'true';
        toggle.setAttribute('aria-expanded', String(!open));
        menu.classList.toggle('is-open', !open);
        document.documentElement.classList.toggle('nav-locked', !open);
    });

    // Close when a link is tapped or on Escape.
    menu.addEventListener('click', (e) => {
        if (e.target.closest('a')) close();
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') close();
    });
}

function initLangSwitcher() {
    const switcher = document.querySelector('[data-lang-switcher]');
    if (!switcher) return;

    const toggle = switcher.querySelector('.lang-switcher__toggle');

    toggle.addEventListener('click', (e) => {
        e.stopPropagation();
        const open = switcher.classList.toggle('is-open');
        toggle.setAttribute('aria-expanded', String(open));
    });

    // Close on outside click or Escape.
    document.addEventListener('click', (e) => {
        if (!switcher.contains(e.target)) {
            switcher.classList.remove('is-open');
            toggle.setAttribute('aria-expanded', 'false');
        }
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            switcher.classList.remove('is-open');
            toggle.setAttribute('aria-expanded', 'false');
        }
    });
}

function initStickyHeader() {
    const header = document.querySelector('[data-header]');
    if (!header) return;

    const onScroll = () => header.classList.toggle('is-scrolled', window.scrollY > 8);
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
}

/* ===================================================================
   Mega menus
   =================================================================== */

function initMegaMenus() {
    const items = [...document.querySelectorAll('[data-mega-item]')];
    if (!items.length) return;

    let current = null;

    const close = (item) => {
        if (!item) return;
        item.classList.remove('is-open');
        item.querySelector('[data-mega-caret]')?.setAttribute('aria-expanded', 'false');
        if (current === item) current = null;
    };

    const open = (item) => {
        if (current && current !== item) close(current);
        item.classList.add('is-open');
        item.querySelector('[data-mega-caret]')?.setAttribute('aria-expanded', 'true');
        current = item;
    };

    items.forEach((item) => {
        const link = item.querySelector('[data-mega-link]');
        const caret = item.querySelector('[data-mega-caret]');
        let openTimer = 0;
        let closeTimer = 0;

        // Hover intent (pointer devices on desktop widths only). The panel
        // is a DOM child of the item, so hovering it keeps the item "hot".
        item.addEventListener('mouseenter', () => {
            if (!DESKTOP_MQ.matches || !HOVER_MQ.matches) return;
            clearTimeout(closeTimer);
            openTimer = setTimeout(() => open(item), OPEN_DELAY);
        });
        item.addEventListener('mouseleave', () => {
            if (!DESKTOP_MQ.matches || !HOVER_MQ.matches) return;
            clearTimeout(openTimer);
            closeTimer = setTimeout(() => close(item), CLOSE_DELAY);
        });

        caret?.addEventListener('click', () => {
            item.classList.contains('is-open') ? close(item) : open(item);
        });

        // Touch on desktop widths: first tap opens, second tap navigates.
        link?.addEventListener('click', (e) => {
            if (DESKTOP_MQ.matches && !HOVER_MQ.matches && !item.classList.contains('is-open')) {
                e.preventDefault();
                open(item);
            }
        });

        // ArrowDown on the trigger jumps into the open panel.
        link?.addEventListener('keydown', (e) => {
            if (e.key !== 'ArrowDown' || !DESKTOP_MQ.matches) return;
            e.preventDefault();
            open(item);
            item.querySelector('[data-mega-panel] a')?.focus();
        });
    });

    document.addEventListener('click', (e) => {
        if (current && !current.contains(e.target)) close(current);
    });

    document.addEventListener('keydown', (e) => {
        if (e.key !== 'Escape' || !current) return;
        const focusTarget = current.querySelector('[data-mega-caret], [data-mega-link]');
        close(current);
        focusTarget?.focus();
    });

    // Keep desktop panels in sync with keyboard focus position.
    document.addEventListener('focusin', (e) => {
        if (current && DESKTOP_MQ.matches && !current.contains(e.target)) close(current);
    });

    // Reset all open states when crossing the desktop/mobile breakpoint.
    DESKTOP_MQ.addEventListener('change', () => {
        items.forEach(close);
        document.querySelectorAll('[data-mega-cat].is-open').forEach((cat) => {
            cat.classList.remove('is-open');
            cat.querySelector('[data-mega-cat-toggle]')?.setAttribute('aria-expanded', 'false');
        });
    });

    initMegaCategories();
}

/**
 * Products panel: the category rail drives the preview pane on desktop
 * (hover/focus + arrow keys) and a one-at-a-time accordion on mobile.
 */
function initMegaCategories() {
    document.querySelectorAll('[data-mega-panel]').forEach((panel) => {
        const cats = [...panel.querySelectorAll('[data-mega-cat]')];
        if (!cats.length) return;

        const links = cats.map((cat) => cat.querySelector('[data-mega-cat-link]'));

        const activate = (target) => {
            cats.forEach((cat) => cat.classList.toggle('is-active', cat === target));
        };

        cats.forEach((cat, i) => {
            const link = links[i];

            link.addEventListener('mouseenter', () => {
                if (DESKTOP_MQ.matches) activate(cat);
            });
            link.addEventListener('focus', () => {
                if (DESKTOP_MQ.matches) activate(cat);
            });

            link.addEventListener('keydown', (e) => {
                if (!DESKTOP_MQ.matches) return;
                if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                    e.preventDefault();
                    const dir = e.key === 'ArrowDown' ? 1 : -1;
                    links[(i + dir + links.length) % links.length].focus();
                } else if (e.key === 'Home') {
                    e.preventDefault();
                    links[0].focus();
                } else if (e.key === 'End') {
                    e.preventDefault();
                    links[links.length - 1].focus();
                }
            });

            // Mobile: one category pane open at a time keeps the list scannable.
            cat.querySelector('[data-mega-cat-toggle]')?.addEventListener('click', () => {
                const willOpen = !cat.classList.contains('is-open');
                cats.forEach((c) => {
                    const isThis = c === cat && willOpen;
                    c.classList.toggle('is-open', isThis);
                    c.querySelector('[data-mega-cat-toggle]')?.setAttribute('aria-expanded', String(isThis));
                });
            });

            initChipPreview(cat);
        });
    });
}

/**
 * Featured-chip preview: hovering/focusing a "featured model" chip
 * crossfades the category pane's media box to that product's visual
 * (photo if available + animated name reveal), and eases back to the
 * category visual on leave — same hover-intent feel as the rail.
 *
 * Photos are optional: assets/images/products/{cat}-{product}.jpg is
 * preloaded on demand; when missing, the blueprint placeholder stays
 * underneath and only the name plate animates in, so the interaction
 * works before any photography exists. Desktop pointer/keyboard only.
 */
function initChipPreview(cat) {
    const media = cat.querySelector('[data-mega-media]');
    const chips = [...cat.querySelectorAll('.mega-chip[data-chip-img]')];
    if (!media || !chips.length) return;

    const assetsBase = cat.closest('.mega')?.dataset.assetsBase ?? '';
    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // Preview layer (image + name plate), built once per pane.
    const layer = document.createElement('div');
    layer.className = 'mega-preview';
    layer.setAttribute('aria-hidden', 'true');
    layer.innerHTML = '<div class="mega-preview__img"></div><span class="mega-preview__name"></span>';
    media.appendChild(layer);

    const img = layer.querySelector('.mega-preview__img');
    const name = layer.querySelector('.mega-preview__name');
    const loaded = new Map(); // src -> true (ok) | false (missing)
    let current = '';
    let hideTimer = 0;

    const show = (chip) => {
        if (!DESKTOP_MQ.matches) return;
        clearTimeout(hideTimer);

        const src = `${assetsBase}/${chip.dataset.chipImg}`;
        current = src;
        name.textContent = chip.dataset.chipName ?? chip.textContent.trim();

        const paint = (ok) => {
            if (current !== src) return; // pointer already moved on
            img.style.backgroundImage = ok ? `url("${src}")` : '';
            media.classList.add('is-preview');
            if (!reduce) {
                // Restart the reveal so quick chip-to-chip moves still animate.
                layer.classList.remove('is-swap');
                void layer.offsetWidth;
                layer.classList.add('is-swap');
            }
        };

        if (loaded.has(src)) {
            paint(loaded.get(src));
            return;
        }
        const probe = new Image();
        probe.onload = () => { loaded.set(src, true); paint(true); };
        probe.onerror = () => { loaded.set(src, false); paint(false); };
        probe.src = src;
    };

    const hide = () => {
        clearTimeout(hideTimer);
        hideTimer = setTimeout(() => {
            current = '';
            media.classList.remove('is-preview');
        }, 140);
    };

    chips.forEach((chip) => {
        chip.addEventListener('mouseenter', () => { if (HOVER_MQ.matches) show(chip); });
        chip.addEventListener('mouseleave', () => { if (HOVER_MQ.matches) hide(); });
        chip.addEventListener('focus', () => show(chip));
        chip.addEventListener('blur', hide);
    });
}
