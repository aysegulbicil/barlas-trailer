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
 * İç Araçlar hub'ı (/patron) — locale önekinsiz, dizine girmez
 * --------------------------------------------------------------------
 * Bu rotalar, Docker/Apache ortamında deploy/apache-apps.conf Alias'ının
 * sunduğu apps/_shared/auth/public/*.php dosyalarının CI4 karşılığıdır.
 * Alias VARSA istekler ona düşer (bu grup devreye girmez); Alias YOKSA
 * (ör. Hostinger paylaşımlı hosting) /patron artık burada çalışır — 404 biter.
 * Eski .php bağlantıları da (bookmark / next parametresi) aynı metotlara düşsün.
 */
$routes->get('patron', 'Patron::index', ['as' => 'patron']);
$routes->get('patron/index.php', 'Patron::index');
$routes->match(['get', 'post'], 'patron/login', 'Patron::login', ['as' => 'patron.login']);
$routes->match(['get', 'post'], 'patron/login.php', 'Patron::login');
$routes->get('patron/logout', 'Patron::logout', ['as' => 'patron.logout']);
$routes->get('patron/logout.php', 'Patron::logout');
$routes->get('patron/go-qr', 'Patron::goQr', ['as' => 'patron.goqr']);
$routes->get('patron/go-qr.php', 'Patron::goQr');

/*
 * --------------------------------------------------------------------
 * İç araçlar: /fatura + /teklif (locale önekinsiz, dizine girmez)
 * --------------------------------------------------------------------
 * Eski apps/ Alias düzeninin ana uygulamaya gömülmüş hâli (2026-07-10):
 * fatura sayfaları CI4 controller/view, teklif SPA'sı public/teklif/ui
 * statikleri + bu API rotaları üzerinden çalışır. appsauth filtresi
 * /patron girişini şart koşar. cachePage BİLİNÇLİ kullanılmaz.
 */
$routes->group('fatura', ['filter' => 'appsauth'], static function (RouteCollection $routes): void {
    $routes->match(['get', 'post'], '/', 'Fatura::index', ['as' => 'fatura']);
    $routes->match(['get', 'post'], 'faturalar', 'Fatura::invoices', ['as' => 'fatura.invoices']);
    $routes->match(['get', 'post'], 'form', 'Fatura::form', ['as' => 'fatura.form']);
    $routes->match(['get', 'post'], 'firmalar', 'Fatura::companies', ['as' => 'fatura.companies']);
    $routes->match(['get', 'post'], 'import', 'Fatura::import', ['as' => 'fatura.import']);
    $routes->get('export', 'Fatura::export', ['as' => 'fatura.export']);
});

$routes->group('teklif', ['filter' => 'appsauth'], static function (RouteCollection $routes): void {
    $routes->get('/', 'Teklif::index', ['as' => 'teklif']);
    $routes->get('index.php', 'Teklif::index');
    // app.js göreli çağırır: ../server/api/save.php, ../data/x.json, ../offers/...
    $routes->match(['get', 'post'], 'server/api/(:segment)', 'Teklif::api/$1');
    $routes->get('data/(:segment)', 'Teklif::data/$1');
    $routes->get('offers/(:any)', 'Teklif::offer/$1');
});

/*
 * --------------------------------------------------------------------
 * QR personel/yoklama uygulaması (locale önekinsiz, dizine girmez)
 * --------------------------------------------------------------------
 * apps/qr (ikinci CI4) 2026-07-10'da ana uygulamaya gömüldü; bu grup,
 * apps/qr/app/Config/Routes.php'nin /qr önekli birebir karşılığıdır.
 * Kendi personel girişi vardır (qrauth/qradmin, oturumda user_id+role);
 * hub'dan geçiş /patron/go-qr → /qr/sso (HMAC token). Girişsiz rotalar:
 * kök (login), q/{kod} (telefonla QR okutma), display/* (ekran), sso.
 */
