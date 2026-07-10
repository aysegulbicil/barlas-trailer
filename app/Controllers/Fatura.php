<?php

namespace App\Controllers;

use App\Libraries\XlsxWriter;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\HTTP\DownloadResponse;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Embedded invoice tracking tool (/fatura).
 *
 * Port of the standalone pure-PHP app in apps/fatura (kept there as backup)
 * onto the main CI4 application: the SQLite default connection replaces the
 * PDO-MySQL link, route names replace *.php links and views extend a shared
 * layout. Routes are protected by the `appsauth` filter (app/Config/Routes.php),
 * so there is deliberately no session/auth check here. Page cache is
 * intentionally NOT used: these are private, stateful screens.
 */
class Fatura extends BaseController
{
    /** Keep BaseController's helpers and add the fatura view helpers. */
    protected $helpers = ['url', 'locale', 'auth', 'setting', 'fatura'];

    private ?BaseConnection $dbc = null;

    private function db(): BaseConnection
    {
        return $this->dbc ??= db_connect();
    }

    /** GET|POST /fatura — overall summary dashboard. */
    public function index(): string
    {
        $db = $this->db();

        // ---- Global totals ----
        $ozet = $db->query(
            "SELECT
                COUNT(*) AS adet,
                COALESCE(SUM(toplam),0) AS toplam,
                COALESCE(SUM(CASE WHEN durum='Ödendi'   THEN toplam END),0) AS odenen,
                COALESCE(SUM(CASE WHEN durum='Ödenmedi' THEN toplam END),0) AS kalan
             FROM faturalar"
        )->getRowArray();

        $firmaAdet = (int) $db->query('SELECT COUNT(*) AS cnt FROM firmalar')->getRowArray()['cnt'];
        $adet      = (int) $ozet['adet'];
        $toplam    = (float) $ozet['toplam'];
        $odenen    = (float) $ozet['odenen'];
        $kalan     = (float) $ozet['kalan'];
        $oran      = $toplam > 0 ? $odenen / $toplam : 0;

        // ---- Top 5 companies by outstanding debt ----
        $enBorclu = $db->query(
            "SELECT f.ad, COALESCE(SUM(fa.toplam),0) AS borc
               FROM firmalar f
               JOIN faturalar fa ON fa.firma_id = f.id AND fa.durum='Ödenmedi'
           GROUP BY f.id, f.ad
           HAVING borc > 0
           ORDER BY borc DESC
              LIMIT 5"
        )->getResultArray();
        $maxBorc = 0.0;
        foreach ($enBorclu as $b) {
            $maxBorc = max($maxBorc, (float) $b['borc']);
        }

        // ---- Per-company breakdown (first 12) ----
        $firmalar = $db->query(
            "SELECT f.id, f.ad,
                    COUNT(fa.id) AS adet,
                    COALESCE(SUM(fa.toplam),0) AS toplam,
                    COALESCE(SUM(CASE WHEN fa.durum='Ödenmedi' THEN fa.toplam END),0) AS kalan
               FROM firmalar f
          LEFT JOIN faturalar fa ON fa.firma_id = f.id
           GROUP BY f.id, f.ad
           ORDER BY kalan DESC, toplam DESC
              LIMIT 12"
        )->getResultArray();

        return view('fatura/index', [
            'title'     => 'Genel Özet',
            'active'    => 'fatura',
            'adet'      => $adet,
            'toplam'    => $toplam,
            'odenen'    => $odenen,
            'kalan'     => $kalan,
            'oran'      => $oran,
            'firmaAdet' => $firmaAdet,
            'enBorclu'  => $enBorclu,
            'maxBorc'   => $maxBorc,
            'firmalar'  => $firmalar,
        ]);
    }

