<?php

/**
 * View helpers for the embedded invoice tracking tool (/fatura).
 * Ported from apps/fatura/db.php (para / tarih / pagination_html),
 * prefixed with fatura_ to avoid global collisions.
 */

if (! function_exists('fatura_para')) {
    /** Turkish Lira format: 1.234,56 ₺ */
    function fatura_para($n): string
    {
        return number_format((float) $n, 2, ',', '.') . ' ₺';
    }
}

if (! function_exists('fatura_tarih')) {
    /** Formats a date as dd.mm.yyyy; em dash when empty. */
    function fatura_tarih($d): string
    {
        if (! $d) {
            return '—';
        }
        $t = strtotime((string) $d);

        return $t ? date('d.m.Y', $t) : esc((string) $d);
    }
}

if (! function_exists('fatura_pagination')) {
    /**
     * Builds numbered pagination links relative to the current path.
     * $base: current query parameters (without "page").
     */
    function fatura_pagination(int $current, int $total, array $base = []): string
    {
        if ($total <= 1) {
            return '';
        }

        $url = static function (int $p) use ($base): string {
            $q = array_merge($base, ['page' => $p]);

            return '?' . http_build_query($q);
        };

        $out = '<nav class="pagination">';
        $out .= $current > 1
            ? '<a href="' . esc($url($current - 1)) . '">‹</a>'
            : '<span class="disabled">‹</span>';

        $win   = 2;
        $start = max(1, $current - $win);
        $end   = min($total, $current + $win);

        if ($start > 1) {
            $out .= '<a href="' . esc($url(1)) . '">1</a>';
            if ($start > 2) {
                $out .= '<span class="gap">…</span>';
            }
        }
        for ($p = $start; $p <= $end; $p++) {
            $out .= $p === $current
                ? '<span class="current">' . $p . '</span>'
                : '<a href="' . esc($url($p)) . '">' . $p . '</a>';
        }
        if ($end < $total) {
            if ($end < $total - 1) {
                $out .= '<span class="gap">…</span>';
            }
            $out .= '<a href="' . esc($url($total)) . '">' . $total . '</a>';
        }

        $out .= $current < $total
            ? '<a href="' . esc($url($current + 1)) . '">›</a>'
            : '<span class="disabled">›</span>';
        $out .= '</nav>';

        return $out;
    }
}
