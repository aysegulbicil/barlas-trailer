<?php

namespace App\Libraries;

/**
 * ProductCatalog
 *
 * File-based product catalogue (no database). The data lives in
 * app/Data/products.json and is generated from the master document
 * "TUM_URUNLER_HIYERARSIK_KATALOG" — 11 main categories, 76 products and
 * 136 model variants with their complete technical sheets.
 *
 * All products are presented as Barlas products: the source document's
 * reference manufacturers are scrubbed at generation time and never
 * reach this layer (see the converter notes in app/Data).
 *
 * The JSON is the single source of truth for product CONTENT (names,
 * aliases, technical specifications). UI labels around it (buttons,
 * section titles, filters) come from the Products language file so the
 * chrome is fully localized while the technical content stays exactly
 * as published in the source catalogue.
 *
 * LOCALIZATION — Turkish base + per-locale overlays. products.json is
 * the Turkish master; an optional app/Data/products.{locale}.json
 * overlay carries translated category/product names, variant model
 * names and (optionally) translated spec lines. Overlays are merged by
 * slug — slugs are identical across locales (same rule as wiki/news
 * content), so URLs never change. Anything missing from an overlay
 * (a product, a variant's specs, aliases) silently falls back to the
 * Turkish master, mirroring the MarkdownContent tr-fallback policy.
 * Aliases from overlays are ADDED to the Turkish ones (they feed the
 * AI assistant's matcher, so both languages keep working).
 *
 * Spec block types inside each variant's "specs" list (order preserved):
 *   h  = explicit group heading from the source document
 *   g  = inferred group title (short label line in the source)
 *   li = bulleted specification line
 *   p  = plain text line
 */
final class ProductCatalog
{
    /** @var array<string, array> per-locale memoized catalogue */
    private static array $data = [];

    /** Loads and memoizes the catalogue for the given/request locale. */
    private static function data(?string $locale = null): array
    {
        $locale ??= self::requestLocale();

        if (! isset(self::$data[$locale])) {
            $base = self::readJson(APPPATH . 'Data/products.json');

            if ($locale !== 'tr') {
                $overlay = self::readJson(APPPATH . 'Data/products.' . $locale . '.json');
                if ($overlay !== []) {
                    $base = self::localize($base, $overlay);
                }
            }

            self::$data[$locale] = $base === [] ? ['categories' => []] : $base;
        }

        return self::$data[$locale];
    }

    /** Current request locale; safe under CLI (spark) where there is none. */
    private static function requestLocale(): string
    {
        $request = service('request');

        return method_exists($request, 'getLocale')
            ? $request->getLocale()
            : (config('App')->defaultLocale ?? 'tr');
    }

    private static function readJson(string $file): array
    {
        $json = is_file($file) ? file_get_contents($file) : '';
        $data = json_decode($json ?: '{}', true);

        return is_array($data) ? $data : [];
    }

    /**
     * Merges a translation overlay onto the Turkish master by slug.
     * Overlay entries may carry: category name, product name/aliases,
     * variant model and specs. Missing pieces keep the Turkish text.
     */
    private static function localize(array $base, array $overlay): array
    {
        // NOTE: by-ref foreach must run on the variable itself — a
        // `$x ?? []` expression iterates a silent temporary copy and
        // every write is lost. Guard with is_array() instead.
        if (! is_array($base['categories'] ?? null)) {
            return $base;
        }

        $oCats = array_column($overlay['categories'] ?? [], null, 'slug');

        foreach ($base['categories'] as &$cat) {
            $oCat = $oCats[$cat['slug']] ?? null;
            if ($oCat === null) {
                continue;
            }
            if (! empty($oCat['name'])) {
                $cat['name'] = $oCat['name'];
            }
            if (! is_array($cat['products'] ?? null)) {
                continue;
            }

            $oProds = array_column($oCat['products'] ?? [], null, 'slug');
            foreach ($cat['products'] as &$prod) {
                $oProd = $oProds[$prod['slug']] ?? null;
                if ($oProd === null) {
                    continue;
                }
                if (! empty($oProd['name'])) {
                    $prod['name'] = $oProd['name'];
                }
                if (! empty($oProd['aliases'])) {
                    $prod['aliases'] = array_values(array_unique(array_merge(
                        (array) ($prod['aliases'] ?? []),
                        (array) $oProd['aliases'],
                    )));
                }
                if (! is_array($prod['variants'] ?? null)) {
                    continue;
                }

                $oVars = array_column($oProd['variants'] ?? [], null, 'slug');
                foreach ($prod['variants'] as &$var) {
                    $oVar = $oVars[$var['slug']] ?? null;
                    if ($oVar === null) {
                        continue;
                    }
                    if (! empty($oVar['model'])) {
                        $var['model'] = $oVar['model'];
                    }
                    if (! empty($oVar['specs'])) {
                        $var['specs'] = $oVar['specs'];
                    }
                }
                unset($var);
            }
            unset($prod);
        }
        unset($cat);

        return $base;
    }