    /** GET|POST /fatura/faturalar — invoice list with filters + delete action. */
    public function invoices(): string
    {
        $db    = $this->db();
        $flash = '';

        // ---- Delete ----
        if ($this->request->is('post') && $this->request->getPost('action') === 'sil') {
            $id = (int) $this->request->getPost('id');
            if ($id) {
                $db->query('DELETE FROM faturalar WHERE id = ?', [$id]);
                $flash = 'Fatura silindi.';
            }
        }

        // ---- Filters ----
        $q     = trim((string) $this->request->getGet('q'));
        $firma = (int) $this->request->getGet('firma');
        $durum = (string) ($this->request->getGet('durum') ?? '');
        $tur   = (string) ($this->request->getGet('tur') ?? '');
        $page  = max(1, (int) $this->request->getGet('page'));
        $per   = 10;

        $where = [];
        $args  = [];
        if ($q !== '') {
            $where[] = '(f.ad LIKE ? OR fa.fatura_no LIKE ? OR fa.aciklama LIKE ?)';
            $like    = '%' . $q . '%';
            array_push($args, $like, $like, $like);
        }
        if ($firma > 0) {
            $where[] = 'fa.firma_id = ?';
            $args[]  = $firma;
        }
        if (in_array($durum, ['Ödendi', 'Ödenmedi'], true)) {
            $where[] = 'fa.durum = ?';
            $args[]  = $durum;
        }
        if (in_array($tur, ['Gelen', 'Giden'], true)) {
            $where[] = 'fa.tur = ?';
            $args[]  = $tur;
        }
        $wsql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

        // ---- Count + pagination ----
        $total  = (int) $db->query(
            "SELECT COUNT(*) AS cnt FROM faturalar fa JOIN firmalar f ON f.id=fa.firma_id {$wsql}",
            $args
        )->getRowArray()['cnt'];
        $pages  = (int) max(1, ceil($total / $per));
        $page   = min($page, $pages);
        $offset = ($page - 1) * $per;

        // ---- Summary for the current filter ----
        $sum = $db->query(
            "SELECT COALESCE(SUM(fa.toplam),0) t,
                    COALESCE(SUM(CASE WHEN fa.durum='Ödenmedi' THEN fa.toplam END),0) k
               FROM faturalar fa JOIN firmalar f ON f.id=fa.firma_id {$wsql}",
            $args
        )->getRowArray();

        // ---- Rows ----
        $rows = $db->query(
            "SELECT fa.*, f.ad AS firma_ad
               FROM faturalar fa JOIN firmalar f ON f.id=fa.firma_id
               {$wsql}
           ORDER BY (fa.durum='Ödenmedi') DESC, fa.fatura_tarihi DESC, fa.id DESC
              LIMIT {$per} OFFSET {$offset}",
            $args
        )->getResultArray();

        $firmaList = $db->query('SELECT id, ad FROM firmalar ORDER BY ad')->getResultArray();

        $baseParams = array_filter(
            ['q' => $q, 'firma' => $firma ?: '', 'durum' => $durum, 'tur' => $tur],
            static fn ($v) => $v !== '' && $v !== 0
        );

        return view('fatura/invoices', [
            'title'      => 'Faturalar',
            'active'     => 'fatura.invoices',
            'flash'      => $flash,
            'q'          => $q,
            'firma'      => $firma,
            'durum'      => $durum,
            'tur'        => $tur,
            'total'      => $total,
            'sum'        => $sum,
            'rows'       => $rows,
            'firmaList'  => $firmaList,
            'page'       => $page,
            'pages'      => $pages,
            'baseParams' => $baseParams,
        ]);
    }

