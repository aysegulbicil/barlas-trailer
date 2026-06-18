<?php

declare(strict_types=1);

namespace App\Libraries;

use Config\Ai as AiConfig;

/**
 * AiAssistant - YEREL (kredisiz) ürün & hizmet asistanı.
 *
 * Dış API YOKTUR. Soruyu kendi verimizden cevaplar:
 *   - Ürünler   -> app/Data/products.json (ProductCatalog)
 *   - Hizmetler  -> aşağıdaki SERVICES kaydı + Navigation dil dosyası
 *   - Çok dillilik -> app/Data/ai-synonyms.php (yabancı/argo kelime eşlemesi)
 *
 * Akış: soru jetonlara ayrılır, eş anlamlılarla genişletilir, ürün ve
 * hizmet kataloğunda puanlanır; niyete göre ziyaretçinin dilinde şablon bir
 * cevap + "Ürüne/Hizmete Git" kartları + iletişim (lead) butonları döner.
 * Emin olunmayan hiçbir şey UYDURULMAZ; eşleşme yoksa iletişime yönlendirir.
 */
final class AiAssistant
{
    private AiConfig $config;
    private static ?array $synCache = null;

    /** Hizmet kaydı (slug + Navigation anahtarı). Adlar dile göre lang'den gelir. */
    private const SERVICES = [
        ['slug' => 'trailer-repair',          'key' => 'trailer_repair'],
        ['slug' => 'frigo-repair',            'key' => 'frigo_repair'],
        ['slug' => 'silobas-repair',          'key' => 'silobas_repair'],
        ['slug' => 'curtain-repair',          'key' => 'curtain_repair'],
        ['slug' => 'adr-tanker-repair',       'key' => 'adr_tanker_repair'],
        ['slug' => 'lowbed-repair',           'key' => 'lowbed_repair'],
        ['slug' => 'aluminum-silobas-repair', 'key' => 'aluminum_silobas_repair'],
        ['slug' => 'fuel-tank-repair-center', 'key' => 'fuel_center'],
        ['slug' => 'axle-repair',             'key' => 'axle_repair'],
        ['slug' => 'suspension-repair',       'key' => 'suspension_repair'],
    ];

    /** Selamlama ve teşekkür (normalize edilmiş, çok dilli). */
    private const GREETINGS = [
        'merhaba', 'merhabalar', 'selam', 'selamlar', 'slm', 'mrb', 'gunaydin',
        'hi', 'hello', 'hey', 'bonjour', 'salut', 'privet', 'привет', 'здравствуйте',
        'salam', 'مرحبا', 'اهلا', 'السلام',
    ];
    private const THANKS = [
        'tesekkur', 'tesekkurler', 'tesekkurederim', 'sagol', 'sagolun', 'thanks',
        'thank', 'merci', 'spasibo', 'спасибо', 'shukran', 'شكرا',
    ];

    /** Hizmet/onarım niyeti taşıyan kelimeler (normalize, çok dilli). */
    private const SERVICE_WORDS = [
        'tamir', 'tamiri', 'onarim', 'onarimi', 'bakim', 'bakimi', 'servis', 'revizyon',
        'tadilat', 'repair', 'service', 'maintenance', 'fix', 'reparation', 'entretien',
        'remont', 'ремонт', 'обслуживание', 'siyana', 'صيانة', 'اصلاح', 'تصليح',
    ];

    /** Eşleşmeye katkısı olmayan kelimeler. */
    private const STOPWORDS = [
        'bir' => 1, 'bu' => 1, 've' => 1, 'ile' => 1, 'için' => 1, 'icin' => 1,
        'mi' => 1, 'mu' => 1, 'ne' => 1, 'ya' => 1, 'ki' => 1, 'en' => 1, 'cok' => 1,
        'daha' => 1, 'gibi' => 1, 'olan' => 1, 'bana' => 1, 'lazim' => 1, 'var' => 1,
        'yok' => 1, 'nasil' => 1, 'hangi' => 1, 'istiyorum' => 1, 'ariyorum' => 1,
        'the' => 1, 'and' => 1, 'for' => 1, 'with' => 1, 'you' => 1, 'are' => 1,
        'can' => 1, 'what' => 1, 'need' => 1, 'want' => 1, 'how' => 1, 'have' => 1,
        'looking' => 1, 'que' => 1, 'pour' => 1, 'avec' => 1, 'des' => 1, 'les' => 1,
    ];

