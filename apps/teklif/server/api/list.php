<?php
/* List saved offers (newest first) */
require __DIR__ . '/_common.php';

$out = [];
foreach (glob(offersDir().'/*', GLOB_ONLYDIR) as $d){
  $o = jread($d.'/offer.json', []);

  /* product summary — supports new items[] and old single product */
  $product = '';
  if (!empty($o['items']) && is_array($o['items'])){
    $names = [];
    foreach ($o['items'] as $it){ if (!empty($it['product']['name'])) $names[] = $it['product']['name']; }
    if ($names){
      $product = $names[0];
      if (count($names) > 1) $product .= ' (+'.(count($names)-1).' ürün)';
    }
  } else {
    $product = $o['product']['name'] ?? '';
  }

  $out[] = [
    'folder'   => basename($d),
    'offer_no' => $o['offer_no'] ?? basename($d),
    'date'     => $o['date'] ?? '',
    'customer' => ($o['customer']['name'] ?? '') ?: ($o['customer']['company'] ?? ''),
    'product'  => $product,
    'pdf'      => is_file($d.'/offer.pdf'),
    'mtime'    => @filemtime($d) ?: 0
  ];
}
usort($out, fn($a,$b) => $b['mtime'] <=> $a['mtime']);
respond($out);
