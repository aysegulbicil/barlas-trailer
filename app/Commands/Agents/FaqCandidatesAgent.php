<?php

namespace App\Commands\Agents;

/**
 * SSS aday ajanı (§7.5'in veri toplama yarısı) — AI asistan loglarındaki
 * (writable/ai-logs/*.jsonl) gerçek ziyaretçi sorularını normalleştirip
 * sıklığa göre derler ve writable/data/faq-candidates.json dosyasına
 * yazar. Birden fazla kez sorulan soru varsa dikkat kuyruğuna not düşer;
 * Ana Admin onaylayınca soru app/Language/{locale}/Faq.php dosyalarına eklenir
 * (yayın kararı insanda — otomatik yayın yok).
 *
 * cron önerisi (haftada bir, pazartesi 06:00):
 *   0 6 * * 1  cd /var/www/html && php spark agents:faq-candidates
 */
class FaqCandidatesAgent extends AgentCommand
{
    protected $name        = 'agents:faq-candidates';
    protected $description = 'Asistan loglarından SSS aday sorularını derler (Ana Admin incelemesi için).';

    protected function agentName(): string
    {
        return 'faq-candidates';
    }

    protected function work(): array
    {
        $counts  = [];
        $samples = [];

        foreach (glob(WRITEPATH . 'ai-logs/*.jsonl') ?: [] as $file) {
            $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];

            foreach ($lines as $line) {
                $row = json_decode($line, true);
                if (! is_array($row)) {
                    continue;
                }

                $q = trim((string) ($row['q'] ?? ''));
                if ($q === '') {
                    continue;
                }

                $norm = mb_strtolower(preg_replace('/\s+/u', ' ', $q) ?? $q);

                $counts[$norm] = ($counts[$norm] ?? 0) + 1;
                $samples[$norm] ??= [
                    'q'      => $q,
                    'locale' => (string) ($row['locale'] ?? ''),
                    'first'  => (string) ($row['time'] ?? ''),
                ];
            }
        }

        arsort($counts);

        $candidates = [];
        foreach (array_slice($counts, 0, 30, true) as $norm => $n) {
            $candidates[] = [
                'q'          => $samples[$norm]['q'],
                'count'      => $n,
                'locale'     => $samples[$norm]['locale'],
                'first_seen' => $samples[$norm]['first'],
            ];
        }

        $dir = WRITEPATH . 'data';
        if (! is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        file_put_contents($dir . '/faq-candidates.json', json_encode([
            'updated'         => date('c'),
            'total_questions' => array_sum($counts),
            'unique'          => count($counts),
            'candidates'      => $candidates,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n", LOCK_EX);

        $repeats = count(array_filter($counts, static fn (int $n): bool => $n >= 2));
        if ($repeats > 0) {
            $this->alert(
                'faq-candidates',
                $repeats . ' soru birden fazla kez soruldu — SSS adayı olarak inceleyin: writable/data/faq-candidates.json'
            );
        }

        return [
            'total'   => array_sum($counts),
            'unique'  => count($counts),
            'repeats' => $repeats,
        ];
    }
}
