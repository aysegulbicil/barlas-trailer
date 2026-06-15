<?php

namespace App\Controllers;

use Config\App;

/**
 * LanguageController
 *
 * Handles locale entry points that live OUTSIDE the "/{locale}" group:
 *  - index():  the bare "/" root, redirects to the best locale.
 *  - switch(): "/lang/{locale}", switches language and stores the choice.
 */
class LanguageController extends BaseController
{
    /**
     * Root entry point. Sends visitors to a locale-prefixed home page,
     * preferring a previously stored choice, then the negotiated locale.
     */
    public function index(): \CodeIgniter\HTTP\RedirectResponse
    {
        $locale = $this->resolvePreferredLocale();

        return redirect()->to('/' . $locale);
    }

    /**
     * Stores the chosen locale in a long-lived cookie and redirects the
     * visitor back to the page they came from, rewritten for that locale.
     */
    public function switch(string $locale): \CodeIgniter\HTTP\RedirectResponse
    {
        $config = config(App::class);

        if (! in_array($locale, $config->supportedLocales, true)) {
            $locale = $config->defaultLocale;
        }

        // Remember the choice for one year.
        helper('cookie');
        set_cookie('locale', $locale, YEAR);

        // Figure out where to return: the referrer path rewritten to $locale,
        // falling back to that locale's home page.
        helper('locale');
        $referer = previous_url();
        $target  = '/' . $locale;

        if (is_string($referer) && $referer !== '') {
            $path  = trim((string) parse_url($referer, PHP_URL_PATH), '/');
            $parts = $path === '' ? [] : explode('/', $path);

            // Drop framework noise ("index.php") and any empty segments.
            $parts = array_values(array_filter($parts, static fn (string $p): bool => $p !== '' && $p !== 'index.php'));

            // Drop a leading locale segment so we can swap in the new one.
            if (isset($parts[0]) && in_array($parts[0], $config->supportedLocales, true)) {
                array_shift($parts);
            }

            $target = $parts === [] ? '/' . $locale : '/' . $locale . '/' . implode('/', $parts);
        }

        return redirect()->to($target);
    }

    /**
     * Picks the best locale: stored cookie first, then Accept-Language
     * negotiation, then the configured default.
     */
    private function resolvePreferredLocale(): string
    {
        $config = config(App::class);

        helper('cookie');
        $cookie = get_cookie('locale');

        if (is_string($cookie) && in_array($cookie, $config->supportedLocales, true)) {
            return $cookie;
        }

        // negotiateLocale = true makes the request negotiate against
        // supportedLocales using the Accept-Language header.
        $negotiated = $this->request->getLocale();

        return in_array($negotiated, $config->supportedLocales, true)
            ? $negotiated
            : $config->defaultLocale;
    }
}
