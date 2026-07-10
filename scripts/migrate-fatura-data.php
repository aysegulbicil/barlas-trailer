<?php

/**
 * One-shot data migration for the embedded /fatura tool:
 * MariaDB `fatura_takip` (apps/fatura legacy DB) → main SQLite database.
 *
 * Run inside the barlas-apache container as www-data (after `spark migrate`):
 *   docker exec -u www-data barlas-apache php /var/www/html/scripts/migrate-fatura-data.php
 *
 * Idempotent: exits early ("already migrated, skipping") when the target
 * tables already contain rows. Row ids are copied verbatim so
 * faturalar.firma_id references stay intact; sqlite_sequence follows the
 * max id automatically on explicit-id inserts.
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "CLI only.\n");
    exit(1);
}

// ---- Connection settings (compose sets FATURA_DB_* on the web service) ----
$mysqlHost = getenv('FATURA_DB_HOST') ?: 'db';
$mysqlPort = getenv('FATURA_DB_PORT') ?: '3306';
$mysqlName = getenv('FATURA_DB_NAME') ?: 'fatura_takip';
$mysqlUser = getenv('FATURA_DB_USER') ?: 'apps';
$mysqlPass = getenv('FATURA_DB_PASS') ?: 'apps_pass';

$sqlitePath = dirname(__DIR__) . '/writable/db/barlas.sqlite';

$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $src = new PDO(
        "mysql:host={$mysqlHost};port={$mysqlPort};dbname={$mysqlName};charset=utf8mb4",
        $mysqlUser,
        $mysqlPass,
        $opt
    );
} catch (PDOException $e) {
    fwrite(STDERR, "Cannot connect to source MySQL ({$mysqlHost}:{$mysqlPort}/{$mysqlName}): " . $e->getMessage() . "\n");
    exit(1);
}

if (! is_file($sqlitePath)) {
    fwrite(STDERR, "SQLite database not found: {$sqlitePath}\n");
    exit(1);
}
$dst = new PDO('sqlite:' . $sqlitePath, null, null, $opt);
$dst->exec('PRAGMA foreign_keys = ON');

// ---- Target tables must exist (created by the CreateFaturaTables migration) ----
foreach (['firmalar', 'faturalar'] as $t) {
    $exists = $dst->query(
        "SELECT name FROM sqlite_master WHERE type='table' AND name=" . $dst->quote($t)
    )->fetchColumn();
    if (! $exists) {
        fwrite(STDERR, "Target table '{$t}' missing — run `php spark migrate` first.\n");
        exit(1);
    }
}

// ---- Counts before ----
$srcFirmalar  = (int) $src->query('SELECT COUNT(*) FROM firmalar')->fetchColumn();
$srcFaturalar = (int) $src->query('SELECT COUNT(*) FROM faturalar')->fetchColumn();
$dstFirmalar  = (int) $dst->query('SELECT COUNT(*) FROM firmalar')->fetchColumn();
$dstFaturalar = (int) $dst->query('SELECT COUNT(*) FROM faturalar')->fetchColumn();

echo "Source (MySQL {$mysqlName}):  firmalar={$srcFirmalar}  faturalar={$srcFaturalar}\n";
echo "Target (SQLite):             firmalar={$dstFirmalar}  faturalar={$dstFaturalar}\n";

if ($dstFirmalar > 0 || $dstFaturalar > 0) {
    echo "Target tables already contain rows — already migrated, skipping.\n";
    exit(0);
}

// ---- Copy (single transaction, ids preserved) ----
$dst->beginTransaction();

try {
    $insFirma = $dst->prepare('INSERT INTO firmalar (id, ad, olusturma) VALUES (?, ?, ?)');
    foreach ($src->query('SELECT id, ad, olusturma FROM firmalar ORDER BY id') as $r) {
        $insFirma->execute([$r['id'], $r['ad'], $r['olusturma']]);
    }

    $insFatura = $dst->prepare(
        'INSERT INTO faturalar
         (id, firma_id, fatura_tarihi, fatura_no, tur, tutar, kdv_orani, kdv_tutari, toplam,
          odeme_sekli, odeme_tarihi, durum, aciklama, olusturma)
         VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
    );
    $sel = 'SELECT id, firma_id, fatura_tarihi, fatura_no, tur, tutar, kdv_orani, kdv_tutari, toplam,
                   odeme_sekli, odeme_tarihi, durum, aciklama, olusturma
              FROM faturalar ORDER BY id';
    foreach ($src->query($sel) as $r) {
        $insFatura->execute([
            $r['id'], $r['firma_id'], $r['fatura_tarihi'], $r['fatura_no'], $r['tur'],
            $r['tutar'], $r['kdv_orani'], $r['kdv_tutari'], $r['toplam'],
            $r['odeme_sekli'], $r['odeme_tarihi'], $r['durum'], $r['aciklama'], $r['olusturma'],
        ]);
    }

    $dst->commit();
} catch (Throwable $e) {
    $dst->rollBack();
    fwrite(STDERR, 'Migration failed, rolled back: ' . $e->getMessage() . "\n");
    exit(1);
}

// ---- Verify ----
$newFirmalar  = (int) $dst->query('SELECT COUNT(*) FROM firmalar')->fetchColumn();
$newFaturalar = (int) $dst->query('SELECT COUNT(*) FROM faturalar')->fetchColumn();

echo "Copied: firmalar {$srcFirmalar} → {$newFirmalar}, faturalar {$srcFaturalar} → {$newFaturalar}\n";

if ($newFirmalar !== $srcFirmalar || $newFaturalar !== $srcFaturalar) {
    fwrite(STDERR, "Row count mismatch after copy!\n");
    exit(1);
}

echo 'Done. Total rows migrated: ' . ($newFirmalar + $newFaturalar) . "\n";
exit(0);
