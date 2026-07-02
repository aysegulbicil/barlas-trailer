<?php $this->extend('layouts/panel') ?>

<?php $this->section('title') ?>İçerik Fabrikası<?php $this->endSection() ?>

<?php $this->section('content') ?>

<h1 class="panel-h1">İçerik Fabrikası — Onay Kuyruğu</h1>

<?php if (! empty($message)): ?>
    <div class="panel-box"><span class="panel-badge panel-badge--ok">✓</span> <?= esc($message) ?></div>
<?php endif; ?>
<?php if (! empty($error)): ?>
    <div class="panel-box"><span class="panel-badge panel-badge--no">!</span> <?= esc($error) ?></div>
<?php endif; ?>

<div class="panel-box">
    <?php if ($drafts === []): ?>
        <p class="panel-empty">
            Bekleyen taslak yok. Yeni araç kaydı girildikten sonra konteynerde
            <code>php spark agents:content-factory</code> çalıştırın (VPS'te cron'a bağlanacak).
        </p>
    <?php else: ?>
        <?php foreach ($drafts as $draft): ?>
            <article style="border-bottom:1px solid rgba(127,152,190,.2); padding-block:1rem">
                <h2 style="margin:0 0 .3rem; font-size:1.05rem"><?= esc($draft['title'] ?? '') ?></h2>
                <p class="panel-empty" style="margin:0 0 .6rem">
                    Kaynak: <?= esc($draft['source']['model'] ?? '?') ?>
                    (şasi <?= esc($draft['source']['chassis_masked'] ?? '—') ?>) ·
                    Taslak: <?= esc(substr((string) ($draft['created'] ?? ''), 0, 16)) ?>
                </p>
                <details style="margin-block-end:.6rem">
                    <summary>Haber gövdesi (markdown)</summary>
                    <pre style="white-space:pre-wrap"><?= esc($draft['body'] ?? '') ?></pre>
                </details>
                <details style="margin-block-end:.8rem">
                    <summary>Sosyal medya taslağı (kopyala-yapıştır)</summary>
                    <pre style="white-space:pre-wrap"><?= esc($draft['social'] ?? '') ?></pre>
                </details>
                <div style="display:flex; gap:.6rem">
                    <form method="post" action="/panel/content/decide">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= esc($draft['id'] ?? '', 'attr') ?>">
                        <input type="hidden" name="action" value="approve">
                        <button type="submit" class="panel-btn">✓ Onayla ve yayınla</button>
                    </form>
                    <form method="post" action="/panel/content/decide">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= esc($draft['id'] ?? '', 'attr') ?>">
                        <input type="hidden" name="action" value="reject">
                        <button type="submit" class="panel-btn panel-btn--danger">✕ Reddet</button>
                    </form>
                </div>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<h2 class="panel-h1" style="font-size:1.1rem; margin-block-start:1.6rem">Son yayınlananlar</h2>

<div class="panel-box">
    <?php if ($published === []): ?>
        <p class="panel-empty">Henüz yayınlanan haber yok.</p>
    <?php else: ?>
        <table class="panel-table">
            <thead><tr><th>Başlık</th><th>Yayın</th><th>Bağlantı</th></tr></thead>
            <tbody>
            <?php foreach ($published as $row): ?>
                <tr>
                    <td><?= esc($row['title'] ?? '') ?></td>
                    <td><?= esc(substr((string) ($row['published_at'] ?? ''), 0, 10)) ?></td>
                    <td><a href="/tr/news/<?= esc($row['id'] ?? '', 'attr') ?>" target="_blank" rel="noopener">/tr/news/<?= esc($row['id'] ?? '') ?></a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <p class="panel-empty" style="margin-top:0.9rem">
            Çeviri dosyaları (<code>app/Data/content/news/{en,ru,ar,fr}/</code>) eklenene kadar
            diğer diller Türkçe içeriğe düşer; AI çeviri hattı (§3) kurulunca otomatik dolacak.
        </p>
    <?php endif; ?>
</div>

<?php $this->endSection() ?>
