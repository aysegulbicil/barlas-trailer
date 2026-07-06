<?php
require_once __DIR__ . '/db.php';
$pdo = db();

$flash = '';
// ---- Silme ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'sil') {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id) {
        $pdo->prepare('DELETE FROM faturalar WHERE id = ?')->execute([$id]);
        $flash = 'Fatura silindi.';
    }
}

// ---- Filtreler ----
$q      = trim((string) ($_GET['q'] ?? ''));
$firma  = (int) ($_GET['firma'] ?? 0);
$durum  = (string) ($_GET['durum'] ?? '');
$tur    = (string) ($_GET['tur'] ?? '');
$page   = max(1, (int) ($_GET['page'] ?? 1));
$per    = 10;

$where = [];
$args  = [];
if ($q !== '') {
    $where[] = '(f.ad LIKE ? OR fa.fatura_no LIKE ? OR fa.aciklama LIKE ?)';
    $like = '%' . $q . '%';
    array_push($args, $like, $like, $like);
}
if ($firma > 0)                                   { $where[] = 'fa.firma_id = ?'; $args[] = $firma; }
if (in_array($durum, ['Ödendi', 'Ödenmedi'], true)) { $where[] = 'fa.durum = ?';   $args[] = $durum; }
if (in_array($tur, ['Gelen', 'Giden'], true))      { $where[] = 'fa.tur = ?';      $args[] = $tur; }
$wsql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

// ---- Sayım + sayfalama ----
$cnt = $pdo->prepare("SELECT COUNT(*) FROM faturalar fa JOIN firmalar f ON f.id=fa.firma_id $wsql");
$cnt->execute($args);
$total   = (int) $cnt->fetchColumn();
$pages   = (int) max(1, ceil($total / $per));
$page    = min($page, $pages);
$offset  = ($page - 1) * $per;

// ---- Filtreye göre özet ----
$sumStmt = $pdo->prepare(
    "SELECT COALESCE(SUM(fa.toplam),0) t,
            COALESCE(SUM(CASE WHEN fa.durum='Ödenmedi' THEN fa.toplam END),0) k
       FROM faturalar fa JOIN firmalar f ON f.id=fa.firma_id $wsql"
);
$sumStmt->execute($args);
$sum = $sumStmt->fetch();

// ---- Kayıtlar ----
$sql = "SELECT fa.*, f.ad AS firma_ad
          FROM faturalar fa JOIN firmalar f ON f.id=fa.firma_id
          $wsql
      ORDER BY (fa.durum='Ödenmedi') DESC, fa.fatura_tarihi DESC, fa.id DESC
         LIMIT $per OFFSET $offset";
$st = $pdo->prepare($sql);
$st->execute($args);
$rows = $st->fetchAll();

$firmaList = $pdo->query('SELECT id, ad FROM firmalar ORDER BY ad')->fetchAll();

$baseParams = array_filter(['q' => $q, 'firma' => $firma ?: '', 'durum' => $durum, 'tur' => $tur], fn($v) => $v !== '' && $v !== 0);

$title = 'Faturalar';
$active = 'faturalar.php';
require __DIR__ . '/partials/header.php';
?>

<div class="page-head">
  <div>
    <h1>Faturalar</h1>
    <div class="sub"><?= $total ?> kayıt · Toplam <?= para($sum['t']) ?> · Kalan <span style="color:#e11d48"><?= para($sum['k']) ?></span></div>
  </div>
  <a href="fatura_form.php" class="btn btn-primary">
    <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
    Yeni Fatura
  </a>
</div>

<?php if ($flash): ?><div class="alert ok"><?= e($flash) ?></div><?php endif; ?>

<form method="get" class="toolbar">
  <input type="text" name="q" value="<?= e($q) ?>" placeholder="Firma, fatura no veya açıklama ara…">
  <select name="firma">
    <option value="">Tüm firmalar</option>
    <?php foreach ($firmaList as $fl): ?>
      <option value="<?= (int)$fl['id'] ?>" <?= $firma === (int)$fl['id'] ? 'selected' : '' ?>><?= e($fl['ad']) ?></option>
    <?php endforeach; ?>
  </select>
  <select name="durum">
    <option value="">Tüm durumlar</option>
    <option value="Ödenmedi" <?= $durum === 'Ödenmedi' ? 'selected' : '' ?>>Ödenmedi</option>
    <option value="Ödendi"   <?= $durum === 'Ödendi'   ? 'selected' : '' ?>>Ödendi</option>
  </select>
  <select name="tur">
    <option value="">Tüm türler</option>
    <option value="Gelen" <?= $tur === 'Gelen' ? 'selected' : '' ?>>Gelen</option>
    <option value="Giden" <?= $tur === 'Giden' ? 'selected' : '' ?>>Giden</option>
  </select>
  <button class="btn btn-primary" type="submit">Filtrele</button>
  <a href="faturalar.php" class="btn">Temizle</a>
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
        <?php if (!$rows): ?>
          <tr><td colspan="9" class="empty" style="padding:36px">Kayıt bulunamadı.</td></tr>
        <?php else: foreach ($rows as $r): ?>
          <tr>
            <td style="font-weight:600;white-space:normal;max-width:240px"><?= e($r['firma_ad']) ?></td>
            <td><?= $r['fatura_no'] ? e($r['fatura_no']) : '<span style="color:#cbd5e1">—</span>' ?></td>
            <td class="mid"><?= tarih($r['fatura_tarihi']) ?></td>
            <td class="mid"><span class="badge tur"><?= e($r['tur']) ?></span></td>
            <td class="num tabular"><?= para($r['tutar']) ?></td>
            <td class="num tabular" style="color:#94a3b8"><?= para($r['kdv_tutari']) ?></td>
            <td class="num tabular" style="font-weight:600"><?= para($r['toplam']) ?></td>
            <td class="mid">
              <span class="badge <?= $r['durum'] === 'Ödendi' ? 'paid' : 'unpaid' ?>"><?= e($r['durum']) ?></span>
            </td>
            <td class="mid" style="white-space:nowrap">
              <a href="fatura_form.php?id=<?= (int)$r['id'] ?>" class="btn btn-sm" title="Düzenle">✎</a>
              <form method="post" style="display:inline" onsubmit="return confirm('Bu fatura silinsin mi?')">
                <input type="hidden" name="action" value="sil">
                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <button class="btn btn-sm btn-danger" type="submit" title="Sil">🗑</button>
              </form>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?= pagination_html($page, $pages, $baseParams) ?>

<?php require __DIR__ . '/partials/footer.php'; ?>
