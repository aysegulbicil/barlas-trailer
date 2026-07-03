<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * Blog controller
 *
 * File-based blog (no database). Locale-independent post metadata
 * (slug, date, category) lives in the POSTS registry below; all
 * localized text (title, excerpt, body) lives in the Blog language
 * files keyed by slug, so slugs/URLs stay identical across locales and
 * the language switcher keeps working on detail pages.
 *
 * Posts are text-only (no cover images): the editorial line is
 * research-backed industry articles citing peer-reviewed sources.
 */
class Blog extends BaseController
{
    /**
     * Post registry, grouped by editorial series (display order is sorted
     * by date at runtime — see registry()). 100 posts across 10 categories.
     */
    private const POSTS = [
        // Series 1-2: aerodynamics + materials (2026-07)
        ['slug' => 'tanker-aerodynamics-fuel-savings',        'date' => '2026-06-25', 'category' => 'aerodynamics'],
        ['slug' => 'composite-trailer-weight-reduction',      'date' => '2026-06-18', 'category' => 'materials'],
        ['slug' => 'side-skirt-selection-guide',              'date' => '2026-06-10', 'category' => 'aerodynamics'],
        ['slug' => 'hardox-vs-aluminium-chassis',             'date' => '2026-06-02', 'category' => 'materials'],
        ['slug' => 'boat-tail-diffuser-guide',                'date' => '2026-05-26', 'category' => 'aerodynamics'],
        ['slug' => 'fea-lightweight-trailer-optimisation',    'date' => '2026-05-18', 'category' => 'materials'],
        ['slug' => 'container-carrier-drag-reduction',        'date' => '2026-05-11', 'category' => 'aerodynamics'],
        ['slug' => 'silo-trailer-stainless-composite',        'date' => '2026-05-04', 'category' => 'materials'],
        ['slug' => 'cab-over-vs-conventional-aerodynamics',   'date' => '2026-04-27', 'category' => 'aerodynamics'],
        ['slug' => 'carbon-fiber-trailer-manufacturing',      'date' => '2026-04-20', 'category' => 'materials'],
        ['slug' => 'adjustable-roof-height-trailers',         'date' => '2026-04-13', 'category' => 'aerodynamics'],
        ['slug' => 'trailer-material-selection-2026',         'date' => '2026-04-06', 'category' => 'materials'],
        ['slug' => 'wind-tunnel-to-road-data',                'date' => '2026-03-30', 'category' => 'aerodynamics'],
        ['slug' => 'tipper-wear-plate-hardox-400-450',        'date' => '2026-03-23', 'category' => 'materials'],
        ['slug' => 'silo-tipper-wind-resistance',             'date' => '2026-03-16', 'category' => 'aerodynamics'],
        ['slug' => 'lowbed-high-strength-steel',              'date' => '2026-03-09', 'category' => 'materials'],
        ['slug' => 'gap-fairing-systems',                     'date' => '2026-03-02', 'category' => 'aerodynamics'],
        ['slug' => 'lean-material-management',                'date' => '2026-02-23', 'category' => 'materials'],
        ['slug' => 'lowbed-heavy-haul-aerodynamics',          'date' => '2026-02-16', 'category' => 'aerodynamics'],
        ['slug' => 'tanker-corrosion-material-coating',       'date' => '2026-02-09', 'category' => 'materials'],

        // Series 3: zero-emission & electric trailer trends
        ['slug' => 'what-is-e-trailer',                       'date' => '2026-06-28', 'category' => 'electric'],
        ['slug' => 'e-axle-energy-recovery',                  'date' => '2026-04-17', 'category' => 'electric'],
        ['slug' => 'electric-reefer-units',                   'date' => '2026-02-04', 'category' => 'electric'],
        ['slug' => 'solar-panels-on-trailers',                'date' => '2025-11-24', 'category' => 'electric'],
        ['slug' => 'trailer-design-for-electric-trucks',      'date' => '2025-09-13', 'category' => 'electric'],
        ['slug' => 'battery-trailer-weight-range',            'date' => '2025-07-03', 'category' => 'electric'],
        ['slug' => 'charging-infrastructure-fleet',           'date' => '2025-04-22', 'category' => 'electric'],
        ['slug' => 'hydrogen-freight-trailers',               'date' => '2025-02-09', 'category' => 'electric'],
        ['slug' => 'zero-emission-zones-europe',              'date' => '2024-11-29', 'category' => 'electric'],
        ['slug' => 'electric-trailer-market-2030',            'date' => '2024-09-18', 'category' => 'electric'],

        // Series 4: tanker & ADR (dangerous goods)
        ['slug' => 'what-is-adr-basics',                      'date' => '2026-06-19', 'category' => 'adr'],
        ['slug' => 'partial-fill-tanker-rollover',            'date' => '2026-04-08', 'category' => 'adr'],
        ['slug' => 'tanker-baffle-compartment-design',        'date' => '2026-01-26', 'category' => 'adr'],
        ['slug' => 'adr-tanker-inspections',                  'date' => '2025-11-15', 'category' => 'adr'],
        ['slug' => 'static-electricity-grounding',            'date' => '2025-09-04', 'category' => 'adr'],
        ['slug' => 'bottom-loading-vapor-recovery',           'date' => '2025-06-24', 'category' => 'adr'],
        ['slug' => 'lpg-pressure-tanker-design',              'date' => '2025-04-13', 'category' => 'adr'],
        ['slug' => 'chemical-tanker-compatibility',           'date' => '2025-01-31', 'category' => 'adr'],
        ['slug' => 'adr-driver-training',                     'date' => '2024-11-20', 'category' => 'adr'],
        ['slug' => 'tanker-safety-culture',                   'date' => '2024-09-09', 'category' => 'adr'],

        // Series 5: European regulations & CO2 standards
        ['slug' => 'eu-co2-standards-trailers',               'date' => '2026-06-10', 'category' => 'regulations'],
        ['slug' => 'what-is-vecto-trailer',                   'date' => '2026-03-30', 'category' => 'regulations'],
        ['slug' => 'eu-weights-dimensions-directive',         'date' => '2026-01-17', 'category' => 'regulations'],
        ['slug' => 'eurovignette-co2-tolls',                  'date' => '2025-11-06', 'category' => 'regulations'],
        ['slug' => 'extended-producer-responsibility',        'date' => '2025-08-26', 'category' => 'regulations'],
        ['slug' => 'eu-roadworthiness-rules',                 'date' => '2025-06-15', 'category' => 'regulations'],
        ['slug' => 'smart-tachograph-trailer-data',           'date' => '2025-04-04', 'category' => 'regulations'],
        ['slug' => 'quiet-trailer-noise-rules',               'date' => '2025-01-22', 'category' => 'regulations'],
        ['slug' => 'eu-type-approval-trailers',               'date' => '2024-11-11', 'category' => 'regulations'],
        ['slug' => 'cbam-steel-trailer-costs',                'date' => '2024-08-31', 'category' => 'regulations'],

        // Series 6: logistics & fleet management
        ['slug' => 'trailer-telematics-guide',                'date' => '2026-06-01', 'category' => 'fleet'],
        ['slug' => 'reduce-empty-mileage',                    'date' => '2026-03-21', 'category' => 'fleet'],
        ['slug' => 'trailer-pooling-model',                   'date' => '2026-01-08', 'category' => 'fleet'],
        ['slug' => 'fleet-replacement-age',                   'date' => '2025-10-28', 'category' => 'fleet'],
        ['slug' => 'trailer-tco-calculation',                 'date' => '2025-08-17', 'category' => 'fleet'],
        ['slug' => 'tpms-fuel-savings',                       'date' => '2025-06-06', 'category' => 'fleet'],
        ['slug' => 'drop-and-hook-strategy',                  'date' => '2025-03-26', 'category' => 'fleet'],
        ['slug' => 'cold-chain-data-logging',                 'date' => '2025-01-13', 'category' => 'fleet'],
        ['slug' => 'intermodal-trailer-selection',            'date' => '2024-11-02', 'category' => 'fleet'],
        ['slug' => 'winter-fleet-readiness',                  'date' => '2024-08-22', 'category' => 'fleet'],

        // Series 7: autonomous driving & platooning
        ['slug' => 'what-is-truck-platooning',                'date' => '2026-05-23', 'category' => 'autonomous'],
        ['slug' => 'platooning-fuel-data',                    'date' => '2026-03-12', 'category' => 'autonomous'],
        ['slug' => 'autonomous-ready-trailer',                'date' => '2025-12-30', 'category' => 'autonomous'],
        ['slug' => 'trailer-perception-sensors',              'date' => '2025-10-19', 'category' => 'autonomous'],
        ['slug' => 'autonomous-yard-operations',              'date' => '2025-08-08', 'category' => 'autonomous'],
        ['slug' => 'ebs-autonomous-braking',                  'date' => '2025-05-28', 'category' => 'autonomous'],
        ['slug' => 'v2v-truck-trailer-communication',         'date' => '2025-03-17', 'category' => 'autonomous'],
        ['slug' => 'hub-to-hub-autonomous',                   'date' => '2025-01-04', 'category' => 'autonomous'],
        ['slug' => 'autonomous-liability-insurance',          'date' => '2024-10-24', 'category' => 'autonomous'],
        ['slug' => 'adas-to-full-autonomy',                   'date' => '2024-08-13', 'category' => 'autonomous'],

        // Series 8: sustainability & circular economy
        ['slug' => 'circular-economy-heavy-vehicles',         'date' => '2026-05-14', 'category' => 'sustainability'],
        ['slug' => 'trailer-remanufacturing',                 'date' => '2026-03-03', 'category' => 'sustainability'],
        ['slug' => 'trailer-lifecycle-assessment',            'date' => '2025-12-21', 'category' => 'sustainability'],
        ['slug' => 'recycled-steel-trailers',                 'date' => '2025-10-10', 'category' => 'sustainability'],
        ['slug' => 'green-supply-chain-tenders',              'date' => '2025-07-30', 'category' => 'sustainability'],
        ['slug' => 'end-of-life-trailer-recovery',            'date' => '2025-05-19', 'category' => 'sustainability'],
        ['slug' => 'bio-based-materials-trailers',            'date' => '2025-03-08', 'category' => 'sustainability'],
        ['slug' => 'carbon-reporting-fleets',                 'date' => '2024-12-26', 'category' => 'sustainability'],
        ['slug' => 'low-emission-coatings',                   'date' => '2024-10-15', 'category' => 'sustainability'],
        ['slug' => 'sustainability-certifications',           'date' => '2024-08-04', 'category' => 'sustainability'],

        // Series 9: maintenance, safety & new technologies
        ['slug' => 'predictive-maintenance-trailers',         'date' => '2026-05-05', 'category' => 'maintenance'],
        ['slug' => 'brake-wear-mistakes',                     'date' => '2026-02-22', 'category' => 'maintenance'],
        ['slug' => 'axle-suspension-maintenance',             'date' => '2025-12-12', 'category' => 'maintenance'],
        ['slug' => 'trailer-lighting-electrics',              'date' => '2025-10-01', 'category' => 'maintenance'],
        ['slug' => 'kingpin-wear-measurement',                'date' => '2025-07-21', 'category' => 'maintenance'],
        ['slug' => 'welding-repair-safety',                   'date' => '2025-05-10', 'category' => 'maintenance'],
        ['slug' => 'load-securing-equipment',                 'date' => '2025-02-27', 'category' => 'maintenance'],
        ['slug' => 'ebs-diagnostics',                         'date' => '2024-12-17', 'category' => 'maintenance'],
        ['slug' => 'tire-management-program',                 'date' => '2024-10-06', 'category' => 'maintenance'],
        ['slug' => 'inspection-preparation-checklist',        'date' => '2024-07-26', 'category' => 'maintenance'],

        // Series 10: segment-specific topics (tipper, lowbed, silo, container)
        ['slug' => 'tipper-body-type-guide',                  'date' => '2026-04-26', 'category' => 'segments'],
        ['slug' => 'tipper-tipping-stability',                'date' => '2026-02-13', 'category' => 'segments'],
        ['slug' => 'lowbed-permit-processes',                 'date' => '2025-12-03', 'category' => 'segments'],
        ['slug' => 'extendable-lowbed-wind',                  'date' => '2025-09-22', 'category' => 'segments'],
        ['slug' => 'silo-discharge-optimisation',             'date' => '2025-07-12', 'category' => 'segments'],
        ['slug' => 'food-grade-silo-hygiene',                 'date' => '2025-05-01', 'category' => 'segments'],
        ['slug' => 'multi-size-container-chassis',            'date' => '2025-02-18', 'category' => 'segments'],
        ['slug' => 'curtainsider-en12642-xl',                 'date' => '2024-12-08', 'category' => 'segments'],
        ['slug' => 'reefer-panel-atp',                        'date' => '2024-09-27', 'category' => 'segments'],
        ['slug' => 'platform-project-cargo',                  'date' => '2024-07-17', 'category' => 'segments'],
    ];

