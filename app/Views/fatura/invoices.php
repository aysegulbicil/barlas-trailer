<?php

/**
 * /fatura/faturalar — invoice list with filters, delete and pagination.
 * Data: $flash, $q, $firma, $durum, $tur, $total, $sum, $rows, $firmaList,
 *       $page, $pages, $baseParams
 * POST forms deliberately have no action attribute (submit back to the
 * current URL, query string included) — mirrors the standalone app.
 */
?>
<?= $this->extend('fatura/layout') ?>

<?= $this->section('content') ?>

<div class="page-head">
  <div>
    <h1>Faturalar</h1>
    <div class="sub"><?= $total ?> kayıt · Toplam <?= fatura_para($sum['t']) ?> · Kalan <span style="color:#e11d48"><?= fatura_para($sum['k']) ?></span></div>
  </div>
  <a href="<?= route_to('fatura.form') ?>" class="btn btn-primary">
    <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
    Yeni Fatura
  </a>
</div>

<?php if ($flash): ?><div class="alert ok"><?= esc($flash) ?></div><?php endif; ?>

<form method="get" class="toolbar">
  <input type="text" name="q" value="<?= esc($q) ?>" placeholder="Firma, fatura no veya açıklama ara…">
  <select name="firma">
    <option value="">Tüm firmalar</option>
    <?php foreach ($firmaList as $fl): ?>
      <option value="<?= (int) $fl['id'] ?>" <?= $firma === (int) $fl['id'] ? 'selected' : '' ?>><?= esc($fl['ad']) ?></option>
    <?php endforeach; ?>
  </select>
  <select name="durum">
    <option value="">Tüm durumlar</option>
    <option value="Ödenmedi" <?= $durum === 'Ödenmedi' ? 'selected' : '' ?>>Ödenmedi</option>
    <option value="Ödendi"   <?= $durum === 'Ödendi' ? 'selected' : '' ?>>Ödendi</option>
  </select>
  <select name="tur">
    <option value="">Tüm türler</option>
    <option value="Gelen" <?= $tur === 'Gelen' ? 'selected' : '' ?>>Gelen</option>
    <option value="Giden" <?= $tur === 'Giden' ? 'selected' : '' ?>>Giden</option>
  </select>
  <button class="btn btn-primary" type="submit">Filtrele</button>
  <a href="<?= route_to('fatura.invoices') ?>" class="btn">Temizle</a>
</form>

<div class="card">
  <div class="table-wrap">
    <table class="grid">
      <thead>
        <tr>
          <th>Firma</th>
          <th>Fatura No</th>
          <th class="mid">Tarih</th>
          <th class="mid">Tür</th>
          <th class="num">Tutar</th>
          <th class="num">KDV</th>
          <th class="num">Toplam</th>
          <th class="mid">Durum</th>
          <th class="mid">İşlem</th>
        </tr>
      </thead>
      <tbody>
        <?php if (! $rows): ?>
          <tr><td colspan="9" class="empty" style="padding:36px">Kayıt bulunamadı.</td></tr>
        <?php else: foreach ($rows as $r): ?>
          <tr>
            <td style="font-weight:600;white-space:normal;max-width:240px"><?= esc($r['firma_ad']) ?></td>
            <td><?= $r['fatura_no'] ? esc($r['fatura_no']) : '<span style="color:#cbd5e1">—</span>' ?></td>
            <td class="mid"><?= fatura_tarih($r['fatura_tarihi']) ?></td>
            <td class="mid"><span class="badge tur"><?= esc($r['tur']) ?></span></td>
            <td class="num tabular"><?= fatura_para($r['tutar']) ?></td>
            <td class="num tabular" style="color:#94a3b8"><?= fatura_para($r['kdv_tutari']) ?></td>
            <td class="num tabular" style="font-weight:600"><?= fatura_para($r['toplam']) ?></td>
            <td class="mid">
              <span class="badge <?= $r['durum'] === 'Ödendi' ? 'paid' : 'unpaid' ?>"><?= esc($r['durum']) ?></span>
            </td>
            <td class="mid" style="white-space:nowrap">
              <a href="<?= route_to('fatura.form') ?>?id=<?= (int) $r['id'] ?>" class="btn btn-sm" title="Düzenle">✎</a>
              <form method="post" style="display:inline" onsubmit="return confirm('Bu fatura silinsin mi?')">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="sil">
                <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                <button class="btn btn-sm btn-danger" type="submit" title="Sil">🗑</button>
              </form>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?= fatura_pagination($page, $pages, $baseParams) ?>

<?= $this->endSection() ?>
