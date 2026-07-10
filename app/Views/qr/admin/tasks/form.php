<?= $this->extend('qr/layout/app') ?>
<?= $this->section('content') ?>
<?php $isEdit = $task !== null; ?>
<h1><?= $isEdit ? 'Görevi düzenle' : 'Görev ata' ?></h1>
<p class="back-link"><a href="<?= qr_url('admin/tasks') ?>">&larr; Görevler</a></p>
<div class="card pad-lg" style="max-width:600px">
    <?= view('qr/admin/tasks/_form', ['task' => $task, 'employees' => $employees, 'prefillUser' => $prefillUser ?? 0]) ?>
</div>
<?= $this->endSection() ?>
