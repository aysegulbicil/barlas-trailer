<?php
/* Store a browser-generated PDF in an existing offer folder. */
require __DIR__ . '/_common.php';

$folder = safeName($_POST['folder'] ?? '');
$base   = realpath(offersDir());
$dir    = ($folder !== '' && $base !== false) ? realpath($base . DIRECTORY_SEPARATOR . $folder) : false;

if ($dir === false || !is_dir($dir) || !str_starts_with($dir, $base . DIRECTORY_SEPARATOR)) {
  http_response_code(404);
  respond(['ok'=>false, 'message'=>'Teklif klasörü bulunamadı']);
}

$upload = $_FILES['pdf'] ?? null;
$tmp    = is_array($upload) ? ($upload['tmp_name'] ?? '') : '';
$size   = is_array($upload) ? (int)($upload['size'] ?? 0) : 0;
if (!$upload || ($upload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || !is_uploaded_file($tmp)) {
  http_response_code(400);
  respond(['ok'=>false, 'message'=>'PDF dosyası alınamadı']);
}
if ($size < 800 || $size > 30 * 1024 * 1024 || file_get_contents($tmp, false, null, 0, 5) !== '%PDF-') {
  http_response_code(400);
  respond(['ok'=>false, 'message'=>'Geçersiz PDF dosyası']);
}

$target = $dir . DIRECTORY_SEPARATOR . 'offer.pdf';
$staged = $dir . DIRECTORY_SEPARATOR . 'offer.pdf.uploading';
$bytes  = file_put_contents($staged, file_get_contents($tmp), LOCK_EX);
if ($bytes !== $size || (!@rename($staged, $target) && (!@unlink($target) || !@rename($staged, $target)))) {
  @unlink($staged);
  http_response_code(500);
  respond(['ok'=>false, 'message'=>'PDF arşive yazılamadı']);
}

respond(['ok'=>true, 'bytes'=>$bytes]);