    /** Category keys shown in the sidebar (labels come from Blog.cat_*). */
    private const CATEGORIES = [
        'aerodynamics', 'materials', 'electric', 'adr', 'regulations',
        'fleet', 'autonomous', 'sustainability', 'maintenance', 'segments',
    ];

    /** Kayıt defterini tarih sırasıyla (yeniden eskiye) dış tüketicilere açar. */
    public static function registry(): array
    {
        $posts = self::POSTS;
        usort($posts, static fn (array $a, array $b): int => strcmp($b['date'], $a['date']));

        return $posts;
    }

    /**
     * Blog list page with optional search (?q=) and category (?category=) filters.
     */
    public function index(): string
    {
        $query    = trim((string) $this->request->getGet('q'));
        $category = (string) $this->request->getGet('category');

        if (! in_array($category, self::CATEGORIES, true)) {
            $category = '';
        }

        $posts = $this->localizedPosts();

        // Category counts always reflect the full set, not the filtered one.
        $counts = array_count_values(array_column($posts, 'category'));

        if ($category !== '') {
            $posts = array_filter($posts, static fn (array $post): bool => $post['category'] === $category);
        }

        if ($query !== '') {
            $posts = array_filter($posts, static function (array $post) use ($query): bool {
                return mb_stripos($post['title'], $query) !== false
                    || mb_stripos($post['excerpt'], $query) !== false;
            });
        }

        $data = [
            'metaTitle'       => lang('Blog.meta_title'),
            'metaDescription' => lang('Blog.meta_description'),
            'posts'           => array_values($posts),
            'categories'      => self::CATEGORIES,
            'categoryCounts'  => $counts,
            'activeCategory'  => $category,
            'searchQuery'     => $query,
        ];

        return view('pages/blog/index', $data);
    }

