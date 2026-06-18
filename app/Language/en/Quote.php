<?php

/**
 * Quote language strings (en)
 *
 * Product-aware quote flow: product picker page copy and the WhatsApp
 * message template. The {product} and {url} tokens are filled in by the
 * Quote controller via a plain str_replace (no ICU formatting).
 */
return [
    'meta_title'       => 'Get a Quote',
    'meta_description' => 'Pick the product you are interested in and get a quote quickly over WhatsApp. The Barlas Trailer sales team replies the same day.',

    'page_title'       => 'Get a Quote',
    'page_lead'        => 'Choose the product you would like a quote for; we will reply quickly over WhatsApp with the product details.',

    'search_placeholder' => 'Search products…',
    'no_results'         => 'No matching products found.',

    'wa_note'          => 'When you pick a product, WhatsApp opens with the product details and page link — just send the message.',

    'form_alt_text'    => 'Not using WhatsApp?',
    'form_alt_link'    => 'Send via the form',

    // {product} = product (optionally "— variant"), {url} = product page link
    'wa_message'       => "*Quote Request · Barlas Trailer*\n\nProduct: *{product}*\nCategory: {category}\nProduct page: {url}\n\nHello, I would like to request a quote for the product above.",
];