    public function __construct(?AiConfig $config = null)
    {
        $this->config = $config ?? config(AiConfig::class);
    }

    /** Asistan açık mı? (Yerel olduğu için anahtar gerekmez.) */
    public function isConfigured(): bool
    {
        return $this->config->enabled;
    }

    /**
     * Bir soruyu yerel olarak yanıtlar.
     *
     * @return array{ok: bool, status: string, intent?: string, answer?: string,
     *               products?: array, services?: array, cta?: array}
     */
    public function ask(string $question, string $locale = 'tr'): array
    {
        $q = trim($question);
        if ($q === '') {
            return ['ok' => false, 'status' => 'empty'];
        }
        if (mb_strlen($q) > $this->config->maxQuestionLength) {
            $q = mb_substr($q, 0, $this->config->maxQuestionLength);
        }

        helper(['url', 'locale']);

        $base   = $this->tokens($q);
        $tokens = $this->expand($base);

        $products = $this->retrieveProducts($tokens, $locale);
        $services = $this->retrieveServices($tokens, $locale);

        $prodOk       = $products !== [] && $products[0]['score'] >= 2;
        $servOk       = $services !== [] && $services[0]['score'] >= 2;
        $wantsService = $this->hasAny($base, self::SERVICE_WORDS);

        // --- Niyet kararı ---
        if ($wantsService && $servOk) {
            return $this->serviceAnswer($services, $locale);
        }
        if ($prodOk) {
            return $this->productAnswer($products, $locale);
        }
        if ($servOk) {
            return $this->serviceAnswer($services, $locale);
        }

        // Eşleşme yok: selam / teşekkür / belirsiz / kapsam dışı
        if ($this->hasAny($base, self::GREETINGS)) {
            return $this->compose('greeting', lang('Ai.greeting', [], $locale), [], [], $this->cta($locale, false));
        }
        if ($this->hasAny($base, self::THANKS)) {
            return $this->compose('thanks', lang('Ai.thanks', [], $locale), [], [], null);
        }
        if ($base === []) {
            return $this->compose('ask_more', lang('Ai.ask_more', [], $locale), [], [], null);
        }

        // Gerçek kelimeler var ama hiçbir şeye uymadı -> nazik ret + yönlendirme
        return $this->compose('no_match', lang('Ai.no_match', [], $locale), [], [], $this->cta($locale, true));
    }

    /* ================================================================
       Cevap kurucular
       ================================================================ */

    private function productAnswer(array $products, string $locale): array
    {
        $cards = $this->productCards(array_slice($products, 0, $this->config->maxProducts), $locale);
        $intro = count($cards) === 1
            ? lang('Ai.product_intro_one', [], $locale)
            : lang('Ai.product_intro', [], $locale);

        return $this->compose('product', $intro, $cards, [], $this->cta($locale, false));
    }

    private function serviceAnswer(array $services, string $locale): array
    {
        $cards = $this->serviceCards(array_slice($services, 0, 3), $locale);
        $intro = count($cards) === 1
            ? lang('Ai.service_intro_one', [], $locale)
            : lang('Ai.service_intro', [], $locale);

        return $this->compose('service', $intro, [], $cards, $this->cta($locale, false));
    }

    private function compose(string $intent, string $answer, array $products, array $services, ?array $cta): array
    {
        return [
            'ok'       => true,
            'status'   => 'answered',
            'intent'   => $intent,
            'answer'   => $answer,
            'products' => $products,
            'services' => $services,
            'cta'      => $cta,
        ];
    }

    /* ================================================================
       Retrieval - ürünler
       ================================================================ */

