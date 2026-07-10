<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use Config\App;

/**
 * Metrics — birinci taraf, çerezsiz sayfa ölçümü (KVKK dostu).
 *
 * İstemci beacon'ı (partials/metrics.php) sayfa başına tek POST atar;
 * kayıt günlük JSONL akışına eklenir: writable/data/metrics/hits-Y-m-d.jsonl.
 * Kamusal sayfalara DB sokmama kuralına uyar (dosya tabanlı, JSONL).
 *
 * Gizlilik sözleşmesi:
 *  - Çerez / localStorage KULLANILMAZ; yanıt her zaman boş 204'tür.
 *  - IP ve ham User-Agent DİSKE YAZILMAZ. Tekil ziyaretçi sayımı için
 *    hash(IP|UA|günlük-tuz) üretilir; tuz her gün yenilenir ve eski tuz
 *    silinir, bu yüzden dünün kimliği bugün yeniden üretilemez.
 *  - Referrer'ın yalnız host'u tutulur (yol/parametre atılır).
 *
 * Uç nokta locale önekinsizdir (sitemap.xml gibi) ve CSRF'ten muaftır
 * (Config\Filters — sendBeacon token taşıyamaz; kayıt eklemekten başka
 * yan etkisi olmayan, yanıtsız bir uçtur).
 */
class Metrics extends BaseController
{
    public function hit(): ResponseInterface
    {
        $response = $this->response->setStatusCode(204)->setBody('');

        // Bot/önizleme trafiğini sayma (kaba UA imzası yeterli; sayfa
        // görüntüleme istatistiği için yanlış-negatif zararsızdır).
        $ua = (string) $this->request->getUserAgent()->getAgentString();
        if ($ua === '' || preg_match('/bot|crawl|spider|slurp|preview|lighthouse|headless|facebookexternalhit|whatsapp|telegram|skype|curl|wget|python|monitor|uptime/i', $ua) === 1) {
            return $response;
        }

        $body = json_decode((string) $this->request->getBody(), true);
        if (! is_array($body)) {
            return $response;
        }

        $path = (string) ($body['p'] ?? '');
        if ($path === '' || $path[0] !== '/' || strlen($path) > 200) {
            return $response;
        }

        // Locale, yolun ilk segmentinden (kayıt dilsiz bir uca aitse boş kalır).
        $segment = explode('/', trim($path, '/'), 2)[0];
        $locale  = in_array($segment, config(App::class)->supportedLocales, true) ? $segment : '';

        // Dış referrer'ın yalnız host'u; kendi alanımızdan gelenler iç gezinmedir.
        $referrer = '';
        $rawRef   = (string) ($body['r'] ?? '');
        if ($rawRef !== '') {
            $host    = strtolower((string) (parse_url($rawRef, PHP_URL_HOST) ?? ''));
            $ownHost = strtolower((string) (parse_url(base_url(), PHP_URL_HOST) ?? ''));
            if ($host !== '' && $host !== $ownHost) {
                $referrer = substr($host, 0, 100);
            }
        }

        $dir = WRITEPATH . 'data/metrics';
        if (! is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        $visitor = substr(hash('sha256', $this->request->getIPAddress() . '|' . $ua . '|' . $this->dailySalt($dir)), 0, 16);

        $row = [
            'ts'  => date('H:i:s'),
            'p'   => $path,
            'loc' => $locale,
            'ref' => $referrer,
            'v'   => $visitor,
        ];

        @file_put_contents(
            $dir . '/hits-' . date('Y-m-d') . '.jsonl',
            json_encode($row, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n",
            FILE_APPEND | LOCK_EX
        );

        return $response;
    }

    /**
     * Günlük anonimleştirme tuzu. Gün değişince yenisi üretilir ve ESKİLER
     * SİLİNİR — geçmiş günlerin ziyaretçi hash'leri kalıcı olarak çözülemez.
     */
    private function dailySalt(string $dir): string
    {
        $file = $dir . '/.salt-' . date('Y-m-d');
        if (is_file($file)) {
            return (string) file_get_contents($file);
        }

        foreach (glob($dir . '/.salt-*') ?: [] as $old) {
            @unlink($old);
        }

        $salt = bin2hex(random_bytes(16));
        @file_put_contents($file, $salt, LOCK_EX);

        return $salt;
    }
}
