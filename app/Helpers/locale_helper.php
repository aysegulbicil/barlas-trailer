<?php

/**
 * Locale helper
 *
 * Small utility functions to work with the multilingual (i18n) layer.
 * All public pages live under a "/{locale}" URL prefix, so these helpers
 * centralize locale detection, URL building and RTL handling.
 */

use Config\App;

if (! function_exists('supported_locales')) {
    /**
     * Returns the list of locales the application supports.
     *
     * @return list<string>
     */
    function supported_locales(): array
    {
        return config(App::class)->supportedLocales;
    }
}

if (! function_exists('default_locale')) {
    /**
     * Returns the application default locale.
     */
    function default_locale(): string
    {
        return config(App::class)->defaultLocale;
    }
}

if (! function_exists('current_locale')) {
    /**
     * Returns the locale resolved for the current request.
     */
    function current_locale(): string
    {
        return service('request')->getLocale();
    }
}

if (! function_exists('is_rtl')) {
    /**
     * Whether the given locale (or the current one) is right-to-left.
     */
    function is_rtl(?string $locale = null): bool
    {
        $locale ??= current_locale();

        return in_array($locale, ['ar', 'fa', 'he', 'ur'], true);
    }
}

if (! function_exists('locale_direction')) {
    /**
     * HTML "dir" attribute value for the given/current locale.
     */
    function locale_direction(?string $locale = null): string
    {
        return is_rtl($locale) ? 'rtl' : 'ltr';
    }
}

if (! function_exists('locale_url')) {
    /**
     * Builds an absolute URL for a path within a given locale.
     *
     * Example: locale_url('blog', 'en') => https://site/en/blog
     */
    function locale_url(string $path = '', ?string $locale = null): string
    {
        $locale ??= current_locale();
        $path = trim($path, '/');

        $segment = $path === '' ? $locale : $locale . '/' . $path;

        return site_url($segment);
    }
}

if (! function_exists('current_path')) {
    /**
     * Returns the current request path WITHOUT the leading locale segment.
     */
    function current_path(): string
    {
        $uri      = service('request')->getUri();
        $segments = $uri->getSegments();

        // Drop the first segment if it is a known locale.
        if (isset($segments[0]) && in_array($segments[0], supported_locales(), true)) {
            array_shift($segments);
        }

        return implode('/', $segments);
    }
}

if (! function_exists('switch_locale_url')) {
    /**
     * Returns the current page URL rewritten for a different locale.
     * Used by the language switcher to keep the visitor on the same page.
     */
    function switch_locale_url(string $locale): string
    {
        return locale_url(current_path(), $locale);
    }
}

if (! function_exists('locale_native_name')) {
    /**
     * Human-readable native name for a locale, used in the language switcher.
     */
    function locale_native_name(string $locale): string
    {
        $names = [
            'tr' => 'Türkçe',
            'en' => 'English',
            'ru' => 'Русский',
            'ar' => 'العربية',
            'fr' => 'Français',
        ];

        return $names[$locale] ?? strtoupper($locale);
    }
}
