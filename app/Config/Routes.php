<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

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
    $routes->get('contact', 'Contact::index', ['as' => 'contact']);
    $routes->post('contact', 'Contact::submit', ['as' => 'contact.submit']);
});
