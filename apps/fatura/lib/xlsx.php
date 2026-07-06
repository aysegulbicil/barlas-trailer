<?php
/**
 * Saf PHP XLSX yazıcı — harici kütüphane VE php_zip eklentisi GEREKTİRMEZ.
 * ZIP arşivi "store" (sıkıştırmasız) yöntemiyle elle kurulur.
 *
 * Hücre stil indeksleri (styles.xml ile birebir):
 *   0  varsayılan
 *   1  başlık şeridi (beyaz kalın büyük yazı, lacivert zemin, ortalı)
 *   2  tablo başlığı (beyaz kalın, lacivert zemin, ortalı, kenarlık)
 *   3  metin            4  metin (zebra)
 *   5  para #,##0.00    6  para (zebra)
 *   7  ortalı metin     8  ortalı metin (zebra)
 *   9  özet etiketi (kalın)   10 özet para (kalın)   11 özet vurgulu (kalın kırmızı)
 *   12 alt başlık (gri italik)
 */

class XlsxWriter
{
    /** @var array<int,array> her sayfa: ['name'=>..,'rows'=>..,'merges'=>..,'cols'=>..] */
    private array $sheets = [];

    /**
     * @param string $name   Sayfa adı (otomatik temizlenir/benzersizleştirilir)
     * @param array  $rows   Satır dizisi; her hücre ['v'=>deger,'s'=>stil,'t'=>'n'|'s'] ya da null
     * @param array  $merges ["A1:F1", ...]
     * @param array  $cols   1 tabanlı sütun => genişlik
     */
    public function addSheet(string $name, array $rows, array $merges = [], array $cols = []): void
    {
        $this->sheets[] = [
            'name'   => $this->safeSheetName($name),
            'rows'   => $rows,
            'merges' => $merges,
            'cols'   => $cols,
        ];
    }

