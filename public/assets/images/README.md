# Homepage Images

Drop real images here with the **exact filenames** below. Until a file exists,
the homepage shows a premium gradient + blueprint **placeholder** automatically
(no broken images), so the layout always looks intentional. Replacing a
placeholder is just a matter of adding the file with the right name.

## 🔴 Priority (highest visual impact)

| Filename | Use | Suggested size | Notes |
|---|---|---|---|
| `hero-1.jpg` | Hero slide 1 background | 1920×1080+ | ADR fuel tanker on road/yard, dark-friendly |
| `hero-2.jpg` | Hero slide 2 background | 1920×1080+ | Lowbed carrying machinery |
| `hero-3.jpg` | Hero slide 3 background | 1920×1080+ | Factory / production line / export fleet |
| `category-tankers.jpg` | Product card | 1200×900 | Tanker |
| `category-silos.jpg` | Product card | 1200×900 | Silo trailer (silobas) |
| `category-tippers.jpg` | Product card | 1200×900 | Tipper (damper) |
| `category-dry-cargo.jpg` | Product card | 1200×900 | Dry cargo / box |
| `category-curtain.jpg` | Product card | 1200×900 | Curtainsider (perdeli/tenteli) |
| `category-reefer.jpg` | Product card | 1200×900 | Refrigerated (frigorifik) |
| `category-lowbed.jpg` | Product card | 1200×900 | Lowbed |
| `category-containers.jpg` | Product card | 1200×900 | Container carrier |
| `stats-bg.jpg` | Stats band background | 1920×1000 | Dark industrial (welding / factory / fleet) |

## 🟡 Secondary (raises quality further)

| Filename | Use | Suggested size | Notes |
|---|---|---|---|
| `about.jpg` | About section image | 900×1100 (portrait) | Production hall / company |
| `showcase.png` | Featured product (hotspots) | 1600×1000, **transparent** | Cut-out tanker/trailer render |
| `services-workshop.jpg` | Services section | 1200×800 | Repair workshop |
| `why-bg.jpg` | Why-choose-us image | 1000×1200 | Quality control / welding close-up |
| `cta-bg.jpg` | CTA band background | 1920×900 | Dark industrial |
| `partners/partner-1.svg` … | References marquee | any, transparent | Client / partner logos (svg or png) |

## 🔵 Blog covers (in `blog/` subfolder)

| Filename | Use | Suggested size | Notes |
|---|---|---|---|
| `blog/blog-adr-safety.jpg` | Post cover | 1600×1000 | ADR tanker / hazmat placards |
| `blog/blog-silo-maintenance.jpg` | Post cover | 1600×1000 | Silo trailer in workshop |
| `blog/blog-lowbed-guide.jpg` | Post cover | 1600×1000 | Lowbed loading machinery |
| `blog/blog-lightweight-chassis.jpg` | Post cover | 1600×1000 | Bare chassis / production line |
| `blog/blog-cold-chain.jpg` | Post cover | 1600×1000 | Reefer trailer / cold storage |
| `blog/blog-winter-maintenance.jpg` | Post cover | 1600×1000 | Trailer in winter conditions |

## Formats
- Landscape backgrounds: `.jpg` (or `.webp`)
- Product / cut-out: `.png` or `.webp` (transparent where noted)
- Logos: `.svg` preferred

> After adding files, hard-refresh (Ctrl+F5). No code change needed — the CSS
> already points at these paths and falls back to the placeholder if missing.
