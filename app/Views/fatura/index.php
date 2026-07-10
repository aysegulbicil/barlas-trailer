<?php

/**
 * /fatura — overall summary dashboard.
 * Data: $adet, $toplam, $odenen, $kalan, $oran, $firmaAdet, $enBorclu, $maxBorc, $firmalar
 */
?>
<?= $this->extend('fatura/layout') ?>

<?= $this->section('content') ?>

<div class="page-head">
  <div>
    <h1>Genel Özet</h1>
    <div class="sub">Tüm firmalar ve faturalar için anlık durum</div>
  </div>
  <div style="display:flex;gap:10px">
    <a href="<?= route_to('fatura.invoices') ?>" class="btn">Faturalar</a>
    <a href="<?= route_to('fatura.export') ?>" class="btn btn-primary">
      <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16v16H4zM4 9h16M9 9v11"/></svg>
      Excel'e Aktar
    </a>
  </div>
</div>

<?php if ($adet === 0): ?>
  <div class="card card-pad empty">
    <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M7 3h10l3 3v15H4V6z"/><path d="M8 11h8M8 15h6"/></svg>
    <h2 style="margin:0 0 6px">Henüz kayıt yok</h2>
    <p style="margin:0 0 18px">Hazır verini içe aktararak başlayabilirsin (331 fatura / 70 firma).</p>
    <a href="<?= route_to('fatura.import') ?>" class="btn btn-primary">Verileri İçe Aktar</a>
  </div>
<?php else: ?>

  <div class="cards">
    <div class="stat is-total">
      <div class="label">Toplam Tutar</div>
      <div class="value tabular"><?= fatura_para($toplam) ?></div>
      <div class="meta"><?= $adet ?> fatura · <?= $firmaAdet ?> firma</div>
    </div>
    <div class="stat is-paid">
      <div class="label">Ödenen</div>
      <div class="value tabular" style="font-size:22px"><?= fatura_para($odenen) ?></div>
      <div class="meta">%<?= number_format($oran * 100, 1, ',', '.') ?> tahsil edildi</div>
    </div>
    <div class="stat is-debt">
      <div class="label">Kalan Borç</div>
      <div class="value tabular" style="font-size:22px"><?= fatura_para($kalan) ?></div>
      <div class="meta">%<?= number_format((1 - $oran) * 100, 1, ',', '.') ?> bekliyor</div>
    </div>
    <div class="stat is-count">
      <div class="label">Fatura Adedi</div>
      <div class="value tabular"><?= $adet ?></div>
      <div class="meta"><?= $firmaAdet ?> firmaya dağılmış</div>
    </div>
  </div>

  <div style="display:grid;grid-template-columns:300px 1fr;gap:16px;align-items:stretch;margin-bottom:16px">
    <!-- Payment ratio donut chart -->
    <div class="card card-pad" style="display:flex;flex-direction:column;align-items:center;justify-content:center">
      <div style="font-weight:700;margin-bottom:8px;align-self:flex-start">Ödeme Oranı</div>
      <?php
        $r    = 70;
        $cx   = 90;
        $cy   = 90;
        $circ = 2 * M_PI * $r;
        $dash = $oran * $circ;
      ?>
      <svg width="180" height="180" viewBox="0 0 180 180">
        <circle cx="<?= $cx ?>" cy="<?= $cy ?>" r="<?= $r ?>" fill="none" stroke="#eef0f7" stroke-width="20"/>
        <circle cx="<?= $cx ?>" cy="<?= $cy ?>" r="<?= $r ?>" fill="none" stroke="url(#g1)" stroke-width="20"
                stroke-linecap="round" stroke-dasharray="<?= round($dash, 1) ?> <?= round($circ, 1) ?>"
                transform="rotate(-90 <?= $cx ?> <?= $cy ?>)"/>
        <defs><linearGradient id="g1" x1="0" y1="0" x2="1" y2="1">
          <stop offset="0" stop-color="#10b981"/><stop offset="1" stop-color="#34d399"/>
        </linearGradient></defs>
        <text x="90" y="84" text-anchor="middle" font-size="30" font-weight="700" fill="#1e293b">%<?= round($oran * 100) ?></text>
        <text x="90" y="106" text-anchor="middle" font-size="12" fill="#64748b">ödendi</text>
      </svg>
      <div style="display:flex;gap:16px;margin-top:10px;font-size:12px;color:#64748b">
        <span><span style="display:inline-block;width:9px;height:9px;border-radius:2px;background:#10b981;margin-right:5px"></span>Ödenen</span>
        <span><span style="display:inline-block;width:9px;height:9px;border-radius:2px;background:#eef0f7;margin-right:5px"></span>Kalan</span>
      </div>
    </div>

    <!-- Companies with the largest outstanding debt -->
    <div class="card card-pad">
      <div style="font-weight:700;margin-bottom:14px">En Çok Borç Olunan 5 Firma</div>
      <?php if (! $enBorclu): ?>
        <p class="hint">Ödenmemiş borç bulunmuyor. 🎉</p>
      <?php else: foreach ($enBorclu as $b):
          $w = $maxBorc > 0 ? max(4, ($b['borc'] / $maxBorc) * 100) : 0; ?>
        <div style="margin-bottom:13px">
          <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:5px">
            <span style="font-weight:600"><?= esc($b['ad']) ?></span>
            <span class="tabular" style="color:#e11d48;font-weight:600"><?= fatura_para($b['borc']) ?></span>
          </div>
          <div style="height:9px;background:#f1f3fb;border-radius:999px;overflow:hidden">
            <div style="height:100%;width:<?= $w ?>%;border-radius:999px;background:linear-gradient(90deg,#fb7185,#e11d48)"></div>
          </div>
        </div>
      <?php endforeach; endif; ?>
    </div>
  </div>

  <!-- Per-company breakdown -->
  <div class="card">
    <div class="card-head">
      <span>Firma Bazlı Durum</span>
      <a href="<?= route_to('fatura.companies') ?>" class="btn btn-sm">Tümünü gör →</a>
    </div>
    <div class="table-wrap">
      <table class="grid">
        <thead>
          <tr>
            <th>Firma</th>
            <th class="mid">Fatura</th>
            <th class="num">Toplam</th>
            <th class="num">Kalan Borç</th>
            <th class="mid">Durum</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($firmalar as $f): ?>
          <tr>
            <td><a href="<?= route_to('fatura.invoices') ?>?firma=<?= (int) $f['id'] ?>" style="font-weight:600;color:var(--brand1)"><?= esc($f['ad']) ?></a></td>
            <td class="mid tabular"><?= (int) $f['adet'] ?></td>
            <td class="num tabular"><?= fatura_para($f['toplam']) ?></td>
            <td class="num tabular" style="color:<?= $f['kalan'] > 0 ? '#e11d48' : '#94a3b8' ?>"><?= fatura_para($f['kalan']) ?></td>
            <td class="mid">
              <?php if ($f['kalan'] > 0): ?>
                <span class="badge unpaid">Borç var</span>
              <?php else: ?>
                <span class="badge paid">Temiz</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

<?php endif; ?>

<?= $this->endSection() ?>
