# DESIGN.md — Barlas Trailer

> Locked design system for the homepage redesign. Generated via `/impeccable init`,
> combining Impeccable + Taste + Emil Kowalski (design engineering) standards.
> Every later stylesheet and view MUST reference this. Tokens live in
> `public/assets/css/variables.css`; the premium presentation layer lives in
> `public/assets/css/redesign.css` (loaded last).

## Design read

Redesign of a heavy-industry B2B manufacturer (fuel/LPG tankers, silos, tippers,
lowbeds, semi-trailers) for an international procurement audience, with an
**industrial-luxury** language, leaning toward native CSS design tokens +
GSAP/Swiper/AOS with restrained, cinematic motion.

Mode: **Redesign — Preserve.** Brand, information architecture, route slugs,
language files (TR/EN/RU/AR/FR) and the CodeIgniter 4 structure are preserved.
Only the presentation and motion layers are rebuilt.

Dials: `DESIGN_VARIANCE 6` · `MOTION_INTENSITY 6` · `VISUAL_DENSITY 4`.

## The lane: "Engineered steel, presented quietly"

Heavy machinery rendered with the calm and confidence of a luxury brand. Lots of
negative space, a strong typographic spine, graphite/steel surfaces, hairline
borders instead of boxed cards, and a single warm accent used like a weld spark.
No generic SaaS card-soup, no purple/blue mesh gradients, no glowing buttons.

## Color — locked

Brand stays navy + a single orange accent. Neutrals recalibrated cooler/deeper.

- `--primary #005BAA` (steel blue, the brand) and its dark/bright variants.
- `--accent #F97316` — the ONE accent. Used sparingly: a single live node, a
  hairline underline, one micro-detail per section at most. Never a section theme.
- Ink: deep graphite-navy `--dark #0B1220` / `--dark-soft #131c2e` for dark bands
  and the hero. No pure black (`#000`), no pure white text on color.
- Surfaces: off-white `--light #F6F8FB`, white cards, **hairline** borders
  (`--hairline`) carry hierarchy before shadows do.
- Shadows are tinted navy, never pure black. Glows are removed.

Color Consistency Lock: orange accent is identical across all sections. Theme
Lock: light page throughout, with deliberate dark bands (hero, stats, CTA) that
belong to the same family (graphite-navy), not a different website.

## Typography — locked

- **Display / headings:** `Sora` (600/700/800), tight tracking, `line-height 1.05`.
  Confident geometric grotesque that reads as engineering, not template Inter.
- **Body / UI:** `Inter` (self-loaded webfont now, not just a system fallback),
  `line-height 1.6`, measure capped at ~68ch.
- Per-glyph fallback to Inter covers Cyrillic (RU); Arabic (AR) falls back to the
  system Arabic face. Both fonts loaded with `display: swap` + preconnect.
- Emphasis within a headline uses weight/color of the SAME family — never a
  random serif word dropped in. Big numbers (stats, counters) use display weight.

Eyebrow restraint: small uppercase labels are rationed (Taste rule: ≤ 1 per 3
sections). The headline alone usually carries the section.

## Shape & spacing — locked

- One radius scale: pills for buttons/chips, `--radius-lg (18px)` for media frames
  and surfaces, `--radius-sm` for inputs. No mixed-system corners.
- Generous vertical rhythm: `--section-y` ~ clamp(4.5rem … 8.5rem).
- Group with negative space and hairlines first; reach for a real card only when
  elevation communicates true hierarchy.

## Motion — Emil Kowalski rules

- Easings: `--ease-out: cubic-bezier(0.22,1,0.32,1)` (strong, for enter/exit),
  `--ease-in-out: cubic-bezier(0.76,0,0.24,1)` (on-screen movement),
  `--ease-spring` for the few "alive" moments. Never `ease-in` on UI.
- Durations: press 120–160ms, hover/dropdown 180–240ms, reveals 420–560ms,
  slider 700–900ms (cinematic, deliberate). UI interactions stay < 300ms.
- Animate **only** `transform` and `opacity`. Buttons get `:active { scale(0.97) }`
  for tactile feedback. Reveals enter from `translateY` + `opacity`, never `scale(0)`.
- Scroll reveals stagger 40–80ms between siblings. Pagination/underlines morph.
- `prefers-reduced-motion`: all ambient loops, parallax and entrance transforms
  collapse to static; only gentle opacity remains. Honored site-wide.

## Removed (anti-slop)

