# Teklif Al — Ürün-Bağlamlı Teklif Sistemi (Plan)

> Durum: **PLAN** (uygulamaya geçilmedi). Hedef: "Teklif Al" butonlarını,
> tıklandığı ürünü tanıyan ve WhatsApp'a hazır mesajla yönlendiren bir akışa
> dönüştürmek.

---

## 1. Mevcut Durum (sistemde ne var)

- **Tüm "Teklif Al" butonları `/contact`'a gidiyor.** Header, ana sayfa CTA,
  ürün liste / kategori / detay ve hizmetler — hepsi `locale_url('contact')`.
- Sonuç: kullanıcı **belirli bir ürün detayında** "Teklif Al"a bastığında
  **hangi ürüne baktığı bilgisi kayboluyor**, genel iletişim formuna düşüyor.
- İletişim formu mesajı `writable/leads/YYYY-MM.jsonl`'e yazıp
  `info@barlastrailer.com`'a e-posta atıyor. Ayrı bir teklif yapısı, Model veya
  veritabanı **yok**.
- **WhatsApp zaten var:** `App\Libraries\AiAssistant.php` içinde
  `https://wa.me/<numara>?text=...` linki kuruluyor. Numara `Common.phone_raw`
  (`05525002000` → `905525002000`). 5 dilde `whatsapp_message` metinleri mevcut.
- Ürün kataloğu dosya tabanlı (`app/Data/products.json`): 11 kategori, ürün ve
  varyantlar. **Ürünlerin görsel alanı yok**; `lowbed-foto/` klasörü henüz
  bağlanmamış bir foto placeholder'ı.
- `partials/meta.php` `$metaImage` override destekliyor, ama **ürün detay
  sayfası şu an `$metaImage` geçmiyor** → OG önizlemesi varsayılan görseli
  kullanıyor.

---

## 2. WhatsApp Yönlendirme Fikri — Değerlendirme

**Mantıklı mı? Evet, bu kullanım için doğru tercih.** B2B, yüksek değerli /
düşük hacimli, satış odaklı bir iş; hedef pazarlar (TR / Rusya / Arap ülkeleri)
WhatsApp'ın baskın olduğu yerler. Form doldurma sürtünmesini kaldırır, sohbet
anında başlar, mevcut wa.me entegrasyonunu ve numarayı yeniden kullanır.

**Tek önemli teknik düzeltme — görsel:** wa.me click-to-chat linki
**sadece metin** ön-doldurur; ürün görselini dosya olarak otomatik ekleyemez.
İki seçenek var:

1. **(Önerilen) Mesaja ürün sayfası URL'sini koy.** WhatsApp, mesajdaki linki
   görünce sayfanın `og:image`'inden **otomatik önizleme kartı** üretir — ürün
   fotoğrafı önizlemede görünür. Sıfır ek altyapı. (Şart: ürün sayfası ürün
   fotoğrafını `og:image` olarak yayınlamalı — bkz. §6.)
2. Görseli gerçekten *gönderilmiş foto* olarak iletmek istiyorsanız bu
   **WhatsApp Business API** (Cloud API / sağlayıcı) gerektirir: sunucu tarafı,
   onaylı şablonlar, aylık maliyet. Bu kapsam için **gerekli değil, önermiyorum.**

**Sonuç:** wa.me + ürün URL'li mesaj + og:image önizleme = istediğiniz "görsel +
bilgi" deneyiminin %90'ı, ek maliyet olmadan.

---

## 3. Önerilen Mimari

### 3.1 Akış şeması

```
Ürün detay/kategori/liste "Teklif Al"
        │  (ürün + varyant biliniyor)
        ▼
  /{locale}/teklif/{kategori}/{slug}        ── Quote::start()
        │  ürünü bul → mesajı kur → (ops.) logla
        ▼
  302 → https://wa.me/905525002000?text=<hazır mesaj>
        ▼
  WhatsApp açılır, mesaj + ürün linki önizlemesi hazır → kullanıcı "Gönder"


Header / ana sayfa CTA / hizmetler "Teklif Al"
        │  (ürün belli değil)
        ▼
  /{locale}/teklif                          ── Quote::index()
        │  hafif ürün seçtirme (kategori → ürün)
        ▼   seçim yapılınca aynı start() akışına girer
```

### 3.2 Rotalar (`app/Config/Routes.php`, mevcut `{locale}` grubuna)

```php
$routes->get('teklif', 'Quote::index', ['as' => 'quote']);
$routes->get('teklif/(:segment)/(:segment)', 'Quote::start/$1/$2', ['as' => 'quote.start']);
// opsiyonel: belirli varyant için
$routes->get('teklif/(:segment)/(:segment)/(:segment)', 'Quote::start/$1/$2/$3');
```

`teklif` slug'ı ürün rotalarının desenini taklit eder (tahmin edilebilir,
SEO dostu). İstenirse ileride dile göre yerelleştirilebilir (`/en/quote`),
ama tek slug daha basit.

### 3.3 Controller — `App\Controllers\Quote`

- **`index()`** → hafif "ürün seç" sayfası (kategori → ürün). Header'daki global
  buton buraya gelir. Mevcut ürün grid partial'ları yeniden kullanılır.
