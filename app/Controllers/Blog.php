<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * Blog controller
 *
 * File-based blog (no database). Locale-independent post metadata
 * (slug, date, category, cover image) lives in the POSTS registry below;
 * all localized text (title, excerpt, body) lives in the Blog language
 * files keyed by slug, so slugs/URLs stay identical across locales and
 * the language switcher keeps working on detail pages.
 */
class Blog extends BaseController
{
    /**
     * Post registry, newest first. Each entry's "image" maps to
     * public/assets/images/blog/{image}; until the file exists the
     * media-frame placeholder is shown (same pattern as the homepage).
     */
    private const POSTS = [
        ['slug' => 'adr-tanker-safety-standards',   'date' => '2026-05-18', 'category' => 'safety',      'image' => 'blog-adr-safety.jpg'],
        ['slug' => 'silo-trailer-maintenance-tips', 'date' => '2026-04-22', 'category' => 'maintenance', 'image' => 'blog-silo-maintenance.jpg'],
        ['slug' => 'lowbed-heavy-transport-guide',  'date' => '2026-03-30', 'category' => 'industry',    'image' => 'blog-lowbed-guide.jpg'],
        ['slug' => 'lightweight-chassis-technology', 'date' => '2026-03-05', 'category' => 'technology', 'image' => 'blog-lightweight-chassis.jpg'],
        ['slug' => 'reefer-cold-chain-logistics',   'date' => '2026-02-10', 'category' => 'technology',  'image' => 'blog-cold-chain.jpg'],
        ['slug' => 'trailer-winter-maintenance',    'date' => '2026-01-15', 'category' => 'maintenance', 'image' => 'blog-winter-maintenance.jpg'],
    ];

    /** Category keys shown in the sidebar (labels come from Blog.cat_*). */
    private const CATEGORIES = ['industry', 'maintenance', 'safety', 'technology'];

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

        foreach (self::POSTS as $meta) {
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
