<?php

/**
 * "Yeni" layout — koyu, sıfırdan tasarlanmış arayüz kabuğu.
 *
 * Ana sayfanın yeni tasarımı (pages/home.php) bu layout'u kullanır.
 * Eski layout'tan (layouts/main.php) bağımsızdır: Bootstrap, AOS, Swiper
 * ve eski tasarım sistemi yüklenmez. İç sayfalar eski layout'ta kalır.
 *
 * Sağlanan bölümler:
 *   - $this->section('content')  (zorunlu)
 *   - $this->section('scripts')  (opsiyonel; sayfaya özel JS)
 */
$locale = current_locale();

/* Önbellek kırıcı: dosya değişince URL de değişir, tarayıcı eski
   JS/CSS'i kullanmaz. */
$asset = static function (string $path): string {
    $file = FCPATH . $path;
    $ver  = is_file($file) ? (string) filemtime($file) : '1';
    return base_url($path) . '?v=' . $ver;
};
?>
<!DOCTYPE html>
<html lang="<?= esc($locale) ?>" dir="<?= locale_direction($locale) ?>">
<head>
    <!-- Tema: ilk boyadan önce ayarla (FOUC yok). Öncelik: kullanıcı
         seçimi (localStorage) → yoksa işletim sistemi tercihi. -->
    <script>
        (function () {
            try {
                var k = 'barlas-theme', s = localStorage.getItem(k);
                var t = (s === 'light' || s === 'dark') ? s
                    : (window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark');
                document.documentElement.setAttribute('data-theme', t);
            } catch (e) {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        })();
    </script>

    <?= $this->include('partials/meta') ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@500;600;700;800&display=swap">

    <!-- Ortak header: eski tasarımın token + menü/mega stilleri,
         sonra yeni tema, en sonda koyu header kaplaması (sıra önemli) -->
    <link rel="stylesheet" href="<?= $asset('assets/css/variables.css') ?>">
    <link rel="stylesheet" href="<?= $asset('assets/css/navigation.css') ?>">
    <link rel="stylesheet" href="<?= $asset('assets/css/yeni.css') ?>">
    <link rel="stylesheet" href="<?= $asset('assets/css/yeni-footer.css') ?>">
    <link rel="stylesheet" href="<?= $asset('assets/css/yeni-header.css') ?>">
    <link rel="stylesheet" href="<?= $asset('assets/css/theme-light.css') ?>">

    <!-- Sayfaya özel stiller (opsiyonel) -->
    <?= $this->renderSection('styles') ?>
</head>
<body>

    <?= $this->include('partials/header') ?>

    <?= $this->renderSection('content') ?>

    <?= $this->include('partials/footer') ?>

    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/lenis@1.1.18/dist/lenis.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.149.0/build/three.min.js" defer></script>
    <script src="<?= $asset('assets/js/yeni-app.js') ?>" defer></script>
    <script src="<?= $asset('assets/js/yeni-tanker.js') ?>" defer></script>
    <script src="<?= $asset('assets/js/theme-toggle.js') ?>" defer></script>

    <!-- Ortak header davranışı: mobil çekmece, mega menüler, dil seçici,
         scrolled durumu (eski tasarımla aynı modül — tek kaynak) -->
    <script type="module">
        import { initNavigation } from '<?= base_url('assets/js/navigation.js') ?>';
        initNavigation();
    </script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
