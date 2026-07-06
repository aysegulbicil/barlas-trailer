<?php
require_once __DIR__ . '/db.php';
$pdo = db();

$id = (int) ($_GET['id'] ?? 0);
$err = '';

// Varsayılan kayıt
$f = [
    'firma_id' => 0, 'fatura_tarihi' => '', 'fatura_no' => '', 'tur' => 'Gelen',
    'tutar' => '', 'kdv_orani' => '20', 'odeme_sekli' => '', 'odeme_tarihi' => '',
    'durum' => 'Ödenmedi', 'aciklama' => '',
];

if ($id && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $st = $pdo->prepare('SELECT * FROM faturalar WHERE id = ?');
    $st->execute([$id]);
    $row = $st->fetch();
    if (!$row) { header('Location: faturalar.php'); exit; }
    $f = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $f = array_merge($f, $_POST);
    $yeniFirma = trim((string) ($_POST['yeni_firma'] ?? ''));
    $firmaId   = (int) ($_POST['firma_id'] ?? 0);

    // Yeni firma girildiyse oluştur / bul
    if ($yeniFirma !== '') {
        $sel = $pdo->prepare('SELECT id FROM firmalar WHERE ad = ?');
        $sel->execute([$yeniFirma]);
        $firmaId = (int) $sel->fetchColumn();
        if (!$firmaId) {
            $pdo->prepare('INSERT INTO firmalar (ad) VALUES (?)')->execute([$yeniFirma]);
            $firmaId = (int) $pdo->lastInsertId();
        }
    }

    $tutar = (float) str_replace(',', '.', (string) ($_POST['tutar'] ?? 0));
    $kdvO  = (float) str_replace(',', '.', (string) ($_POST['kdv_orani'] ?? 0));
    $kdvT  = round($tutar * $kdvO / 100, 2);
    $toplam = round($tutar + $kdvT, 2);

    if (!$firmaId)            $err = 'Lütfen bir firma seç ya da yeni firma adı gir.';
    elseif ($tutar <= 0)      $err = 'Tutar 0\'dan büyük olmalı.';

    if (!$err) {
        $params = [
            $firmaId,
            ($_POST['fatura_tarihi'] ?? '') ?: null,
            trim((string)($_POST['fatura_no'] ?? '')) ?: null,
            in_array($_POST['tur'] ?? '', ['Gelen','Giden'], true) ? $_POST['tur'] : 'Gelen',
            $tutar, $kdvO, $kdvT, $toplam,
            trim((string)($_POST['odeme_sekli'] ?? '')) ?: null,
            ($_POST['odeme_tarihi'] ?? '') ?: null,
            in_array($_POST['durum'] ?? '', ['Ödendi','Ödenmedi'], true) ? $_POST['durum'] : 'Ödenmedi',
            trim((string)($_POST['aciklama'] ?? '')) ?: null,
        ];
        if ($id) {
            $params[] = $id;
            $pdo->prepare(
                "UPDATE faturalar SET firma_id=?,fatura_tarihi=?,fatura_no=?,tur=?,tutar=?,
                 kdv_orani=?,kdv_tutari=?,toplam=?,odeme_sekli=?,odeme_tarihi=?,durum=?,aciklama=?
                 WHERE id=?"
            )->execute($params);
        } else {
            $pdo->prepare(
                "INSERT INTO faturalar
                 (firma_id,fatura_tarihi,fatura_no,tur,tutar,kdv_orani,kdv_tutari,toplam,odeme_sekli,odeme_tarihi,durum,aciklama)
                 VALUES (?,?,?,?,?,?,?,?,?,?,?,?)"
            )->execute($params);
        }
        header('Location: faturalar.php');
        exit;
    }
}

$firmaList = $pdo->query('SELECT id, ad FROM firmalar ORDER BY ad')->fetchAll();

$title = $id ? 'Faturayı Düzenle' : 'Yeni Fatura';
$active = 'faturalar.php';
require __DIR__ . '/partials/header.php';
?>

