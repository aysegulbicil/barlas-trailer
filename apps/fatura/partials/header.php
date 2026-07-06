<?php
/** Ortak sayfa başlığı + tema. Beklenen değişkenler: $title, $active */
if (!isset($title))  $title  = 'Fatura Takip';
if (!isset($active)) $active = '';

$nav = [
    'index.php'     => ['Genel Özet', 'M3 12l9-8 9 8M5 10v10h14V10'],
    'faturalar.php' => ['Faturalar',  'M7 3h10l3 3v15H4V6zM7 3v4h10'],
    'firmalar.php'  => ['Firmalar',   'M3 21V8l7-4 7 4v13M9 21v-6h2v6'],
    'export.php'    => ['Excel\'e Aktar', 'M4 4h16v16H4zM4 9h16M9 9v11'],
    'import.php'    => ['İçe Aktar',  'M12 3v12m0 0l-4-4m4 4l4-4M4 21h16'],
];
?>
<!doctype html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?> · Fatura Takip</title>
<style>
:root{
  --brand1:#4f46e5; --brand2:#6366f1; --brand3:#818cf8;
  --ink:#1e293b; --ink-soft:#64748b; --line:#e6e8f0;
  --bg:#f4f5fb; --card:#ffffff;
  --ok:#059669; --ok-bg:#ecfdf5; --debt:#e11d48; --debt-bg:#fff1f3;
  --warn:#b45309; --warn-bg:#fffbeb;
  --shadow:0 1px 2px rgba(16,24,40,.04),0 8px 24px rgba(16,24,40,.06);
  --radius:14px;
}
*{box-sizing:border-box}
html,body{margin:0;padding:0}
body{
  font:14px/1.5 -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;
  color:var(--ink); background:var(--bg);
  overflow-x:hidden;
  -webkit-font-smoothing:antialiased;
}
a{color:inherit;text-decoration:none}
.tabular{font-variant-numeric:tabular-nums}

/* üstte ince gradient şerit */
.topstrip{height:4px;background:linear-gradient(90deg,var(--brand1),var(--brand3),var(--brand1))}

/* yapışkan, blur'lu üst bar */
.topbar{
  position:sticky; top:0; z-index:50;
  background:rgba(255,255,255,.78);
  backdrop-filter:saturate(180%) blur(12px);
  -webkit-backdrop-filter:saturate(180%) blur(12px);
  border-bottom:1px solid var(--line);
}
.topbar-inner{
  max-width:1480px; margin:0 auto; padding:10px 24px;
  display:flex; align-items:center; gap:22px; min-width:0;
}
.brand{display:flex;align-items:center;gap:11px;font-weight:700;font-size:16px;letter-spacing:-.2px}
.brand .logo{
  width:34px;height:34px;border-radius:10px;flex:none;
  background:linear-gradient(135deg,var(--brand1),var(--brand3));
  display:grid;place-items:center;color:#fff;
  box-shadow:0 4px 12px rgba(79,70,229,.35);
}
.brand small{display:block;font-weight:500;font-size:11px;color:var(--ink-soft);margin-top:1px}

