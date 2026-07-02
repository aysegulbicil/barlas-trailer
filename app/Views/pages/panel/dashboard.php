<?php $this->extend('layouts/panel') ?>

<?php $this->section('title') ?>Brifing<?php $this->endSection() ?>

<?php $this->section('content') ?>

<h1 class="panel-h1">Komuta Merkezi</h1>

<div class="panel-cards">
    <a class="panel-card" href="/panel/leads">
        <span class="panel-card__num"><?= esc((string) $counts['leads']) ?></span>
        <span class="panel-card__label">Lead (bu ay)</span>
    </a>
    <a class="panel-card" href="/panel/intents">
        <span class="panel-card__num"><?= esc((string) $counts['intents']) ?></span>
        <span class="panel-card__label">Teklif niyeti (bu ay)</span>
    </a>
    <a class="panel-card" href="/panel/faq-candidates">
        <span class="panel-card__num"><?= esc((string) $counts['asks']) ?></span>
        <span class="panel-card__label">Asistan sorusu (bu ay)</span>
    </a>
    <a class="panel-card" href="/panel/alerts">
        <span class="panel-card__num"><?= esc((string) $counts['alerts']) ?></span>
        <span class="panel-card__label">Bekleyen alarm</span>
    </a>
    <?php if (is_array($rates) && isset($rates['rates']['usd']['sell'])): ?>
        <div class="panel-card">
            <span class="panel-card__num"><?= esc(number_format((float) $rates['rates']['usd']['sell'], 2)) ?></span>
            <span class="panel-card__label">USD satış (TCMB)</span>
        </div>
        <div class="panel-card">
            <span class="panel-card__num"><?= esc(number_format((float) $rates['rates']['eur']['sell'], 2)) ?></span>
            <span class="panel-card__label">EUR satış (TCMB)</span>
        </div>
    <?php endif; ?>
</div>

<div class="panel-box">
    <h2>Son sabah brifingi<?= $briefing ? ' — ' . esc($briefing['name']) : '' ?></h2>
    <?php if ($briefing): ?>
        <pre class="panel-pre"><?= esc($briefing['body']) ?></pre>
    <?php else: ?>
        <p class="panel-empty">Henüz brifing üretilmedi. Konteynerde <code>php spark agents:digest</code> çalıştırın
            (cron'a bağlanınca her sabah otomatik gelir).</p>
    <?php endif; ?>
</div>

<?php $this->endSection() ?>
