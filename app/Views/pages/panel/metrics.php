<?php $this->extend('layouts/panel') ?>

<?php $this->section('title') ?>Analitik<?php $this->endSection() ?>

<?php $this->section('content') ?>

<?php
/**
 * Analitik ekranı — birinci taraf beacon akışının 30 günlük özeti.
 * Veri: Panel::metrics (writable/data/metrics/hits-*.jsonl).
 * Grafik saf CSS'tir (yükseklik yüzdesi); dış kütüphane yüklenmez.
 */
$maxHits = max(1, max(array_column($days, 'hits')));
?>

<style>
    .mchart { display: flex; align-items: flex-end; gap: 3px; height: 140px; margin-top: 12px; }
    .mchart__bar { flex: 1; min-width: 4px; background: linear-gradient(180deg, #4da6ff, #1f6fd6); border-radius: 3px 3px 0 0; opacity: .85; }
    .mchart__bar:hover { opacity: 1; }
    .mchart__bar--empty { background: rgba(255,255,255,.08); }
    .mgrid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; align-items: start; }
    .mnote { opacity: .65; font-size: .85em; margin-top: 8px; }
</style>

<h1 class="panel-h1">Analitik (birinci taraf, çerezsiz)</h1>

<div class="panel-cards">
    <div class="panel-card">
        <span class="panel-card__num"><?= esc((string) $today['hits']) ?></span>
        <span class="panel-card__label">Bugün görüntüleme</span>
    </div>
    <div class="panel-card">
        <span class="panel-card__num"><?= esc((string) $today['uniques']) ?></span>
        <span class="panel-card__label">Bugün tekil ziyaretçi</span>
    </div>
    <div class="panel-card">
        <span class="panel-card__num"><?= esc((string) $totals['hits7']) ?></span>
        <span class="panel-card__label">Son 7 gün görüntüleme</span>
    </div>
    <div class="panel-card">
        <span class="panel-card__num"><?= esc((string) $totals['uniq7']) ?></span>
        <span class="panel-card__label">Son 7 gün tekil (günlük toplam)</span>
    </div>
    <div class="panel-card">
        <span class="panel-card__num"><?= esc((string) $totals['hits30']) ?></span>
        <span class="panel-card__label">Son 30 gün görüntüleme</span>
    </div>
</div>

<div class="panel-box">
    <h2>Günlük görüntüleme (30 gün)</h2>
    <?php if ($totals['hits30'] === 0): ?>
        <p class="panel-empty">Henüz kayıt yok. Beacon her kamusal sayfada çalışır; ilk ziyaretlerle birlikte
            burada birikmeye başlar (botlar sayılmaz).</p>
    <?php else: ?>
        <div class="mchart">
            <?php foreach ($days as $day): ?>
                <div class="mchart__bar <?= $day['hits'] === 0 ? 'mchart__bar--empty' : '' ?>"
                     style="height: <?= max(2, (int) round($day['hits'] / $maxHits * 100)) ?>%"
                     title="<?= esc($day['date'], 'attr') ?> — <?= esc((string) $day['hits'], 'attr') ?> görüntüleme, <?= esc((string) $day['uniques'], 'attr') ?> tekil"></div>
            <?php endforeach; ?>
        </div>
        <p class="mnote">Çubuğun üzerine gelin: tarih + görüntüleme + tekil. Tekil ziyaretçi hash'i günlük tuzla
            üretilir; kimlik ertesi gün çözülemez (KVKK dostu tasarım).</p>
    <?php endif; ?>
</div>

<div class="mgrid">
    <div class="panel-box">
        <h2>En çok görüntülenen sayfalar (30 gün)</h2>
        <?php if ($pages === []): ?>
            <p class="panel-empty">Kayıt yok.</p>
        <?php else: ?>
            <table class="panel-table">
                <thead><tr><th>Sayfa</th><th>Görüntüleme</th></tr></thead>
                <tbody>
                <?php foreach ($pages as $path => $count): ?>
                    <tr><td><?= esc($path) ?></td><td><?= esc((string) $count) ?></td></tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div class="panel-box">
        <h2>Dil dağılımı (30 gün)</h2>
        <?php if ($locales === []): ?>
            <p class="panel-empty">Kayıt yok.</p>
        <?php else: ?>
            <table class="panel-table">
                <thead><tr><th>Dil</th><th>Görüntüleme</th></tr></thead>
                <tbody>
                <?php foreach ($locales as $locale => $count): ?>
                    <tr><td><?= esc($locale) ?></td><td><?= esc((string) $count) ?></td></tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div class="panel-box">
        <h2>Dış kaynaklar (30 gün)</h2>
        <?php if ($referrers === []): ?>
            <p class="panel-empty">Henüz dış kaynak yok (doğrudan girişler burada görünmez).</p>
        <?php else: ?>
            <table class="panel-table">
                <thead><tr><th>Kaynak</th><th>Gelen</th></tr></thead>
                <tbody>
                <?php foreach ($referrers as $host => $count): ?>
                    <tr><td><?= esc($host) ?></td><td><?= esc((string) $count) ?></td></tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php $this->endSection() ?>
