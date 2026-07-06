<?php
/**
 * Excel dışa aktarım — saf PHP, harici kütüphane/zip eklentisi gerektirmez.
 * ?firma=ID verilirse tek firmalık tek sayfa; verilmezse GENEL ÖZET + her firma için ayrı sayfa.
 */
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/lib/xlsx.php';

$pdo = db();
$firmaId = (int) ($_GET['firma'] ?? 0);

// Faturası olan firmalar (filtreye göre)
$fSql = 'SELECT id, ad FROM firmalar' . ($firmaId ? ' WHERE id = ?' : '') . ' ORDER BY ad';
$fSt  = $pdo->prepare($fSql);
$fSt->execute($firmaId ? [$firmaId] : []);
$firmalar = $fSt->fetchAll();

if (!$firmalar) { header('Location: faturalar.php'); exit; }

$fatSt = $pdo->prepare(
    "SELECT fatura_tarihi, fatura_no, tur, tutar, kdv_orani, kdv_tutari, toplam,
            odeme_sekli, odeme_tarihi, durum
       FROM faturalar WHERE firma_id = ?
   ORDER BY (durum='Ödenmedi') DESC, fatura_tarihi, id"
);

$baslik  = 'AYLIK FATURA VE ÖDEME TAKİP TABLOSU';
$kollar  = ['Fatura Tarihi','Fatura No','Tür','Tutar','KDV %','KDV Tutarı','Genel Toplam','Ödeme Şekli','Ödeme Tarihi','Durum'];
$genislik = [1=>13, 2=>22, 3=>9, 4=>14, 5=>8, 6=>14, 7=>16, 8=>15, 9=>13, 10=>11];

$tarihGoster = function ($d) {
    if (!$d) return '';
    $t = strtotime((string) $d);
    return $t ? date('d.m.Y', $t) : (string) $d;
};

/** Tek firma sayfası üretir. */
$firmaSayfasi = function (array $firma) use ($fatSt, $baslik, $kollar, $genislik, $tarihGoster): array {
    $fatSt->execute([$firma['id']]);
    $list = $fatSt->fetchAll();

    $rows = [];
    // 1) başlık şeridi (A1:J1 birleşik)
    $r1 = [['v' => $baslik, 's' => 1, 't' => 's']];
    for ($i = 1; $i < 10; $i++) $r1[] = ['v' => '', 's' => 1, 't' => 's'];
    $rows[] = $r1;
    // 2) firma alt başlığı
    $rows[] = [['v' => 'Firma: ' . $firma['ad'], 's' => 12, 't' => 's']];
    // 3) boş
    $rows[] = [];
    // 4) tablo başlığı
    $hdr = [];
    foreach ($kollar as $k) $hdr[] = ['v' => $k, 's' => 2, 't' => 's'];
    $rows[] = $hdr;

    // 5) veri satırları (zebra)
    $toplam = $odenen = $kalan = 0.0;
    foreach ($list as $i => $f) {
        $z = ($i % 2 === 1); // tek satırlar zebra
        $sTxt = $z ? 4 : 3;   $sMon = $z ? 6 : 5;   $sMid = $z ? 8 : 7;
        $rows[] = [
            ['v' => $tarihGoster($f['fatura_tarihi']), 's' => $sMid, 't' => 's'],
            ['v' => (string) ($f['fatura_no'] ?? ''),  's' => $sTxt, 't' => 's'],
            ['v' => $f['tur'],                          's' => $sMid, 't' => 's'],
            ['v' => (float) $f['tutar'],                's' => $sMon, 't' => 'n'],
            ['v' => (float) $f['kdv_orani'],            's' => $sMid, 't' => 'n'],
            ['v' => (float) $f['kdv_tutari'],           's' => $sMon, 't' => 'n'],
            ['v' => (float) $f['toplam'],               's' => $sMon, 't' => 'n'],
            ['v' => (string) ($f['odeme_sekli'] ?? ''), 's' => $sTxt, 't' => 's'],
            ['v' => $tarihGoster($f['odeme_tarihi']),   's' => $sMid, 't' => 's'],
            ['v' => $f['durum'],                        's' => $sMid, 't' => 's'],
        ];
        $toplam += (float) $f['toplam'];
        if ($f['durum'] === 'Ödendi') $odenen += (float) $f['toplam']; else $kalan += (float) $f['toplam'];
    }

    // 6) boş satır + FİNANSAL ÖZET kutusu (F:G sütunları)
    $rows[] = [];
    $box = function (string $label, $val, int $valStyle) {
        $r = array_fill(0, 5, null);          // A-E boş
        $r[5] = ['v' => $label, 's' => 9, 't' => 's'];   // F
        $r[6] = ['v' => $val,   's' => $valStyle, 't' => 'n']; // G
        return $r;
    };
    $hd = array_fill(0, 5, null);
    $hd[5] = ['v' => 'FİNANSAL ÖZET', 's' => 2, 't' => 's'];
    $hd[6] = ['v' => '', 's' => 2, 't' => 's'];
    $rows[] = $hd;
    $rows[] = $box('Toplam Tutar', round($toplam, 2), 10);
    $rows[] = $box('Ödenen',       round($odenen, 2), 10);
    $rows[] = $box('Kalan Borç',   round($kalan, 2), 11);
    // Fatura adedi (sayı, para değil → düz)
    $rc = array_fill(0, 5, null);
    $rc[5] = ['v' => 'Fatura Adedi', 's' => 9, 't' => 's'];
    $rc[6] = ['v' => count($list),   's' => 10, 't' => 'n'];
    $rows[] = $rc;

    $merges = ['A1:J1'];
    return [$rows, $merges, $genislik];
};

