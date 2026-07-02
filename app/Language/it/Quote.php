<?php

/**
 * Quote (preventivo) testi lingua (it)
 *
 * Flusso preventivo orientato al prodotto: testi della pagina di scelta
 * prodotto e modello del messaggio WhatsApp. {product} e {url} vengono
 * riempiti dal controller Quote via str_replace (niente ICU).
 */
return [
    'meta_title'       => 'Richiedi un preventivo',
    'meta_description' => 'Scegli il prodotto che ti interessa e ricevi rapidamente un preventivo via WhatsApp. Il team commerciale Barlas Trailer risponde in giornata.',

    'page_title'       => 'Richiedi un preventivo',
    'page_lead'        => 'Scegli il prodotto per cui desideri un preventivo; ti risponderemo rapidamente via WhatsApp con i dettagli.',

    'search_placeholder' => 'Cerca prodotti…',
    'no_results'         => 'Nessun prodotto corrispondente trovato.',

    'wa_note'          => 'Quando scegli un prodotto, WhatsApp si apre con i dettagli e il link alla pagina — basta inviare il messaggio.',

    'form_alt_text'    => 'Non usi WhatsApp?',
    'form_alt_link'    => 'Invia tramite il modulo',

    // {product} = prodotto (eventuale "— variante"), {url} = link alla pagina prodotto
    'wa_message'       => "*Richiesta di preventivo · Barlas Trailer*\n\nProdotto: *{product}*\nCategoria: {category}\nPagina prodotto: {url}\n\nBuongiorno, vorrei richiedere un preventivo per il prodotto indicato sopra.",
];
