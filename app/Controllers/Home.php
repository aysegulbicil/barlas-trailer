<?php

namespace App\Controllers;

/**
 * Home controller
 *
 * Renders the localized landing page. The active locale is already
 * resolved by LanguageFilter from the {locale} URI segment, so all
 * lang() calls in the view return the correct translation.
 */
class Home extends BaseController
{
    public function index(): string
    {
        $data = [
            // Page-level SEO overrides (fall back to Common.* defaults if omitted).
            'metaTitle'       => lang('Common.meta_title'),
            'metaDescription' => lang('Common.meta_description'),
        ];

        return view('pages/home', $data);
    }
}
