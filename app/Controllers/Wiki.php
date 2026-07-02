<?php

namespace App\Controllers;

use App\Libraries\MarkdownContent;
use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * Wiki controller — Treyler Ansiklopedisi (trafik motorları planının
 * "sektörün Vikipedisi" maddesi). İçerik front-matter'lı Markdown
 * dosyalarından gelir (app/Data/content/wiki/{locale}/); yeni madde
 * eklemek = 5 dilde .md dosyası bırakmak, kod değişikliği gerekmez.
 */
class Wiki extends BaseController
{
    public function index(): string
    {
        $this->cachePage(3600);

        return view('pages/wiki/index', [
            'metaTitle'       => lang('Wiki.meta_title'),
            'metaDescription' => lang('Wiki.meta_description'),
            'items'           => MarkdownContent::items('wiki', current_locale()),
        ]);
    }

    public function detail(string $slug): string
    {
        $this->cachePage(3600);

        $item = MarkdownContent::item('wiki', current_locale(), $slug);
        if ($item === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        // İlgili maddeler: aynı listeden kendisi hariç ilk 3.
        $related = array_slice(
            array_filter(
                MarkdownContent::items('wiki', current_locale()),
                static fn (array $i): bool => $i['slug'] !== $slug
            ),
            0,
            3
        );

        return view('pages/wiki/detail', [
            'metaTitle'       => $item['title'],
            'metaDescription' => $item['description'],
            'item'            => $item,
            'related'         => $related,
        ]);
    }
}
