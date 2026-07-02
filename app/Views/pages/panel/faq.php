<?php $this->extend('layouts/panel') ?>

<?php $this->section('title') ?>SSS Adayları<?php $this->endSection() ?>

<?php $this->section('content') ?>

<h1 class="panel-h1">SSS Aday Soruları</h1>

<div class="panel-box">
    <?php if ($data === null): ?>
        <p class="panel-empty">Henüz derleme yok. Konteynerde <code>php spark agents:faq-candidates</code> çalıştırın.</p>
    <?php else: ?>
        <p class="panel-empty">
            Güncelleme: <?= esc($data['updated'] ?? '?') ?> ·
            Toplam soru: <?= esc((string) ($data['total_questions'] ?? 0)) ?> ·
            Benzersiz: <?= esc((string) ($data['unique'] ?? 0)) ?>
        </p>
        <table class="panel-table">
            <thead><tr><th>#</th><th>Soru</th><th>Dil</th><th>İlk görülme</th></tr></thead>
            <tbody>
            <?php foreach (($data['candidates'] ?? []) as $candidate): ?>
                <tr>
                    <td><strong><?= esc((string) ($candidate['count'] ?? 1)) ?>×</strong></td>
                    <td><?= esc($candidate['q'] ?? '') ?></td>
                    <td><?= esc($candidate['locale'] ?? '') ?></td>
                    <td><?= esc(substr((string) ($candidate['first_seen'] ?? ''), 0, 10)) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <p class="panel-empty" style="margin-top:0.9rem">
            Onayladığın soruyu <code>app/Language/{dil}/Faq.php</code> dosyalarına (5 dilde birden) ekleyerek yayınla.
        </p>
    <?php endif; ?>
</div>

<?php $this->endSection() ?>
