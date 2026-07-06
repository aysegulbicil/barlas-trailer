<?php
/* Delete an offer folder */
require __DIR__ . '/_common.php';

$in = reqBody();
$f  = safeName($in['folder'] ?? '');
$dir= offersDir() . DIRECTORY_SEPARATOR . $f;
if ($f === '' || !is_dir($dir)) respond(['ok'=>false, 'message'=>'Bulunamadı']);

$it = new RecursiveIteratorIterator(
  new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
  RecursiveIteratorIterator::CHILD_FIRST
);
foreach ($it as $p){ $p->isDir() ? @rmdir($p->getPathname()) : @unlink($p->getPathname()); }
@rmdir($dir);

respond(['ok' => !is_dir($dir)]);
