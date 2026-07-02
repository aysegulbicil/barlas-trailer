<?php

namespace App\Commands\Agents;

/**
 * Kahya — sabah brifingi v0 (§11'in ham, LLM'siz hali).
 *
 * Dünün hareketini tek bir okunur Markdown rapora derler:
 * yeni lead'ler, teklif niyetleri, asistan soruları, bekleyen ajan
 * alarmları ve güncel kur. Rapor writable/data/briefings/YYYY-MM-DD.md
 * dosyasına yazılır; rapora giren alarmlar alerts/archive/ altına
 * taşınır (okundu sayılır). Jarvis paneli geldiğinde bu dosyalar
 * doğrudan brifing ekranının beslemesi olur.
 *
 * cron önerisi (her sabah 06:00):
 *   0 6 * * *  cd /var/www/html && php spark agents:digest
 */
class DigestAgent extends AgentCommand
{
    protected $name        = 'agents:digest';
    protected $description = 'Dünün özetini (lead/teklif/asistan/alarm/kur) sabah brifingi olarak derler.';

    protected function agentName(): string
    {
        return 'digest';
    }

    protected function work(): array
    {
        $day      = date('Y-m-d', strtotime('-1 day'));
        $leads    = $this->rowsFor(WRITEPATH . 'leads', $day);
        $quotes   = $this->rowsFor(WRITEPATH . 'quotes', $day);
        $askRows  = $this->rowsFor(WRITEPATH . 'ai-logs', $day);
        $alerts   = $this->pendingAlerts();
        $rates    = json_decode((string) @file_get_contents(WRITEPATH . 'data/rates.json'), true);

        $md = "# Sabah Brifingi — {$day}\n\n";

        $md .= "## Dünün hareketi\n";
        $md .= '- Yeni iletişim başvurusu: **' . count($leads) . "**\n";
        $md .= '- Teklif niyeti (WhatsApp akışı): **' . count($quotes) . "**\n";
        $md .= '- Asistana sorulan soru: **' . count($askRows) . "**\n\n";

        if ($leads !== []) {
            $md .= "### Lead'ler\n";
            foreach ($leads as $row) {
                $md .= '- ' . ($row['name'] ?? '?') . ' — ' . ($row['subject'] ?: mb_substr((string) ($row['message'] ?? ''), 0, 60)) . "\n";
            }
            $md .= "\n";
        }

        if ($askRows !== []) {
            $md .= "### Asistan soruları\n";
            foreach (array_slice($askRows, 0, 10) as $row) {
                $md .= '- [' . ($row['locale'] ?? '?') . '] ' . ($row['q'] ?? '') . "\n";
            }
            $md .= "\n";
        }

        $md .= "## Bekleyen alarmlar (" . count($alerts) . ")\n";
        if ($alerts === []) {
            $md .= "- Yok — sistem sakin.\n\n";
        } else {
            foreach ($alerts as $alert) {
                $md .= '- [' . ($alert['agent'] ?? '?') . '] ' . ($alert['message'] ?? '') . "\n";
            }
            $md .= "\n";
        }

        if (is_array($rates) && isset($rates['rates'])) {
            $md .= "## Kur (TCMB, {$rates['updated']})\n";
            $md .= '- USD satış: ' . ($rates['rates']['usd']['sell'] ?? '?') . "\n";
            $md .= '- EUR satış: ' . ($rates['rates']['eur']['sell'] ?? '?') . "\n\n";
        }

        $md .= "---\n_Bu rapor agents:digest tarafından otomatik üretildi._\n";

        $dir = WRITEPATH . 'data/briefings';
        if (! is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        file_put_contents($dir . '/' . $day . '.md', $md, LOCK_EX);

        $this->archiveAlerts();

        return [
            'briefing' => 'writable/data/briefings/' . $day . '.md',
            'leads'    => count($leads),
            'quotes'   => count($quotes),
            'asks'     => count($askRows),
            'alerts'   => count($alerts),
        ];
    }

    /** Aylık JSONL dosyalarından yalnız verilen güne ait satırları döndürür. */
    private function rowsFor(string $dir, string $day): array
    {
        $month = substr($day, 0, 7); // YYYY-MM
        $file  = $dir . '/' . $month . '.jsonl';
        if (! is_file($file)) {
            return [];
        }

        $rows = [];
        foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
            $row = json_decode($line, true);
            if (is_array($row) && str_starts_with((string) ($row['time'] ?? ''), $day)) {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    /** @return list<array> arşivlenmemiş alarm dosyaları (eski → yeni) */
    private function pendingAlerts(): array
    {
        $out = [];
        foreach (glob(WRITEPATH . 'data/alerts/*.json') ?: [] as $file) {
            $row = json_decode((string) file_get_contents($file), true);
            if (is_array($row)) {
                $out[] = $row;
            }
        }

        return $out;
    }

    /** Rapora giren alarmları archive/ altına taşır (okundu sayılır). */
    private function archiveAlerts(): void
    {
        $archive = WRITEPATH . 'data/alerts/archive';
        if (! is_dir($archive)) {
            @mkdir($archive, 0775, true);
        }

        foreach (glob(WRITEPATH . 'data/alerts/*.json') ?: [] as $file) {
            @rename($file, $archive . '/' . basename($file));
        }
    }
}
