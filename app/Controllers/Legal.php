<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * Legal controller — şeffaflık sayfaları (ekosistem planı §10:
 * "Verileriniz nasıl korunuyor" sade anlatım; güven = satış).
 *
 * İçerik app/Language/{locale}/Legal.php dosyalarından gelir; her sayfa
 * ['title', 'intro', 'sections' => [['h', 'p' => [...]]]] yapısındadır.
 * Footer'daki KVKK / Gizlilik / Çerez bağlantıları buraya bağlanır.
 */
class Legal extends BaseController
{
    private const PAGES = ['kvkk', 'privacy', 'cookies'];

    public function page(string $key): string
    {
        if (! in_array($key, self::PAGES, true)) {
            throw PageNotFoundException::forPageNotFound();
        }

        $this->cachePage(3600);

        $page = lang('Legal.' . $key);
        if (! is_array($page)) {
            throw PageNotFoundException::forPageNotFound();
        }

        return view('pages/legal', [
            'metaTitle'       => (string) ($page['title'] ?? ''),
            'metaDescription' => (string) ($page['intro'] ?? ''),
            'page'            => $page,
        ]);
    }
}
