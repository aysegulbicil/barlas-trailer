<?php

/**
 * Quote language strings (ro)
 *
 * Product-aware quote flow: product picker page copy and the WhatsApp
 * message template. The {product} and {url} tokens are filled in by the
 * Quote controller via a plain str_replace (no ICU formatting).
 */
return [
    'meta_title'       => 'Cereți o ofertă',
    'meta_description' => 'Alegeți produsul care vă interesează și primiți rapid o ofertă pe WhatsApp. Echipa de vânzări Barlas Trailer răspunde în aceeași zi.',

    'page_title'       => 'Cereți o ofertă',
    'page_lead'        => 'Alegeți produsul pentru care doriți o ofertă; vă răspundem rapid pe WhatsApp cu detaliile produsului.',

    'search_placeholder' => 'Căutați produse…',
    'no_results'         => 'Nu s-au găsit produse care să corespundă.',

    'wa_note'          => 'Când alegeți un produs, WhatsApp se deschide cu detaliile produsului și linkul paginii — trebuie doar să trimiteți mesajul.',

    'form_alt_text'    => 'Nu folosiți WhatsApp?',
    'form_alt_link'    => 'Trimiteți prin formular',

    // {product} = product (optionally "— variant"), {url} = product page link
    'wa_message'       => "*Cerere de ofertă · Barlas Trailer*\n\nProdus: *{product}*\nCategorie: {category}\nPagina produsului: {url}\n\nBună ziua, aș dori să solicit o ofertă pentru produsul de mai sus.",
];