- **`start($category, $slug, $variant = null)`**
  1. `ProductCatalog` ile ürünü bul (yoksa 404).
  2. *(Opsiyonel)* teklif niyetini logla → `writable/quotes/YYYY-MM.jsonl`
     (leads ile aynı desen).
  3. Yerelleştirilmiş WhatsApp mesajını kur.
  4. `https://wa.me/<numara>?text=<encoded>` adresine **302 redirect**.

### 3.4 Mesaj kurucu (paylaşılan helper / küçük Library)

- `AiAssistant::whatsappNumber()` mantığını **ortak bir helper'a çıkar**; hem AI
  hem Quote tek kaynaktan kullansın (numara tek yerde).
- Şablon `app/Language/{loc}/Quote.php` içinde. TR örneği:

  ```
  Merhaba, *{product}*{variant} için teklif almak istiyorum.
  Ürün: {url}
  — Barlas Trailer web sitesinden
  ```

  `{product}` ürün adı, `{variant}` varsa model, `{url}` ürün sayfası
  (önizleme kartını tetikler). 5 dilde (tr/en/ru/ar/fr) hazırlanır.

### 3.5 Buton değişiklikleri

| Konum | Şu an | Olacak |
|---|---|---|
| Ürün detay | `→ /contact` | `→ /teklif/{kategori}/{slug}` (+ seçili varyant) |
| Ürün kategori / liste kartı | `→ /contact` | `→ /teklif/{kategori}/{slug}` |
| Header / ana CTA / hizmetler | `→ /contact` | `→ /teklif` (ürün seçtir) |

Böylece **global tıklamalar bile ürün-nitelikli** teklife dönüşür.

---

## 4. Depolama Kararı (sorunuzun cevabı)

"Geniş çaplı teklif yönetimi olmayacak" dediğiniz için:

- **Veritabanı / admin panel KURMAYIN.** Asıl kayıt = WhatsApp sohbeti (zaten
  satış akışınızın içinde).
- Sadece **hafif bir sunucu logu** ekleyin: `writable/quotes/*.jsonl` (leads ile
  birebir aynı desen) — hangi ürün ne sıklıkta teklif tıklaması alıyor, dil,
  zaman damgası. Sıfır altyapı, analitik kazandırır, ileriye dönük güvenli.
- İleride hacim büyürse bu logu MySQL + basit bir admin listesine taşımak kolay.
  **Şimdilik gerek yok.**

> Özet: **Dosya tabanlı log + sistem-kaydı olarak WhatsApp.** v1 için DB yok.

---

## 5. Uç Durumlar & Yedekler

- **Masaüstünde WhatsApp uygulaması yoksa:** wa.me otomatik
  `web.whatsapp.com`'a düşer (yerleşik davranış).
- **WhatsApp istemeyen kullanıcı:** seçim/teklif sayfasında ikincil bir
  "Formla gönder" bağlantısı (mevcut `/contact`) bırakılır.
- **Bot/kötüye kullanım:** redirect endpoint'i GET ve zararsız (sadece link
  kurar); opsiyonel niyet logu dışında PII saklanmaz.
- **İleride:** mesaj kurucu, ürün kategorisine göre farklı satış numarasına
  yönlendirecek şekilde kolayca genişletilebilir.

---

## 6. og:image Düzeltmesi (zengin önizleme için — opsiyonel ama değerli)

WhatsApp önizlemesinde **ürün fotoğrafının** çıkması için:

1. `products.json`'a ürün başına `image` alanı ekle **veya** konvansiyon
   belirle: `assets/images/products/{slug}.jpg`.
2. `ProductCatalog`'u bu alanı döndürecek şekilde bağla.
3. Ürün detay controller'ında `$metaImage = <ürün görseli>` geçir
   (meta.php zaten destekliyor).

Görseller hazır olana kadar önizleme varsayılan OG görseline düşer — akış yine
çalışır, sadece ürüne özel olmaz. (`lowbed-foto/` klasörü bu iş için bekliyor.)

---

## 7. Uygulama Adımları (onay sonrası — şu an YAPILMIYOR)

1. `whatsappNumber()`'ı ortak helper/Library'ye çıkar.
2. `Quote` controller + rotaları ekle.
3. `Language/{loc}/Quote.php` mesaj şablonları (5 dil).
4. Ürün seçtirme sayfası (mevcut grid partial'larını kullan).
5. "Teklif Al" butonlarını yeni rotalara bağla.
6. *(Ops.)* teklif-niyeti loglaması.
7. *(Ops.)* ürün görsel alanı + og:image bağlama.
8. Test: her dil, ürün/varyant, header seçtirme, masaüstü + mobil WhatsApp açılışı.

## 8. Tahmini Efor

- Çekirdek akış (rota + controller + şablon + buton bağlama): **~yarım gün**.
- Ürün seçtirme sayfası: **~2-3 saat**.
- Ürün görseli / og:image bağlama: fotoğrafların hazır olmasına bağlı.

---

*Karar bekleyen tek nokta:* §3.5'te header / CTA / hizmet butonları **ürün
seçtirme sayfasına** mı gitsin (önerilen), yoksa mevcut `/contact` formunda mı
kalsın? Gerisi nettir.
