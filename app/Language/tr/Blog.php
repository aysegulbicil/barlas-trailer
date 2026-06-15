<?php

/**
 * Blog language strings (tr)
 *
 * UI metinleri + yazı içerikleri. Yazı metinleri slug ile eşleşir;
 * slug, tarih, kategori ve görsel bilgisi Blog controller'daki
 * kayıt listesinde tutulur (tüm dillerde ortak URL için).
 */
return [
    // SEO
    'meta_title'       => 'Blog',
    'meta_description' => 'Treyler ve tanker sektöründen haberler, bakım ipuçları, teknoloji yazıları ve uzman rehberleri.',

    // Sayfa başlığı
    'page_title' => 'Blog',
    'page_lead'  => 'Sektörden haberler, bakım ipuçları ve mühendislik odaklı rehberler.',

    // Kenar çubuğu
    'search_title'       => 'Arama',
    'search_placeholder' => 'Blog yazılarında ara...',
    'search_submit'      => 'Ara',
    'categories_title'   => 'Kategoriler',
    'all_posts'          => 'Tüm Yazılar',

    // Kartlar ve liste durumları
    'read_more'        => 'Devamını Oku',
    'results_found'    => 'yazı bulundu',
    'no_results_title' => 'Sonuç bulunamadı',
    'no_results_text'  => 'Arama kriterlerinize uygun yazı bulunamadı. Farklı bir anahtar kelime deneyin veya tüm yazılara göz atın.',
    'clear_filters'    => 'Tüm yazıları göster',

    // Detay sayfası
    'related_title'   => 'İlgili Yazılar',
    'related_eyebrow' => 'Okumaya Devam Edin',
    'back_to_blog'    => 'Tüm Yazılara Dön',

    // Kategoriler
    'cat_industry'    => 'Sektör',
    'cat_maintenance' => 'Bakım & Servis',
    'cat_safety'      => 'Güvenlik & ADR',
    'cat_technology'  => 'Teknoloji',

    // Tarih biçimlendirme
    'months' => ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'],

    // Yazılar (slug => metinler)
    'posts' => [
        'adr-tanker-safety-standards' => [
            'title'   => 'ADR Sertifikalı Tankerlerde Güvenlik Standartları',
            'excerpt' => 'Tehlikeli madde taşımacılığında ADR mevzuatının tanker tasarımına etkileri ve operatörlerin dikkat etmesi gereken temel güvenlik unsurları.',
            'content' => '
                <p>Tehlikeli maddelerin karayoluyla taşınması, uluslararası ADR anlaşması ile sıkı kurallara bağlanmıştır. Akaryakıt, LPG ve kimyasal taşımacılığında kullanılan tankerlerin tasarımından üretimine, test süreçlerinden periyodik muayenelerine kadar her aşama bu mevzuat çerçevesinde yürütülür.</p>
                <h2>ADR Tanker Tasarımını Nasıl Şekillendirir?</h2>
                <p>ADR sertifikalı bir tankerde gövde malzemesi, et kalınlığı, bölme sayısı ve dalgakıran yerleşimi taşınacak ürünün sınıfına göre hesaplanır. Devrilme dayanımı, statik elektrik topraklaması, acil kapatma vanaları ve taşma önleme sistemleri tasarımın ayrılmaz parçalarıdır. Üretim sonrasında basınç ve sızdırmazlık testleri bağımsız kuruluşlarca doğrulanır.</p>
                <h2>Operatörlerin Dikkat Etmesi Gerekenler</h2>
                <p>Sertifika tek başına yeterli değildir; güvenlik, doğru kullanım ve düzenli denetimle sürdürülür. Operatörlerin öncelik vermesi gereken başlıca konular şunlardır:</p>
                <ul>
                    <li>Ara ve periyodik muayene tarihlerinin takibi</li>
                    <li>Vana, conta ve emniyet ekipmanlarının düzenli kontrolü</li>
                    <li>Dolum ve boşaltım prosedürlerine tam uyum</li>
                    <li>Sürücülerin ADR eğitim belgelerinin güncelliği</li>
                </ul>
                <p>Doğru tasarlanmış bir tanker ve bilinçli bir operasyon ekibi bir araya geldiğinde, tehlikeli madde taşımacılığı hem güvenli hem de verimli bir şekilde yürütülebilir.</p>
            ',
        ],

        'silo-trailer-maintenance-tips' => [
            'title'   => 'Silobas Bakımında Ömür Uzatan 7 İpucu',
            'excerpt' => 'Toz ve granül yük taşıyan silobasların verimli ve uzun ömürlü çalışması için uygulanması gereken temel bakım adımları.',
            'content' => '
                <p>Silobaslar; çimento, un, PVC ve benzeri toz veya granül ürünlerin taşınmasında yoğun şartlar altında çalışır. Basınçlı boşaltım sistemi ve hava hatları, düzenli bakım yapılmadığında verim kaybının ve arızaların ilk kaynağı haline gelir.</p>
                <h2>Düzenli Bakımın Önemi</h2>
                <p>Planlı bakım yalnızca arıza riskini azaltmakla kalmaz; boşaltım süresini kısaltır, yakıt tüketimini düşürür ve aracın ikinci el değerini korur. Aşağıdaki adımlar, sahada en sık karşılaşılan sorunların büyük bölümünü önler:</p>
                <ul>
                    <li>Hava hatları ve körüklerde sızıntı kontrolünü her seferde yapın</li>
                    <li>Kompresör yağ seviyesini ve filtrelerini düzenli kontrol edin</li>
                    <li>Boşaltım vanalarının contalarını aşınmaya karşı izleyin</li>
                    <li>Üst doldurma kapaklarının kilitlerini ve contalarını test edin</li>
                    <li>Tank içini ürün değişimlerinde mutlaka temizleyin</li>
                    <li>Emniyet ventilini yılda en az bir kez test ettirin</li>
                    <li>Şasi ve dingil bağlantılarını periyodik olarak torklayın</li>
                </ul>
                <h2>Profesyonel Servis Desteği</h2>
                <p>Günlük kontroller operatör tarafından yapılabilse de basınç sistemleri, ventiller ve yapısal kontroller uzman servis ekipleri tarafından yürütülmelidir. Üretici onaylı yedek parça kullanımı, sistemin ilk günkü performansını korumanın en güvenilir yoludur.</p>
            ',
        ],

        'lowbed-heavy-transport-guide' => [
            'title'   => 'Ağır Yük Taşımacılığında Lowbed Seçim Rehberi',
            'excerpt' => 'İş makinesi ve ağır ekipman taşımacılığında doğru lowbed konfigürasyonunu belirlemek için dikkate alınması gereken kriterler.',
            'content' => '
                <p>İş makineleri, jeneratörler, trafolar ve endüstriyel ekipmanların taşınması, standart treylerlerin sınırlarını aşan özel çözümler gerektirir. Lowbed treylerler, alçak yükleme yüksekliği ve yüksek taşıma kapasitesiyle bu alandaki en kritik araçlardır.</p>
                <h2>Doğru Konfigürasyonu Belirlemek</h2>
                <p>Lowbed seçiminde ilk soru her zaman aynıdır: Ne taşınacak? Yükün ağırlığı, boyutları ve ağırlık merkezi; dingil sayısından platform uzunluğuna, rampa tipinden genişletme ihtiyacına kadar tüm teknik kararları belirler. İki, üç ve dört dingilli sabit modellerin yanı sıra modüler ve uzayabilir platformlu çözümler de mevcuttur.</p>
                <h2>Seçimde Öne Çıkan Kriterler</h2>
                <ul>
                    <li>Maksimum yük kapasitesi ve dingil başına düşen ağırlık</li>
                    <li>Yükleme yüksekliği ve havuz uzunluğu</li>
                    <li>Hidrolik veya yaylı rampa seçenekleri</li>
                    <li>Kendinden dümenli (self-steering) dingil ihtiyacı</li>
                    <li>Güzergâhtaki köprü ve tünel kısıtlamaları</li>
                </ul>
                <p>Doğru konfigüre edilmiş bir lowbed, yalnızca yükü taşımakla kalmaz; operasyon güvenliğini artırır, geçiş izin süreçlerini kolaylaştırır ve uzun vadede işletme maliyetlerini düşürür. Seçim sürecinde üretici mühendislik ekibiyle birlikte çalışmak en sağlıklı sonucu verir.</p>
            ',
        ],

        'lightweight-chassis-technology' => [
            'title'   => 'Hafif Şasi Teknolojisi: Daha Az Ağırlık, Daha Fazla Yük',
            'excerpt' => 'Yüksek dayanımlı çelikler ve optimize şasi tasarımıyla boş ağırlığı düşürmenin taşımacılığa sağladığı operasyonel avantajlar.',
            'content' => '
                <p>Karayolu taşımacılığında yasal toplam ağırlık sınırları sabitken, kazanç treylerin boş ağırlığından elde edilir. Şasiden kazanılan her kilogram, doğrudan taşınabilir faydalı yüke dönüşür ve sefer başına gelirin artması anlamına gelir.</p>
                <h2>Yüksek Dayanımlı Çeliklerin Rolü</h2>
                <p>Modern treyler üretiminde standart yapı çeliklerinin yerini giderek yüksek dayanımlı (high-strength) çelikler alıyor. Bu malzemeler, daha ince kesitlerle aynı veya daha yüksek mukavemet değerleri sunar. Doğru kaynak prosedürleri ve hassas üretim teknikleriyle birleştiğinde, şasi ağırlığında ciddi düşüş sağlanırken yorulma ömrü korunur.</p>
                <h2>Tasarım Optimizasyonu</h2>
                <p>Malzeme tek başına yeterli değildir. Sonlu elemanlar analizi (FEA) ile şasi üzerindeki gerilme dağılımı modellenir; malzeme yalnızca ihtiyaç duyulan bölgelerde kullanılır. Boyuna kiriş geometrisi, traves yerleşimi ve bağlantı detayları bu analizlere göre optimize edilir.</p>
                <p>Sonuç; daha düşük yakıt tüketimi, daha fazla faydalı yük ve daha az karbon emisyonudur. Hafifletme, günümüz treyler mühendisliğinin hem ekonomik hem de çevresel açıdan en önemli başlıklarından biridir.</p>
            ',
        ],

        'reefer-cold-chain-logistics' => [
            'title'   => 'Soğuk Zincir Lojistiğinde Frigorifik Treyler Teknolojileri',
            'excerpt' => 'Gıda ve ilaç taşımacılığında soğuk zincirin kesintisiz korunmasını sağlayan modern frigorifik treyler çözümleri.',
            'content' => '
                <p>Gıda ve ilaç gibi sıcaklığa duyarlı ürünlerin taşınmasında en küçük bir sapma, ürün kaybına ve ciddi sağlık risklerine yol açabilir. Soğuk zincir lojistiğinin temel taşı olan frigorifik treylerler, bu hassas operasyonun kesintisiz yürümesini sağlar.</p>
                <h2>İzolasyon Kalitesi Her Şeyin Temeli</h2>
                <p>Bir frigorifik kasanın performansı, soğutma ünitesinden önce izolasyon kalitesiyle belirlenir. Yüksek yoğunluklu poliüretan dolgulu panel sistemleri, ısı köprülerini en aza indiren bağlantı detaylarıyla birleştiğinde, soğutma ünitesinin daha az çalışmasını ve yakıt tüketiminin düşmesini sağlar. Kapı contaları ve zemin yapısı da toplam ısı kaçağında belirleyici rol oynar.</p>
                <h2>İzleme ve Veri Kaydı</h2>
                <p>Modern soğuk zincir operasyonlarında sıcaklık takibi artık kabin göstergesiyle sınırlı değildir. Gerçek zamanlı telemetri sistemleri; kasa içi sıcaklığı, kapı açılışlarını ve soğutucu performansını uzaktan izlemeye olanak tanır. Bu veriler hem mevzuat uyumluluğu hem de müşteriye kalite kanıtı sunulması açısından giderek daha önemli hale gelmektedir.</p>
                <p>Doğru izolasyon, güvenilir soğutma ünitesi ve kesintisiz izleme bir araya geldiğinde, soğuk zincir en uzun güzergâhlarda dahi güvenle korunur.</p>
            ',
        ],

        'trailer-winter-maintenance' => [
            'title'   => 'Kış Şartlarına Hazırlık: Treyler Bakım Kontrol Listesi',
            'excerpt' => 'Düşük sıcaklık, tuz ve zorlu yol koşullarına karşı treyler filonuzu kış sezonuna hazırlamak için kapsamlı kontrol listesi.',
            'content' => '
                <p>Kış ayları; düşük sıcaklıklar, yol tuzu ve zorlu zemin koşullarıyla treylerler üzerinde yılın en yıpratıcı dönemini oluşturur. Sezon başlamadan yapılacak planlı bir hazırlık, kış ortasında yaşanacak yol kenarı arızalarının önüne geçer.</p>
                <h2>Fren ve Hava Sistemleri</h2>
                <p>Soğuk havada hava kurutucusunun önemi artar; kartuş zamanında değiştirilmezse hatlarda biriken nem donarak fren sistemini kilitleyebilir. Fren balataları, diskler ve otomatik cırcırlar sezon öncesinde mutlaka kontrol edilmelidir.</p>
                <h2>Sezon Öncesi Kontrol Listesi</h2>
                <ul>
                    <li>Hava kurutucu kartuşunu değiştirin, tankları tahliye edin</li>
                    <li>Fren balata kalınlıklarını ve disk yüzeylerini kontrol edin</li>
                    <li>Lastik diş derinliklerini ve basınçlarını ölçün</li>
                    <li>Aydınlatma ve elektrik bağlantılarını gözden geçirin</li>
                    <li>Şasiyi yıkayıp tuz korozyonuna karşı koruyucu uygulayın</li>
                    <li>Kapı menteşe ve kilitlerini gresleyin</li>
                </ul>
                <p>Düzenli kış hazırlığı, filo kullanılabilirliğini artırmanın ve araçların servis ömrünü uzatmanın en düşük maliyetli yoludur. Kapsamlı sezonluk kontroller için yetkili servis ağımızdan destek alabilirsiniz.</p>
            ',
        ],
    ],
];
