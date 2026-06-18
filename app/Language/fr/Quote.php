<?php

/**
 * Quote language strings (fr)
 *
 * Demande de devis liée au produit : textes de la page de sélection du
 * produit et modèle de message WhatsApp. Les jetons {product} et {url}
 * sont remplis par le contrôleur Quote via un simple str_replace (pas de
 * formatage ICU).
 */
return [
    'meta_title'       => 'Demander un devis',
    'meta_description' => 'Choisissez le produit qui vous intéresse et obtenez rapidement un devis via WhatsApp. L’équipe commerciale Barlas Trailer répond le jour même.',

    'page_title'       => 'Demander un devis',
    'page_lead'        => 'Choisissez le produit pour lequel vous souhaitez un devis ; nous vous répondrons rapidement via WhatsApp avec les détails du produit.',

    'search_placeholder' => 'Rechercher des produits…',
    'no_results'         => 'Aucun produit correspondant.',

    'wa_note'          => 'Lorsque vous choisissez un produit, WhatsApp s’ouvre avec les détails du produit et le lien de la page — il suffit d’envoyer le message.',

    'form_alt_text'    => 'Vous n’utilisez pas WhatsApp ?',
    'form_alt_link'    => 'Envoyez via le formulaire',

    // {product} = produit (éventuellement « — variante »), {url} = lien de la page produit
    'wa_message'       => "*Demande de devis · Barlas Trailer*\n\nProduit : *{product}*\nCatégorie : {category}\nPage produit : {url}\n\nBonjour, je souhaite demander un devis pour le produit ci-dessus.",
];
