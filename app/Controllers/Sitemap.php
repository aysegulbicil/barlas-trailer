<?php

namespace App\Controllers;

use App\Libraries\ProductCatalog;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Sitemap + robots controller
 *
 * File-based site: the URL inventory derives from the same registries the
 * pages themselves render from (ProductCatalog, Blog::registry(),
 * Services::registry()), so the sitemap cannot drift from the real routes.
 * Every path is listed once per locale, each entry carrying the full
 * hreflang alternate set (x-default included) — mirrors partials/meta.php.
 */
class Sitemap extends BaseController
{
    public function index(): ResponseInterface
    {
        $locales = supported_locales();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"'
            . ' xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";

        foreach ($this->paths() as $path => $lastmod) {
            $alternates = '';
            foreach ($locales as $alt) {
                $alternates .= '    <xhtml:link rel="alternate" hreflang="' . $alt . '" href="' . $this->url($alt, $path) . '"/>' . "\n";
            }
            $alternates .= '    <xhtml:link rel="alternate" hreflang="x-default" href="' . $this->url(default_locale(), $path) . '"/>' . "\n";

            foreach ($locales as $locale) {
                $xml .= "  <url>\n"
                    . '    <loc>' . $this->url($locale, $path) . "</loc>\n"
                    . $alternates
                    . ($lastmod !== null ? '    <lastmod>' . $lastmod . "</lastmod>\n" : '')
                    . "  </url>\n";
            }
        }

        return $this->response
            ->setContentType('application/xml')
            ->setBody($xml . '</urlset>' . "\n");
    }

    /**
     * robots.txt — Sitemap satırı mutlak URL ister; ortama göre doğru host
     * üretilsin diye statik dosya yerine buradan servis edilir.
     */
    public function robots(): ResponseInterface
    {
        $body = "User-agent: *\n"
            . "Allow: /\n\n"
            . "# Çerez kuran dil değiştirme yönlendirmeleri taranmasın\n"
            . "Disallow: /lang/\n\n"
            . 'Sitemap: ' . base_url('sitemap.xml') . "\n";

        return $this->response->setContentType('text/plain')->setBody($body);
    }

    /** @return array<string, string|null> locale önekinden sonraki path => lastmod */
    private function paths(): array
    {
        $paths = [
            ''         => null,
            'products' => null,
            'services' => null,
            'blog'     => null,
            'media'    => null,
            'tools'    => null,
            'quiz'     => null,
            'faq'      => null,
            'games'        => null,
            'games/tetris' => null,
            'games/daily'  => null,
            'kvkk'     => null,
            'privacy'  => null,
            'cookies'  => null,
            'teklif'   => null,
            'contact'  => null,
        ];

        foreach (Blog::registry() as $post) {
            $paths['blog/' . $post['slug']] = $post['date'];
        }

        foreach (Services::registry() as $service) {
            $paths['services/' . $service['slug']] = null;
        }

        foreach (ProductCatalog::categories() as $category) {
            $paths['products/' . $category['slug']] = null;

            foreach ($category['products'] ?? [] as $product) {
                $paths['products/' . $category['slug'] . '/' . $product['slug']] = null;
            }
        }

        return $paths;
    }

    private function url(string $locale, string $path): string
    {
        return htmlspecialchars(base_url(trim($locale . '/' . $path, '/')), ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
