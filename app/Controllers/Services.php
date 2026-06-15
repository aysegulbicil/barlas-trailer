<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * Services controller
 *
 * File-based services (no database). Locale-independent metadata (slug,
 * icon path, cover image, language key) lives in the SERVICES registry
 * below; all localized text (name, lead, body, points) comes from the
 * Navigation language file (service names, reused from the mega menu)
 * and the Services language file (longer per-service copy), keyed by
 * slug. Slugs/URLs stay identical across locales so the mega menu links
 * and the language switcher keep working on detail pages.
 */
class Services extends BaseController
{
    /**
     * Service registry, in menu order. "key" maps to Navigation.srv_*
     * (name + one-line desc, already translated in all locales). "icon"
     * is the same single-path SVG used by the mega menu. "image" maps to
     * public/assets/images/services/{image}; until the file exists the
     * media-frame placeholder is shown (same pattern as the blog).
     */
    private const SERVICES = [
        ['slug' => 'trailer-repair',          'key' => 'trailer_repair',          'image' => 'srv-trailer-repair.jpg',          'icon' => 'M14.5 6.5a4 4 0 11-5 5L4 17l3 3 5.5-5.5a4 4 0 002-8z'],
        ['slug' => 'frigo-repair',            'key' => 'frigo_repair',            'image' => 'srv-frigo-repair.jpg',            'icon' => 'M12 3v18 M5 7l14 10 M19 7L5 17'],
        ['slug' => 'silobas-repair',          'key' => 'silobas_repair',          'image' => 'srv-silobas-repair.jpg',          'icon' => 'M5 21V9l7-5 7 5v12 M9 21v-6h6v6'],
        ['slug' => 'curtain-repair',          'key' => 'curtain_repair',          'image' => 'srv-curtain-repair.jpg',          'icon' => 'M4 6h16v12H4z M8 6v12 M12 6v12 M16 6v12'],
        ['slug' => 'adr-tanker-repair',       'key' => 'adr_tanker_repair',       'image' => 'srv-adr-tanker-repair.jpg',       'icon' => 'M12 4l9 15H3z M12 10v4 M12 17h.01'],
        ['slug' => 'lowbed-repair',           'key' => 'lowbed_repair',           'image' => 'srv-lowbed-repair.jpg',           'icon' => 'M2 15h20 M4 15v-4h10l3 4 M6 18a1.5 1.5 0 100-3'],
        ['slug' => 'aluminum-silobas-repair', 'key' => 'aluminum_silobas_repair', 'image' => 'srv-aluminum-silobas-repair.jpg', 'icon' => 'M5 21V9l7-5 7 5v12 M9 13l3 3 3-3'],
        ['slug' => 'fuel-tank-repair-center', 'key' => 'fuel_center',             'image' => 'srv-fuel-center.jpg',             'icon' => 'M6 21V4h8v17 M6 21h8 M14 8h3v9a2 2 0 11-4 0'],
        ['slug' => 'axle-repair',             'key' => 'axle_repair',             'image' => 'srv-axle-repair.jpg',             'icon' => 'M2 12h20 M7 14.5a2.5 2.5 0 100-5 M17 14.5a2.5 2.5 0 100-5'],
        ['slug' => 'suspension-repair',       'key' => 'suspension_repair',       'image' => 'srv-suspension-repair.jpg',       'icon' => 'M4 17c2-3 4 3 6 0s4 3 6 0 4 3 4 0 M4 7h16'],
    ];

    /**
     * Services list page: hero + a card grid of every service.
     */
    public function index(): string
    {
        $data = [
            'metaTitle'       => lang('Services.meta_title'),
            'metaDescription' => lang('Services.meta_description'),
            'services'        => $this->localizedServices(),
        ];

        return view('pages/services/index', $data);
    }

    /**
     * Service detail page: hero + cover image + general copy + points,
     * with a small "related services" rail (the neighbours in the list).
     */
    public function detail(string $slug): string
    {
        $services = $this->localizedServices();
        $service  = null;
        $index    = null;

        foreach ($services as $i => $candidate) {
            if ($candidate['slug'] === $slug) {
                $service = $candidate;
                $index   = $i;
                break;
            }
        }

        if ($service === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        // Related = up to 3 other services, starting after the current one
        // and wrapping around, so every page shows a varied rail.
        $related = [];
        $count   = count($services);
        for ($step = 1; $step <= $count - 1 && count($related) < 3; $step++) {
            $related[] = $services[($index + $step) % $count];
        }

        $data = [
            'metaTitle'       => $service['name'] . ' — ' . lang('Navigation.services'),
            'metaDescription' => $service['lead'] !== '' ? $service['lead'] : $service['desc'],
            'service'         => $service,
            'related'         => $related,
        ];

        return view('pages/services/detail', $data);
    }

    /**
     * Merges the registry with the current locale's texts: the name and
     * one-line description come from Navigation (shared with the mega
     * menu); the longer lead/body/points come from the Services file.
     *
     * @return list<array<string, mixed>>
     */
    private function localizedServices(): array
    {
        $copy   = lang('Services.items');
        $copy   = is_array($copy) ? $copy : [];
        $merged = [];

        foreach (self::SERVICES as $meta) {
            $text = $copy[$meta['slug']] ?? [];

            $merged[] = $meta + [
                'name'   => lang('Navigation.srv_' . $meta['key']),
                'desc'   => lang('Navigation.srv_' . $meta['key'] . '_desc'),
                'lead'   => $text['lead'] ?? '',
                'body'   => $text['body'] ?? '',
                'points' => is_array($text['points'] ?? null) ? $text['points'] : [],
            ];
        }

        return $merged;
    }
}