    /** @return list<array<string, mixed>> all 11 categories in catalogue order */
    public static function categories(): array
    {
        return self::data()['categories'] ?? [];
    }

    /** Finds one category by slug. */
    public static function category(string $slug): ?array
    {
        foreach (self::categories() as $category) {
            if ($category['slug'] === $slug) {
                return $category;
            }
        }

        return null;
    }

    /** Finds one product by category + product slug. */
    public static function product(string $categorySlug, string $productSlug): ?array
    {
        $category = self::category($categorySlug);
        if ($category === null) {
            return null;
        }

        foreach ($category['products'] as $product) {
            if ($product['slug'] === $productSlug) {
                return $product;
            }
        }

        return null;
    }

    /**
     * Featured products of a category: the N products with the most
     * model variants (ties keep catalogue order). Used by the header
     * mega menu chips.
     *
     * @return list<array<string, mixed>>
     */
    public static function featured(array $category, int $limit = 4): array
    {
        $products = $category['products'] ?? [];
        $indexed  = [];

        foreach ($products as $i => $product) {
            $indexed[] = ['i' => $i, 'p' => $product];
        }

        usort($indexed, static function (array $a, array $b): int {
            $diff = count($b['p']['variants']) <=> count($a['p']['variants']);

            return $diff !== 0 ? $diff : ($a['i'] <=> $b['i']);
        });

        $featured = array_column(array_slice($indexed, 0, $limit), 'p');

        // Present featured chips in catalogue order, not popularity order.
        usort($featured, static fn (array $a, array $b): int => strnatcmp($a['no'], $b['no']));

        return $featured;
    }

    /** Total number of variants in a category. */
    public static function variantCount(array $category): int
    {
        $count = 0;
        foreach ($category['products'] ?? [] as $product) {
            $count += count($product['variants']);
        }

        return $count;
    }

    /**
     * Groups a variant's flat spec lines into titled sections for
     * rendering. Content is passed through verbatim; only the grouping
     * implied by the source document's headings is applied.
     *
     * @return list<array{title: string, items: list<array{t: string, x: string}>}>
     */
    public static function specSections(array $variant): array
    {
        $sections = [];
        $current  = ['title' => '', 'items' => []];

        foreach ($variant['specs'] ?? [] as $line) {
            if ($line['t'] === 'h' || $line['t'] === 'g') {
                if ($current['title'] !== '' || $current['items'] !== []) {
                    $sections[] = $current;
                }
                $current = ['title' => $line['x'], 'items' => []];
                continue;
            }
            $current['items'][] = $line;
        }

        if ($current['title'] !== '' || $current['items'] !== []) {
            $sections[] = $current;
        }

        return $sections;
    }

    /**
     * Display-ready sections with a NORMALIZED, uniform layout.
     *
     * The source sheets come from differently formatted documents, so a
     * raw render looks inconsistent (some variants are "Key: value"
     * paragraphs, others bullet lists, others many one-line groups).
     * This pass maps everything onto three uniform block types without
     * changing any wording:
     *
     *   kv    "Key: value" plain lines → aligned spec rows. Runs of 2+
     *         one-line titled groups (title + single short value) are
     *         merged into one key/value grid, the titles becoming keys.
     *   list  bulleted lines → one bullet list per run.
     *   text  anything else → plain paragraph.
     *
     * @return list<array{title: string, merged: bool, blocks: list<array{0: string, 1: mixed}>}>
     */
    public static function displaySections(array $variant): array
    {
        $raw = self::specSections($variant);

        // Mark "tiny" sections: a title with exactly one short plain line.
        $marked = [];
        foreach ($raw as $section) {
            $tiny = $section['title'] !== ''
                && count($section['items']) === 1
                && $section['items'][0]['t'] === 'p'
                && mb_strlen($section['items'][0]['x']) <= 110;
            $marked[] = ['tiny' => $tiny, 's' => $section];
        }

        // Merge runs of 2+ tiny sections into a single key/value grid.
        $out = [];
        $i   = 0;
        $n   = count($marked);
        while ($i < $n) {
            if ($marked[$i]['tiny']) {
                $j = $i;
                while ($j < $n && $marked[$j]['tiny']) {
                    $j++;
                }
                if ($j - $i >= 2) {
                    $rows = [];
                    for ($k = $i; $k < $j; $k++) {
                        $rows[] = [$marked[$k]['s']['title'], $marked[$k]['s']['items'][0]['x']];
                    }
                    $out[] = ['title' => '', 'merged' => true, 'blocks' => [['kv', $rows]]];
                    $i = $j;
                    continue;
                }
            }
            $out[] = self::sectionBlocks($marked[$i]['s']);
            $i++;
        }

        return $out;
    }

