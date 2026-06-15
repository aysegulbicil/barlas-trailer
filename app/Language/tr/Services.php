<?php

/**
 * Services language strings (tr)
 *
 * Page-level copy + per-service general content for the services list
 * and detail pages. Service NAMES and one-line descriptions are NOT
 * duplicated here — they come from Navigation.srv_* (shared with the
 * mega menu). "items" is keyed by the service slug used in URLs.
 *
 * body = trusted developer-authored HTML (rendered raw in the view).
 */
return [
    'meta_title'       => 'Hizmetlerimiz',
    'meta_description' => 'Barlas servis: her marka dorse, tanker, silobas, frigo ve lowbed için profesyonel bakım, onarım ve revizyon.',

    'page_title'       => 'Hizmetlerimiz',
    'page_lead'        => 'Her marka treyler ve üst yapı için uçtan uca bakım, onarım ve revizyon hizmetleri.',

    'card_more'        => 'Detayları gör',
    'detail_eyebrow'   => 'Hizmet',
    'whatwedo_title'   => 'Neler yapıyoruz',
    'cta_title'        => 'Aracınız için hızlı çözüm',
    'cta_text'         => 'Uzman ekibimiz aracınız için hazır; hızlı dönüş ve güvenilir işçilik sağlıyoruz.',
    'call_button'      => 'Hemen Ara',
    'related_title'    => 'Diğer hizmetler',
    'back_to_services' => 'Tüm hizmetler',

    'items' => [
        'trailer-repair' => [
            'lead'   => 'Her marka dorse için kapsamlı bakım, onarım ve revizyon.',
            'body'   => '<p>Barlas servis ekibi; şasi, üst yapı, fren ve elektrik sistemleri dahil her marka dorsede uçtan uca bakım ve onarım yapar. Arızayı doğru teşhis eder, orijinal veya eşdeğer parçayla kalıcı çözüm sunarız.</p><p>Periyodik bakımdan ağır hasar onarımına kadar tüm işlemleri, aracınızı en kısa sürede güvenle yola çıkaracak şekilde planlarız.</p>',
            'points' => ['Şasi düzeltme ve kaynak onarımı', 'Fren, aks ve elektrik sistemleri', 'Periyodik bakım ve revizyon', 'Her marka için yedek parça'],
        ],
        'frigo-repair' => [
            'lead'   => 'Frigorifik kasa, yalıtım ve soğutucu ünite onarımı.',
            'body'   => '<p>Soğuk zincirin kesintisiz kalması için frigorifik kasalarınızın panel, yalıtım ve soğutma ünitesi onarımını modern ekipmanlarla yaparız. Sızdırmazlık ve ısı yalıtımını yeniden kazandırarak verimi artırırız.</p><p>Hızlı ve etkili müdahaleyle kasanızı en yüksek performansta tutmayı hedefleriz.</p>',
            'points' => ['Panel ve yalıtım onarımı', 'Soğutucu ünite bakımı', 'Sızdırmazlık ve kapı contası', 'Zemin ve iç kaplama yenileme'],
        ],
        'silobas-repair' => [
            'lead'   => 'Silobas gövde ve boşaltma sistemi onarımı.',
            'body'   => '<p>Toz ve granül yük taşıyan silobaslarınızda gövde, boşaltma sistemi ve hava hatlarının onarımını profesyonel ekiple gerçekleştiririz. Dayanıklılığı ve boşaltma performansını yeniden kazandırırız.</p><p>Düzenli bakımla araç ömrünü uzatır, sahada duruş süresini en aza indiririz.</p>',
            'points' => ['Gövde ve konik onarımı', 'Boşaltma ve hava sistemi', 'Kaynak ve sızdırmazlık', 'Kapak ve menhol bakımı'],
        ],
        'curtain-repair' => [
            'lead'   => 'Tente, perde ve mekanizma yenileme.',
            'body'   => '<p>Tenteli dorse ve çadır sistemlerinde branda, perde, kayar çatı ve mekanizma onarımını yaparız. Yıpranan parçaları yenileyerek hızlı yükleme-boşaltma konforunu geri kazandırırız.</p><p>İhtiyaca göre dijital baskılı branda ve tente yazısı çözümleri de sunarız.</p>',
            'points' => ['Branda ve perde değişimi', 'Kayar çatı ve mekanizma', 'Çadır tamiri', 'Dijital baskılı branda'],
        ],
        'adr-tanker-repair' => [
            'lead'   => 'ADR standartlarında sertifikalı tanker onarımı.',
            'body'   => '<p>Tehlikeli madde taşıyan tankerlerin onarımını ADR standartlarına uygun, sertifikalı süreçlerle yaparız. Gövde, ekipman ve güvenlik donanımını mevzuata uygun şekilde yenileriz.</p><p>Muayeneye hazırlık dahil tüm adımlarda güvenliği önceliklendiririz.</p>',
            'points' => ['ADR uyumlu gövde onarımı', 'Ekipman ve donanım yenileme', 'Muayene öncesi hazırlık', 'Sızdırmazlık testleri'],
        ],
        'lowbed-repair' => [
            'lead'   => 'Lowbed şasi ve hidrolik sistem onarımı.',
            'body'   => '<p>Ağır iş makinesi ve gabari dışı yük taşıyan lowbedlerin şasi, rampa ve hidrolik sistemlerini onarırız. Yük altında güvenli çalışma için kritik bağlantıları kontrol eder, gerekli takviyeleri yaparız.</p><p>Aracınızı sorunsuz ve güvenli çalışır halde teslim ederiz.</p>',
            'points' => ['Şasi ve rampa onarımı', 'Hidrolik sistem bakımı', 'Aks ve süspansiyon kontrolü', 'Takviye ve kaynak'],
        ],
        'aluminum-silobas-repair' => [
            'lead'   => 'Alüminyum gövdelerde uzman kaynak ve onarım.',
            'body'   => '<p>Alüminyum silobas ve gövdelerde uzmanlık gerektiren kaynak ve onarım işlerini deneyimli ekiple yaparız. Malzeme bütünlüğünü koruyarak kalıcı ve güvenli sonuçlar elde ederiz.</p><p>Her projenin ihtiyacına uygun çözüm geliştiririz.</p>',
            'points' => ['Alüminyum kaynak', 'Gövde ve konik onarımı', 'Çatlak ve deformasyon giderme', 'Yüzey ve sızdırmazlık'],
        ],
        'fuel-tank-repair-center' => [
            'lead'   => 'Akaryakıt tankerlerine özel tamir merkezi.',
            'body'   => '<p>Akaryakıt tankerleri için ayrılmış özel merkezimizde gövde, bölme ve ekipman onarımını güvenli şartlarda gerçekleştiririz. Deneyimli ekibimiz hızlı ve güvenilir çözüm sunar.</p><p>Ankara ve çevresine özel, tankere odaklı bir servis deneyimi sağlarız.</p>',
            'points' => ['Gövde ve bölme onarımı', 'Pompa ve donanım', 'Güvenli ortamda müdahale', 'Periyodik bakım'],
        ],
        'axle-repair' => [
            'lead'   => 'Dingil ve aks sistemleri bakım-onarımı.',
            'body'   => '<p>Dingil, aks ve ilgili bağlantı parçalarının bakım ve onarımını yaparız. Bu hizmetin detaylı içeriği yakında güncellenecek; bilgi için bizimle iletişime geçebilirsiniz.</p>',
            'points' => ['Dingil bakımı', 'Aks ayar ve onarımı', 'Rulman ve fren bağlantıları'],
        ],
        'suspension-repair' => [
            'lead'   => 'Kulak ve süspansiyon bağlantıları onarımı.',
            'body'   => '<p>Kulak ve süspansiyon bağlantı elemanlarının onarım ve yenileme işlemlerini yaparız. Bu hizmetin detaylı içeriği yakında güncellenecek; bilgi için bizimle iletişime geçebilirsiniz.</p>',
            'points' => ['Kulak (askı) onarımı', 'Süspansiyon bağlantıları', 'Burç ve makas kontrolü'],
        ],
    ],
];
