<?= $this->extend('qr/layout/app') ?>
<?= $this->section('content') ?>
<?php $isEdit = $position !== null; ?>
<h1><?= $isEdit ? 'Pozisyonu düzenle' : 'Yeni pozisyon' ?></h1>
<p class="back-link"><a href="<?= qr_url('admin/positions') ?>">&larr; Pozisyonlar</a></p>
<div class="card pad-lg" style="max-width:520px">
    <?= view('qr/admin/positions/_form', ['position' => $position, 'departments' => $departments]) ?>
</div>
<?= $this->endSection() ?>
