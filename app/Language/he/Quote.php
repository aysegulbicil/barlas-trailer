<?php

/**
 * Quote language strings (he)
 *
 * Product-aware quote flow: product picker page copy and the WhatsApp
 * message template. The {product} and {url} tokens are filled in by the
 * Quote controller via a plain str_replace (no ICU formatting).
 */
return [
    'meta_title'       => 'קבלת הצעת מחיר',
    'meta_description' => 'בחרו את המוצר שמעניין אתכם וקבלו הצעת מחיר במהירות דרך WhatsApp. צוות המכירות של Barlas Trailer משיב עוד באותו יום.',

    'page_title'       => 'קבלת הצעת מחיר',
    'page_lead'        => 'בחרו את המוצר שעבורו תרצו הצעת מחיר; נשיב במהירות ב-WhatsApp עם פרטי המוצר.',

    'search_placeholder' => 'חיפוש מוצרים…',
    'no_results'         => 'לא נמצאו מוצרים תואמים.',

    'wa_note'          => 'כאשר בוחרים מוצר, WhatsApp נפתח עם פרטי המוצר וקישור לעמוד — נותר רק לשלוח את ההודעה.',

    'form_alt_text'    => 'לא משתמשים ב-WhatsApp?',
    'form_alt_link'    => 'שליחה דרך הטופס',

    // {product} = product (optionally "— variant"), {url} = product page link
    'wa_message'       => "*בקשת הצעת מחיר · Barlas Trailer*\n\nמוצר: *{product}*\nקטגוריה: {category}\nעמוד המוצר: {url}\n\nשלום, אשמח לקבל הצעת מחיר עבור המוצר שלמעלה.",
];