$xlsx = new XlsxWriter();

if (!$firmaId && count($firmalar) > 1) {
    // ---- GENEL ÖZET sayfası ----
    $ozetSt = $pdo->query(
        "SELECT f.ad,
                COUNT(fa.id) adet,
                COALESCE(SUM(fa.toplam),0) toplam,
                COALESCE(SUM(CASE WHEN fa.durum='Ödendi'   THEN fa.toplam END),0) odenen,
                COALESCE(SUM(CASE WHEN fa.durum='Ödenmedi' THEN fa.toplam END),0) kalan
           FROM firmalar f JOIN faturalar fa ON fa.firma_id=f.id
       GROUP BY f.id, f.ad ORDER BY kalan DESC, f.ad"
    )->fetchAll();

    $rows = [];
    $r1 = [['v' => 'GENEL ÖZET — FİRMA BAZLI', 's' => 1, 't' => 's']];
    for ($i = 1; $i < 5; $i++) $r1[] = ['v' => '', 's' => 1, 't' => 's'];
    $rows[] = $r1;
    $rows[] = [['v' => date('d.m.Y') . ' tarihli rapor', 's' => 12, 't' => 's']];
    $rows[] = [];
    $rows[] = [
        ['v' => 'Firma', 's' => 2, 't' => 's'],
        ['v' => 'Fatura Adedi', 's' => 2, 't' => 's'],
        ['v' => 'Toplam', 's' => 2, 't' => 's'],
        ['v' => 'Ödenen', 's' => 2, 't' => 's'],
        ['v' => 'Kalan Borç', 's' => 2, 't' => 's'],
    ];
    $gT = $gO = $gK = 0.0; $gA = 0;
    foreach ($ozetSt as $i => $o) {
        $z = ($i % 2 === 1);
        $sTxt = $z ? 4 : 3; $sMon = $z ? 6 : 5; $sMid = $z ? 8 : 7;
        $rows[] = [
            ['v' => $o['ad'],            's' => $sTxt, 't' => 's'],
            ['v' => (int) $o['adet'],    's' => $sMid, 't' => 'n'],
            ['v' => (float) $o['toplam'],'s' => $sMon, 't' => 'n'],
            ['v' => (float) $o['odenen'],'s' => $sMon, 't' => 'n'],
            ['v' => (float) $o['kalan'], 's' => $sMon, 't' => 'n'],
        ];
        $gT += (float) $o['toplam']; $gO += (float) $o['odenen']; $gK += (float) $o['kalan']; $gA += (int) $o['adet'];
    }
    // genel toplam satırı
    $rows[] = [
        ['v' => 'GENEL TOPLAM', 's' => 9, 't' => 's'],
        ['v' => $gA, 's' => 10, 't' => 'n'],
        ['v' => round($gT, 2), 's' => 10, 't' => 'n'],
        ['v' => round($gO, 2), 's' => 10, 't' => 'n'],
        ['v' => round($gK, 2), 's' => 11, 't' => 'n'],
    ];
    $xlsx->addSheet('GENEL ÖZET', $rows, ['A1:E1'], [1=>34, 2=>14, 3=>16, 4=>16, 5=>16]);
}

// Her firma için sayfa
foreach ($firmalar as $firma) {
    [$rows, $merges, $cols] = $firmaSayfasi($firma);
    $xlsx->addSheet($firma['ad'], $rows, $merges, $cols);
}

$ad = $firmaId
    ? 'Fatura_' . preg_replace('/[^A-Za-z0-9]+/', '_', $firmalar[0]['ad'])
    : 'Fatura_Takip_GENEL_OZET';
$xlsx->download($ad . '_' . date('Y-m-d') . '.xlsx');