.nav{display:flex;align-items:center;gap:4px;margin-left:auto;flex-wrap:wrap}
.nav a{
  display:inline-flex;align-items:center;gap:7px;
  padding:8px 14px;border-radius:999px;font-weight:600;font-size:13px;
  color:var(--ink-soft); transition:.15s background,.15s color;
}
.nav a svg{width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:1.8}
.nav a:hover{background:#eef0fb;color:var(--brand1)}
.nav a.active{
  color:#fff;
  background:linear-gradient(135deg,var(--brand1),var(--brand3));
  box-shadow:0 6px 16px rgba(79,70,229,.30);
}

.container{max-width:1480px;margin:0 auto;padding:26px 24px 60px;min-width:0}

/* sayfa başlığı */
.page-head{display:flex;align-items:flex-end;justify-content:space-between;gap:16px;margin-bottom:22px;flex-wrap:wrap}
.page-head h1{margin:0;font-size:24px;letter-spacing:-.4px}
.page-head .sub{color:var(--ink-soft);font-size:13px;margin-top:4px}

/* kartlar / istatistikler */
.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:16px;margin-bottom:22px}
.stat{background:var(--card);border:1px solid var(--line);border-radius:var(--radius);padding:18px 20px;box-shadow:var(--shadow);position:relative;overflow:hidden}
.stat::before{content:"";position:absolute;left:0;top:0;bottom:0;width:4px;background:var(--brand2)}
.stat.is-total::before{background:linear-gradient(var(--brand1),var(--brand3))}
.stat.is-paid::before{background:var(--ok)}
.stat.is-debt::before{background:var(--debt)}
.stat.is-count::before{background:#0ea5e9}
.stat .label{color:var(--ink-soft);font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.4px}
.stat .value{font-size:26px;font-weight:700;margin-top:6px;letter-spacing:-.5px}
.stat.is-paid .value{color:var(--ok)}
.stat.is-debt .value{color:var(--debt)}
.stat .meta{color:var(--ink-soft);font-size:12px;margin-top:4px}

.card{background:var(--card);border:1px solid var(--line);border-radius:var(--radius);box-shadow:var(--shadow)}
.card-pad{padding:22px}
.card-head{padding:16px 20px;border-bottom:1px solid var(--line);font-weight:700;display:flex;align-items:center;justify-content:space-between;gap:12px}

/* butonlar */
.btn{
  display:inline-flex;align-items:center;gap:7px;cursor:pointer;
  padding:9px 16px;border-radius:10px;font-weight:600;font-size:13px;
  border:1px solid var(--line);background:#fff;color:var(--ink);transition:.15s;
}
.btn:hover{background:#f8f9ff;border-color:#d6d9ec}
.btn svg{width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:1.9}
.btn-primary{
  color:#fff;border:none;
  background:linear-gradient(135deg,var(--brand1),var(--brand3));
  box-shadow:0 6px 16px rgba(79,70,229,.28);
}
.btn-primary:hover{filter:brightness(1.06)}
.btn-danger{color:var(--debt);border-color:#fecdd3;background:#fff}
.btn-danger:hover{background:var(--debt-bg)}
.btn-sm{padding:6px 11px;font-size:12px;border-radius:8px}

/* uyarılar */
.alert{padding:13px 16px;border-radius:10px;font-size:13px;margin-bottom:18px;font-weight:500}
.alert.ok{background:var(--ok-bg);color:#065f46;border:1px solid #a7f3d0}
.alert.err{background:var(--debt-bg);color:#9f1239;border:1px solid #fecdd3}
.hint{color:var(--ink-soft);font-size:12.5px}

/* tablolar */
.table-wrap{overflow-x:auto}
table.grid{width:100%;border-collapse:collapse;font-size:13px}
table.grid th,table.grid td{padding:7px 12px;border-bottom:1px solid var(--line);text-align:left;white-space:nowrap}
table.grid th{
  position:sticky;top:0;background:#f8f9fd;color:var(--ink-soft);
  font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;
}
table.grid tbody tr:nth-child(even){background:#fafbff}
table.grid tbody tr:hover{background:#f1f3ff}
table.grid td.num,table.grid th.num{text-align:right;font-variant-numeric:tabular-nums;width:130px}
table.grid td.mid,table.grid th.mid{text-align:center;width:90px}

/* durum rozetleri */
.badge{display:inline-block;padding:3px 10px;border-radius:999px;font-size:11.5px;font-weight:700}
.badge.paid{background:var(--ok-bg);color:#047857}
.badge.unpaid{background:var(--debt-bg);color:#be123c}
.badge.tur{background:#eef0fb;color:var(--brand1)}

/* toolbar / filtreler */
.toolbar{display:flex;gap:10px;flex-wrap:wrap;align-items:center;margin-bottom:16px}
.toolbar input[type=text],.toolbar select,.field input,.field select,.field textarea{
  padding:9px 12px;border:1px solid var(--line);border-radius:10px;font:inherit;font-size:13px;background:#fff;color:var(--ink);
}
.toolbar input[type=text]{min-width:220px}
.toolbar input:focus,.toolbar select:focus,.field input:focus,.field select:focus,.field textarea:focus{
  outline:none;border-color:var(--brand2);box-shadow:0 0 0 3px rgba(99,102,241,.15)
}

/* form ızgarası */
.form-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px}
.field{display:flex;flex-direction:column;gap:6px}
.field label{font-size:12px;font-weight:600;color:var(--ink-soft)}
.field input,.field select,.field textarea{width:100%}

/* sayfalama */
.pagination{display:flex;gap:6px;justify-content:center;margin-top:18px;flex-wrap:wrap}
.pagination a,.pagination span{
  min-width:34px;height:34px;padding:0 10px;border-radius:9px;
  display:inline-flex;align-items:center;justify-content:center;font-size:13px;font-weight:600;
  border:1px solid var(--line);background:#fff;color:var(--ink-soft);
}
.pagination a:hover{border-color:var(--brand2);color:var(--brand1)}
.pagination .current{background:linear-gradient(135deg,var(--brand1),var(--brand3));color:#fff;border:none}
.pagination .disabled,.pagination .gap{opacity:.45;border:none;background:transparent}

.empty{text-align:center;padding:48px 20px;color:var(--ink-soft)}
.empty svg{width:46px;height:46px;stroke:#c7cbe0;fill:none;stroke-width:1.5;margin-bottom:12px}
</style>
</head>
<body>
<div class="topstrip"></div>
<header class="topbar">
  <div class="topbar-inner">
    <a href="index.php" class="brand">
      <span class="logo">
        <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 3h10l3 3v15H4V6z"/><path d="M8 8h8M8 12h8M8 16h5"/></svg>
      </span>
      <span>Fatura Takip<small>Fatura &amp; ödeme yönetimi</small></span>
    </a>
    <nav class="nav">
      <?php foreach ($nav as $href => $item): ?>
        <a href="<?= $href ?>" class="<?= $active === $href ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="<?= $item[1] ?>"/></svg>
          <?= $item[0] ?>
        </a>
      <?php endforeach; ?>
    </nav>
  </div>
</header>
<main class="container">
