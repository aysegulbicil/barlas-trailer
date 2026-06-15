<?php

/**
 * "Inner" layout — iç sayfalar için koyu "yeni" tema kabuğu.
 *
 * Blog ve diğer içerik sayfaları bu layout'u kullanır. Ana sayfayla
 * AYNI header, footer ve tema dilini paylaşır; ancak ana sayfanın ağır
 * animasyon paketini (three.js, gsap, lenis, tanker) yüklemez — yalnızca
 * ortak header davranışı ve tema değiştirici çalışır.
 *
 * Sağlanan bölümler:
 *   - $this->section('content')   (zorunlu)
 *   - $this->section('bodyClass') (opsiyonel; <body> sınıfı)
 *   - $this->section('styles')    (opsiyonel; sayfaya özel CSS)
 *   - $this->section('scripts')   (opsiyonel; sayfaya özel JS)
 */
$locale = current_locale();

/* Önbellek kırıcı: dosya değişince URL de değişir. */
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

    <!-- Sıra önemli: token'lar → menü → yeni tema → iç sayfalar →
         koyu header kaplaması → en sonda aydınlık tema ezmesi. -->
    <link rel="stylesheet" href="<?= $asset('assets/css/variables.css') ?>">
    <link rel="stylesheet" href="<?= $asset('assets/css/navigation.css') ?>">
    <link rel="stylesheet" href="<?= $asset('assets/css/yeni.css') ?>">
    <link rel="stylesheet" href="<?= $asset('assets/css/yeni-pages.css') ?>">
    <link rel="stylesheet" href="<?= $asset('assets/css/yeni-footer.css') ?>">
    <link rel="stylesheet" href="<?= $asset('assets/css/yeni-header.css') ?>">
    <link rel="stylesheet" href="<?= $asset('assets/css/theme-light.css') ?>">

    <?= $this->renderSection('styles') ?>
</head>
<body class="page page--<?= esc($this->renderSection('bodyClass') ?: 'inner') ?>">

    <?= $this->include('partials/header') ?>

    <main id="main-content" role="main">
        <?= $this->renderSection('content') ?>
    </main>

    <?= $this->include('partials/footer') ?>

    <!-- Ortak header davranışı: mobil çekmece, mega menüler, dil seçici,
         scrolled durumu (ana sayfayla aynı modül). -->
    <script type="module">
        import { initNavigation } from '<?= base_url('assets/js/navigation.js') ?>';
        initNavigation();
    </script>
    <script src="<?= $asset('assets/js/theme-toggle.js') ?>" defer></script>
    <script src="<?= $asset('assets/js/inner-reveal.js') ?>" defer></script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
