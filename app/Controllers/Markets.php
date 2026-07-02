<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * Markets controller — ülke/pazar açılış sayfaları (§7.11).
 *
 * Kayıt defteri app/Data/markets.json (locale-bağımsız); ülke adları ve
 * tüm UI metinleri Markets dil dosyalarından gelir. Ürün grupları gerçek
 * katalog kategorilerine (products.json slug'ları) bağlanır — kırık link
 * üretilemez. Sayfada UYDURMA ihracat rakamı yoktur; gerçek ihracat
 * verisi geldiğinde markets.json'a alan eklenerek zenginleştirilir.
 */
class Markets extends BaseController
{
    /** Bölge grupları — index sayfası bu sırayla gruplar. */
    private const REGIONS = ['europe', 'cis', 'mena'];

    /** @var list<array<string, mixed>>|null */
    private static ?array $countries = null;

    /** @return list<array<string, mixed>> kayıt defteri (Sitemap da kullanır) */
    public static function registry(): array
    {
        if (self::$countries === null) {
            $data            = json_decode((string) @file_get_contents(APPPATH . 'Data/markets.json'), true);
            self::$countries = is_array($data['countries'] ?? null) ? $data['countries'] : [];
        }

        return self::$countries;
    }

    public function index(): string
    {
        $this->cachePage(3600);

        // Bölgeye göre grupla; ülke adı geçerli dilde sıralansın.
        $grouped = array_fill_keys(self::REGIONS, []);

        foreach (self::registry() as $country) {
            $country['name']                = lang('Markets.country_' . str_replace('-', '_', $country['slug']));
            $grouped[$country['region']][] = $country;
        }

        foreach ($grouped as &$countries) {
            usort($countries, static fn (array $a, array $b): int => strcoll($a['name'], $b['name']));
        }

        return view('pages/markets/index', [
            'metaTitle'       => lang('Markets.meta_title'),
            'metaDescription' => lang('Markets.meta_description'),
            'grouped'         => $grouped,
        ]);
    }

    public function detail(string $slug): string
    {
        $this->cachePage(3600);

        $country = null;
        foreach (self::registry() as $candidate) {
            if ($candidate['slug'] === $slug) {
                $country = $candidate;
                break;
            }
        }

        if ($country === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $name = lang('Markets.country_' . str_replace('-', '_', $slug));

        // Kategori kartları: ad/açıklama mega menüyle aynı kaynaktan
        // (Navigation.cat_*) gelir — çeviri tek yerde yaşar.
        $categories = [];
        foreach ($country['categories'] as $catSlug) {
            $key          = 'cat_' . str_replace('-', '_', $catSlug);
            $categories[] = [
                'slug'  => $catSlug,
                'title' => lang('Navigation.' . $key),
                'desc'  => lang('Navigation.' . $key . '_desc'),
            ];
        }

        return view('pages/markets/detail', [
            'metaTitle'       => sprintf(lang('Markets.detail_meta_title'), $name),
            'metaDescription' => sprintf(lang('Markets.detail_lead'), $name),
            'country'         => $country,
            'name'            => $name,
            'regionLabel'     => lang('Markets.region_' . $country['region']),
            'categories'      => $categories,
        ]);
    }
}
