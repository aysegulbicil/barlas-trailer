<?php

namespace App\Commands\Agents;

use App\Models\VehicleModel;

/**
 * İçerik Fabrikası ajanı v1 (§7.6) — "kendi kendini yazan site" omurgası.
 *
 * Akış: yeni araç kaydı (SQLite) → haber + sosyal medya TASLAĞI üretilir
 * → writable/data/content/drafts/ onay kuyruğuna düşer → Ana Admin
 * Panel > İçerik'ten tek tıkla onaylar/reddeder → onay, haberi
 * app/Data/content/news/tr/{slug}.md olarak yayınlar (kamusal /news).
 *
 * v1 metin üretimi ŞABLON tabanlıdır (saf kod, sıfır API maliyeti —
 * ekonomik mimari ilkesi). AI aboneliği bağlandığında buildDraft()
 * içindeki başlık/gövde/sosyal üretimi AI çağrısıyla değiştirilir;
 * kuyruk ve onay akışı aynen kalır. Şasi numarası kamusal metne ASLA
 * tam yazılmaz (son 4 hane maskeli) — QR kart gizliliğiyle aynı ilke.
 *
 * cron önerisi (her akşam 18:00):
 *   0 18 * * *  cd /var/www/html && php spark agents:content-factory
 */
class ContentFactoryAgent extends AgentCommand
{
    protected $name        = 'agents:content-factory';
    protected $description = 'Yeni araç kayıtlarından haber + sosyal post taslağı üretip onay kuyruğuna düşürür.';

    protected function agentName(): string
    {
        return 'content-factory';
    }

    protected function work(): array
    {
        $state  = $this->loadState();
        $lastId = (int) ($state['last_vehicle_id'] ?? 0);
        $drafts = 0;

        $vehicles = (new VehicleModel())
            ->where('id >', $lastId)
            ->orderBy('id', 'ASC')
            ->findAll();

        foreach ($vehicles as $vehicle) {
            $this->queueDraft($this->buildDraft($vehicle));
            $state['last_vehicle_id'] = (int) $vehicle['id'];
            $drafts++;
        }

        $this->saveState($state);

        if ($drafts > 0) {
            $this->alert('drafts-queued', sprintf(
                '%d yeni içerik taslağı onay bekliyor — Panel > İçerik ekranından tek tıkla yayınla/reddet.',
                $drafts
            ));
        }

        return ['new_vehicles' => count($vehicles), 'drafts' => $drafts];
    }

    /**
     * Şablon tabanlı taslak (v1). AI entegrasyonunda yalnız bu metod değişir.
     *
     * @param array<string, mixed> $vehicle
     * @return array<string, mixed>
     */
    private function buildDraft(array $vehicle): array
    {
        $model  = (string) $vehicle['model'];
        $year   = $vehicle['year'] !== null ? (string) $vehicle['year'] : date('Y');
        $masked = '…' . substr((string) $vehicle['chassis_no'], -4);
        $slug   = 'yeni-uretim-' . $this->slugify($model) . '-' . $vehicle['id'];

        $title = sprintf('Üretim hattından yeni bir %s çıktı', $model);

        $body = sprintf(
            "Üretim hattımızda tamamlanan yeni bir **%s** (%s) kalite kontrollerini geçerek sahaya hazır hale geldi.\n\n"
            . "Her aracımız gibi bu araç da teslimatla birlikte QR araç kimlik kartına kavuşuyor: teknik dosya, garanti durumu "
            . "ve bakım geçmişi tek taramayla erişilebilir olacak.\n\n"
            . "Benzer bir araca ihtiyacınız varsa [ürün kataloğumuza](/tr/products) göz atabilir veya doğrudan teklif isteyebilirsiniz.",
            $model,
            $year
        );

        $social = sprintf(
            'Üretim hattımızdan yeni bir %s sahaya hazır! 💪 Kalite kontrolden tam not aldı, QR kimlik kartıyla yola çıkıyor. '
            . '#treyler #trailer #üretim #BarlasTrailer',
            $model
        );

        return [
            'id'      => $slug,
            'created' => date('c'),
            'source'  => [
                'vehicle_id'     => (int) $vehicle['id'],
                'model'          => $model,
                'year'           => $year,
                'chassis_masked' => $masked,
            ],
            'title'       => $title,
            'description' => sprintf('%s üretim hattımızda tamamlandı — kalite kontrolden geçti, sahaya hazır.', $model),
            'body'        => $body,
            'social'      => $social,
        ];
    }

    private function queueDraft(array $draft): void
    {
        $dir = WRITEPATH . 'data/content/drafts';
        if (! is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        @file_put_contents(
            $dir . '/' . $draft['id'] . '.json',
            json_encode($draft, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . "\n",
            LOCK_EX
        );
    }

    /** Türkçe karakterleri sadeleştiren, [a-z0-9-] üreten slug. */
    private function slugify(string $text): string
    {
        $text = strtr($text, ['ı' => 'i', 'İ' => 'i', 'ş' => 's', 'Ş' => 's', 'ğ' => 'g', 'Ğ' => 'g', 'ü' => 'u', 'Ü' => 'u', 'ö' => 'o', 'Ö' => 'o', 'ç' => 'c', 'Ç' => 'c']);
        $text = strtolower($text);
        $text = (string) preg_replace('/[^a-z0-9]+/', '-', $text);

        return trim($text, '-') ?: 'arac';
    }

    private function statePath(): string
    {
        return WRITEPATH . 'data/agents/content-factory/state.json';
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
