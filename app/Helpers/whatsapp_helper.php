<?php

/**
 * WhatsApp helper
 *
 * Tek kaynak: WhatsApp numarası ve click-to-chat (wa.me) URL üretimi.
 * Hem AI asistanı hem de teklif (Quote) akışı buradan beslenir, böylece
 * numara ve bağlantı biçimi tek yerde yönetilir.
 *
 * Numara, Common.phone_raw dil anahtarından türetilir (ör. 05525002000),
 * rakam-dışı karakterler temizlenir ve baştaki "0" ülke koduna (90) çevrilir.
 */

if (! function_exists('whatsapp_number')) {
    /**
     * Mağaza WhatsApp numarasını wa.me biçiminde döndürür.
     * Örn: "05525002000" -> "905525002000".
     */
    function whatsapp_number(): string
    {
        $raw = preg_replace('/\D/', '', (string) lang('Common.phone_raw'));
        if ($raw === null || $raw === '') {
            $raw = '05525002000';
        }
        if (str_starts_with($raw, '0')) {
            $raw = '90' . substr($raw, 1);
        }

        return $raw;
    }
}

if (! function_exists('whatsapp_url')) {
    /**
     * Ön-doldurulmuş metinle bir wa.me click-to-chat bağlantısı kurar.
     * Not: wa.me yalnızca METİN ön-doldurur; görsel eklenemez. Mesaja bir
     * ürün URL'si koymak, WhatsApp'ın sayfanın og:image'inden otomatik
     * önizleme kartı üretmesini sağlar.
     */
    function whatsapp_url(string $text = ''): string
    {
        $base = 'https://wa.me/' . whatsapp_number();

        return $text === '' ? $base : $base . '?text=' . rawurlencode($text);
    }
}
