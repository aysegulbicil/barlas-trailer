<?php

namespace App\Commands\Agents;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Throwable;

/**
 * Ajan ordusu taban sınıfı (ekosistem planı §9 — cron + kod).
 *
 * Ortak davranış: kill-switch bayrağı, JSONL koşu günlüğü ve dikkat/alarm
 * kuyruğu. Ajanlar VPS cron'undan spark komutu olarak çalıştırılır; örnek
 * kayıtlar her ajanın kendi docblock'undadır.
 *
 * Kill-switch (Ana Admin acil freni): writable/data/flags/ altına
 * "kill-all.flag" ya da "kill-{ajan}.flag" adlı boş bir dosya bırakılırsa
 * ajan hiçbir iş yapmadan çıkar; dosya silinince kaldığı yerden devam eder.
 */
abstract class AgentCommand extends BaseCommand
{
    protected $group = 'Agents';

    /** Ajanın kısa adı (dosya/dizin adlarında kullanılır). */
    abstract protected function agentName(): string;

    /** Asıl iş; rapor alanları döndürür. Hata fırlatabilir. */
    abstract protected function work(): array;

    public function run(array $params)
    {
        $name  = $this->agentName();
        $flags = WRITEPATH . 'data/flags/';

        if (is_file($flags . 'kill-all.flag') || is_file($flags . 'kill-' . $name . '.flag')) {
            CLI::write("[{$name}] kill-switch aktif — atlandı.", 'yellow');

            return;
        }

        $t0 = microtime(true);

        try {
            $report = $this->work();
            $ok     = true;
        } catch (Throwable $e) {
            $report = ['error' => $e->getMessage()];
            $ok     = false;
            $this->alert('run-failed', $e->getMessage());
        }

        $this->logRun($name, $ok, $report, microtime(true) - $t0);

        CLI::write(
            "[{$name}] " . ($ok ? 'tamam' : 'HATA') . ' (' . number_format((microtime(true) - $t0) * 1000) . ' ms)',
            $ok ? 'green' : 'red'
        );

        foreach ($report as $k => $v) {
            CLI::write('  ' . $k . ': ' . (is_scalar($v) ? (string) $v : json_encode($v, JSON_UNESCAPED_UNICODE)));
        }
    }

    /** Koşu kaydı: writable/data/agents/{ajan}/YYYY-MM.jsonl */
    protected function logRun(string $name, bool $ok, array $report, float $seconds): void
    {
        $dir = WRITEPATH . 'data/agents/' . $name;
        if (! is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        $line = json_encode([
            'time'   => date('c'),
            'ok'     => $ok,
            'ms'     => (int) round($seconds * 1000),
            'report' => $report,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if ($line !== false) {
            @file_put_contents($dir . '/' . date('Y-m') . '.jsonl', $line . "\n", FILE_APPEND | LOCK_EX);
        }
    }

    /**
     * Dikkat kuyruğu: Ana Admin'in göreceği alarm dosyası bırakır —
     * ileride Jarvis onay/dikkat kuyruğunun ham beslemesi.
     */
    protected function alert(string $type, string $message): void
    {
        $dir = WRITEPATH . 'data/alerts';
        if (! is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        // uniqid eki: aynı saniyede üretilen alarmlar birbirini ezmesin.
        @file_put_contents(
            $dir . '/' . date('Ymd-His') . '-' . $this->agentName() . '-' . substr(uniqid(), -6) . '.json',
            json_encode([
                'time'    => date('c'),
                'agent'   => $this->agentName(),
                'type'    => $type,
                'message' => $message,
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        );
    }
}
