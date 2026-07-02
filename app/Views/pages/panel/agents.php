<?php $this->extend('layouts/panel') ?>

<?php $this->section('title') ?>Ajanlar<?php $this->endSection() ?>

<?php $this->section('content') ?>

<h1 class="panel-h1">Ajan Ordusu</h1>

<div class="panel-box">
    <h2>Acil fren (kill-switch)</h2>
    <p class="panel-empty">Durdurulan ajan, cron tetiklese bile hiçbir iş yapmadan çıkar. Bayrak kalkınca kaldığı yerden devam eder.</p>
    <form class="panel-inline-form" method="post" action="/panel/agents/toggle">
        <?= csrf_field() ?>
        <input type="hidden" name="agent" value="all">
        <button class="panel-btn <?= $killedAll ? 'panel-btn--primary' : 'panel-btn--danger' ?>" type="submit">
            <?= $killedAll ? 'TÜM AJANLARI SERBEST BIRAK' : 'TÜM AJANLARI DURDUR' ?>
        </button>
    </form>
</div>

<?php foreach ($agents as $name => $info): ?>
    <div class="panel-box">
        <h2>
            agents:<?= esc($name) ?>
            <?php if ($killedAll || $info['killed']): ?>
                <span class="panel-badge panel-badge--no">DURDURULDU</span>
            <?php else: ?>
                <span class="panel-badge panel-badge--ok">AKTİF</span>
            <?php endif; ?>
        </h2>

        <form class="panel-inline-form" method="post" action="/panel/agents/toggle">
            <?= csrf_field() ?>
            <input type="hidden" name="agent" value="<?= esc($name, 'attr') ?>">
            <button class="panel-btn <?= $info['killed'] ? 'panel-btn--primary' : 'panel-btn--danger' ?>" type="submit">
                <?= $info['killed'] ? 'Serbest bırak' : 'Durdur' ?>
            </button>
        </form>

        <?php if ($info['runs'] !== []): ?>
            <table class="panel-table" style="margin-top:0.9rem">
                <thead><tr><th>Zaman</th><th>Durum</th><th>Süre</th><th>Rapor</th></tr></thead>
                <tbody>
                <?php foreach ($info['runs'] as $run): ?>
                    <tr>
                        <td><?= esc(substr((string) ($run['time'] ?? ''), 0, 16)) ?></td>
                        <td>
                            <span class="panel-badge <?= ($run['ok'] ?? false) ? 'panel-badge--ok' : 'panel-badge--no' ?>">
                                <?= ($run['ok'] ?? false) ? 'OK' : 'HATA' ?>
                            </span>
                        </td>
                        <td><?= esc((string) ($run['ms'] ?? '?')) ?> ms</td>
                        <td><?= esc(mb_substr(json_encode($run['report'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 0, 140)) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="panel-empty" style="margin-top:0.9rem">Bu ay koşu kaydı yok.</p>
        <?php endif; ?>
    </div>
<?php endforeach; ?>

<?php $this->endSection() ?>
