<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// {locale} yalnız App::$supportedLocales ile eşleşsin: /de/... gibi
// tanımsız önekler TR içerik sunmak yerine 404 versin (duplicate content).
$routes->useSupportedLocalesOnly(true);

/*
 * --------------------------------------------------------------------
 * Locale switch route (no locale prefix)
 * --------------------------------------------------------------------
 * Sets the preferred locale and redirects back. The {locale} placeholder
 * is validated by CodeIgniter against App::$supportedLocales.
 */
$routes->get('lang/(:segment)', 'LanguageController::switch/$1', ['as' => 'lang.switch']);

/*
 * --------------------------------------------------------------------
 * Root redirect
 * --------------------------------------------------------------------
 * Visitors hitting "/" are forwarded to the default/negotiated locale.
 */
$routes->get('/', 'LanguageController::index');

/*
 * --------------------------------------------------------------------
 * SEO endpoints (no locale prefix)
 * --------------------------------------------------------------------
 * robots.txt statik dosya değil: Sitemap satırının ortamın gerçek
 * adresini (base_url) taşıması için buradan servis edilir.
 */
$routes->get('sitemap.xml', 'Sitemap::index', ['as' => 'sitemap']);
$routes->get('robots.txt', 'Sitemap::robots');

/*
 * QR Araç Kimlik Kartı (§5.1) — QR kısa kalsın diye locale önekinsiz;
 * sayfa, ziyaretçinin çerez/tarayıcı diliyle yerelleşir.
 */
$routes->get('v/(:segment)', 'Vehicle::card/$1', ['as' => 'vehicle.card']);

/*
 * --------------------------------------------------------------------
 * Jarvis paneli (§11) — kimlikli dünya (locale önekinsiz, dizine girmez)
 * --------------------------------------------------------------------
 * Giriş/çıkış rotaları Shield'den gelir; kayıt (register) ve magic-link
 * BİLİNÇLİ kapalı — kullanıcı yalnız "spark panel:admin" ile açılır.
 */
service('auth')->routes($routes, ['except' => ['register', 'magic-link']]);

$routes->group('panel', ['filter' => 'session'], static function (RouteCollection $routes): void {
    $routes->get('/', 'Panel::index', ['as' => 'panel']);
    $routes->get('alerts', 'Panel::alerts', ['as' => 'panel.alerts']);
    $routes->post('alerts/archive', 'Panel::archiveAlerts', ['as' => 'panel.alerts.archive']);
    $routes->get('leads', 'Panel::leads', ['as' => 'panel.leads']);
    $routes->get('intents', 'Panel::intents', ['as' => 'panel.intents']);
    $routes->get('faq-candidates', 'Panel::faqCandidates', ['as' => 'panel.faq']);
    $routes->get('vehicles', 'Panel::vehicles', ['as' => 'panel.vehicles']);
    $routes->post('vehicles', 'Panel::addVehicle', ['as' => 'panel.vehicles.add']);
    $routes->get('agents', 'Panel::agents', ['as' => 'panel.agents']);
    $routes->post('agents/toggle', 'Panel::toggleAgent', ['as' => 'panel.agents.toggle']);
    $routes->get('content', 'Panel::content', ['as' => 'panel.content']);
    $routes->post('content/decide', 'Panel::decideContent', ['as' => 'panel.content.decide']);
});

/*
 * --------------------------------------------------------------------
 * Localized routes
 * --------------------------------------------------------------------
 * Every public page lives under a /{locale} prefix (e.g. /tr, /en/blog).
 * The {locale} placeholder is automatically matched against the
 * supportedLocales list and exposed to controllers via $request->getLocale().
 */
