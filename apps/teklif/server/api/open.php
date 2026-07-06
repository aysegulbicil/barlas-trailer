<?php
/* Return one offer's data (for editing) */
require __DIR__ . '/_common.php';

$f = safeName($_GET['folder'] ?? '');
$file = offersDir() . DIRECTORY_SEPARATOR . $f . DIRECTORY_SEPARATOR . 'offer.json';
if ($f === '' || !is_file($file)) respond(['ok'=>false, 'message'=>'Bulunamadı']);
respond(jread($file, ['ok'=>false]));
