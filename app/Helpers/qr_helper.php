<?php

declare(strict_types=1);

/**
 * QR uygulaması URL yardımcıları — apps/qr standalone iken baseURL
 * ".../qr/" olduğundan tüm kod kök-göreli URL üretiyordu; gömülü düzende
 * /qr öneki tek noktadan buradan verilir (site_url('x') → qr_url('x')).
 */

if (! function_exists('qr_url')) {
    function qr_url(string $path = ''): string
    {
        $path = ltrim($path, '/');

        return site_url('qr' . ($path !== '' ? '/' . $path : ''));
    }
}
