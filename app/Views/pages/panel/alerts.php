<?php $this->extend('layouts/panel') ?>

<?php $this->section('title') ?>Alarmlar<?php $this->endSection() ?>

<?php $this->section('content') ?>

<h1 class="panel-h1">Dikkat Kuyruğu</h1>

<div class="panel-box">
    <?php if ($alerts === []): ?>
        <p class="panel-empty">Bekleyen alarm yok — sistem sakin. (Rapora giren alarmlar sabah brifinginde arşivlenir.)</p>
    <?php else: ?>
        <table class="panel-table">
            <thead><tr><th>Zaman</th><th>Ajan</th><th>Tür</th><th>Mesaj</th></tr></thead>
            <tbody>
            <?php foreach ($alerts as $alert): ?>
                <tr>
                    <td><?= esc($alert['time'] ?? '') ?></td>
                    <td><?= esc($alert['agent'] ?? '') ?></td>
                    <td><?= esc($alert['type'] ?? '') ?></td>
                    <td><?= esc($alert['message'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <form class="panel-inline-form" method="post" action="/panel/alerts/archive" style="display:block;margin-top:1rem">
            <?= csrf_field() ?>
            <button class="panel-btn" type="submit">Tümünü okundu say (arşivle)</button>
        </form>
    <?php endif; ?>
</div>

<?php $this->endSection() ?>
