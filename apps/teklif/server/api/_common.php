<?php
/* Shared helpers — included by every API endpoint */
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function root(){ return dirname(__DIR__, 2); }            // project root
function offersDir(){ return root() . DIRECTORY_SEPARATOR . 'offers'; }
function dataDir(){ return root() . DIRECTORY_SEPARATOR . 'data'; }

function jread($f, $d = null){ return is_file($f) ? json_decode(file_get_contents($f), true) : $d; }
function jwrite($f, $v){ file_put_contents($f, json_encode($v, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)); }
function reqBody(){ return json_decode(file_get_contents('php://input'), true) ?: []; }
function respond($v){ echo json_encode($v, JSON_UNESCAPED_UNICODE); exit; }

/* folder/file name safety (block path traversal) */
function safeName($s){
  $s = basename((string)$s);
  return preg_replace('/[^A-Za-z0-9._\- ]/u', '', $s);
}

/* customer name -> file-friendly slug */
function slugify($s){
  $s = trim((string)$s);
  $tr = ['ı'=>'i','İ'=>'i','ğ'=>'g','Ğ'=>'g','ü'=>'u','Ü'=>'u','ş'=>'s','Ş'=>'s','ö'=>'o','Ö'=>'o','ç'=>'c','Ç'=>'c'];
  $s = strtr($s, $tr);
  $s = preg_replace('/[^A-Za-z0-9]+/', '-', $s);
  $s = trim($s, '-');
  return $s !== '' ? substr($s, 0, 40) : 'customer';
}

/* DD.MM.YYYY -> YYYY-MM-DD (for folder name) */
function dateISO($t){
  if (preg_match('/^(\d{2})\.(\d{2})\.(\d{4})$/', trim((string)$t), $m)) return "$m[3]-$m[2]-$m[1]";
  return date('Y-m-d');
}

/* find all headless-print capable browsers, in preferred order */
function findBrowsers(){
  if (PHP_OS_FAMILY !== 'Windows'){
    $found = [];
    foreach (['/usr/bin/chromium', '/usr/bin/chromium-browser', '/usr/bin/google-chrome'] as $e){
      if (is_file($e)) $found[] = $e;
    }
    return $found;
  }
  $pf   = getenv('PROGRAMFILES')      ?: 'C:\\Program Files';
  $pf86 = getenv('PROGRAMFILES(X86)') ?: 'C:\\Program Files (x86)';
  $la   = getenv('LOCALAPPDATA')      ?: '';
  $candidates = [
    $pf  .'\\Google\\Chrome\\Application\\chrome.exe',
    $pf86.'\\Google\\Chrome\\Application\\chrome.exe',
    $la  .'\\Google\\Chrome\\Application\\chrome.exe',
    $pf86.'\\Microsoft\\Edge\\Application\\msedge.exe',
    $pf  .'\\Microsoft\\Edge\\Application\\msedge.exe',
  ];
  $found = [];
  foreach ($candidates as $e){ if ($e && is_file($e)) $found[] = $e; }
  return array_values(array_unique($found));
}

function findBrowser(){
  return findBrowsers()[0] ?? null;
}

/* offer.html -> offer.pdf (silent, via Edge/Chrome headless) */
function makePdf($htmlPath, $pdfPath){
  if (!function_exists('exec')) return false;
  $browsers = findBrowsers();
  if (!$browsers) return false;

  if (PHP_OS_FAMILY !== 'Windows'){
    // Docker/Linux: www-data'nın HOME'u yazılabilir değil (HOME=/tmp şart),
    // konteynerde sandbox kapalı olmalı, /dev/shm 64 MB olduğundan devre dışı.
    foreach ($browsers as $exe){
      $prof = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'barlas_pdf_' . uniqid();
      @unlink($pdfPath);
      $cmd = 'HOME=/tmp ' . escapeshellarg($exe)
           . ' --headless --no-sandbox --disable-gpu --disable-dev-shm-usage'
           . ' --no-pdf-header-footer --no-margins'
           . ' --user-data-dir=' . escapeshellarg($prof)
           . ' --print-to-pdf=' . escapeshellarg($pdfPath)
           . ' ' . escapeshellarg('file://' . $htmlPath);
      @exec($cmd . ' 2>&1', $o, $rc);
      if (is_file($pdfPath) && filesize($pdfPath) > 800) return true;
    }
    return false;
  }

  $url  = 'file:///' . str_replace('\\', '/', $htmlPath);
  $variants = [
    '--headless=new --disable-gpu --no-pdf-header-footer',
    '--headless --disable-gpu --print-to-pdf-no-header',
  ];
  foreach ($browsers as $exe){
    foreach ($variants as $flags){
      $prof = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'barlas_pdf_' . uniqid();
      @unlink($pdfPath);
      $cmd = escapeshellarg($exe) . ' ' . $flags
           . ' --no-margins --user-data-dir=' . escapeshellarg($prof)
           . ' --print-to-pdf=' . escapeshellarg($pdfPath)
           . ' ' . escapeshellarg($url);
      @exec($cmd . ' 2>&1', $o, $rc);
      if (is_file($pdfPath) && filesize($pdfPath) > 800) return true;
    }
  }
  return false;
}

/* open saved folder in Windows Explorer (best-effort; no-op on Linux) */
function openFolder($dir){
  if (PHP_OS_FAMILY !== 'Windows') return;
  if (function_exists('exec')) @exec('cmd /c start "" explorer "'.$dir.'"');
}
