<?php

namespace App\Commands\Agents;

/**
 * Analist ajanı (§9) — ziyaretçi davranışı ve dönüşüm içgörüleri, saf kod.
 *
 * Dosya tabanlı akışları (writable/{leads,quotes,ai-logs}) okur; son 7 günü
 * önceki 7 günle karşılaştırır: hacim trendi, dil dağılımı, en çok sorulan
 * ürünler. Rapor writable/data/agents/analyst/insights.json'a yazılır ve
 * özet, dikkat kuyruğuna düşer — Kahya (digest) sabah brifingine taşır.
 * Harici analytics servisi YOK (ekonomik mimari + KVKK sadeliği).
 *
 * cron önerisi (her Pazartesi 07:00):
 *   0 7 * * 1  cd /var/www/html && php spark agents:analyst
 */
class AnalystAgent extends AgentCommand
{
    protected $name        = 'agents:analyst';
    protected $description = 'Lead/teklif/asistan akışlarından haftalık davranış ve dönüşüm içgörüleri çıkarır.';

    protected function agentName(): string
    {
        return 'analyst';
    }

    protected function work(): array
    {
        $now      = time();
        $thisWeek = static fn (int $t): bool => $t >= strtotime('-7 days');
        $lastWeek = static fn (int $t): bool => $t >= strtotime('-14 days') && $t < strtotime('-7 days');

        $streams = [
            'leads'  => $this->rows('leads'),
            'quotes' => $this->rows('quotes'),
            'asks'   => $this->rows('ai-logs'),
        ];

        $counts = [];
        foreach ($streams as $key => $rows) {
            $counts[$key] = [
                'this_week' => count(array_filter($rows, static fn (array $r): bool => $thisWeek(strtotime((string) ($r['time'] ?? '')) ?: 0))),
                'last_week' => count(array_filter($rows, static fn (array $r): bool => $lastWeek(strtotime((string) ($r['time'] ?? '')) ?: 0))),
            ];
        }

        // Asistan sorularında dil dağılımı ve teklifte en çok geçen ürünler (7 gün).
        $locales  = [];
        $products = [];

        foreach ($streams['asks'] as $row) {
            if ($thisWeek(strtotime((string) ($row['time'] ?? '')) ?: 0)) {
                $locale           = (string) ($row['locale'] ?? '?');
                $locales[$locale] = ($locales[$locale] ?? 0) + 1;
            }
        }

        foreach ($streams['quotes'] as $row) {
            if ($thisWeek(strtotime((string) ($row['time'] ?? '')) ?: 0)) {
                $name            = (string) ($row['name'] ?? '?');
                $products[$name] = ($products[$name] ?? 0) + 1;
            }
        }

        arsort($locales);
        arsort($products);

        $report = [
            'updated'      => date('c'),
            'window_days'  => 7,
            'counts'       => $counts,
            'ask_locales'  => $locales,
            'top_products' => array_slice($products, 0, 5, true),
        ];

        $dir = WRITEPATH . 'data/agents/analyst';
        if (! is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        @file_put_contents(
            $dir . '/insights.json',
            json_encode($report, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n",
            LOCK_EX
        );

        // Okunur özet → dikkat kuyruğu (Kahya brifinge taşır).
        $trend = static function (array $c): string {
            if ($c['last_week'] === 0) {
                return $c['this_week'] > 0 ? '(yeni)' : '(hareket yok)';
            }
            $pct = (int) round((($c['this_week'] - $c['last_week']) / $c['last_week']) * 100);

            return ($pct >= 0 ? '+' : '') . $pct . '%';
        };

        $topProduct = array_key_first($report['top_products']) ?? '—';

        $this->alert('weekly-insights', sprintf(
            'Haftalık analiz: %d lead %s, %d teklif niyeti %s, %d asistan sorusu %s. En çok teklif istenen: %s.',
            $counts['leads']['this_week'],
            $trend($counts['leads']),
            $counts['quotes']['this_week'],
            $trend($counts['quotes']),
            $counts['asks']['this_week'],
            $trend($counts['asks']),
            $topProduct
        ));

        return [
            'leads_7d'    => $counts['leads']['this_week'],
            'quotes_7d'   => $counts['quotes']['this_week'],
            'asks_7d'     => $counts['asks']['this_week'],
            'top_product' => $topProduct,
        ];
    }

    /**
     * Bu ay + geçen ayın JSONL satırlarını okur (14 günlük pencere iki
     * ay dosyasına yayılabilir).
     *
     * @return list<array<string, mixed>>
     */
    private function rows(string $stream): array
    {
        $rows = [];

        foreach ([date('Y-m'), date('Y-m', strtotime('first day of last month'))] as $month) {
            $file = WRITEPATH . $stream . '/' . $month . '.jsonl';
            if (! is_file($file)) {
                continue;
            }
            foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
                $row = json_decode($line, true);
                if (is_array($row)) {
                    $rows[] = $row;
                }
            }
        }

        return $rows;
    }
}
