<?php

namespace App\Controllers;

use App\Libraries\ProductCatalog;

/**
 * Media controller — "Medya" sayfası (fotoğraflar + videolar)
 *
 * FOTOĞRAFLAR — kategorili galeri (dosya tabanlı, kod düzenlemeye gerek yok):
 *   • Ürün görselleri:
 *       public/assets/images/products/{kategori}-{ürün}.jpg
 *     ProductCatalog'taki 11 kategoriye göre otomatik gruplanır. Dosya adı
 *     ürün sayfalarıyla aynı kuralı kullanır; yeni bir ürün görseli ekleyince
 *     ilgili kategoride kendiliğinden listelenir.
 *   • Önce / Sonra:
 *       public/assets/media/before-after/  (.jpg .jpeg .png .webp .gif .avif)
 *     Buraya bırakılan görseller — her biri tek karede öncesi+sonrasını
 *     gösterir — "Önce / Sonra" kategorisi altında listelenir. Klasör boşken
 *     bu kategori filtresi "yakında" durumu gösterir.
 *
 * VİDEOLAR — public/assets/media/videos/ taranır (.mp4 .webm .ogg .mov).
 *   Klasör boşken "Yakında" rozetli yer tutucu kartlar gösterilir. Bir video
 *   ile aynı ada sahip görsel (tanitim.mp4 + tanitim.jpg) otomatik kapak olur.
 */
class Media extends BaseController
{
    /** Desteklenen uzantılar (küçük harf). */
    private const PHOTO_EXT  = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'];
    private const VIDEO_EXT  = ['mp4', 'webm', 'ogg', 'mov'];
    private const POSTER_EXT = ['jpg', 'jpeg', 'png', 'webp', 'avif'];

    /** Önce/Sonra kategorisinin slug'ı (filtre kimliği + klasör adı). */
    private const BEFORE_AFTER = 'before-after';

    /** Yer tutucu video kartlarında küçük resim olarak kullanılacak görseller. */
    private const FALLBACK_VIDEO_THUMBS = ['hero-2.png', 'hero-3.png', 'hero-1.png'];

    public function index(): string
    {
        // --- Fotoğraflar: kategorili galeri (ürünler + Önce/Sonra) ---
        [$photos, $categories] = $this->buildPhotoGallery();

        // --- Videolar: klasör → yoksa "Yakında" yer tutucu kartlar ---
        $videos               = $this->scanVideos();
        $videosArePlaceholder = false;
        if ($videos === []) {
            $videos               = $this->placeholderVideos();
            $videosArePlaceholder = true;
        }

        return view('pages/media/index', [
            'metaTitle'            => lang('Media.meta_title'),
            'metaDescription'      => lang('Media.meta_description'),
            'photos'               => $photos,
            'categories'           => $categories,
            'videos'               => $videos,
            'videosArePlaceholder' => $videosArePlaceholder,
        ]);
    }

    /**
     * Kategorili fotoğraf galerisini kurar.
     *
     * Dönen değer iki listeden oluşur:
     *   0 → bütün fotoğraflar  (her biri kategori slug'ıyla etiketli)
     *   1 → filtre çubuğu      (görseli olan kategoriler + her zaman Önce/Sonra)
     *
     * @return array{
     *     0: list<array{url:string,alt:string,cat:string}>,
     *     1: list<array{slug:string,name:string,count:int}>
     * }
     */
    private function buildPhotoGallery(): array
    {
        $photos     = [];
        $categories = [];

        // 1) Ürün görselleri — katalog sırasına göre kategoriler.
        foreach (ProductCatalog::categories() as $category) {
            $slug  = (string) ($category['slug'] ?? '');
            if ($slug === '') {
                continue;
            }

            $count = 0;
            foreach ($category['products'] ?? [] as $product) {
                $file = $slug . '-' . ($product['slug'] ?? '') . '.jpg';
                if (! is_file(FCPATH . 'assets/images/products/' . $file)) {
                    continue;
                }
                $photos[] = [
                    'url' => base_url('assets/images/products/' . rawurlencode($file)),
                    'alt' => (string) ($product['name'] ?? $this->categoryName($slug, $category)),
                    'cat' => $slug,
                ];
                $count++;
            }

            if ($count > 0) {
                $categories[] = [
                    'slug'  => $slug,
                    'name'  => $this->categoryName($slug, $category),
                    'count' => $count,
                ];
            }
        }

        // 2) Önce / Sonra — kendi klasöründen tek tek görseller.
        $baDir   = FCPATH . 'assets/media/' . self::BEFORE_AFTER;
        $baFiles = $this->filesWithExt($baDir, self::PHOTO_EXT);
        foreach ($baFiles as $file) {
            $photos[] = [
                'url' => base_url('assets/media/' . self::BEFORE_AFTER . '/' . rawurlencode($file)),
                'alt' => $this->prettyName($file),
                'cat' => self::BEFORE_AFTER,
            ];
        }

        // Önce/Sonra her zaman bir filtre olarak görünür (boşken "yakında").
        $categories[] = [
            'slug'  => self::BEFORE_AFTER,
            'name'  => lang('Media.cat_before_after'),
            'count' => count($baFiles),
        ];

        return [$photos, $categories];
    }

