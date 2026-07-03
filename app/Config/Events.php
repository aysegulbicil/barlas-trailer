<?php

namespace Config;

use CodeIgniter\Events\Events;
use CodeIgniter\Exceptions\FrameworkException;
use CodeIgniter\HotReloader\HotReloader;

/*
 * --------------------------------------------------------------------
 * Application Events
 * --------------------------------------------------------------------
 * Events allow you to tap into the execution of the program without
 * modifying or extending core files. This file provides a central
 * location to define your events, though they can always be added
 * at run-time, also, if needed.
 *
 * You create code that can execute by subscribing to events with
 * the 'on()' method. This accepts any form of callable, including
 * Closures, that will be executed when the event is triggered.
 *
 * Example:
 *      Events::on('create', [$myInstance, 'myMethod']);
 */

Events::on('pre_system', static function (): void {
    if (ENVIRONMENT !== 'testing') {
        $value = ini_get('zlib.output_compression');

        if (filter_var($value, FILTER_VALIDATE_BOOLEAN) || (int) $value > 0) {
            throw FrameworkException::forEnabledZlibOutputCompression();
        }

        while (ob_get_level() > 0) {
            ob_end_flush();
        }

        ob_start(static fn ($buffer) => $buffer);
    }

    /*
     * --------------------------------------------------------------------
     * Debug Toolbar Listeners.
     * --------------------------------------------------------------------
     * If you delete, they will no longer be collected.
     */
    if (CI_DEBUG && ! is_cli()) {
        Events::on('DBQuery', 'CodeIgniter\Debug\Toolbar\Collectors\Database::collect');
        service('toolbar')->respond();
        // Hot Reload route - for framework use on the hot reloader.
        if (ENVIRONMENT === 'development') {
            service('routes')->get('__hot-reload', static function (): void {
                (new HotReloader())->run();
            });
        }
    }

    /*
     * --------------------------------------------------------------------
     * Referans logo klasörü bekçisi (pre_system → sayfa önbelleğinden ÖNCE)
     * --------------------------------------------------------------------
     * public/assets/images/refs/ içindeki her değişiklikte (ekle/sil/yeniden
     * adlandır/üzerine yaz) sayfa önbelleği otomatik temizlenir → logo bandı
     * elle "cache:clear" gerektirmeden HEP güncel kalır. Maliyet: istek başına
     * bir scandir + dosya başına bir stat (≈1 ms). İmza damgası cache
     * klasörünün DIŞINDA tutulur ki clean() onu silmesin.
     */
    if (! is_cli()) {
        $refsDir = FCPATH . 'assets/images/refs';
        if (is_dir($refsDir)) {
            $sig = [];
            foreach (scandir($refsDir) ?: [] as $f) {
                if ($f === '.' || $f === '..') continue;
                $sig[] = $f . ':' . (string) @filemtime($refsDir . DIRECTORY_SEPARATOR . $f);
            }
            $signature = md5(implode('|', $sig));
            $stampFile = WRITEPATH . 'data/refs-dir.stamp';
            $known     = is_file($stampFile) ? (string) file_get_contents($stampFile) : '';
            if ($signature !== $known) {
                try { cache()->clean(); } catch (\Throwable $e) { /* önbellek yoksa sorun değil */ }
                @file_put_contents($stampFile, $signature);
            }
        }
    }
});
