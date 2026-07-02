<?php

namespace App\Controllers;

/**
 * Faq controller — SSS motoru (ekosistem planı §7.5).
 *
 * İçerik app/Language/{locale}/Faq.php dosyalarından gelir (items:
 * [['q', 'a'], ...]); sayfa FAQPage JSON-LD şemasıyla yayınlanır.
 * Yeni soru adayları asistan loglarından "agents:faq-candidates"
 * komutuyla derlenir (writable/data/faq-candidates.json) — onaylanan
 * sorular bu dil dosyalarına eklenir.
 */
class Faq extends BaseController
{
    public function index(): string
    {
        $this->cachePage(3600);

        $items = lang('Faq.items');
        $items = is_array($items) ? $items : [];

        return view('pages/faq', [
            'metaTitle'       => lang('Faq.meta_title'),
            'metaDescription' => lang('Faq.meta_description'),
            'items'           => $items,
        ]);
    }
}
