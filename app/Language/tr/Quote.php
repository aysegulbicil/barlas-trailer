<?php

/**
 * Quote (Teklif) language strings (tr)
 *
 * Ürün-bağlamlı teklif akışı: ürün seçtirme sayfası metinleri ve WhatsApp
 * mesaj şablonu. {product} ve {url} jetonları Quote controller tarafından
 * doldurulur (ICU değil, düz str_replace — özel karakter derdi olmaz).
 */
return [
    'meta_title'       => 'Teklif Al',
    'meta_description' => 'İlgilendiğiniz ürünü seçin, WhatsApp üzerinden hızlıca teklif alın. Barlas Trailer satış ekibi aynı gün dönüş yapar.',

    'page_title'       => 'Teklif Al',
    'page_lead'        => 'Teklif almak istediğiniz ürünü seçin; WhatsApp üzerinden ürün bilgisiyle birlikte hızlıca dönüş yapalım.',

    'search_placeholder' => 'Ürün ara…',
    'no_results'         => 'Eşleşen ürün bulunamadı.',

    'wa_note'          => 'Ürünü seçtiğinizde WhatsApp, ürün bilgisi ve sayfa bağlantısıyla açılır — mesajı göndermeniz yeterli.',

    'form_alt_text'    => 'WhatsApp kullanmıyor musunuz?',
    'form_alt_link'    => 'Formla gönderin',

    // {product} = ürün (varsa "— varyant"), {url} = ürün sayfası bağlantısı
    'wa_message'       => "*Teklif Talebi · Barlas Trailer*\n\nÜrün: *{product}*\nKategori: {category}\nÜrün sayfası: {url}\n\nMerhaba, yukarıdaki ürün için teklif almak istiyorum.",
];