    /** GET|POST /fatura/form — create / edit an invoice (?id= edits). */
    public function form(): string|RedirectResponse
    {
        $db  = $this->db();
        $id  = (int) $this->request->getGet('id');
        $err = '';

        // Default record.
        $f = [
            'firma_id' => 0, 'fatura_tarihi' => '', 'fatura_no' => '', 'tur' => 'Gelen',
            'tutar' => '', 'kdv_orani' => '20', 'odeme_sekli' => '', 'odeme_tarihi' => '',
            'durum' => 'Ödenmedi', 'aciklama' => '',
        ];

        if ($id && ! $this->request->is('post')) {
            $row = $db->query('SELECT * FROM faturalar WHERE id = ?', [$id])->getRowArray();
            if (! $row) {
                return redirect()->to(route_to('fatura.invoices'));
            }
            $f = $row;
        }

        if ($this->request->is('post')) {
            $post      = $this->request->getPost();
            $f         = array_merge($f, $post);
            $yeniFirma = trim((string) ($post['yeni_firma'] ?? ''));
            $firmaId   = (int) ($post['firma_id'] ?? 0);

            // A new company name was typed: find it or create it.
            if ($yeniFirma !== '') {
                $row     = $db->query('SELECT id FROM firmalar WHERE ad = ?', [$yeniFirma])->getRowArray();
                $firmaId = (int) ($row['id'] ?? 0);
                if (! $firmaId) {
                    $db->query('INSERT INTO firmalar (ad) VALUES (?)', [$yeniFirma]);
                    $firmaId = (int) $db->insertID();
                }
            }

            $tutar  = (float) str_replace(',', '.', (string) ($post['tutar'] ?? '0'));
            $kdvO   = (float) str_replace(',', '.', (string) ($post['kdv_orani'] ?? '0'));
            $kdvT   = round($tutar * $kdvO / 100, 2);
            $toplam = round($tutar + $kdvT, 2);

            if (! $firmaId) {
                $err = 'Lütfen bir firma seç ya da yeni firma adı gir.';
            } elseif ($tutar <= 0) {
                $err = 'Tutar 0\'dan büyük olmalı.';
            }

            if ($err === '') {
                $params = [
                    $firmaId,
                    ($post['fatura_tarihi'] ?? '') ?: null,
                    trim((string) ($post['fatura_no'] ?? '')) ?: null,
                    in_array($post['tur'] ?? '', ['Gelen', 'Giden'], true) ? $post['tur'] : 'Gelen',
                    $tutar, $kdvO, $kdvT, $toplam,
                    trim((string) ($post['odeme_sekli'] ?? '')) ?: null,
                    ($post['odeme_tarihi'] ?? '') ?: null,
                    in_array($post['durum'] ?? '', ['Ödendi', 'Ödenmedi'], true) ? $post['durum'] : 'Ödenmedi',
                    trim((string) ($post['aciklama'] ?? '')) ?: null,
                ];
                if ($id) {
                    $params[] = $id;
                    $db->query(
                        'UPDATE faturalar SET firma_id=?,fatura_tarihi=?,fatura_no=?,tur=?,tutar=?,
                         kdv_orani=?,kdv_tutari=?,toplam=?,odeme_sekli=?,odeme_tarihi=?,durum=?,aciklama=?
                         WHERE id=?',
                        $params
                    );
                } else {
                    $db->query(
                        'INSERT INTO faturalar
                         (firma_id,fatura_tarihi,fatura_no,tur,tutar,kdv_orani,kdv_tutari,toplam,odeme_sekli,odeme_tarihi,durum,aciklama)
                         VALUES (?,?,?,?,?,?,?,?,?,?,?,?)',
                        $params
                    );
                }

                return redirect()->to(route_to('fatura.invoices'));
            }
        }

        $firmaList = $db->query('SELECT id, ad FROM firmalar ORDER BY ad')->getResultArray();

        return view('fatura/form', [
            'title'     => $id ? 'Faturayı Düzenle' : 'Yeni Fatura',
            'active'    => 'fatura.invoices',
            'id'        => $id,
            'f'         => $f,
            'err'       => $err,
            'firmaList' => $firmaList,
        ]);
    }