    private function retrieveProducts(array $tokens, string $locale): array
    {
        if ($tokens === []) {
            return [];
        }

        $scored = [];
        foreach (ProductCatalog::categories() as $cat) {
            $catHay = $this->normalize((string) ($cat['name'] ?? ''));
            foreach ($cat['products'] ?? [] as $p) {
                $models = implode(' ', array_map(static fn ($v) => (string) ($v['model'] ?? ''), $p['variants'] ?? []));
                $hay    = $this->normalize(($p['name'] ?? '') . ' ' . implode(' ', (array) ($p['aliases'] ?? [])) . ' ' . $models);

                $score = 0;
                foreach ($tokens as $t) {
                    if ($t === '') {
                        continue;
                    }
                    if (str_contains($hay, $t)) {
                        $score += 2;
                    } elseif (str_contains($catHay, $t)) {
                        $score += 1;
                    }
                }
                if ($score <= 0) {
                    continue;
                }

                $scored[] = [
                    'score'    => $score,
                    'variants' => count($p['variants'] ?? []),
                    'name'     => (string) ($p['name'] ?? ''),
                    'category' => (string) ($cat['name'] ?? ''),
                    'catSlug'  => (string) ($cat['slug'] ?? ''),
                    'slug'     => (string) ($p['slug'] ?? ''),
                ];
            }
        }

        usort($scored, static fn ($a, $b) => ($b['score'] <=> $a['score']) ?: ($b['variants'] <=> $a['variants']));

        return $scored;
    }

    private function productCards(array $items, string $locale): array
    {
        $label = lang('Ai.go_to_product', [], $locale);
        $out   = [];
        foreach ($items as $m) {
            $url = function_exists('locale_url')
                ? locale_url("products/{$m['catSlug']}/{$m['slug']}", $locale)
                : "/{$locale}/products/{$m['catSlug']}/{$m['slug']}";

            $rel   = "assets/images/products/{$m['catSlug']}-{$m['slug']}.jpg";
            $image = is_file(FCPATH . $rel) ? base_url($rel) : '';

            $out[] = [
                'name'     => $m['name'],
                'category' => $m['category'],
                'url'      => $url,
                'image'    => $image,
                'button'   => $label,
            ];
        }

        return $out;
    }

    /* ================================================================
       Retrieval - hizmetler (yerelleştirilmiş)
       ================================================================ */

    private function retrieveServices(array $tokens, string $locale): array
    {
        if ($tokens === []) {
            return [];
        }

        $scored = [];
        foreach (self::SERVICES as $s) {
            $name = (string) lang('Navigation.srv_' . $s['key'], [], $locale);
            $desc = (string) lang('Navigation.srv_' . $s['key'] . '_desc', [], $locale);
            $hay  = $this->normalize($name . ' ' . $desc . ' ' . str_replace('-', ' ', $s['slug']));

            $score = 0;
            foreach ($tokens as $t) {
                if ($t !== '' && str_contains($hay, $t)) {
                    $score += 2;
                }
            }
            if ($score > 0) {
                $scored[] = ['score' => $score, 'name' => $name, 'slug' => $s['slug']];
            }
        }

        usort($scored, static fn ($a, $b) => $b['score'] <=> $a['score']);

        return $scored;
    }

    private function serviceCards(array $items, string $locale): array
    {
        $label = lang('Ai.go_to_service', [], $locale);
        $out   = [];
        foreach ($items as $s) {
            $url = function_exists('locale_url')
                ? locale_url("services/{$s['slug']}", $locale)
                : "/{$locale}/services/{$s['slug']}";

            $rel   = "assets/images/services/srv-{$s['slug']}.jpg";
            $image = is_file(FCPATH . $rel) ? base_url($rel) : '';

            $out[] = [
                'name'   => $s['name'],
                'url'    => $url,
                'image'  => $image,
                'button' => $label,
            ];
        }

        return $out;
    }

    /* ================================================================
       İletişim (lead) çağrısı
       ================================================================ */

    private function cta(string $locale, bool $withAllProducts): ?array
    {
        $wa = 'https://wa.me/' . $this->whatsappNumber()
            . '?text=' . rawurlencode((string) lang('Ai.whatsapp_message', [], $locale));

        $actions = [
            ['label' => (string) lang('Ai.contact_cta', [], $locale), 'url' => locale_url('contact', $locale), 'type' => 'contact'],
            ['label' => (string) lang('Ai.whatsapp_cta', [], $locale), 'url' => $wa, 'type' => 'whatsapp'],
        ];
        if ($withAllProducts) {
            $actions[] = ['label' => (string) lang('Ai.all_products_cta', [], $locale), 'url' => locale_url('products', $locale), 'type' => 'products'];
        }

        return ['text' => (string) lang('Ai.cta_text', [], $locale), 'actions' => $actions];
    }