    /** Builds the uniform block list of one section (order preserved). */
    private static function sectionBlocks(array $section): array
    {
        $blocks = [];

        foreach ($section['items'] as $line) {
            if ($line['t'] === 'li') {
                if ($blocks === [] || $blocks[array_key_last($blocks)][0] !== 'list') {
                    $blocks[] = ['list', []];
                }
                $blocks[array_key_last($blocks)][1][] = $line['x'];
                continue;
            }

            // Plain "Key: value" line → spec row (key short, no URLs).
            if (preg_match('/^([^:]{1,60}?)\h*:\h*(\S.*)$/u', $line['x'], $m)
                && ! str_contains($m[1], 'http') && ! str_contains($m[2], '//')) {
                if ($blocks === [] || $blocks[array_key_last($blocks)][0] !== 'kv') {
                    $blocks[] = ['kv', []];
                }
                $blocks[array_key_last($blocks)][1][] = [$m[1], $m[2]];
                continue;
            }

            $blocks[] = ['text', $line['x']];
        }

        return ['title' => $section['title'], 'merged' => false, 'blocks' => $blocks];
    }

    /**
     * Title/key keywords routing content into the three master groups.
     * Turkish (master) + English (products.en.json overlay specs); other
     * locales keep Turkish specs for now, so no further words needed.
     */
    private const DIM_WORDS = [
        'ölçü', 'olcu', 'boyut', 'genişlik', 'genislik', 'yükseklik', 'yukseklik',
        'uzunluk', 'dingil mesafe', 'king-pin', 'king pin', 'kingpin', 'ağırlık',
        'agirlik', 'çap', 'cap (', '5.teker', 'teker yüksekliği', 'mesafesi',
        'dimension', 'width', 'height', 'length', 'axle spread', 'axle spacing',
        'wheelbase', 'weight', 'diameter', 'distance', 'fifth wheel',
    ];
    private const CAP_WORDS = [
        'kapasite', 'hacim', 'bölme', 'bolme', 'tonaj',
        'capacity', 'volume', 'compartment', 'tonnage', 'payload',
    ];

    /** Generic titles that would only duplicate the master heading. */
    private const GENERIC_TITLES = [
        'teknik özellikler', 'teknik bilgi', 'teknik bilgiler', 'teknik ölçüler',
        'ölçüler', 'kapasite', 'genel', 'genel özellikler',
        'technical specifications', 'technical data', 'technical details',
        'technical dimensions', 'dimensions', 'capacity', 'general',
        'general features',
    ];

    private static function classify(string $text): string
    {
        $t = mb_strtolower($text);
        foreach (self::CAP_WORDS as $w) {
            if (str_contains($t, $w)) {
                return 'capacity';
            }
        }
        foreach (self::DIM_WORDS as $w) {
            if (str_contains($t, $w)) {
                return 'dimensions';
            }
        }
        return 'info';
    }

    /**
     * The whole sheet folded into AT MOST three master sections, the
     * same on every product page:
     *
     *   dimensions  Teknik Ölçüler   (widths, heights, axle spreads…)
     *   capacity    Teknik Kapasite  (volumes, compartments, tonnage…)
     *   info        Teknik Bilgi     (chassis, brakes, equipment… with
     *                                 small inline sub-headings)
     *
     * Source sub-headings survive as inline 'sub' blocks; "Key: value"
     * rows are routed by their key. Wording stays verbatim.
     *
     * @return list<array{key: string, blocks: list<array{0: string, 1: mixed}>}>
     */
    public static function masterSections(array $variant): array
    {
        $masters = ['dimensions' => [], 'capacity' => [], 'info' => []];

        $pushKv = static function (string $master, array $row) use (&$masters): void {
            $last = array_key_last($masters[$master]);
            if ($last === null || $masters[$master][$last][0] !== 'kv') {
                $masters[$master][] = ['kv', []];
                $last = array_key_last($masters[$master]);
            }
            $masters[$master][$last][1][] = $row;
        };

        foreach (self::displaySections($variant) as $section) {
            // Untitled or merged one-liner grids: route row by row / block by block.
            if ($section['title'] === '') {
                foreach ($section['blocks'] as $block) {
                    if ($block[0] === 'kv') {
                        foreach ($block[1] as $row) {
                            $pushKv(self::classify($row[0]), $row);
                        }
                    } else {
                        $masters['info'][] = $block;
                    }
                }
                continue;
            }

            // Titled section: route as a whole, keep its name as a sub-heading
            // unless it would just repeat the master title.
            $master  = self::classify($section['title']);
            $generic = in_array(mb_strtolower($section['title']), self::GENERIC_TITLES, true);

            if ($section['blocks'] === []) {
                // Source parent heading without own lines: keep as label only.
                if (! $generic) {
                    $masters[$master][] = ['sub', $section['title']];
                }
                continue;
            }

            if (! $generic) {
                $masters[$master][] = ['sub', $section['title']];
            }
            foreach ($section['blocks'] as $block) {
                $masters[$master][] = $block;
            }
        }

        $out = [];
        foreach (['dimensions', 'capacity', 'info'] as $key) {
            if ($masters[$key] !== []) {
                $out[] = ['key' => $key, 'blocks' => $masters[$key]];
            }
        }

        return $out;
    }
}
