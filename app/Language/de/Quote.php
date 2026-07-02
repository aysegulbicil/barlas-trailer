<?php

/**
 * Quote (Angebot) Sprachtexte (de)
 *
 * Produktbezogener Angebotsablauf: Texte der Produktauswahlseite und die
 * WhatsApp-Nachrichtenvorlage. {product} und {url} werden vom Quote-
 * Controller per str_replace gefüllt (kein ICU-Formatting).
 */
return [
    'meta_title'       => 'Angebot anfordern',
    'meta_description' => 'Wählen Sie das gewünschte Produkt und erhalten Sie schnell ein Angebot über WhatsApp. Das Barlas-Trailer-Vertriebsteam antwortet noch am selben Tag.',

    'page_title'       => 'Angebot anfordern',
    'page_lead'        => 'Wählen Sie das Produkt, für das Sie ein Angebot wünschen; wir antworten schnell über WhatsApp mit den Produktdetails.',

    'search_placeholder' => 'Produkte durchsuchen…',
    'no_results'         => 'Keine passenden Produkte gefunden.',

    'wa_note'          => 'Wenn Sie ein Produkt wählen, öffnet sich WhatsApp mit den Produktdetails und dem Seitenlink — einfach absenden.',

    'form_alt_text'    => 'Kein WhatsApp?',
    'form_alt_link'    => 'Über das Formular senden',

    // {product} = Produkt (optional "— Variante"), {url} = Produktseiten-Link
    'wa_message'       => "*Angebotsanfrage · Barlas Trailer*\n\nProdukt: *{product}*\nKategorie: {category}\nProduktseite: {url}\n\nHallo, ich möchte ein Angebot für das oben genannte Produkt anfordern.",
];
