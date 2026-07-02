<?php

/**
 * Quote (wycena) teksty językowe (pl)
 *
 * Produktowy proces wyceny: teksty strony wyboru produktu i szablon
 * wiadomości WhatsApp. {product} i {url} wypełnia kontroler Quote przez
 * str_replace (bez formatowania ICU).
 */
return [
    'meta_title'       => 'Poproś o wycenę',
    'meta_description' => 'Wybierz interesujący Cię produkt i szybko otrzymaj wycenę przez WhatsApp. Zespół sprzedaży Barlas Trailer odpowiada tego samego dnia.',

    'page_title'       => 'Poproś o wycenę',
    'page_lead'        => 'Wybierz produkt, którego wycenę chcesz otrzymać; szybko odpowiemy przez WhatsApp ze szczegółami produktu.',

    'search_placeholder' => 'Szukaj produktów…',
    'no_results'         => 'Nie znaleziono pasujących produktów.',

    'wa_note'          => 'Po wybraniu produktu otworzy się WhatsApp ze szczegółami i linkiem do strony — wystarczy wysłać wiadomość.',

    'form_alt_text'    => 'Nie korzystasz z WhatsAppa?',
    'form_alt_link'    => 'Wyślij przez formularz',

    // {product} = produkt (opcjonalnie "— wariant"), {url} = link do strony produktu
    'wa_message'       => "*Zapytanie o wycenę · Barlas Trailer*\n\nProdukt: *{product}*\nKategoria: {category}\nStrona produktu: {url}\n\nDzień dobry, proszę o wycenę powyższego produktu.",
];