    /**
     * Bir kategorinin yerelleştirilmiş adı. Çeviri anahtarı yoksa katalogdaki
     * ada düşer (Products controller'daki davranışla aynı).
     */
    private function categoryName(string $slug, array $category): string
    {
        $key   = 'Navigation.cat_' . str_replace('-', '_', $slug);
        $label = lang($key);

        return $label === $key ? (string) ($category['name'] ?? $slug) : $label;
    }

    /**
     * Video klasörünü tarar; varsa eşleşen poster görselini bağlar.
     *
     * @return list<array{placeholder:bool,title:string,url:string,poster:?string,mime:string}>
     */
    private function scanVideos(): array
    {
        $dir   = FCPATH . 'assets/media/videos';
        $files = $this->filesWithExt($dir, self::VIDEO_EXT);

        $mimes = ['mp4' => 'video/mp4', 'webm' => 'video/webm', 'ogg' => 'video/ogg', 'mov' => 'video/mp4'];

        $videos = [];
        foreach ($files as $file) {
            $ext  = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            $base = pathinfo($file, PATHINFO_FILENAME);

            // Aynı isimli poster görseli ara (tanitim.mp4 → tanitim.jpg).
            $poster = null;
            foreach (self::POSTER_EXT as $pext) {
                if (is_file($dir . DIRECTORY_SEPARATOR . $base . '.' . $pext)) {
                    $poster = base_url('assets/media/videos/' . rawurlencode($base . '.' . $pext));
                    break;
                }
            }

            $videos[] = [
                'placeholder' => false,
                'title'       => $this->prettyName($file),
                'url'         => base_url('assets/media/videos/' . rawurlencode($file)),
                'poster'      => $poster,
                'mime'        => $mimes[$ext] ?? 'video/mp4',
            ];
        }

        return $videos;
    }

    /**
     * Klasör boşken gösterilecek "Yakında" yer tutucu video kartları.
     * Başlıklar dil dosyasından, küçük resimler sitedeki görsellerden gelir.
     *
     * @return list<array{placeholder:bool,title:string,thumb:string,alt:string}>
     */
    private function placeholderVideos(): array
    {
        $titles = lang('Media.video_placeholders');
        $titles = is_array($titles) ? $titles : [];

        $thumbs = array_values(array_filter(
            self::FALLBACK_VIDEO_THUMBS,
            static fn (string $f): bool => is_file(FCPATH . 'assets/images/' . $f)
        ));

        $videos = [];
        foreach ($titles as $i => $title) {
            $thumb = $thumbs === [] ? null : $thumbs[$i % count($thumbs)];
            $videos[] = [
                'placeholder' => true,
                'title'       => (string) $title,
                'thumb'       => $thumb === null ? '' : base_url('assets/images/' . $thumb),
                'alt'         => (string) $title,
            ];
        }

        return $videos;
    }

    /**
     * Bir klasördeki, verilen uzantılara sahip dosya adlarını doğal sırada
     * döndürür.
     *
     * @param list<string> $ext
     * @return list<string>
     */
    private function filesWithExt(string $dir, array $ext): array
    {
        if (! is_dir($dir)) {
            return [];
        }

        $out = [];
        foreach (scandir($dir) ?: [] as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            if (! is_file($dir . DIRECTORY_SEPARATOR . $entry)) {
                continue;
            }
            if (in_array(strtolower(pathinfo($entry, PATHINFO_EXTENSION)), $ext, true)) {
                $out[] = $entry;
            }
        }

        natcasesort($out);

        return array_values($out);
    }

    /**
     * Dosya adından okunabilir bir başlık üretir
     * (örn. "fabrika-uretim_01.jpg" → "Fabrika Üretim 01").
     */
    private function prettyName(string $file): string
    {
        $name = pathinfo($file, PATHINFO_FILENAME);
        $name = str_replace(['-', '_'], ' ', $name);
        $name = trim(preg_replace('/\s+/', ' ', $name) ?? $name);

        return $name === '' ? lang('Media.tab_photos') : mb_convert_case($name, MB_CASE_TITLE, 'UTF-8');
    }
}