$routes->group('{locale}', static function (RouteCollection $routes): void {
    $routes->get('/', 'Home::index', ['as' => 'home']);
    $routes->get('blog', 'Blog::index', ['as' => 'blog']);
    $routes->get('blog/(:segment)', 'Blog::detail/$1', ['as' => 'blog.detail']);
    $routes->get('services', 'Services::index', ['as' => 'services']);
    $routes->get('services/(:segment)', 'Services::detail/$1', ['as' => 'services.detail']);
    $routes->get('products', 'Products::index', ['as' => 'products']);
    $routes->get('products/(:segment)', 'Products::category/$1', ['as' => 'products.category']);
    $routes->get('products/(:segment)/(:segment)', 'Products::detail/$1/$2', ['as' => 'products.detail']);
    $routes->get('media', 'Media::index', ['as' => 'media']);

    // Trafik motorları: hesaplayıcılar + "Hangi treyler bana uygun?" testi + SSS.
    $routes->get('tools', 'Tools::index', ['as' => 'tools']);
    $routes->get('quiz', 'Tools::quiz', ['as' => 'quiz']);

    // Treyler Ansiklopedisi — markdown içerik motoru (app/Data/content/wiki)
    $routes->get('wiki', 'Wiki::index', ['as' => 'wiki']);
    $routes->get('wiki/(:segment)', 'Wiki::detail/$1', ['as' => 'wiki.detail']);

    // Ülke/pazar sayfaları (§7.11) — app/Data/markets.json kayıt defteri
    $routes->get('markets', 'Markets::index', ['as' => 'markets']);
    $routes->get('markets/(:segment)', 'Markets::detail/$1', ['as' => 'markets.detail']);

    // Üretim haberleri (§7.6 içerik fabrikası çıktısı) — markdown motoru
    $routes->get('news', 'News::index', ['as' => 'news']);
    $routes->get('news/(:segment)', 'News::detail/$1', ['as' => 'news.detail']);

    // Şasi doğrulama (§5.9): orijinallik + garanti sorgusu
    $routes->get('verify', 'Vehicle::verify', ['as' => 'verify']);
    $routes->post('verify', 'Vehicle::check', ['as' => 'verify.check']);

    // Basın Odası (§7.10): public/assets/brand'dan otomatik derlenen arşiv
    $routes->get('press', 'Press::index', ['as' => 'press']);
    $routes->get('faq', 'Faq::index', ['as' => 'faq']);

    // Bekleme Salonu (§8): oyun merkezi + Treyler Tetris + Günün Sorusu.
    $routes->get('games', 'Games::index', ['as' => 'games']);
    $routes->get('games/tetris', 'Games::tetris', ['as' => 'games.tetris']);
    $routes->get('games/daily', 'Games::daily', ['as' => 'games.daily']);

    // Şeffaflık sayfaları (KVKK / gizlilik / çerez) — footer yasal barı.
    $routes->get('kvkk', 'Legal::page/kvkk', ['as' => 'legal.kvkk']);
    $routes->get('privacy', 'Legal::page/privacy', ['as' => 'legal.privacy']);
    $routes->get('cookies', 'Legal::page/cookies', ['as' => 'legal.cookies']);

    // Teklif (Quote) — ürün-bağlamlı teklif akışı.
    //   teklif                          → ürün seçtirme sayfası (genel butonlar)
    //   teklif/{kategori}/{ürün}         → WhatsApp'a yönlendirir
    //   teklif/{kategori}/{ürün}/{varyant} → belirli varyant için
    $routes->get('teklif', 'Quote::index', ['as' => 'quote']);
    $routes->get('teklif/(:segment)/(:segment)', 'Quote::start/$1/$2', ['as' => 'quote.start']);
    $routes->get('teklif/(:segment)/(:segment)/(:segment)', 'Quote::start/$1/$2/$3', ['as' => 'quote.start.variant']);

    $routes->get('contact', 'Contact::index', ['as' => 'contact']);
    $routes->post('contact', 'Contact::submit', ['as' => 'contact.submit']);

    // Yapay zeka asistanı (ana sayfa #asistan konsolu) — yalnızca POST/JSON.
    $routes->post('ai/ask', 'Ai::ask', ['as' => 'ai.ask']);
});
