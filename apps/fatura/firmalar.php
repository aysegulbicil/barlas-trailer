<?php
require_once __DIR__ . '/db.php';
$pdo = db();

$flash = ''; $err = '';

// ---- Ekle ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'ekle') {
    $ad = trim((string) ($_POST['ad'] ?? ''));
    if ($ad === '') {
        $err = 'Firma adı boş olamaz.';
    } else {
        $sel = $pdo->prepare('SELECT id FROM firmalar WHERE ad = ?');
        $sel->execute([$ad]);
        if ($sel->fetchColumn()) {
            $err = 'Bu firma zaten kayıtlı.';
        } else {
            $pdo->prepare('INSERT INTO firmalar (ad) VALUES (?)')->execute([$ad]);
            $flash = '“' . $ad . '” eklendi.';
        }
    }
}

// ---- Sil ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'sil') {
    $fid = (int) ($_POST['id'] ?? 0);
    if ($fid) {
        $pdo->prepare('DELETE FROM firmalar WHERE id = ?')->execute([$fid]);
        $flash = 'Firma ve bağlı faturaları silindi.';
    }
}

// ---- Liste + sayfalama ----
$q    = trim((string) ($_GET['q'] ?? ''));
$page = max(1, (int) ($_GET['page'] ?? 1));
$per  = 10;

$wsql = ''; $args = [];
if ($q !== '') { $wsql = 'WHERE f.ad LIKE ?'; $args[] = '%' . $q . '%'; }

$cnt = $pdo->prepare("SELECT COUNT(*) FROM firmalar f $wsql");
$cnt->execute($args);
$total  = (int) $cnt->fetchColumn();
$pages  = (int) max(1, ceil($total / $per));
$page   = min($page, $pages);
$offset = ($page - 1) * $per;

$sql = "SELECT f.id, f.ad,
               COUNT(fa.id) AS adet,
               COALESCE(SUM(fa.toplam),0) AS toplam,
               COALESCE(SUM(CASE WHEN fa.durum='Ödenmedi' THEN fa.toplam END),0) AS kalan
          FROM firmalar f
     LEFT JOIN faturalar fa ON fa.firma_id = f.id
          $wsql
      GROUP BY f.id, f.ad
      ORDER BY f.ad
         LIMIT $per OFFSET $offset";
$st = $pdo->prepare($sql);
$st->execute($args);
$rows = $st->fetchAll();

$title = 'Firmalar';
$active = 'firmalar.php';
require __DIR__ . '/partials/header.php';
?>

<div class="page-head">
  <div>
    <h1>Firmalar</h1>
    <div class="sub"><?= $total ?> firma kayıtlı</div>
  </div>
</div>

<?php if ($flash): ?><div class="alert ok"><?= e($flash) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert err"><?= e($err) ?></div><?php endif; ?>

<div class="card card-pad" style="margin-bottom:18px">
  <form method="post" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end">
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
  <input type="text" name="q" value="<?= e($q) ?>" placeholder="Firma ara…">
  <button class="btn btn-primary" type="submit">Ara</button>
  <a href="firmalar.php" class="btn">Temizle</a>
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
        <?php if (!$rows): ?>
          <tr><td colspan="5" class="empty" style="padding:36px">Firma bulunamadı.</td></tr>
        <?php else: foreach ($rows as $r): ?>
          <tr>
            <td style="font-weight:600;white-space:normal"><?= e($r['ad']) ?></td>
            <td class="mid tabular"><?= (int)$r['adet'] ?></td>
            <td class="num tabular"><?= para($r['toplam']) ?></td>
            <td class="num tabular" style="color:<?= $r['kalan']>0?'#e11d48':'#94a3b8' ?>"><?= para($r['kalan']) ?></td>
            <td class="mid" style="white-space:nowrap">
              <a href="faturalar.php?firma=<?= (int)$r['id'] ?>" class="btn btn-sm">Faturalar</a>
              <form method="post" style="display:inline" onsubmit="return confirm('“<?= e($r['ad']) ?>” ve TÜM faturaları silinecek. Emin misin?')">
                <input type="hidden" name="action" value="sil">
                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <button class="btn btn-sm btn-danger" type="submit">🗑</button>
              </form>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?= pagination_html($page, $pages, $q !== '' ? ['q' => $q] : []) ?>

<?php require __DIR__ . '/partials/footer.php'; ?>