    public function download(string $filename): void
    {
        $data = $this->build();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($data));
        header('Cache-Control: max-age=0');
        echo $data;
    }

    public function save(string $path): void
    {
        file_put_contents($path, $this->build());
    }

    // ---------------------------------------------------------------- helpers

    private function safeSheetName(string $name): string
    {
        $name = str_replace(['\\', '/', '*', '?', ':', '[', ']'], ' ', $name);
        $name = trim(preg_replace('/\s+/u', ' ', $name));
        if ($name === '') $name = 'Sayfa';
        if (mb_strlen($name) > 31) $name = mb_substr($name, 0, 31);

        // benzersizleştir
        $base = $name; $i = 2;
        $used = array_map(fn($s) => mb_strtolower($s['name']), $this->sheets);
        while (in_array(mb_strtolower($name), $used, true)) {
            $suffix = ' (' . $i . ')';
            $name = mb_substr($base, 0, 31 - mb_strlen($suffix)) . $suffix;
            $i++;
        }
        return $name;
    }

    private static function colLetter(int $c): string // 1 -> A
    {
        $s = '';
        while ($c > 0) { $m = ($c - 1) % 26; $s = chr(65 + $m) . $s; $c = intdiv($c - 1 - $m, 26); }
        return $s;
    }

    private static function xml(string $s): string
    {
        return htmlspecialchars($s, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    // ---------------------------------------------------------------- build

    private function build(): string
    {
        $files = [];
        $files['[Content_Types].xml']      = $this->contentTypes();
        $files['_rels/.rels']              = $this->rootRels();
        $files['xl/workbook.xml']          = $this->workbook();
        $files['xl/_rels/workbook.xml.rels'] = $this->workbookRels();
        $files['xl/styles.xml']            = $this->styles();
        foreach ($this->sheets as $i => $s) {
            $files['xl/worksheets/sheet' . ($i + 1) . '.xml'] = $this->sheetXml($s);
        }
        return $this->zip($files);
    }

    private function contentTypes(): string
    {
        $over = '';
        foreach ($this->sheets as $i => $s) {
            $over .= '<Override PartName="/xl/worksheets/sheet' . ($i + 1)
                  . '.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
        }
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . $over . '</Types>';
    }

    private function rootRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';
    }

    private function workbook(): string
    {
        $sheets = '';
        foreach ($this->sheets as $i => $s) {
            $sheets .= '<sheet name="' . self::xml($s['name']) . '" sheetId="' . ($i + 1)
                    . '" r:id="rId' . ($i + 1) . '"/>';
        }
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
            . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets>' . $sheets . '</sheets></workbook>';
    }

    private function workbookRels(): string
    {
        $rel = '';
        foreach ($this->sheets as $i => $s) {
            $rel .= '<Relationship Id="rId' . ($i + 1)
                 . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" '
                 . 'Target="worksheets/sheet' . ($i + 1) . '.xml"/>';
        }
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . $rel . '</Relationships>';
    }

    private function styles(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
          . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
          . '<numFmts count="1"><numFmt numFmtId="164" formatCode="#,##0.00"/></numFmts>'
          . '<fonts count="5">'
            . '<font><sz val="11"/><name val="Calibri"/></font>'                                    // 0
            . '<font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font>'          // 1 beyaz kalın
            . '<font><b/><sz val="11"/><name val="Calibri"/></font>'                                  // 2 kalın
            . '<font><b/><sz val="11"/><color rgb="FFC00000"/><name val="Calibri"/></font>'           // 3 kalın kırmızı
            . '<font><b/><sz val="16"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font>'           // 4 başlık
          . '</fonts>'
          . '<fills count="5">'
            . '<fill><patternFill patternType="none"/></fill>'                                         // 0
            . '<fill><patternFill patternType="gray125"/></fill>'                                      // 1
            . '<fill><patternFill patternType="solid"><fgColor rgb="FF1F4E78"/></patternFill></fill>'  // 2 lacivert
            . '<fill><patternFill patternType="solid"><fgColor rgb="FFF2F4F8"/></patternFill></fill>'  // 3 zebra
            . '<fill><patternFill patternType="solid"><fgColor rgb="FFDCE6F1"/></patternFill></fill>'  // 4 özet zemin
          . '</fills>'
          . '<borders count="2">'
            . '<border><left/><right/><top/><bottom/><diagonal/></border>'                             // 0
            . '<border>'                                                                               // 1 ince gri
              . '<left style="thin"><color rgb="FFD9D9D9"/></left>'
              . '<right style="thin"><color rgb="FFD9D9D9"/></right>'
              . '<top style="thin"><color rgb="FFD9D9D9"/></top>'
              . '<bottom style="thin"><color rgb="FFD9D9D9"/></bottom><diagonal/>'
            . '</border>'
          . '</borders>'
          . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
          . '<cellXfs count="13">'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'                                                                   // 0
            . '<xf numFmtId="0" fontId="4" fillId="2" borderId="0" xfId="0" applyFont="1" applyFill="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>' // 1 başlık
            . '<xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>' // 2 th
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>'   // 3 metin
            . '<xf numFmtId="0" fontId="0" fillId="3" borderId="1" xfId="0" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>' // 4 metin zebra
            . '<xf numFmtId="164" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>'  // 5 para
            . '<xf numFmtId="164" fontId="0" fillId="3" borderId="1" xfId="0" applyNumberFormat="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>' // 6 para zebra
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>' // 7 ortalı
            . '<xf numFmtId="0" fontId="0" fillId="3" borderId="1" xfId="0" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>' // 8 ortalı zebra
            . '<xf numFmtId="0" fontId="2" fillId="4" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>' // 9 özet etiket
            . '<xf numFmtId="164" fontId="2" fillId="4" borderId="1" xfId="0" applyNumberFormat="1" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>' // 10 özet para
            . '<xf numFmtId="164" fontId="3" fillId="4" borderId="1" xfId="0" applyNumberFormat="1" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>' // 11 özet vurgu
            . '<xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>' // 12 alt başlık
          . '</cellXfs>'
          . '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
          . '</styleSheet>';
    }

    private function sheetXml(array $s): string
    {
        // sütun genişlikleri
        $colsXml = '';
        if ($s['cols']) {
            $colsXml = '<cols>';
            foreach ($s['cols'] as $idx => $w) {
                $colsXml .= '<col min="' . $idx . '" max="' . $idx . '" width="' . $w . '" customWidth="1"/>';
            }
            $colsXml .= '</cols>';
        }

        $rowsXml = '';
        $rn = 0;
        foreach ($s['rows'] as $row) {
            $rn++;
            $cellsXml = '';
            $cn = 0;
            foreach ($row as $cell) {
                $cn++;
                if ($cell === null) continue;
                $ref = self::colLetter($cn) . $rn;
                $st  = (int) ($cell['s'] ?? 0);
                $t   = $cell['t'] ?? 's';
                $v   = $cell['v'];
                if ($t === 'n') {
                    $cellsXml .= '<c r="' . $ref . '" s="' . $st . '"><v>' . (is_numeric($v) ? $v + 0 : 0) . '</v></c>';
                } else {
                    $cellsXml .= '<c r="' . $ref . '" s="' . $st . '" t="inlineStr"><is><t xml:space="preserve">'
                              . self::xml((string) $v) . '</t></is></c>';
                }
            }
            $rowsXml .= '<row r="' . $rn . '">' . $cellsXml . '</row>';
        }

        $mergeXml = '';
        if ($s['merges']) {
            $mergeXml = '<mergeCells count="' . count($s['merges']) . '">';
            foreach ($s['merges'] as $m) $mergeXml .= '<mergeCell ref="' . $m . '"/>';
            $mergeXml .= '</mergeCells>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . $colsXml
            . '<sheetData>' . $rowsXml . '</sheetData>'
            . $mergeXml
            . '</worksheet>';
    }

    // ---------------------------------------------------------------- zip (store)

    /** @param array<string,string> $files yol => içerik */
    private function zip(array $files): string
    {
        $local = '';
        $central = '';
        $offset = 0;

        foreach ($files as $name => $data) {
            $crc  = crc32($data);
            $len  = strlen($data);
            $nlen = strlen($name);

            // yerel başlık
            $lh  = pack('V', 0x04034b50)
                 . pack('v', 20)        // sürüm
                 . pack('v', 0)         // bayraklar
                 . pack('v', 0)         // yöntem: store
                 . pack('v', 0)         // saat
                 . pack('v', 0x21)      // tarih (1980-01-01)
                 . pack('V', $crc)
                 . pack('V', $len)      // sıkıştırılmış boy
                 . pack('V', $len)      // gerçek boy
                 . pack('v', $nlen)
                 . pack('v', 0)         // ek alan
                 . $name;
            $local .= $lh . $data;

            // merkezi dizin
            $central .= pack('V', 0x02014b50)
                 . pack('v', 20)        // yapan sürüm
                 . pack('v', 20)        // gerekli sürüm
                 . pack('v', 0)
                 . pack('v', 0)
                 . pack('v', 0)
                 . pack('v', 0x21)
                 . pack('V', $crc)
                 . pack('V', $len)
                 . pack('V', $len)
                 . pack('v', $nlen)
                 . pack('v', 0)         // ek
                 . pack('v', 0)         // yorum
                 . pack('v', 0)         // disk
                 . pack('v', 0)         // iç öznitelik
                 . pack('V', 0)         // dış öznitelik
                 . pack('V', $offset)   // yerel başlık konumu
                 . $name;

            $offset += strlen($lh) + $len;
        }

        $cdSize = strlen($central);
        $eocd = pack('V', 0x06054b50)
              . pack('v', 0)
              . pack('v', 0)
              . pack('v', count($files))
              . pack('v', count($files))
              . pack('V', $cdSize)
              . pack('V', $offset)
              . pack('v', 0);

        return $local . $central . $eocd;
    }
}