    /**
     * Blog detail page with a 3-card "related posts" section
     * (same category first, then most recent).
     */
    public function detail(string $slug): string
    {
        $this->cachePage(3600);

        $posts = $this->localizedPosts();
        $post  = null;

        foreach ($posts as $candidate) {
            if ($candidate['slug'] === $slug) {
                $post = $candidate;
                break;
            }
        }

        if ($post === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $others  = array_filter($posts, static fn (array $p): bool => $p['slug'] !== $slug);
        $related = array_filter($others, static fn (array $p): bool => $p['category'] === $post['category']);

        foreach ($others as $candidate) {
            if (count($related) >= 3) {
                break;
            }
            if ($candidate['category'] !== $post['category']) {
                $related[] = $candidate;
            }
        }

        $data = [
            'metaTitle'       => $post['title'],
            'metaDescription' => $post['excerpt'],
            'post'            => $post,
            'related'         => array_slice(array_values($related), 0, 3),
        ];

        return view('pages/blog/detail', $data);
    }

    /**
     * Merges the registry with the current locale's texts and adds a
     * pre-formatted, localized publication date.
     *
     * @return list<array<string, mixed>>
     */
    private function localizedPosts(): array
    {
        $texts  = lang('Blog.posts');
        $merged = [];

        foreach (self::registry() as $meta) {
            $text = $texts[$meta['slug']] ?? null;

            if ($text === null) {
                continue; // Not translated yet: hide instead of breaking the page.
            }

            $merged[] = $meta + $text + [
                'dateFormatted' => $this->formatDate($meta['date']),
                'categoryLabel' => lang('Blog.cat_' . $meta['category']),
            ];
        }

        return $merged;
    }

    /**
     * Formats a Y-m-d date with localized month names (no intl dependency).
     */
    private function formatDate(string $date): string
    {
        [$year, $month, $day] = explode('-', $date);
        $months = lang('Blog.months');
        $name   = $months[(int) $month - 1] ?? $month;

        return current_locale() === 'en'
            ? sprintf('%s %d, %s', $name, (int) $day, $year)
            : sprintf('%d %s %s', (int) $day, $name, $year);
    }
}
