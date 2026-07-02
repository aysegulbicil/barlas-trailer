<?php

namespace App\Commands\Agents;

use RuntimeException;

/**
 * Kur ajanı (Kahya'nın döviz görevi) — TCMB today.xml'den USD/EUR alış-satış
 * kurlarını çekip writable/data/rates.json dosyasına atomik yazar.
 * Konfigüratörün "anlık fiyat + kur korumalı gösterim" özelliği bu dosyadan
 * beslenecek; istek yolunda dış API çağrısı yapılmaz (ekonomik mimari).
 *
 * cron önerisi (hafta içi mesai saatleri, saat başı):
 *   0 7-19 * * 1-5  cd /var/www/html && php spark agents:rates
 */
class RatesAgent extends AgentCommand
{
    protected $name        = 'agents:rates';
    protected $description = 'TCMB kurlarını çekip writable/data/rates.json dosyasını günceller.';

    protected function agentName(): string
    {
        return 'rates';
    }

    protected function work(): array
    {
        $ctx = stream_context_create(['http' => ['timeout' => 15, 'user_agent' => 'BarlasAgent/1.0']]);
        $xml = @file_get_contents('https://www.tcmb.gov.tr/kurlar/today.xml', false, $ctx);

        if ($xml === false || $xml === '') {
            throw new RuntimeException('TCMB today.xml indirilemedi');
        }

        $doc = @simplexml_load_string($xml);
        if ($doc === false) {
            throw new RuntimeException('TCMB XML ayrıştırılamadı');
        }

        $rates = [];

        foreach ($doc->Currency as $cur) {
            $code = (string) $cur['CurrencyCode'];
            if (! in_array($code, ['USD', 'EUR'], true)) {
                continue;
            }

            $rates[strtolower($code)] = [
                'buy'  => (float) str_replace(',', '.', (string) $cur->ForexBuying),
                'sell' => (float) str_replace(',', '.', (string) $cur->ForexSelling),
            ];
        }

        if (! isset($rates['usd'], $rates['eur'])) {
            throw new RuntimeException('USD/EUR kuru bulunamadı');
        }

        $dir = WRITEPATH . 'data';
        if (! is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        // Atomik yazım (tmp + rename): yarım dosya asla servis edilmez.
        $out = [
            'updated' => date('c'),
            'source'  => 'tcmb-today',
            'base'    => 'TRY',
            'rates'   => $rates,
        ];
        $tmp = $dir . '/rates.json.tmp';
        file_put_contents($tmp, json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n", LOCK_EX);
        rename($tmp, $dir . '/rates.json');

        return [
            'usd_sell' => $rates['usd']['sell'],
            'eur_sell' => $rates['eur']['sell'],
        ];
    }
}
