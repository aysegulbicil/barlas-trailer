<?php

/**
 * Quote language strings (bg)
 *
 * Product-aware quote flow: product picker page copy and the WhatsApp
 * message template. The {product} and {url} tokens are filled in by the
 * Quote controller via a plain str_replace (no ICU formatting).
 */
return [
    'meta_title'       => 'Поискайте оферта',
    'meta_description' => 'Изберете продукта, който ви интересува, и получете бърза оферта през WhatsApp. Търговският екип на Barlas Trailer отговаря още същия ден.',

    'page_title'       => 'Поискайте оферта',
    'page_lead'        => 'Изберете продукта, за който желаете оферта; ще ви отговорим бързо през WhatsApp с подробностите за продукта.',

    'search_placeholder' => 'Търсете продукти…',
    'no_results'         => 'Няма намерени продукти.',

    'wa_note'          => 'Когато изберете продукт, WhatsApp се отваря с данните за продукта и връзка към страницата — просто изпратете съобщението.',

    'form_alt_text'    => 'Не използвате WhatsApp?',
    'form_alt_link'    => 'Изпратете през формата',

    // {product} = product (optionally "— variant"), {url} = product page link
    'wa_message'       => "*Заявка за оферта · Barlas Trailer*\n\nПродукт: *{product}*\nКатегория: {category}\nПродуктова страница: {url}\n\nЗдравейте, бих искал да получа оферта за посочения по-горе продукт.",
];
