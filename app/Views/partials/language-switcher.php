<?php

/**
 * Language switcher partial
 *
 * Dropdown listing every supported locale. Each link points to the SAME
 * page in the target language (switch_locale_url) so the visitor keeps
 * their place. The active locale is marked for accessibility.
 */
$active = current_locale();
?>
<div class="lang-switcher" data-lang-switcher>
    <button class="lang-switcher__toggle" type="button" aria-haspopup="true" aria-expanded="false"
            aria-label="<?= esc(lang('Navigation.choose_language'), 'attr') ?>">
        <span class="lang-switcher__current"><?= esc(strtoupper($active)) ?></span>
        <span class="lang-switcher__caret" aria-hidden="true">&#9662;</span>
    </button>
    <ul class="lang-switcher__menu" role="menu">
        <?php foreach (supported_locales() as $loc): ?>
            <li role="none">
                <a role="menuitem"
                   class="lang-switcher__item<?= $loc === $active ? ' is-active' : '' ?>"
                   hreflang="<?= esc($loc, 'attr') ?>"
                   href="<?= esc(switch_locale_url($loc)) ?>"
                   <?= $loc === $active ? 'aria-current="true"' : '' ?>>
                    <span class="lang-switcher__code"><?= esc(strtoupper($loc)) ?></span>
                    <span class="lang-switcher__name"><?= esc(locale_native_name($loc)) ?></span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