$routes->group('qr', ['namespace' => 'App\Controllers\Qr'], static function (RouteCollection $routes): void {
    $routes->get('/', 'Auth::login', ['as' => 'qr']);
    $routes->get('q/(:segment)', 'Scan::location/$1');
    $routes->get('display/(:num)/(:segment)/token', 'Display::token/$1/$2');
    $routes->get('display/(:num)/(:segment)', 'Display::screen/$1/$2');

    $routes->get('login', 'Auth::login', ['as' => 'qr.login']);
    $routes->post('login', 'Auth::attemptLogin');
    $routes->get('logout', 'Auth::logout', ['as' => 'qr.logout']);
    $routes->get('sso', 'Sso::enter'); // hub'dan tokenli otomatik giriş

    $routes->group('', ['filter' => 'qrauth'], static function (RouteCollection $routes): void {
        $routes->get('dashboard', 'Dashboard::index', ['as' => 'qr.dashboard']);
        $routes->get('punch', 'Punch::index');
        $routes->get('history', 'History::index');
        $routes->post('attendance/punch', 'Attendance::punch');
        $routes->get('requests', 'Requests::index');
        $routes->post('requests/leave', 'Requests::createLeave');
        $routes->post('requests/advance', 'Requests::createAdvance');
        $routes->get('notifications', 'Notifications::index');
        $routes->get('gorevlerim', 'Tasks::mine');
        $routes->post('gorevlerim/(:num)/durum', 'Tasks::updateStatus/$1');
    });

    $routes->group('admin', ['filter' => 'qradmin', 'namespace' => 'App\Controllers\Qr\Admin'], static function (RouteCollection $routes): void {
        $routes->get('', 'Home::index', ['as' => 'qr.admin']);

        $routes->get('employees', 'Employees::index');
        $routes->get('employees/new', 'Employees::new');
        $routes->post('employees', 'Employees::create');
        $routes->post('employees/bulk', 'Employees::bulk');
        $routes->get('employees/(:num)/edit', 'Employees::edit/$1');
        $routes->post('employees/(:num)', 'Employees::update/$1');
        $routes->get('employees/(:num)', 'Employees::show/$1');

        $routes->get('departments', 'Departments::index');
        $routes->get('departments/new', 'Departments::new');
        $routes->post('departments', 'Departments::create');
        $routes->get('departments/(:num)/edit', 'Departments::edit/$1');
        $routes->post('departments/(:num)', 'Departments::update/$1');
        $routes->post('departments/(:num)/delete', 'Departments::delete/$1');

        $routes->get('positions', 'Positions::index');
        $routes->get('positions/new', 'Positions::new');
        $routes->post('positions', 'Positions::create');
        $routes->get('positions/(:num)/edit', 'Positions::edit/$1');
        $routes->post('positions/(:num)', 'Positions::update/$1');
        $routes->post('positions/(:num)/delete', 'Positions::delete/$1');

        $routes->get('shifts', 'Shifts::index');
        $routes->get('shifts/new', 'Shifts::new');
        $routes->post('shifts', 'Shifts::create');
        $routes->get('shifts/(:num)/edit', 'Shifts::edit/$1');
        $routes->post('shifts/(:num)', 'Shifts::update/$1');
        $routes->post('shifts/(:num)/delete', 'Shifts::delete/$1');
        $routes->get('shift-schedule', 'ShiftSchedule::index');
        $routes->post('shift-schedule', 'ShiftSchedule::save');

        $routes->get('locations', 'Locations::index');
        $routes->get('locations/new', 'Locations::new');
        $routes->post('locations', 'Locations::create');
        $routes->get('locations/(:num)/edit', 'Locations::edit/$1');
        $routes->post('locations/(:num)', 'Locations::update/$1');
        $routes->post('locations/(:num)/delete', 'Locations::delete/$1');
        $routes->get('locations/(:num)/qr', 'Locations::qr/$1');
        $routes->get('locations/(:num)/token', 'Locations::token/$1');

        $routes->get('suspicious', 'Suspicious::index');
        $routes->get('attendance', 'Attendance::index');
        $routes->get('attendance/new', 'Attendance::new');
        $routes->post('attendance', 'Attendance::create');
        $routes->get('attendance/(:num)/edit', 'Attendance::edit/$1');
        $routes->post('attendance/(:num)', 'Attendance::update/$1');
        $routes->post('attendance/(:num)/delete', 'Attendance::delete/$1');
        $routes->get('tasks', 'Tasks::index');
        $routes->get('tasks/new', 'Tasks::new');
        $routes->post('tasks', 'Tasks::create');
        $routes->get('tasks/(:num)/edit', 'Tasks::edit/$1');
        $routes->post('tasks/(:num)', 'Tasks::update/$1');
        $routes->post('tasks/(:num)/delete', 'Tasks::delete/$1');

        $routes->get('requests', 'Requests::index');
        $routes->post('requests/leave/(:num)/approve', 'Requests::approveLeave/$1');
        $routes->post('requests/leave/(:num)/reject', 'Requests::rejectLeave/$1');
        $routes->post('requests/advance/(:num)/approve', 'Requests::approveAdvance/$1');
        $routes->post('requests/advance/(:num)/reject', 'Requests::rejectAdvance/$1');

        $routes->get('payroll', 'Payroll::index');
        $routes->post('payroll/close', 'Payroll::close');
        $routes->post('payroll/reopen', 'Payroll::reopen');
        $routes->get('payroll/(:num)', 'Payroll::show/$1');
        $routes->post('payroll/(:num)/advance', 'Payroll::addAdvance/$1');
        $routes->post('payroll/(:num)/advance/(:num)/delete', 'Payroll::deleteAdvance/$1/$2');

        $routes->get('reports', 'Reports::index');
        $routes->get('reports/export', 'Reports::export');
        $routes->get('reports/print', 'Reports::printView');
        $routes->get('settings', 'Settings::index');
        $routes->post('settings', 'Settings::save');
    });
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