- Custom cursor follower (accessibility/perf-hostile AI tell) — removed.
- Button drop-glows → tinted shadows / hairlines.
- Empty/broken hero trust `<ul>` → removed (trust signals live below the hero).
- Commented-out CTA band → restored and re-skinned.

## NEW HOMEPAGE (current) — "yeni" interface

The homepage was rebuilt from scratch (autoklasa.pl-referenced, dark theme +
Barlas blue) and now renders via `layouts/yeni.php` + `pages/home.php` using
`assets/css/yeni.css`, `assets/js/yeni-app.js`, `assets/js/yeni-tanker.js`.
Flow: kinetic hero + 3D tanker orbit + design/build/deliver strip → trust
ticker → counters → hotspot product stage (700px frame) → sticky "Neden
Barlas" cards → process line → AI assistant console (demo) → auto-scrolling
references belt → parallax gallery (3 columns, "view all" → products) →
footer with contact. New language keys: `flow_step_1..3`, `gallery_title`,
`gallery_lead`, `gallery_cta` (all 5 locales). The sections below
(cinematic flow layer, 3D actor, old redesign) describe the PREVIOUS
homepage; those files (`flow.css`, `flow.js`, `tanker-3d.js`, `pages/home/*`
partials) remain in the repo but are no longer loaded by the homepage.

## Cinematic flow layer (homepage)

Design read: redesign-overhaul of a B2B industrial manufacturer landing;
dials VARIANCE 8 / MOTION 8 / DENSITY 4. IA order, slugs, nav labels and all
language keys preserved. The page reads as one continuous scroll story:

- **Hero** — single kinetic hero (the 3-slide Swiper retired; its copy keys
  remain unused in the language files). Masked headline lines rise on load;
  on desktop the hero pins for +55% while the headline drifts up and the 3D
  tanker swings from showroom 3/4 to road-ready profile.
- **Theme lock** — dark open (hero) → light body → dark close (CTA finale +
  footer). Stats and the AI console were re-skinned light; no random dark
  strips mid-page. The dark finale is the page's one deliberate theme switch.
- **Categories** — horizontal scroll-pan strip (canonical pin + scrub,
  `start: 'top top'`); native scroll-snap below 992px / reduced motion.
- **References** — the logo marquee became a hairline logo wall: the trust
  strip is the page's single marquee.
- **Eyebrow budget** — 3 on the page (hero, categories, process); the AI
  pill is a sentence-case badge, not an uppercase eyebrow.
- Files: `assets/js/flow.js` (Lenis + ScrollTrigger orchestration; emits
  `flow:ready`), `assets/css/flow.css` (cinematic layer, loaded after
  redesign.css). All initial-hidden states live under `html.has-flow` so
  no-JS / reduced-motion users always see content.
- Known compromise: hero "Teklif Al" and finale "İletişime Geç" share the
  contact intent with different labels — copy lives in language files and
  is preserved by rule; unify labels there if desired.

## 3D tanker actor (scroll-driven scene)

Pattern: one fixed, pointer-transparent WebGL canvas (`.tanker-stage`, z-index 5,
under the header) hosts a procedurally built tanker semi-trailer that stays on
stage for the whole page — cursor-orbit in the hero (Spline-style), then GSAP
ScrollTrigger scrubs a single shared `pose {x,y,ry,rx,s,o}` in three acts:
hero pin (showroom 3/4 → road profile) → drives off stage left through the
light body → returns to park beside the dark CTA finale. It waits for
`flow:ready` so its triggers are built after flow.js's pins. Files:

- `public/assets/js/tanker-3d.js` — plain deferred script; needs global THREE
  (three@0.149 UMD via CDN, loaded only on home), gsap + ScrollTrigger.
- `public/assets/css/tanker-3d.css` — stage layer + hero image hand-off via
  `html.has-tanker-3d`.
- `preview-3d.html` (project root) — standalone test bench, safe to delete.

Rules: progressive enhancement only (skips on reduced-motion, <992px, missing
WebGL/libs — static hero images remain); never blocks pointer events; the model
is procedural (no asset licenses) with one orange accent (discharge valve) and
the brand-blue waistline; swap-in path for a real `.glb` is documented in the
JS header. Choreography must keep the vehicle clear of reading columns.

## Preserve list (never change silently)

Route slugs, nav labels, language keys, form field names, brand wordmark, the
section IA order (hero → marquee → about → categories → showcase → stats →
process → services → ai-assistant → references → cta).
