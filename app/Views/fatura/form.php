<?php

/**
 * /fatura/form — create / edit an invoice (?id= edits).
 * Data: $id, $f (record or defaults, POST-merged on error), $err, $firmaList
 * The form has no action attribute so it posts back to the current URL
 * (keeps ?id= while editing) — mirrors the standalone app.
 */
?>
<?= $this->extend('fatura/layout') ?>

<?= $this->section('content') ?>

<div class="page-head">
  <div>
    <h1><?= $id ? 'Faturayı Düzenle' : 'Yeni Fatura' ?></h1>
    <div class="sub">KDV ve toplam otomatik hesaplanır</div>
  </div>
  <a href="<?= route_to('fatura.invoices') ?>" class="btn">← Listeye dön</a>
</div>

<?php if ($err): ?><div class="alert err"><?= esc($err) ?></div><?php endif; ?>

<form method="post" class="card card-pad">
  <?= csrf_field() ?>
  <div class="form-grid">
    <div class="field" style="grid-column:1/-1">
      <label>Firma</label>
      <select name="firma_id" id="firma_id">
        <option value="">— Firma seç —</option>
        <?php foreach ($firmaList as $fl): ?>
          <option value="<?= (int) $fl['id'] ?>" <?= (int) $f['firma_id'] === (int) $fl['id'] ? 'selected' : '' ?>><?= esc($fl['ad']) ?></option>
        <?php endforeach; ?>
      </select>
      <input type="text" name="yeni_firma" placeholder="…veya yeni firma adı yaz (listede yoksa)" style="margin-top:8px">
    </div>

    <div class="field">
      <label>Fatura Tarihi</label>
      <input type="date" name="fatura_tarihi" value="<?= esc((string) $f['fatura_tarihi']) ?>">
    </div>
    <div class="field">
      <label>Fatura No</label>
      <input type="text" name="fatura_no" value="<?= esc((string) $f['fatura_no']) ?>">
    </div>
    <div class="field">
      <label>Tür</label>
      <select name="tur">
        <option value="Gelen" <?= $f['tur'] === 'Gelen' ? 'selected' : '' ?>>Gelen</option>
        <option value="Giden" <?= $f['tur'] === 'Giden' ? 'selected' : '' ?>>Giden</option>
      </select>
    </div>

    <div class="field">
      <label>Tutar (KDV hariç)</label>
      <input type="number" step="0.01" name="tutar" id="tutar" value="<?= esc((string) $f['tutar']) ?>" required>
    </div>
    <div class="field">
      <label>KDV Oranı (%)</label>
      <input type="number" step="0.01" name="kdv_orani" id="kdv_orani" value="<?= esc((string) $f['kdv_orani']) ?>">
    </div>
    <div class="field">
      <label>Genel Toplam</label>
      <input type="text" id="toplam_gosterim" readonly style="background:#f8f9fd;font-weight:700">
    </div>

    <div class="field">
      <label>Durum</label>
      <select name="durum">
        <option value="Ödenmedi" <?= $f['durum'] === 'Ödenmedi' ? 'selected' : '' ?>>Ödenmedi</option>
        <option value="Ödendi"   <?= $f['durum'] === 'Ödendi' ? 'selected' : '' ?>>Ödendi</option>
      </select>
    </div>
    <div class="field">
      <label>Ödeme Şekli</label>
      <input type="text" name="odeme_sekli" value="<?= esc((string) $f['odeme_sekli']) ?>" placeholder="Havale, nakit, çek…">
    </div>
    <div class="field">
      <label>Ödeme Tarihi</label>
      <input type="date" name="odeme_tarihi" value="<?= esc((string) $f['odeme_tarihi']) ?>">
    </div>

    <div class="field" style="grid-column:1/-1">
      <label>Açıklama</label>
      <textarea name="aciklama" rows="2"><?= esc((string) $f['aciklama']) ?></textarea>
    </div>
  </div>

  <div style="display:flex;gap:10px;margin-top:20px">
    <button class="btn btn-primary" type="submit"><?= $id ? 'Değişiklikleri Kaydet' : 'Faturayı Kaydet' ?></button>
    <a href="<?= route_to('fatura.invoices') ?>" class="btn">Vazgeç</a>
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

<?= $this->endSection() ?>
