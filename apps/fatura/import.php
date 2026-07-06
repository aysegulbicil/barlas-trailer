<?php
require_once __DIR__ . '/db.php';

$dataFile = __DIR__ . '/data/faturalar.json';
$kayitlar = is_file($dataFile) ? json_decode(file_get_contents($dataFile), true) : null;

$sonuc = null;
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['onayla'])) {
    if (!is_array($kayitlar)) {
        $err = 'data/faturalar.json bulunamadı veya bozuk.';
    } else {
        $temizle = isset($_POST['temizle']);
        try {
            $pdo = db();
            $pdo->beginTransaction();

            if ($temizle) {
                $pdo->exec('DELETE FROM faturalar');
                $pdo->exec('DELETE FROM firmalar');
            }

            // Firmalar (tekilleştir)
            $selF = $pdo->prepare('SELECT id FROM firmalar WHERE ad = ?');
            $insF = $pdo->prepare('INSERT INTO firmalar (ad) VALUES (?)');
            $firmaMap = [];
            $yeniFirma = 0;
            foreach ($kayitlar as $r) {
                $ad = $r['firma'];
                if (isset($firmaMap[$ad])) continue;
                $selF->execute([$ad]);
                $fid = $selF->fetchColumn();
                if (!$fid) { $insF->execute([$ad]); $fid = $pdo->lastInsertId(); $yeniFirma++; }
                $firmaMap[$ad] = $fid;
            }

            // Faturalar
            $ins = $pdo->prepare("INSERT INTO faturalar
                (firma_id, fatura_tarihi, fatura_no, tur, tutar, kdv_orani, kdv_tutari, toplam, odeme_sekli, odeme_tarihi, durum, aciklama)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
            $n = 0;
            foreach ($kayitlar as $r) {
                $ins->execute([
                    $firmaMap[$r['firma']],
                    $r['fatura_tarihi'] ?: null,
                    $r['fatura_no'] ?: null,
                    in_array($r['tur'], ['Gelen','Giden'], true) ? $r['tur'] : 'Gelen',
                    $r['tutar'], $r['kdv_orani'], $r['kdv_tutari'], $r['toplam'],
                    $r['odeme_sekli'] ?: null,
                    $r['odeme_tarihi'] ?: null,
                    in_array($r['durum'], ['Ödendi','Ödenmedi'], true) ? $r['durum'] : 'Ödenmedi',
                    $r['aciklama'] ?: null,
                ]);
                $n++;
            }
            $pdo->commit();
            $sonuc = ['fatura' => $n, 'firma' => count($firmaMap), 'yeni_firma' => $yeniFirma, 'temizlendi' => $temizle];
        } catch (Exception $ex) {
            if (db()->inTransaction()) db()->rollBack();
            $err = 'İçe aktarma hatası: ' . $ex->getMessage();
        }
    }
}

// Önizleme istatistikleri
$mevcut = (int) db()->query('SELECT COUNT(*) FROM faturalar')->fetchColumn();
$onAdet = is_array($kayitlar) ? count($kayitlar) : 0;
$onFirma = $onToplam = $onOdenen = $onKalan = 0;
if (is_array($kayitlar)) {
    $f = [];
    foreach ($kayitlar as $r) {
        $f[$r['firma']] = 1;
        $onToplam += $r['toplam'];
        if ($r['durum'] === 'Ödendi') $onOdenen += $r['toplam']; else $onKalan += $r['toplam'];
    }
    $onFirma = count($f);
}

$title = 'Verileri İçe Aktar';
$active = '';
require __DIR__ . '/partials/header.php';
?>

<div class="page-head">
    <div>
        <h1>Verileri İçe Aktar</h1>
        <div class="sub">“fatura takip çizelgesi.xlsx” dosyasından temizlenmiş kayıtlar</div>
    </div>
</div>

<?php if ($err): ?><div class="alert err"><?= e($err) ?></div><?php endif; ?>

<?php if ($sonuc): ?>
    <div class="alert ok">
        İçe aktarma tamamlandı: <b><?= $sonuc['fatura'] ?></b> fatura, <b><?= $sonuc['firma'] ?></b> firma eklendi.
        <?= $sonuc['temizlendi'] ? ' (önceki kayıtlar temizlendi)' : '' ?>
    </div>
    <div class="card card-pad">
        <p>Hazır. Artık özet ekranından kontrol edebilirsin.</p>
        <div style="display:flex;gap:10px">
            <a href="index.php" class="btn btn-primary">Genel Özete Git</a>
            <a href="faturalar.php" class="btn">Faturaları Gör</a>
        </div>
        <p class="hint" style="margin-top:16px">Güvenlik için bu içe aktarmayı tekrar çalıştırmana gerek yok; istersen <code>import.php</code> dosyasını silebilirsin.</p>
    </div>
<?php elseif (!is_array($kayitlar)): ?>
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
            <div class="value" style="font-size:20px"><?= para($onOdenen) ?></div>
        </div>
        <div class="stat is-debt">
            <div class="label">Kalan Borç (toplam)</div>
            <div class="value" style="font-size:20px"><?= para($onKalan) ?></div>
        </div>
    </div>

    <div class="card card-pad">
        <?php if ($mevcut > 0): ?>
            <div class="alert err" style="margin-bottom:18px">
                Veritabanında şu an <b><?= $mevcut ?></b> fatura var. Aşağıdaki seçenek işaretliyse bunlar silinip yerine bu veriler yazılır.
            </div>
        <?php endif; ?>
        <form method="post">
            <label style="display:flex;align-items:center;gap:10px;font-weight:500;margin-bottom:18px">
                <input type="checkbox" name="temizle" value="1" checked style="width:auto">
                İçe aktarmadan önce mevcut tüm kayıtları temizle (sıfırdan başlat — önerilir)
            </label>
            <input type="hidden" name="onayla" value="1">
            <div style="display:flex;gap:10px">
                <button class="btn btn-primary" type="submit"><?= $onAdet ?> Faturayı İçe Aktar</button>
                <a href="index.php" class="btn">Vazgeç</a>
            </div>
        </form>
    </div>

    <p class="hint" style="margin-top:16px">
        Firma adları temizlendi: yazım/boşluk/büyük-küçük harf farkları ve bazı kısaltmalar birleştirildi
        (ör. PAKDEMİR TİCARET → PAKDEMİR, İhsan Koçak Mak.San. → İHSAN KOÇAK). Tutarlar kaynak dosyayla birebir aynıdır.
    </p>
<?php endif; ?>

<?php require __DIR__ . '/partials/footer.php'; ?>
