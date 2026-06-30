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
    <!-- Perf: fontları render-blocking yapma — print medyasıyla yükle, gelince
         'all'a çevir. display=swap zaten fallback fontla anında metin gösterir. -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@500;600;700;800&display=swap" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@500;600;700;800&display=swap"></noscript>

    <!-- Perf: yalnızca ana sayfada 3D zincirini erken başlat — CDN bağlantısını
         ısıt, Three.js modülünü ve hero modelini (tanker-3) paralel önceden indir.
         Böylece hero 3D, JS zincirini beklemeden gözle görülür şekilde erken belirir. -->
    <?php
        $__ctrl = '';
        try { $__ctrl = (string) service('router')->controllerName(); } catch (\Throwable $e) { $__ctrl = ''; }
    ?>
    <?php if (stripos($__ctrl, 'Home') !== false): ?>
        <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
        <link rel="modulepreload" href="https://cdn.jsdelivr.net/npm/three@0.149.0/build/three.module.js">
        <link rel="preload" as="fetch" crossorigin href="<?= $asset('assets/models/tanker-3.glb') ?>">
    <?php endif; ?>

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
    <script src="<?= $asset('assets/js/yeni-app.js') ?>" defer></script>
    <script src="<?= $asset('assets/js/theme-toggle.js') ?>" defer></script>

    <!-- 3D: tek bir THREE örneği (ESM) + GLTFLoader. three 0.149 artık global
         "examples/js" sunmuyor; bu yüzden import map ile 'three' eşlenir ve
         GLTFLoader aynı örneği kullanır. Hazır olunca yeni-tanker.js yüklenir. -->
    <script type="importmap">
    {
      "imports": {
        "three": "https://cdn.jsdelivr.net/npm/three@0.149.0/build/three.module.js",
        "three/addons/": "https://cdn.jsdelivr.net/npm/three@0.149.0/examples/jsm/"
      }
    }
    </script>
    <script type="module">
        import * as THREE from 'three';
        import { GLTFLoader } from 'https://cdn.jsdelivr.net/npm/three@0.149.0/examples/jsm/loaders/GLTFLoader.js';
        import { DRACOLoader } from 'https://cdn.jsdelivr.net/npm/three@0.149.0/examples/jsm/loaders/DRACOLoader.js';
        window.THREE = THREE;
        // Draco ile sıkıştırılmış .glb'leri çözmek için DRACOLoader gerekir. Decoder (wasm)
        // yalnızca ilk Draco modeli yüklenince CDN'den çekilir; sıkıştırılmamış modellerde
        // hiç devreye girmez (ileriye dönük güvenli — model sıkıştırılmamış olsa bile çalışır).
        var __barlasDraco = new DRACOLoader();
        __barlasDraco.setDecoderPath('https://cdn.jsdelivr.net/npm/three@0.149.0/examples/jsm/libs/draco/');
        // Mevcut "new window.GLTFLoader()" çağrıları (yeni-tanker.js + contact-tanker.js)
        // hiç değişmeden Draco desteği kazanır: ctor bir nesne döndürünce "new" o nesneyi verir.
        window.GLTFLoader = function () {
            var loader = new GLTFLoader();
            loader.setDRACOLoader(__barlasDraco);
            return loader;
        };
        // Konvoy modelleri (sürüye eklenecek sırayla). Cache-busting'li yollar.
        window.__BARLAS_MODELS = [
            '<?= $asset('assets/models/tanker-1.glb') ?>',
            '<?= $asset('assets/models/tanker-2.glb') ?>',
            '<?= $asset('assets/models/tanker-4.glb') ?>',
            '<?= $asset('assets/models/tanker-5.glb') ?>'
        ];
        // Hero (slide) sahnesinde sergilenen tek model: tanker-3.glb
        window.__BARLAS_HERO_MODEL = '<?= $asset('assets/models/tanker-3.glb') ?>';
        // İletişim sayfası 3D sahnesinde sergilenen model: tanker-1.glb
        window.__BARLAS_CONTACT_MODEL = '<?= $asset('assets/models/tanker-1.glb') ?>';
        (function () {
            function boot() {
                var s = document.createElement('script');
                s.src = '<?= $asset('assets/js/yeni-tanker.js') ?>';
                document.body.appendChild(s);
            }
            // gsap/ScrollTrigger defer scriptleri DOMContentLoaded'a kadar yüklenir;
            // yeni-tanker.js'i o aşamada enjekte et ki yol sahnesi (scrub) çalışsın.
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', boot, { once: true });
            } else {
                boot();
            }
        })();
    </script>

    <!-- Ortak header davranışı: mobil çekmece, mega menüler, dil seçici,
         scrolled durumu (eski tasarımla aynı modül — tek kaynak) -->
    <script type="module">
        import { initNavigation } from '<?= base_url('assets/js/navigation.js') ?>';
        initNavigation();
    </script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
