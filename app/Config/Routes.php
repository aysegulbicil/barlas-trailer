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
