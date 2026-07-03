<?php

/**
 * Quote language strings (el)
 *
 * Product-aware quote flow: product picker page copy and the WhatsApp
 * message template. The {product} and {url} tokens are filled in by the
 * Quote controller via a plain str_replace (no ICU formatting).
 */
return [
    'meta_title'       => 'Ζητήστε Προσφορά',
    'meta_description' => 'Επιλέξτε το προϊόν που σας ενδιαφέρει και λάβετε προσφορά γρήγορα μέσω WhatsApp. Η ομάδα πωλήσεων της Barlas Trailer απαντά την ίδια μέρα.',

    'page_title'       => 'Ζητήστε Προσφορά',
    'page_lead'        => 'Επιλέξτε το προϊόν για το οποίο θέλετε προσφορά· θα σας απαντήσουμε γρήγορα μέσω WhatsApp με τις λεπτομέρειες του προϊόντος.',

    'search_placeholder' => 'Αναζήτηση προϊόντων…',
    'no_results'         => 'Δεν βρέθηκαν προϊόντα που να ταιριάζουν.',

    'wa_note'          => 'Όταν επιλέξετε ένα προϊόν, το WhatsApp ανοίγει με τις λεπτομέρειες του προϊόντος και τον σύνδεσμο της σελίδας — απλώς στείλτε το μήνυμα.',

    'form_alt_text'    => 'Δεν χρησιμοποιείτε WhatsApp;',
    'form_alt_link'    => 'Αποστολή μέσω της φόρμας',

    // {product} = product (optionally "— variant"), {url} = product page link
    'wa_message'       => "*Αίτημα Προσφοράς · Barlas Trailer*\n\nΠροϊόν: *{product}*\nΚατηγορία: {category}\nΣελίδα προϊόντος: {url}\n\nΓεια σας, θα ήθελα να ζητήσω προσφορά για το παραπάνω προϊόν.",
];