<div class="page-head">
  <div>
    <h1><?= $id ? 'Faturayı Düzenle' : 'Yeni Fatura' ?></h1>
    <div class="sub">KDV ve toplam otomatik hesaplanır</div>
  </div>
  <a href="faturalar.php" class="btn">← Listeye dön</a>
</div>

<?php if ($err): ?><div class="alert err"><?= e($err) ?></div><?php endif; ?>

<form method="post" class="card card-pad">
  <div class="form-grid">
    <div class="field" style="grid-column:1/-1">
      <label>Firma</label>
      <select name="firma_id" id="firma_id">
        <option value="">— Firma seç —</option>
        <?php foreach ($firmaList as $fl): ?>
          <option value="<?= (int)$fl['id'] ?>" <?= (int)$f['firma_id'] === (int)$fl['id'] ? 'selected' : '' ?>><?= e($fl['ad']) ?></option>
        <?php endforeach; ?>
      </select>
      <input type="text" name="yeni_firma" placeholder="…veya yeni firma adı yaz (listede yoksa)" style="margin-top:8px">
    </div>

    <div class="field">
      <label>Fatura Tarihi</label>
      <input type="date" name="fatura_tarihi" value="<?= e($f['fatura_tarihi']) ?>">
    </div>
    <div class="field">
      <label>Fatura No</label>
      <input type="text" name="fatura_no" value="<?= e($f['fatura_no']) ?>">
    </div>
    <div class="field">
      <label>Tür</label>
      <select name="tur">
        <option value="Gelen" <?= $f['tur']==='Gelen'?'selected':'' ?>>Gelen</option>
        <option value="Giden" <?= $f['tur']==='Giden'?'selected':'' ?>>Giden</option>
      </select>
    </div>

    <div class="field">
      <label>Tutar (KDV hariç)</label>
      <input type="number" step="0.01" name="tutar" id="tutar" value="<?= e($f['tutar']) ?>" required>
    </div>
    <div class="field">
      <label>KDV Oranı (%)</label>
      <input type="number" step="0.01" name="kdv_orani" id="kdv_orani" value="<?= e($f['kdv_orani']) ?>">
    </div>
    <div class="field">
      <label>Genel Toplam</label>
      <input type="text" id="toplam_gosterim" readonly style="background:#f8f9fd;font-weight:700">
    </div>

    <div class="field">
      <label>Durum</label>
      <select name="durum">
        <option value="Ödenmedi" <?= $f['durum']==='Ödenmedi'?'selected':'' ?>>Ödenmedi</option>
        <option value="Ödendi"   <?= $f['durum']==='Ödendi'?'selected':'' ?>>Ödendi</option>
      </select>
    </div>
    <div class="field">
      <label>Ödeme Şekli</label>
      <input type="text" name="odeme_sekli" value="<?= e($f['odeme_sekli']) ?>" placeholder="Havale, nakit, çek…">
    </div>
    <div class="field">
      <label>Ödeme Tarihi</label>
      <input type="date" name="odeme_tarihi" value="<?= e($f['odeme_tarihi']) ?>">
    </div>

    <div class="field" style="grid-column:1/-1">
      <label>Açıklama</label>
      <textarea name="aciklama" rows="2"><?= e($f['aciklama']) ?></textarea>
    </div>
  </div>

  <div style="display:flex;gap:10px;margin-top:20px">
    <button class="btn btn-primary" type="submit"><?= $id ? 'Değişiklikleri Kaydet' : 'Faturayı Kaydet' ?></button>
    <a href="faturalar.php" class="btn">Vazgeç</a>
  </div>
</form>

<script>
function hesapla(){
  var t = parseFloat((document.getElementById('tutar').value||'0').replace(',','.'))||0;
  var o = parseFloat((document.getElementById('kdv_orani').value||'0').replace(',','.'))||0;
  var toplam = t + (t*o/100);
  document.getElementById('toplam_gosterim').value =
    toplam.toLocaleString('tr-TR',{minimumFractionDigits:2,maximumFractionDigits:2}) + ' ₺';
}
document.getElementById('tutar').addEventListener('input',hesapla);
document.getElementById('kdv_orani').addEventListener('input',hesapla);
hesapla();
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>
