<?php

namespace App\Controllers;

/**
 * Media controller — "Medya" sayfası (fotoğraflar + videolar)
 *
 * İçerik DOSYA TABANLIDIR: kod düzenlemeye gerek yoktur. Sayfa, açılışta
 * şu iki klasörü tarar ve içindeki dosyaları otomatik listeler:
 *
 *   public/assets/media/photos/   →  Fotoğraflar sekmesi (.jpg .jpeg .png .webp .gif .avif)
 *   public/assets/media/videos/   →  Videolar sekmesi   (.mp4 .webm .ogg .mov)
 *
 * Video kapağı (poster): bir video ile aynı ada sahip bir görsel
 * (örn. tanitim.mp4 + tanitim.jpg) aynı klasöre konursa otomatik kapak olur.
 *
 * Klasörler boşken: sayfa boş kalmasın diye GEÇİCİ olarak sitedeki mevcut
 * görseller örnek olarak gösterilir (Fotoğraflar tek tek; Videolar "Yakında"
 * rozetli yer tutucu kartlar). Kendi dosyalarınızı yukarıdaki klasörlere
 * bıraktığınız an örnekler kaybolur, gerçek galeri/oynatıcılar görünür.
 */
class Media extends BaseController
{
    /** Desteklenen uzantılar (küçük harf). */
    private const PHOTO_EXT  = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'];
    private const VIDEO_EXT  = ['mp4', 'webm', 'ogg', 'mov'];
    private const POSTER_EXT = ['jpg', 'jpeg', 'png', 'webp', 'avif'];

    /** Klasör boşken Fotoğraflar sekmesinde gösterilecek örnek görseller. */
    private const FALLBACK_PHOTOS = [
        'hero-1.png', 'hero-2.png', 'hero-3.png',
        'hero-11.png', 'hero-13.png', 'hero-33.png', 'about.jpg',
    ];

    /** Yer tutucu video kartlarında küçük resim olarak kullanılacak görseller. */
    private const FALLBACK_VIDEO_THUMBS = ['hero-2.png', 'hero-3.png', 'hero-1.png'];

    public function index(): string
    {
        // --- Fotoğraflar: klasör → yoksa örnek site görselleri ---
        $photos          = $this->scanPhotos();
        $photosAreSamples = false;
        if ($photos === []) {
            $photos          = $this->fallbackPhotos();
            $photosAreSamples = true;
        }

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
            'photosAreSamples'     => $photosAreSamples,
            'videos'               => $videos,
            'videosArePlaceholder' => $videosArePlaceholder,
        ]);
    }

    /**
     * Fotoğraf klasörünü tarar.
     *
     * @return list<array{url:string,alt:string,placeholder:bool}>
     */
    private function scanPhotos(): array
    {
        $dir   = FCPATH . 'assets/media/photos';
        $files = $this->filesWithExt($dir, self::PHOTO_EXT);

        $photos = [];
        foreach ($files as $file) {
            $photos[] = [
                'url'         => base_url('assets/media/photos/' . rawurlencode($file)),
                'alt'         => $this->prettyName($file),
                'placeholder' => false,
            ];
        }

        return $photos;
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
     * Klasör boşken gösterilecek örnek fotoğraflar (sitedeki görseller).
     *
     * @return list<array{url:string,alt:string,placeholder:bool}>
     */
    private function fallbackPhotos(): array
    {
        $alt = lang('Common.site_name');

        $photos = [];
        foreach (self::FALLBACK_PHOTOS as $file) {
            if (! is_file(FCPATH . 'assets/images/' . $file)) {
                continue;
            }
            $photos[] = [
                'url'         => base_url('assets/images/' . $file),
                'alt'         => $alt,
                'placeholder' => true,
            ];
        }

        return $photos;
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
     * Bir klasördeki, verilen uzantılara sahip dosya adlarını
     * (poster görselleri hariç) doğal sırada döndürür.
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
