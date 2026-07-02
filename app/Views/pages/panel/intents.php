<?php $this->extend('layouts/panel') ?>

<?php $this->section('title') ?>Teklif Niyetleri<?php $this->endSection() ?>

<?php $this->section('content') ?>

<h1 class="panel-h1">Teklif Niyetleri — WhatsApp akışı (bu ay)</h1>

<div class="panel-box">
    <?php if ($rows === []): ?>
        <p class="panel-empty">Bu ay henüz teklif niyeti yok.</p>
    <?php else: ?>
        <table class="panel-table">
            <thead><tr><th>Zaman</th><th>Dil</th><th>Kategori</th><th>Ürün</th><th>Varyant</th></tr></thead>
            <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= esc(substr((string) ($row['time'] ?? ''), 0, 16)) ?></td>
                    <td><?= esc($row['locale'] ?? '') ?></td>
                    <td><?= esc($row['category'] ?? '') ?></td>
                    <td><?= esc($row['product'] ?? ($row['name'] ?? '')) ?></td>
                    <td><?= esc($row['variant'] ?? '—') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php $this->endSection() ?>