    /** GET|POST /fatura/firmalar — company list with add / delete actions. */
    public function companies(): string
    {
        $db    = $this->db();
        $flash = '';
        $err   = '';

        // ---- Add ----
        if ($this->request->is('post') && $this->request->getPost('action') === 'ekle') {
            $ad = trim((string) $this->request->getPost('ad'));
            if ($ad === '') {
                $err = 'Firma adı boş olamaz.';
            } else {
                $exists = $db->query('SELECT id FROM firmalar WHERE ad = ?', [$ad])->getRowArray();
                if ($exists) {
                    $err = 'Bu firma zaten kayıtlı.';
                } else {
                    $db->query('INSERT INTO firmalar (ad) VALUES (?)', [$ad]);
                    $flash = '“' . $ad . '” eklendi.';
                }
            }
        }

        // ---- Delete ----
        if ($this->request->is('post') && $this->request->getPost('action') === 'sil') {
            $fid = (int) $this->request->getPost('id');
            if ($fid) {
                // The FK cascade would also remove the invoices (foreignKeys=true
                // in .env), but delete them explicitly so the outcome never
                // depends on the SQLite pragma.
                $db->query('DELETE FROM faturalar WHERE firma_id = ?', [$fid]);
                $db->query('DELETE FROM firmalar WHERE id = ?', [$fid]);
                $flash = 'Firma ve bağlı faturaları silindi.';
            }
        }

        // ---- List + pagination ----
        $q    = trim((string) $this->request->getGet('q'));
        $page = max(1, (int) $this->request->getGet('page'));
        $per  = 10;

        $wsql = '';
        $args = [];
        if ($q !== '') {
            $wsql   = 'WHERE f.ad LIKE ?';
            $args[] = '%' . $q . '%';
        }

        $total  = (int) $db->query("SELECT COUNT(*) AS cnt FROM firmalar f {$wsql}", $args)->getRowArray()['cnt'];
        $pages  = (int) max(1, ceil($total / $per));
        $page   = min($page, $pages);
        $offset = ($page - 1) * $per;

        $rows = $db->query(
            "SELECT f.id, f.ad,
                    COUNT(fa.id) AS adet,
                    COALESCE(SUM(fa.toplam),0) AS toplam,
                    COALESCE(SUM(CASE WHEN fa.durum='Ödenmedi' THEN fa.toplam END),0) AS kalan
               FROM firmalar f
          LEFT JOIN faturalar fa ON fa.firma_id = f.id
               {$wsql}
           GROUP BY f.id, f.ad
           ORDER BY f.ad
              LIMIT {$per} OFFSET {$offset}",
            $args
        )->getResultArray();

        return view('fatura/companies', [
            'title'  => 'Firmalar',
            'active' => 'fatura.companies',
            'flash'  => $flash,
            'err'    => $err,
            'q'      => $q,
            'total'  => $total,
            'rows'   => $rows,
            'page'   => $page,
            'pages'  => $pages,
        ]);
    }

