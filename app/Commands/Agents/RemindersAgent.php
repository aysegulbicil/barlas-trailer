<?php

namespace App\Commands\Agents;

use App\Models\VehicleModel;

/**
 * Hatırlatıcı ajanı — araç kayıt defterini tarar (§5.4/§5.10/§5.11):
 *   - garanti bitişine ≤30 gün kalan araçlar        → warranty-expiring
 *   - bugün teslimat yıldönümü olan araçlar          → anniversary
 *   - sezon dönümü (1 Ekim kış / 1 Nisan yaz)        → seasonal
 *
 * Ajan GÖNDERİM YAPMAZ: bulgular dikkat kuyruğuna (writable/data/alerts)
 * düşer; Ana Admin panelde görüp müşteriyle kendi iletişir (onay ilkesi).
 * SMTP kurulunca onaylı gönderim bu ajanın üzerine bağlanacak.
 *
 * Tekrar koruması: writable/data/agents/reminders/state.json — aynı araç
 * için aynı yıl içinde aynı hatırlatma ikinci kez üretilmez.
 *
 * cron önerisi (her sabah 06:30):
 *   30 6 * * *  cd /var/www/html && php spark agents:reminders
 */
class RemindersAgent extends AgentCommand
{
    protected $name        = 'agents:reminders';
    protected $description = 'Garanti bitişi, teslimat yıldönümü ve sezon hatırlatmalarını dikkat kuyruğuna düşürür.';

    private const WARRANTY_WINDOW_DAYS = 30;

    protected function agentName(): string
    {
        return 'reminders';
    }

    protected function work(): array
    {
        $today = date('Y-m-d');
        $year  = date('Y');
        $state = $this->loadState();

        $counts = ['warranty' => 0, 'anniversary' => 0, 'seasonal' => 0];

        foreach ((new VehicleModel())->findAll() as $vehicle) {
            $id = (string) $vehicle['id'];

            // Garanti bitişi yaklaşıyor.
            $until = (string) ($vehicle['warranty_until'] ?? '');
            if ($until !== '' && $until >= $today
                && $until <= date('Y-m-d', strtotime('+' . self::WARRANTY_WINDOW_DAYS . ' days'))
                && ($state['warranty'][$id] ?? '') !== $year
            ) {
                $this->alert('warranty-expiring', sprintf(
                    'Garanti bitiyor: %s (şasi %s) — bitiş %s. Müşteriye bakım/uzatma teklifi için fırsat.',
                    $vehicle['model'],
                    $vehicle['chassis_no'],
                    $until
                ));
                $state['warranty'][$id] = $year;
                $counts['warranty']++;
            }

            // Teslimat yıldönümü (ay-gün eşleşmesi).
            $delivered = (string) ($vehicle['delivered_at'] ?? '');
            if ($delivered !== '' && substr($delivered, 5) === substr($today, 5)
                && substr($delivered, 0, 4) < $year
                && ($state['anniversary'][$id] ?? '') !== $year
            ) {
                $age = (int) $year - (int) substr($delivered, 0, 4);
                $this->alert('anniversary', sprintf(
                    'Teslimat yıldönümü: %s (şasi %s) bugün %d. yılını doldurdu — kutlama mesajı fırsatı.',
                    $vehicle['model'],
                    $vehicle['chassis_no'],
                    $age
                ));
                $state['anniversary'][$id] = $year;
                $counts['anniversary']++;
            }
        }

        // Sezon dönümü: 1 Ekim (kış) / 1 Nisan (yaz) — ayda bir kez.
        $month = date('m');
        if (in_array($month, ['10', '04'], true) && ($state['seasonal'] ?? '') !== date('Y-m')) {
            $this->alert('seasonal', $month === '10'
                ? 'Kış sezonu yaklaşıyor: müşterilere kış lastiği / fren / ışık kontrolü hatırlatma kampanyası zamanı.'
                : 'Yaz sezonu yaklaşıyor: müşterilere soğutma / fren / lastik basıncı kontrolü hatırlatma kampanyası zamanı.');
            $state['seasonal'] = date('Y-m');
            $counts['seasonal']++;
        }

        $this->saveState($state);

        return $counts + ['vehicles' => (new VehicleModel())->countAllResults()];
    }

    private function statePath(): string
    {
        return WRITEPATH . 'data/agents/reminders/state.json';
    }

    private function loadState(): array
    {
        $state = json_decode((string) @file_get_contents($this->statePath()), true);

        return is_array($state) ? $state : [];
    }

    private function saveState(array $state): void
    {
        $dir = dirname($this->statePath());
        if (! is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        @file_put_contents($this->statePath(), json_encode($state, JSON_PRETTY_PRINT) . "\n", LOCK_EX);
    }
}
