<?php
/* Save offer: folder + offer.json + offer.html + (auto) offer.pdf */
require __DIR__ . '/_common.php';

$in   = reqBody();
$offer= $in['offer'] ?? null;
$html = $in['html']  ?? '';
if (!$offer) respond(['ok'=>false, 'message'=>'Teklif verisi alınamadı']);

@mkdir(offersDir(), 0777, true);

$no   = safeName($offer['offer_no'] ?? '') ?: 'TKL';
$name = $offer['customer']['name'] ?? ($offer['customer']['company'] ?? '');
$slug = slugify($name);
$iso  = dateISO($offer['date'] ?? '');

/* same offer_no folder exists -> UPDATE (don't bump counter) */
$existing = null;
foreach (glob(offersDir().'/*', GLOB_ONLYDIR) as $d){
  if (strpos(basename($d), '_'.$no) !== false){ $existing = $d; break; }
}
$isUpdate = $existing !== null;

$dir = $isUpdate ? $existing : (offersDir().DIRECTORY_SEPARATOR.$iso.'_'.$slug.'_'.$no);
@mkdir($dir, 0777, true);

/* files */
jwrite($dir.DIRECTORY_SEPARATOR.'offer.json', $offer);
file_put_contents($dir.DIRECTORY_SEPARATOR.'offer.html', $html);

/* counter (only on new offer) */
$counter = jread(dataDir().'/counter.json', ['year'=>(int)date('Y'), 'seq'=>0]);
if (!$isUpdate){
  $counter['seq']  = ($counter['seq'] ?? 0) + 1;
  $counter['year'] = (int)date('Y');
  jwrite(dataDir().'/counter.json', $counter);
}

/* PDF (Edge/Chrome headless) */
$pdfCreated = makePdf($dir.DIRECTORY_SEPARATOR.'offer.html', $dir.DIRECTORY_SEPARATOR.'offer.pdf');

/* open folder */
openFolder($dir);

respond([
  'ok'          => true,
  'folder'      => basename($dir),
  'offer_no'    => $no,
  'seq'         => $counter['seq'] ?? null,
  'updated'     => $isUpdate,
  'pdf_created' => $pdfCreated,
  'path'        => $dir
]);
