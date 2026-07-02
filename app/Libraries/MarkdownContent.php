<?php

namespace App\Libraries;

use League\CommonMark\CommonMarkConverter;

/**
 * MarkdownContent — dosya tabanlı içerik motoru (ansiklopedi ve gelecekteki
 * markdown içerik türleri için ortak katman).
 *
 * Depo düzeni: app/Data/content/{type}/{locale}/{slug}.md
 *   - Slug tüm dillerde AYNIDIR (sitenin locale-bağımsız slug sözleşmesi);
 *     kanonik madde listesi tr dizininden çıkarılır.
 *   - Bir dilde çeviri dosyası yoksa tr içeriğe düşülür (sayfa yine açılır).
 *
 * Dosya biçimi: "---" ile ayrılmış front-matter (title, description,
 * updated) + CommonMark gövde. HTML girdisi güvenlik için ayıklanır.
 */
class MarkdownContent
{
    private static ?CommonMarkConverter $converter = null;

    /** @return list<array{slug:string,title:string,description:string,updated:string}> */
    public static function items(string $type, string $locale): array
    {
        $items = [];

        foreach (self::slugs($type) as $slug) {
            $meta = self::item($type, $locale, $slug, false);
            if ($meta !== null) {
                $items[] = $meta;
            }
        }

        usort($items, static fn (array $a, array $b): int => strcmp($b['updated'], $a['updated']) ?: strcmp($a['title'], $b['title']));

        return $items;
    }

    /** Tek madde; $withBody=true ise render edilmiş 'html' alanı da döner. */
    public static function item(string $type, string $locale, string $slug, bool $withBody = true): ?array
    {
        if (preg_match('/^[a-z0-9-]+$/', $slug) !== 1) {
            return null;
        }

        $file = self::file($type, $locale, $slug) ?? self::file($type, 'tr', $slug);
        if ($file === null) {
            return null;
        }

        [$meta, $body] = self::parse((string) file_get_contents($file));

        $item = [
            'slug'        => $slug,
            'title'       => $meta['title'] ?? $slug,
            'description' => $meta['description'] ?? '',
            'updated'     => $meta['updated'] ?? '',
        ];

        if ($withBody) {
            $item['html'] = (string) self::converter()->convert($body);
        }

        return $item;
    }

    /** @return list<string> kanonik slug listesi (tr dizininden) */
    public static function slugs(string $type): array
    {
        $slugs = [];

        foreach (glob(self::dir($type, 'tr') . '/*.md') ?: [] as $file) {
            $slugs[] = basename($file, '.md');
        }

        sort($slugs);

        return $slugs;
    }

    private static function file(string $type, string $locale, string $slug): ?string
    {
        $file = self::dir($type, $locale) . '/' . $slug . '.md';

        return is_file($file) ? $file : null;
    }

    private static function dir(string $type, string $locale): string
    {
        return APPPATH . 'Data/content/' . $type . '/' . $locale;
    }

    /**
     * @return array{0: array<string,string>, 1: string} [front-matter, gövde]
     *
     * DİKKAT: satır sonu için \R KULLANMA — bayt modunda \R, Unicode NEL
     * (0x85) baytını da eşler ve UTF-8 çok baytlı karakterlerin (ör. Kiril
     * "х" = D1 85) ortasından böler (yaşandı: ru/ar sayfalarında 500).
     */
    private static function parse(string $raw): array
    {
        $nl = '(?:\r\n|\n|\r)';

        if (preg_match('/^---\s*' . $nl . '(.*?)' . $nl . '---\s*' . $nl . '(.*)$/s', $raw, $m) !== 1) {
            return [[], $raw];
        }

        $meta = [];
        foreach (preg_split('/\r\n|\n|\r/', $m[1]) ?: [] as $line) {
            if (str_contains($line, ':')) {
                [$key, $value] = explode(':', $line, 2);
                $meta[trim($key)] = trim($value);
            }
        }

        return [$meta, $m[2]];
    }

    private static function converter(): CommonMarkConverter
    {
        return self::$converter ??= new CommonMarkConverter([
            'html_input'         => 'strip',
            'allow_unsafe_links' => false,
        ]);
    }
}
