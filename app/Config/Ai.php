<?php

declare(strict_types=1);

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Yapay zeka asistanı yapılandırması.
 *
 * Asistan YEREL çalışır: ürün kataloğu (products.json) ve hizmet verisinden
 * cevap verir. DIŞ API / API ANAHTARI GEREKMEZ; sorgu başına maliyet yoktur.
 *
 * Bilgi tabanını düzenlemek için:
 *   - Ürünler   -> app/Data/products.json
 *   - Hizmetler  -> app/Language/{dil}/Navigation.php (srv_* anahtarları)
 *   - Eş anlamlı -> app/Data/ai-synonyms.php
 *   - Cevap metni -> app/Language/{dil}/Ai.php
 */
final class Ai extends BaseConfig
{
    /** Asistanı açar/kapatır. */
    public bool $enabled = true;

    /** Bir cevapta gösterilecek azami ürün kartı sayısı. */
    public int $maxProducts = 3;

    /** Kullanıcı sorusunun azami karakter uzunluğu. */
    public int $maxQuestionLength = 300;

    /** Hız sınırı: IP başına dakikada azami istek. */
    public int $rateLimitPerMinute = 30;

    /** Sorulan soruları (anonim) writable/ai-logs/ içine kaydet. */
    public bool $logQuestions = true;
}
