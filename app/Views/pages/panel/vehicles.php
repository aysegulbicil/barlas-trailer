<?php $this->extend('layouts/panel') ?>

<?php $this->section('title') ?>Araçlar<?php $this->endSection() ?>

<?php $this->section('content') ?>

<h1 class="panel-h1">Araç Kayıt Defteri (QR Kimlik Kartı)</h1>

<?php if (! empty($message)): ?>
    <div class="panel-box"><span class="panel-badge panel-badge--ok">✓</span> <?= esc($message) ?></div>
<?php endif; ?>
<?php if (! empty($error)): ?>
    <div class="panel-box"><span class="panel-badge panel-badge--no">!</span> <?= esc($error) ?></div>
<?php endif; ?>

<div class="panel-box">
    <h2>Yeni araç ekle</h2>
    <p class="panel-empty">Kaydedilen her araç için otomatik QR kimlik kartı üretilir; QR etiketi araca yapıştırılır,
        müşteri okutunca aracın kamusal sayfası açılır. Şasi doğrulama formu da bu kayıtlardan beslenir.</p>
    <form method="post" action="/panel/vehicles" class="panel-vform">
        <?= csrf_field() ?>
        <label>Şasi No *<input name="chassis_no" required maxlength="40" placeholder="NP932000123456789"></label>
        <label>Model *<input name="model" required maxlength="160" placeholder="Gıda Tankeri 34m³"></label>
        <label>Üretim yılı<input name="year" type="number" min="1990" max="2100"></label>
        <label>Teslim tarihi<input name="delivered_at" type="date"></label>
        <label>Garanti bitişi<input name="warranty_until" type="date"></label>
        <label>Not<input name="notes" maxlength="500"></label>
        <button class="panel-btn panel-btn--primary" type="submit">Kaydet</button>
    </form>
</div>

<div class="panel-box">
    <h2>Kayıtlı araçlar (<?= count($vehicles) ?>)</h2>
    <?php if ($vehicles === []): ?>
        <p class="panel-empty">Henüz araç kaydı yok.</p>
    <?php else: ?>
        <table class="panel-table">
            <thead><tr><th>QR</th><th>Şasi</th><th>Model</th><th>Yıl</th><th>Teslim</th><th>Garanti</th><th>Kimlik kartı</th></tr></thead>
            <tbody>
            <?php foreach ($vehicles as $vehicle): ?>
                <tr>
                    <td><img src="<?= $vehicle['qr'] ?>" alt="QR" width="72" height="72" style="background:#fff;border-radius:6px"></td>
                    <td><code><?= esc($vehicle['chassis_no']) ?></code></td>
                    <td><?= esc($vehicle['model']) ?></td>
                    <td><?= esc((string) ($vehicle['year'] ?? '—')) ?></td>
                    <td><?= esc($vehicle['delivered_at'] ?? '—') ?></td>
                    <td><?= esc($vehicle['warranty_until'] ?? '—') ?></td>
                    <td><a href="<?= esc($vehicle['card_url']) ?>" target="_blank" rel="noopener"><?= esc($vehicle['card_url']) ?></a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<style>
    .panel-vform { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0.8rem; align-items: end; }
    .panel-vform label { display: flex; flex-direction: column; gap: 0.3rem; font-size: 0.82rem; color: #7f98be; }
    .panel-vform input { min-height: 44px; padding: 0.45rem 0.7rem; border: 1px solid rgba(127,152,190,.35); border-radius: 9px; background: #0b1322; color: #eef4fc; font: inherit; }
</style>

<?php $this->endSection() ?>
