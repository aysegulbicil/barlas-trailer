<?php

/**
 * Quote (offerte) taalteksten (nl)
 *
 * Productgerichte offertestroom: teksten van de productkiezer en het
 * WhatsApp-berichtsjabloon. {product} en {url} worden door de Quote-
 * controller ingevuld via str_replace (geen ICU-formatting).
 */
return [
    'meta_title'       => 'Offerte aanvragen',
    'meta_description' => 'Kies het product waarin u geïnteresseerd bent en ontvang snel een offerte via WhatsApp. Het salesteam van Barlas Trailer reageert dezelfde dag.',

    'page_title'       => 'Offerte aanvragen',
    'page_lead'        => 'Kies het product waarvoor u een offerte wilt; we reageren snel via WhatsApp met de productdetails.',

    'search_placeholder' => 'Producten zoeken…',
    'no_results'         => 'Geen passende producten gevonden.',

    'wa_note'          => 'Zodra u een product kiest, opent WhatsApp met de productdetails en de paginalink — alleen nog versturen.',

    'form_alt_text'    => 'Geen WhatsApp?',
    'form_alt_link'    => 'Via het formulier versturen',

    // {product} = product (optioneel "— variant"), {url} = productpagina-link
    'wa_message'       => "*Offerteaanvraag · Barlas Trailer*\n\nProduct: *{product}*\nCategorie: {category}\nProductpagina: {url}\n\nHallo, ik wil graag een offerte aanvragen voor bovenstaand product.",
];