    /** 05525002000 -> 905525002000 (wa.me biçimi). Ortak helper'a delege eder. */
    private function whatsappNumber(): string
    {
        helper('whatsapp');

        return whatsapp_number();
    }

    /* ================================================================
       Metin / jeton yardımcıları
       ================================================================ */

    /** Eş anlamlı (çok dilli) genişletme: jeton listesine Türkçe karşılık ekler. */
    private function expand(array $tokens): array
    {
        $map = $this->synonymMap();
        $out = $tokens;
        foreach ($tokens as $t) {
            if (isset($map[$t])) {
                $out = array_merge($out, $map[$t]);
            }
        }

        return array_values(array_unique($out));
    }

    /** ai-synonyms.php -> [normalize(tetikleyici) => [enjekte edilecek Türkçe jetonlar]] */
    private function synonymMap(): array
    {
        if (self::$synCache !== null) {
            return self::$synCache;
        }

        $file   = APPPATH . 'Data/ai-synonyms.php';
        $groups = is_file($file) ? (require $file) : [];
        $map    = [];

        foreach ((array) $groups as $g) {
            $inject = array_map([$this, 'normalize'], (array) ($g['inject'] ?? []));
            foreach ((array) ($g['triggers'] ?? []) as $trigger) {
                $tn = $this->normalize((string) $trigger);
                if ($tn === '') {
                    continue;
                }
                $map[$tn] = array_values(array_unique(array_merge($map[$tn] ?? [], $inject)));
            }
        }

        return self::$synCache = $map;
    }

    /** Jeton listesinde verilen kümeden herhangi biri var mı? */
    private function hasAny(array $tokens, array $set): bool
    {
        $set = array_flip(array_map([$this, 'normalize'], $set));
        foreach ($tokens as $t) {
            if (isset($set[$t])) {
                return true;
            }
        }

        return false;
    }

    /** Küçük harfe çevirir, Türkçe karakterleri sadeleştirir (diğer alfabeler korunur). */
    private function normalize(string $s): string
    {
        $s = mb_strtolower($s, 'UTF-8');

        return strtr($s, [
            // Türkçe
            'ı' => 'i', 'i̇' => 'i', 'İ' => 'i', 'ş' => 's', 'ğ' => 'g',
            'ü' => 'u', 'ö' => 'o', 'ç' => 'c',
            // Latin aksanları (fr/de/es) -> ASCII
            'â' => 'a', 'à' => 'a', 'á' => 'a', 'ä' => 'a',
            'î' => 'i', 'ï' => 'i', 'í' => 'i',
            'û' => 'u', 'ù' => 'u', 'ú' => 'u',
            'ô' => 'o', 'ò' => 'o', 'ó' => 'o',
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
            'ñ' => 'n',
            // Arapça: elif/te/ye varyantları sadeleştirilir, hareke temizlenir
            'أ' => 'ا', 'إ' => 'ا', 'آ' => 'ا', 'ٱ' => 'ا',
            'ة' => 'ه', 'ى' => 'ي',
            'ً' => '', 'ٌ' => '', 'ٍ' => '', 'َ' => '', 'ُ' => '', 'ِ' => '', 'ّ' => '', 'ْ' => '',
        ]);
    }

    /**
     * Soruyu anlamlı jetonlara ayırır (Unicode-duyarlı: Kiril/Arap harfleri korunur).
     *
     * @return list<string>
     */
    private function tokens(string $s): array
    {
        $parts = preg_split('/[^\p{L}\p{N}]+/u', $this->normalize($s), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        $out = [];
        foreach ($parts as $p) {
            if (mb_strlen($p) < 2 || isset(self::STOPWORDS[$p])) {
                continue;
            }
            $out[$p] = true;
        }

        return array_keys($out);
    }
}