    /** GET|POST /fatura/import — one-shot JSON import of the legacy dataset. */
    public function import(): string
    {
        $db = $this->db();

        // Cleaned dataset shipped with the standalone app (read-only source).
        $dataFile = ROOTPATH . 'apps/fatura/data/faturalar.json';
        $kayitlar = is_file($dataFile) ? json_decode((string) file_get_contents($dataFile), true) : null;

        $sonuc = null;
        $err   = '';

        if ($this->request->is('post') && $this->request->getPost('onayla') !== null) {
            if (! is_array($kayitlar)) {
                $err = 'data/faturalar.json bulunamadı veya bozuk.';
            } else {
                $temizle = $this->request->getPost('temizle') !== null;
                $db->transBegin();

                try {
                    if ($temizle) {
                        $db->query('DELETE FROM faturalar');
                        $db->query('DELETE FROM firmalar');
                    }

                    // Companies (de-duplicated by name).
                    $firmaMap  = [];
                    $yeniFirma = 0;
                    foreach ($kayitlar as $r) {
                        $ad = $r['firma'];
                        if (isset($firmaMap[$ad])) {
                            continue;
                        }
                        $row = $db->query('SELECT id FROM firmalar WHERE ad = ?', [$ad])->getRowArray();
                        $fid = (int) ($row['id'] ?? 0);
                        if (! $fid) {
                            $db->query('INSERT INTO firmalar (ad) VALUES (?)', [$ad]);
                            $fid = (int) $db->insertID();
                            $yeniFirma++;
                        }
                        $firmaMap[$ad] = $fid;
                    }

                    // Invoices.
                    $n = 0;
                    foreach ($kayitlar as $r) {
                        $db->query(
                            'INSERT INTO faturalar
                             (firma_id, fatura_tarihi, fatura_no, tur, tutar, kdv_orani, kdv_tutari, toplam, odeme_sekli, odeme_tarihi, durum, aciklama)
                             VALUES (?,?,?,?,?,?,?,?,?,?,?,?)',
                            [
                                $firmaMap[$r['firma']],
                                $r['fatura_tarihi'] ?: null,
                                $r['fatura_no'] ?: null,
                                in_array($r['tur'], ['Gelen', 'Giden'], true) ? $r['tur'] : 'Gelen',
                                $r['tutar'], $r['kdv_orani'], $r['kdv_tutari'], $r['toplam'],
                                $r['odeme_sekli'] ?: null,
                                $r['odeme_tarihi'] ?: null,
                                in_array($r['durum'], ['Ödendi', 'Ödenmedi'], true) ? $r['durum'] : 'Ödenmedi',
                                $r['aciklama'] ?: null,
                            ]
                        );
                        $n++;
                    }
                    $db->transCommit();
                    $sonuc = ['fatura' => $n, 'firma' => count($firmaMap), 'yeni_firma' => $yeniFirma, 'temizlendi' => $temizle];
                } catch (\Throwable $ex) {
                    $db->transRollback();
                    $err = 'İçe aktarma hatası: ' . $ex->getMessage();
                }
            }
        }

        // Preview statistics.
        $mevcut  = (int) $db->query('SELECT COUNT(*) AS cnt FROM faturalar')->getRowArray()['cnt'];
        $veriVar = is_array($kayitlar);
        $onAdet  = $veriVar ? count($kayitlar) : 0;
        $onFirma = 0;
        $onToplam = $onOdenen = $onKalan = 0.0;
        if ($veriVar) {
            $f = [];
            foreach ($kayitlar as $r) {
                $f[$r['firma']] = 1;
                $onToplam += $r['toplam'];
                if ($r['durum'] === 'Ödendi') {
                    $onOdenen += $r['toplam'];
                } else {
                    $onKalan += $r['toplam'];
                }
            }
            $onFirma = count($f);
        }

        return view('fatura/import', [
            'title'    => 'Verileri İçe Aktar',
            'active'   => '',
            'err'      => $err,
            'sonuc'    => $sonuc,
            'veriVar'  => $veriVar,
            'mevcut'   => $mevcut,
            'onAdet'   => $onAdet,
            'onFirma'  => $onFirma,
            'onToplam' => $onToplam,
            'onOdenen' => $onOdenen,
            'onKalan'  => $onKalan,
        ]);
    }

