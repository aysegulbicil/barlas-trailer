<?php

/**
 * Quote language strings (zh)
 *
 * Product-aware quote flow: product picker page copy and the WhatsApp
 * message template. The {product} and {url} tokens are filled in by the
 * Quote controller via a plain str_replace (no ICU formatting).
 */
return [
    'meta_title'       => '获取报价',
    'meta_description' => '选择您感兴趣的产品，通过 WhatsApp 快速获取报价。Barlas Trailer 销售团队当天回复。',

    'page_title'       => '获取报价',
    'page_lead'        => '选择您想询价的产品；我们将通过 WhatsApp 携产品详情快速回复您。',

    'search_placeholder' => '搜索产品……',
    'no_results'         => '未找到匹配的产品。',

    'wa_note'          => '选择产品后，WhatsApp 将自动打开并附带产品详情和页面链接——只需发送消息即可。',

    'form_alt_text'    => '不使用 WhatsApp？',
    'form_alt_link'    => '通过表单发送',

    // {product} = product (optionally "— variant"), {url} = product page link
    'wa_message'       => "*报价请求 · Barlas Trailer*\n\n产品：*{product}*\n分类：{category}\n产品页面：{url}\n\n您好，我想为以上产品索取报价。",
];
