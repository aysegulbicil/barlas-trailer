<?= $this->extend('qr/layout/bare') ?>
<?= $this->section('content') ?>
<?php $loggedIn = (bool) session()->get('user_id'); ?>
<div class="card pad-lg center">
    <h1>Okutma sorunu</h1>
    <p class="muted"><?= esc($message) ?></p>
    <?php if ($loggedIn): ?>
        <a class="btn btn-outline" href="<?= qr_url('dashboard') ?>">Panele dön</a>
    <?php else: ?>
        <a class="btn btn-outline" href="<?= qr_url('login') ?>">Giriş yap</a>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