    /**
     * GET /fatura/export — styled XLSX download.
     * ?firma=ID → single company sheet; otherwise GENEL ÖZET + one sheet per company.
     */
    public function export(): DownloadResponse|RedirectResponse
    {
        $db      = $this->db();
        $firmaId = (int) $this->request->getGet('firma');

        $fSql     = 'SELECT id, ad FROM firmalar' . ($firmaId ? ' WHERE id = ?' : '') . ' ORDER BY ad';
        $firmalar = $db->query($fSql, $firmaId ? [$firmaId] : [])->getResultArray();

        if (! $firmalar) {
            return redirect()->to(route_to('fatura.invoices'));
        }

        $xlsx = new XlsxWriter();

        if (! $firmaId && count($firmalar) > 1) {
            // ---- GENEL ÖZET sheet ----
            $ozet = $db->query(
                "SELECT f.ad,
                        COUNT(fa.id) adet,
                        COALESCE(SUM(fa.toplam),0) toplam,
                        COALESCE(SUM(CASE WHEN fa.durum='Ödendi'   THEN fa.toplam END),0) odenen,
                        COALESCE(SUM(CASE WHEN fa.durum='Ödenmedi' THEN fa.toplam END),0) kalan
                   FROM firmalar f JOIN faturalar fa ON fa.firma_id=f.id
               GROUP BY f.id, f.ad ORDER BY kalan DESC, f.ad"
            )->getResultArray();

            $rows = [];
            $r1   = [['v' => 'GENEL ÖZET — FİRMA BAZLI', 's' => 1, 't' => 's']];
            for ($i = 1; $i < 5; $i++) {
                $r1[] = ['v' => '', 's' => 1, 't' => 's'];
            }
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
            $gT = $gO = $gK = 0.0;
            $gA = 0;
            foreach ($ozet as $i => $o) {
                $z      = ($i % 2 === 1);
                $sTxt   = $z ? 4 : 3;
                $sMon   = $z ? 6 : 5;
                $sMid   = $z ? 8 : 7;
                $rows[] = [
                    ['v' => $o['ad'], 's' => $sTxt, 't' => 's'],
                    ['v' => (int) $o['adet'], 's' => $sMid, 't' => 'n'],
                    ['v' => (float) $o['toplam'], 's' => $sMon, 't' => 'n'],
                    ['v' => (float) $o['odenen'], 's' => $sMon, 't' => 'n'],
                    ['v' => (float) $o['kalan'], 's' => $sMon, 't' => 'n'],
                ];
                $gT += (float) $o['toplam'];
                $gO += (float) $o['odenen'];
                $gK += (float) $o['kalan'];
                $gA += (int) $o['adet'];
            }
            // Grand total row.
            $rows[] = [
                ['v' => 'GENEL TOPLAM', 's' => 9, 't' => 's'],
                ['v' => $gA, 's' => 10, 't' => 'n'],
                ['v' => round($gT, 2), 's' => 10, 't' => 'n'],
                ['v' => round($gO, 2), 's' => 10, 't' => 'n'],
                ['v' => round($gK, 2), 's' => 11, 't' => 'n'],
            ];
            $xlsx->addSheet('GENEL ÖZET', $rows, ['A1:E1'], [1 => 34, 2 => 14, 3 => 16, 4 => 16, 5 => 16]);
        }

        // One sheet per company.
        foreach ($firmalar as $firma) {
            [$rows, $merges, $cols] = $this->buildCompanySheet($firma);
            $xlsx->addSheet($firma['ad'], $rows, $merges, $cols);
        }

        $ad = $firmaId
            ? 'Fatura_' . preg_replace('/[^A-Za-z0-9]+/', '_', $firmalar[0]['ad'])
            : 'Fatura_Takip_GENEL_OZET';

        // Third argument resolves the XLSX mime type from the file extension.
        return $this->response->download($ad . '_' . date('Y-m-d') . '.xlsx', $xlsx->content(), true);
    }

