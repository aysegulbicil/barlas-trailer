<?php

/**
 * /fatura/firmalar — company list with add / delete and pagination.
 * Data: $flash, $err, $q, $total, $rows, $page, $pages
 * POST forms have no action attribute (submit back to the current URL) —
 * mirrors the standalone app.
 */
?>
<?= $this->extend('fatura/layout') ?>

<?= $this->section('content') ?>

<div class="page-head">
  <div>
    <h1>Firmalar</h1>
    <div class="sub"><?= $total ?> firma kayıtlı</div>
  </div>
</div>

<?php if ($flash): ?><div class="alert ok"><?= esc($flash) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert err"><?= esc($err) ?></div><?php endif; ?>

<div class="card card-pad" style="margin-bottom:18px">
  <form method="post" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="ekle">
    <div class="field" style="flex:1;min-width:240px">
      <label>Yeni firma adı</label>
      <input type="text" name="ad" placeholder="Örn. PAKDEMİR" required>
    </div>
    <button class="btn btn-primary" type="submit">
      <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
      Firma Ekle
    </button>
  </form>
</div>

<form method="get" class="toolbar">
  <input type="text" name="q" value="<?= esc($q) ?>" placeholder="Firma ara…">
  <button class="btn btn-primary" type="submit">Ara</button>
  <a href="<?= route_to('fatura.companies') ?>" class="btn">Temizle</a>
</form>

<div class="card">
  <div class="table-wrap">
    <table class="grid">
      <thead>
        <tr>
          <th>Firma</th>
          <th class="mid">Fatura</th>
          <th class="num">Toplam</th>
          <th class="num">Kalan Borç</th>
          <th class="mid">İşlem</th>
        </tr>
      </thead>
      <tbody>
        <?php if (! $rows): ?>
          <tr><td colspan="5" class="empty" style="padding:36px">Firma bulunamadı.</td></tr>
        <?php else: foreach ($rows as $r): ?>
          <tr>
            <td style="font-weight:600;white-space:normal"><?= esc($r['ad']) ?></td>
            <td class="mid tabular"><?= (int) $r['adet'] ?></td>
            <td class="num tabular"><?= fatura_para($r['toplam']) ?></td>
            <td class="num tabular" style="color:<?= $r['kalan'] > 0 ? '#e11d48' : '#94a3b8' ?>"><?= fatura_para($r['kalan']) ?></td>
            <td class="mid" style="white-space:nowrap">
              <a href="<?= route_to('fatura.invoices') ?>?firma=<?= (int) $r['id'] ?>" class="btn btn-sm">Faturalar</a>
              <form method="post" style="display:inline" onsubmit="return confirm('“<?= esc($r['ad']) ?>” ve TÜM faturaları silinecek. Emin misin?')">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="sil">
                <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                <button class="btn btn-sm btn-danger" type="submit">🗑</button>
              </form>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?= fatura_pagination($page, $pages, $q !== '' ? ['q' => $q] : []) ?>

<?= $this->endSection() ?>
