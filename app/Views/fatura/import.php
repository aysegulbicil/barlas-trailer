<?php

/**
 * /fatura/import — one-shot import of the cleaned legacy dataset
 * (apps/fatura/data/faturalar.json).
 * Data: $err, $sonuc, $veriVar, $mevcut, $onAdet, $onFirma, $onToplam,
 *       $onOdenen, $onKalan
 */
?>
<?= $this->extend('fatura/layout') ?>

<?= $this->section('content') ?>

<div class="page-head">
    <div>
        <h1>Verileri İçe Aktar</h1>
        <div class="sub">“fatura takip çizelgesi.xlsx” dosyasından temizlenmiş kayıtlar</div>
    </div>
</div>

<?php if ($err): ?><div class="alert err"><?= esc($err) ?></div><?php endif; ?>

<?php if ($sonuc): ?>
    <div class="alert ok">
        İçe aktarma tamamlandı: <b><?= $sonuc['fatura'] ?></b> fatura, <b><?= $sonuc['firma'] ?></b> firma eklendi.
        <?= $sonuc['temizlendi'] ? ' (önceki kayıtlar temizlendi)' : '' ?>
    </div>
    <div class="card card-pad">
        <p>Hazır. Artık özet ekranından kontrol edebilirsin.</p>
        <div style="display:flex;gap:10px">
            <a href="<?= route_to('fatura') ?>" class="btn btn-primary">Genel Özete Git</a>
            <a href="<?= route_to('fatura.invoices') ?>" class="btn">Faturaları Gör</a>
        </div>
        <p class="hint" style="margin-top:16px">Güvenlik için bu içe aktarmayı tekrar çalıştırmana gerek yok.</p>
    </div>
<?php elseif (! $veriVar): ?>
    <div class="alert err">Veri dosyası (data/faturalar.json) bulunamadı.</div>
<?php else: ?>
    <div class="cards">
        <div class="stat is-total">
            <div class="label">Aktarılacak Fatura</div>
            <div class="value"><?= $onAdet ?></div>
            <div class="meta"><?= $onFirma ?> firma</div>
        </div>
        <div class="stat is-paid">
            <div class="label">Ödenen (toplam)</div>
            <div class="value" style="font-size:20px"><?= fatura_para($onOdenen) ?></div>
        </div>
        <div class="stat is-debt">
            <div class="label">Kalan Borç (toplam)</div>
            <div class="value" style="font-size:20px"><?= fatura_para($onKalan) ?></div>
        </div>
    </div>

    <div class="card card-pad">
        <?php if ($mevcut > 0): ?>
            <div class="alert err" style="margin-bottom:18px">
                Veritabanında şu an <b><?= $mevcut ?></b> fatura var. Aşağıdaki seçenek işaretliyse bunlar silinip yerine bu veriler yazılır.
            </div>
        <?php endif; ?>
        <form method="post">
            <?= csrf_field() ?>
            <label style="display:flex;align-items:center;gap:10px;font-weight:500;margin-bottom:18px">
                <input type="checkbox" name="temizle" value="1" checked style="width:auto">
                İçe aktarmadan önce mevcut tüm kayıtları temizle (sıfırdan başlat — önerilir)
            </label>
            <input type="hidden" name="onayla" value="1">
            <div style="display:flex;gap:10px">
                <button class="btn btn-primary" type="submit"><?= $onAdet ?> Faturayı İçe Aktar</button>
                <a href="<?= route_to('fatura') ?>" class="btn">Vazgeç</a>
            </div>
        </form>
    </div>

    <p class="hint" style="margin-top:16px">
        Firma adları temizlendi: yazım/boşluk/büyük-küçük harf farkları ve bazı kısaltmalar birleştirildi
        (ör. PAKDEMİR TİCARET → PAKDEMİR, İhsan Koçak Mak.San. → İHSAN KOÇAK). Tutarlar kaynak dosyayla birebir aynıdır.
    </p>
<?php endif; ?>

<?= $this->endSection() ?>