    /**
     * Builds the [rows, merges, colWidths] triple of a single company sheet.
     *
     * @return array{0: array, 1: array, 2: array}
     */
    private function buildCompanySheet(array $firma): array
    {
        $baslik   = 'AYLIK FATURA VE ÖDEME TAKİP TABLOSU';
        $kollar   = ['Fatura Tarihi', 'Fatura No', 'Tür', 'Tutar', 'KDV %', 'KDV Tutarı', 'Genel Toplam', 'Ödeme Şekli', 'Ödeme Tarihi', 'Durum'];
        $genislik = [1 => 13, 2 => 22, 3 => 9, 4 => 14, 5 => 8, 6 => 14, 7 => 16, 8 => 15, 9 => 13, 10 => 11];

        $list = $this->db()->query(
            "SELECT fatura_tarihi, fatura_no, tur, tutar, kdv_orani, kdv_tutari, toplam,
                    odeme_sekli, odeme_tarihi, durum
               FROM faturalar WHERE firma_id = ?
           ORDER BY (durum='Ödenmedi') DESC, fatura_tarihi, id",
            [$firma['id']]
        )->getResultArray();

        $rows = [];
        // 1) title strip (A1:J1 merged)
        $r1 = [['v' => $baslik, 's' => 1, 't' => 's']];
        for ($i = 1; $i < 10; $i++) {
            $r1[] = ['v' => '', 's' => 1, 't' => 's'];
        }
        $rows[] = $r1;
        // 2) company subtitle
        $rows[] = [['v' => 'Firma: ' . $firma['ad'], 's' => 12, 't' => 's']];
        // 3) blank
        $rows[] = [];
        // 4) table header
        $hdr = [];
        foreach ($kollar as $k) {
            $hdr[] = ['v' => $k, 's' => 2, 't' => 's'];
        }
        $rows[] = $hdr;

        // 5) data rows (zebra striped)
        $toplam = $odenen = $kalan = 0.0;
        foreach ($list as $i => $f) {
            $z      = ($i % 2 === 1); // odd rows get the zebra fill
            $sTxt   = $z ? 4 : 3;
            $sMon   = $z ? 6 : 5;
            $sMid   = $z ? 8 : 7;
            $rows[] = [
                ['v' => $this->excelDate($f['fatura_tarihi']), 's' => $sMid, 't' => 's'],
                ['v' => (string) ($f['fatura_no'] ?? ''), 's' => $sTxt, 't' => 's'],
                ['v' => $f['tur'], 's' => $sMid, 't' => 's'],
                ['v' => (float) $f['tutar'], 's' => $sMon, 't' => 'n'],
                ['v' => (float) $f['kdv_orani'], 's' => $sMid, 't' => 'n'],
                ['v' => (float) $f['kdv_tutari'], 's' => $sMon, 't' => 'n'],
                ['v' => (float) $f['toplam'], 's' => $sMon, 't' => 'n'],
                ['v' => (string) ($f['odeme_sekli'] ?? ''), 's' => $sTxt, 't' => 's'],
                ['v' => $this->excelDate($f['odeme_tarihi']), 's' => $sMid, 't' => 's'],
                ['v' => $f['durum'], 's' => $sMid, 't' => 's'],
            ];
            $toplam += (float) $f['toplam'];
            if ($f['durum'] === 'Ödendi') {
                $odenen += (float) $f['toplam'];
            } else {
                $kalan += (float) $f['toplam'];
            }
        }

        // 6) blank row + FİNANSAL ÖZET box (columns F:G)
        $rows[] = [];
        $box    = static function (string $label, $val, int $valStyle): array {
            $r    = array_fill(0, 5, null);                        // A-E empty
            $r[5] = ['v' => $label, 's' => 9, 't' => 's'];         // F
            $r[6] = ['v' => $val, 's' => $valStyle, 't' => 'n'];   // G

            return $r;
        };
        $hd     = array_fill(0, 5, null);
        $hd[5]  = ['v' => 'FİNANSAL ÖZET', 's' => 2, 't' => 's'];
        $hd[6]  = ['v' => '', 's' => 2, 't' => 's'];
        $rows[] = $hd;
        $rows[] = $box('Toplam Tutar', round($toplam, 2), 10);
        $rows[] = $box('Ödenen', round($odenen, 2), 10);
        $rows[] = $box('Kalan Borç', round($kalan, 2), 11);
        // Invoice count (plain number, not money-formatted).
        $rc     = array_fill(0, 5, null);
        $rc[5]  = ['v' => 'Fatura Adedi', 's' => 9, 't' => 's'];
        $rc[6]  = ['v' => count($list), 's' => 10, 't' => 'n'];
        $rows[] = $rc;

        return [$rows, ['A1:J1'], $genislik];
    }

    /** Formats a date as dd.mm.yyyy for export cells; empty string when unset. */
    private function excelDate($d): string
    {
        if (! $d) {
            return '';
        }
        $t = strtotime((string) $d);

        return $t ? date('d.m.Y', $t) : (string) $d;
    }
}
