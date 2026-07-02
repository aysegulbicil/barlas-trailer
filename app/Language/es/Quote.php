<?php

/**
 * Quote (presupuesto) textos de idioma (es)
 *
 * Flujo de presupuesto orientado a producto: textos de la página de
 * selección y plantilla del mensaje de WhatsApp. {product} y {url} los
 * rellena el controlador Quote con str_replace (sin formato ICU).
 */
return [
    'meta_title'       => 'Solicitar presupuesto',
    'meta_description' => 'Elige el producto que te interesa y recibe un presupuesto rápido por WhatsApp. El equipo comercial de Barlas Trailer responde el mismo día.',

    'page_title'       => 'Solicitar presupuesto',
    'page_lead'        => 'Elige el producto del que quieres presupuesto; te responderemos rápido por WhatsApp con los detalles.',

    'search_placeholder' => 'Buscar productos…',
    'no_results'         => 'No se encontraron productos coincidentes.',

    'wa_note'          => 'Al elegir un producto, WhatsApp se abre con los detalles y el enlace de la página: solo tienes que enviar el mensaje.',

    'form_alt_text'    => '¿No usas WhatsApp?',
    'form_alt_link'    => 'Enviar por el formulario',

    // {product} = producto (opcional "— variante"), {url} = enlace a la página del producto
    'wa_message'       => "*Solicitud de presupuesto · Barlas Trailer*\n\nProducto: *{product}*\nCategoría: {category}\nPágina del producto: {url}\n\nHola, me gustaría solicitar un presupuesto del producto indicado arriba.",
];
