<?php

namespace App\Controllers;

use App\Libraries\MarkdownContent;
use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * News controller — üretim haberleri (§7.6 AI İçerik Fabrikası'nın kamusal
 * yüzü). İçerik akışı: agents:content-factory taslak üretir → Ana Admin
 * panelde onaylar → onay, app/Data/content/news/tr/{slug}.md dosyasını
 * yazar → bu sayfa MarkdownContent motoruyla yayınlar.
 *
 * Çeviri dosyası olmayan dillerde motor tr içeriğe düşer (MarkdownContent
 * sözleşmesi); AI çeviri hattı (§3) kurulunca diğer diller otomatik dolar.
 */
class News extends BaseController
{
    public function index(): string
    {
        $this->cachePage(3600);

        return view('pages/news/index', [
            'metaTitle'       => lang('News.meta_title'),
            'metaDescription' => lang('News.meta_description'),
            'items'           => MarkdownContent::items('news', current_locale()),
        ]);
    }

    public function detail(string $slug): string
    {
        $this->cachePage(3600);

        $item = MarkdownContent::item('news', current_locale(), $slug);
        if ($item === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return view('pages/news/detail', [
            'metaTitle'       => $item['title'],
            'metaDescription' => $item['description'],
            'item'            => $item,
        ]);
    }
}
