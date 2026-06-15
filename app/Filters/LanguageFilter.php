<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\App;
use Config\Services;

/**
 * LanguageFilter
 *
 * Resolves the active locale for every request from the first URI segment
 * (e.g. /tr, /en/blog) and applies it to the request and to the framework
 * language service. If the segment is not a supported locale, the request
 * is left untouched and the default locale is used.
 */
class LanguageFilter implements FilterInterface
{
    /**
     * @param array|null $arguments
     *
     * @return void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $config           = config(App::class);
        $supportedLocales = $config->supportedLocales;

        $segment = $request->getUri()->getSegment(1);

        $isLocaleSegment = in_array($segment, $supportedLocales, true);
        $locale          = $isLocaleSegment ? $segment : $config->defaultLocale;

        // Apply to the request (drives $request->getLocale()) and to the
        // language service so lang() calls resolve the right translation.
        $request->setLocale($locale);
        Services::language()->setLocale($locale);

        // Remember the visitor's language while they browse a localized page,
        // so the bare "/" root can redirect them back to it on a return visit.
        if ($isLocaleSegment) {
            helper('cookie');
            set_cookie('locale', $locale, YEAR);
        }
    }

    /**
     * @param array|null $arguments
     *
     * @return void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No post-processing required.
    }
}
