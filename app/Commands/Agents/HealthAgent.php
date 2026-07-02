<?php

namespace App\Commands\Agents;

/**
 * Nöbetçi — site sağlığı ajanı (§9): kritik sayfaların HTTP durumunu,
 * writable yazılabilirliğini ve disk doluluğunu denetler; sorun bulursa
 * dikkat kuyruğuna (writable/data/alerts) alarm bırakır. Dış uptime
 * izleyicisinin (UptimeRobot vb.) iç tamamlayıcısıdır — VPS'in kendisi
 * çökerse dış göz devreye girer.
 *
 * cron önerisi (yarım saatte bir):
 *   0,30 * * * *  cd /var/www/html && php spark agents:health
 */
class HealthAgent extends AgentCommand
{
    protected $name        = 'agents:health';
    protected $description = 'Kritik sayfaları ve sunucu sağlığını denetler (Nöbetçi).';

    /** Konteyner/VPS içinden web sunucusuna doğrudan istek atılır. */
    private const BASE = 'http://127.0.0.1';

    private const PAGES = ['/tr', '/tr/products', '/tr/tools', '/tr/faq', '/tr/games', '/tr/kvkk', '/sitemap.xml', '/robots.txt'];

    protected function agentName(): string
    {
        return 'health';
    }

    protected function work(): array
    {
        $fails = [];
        $codes = [];

        foreach (self::PAGES as $page) {
            $code         = $this->statusOf(self::BASE . $page);
            $codes[$page] = $code;
            if ($code !== 200) {
                $fails[] = $page . ' → ' . $code;
            }
        }

        if (! is_writable(WRITEPATH)) {
            $fails[] = 'writable/ yazılamıyor';
        }

        $free    = @disk_free_space(WRITEPATH);
        $total   = @disk_total_space(WRITEPATH);
        $freePct = ($free !== false && $total) ? (int) round($free / $total * 100) : null;
        if ($freePct !== null && $freePct < 10) {
            $fails[] = 'disk boş alan %' . $freePct;
        }

        if ($fails !== []) {
            $this->alert('health-check', implode(' | ', $fails));
        }

        return [
            'pages'     => $codes,
            'disk_free' => $freePct === null ? '?' : $freePct . '%',
            'status'    => $fails === [] ? 'saglikli' : 'SORUN: ' . implode(' | ', $fails),
        ];
    }

    private function statusOf(string $url): int
    {
        // GET kullanılır: CI4'ün get() rotaları HEAD isteğini eşleştirmiyor
        // (HEAD ile tüm sayfalar 404 döner — doğrulandı).
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT      => 'BarlasAgent/1.0 (health)',
        ]);
        curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        return $code;
    }
}
