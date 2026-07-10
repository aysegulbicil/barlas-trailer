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

    <!-- "Her zaman animasyon açık" (kullanıcı tercihi): işletim sistemindeki
         "animasyon efektlerini kapat" (prefers-reduced-motion: reduce) ayarını
         SİTE İÇİN yok say. Bunu tek noktadan yapıyoruz: reduce sorgusunu her
         zaman "eşleşmiyor" döndür → yeni-app/cinema-*/yeni-tanker vb. tüm
         hareket katmanı işletim sistemi ayarından bağımsız çalışır. Diğer
         medya sorguları (tema, hover, işaretçi, genişlik) AYNEN geçer.
         NOT: Bu bir erişilebilirlik ezmesidir; geri almak için bu bloğu sil. -->
    <script>
        (function () {
            try {
                var native = window.matchMedia && window.matchMedia.bind(window);
                if (!native) return;
                window.matchMedia = function (q) {
                    var mql = native(q);
                    if (typeof q === 'string' && /prefers-reduced-motion\s*:\s*reduce/i.test(q)) {
                        return {
                            media: mql.media,
                            matches: false,
                            onchange: null,
                            addListener: function () {},
                            removeListener: function () {},
                            addEventListener: function () {},
                            removeEventListener: function () {},
                            dispatchEvent: function () { return false; }
                        };
                    }
                    return mql;
                };
            } catch (e) { /* yut */ }
        })();
    </script>

    <?= $this->include('partials/meta') ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Perf: fontları render-blocking yapma — print medyasıyla yükle, gelince
         'all'a çevir. display=swap zaten fallback fontla anında metin gösterir. -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@500;600;700;800&display=swap" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@500;600;700;800&display=swap"></noscript>

    <!-- Perf: CDN bağlantısını ısıt + Three.js modülünü erken indir (yol konvoyu
         sahnesi için gerekli). Ana sayfada ayrıca STATİK hero görselini, iletişim
         sayfasında teslimat modelini önceden indir → ilk boya beklemesin. -->
    <?php
        $__ctrl = '';
        try { $__ctrl = (string) service('router')->controllerName(); } catch (\Throwable $e) { $__ctrl = ''; }
        $__isHome    = stripos($__ctrl, 'Home') !== false;
        $__isContact = stripos($__ctrl, 'Contact') !== false;
    ?>
    <?php if ($__isHome || $__isContact): ?>
        <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
        <?php if ($__isHome): ?>
            <!-- Three.js yalnız ana sayfada gerekir (yol konvoyu sahnesi). -->
            <link rel="modulepreload" href="https://cdn.jsdelivr.net/npm/three@0.149.0/build/three.module.js">
            <!-- Statik hero görselini erken indir → hero'nun ilk boyası hızlansın.
                 WebP ~139KB (PNG 2.1MB); URL hero-static.php ile BİREBİR aynı olmalı. -->
            <link rel="preload" as="image" type="image/webp" href="<?= $asset('assets/images/25a460c4-3549-4d3c-9fcc-3140cf583b67.webp') ?>" fetchpriority="high">
        <?php endif; ?>
        <!-- NOT: iletişim sahnesi 3D değil, SVG usta (contact-foreman.js);
             THREE/GLB gerekmiyor. preconnect iletişimde kalır (GSAP aynı CDN'den). -->
    <?php endif; ?>

    <!-- Ortak header: eski tasarımın token + menü/mega stilleri,
         sonra yeni tema, en sonda koyu header kaplaması (sıra önemli) -->
    <link rel="stylesheet" href="<?= $asset('assets/css/variables.css') ?>">
    <link rel="stylesheet" href="<?= $asset('assets/css/navigation.css') ?>">
    <link rel="stylesheet" href="<?= $asset('assets/css/yeni.css') ?>">
    <?php if ($__isHome): ?>
        <!-- Statik tam ekran sinematik hero + 4 büyük seçenek (yalnızca ana sayfa) -->
        <link rel="stylesheet" href="<?= $asset('assets/css/hero-static.css') ?>">
        <link rel="stylesheet" href="<?= $asset('assets/css/home-options.css') ?>">
    <?php endif; ?>
    <link rel="stylesheet" href="<?= $asset('assets/css/yeni-footer.css') ?>">
    <link rel="stylesheet" href="<?= $asset('assets/css/yeni-header.css') ?>">
    <link rel="stylesheet" href="<?= $asset('assets/css/theme-light.css') ?>">
    <!-- Sinematik katman (v3.0): çekirdek token'lar + atmosfer/scroll/mikro
         efekt stilleri. Davranış cinema-*.js'te; reduced-motion'da tamamen
         statik kalır (html.cine-off). İntro yalnız ana sayfada yüklenir. -->
    <link rel="stylesheet" href="<?= $asset('assets/css/cinema.css') ?>">
    <link rel="stylesheet" href="<?= $asset('assets/css/cinema-atmosphere.css') ?>">
    <link rel="stylesheet" href="<?= $asset('assets/css/cinema-scroll.css') ?>">
    <link rel="stylesheet" href="<?= $asset('assets/css/cinema-micro.css') ?>">
    <?php if ($__isHome): ?>
        <link rel="stylesheet" href="<?= $asset('assets/css/cinema-intro.css') ?>">
    <?php endif; ?>
    <?php if (locale_direction($locale) === 'rtl'): ?>
        <link rel="stylesheet" href="<?= $asset('assets/css/rtl.css') ?>">
    <?php endif; ?>

    <!-- Sayfaya özel stiller (opsiyonel) -->
    <?= $this->renderSection('styles') ?>
</head>
<!-- Ana sayfada header, koyu hero görselinin üzerine saydam biner
     (yeni-header.css §3). Diğer sayfalar sticky header'la kalır. -->
<body<?= $__isHome ? ' class="has-hero-header"' : '' ?>>

    <?php if ($__isHome): ?>
        <!-- Sinematik açılış perdesi: HER ZAMAN gizli render edilir (sayfa
             önbelleği çereze göre çeşitleyemez); gösterme kararı istemcide
             (cinema-intro.js + cine_intro oturum çerezi). -->
        <?= $this->include('partials/cinema-intro') ?>
    <?php endif; ?>

    <a class="skip-link" href="#main-content"><?= esc(lang('Navigation.skip_to_content')) ?></a>

    <?= $this->include('partials/header') ?>

    <div id="main-content">
    <?= $this->renderSection('content') ?>
    </div>

    <?= $this->include('partials/footer') ?>

    <!-- Birinci taraf ölçüm (çerezsiz; Panel > Analitik buradan beslenir) -->
    <?= $this->include('partials/metrics') ?>

    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/lenis@1.1.18/dist/lenis.min.js" defer></script>
    <script src="<?= $asset('assets/js/yeni-app.js') ?>" defer></script>
    <script src="<?= $asset('assets/js/theme-toggle.js') ?>" defer></script>
    <script src="<?= $asset('assets/js/voice-mode.js') ?>" defer></script>
    <!-- Sinematik katman: çekirdek İLK yüklenmeli (defer sırası korunur) —
         diğer modüller window.BarlasCinema yoksa sessizce hiçbir şey yapmaz. -->
    <script src="<?= $asset('assets/js/cinema-core.js') ?>" defer></script>
    <script src="<?= $asset('assets/js/cinema-atmosphere.js') ?>" defer></script>
    <script src="<?= $asset('assets/js/cinema-scroll.js') ?>" defer></script>
    <script src="<?= $asset('assets/js/cinema-micro.js') ?>" defer></script>
    <?php if ($__isHome): ?>
        <script src="<?= $asset('assets/js/cinema-intro.js') ?>" defer></script>
    <?php endif; ?>

    <?php if ($__isHome): ?>
    <!-- 3D: tek bir THREE örneği (ESM) + GLTFLoader. three 0.149 artık global
         "examples/js" sunmuyor; bu yüzden import map ile 'three' eşlenir ve
         GLTFLoader aynı örneği kullanır. Hazır olunca yeni-tanker.js yüklenir.
         YALNIZ ana sayfada: 3D sahneler (konvoy) sadece burada var — iletişim
         dahil diğer sayfalar THREE indirmesin (~640KB modül + parse). -->
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
        // Yalnızca 3D YEDEK hero (hero-cinematic) için tutulur; statik hero bunu
        // kullanmaz. Aktif ana sayfa hero'su artık statik görsel olduğundan bu
        // model reachable değildir (indirilmez).
        window.__BARLAS_HERO_MODEL = '<?= $asset('assets/models/tanker-3.glb') ?>';
        // NOT: __BARLAS_CONTACT_MODEL kaldırıldı — iletişim sahnesi artık 3D değil,
        // SVG usta + GSAP (contact-foreman.js) ile çalışır.
        (function () {
            function boot() {
                var s = document.createElement('script');
                s.src = '<?= $asset('assets/js/yeni-tanker.js') ?>';
                document.body.appendChild(s);
                /* Statik sinematik hero motoru. Görsel "ikiye ayrılma" için
                   yalnızca GSAP + ScrollTrigger (defer) gerekir; 3D yok. */
                var hs = document.createElement('script');
                hs.src = '<?= $asset('assets/js/hero-static.js') ?>';
                document.body.appendChild(hs);
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
    <?php endif; ?>

    <!-- Ortak header davranışı: mobil çekmece, mega menüler, dil seçici,
         scrolled durumu (eski tasarımla aynı modül — tek kaynak) -->
    <script type="module">
        import { initNavigation } from '<?= $asset('assets/js/navigation.js') ?>';
        initNavigation();
    </script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
