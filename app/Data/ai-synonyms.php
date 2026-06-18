<?php

/**
 * Yapay zeka asistanı — çok dilli / argo eş anlamlı eşlemesi.
 *
 * Ürün kataloğu (products.json) Türkçedir. Bir ziyaretçi farklı dilde ya da
 * günlük bir kelime yazdığında ("reefer", "frigo", "цистерна", "صهريج",
 * "porte-engins"...) eşleşmeyi sağlamak için, o tetikleyici kelimeleri
 * kataloğa uyan Türkçe anahtarlarla ilişkilendiriyoruz.
 *
 * Her grup:
 *   'inject'   => arama yapılırken EKLENECEK Türkçe token(lar) (kataloğdaki
 *                ürün adı/aliaslarında geçen kelimeler)
 *   'triggers' => bu grubu tetikleyen kelimeler (tr/en/fr/ru/ar + argo)
 *
 * Kelimeler küçük harfe çevrilip karşılaştırılır. Yeni satır ekleyerek
 * asistanı serbestçe "öğretebilirsiniz".
 */
return [
    ['inject' => ['tanker'],            'triggers' => ['tanker', 'tank', 'tanksi', 'citerne', 'цистерна', 'صهريج', 'صهاريج']],
    ['inject' => ['akaryakit', 'tanker'], 'triggers' => ['akaryakit', 'yakit', 'benzin', 'mazot', 'dizel', 'fuel', 'diesel', 'petrol', 'carburant', 'топливо', 'وقود', 'بنزين']],
    ['inject' => ['su', 'tanker'],      'triggers' => ['su', 'arazoz', 'water', 'eau', 'вода', 'ماء', 'مياه']],
    ['inject' => ['sut', 'tanker'],     'triggers' => ['sut', 'milk', 'lait', 'молоко', 'حليب']],
    ['inject' => ['kimyasal', 'tanker'], 'triggers' => ['kimyasal', 'asit', 'chemical', 'acid', 'chimique', 'химия', 'химический', 'كيميائي', 'حمض']],
    ['inject' => ['adr'],               'triggers' => ['adr', 'tehlikeli', 'dangerous', 'hazardous', 'dangereux', 'опасный', 'خطرة', 'خطير']],

    ['inject' => ['damper'],            'triggers' => ['damper', 'damperli', 'tipper', 'dump', 'benne', 'самосвал', 'قلاب', 'قلابة', 'hafriyat', 'maden', 'kaya', 'kum', 'cakil']],

    ['inject' => ['silobas', 'silo'],   'triggers' => ['silo', 'silobas', 'bulk', 'silos', 'صومعة', 'صوامع', 'бункер']],
    ['inject' => ['cimento', 'silobas'], 'triggers' => ['cimento', 'cement', 'ciment', 'цемент', 'اسمنت', 'إسمنت']],
    ['inject' => ['un', 'silobas'],     'triggers' => ['un', 'flour', 'farine', 'мука', 'دقيق', 'yem', 'feed', 'bugday', 'hububat', 'grain', 'wheat']],

    ['inject' => ['lowbed'],            'triggers' => ['lowbed', 'low', 'lowloader', 'lowloder', 'porte', 'трал', 'منخفض', 'منخفضة', 'havuzlu', 'havuz', 'platform']],
    ['inject' => ['lowbed'],            'triggers' => ['makine', 'makinesi', 'dozer', 'ekskavator', 'excavator', 'engins', 'экскаватор', 'حفارة', 'is', 'agir']],

    ['inject' => ['konteyner'],         'triggers' => ['konteyner', 'container', 'conteneur', 'контейнер', 'حاوية', 'حاويات']],

    ['inject' => ['tenteli', 'perdeli'], 'triggers' => ['tente', 'tenteli', 'perde', 'perdeli', 'curtain', 'tilt', 'bachee', 'bache', 'тент', 'шторный', 'ستارة', 'ستائري']],

    ['inject' => ['frigorifik'],        'triggers' => ['frigo', 'frigorifik', 'reefer', 'refrigerated', 'frigorifique', 'рефрижератор', 'مبرد', 'مبردة', 'sogutucu', 'soguk', 'cold']],

    ['inject' => ['kuruyuk', 'kasa'],   'triggers' => ['kuruyuk', 'kuru', 'dry', 'sandik', 'box', 'kasa', 'panelvan', 'фургон', 'صندوق']],

    ['inject' => ['platform'],          'triggers' => ['platform', 'plato', 'flatbed', 'plateau', 'منصة']],
];
