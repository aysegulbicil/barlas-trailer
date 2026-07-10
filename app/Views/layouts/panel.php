<?php

/**
 * Jarvis paneli layout'u — iç araç, bağımsız ve hafif (site temasından
 * ayrı; CDN yok). Tek dilli (Ana Admin Türkçe) — kamusal 5-dil parite
 * kuralı kapsamı dışında, bilinçli istisna.
 */
$panelCss = 'assets/css/panel.css';

$nav = [
    '/panel'                => 'Brifing',
    '/panel/alerts'         => 'Alarmlar',
    '/panel/leads'          => 'Lead\'ler',
    '/panel/intents'        => 'Teklif Niyetleri',
    '/panel/faq-candidates' => 'SSS Adayları',
    '/panel/content'        => 'İçerik',
    '/panel/vehicles'       => 'Araçlar',
    '/panel/metrics'        => 'Analitik',
    '/panel/agents'         => 'Ajanlar',
];
$currentPath = '/' . trim(uri_string(), '/');
?>
<!DOCTYPE html>
<html lang="tr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= esc($this->renderSection('title') ?: 'Jarvis') ?> · Barlas Panel</title>
    <link rel="stylesheet" href="<?= base_url($panelCss) ?>?v=<?= is_file(FCPATH . $panelCss) ? filemtime(FCPATH . $panelCss) : '1' ?>">
</head>
<body class="panel">

<header class="panel-top">
    <span class="panel-top__brand">⚡ Jarvis <small>v1</small></span>
    <nav class="panel-top__nav" aria-label="Panel">
        <?php foreach ($nav as $href => $label): ?>
            <a class="<?= $currentPath === $href ? 'is-active' : '' ?>" href="<?= esc($href) ?>"><?= esc($label) ?></a>
        <?php endforeach; ?>
    </nav>
    <div class="panel-top__user">
        <span><?= esc(auth()->user()->email ?? '') ?></span>
        <a class="panel-top__logout" href="<?= esc(url_to('logout')) ?>">Çıkış</a>
    </div>
</header>

<main class="panel-main">
    <?= $this->renderSection('content') ?>
</main>

</body>
</html>
