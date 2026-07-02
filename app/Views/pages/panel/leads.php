<?php $this->extend('layouts/panel') ?>

<?php $this->section('title') ?>Lead'ler<?php $this->endSection() ?>

<?php $this->section('content') ?>

<h1 class="panel-h1">İletişim Başvuruları (bu ay, en yeni önce)</h1>

<div class="panel-box">
    <?php if ($rows === []): ?>
        <p class="panel-empty">Bu ay henüz başvuru yok.</p>
    <?php else: ?>
        <table class="panel-table">
            <thead><tr><th>Zaman</th><th>Ad</th><th>E-posta / Telefon</th><th>Şirket</th><th>Konu</th><th>Mesaj</th></tr></thead>
            <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= esc(substr((string) ($row['time'] ?? ''), 0, 16)) ?></td>
                    <td><?= esc($row['name'] ?? '') ?></td>
                    <td>
                        <a href="mailto:<?= esc($row['email'] ?? '', 'attr') ?>"><?= esc($row['email'] ?? '') ?></a>
                        <?php if (! empty($row['phone'])): ?><br><?= esc($row['phone']) ?><?php endif; ?>
                    </td>
                    <td><?= esc($row['company'] ?? '') ?></td>
                    <td><?= esc($row['subject'] ?? '') ?></td>
                    <td><?= esc(mb_substr((string) ($row['message'] ?? ''), 0, 160)) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php $this->endSection() ?>
