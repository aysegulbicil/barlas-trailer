<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Libraries\AiAssistant;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use Throwable;

/**
 * Ai controller
 *
 * Ana sayfadaki "#asistan" konsolunu besleyen tek uç nokta. Asistan YEREL
 * çalışır (dış API yok); ürün ve hizmet verimizden cevap verir.
 *
 *   POST /{locale}/ai/ask  ->  JSON { ok, answer, products[], services[], cta }
 *
 * Önlemler: aynı-köken kontrolü, IP başına hız sınırı (Throttler), bal kabı.
 * Sorulan sorular (anonim) writable/ai-logs/YYYY-MM.jsonl içine kaydedilir.
 */
class Ai extends BaseController
{
    public function ask(): ResponseInterface
    {
        $config = config('Ai');

        if (strtoupper($this->request->getMethod()) !== 'POST') {
            return $this->fail(405, lang('Home.ai_error'));
        }
        if (! $this->isSameOrigin()) {
            return $this->fail(403, lang('Home.ai_error'));
        }
        if (! $config->enabled) {
            return $this->fail(200, lang('Home.ai_unconfigured'));
        }

        $json     = $this->request->getJSON(true);
        $payload  = is_array($json) ? $json : [];
        $question = (string) ($payload['q'] ?? $this->request->getPost('q') ?? '');
        $honeypot = (string) ($payload['website'] ?? $this->request->getPost('website') ?? '');

        // Bal kabı: bot doldurmuşsa sessizce başarılı görün.
        if (trim($honeypot) !== '') {
            return $this->response->setJSON(['ok' => true, 'answer' => '', 'products' => [], 'services' => []]);
        }

        // Hız sınırı (IP başına).
        $throttler = Services::throttler();
        if ($throttler->check(md5('ai-' . $this->request->getIPAddress()), $config->rateLimitPerMinute, MINUTE) === false) {
            return $this->fail(429, lang('Home.ai_rate'));
        }

        if (trim($question) === '') {
            return $this->fail(200, lang('Home.ai_empty'));
        }

        $locale    = $this->request->getLocale() ?: current_locale();
        $assistant = new AiAssistant($config);
        $result    = $assistant->ask($question, $locale);

        if (($result['ok'] ?? false) !== true) {
            $msg = ($result['status'] ?? '') === 'empty' ? lang('Home.ai_empty') : lang('Home.ai_error');

            return $this->fail(200, $msg);
        }

        $this->logQuestion($config, $locale, $question, $result);

        return $this->response->setJSON([
            'ok'       => true,
            'answer'   => $result['answer'] ?? '',
            'products' => $result['products'] ?? [],
            'services' => $result['services'] ?? [],
            'cta'      => $result['cta'] ?? null,
        ]);
    }

    /** Hatalı/erken yanıt - JSON, ok=false. */
    private function fail(int $code, string $message): ResponseInterface
    {
        return $this->response->setStatusCode($code)->setJSON([
            'ok'       => false,
            'answer'   => $message,
            'products' => [],
            'services' => [],
        ]);
    }

    /** Anonim soru kaydı (writable/ai-logs/YYYY-MM.jsonl). */
    private function logQuestion(object $config, string $locale, string $question, array $result): void
    {
        if (empty($config->logQuestions)) {
            return;
        }

        try {
            $dir = WRITEPATH . 'ai-logs';
            if (! is_dir($dir)) {
                @mkdir($dir, 0775, true);
            }

            $row = [
                'time'     => date('c'),
                'locale'   => $locale,
                'q'        => mb_substr($question, 0, 300),
                'intent'   => $result['intent'] ?? '',
                'products' => array_map(static fn ($p) => $p['name'] ?? '', $result['products'] ?? []),
                'services' => array_map(static fn ($s) => $s['name'] ?? '', $result['services'] ?? []),
                'iphash'   => substr(hash('sha256', (string) $this->request->getIPAddress()), 0, 12),
            ];

            $line = json_encode($row, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if ($line !== false) {
                @file_put_contents($dir . '/' . date('Y-m') . '.jsonl', $line . "\n", FILE_APPEND | LOCK_EX);
            }
        } catch (Throwable $e) {
            log_message('warning', '[Ai] log failed: {m}', ['m' => $e->getMessage()]);
        }
    }

    /**
     * İstek bu siteden mi geliyor? Origin (yoksa Referer) ana bilgisayar
     * adı, sitenin ana bilgisayar adıyla eşleşmeli. İkisi de yoksa
     * (tarayıcı dışı istemci) hız sınırına güvenir, geçişe izin verir.
     */
    private function isSameOrigin(): bool
    {
        $siteHost = parse_url(base_url(), PHP_URL_HOST);
        if (! is_string($siteHost) || $siteHost === '') {
            return true;
        }

        $origin  = $this->request->getHeaderLine('Origin');
        $referer = $this->request->getHeaderLine('Referer');
        $source  = $origin !== '' ? $origin : $referer;
        if ($source === '') {
            return true;
        }

        $sourceHost = parse_url($source, PHP_URL_HOST);

        return is_string($sourceHost) && strcasecmp($sourceHost, $siteHost) === 0;
    }
}
