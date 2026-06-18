<?php

namespace App\Controllers;

use App\Libraries\ProductCatalog;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Quote (Teklif) controller
 *
 * Ürün-bağlamlı teklif akışı. Tüm "Teklif Al" butonları buraya gelir:
 *
 *   index()                              → ürün seçtirme sayfası. Header / CTA
 *                                          gibi ürünü belli olmayan butonlar
 *                                          buraya düşer.
 *   start({kategori}/{ürün}[/{varyant}]) → ürünü tanır, yerelleştirilmiş bir
 *                                          WhatsApp mesajı kurar, niyeti hafifçe
 *                                          loglar ve kullanıcıyı wa.me'ye
 *                                          yönlendirir.
 *
 * Tasarım notu — depolama: Burada geniş çaplı bir teklif YÖNETİMİ yok. Asıl
 * kayıt WhatsApp sohbetidir. Sunucu tarafında yalnızca hafif bir niyet logu
 * (writable/quotes/YYYY-MM.jsonl) tutulur; bu, hangi ürünün ne sıklıkta teklif
 * aldığını görmek içindir ve form/leads desenini birebir taklit eder.
 *
 * Görsel notu — wa.me click-to-chat yalnızca METİN ön-doldurur; görsel dosyası
 * eklenemez. Mesaja ürün URL'si konur; WhatsApp, ürün sayfasının og:image'inden
 * otomatik bir önizleme kartı üretir (bkz. partials/meta.php $metaImage).
 */
class Quote extends BaseController
{
    /** Bu controller wa.me bağlantısını kurmak için whatsapp helper'ını kullanır. */
    protected $helpers = ['url', 'locale', 'whatsapp'];

    /** Ürün seçtirme sayfası — ürünü belli olmayan teklif butonları buraya gelir. */
    public function index(): string
    {
        return view('pages/quote/index', [
            'metaTitle'       => lang('Quote.meta_title'),
            'metaDescription' => lang('Quote.meta_description'),
            'categories'      => ProductCatalog::categories(),
        ]);
    }

    /**
     * Ürün-bağlamlı teklif: ürünü doğrular, mesajı kurar, WhatsApp'a yönlendirir.
     */
    public function start(string $categorySlug, string $productSlug, ?string $variantSlug = null): RedirectResponse
    {
        $category = ProductCatalog::category($categorySlug);
        $product  = ProductCatalog::product($categorySlug, $productSlug);

        if ($category === null || $product === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        // Opsiyonel varyant: slug eşleşirse model adını mesaja ekleriz.
        $variantName = '';
        if ($variantSlug !== null && ! empty($product['variants'])) {
            foreach ($product['variants'] as $variant) {
                if (($variant['slug'] ?? '') === $variantSlug) {
                    $variantName = (string) ($variant['model'] ?? '');
                    break;
                }
            }
        }

        $productUrl = locale_url('products/' . $categorySlug . '/' . $productSlug);

        $this->logIntent($category, $product, $variantName, $productUrl); // en iyi çaba

        $message = $this->buildMessage(
            (string) $product['name'],
            $variantName,
            $this->categoryName($category),
            $productUrl
        );

        return redirect()->to(whatsapp_url($message));
    }

    /** Yerelleştirilmiş WhatsApp mesajını şablondan kurar (ICU'dan bağımsız). */
    private function buildMessage(string $product, string $variant, string $category, string $url): string
    {
        $label    = $variant !== '' ? $product . ' — ' . $variant : $product;
        $template = (string) lang('Quote.wa_message');

        return str_replace(
            ['{product}', '{category}', '{url}'],
            [$label, $category, $url],
            $template
        );
    }

    /** Kategorinin yerelleştirilmiş adı (çeviri yoksa katalog adına düşer). */
    private function categoryName(array $category): string
    {
        $key   = 'Navigation.cat_' . str_replace('-', '_', $category['slug']);
        $label = lang($key);

        return $label === $key ? (string) $category['name'] : $label;
    }

    /** Teklif niyetini aylık JSONL dosyasına ekler (writable/quotes). */
    private function logIntent(array $category, array $product, string $variant, string $url): void
    {
        $dir = WRITEPATH . 'quotes';
        if (! is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        $row = [
            'time'     => date('c'),
            'locale'   => current_locale(),
            'category' => $category['slug'],
            'product'  => $product['slug'],
            'name'     => $product['name'],
            'variant'  => $variant,
            'url'      => $url,
            'ip'       => $this->request->getIPAddress(),
        ];

        $line = json_encode($row, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($line !== false) {
            @file_put_contents($dir . '/' . date('Y-m') . '.jsonl', $line . "\n", FILE_APPEND | LOCK_EX);
        }
    }
}
