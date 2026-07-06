# Barlas Teklif Oluşturma Sistemi — Planlama Dokümanı

**Hazırlanma tarihi:** 23.06.2026
**Durum:** Planlama (uygulamaya henüz geçilmedi)
**Referans çıktı:** `Vtipi silobas teklif-1.pdf`

---

## 1. Amaç

Seçilen ürüne göre teknik özellikleri **otomatik dolduran**, alanları **düzenlenebilir / silinebilir / eklenebilir** yapan ve sonucu örnekteki gibi bir **PDF teklif** olarak üreten yerel bir uygulama. Her teklif **kendi adına bir klasöre** kaydedilir; geçmiş teklifler sonradan açılıp güncellenebilir. Sistem `barlas-trailer` projesinin **dışında**, bağımsız bir klasörde yaşar.

### Verdiğin kararlar (bu plan bunlara göre yazıldı)

| Karar | Seçim |
|---|---|
| Çalışma şekli | **Yerel uygulama** — bilgisayarda küçük PHP sunucu, `.bat` ile tek tıkla açılır. Klasör oluşturma + diske kayıt tam çalışır. |
| Ürün kapsamı | **Tümü hedef**, ama **önce 1 deneme ürünü** (V Tipi Silobas) ile başlanacak. |
| Geçmiş teklifler | **Düzenlenebilir** — her klasörde PDF **+** düzenlenebilir veri (JSON). |
| PDF görünümü | **Sade / modern tasarım** (Barlas kurumsal mavisi `#005BAA`). |

---

## 2. Konum ve Klasör Yapısı

Sistem, `barlas-trailer`'ın **kardeşi** olarak ayrı bir klasörde durur:

```
C:\Users\precocious\Desktop\
├── barlas-trailer\              ← mevcut site (DOKUNULMAZ, sadece görsel/veri kaynağı)
└── Barlas-Teklif-Sistemi\       ← YENİ sistem (buraya kurulacak)
```

### `Barlas-Teklif-Sistemi\` içeriği

```
Barlas-Teklif-Sistemi\
├── baslat.bat                  → tek tıkla yerel sunucuyu başlatır + tarayıcıyı açar
├── DURDUR.bat                  → sunucuyu kapatır (opsiyonel)
│
├── sunucu\                     → PHP arka uç (backend)
│   ├── index.php               → yönlendirici / API
│   ├── api\
│   │   ├── kaydet.php          → teklif klasörü oluştur + JSON + PDF üret
│   │   ├── listele.php         → geçmiş teklifleri listele
│   │   ├── ac.php              → eski teklifi yükle (düzenleme için)
│   │   └── sil.php             → teklif klasörünü sil
│   └── lib\                    → PDF kütüphanesi (Dompdf)
│
├── arayuz\                     → kullanıcı ekranı (frontend)
│   ├── index.html              → teklif oluşturma / düzenleme ekranı
│   ├── app.js                  → mantık (ürün seç → otomatik dolum → kaydet)
│   ├── stil.css                → arayüz tasarımı
│   └── pdf-sablon.html         → PDF'in HTML şablonu (modern mavi tasarım)
│
├── veri\
│   ├── urunler.json            → ürün listesi + görsel yolları (katalogdan türetilir)
│   ├── sablonlar.json          → her ürünün varsayılan teknik özellik satırları
│   ├── firma.json              → Barlas firma bilgileri (logo, adres, tel, IBAN, KDV %)
│   └── sayac.json              → teklif numarası sayacı
│
├── gorseller\                  → ürün görselleri (barlas-trailer'dan kopyalanır)
│
└── Teklifler\                  → ÇIKTILAR — her teklif kendi klasöründe
    ├── 2026-06-23_Ahmet-Yilmaz_TKL-2026-0001\
    │   ├── teklif.json         → düzenlenebilir veri (yeniden açmak için)
    │   ├── teklif.pdf          → son çıktı
    │   └── teklif.html         → tarayıcıda önizleme
    └── 2026-06-24_XYZ-Lojistik_TKL-2026-0002\
        └── ...
```

**Teklif klasörü adı formatı:** `TARİH_MüşteriAdı_TeklifNo`
örn. `2026-06-23_Ahmet-Yilmaz_TKL-2026-0001`
(Tarih otomatik = oluşturulduğu gün; teklif no otomatik artar.)

---

## 3. Mimari ve Çalışma Mantığı

Neden yerel sunucu? Tarayıcı tek başına diske serbestçe klasör açıp dosya yazamaz. Küçük bir **yerel PHP sunucusu** bunu sorunsuz yapar; üstelik `barlas-trailer` zaten PHP tabanlı, yani makinede PHP kurulu.

### Akış

```
[baslat.bat]
     │  php -S localhost:8765  (yerel sunucu açılır)
     │  start http://localhost:8765   (tarayıcı açılır)
     ▼
[Tarayıcı arayüzü]  ──(1) ürün seç──▶  sablonlar.json'dan özellikler OTOMATIK dolar
     │
     │  (2) kullanıcı: satır düzenle / sil / ekle, fiyat + KDV, müşteri bilgisi
     │
     │  (3) "Kaydet ve PDF Oluştur"
     ▼
[PHP: kaydet.php]
     │  • Teklifler\TARİH_Müşteri_No\ klasörünü oluştur
     │  • teklif.json yaz (düzenlenebilir veri)
     │  • pdf-sablon.html'i verilerle doldur → Dompdf ile teklif.pdf üret
     │  • teklif.html (önizleme) yaz
     ▼
[Arayüz]  ✓ "Teklif kaydedildi" + klasörü açma bağlantısı
```

### Teknoloji seçimleri

- **Arka uç:** PHP (yerleşik sunucu `php -S`, ek kurulum yok). Mevcut projeyle aynı dil.
- **PDF üretimi:** **Dompdf** (saf PHP, HTML+CSS'ten PDF üretir). Mavi tema, tablo, filigran, logo — hepsi HTML/CSS ile rahatça yapılır. Tek seferlik `vendor` olarak gömülür, internet gerektirmez.
- **Ön yüz:** Sade HTML + JavaScript (çerçeve yok, hızlı ve bakımı kolay). İsteğe göre canlı önizleme.
- **Başlatıcı:** `baslat.bat` — çift tıkla her şey açılır; teknik bilgi gerektirmez.

---

## 4. Veri Modeli

### 4.1 `urunler.json` — ürün kataloğu
`barlas-trailer/app/Data/products.json` (11 kategori, ~76 ürün) sadeleştirilerek türetilir:

```jsonc
{
  "kategoriler": [
    {
      "slug": "silos",
      "ad": "Silobas",
      "urunler": [
        {
          "no": "2.6",
          "ad": "V Tipi Silobas",
          "gorsel": "gorseller/silos-v-tipi-silobas.jpg",
          "varyantlar": ["V Tipi Silobas", "3 Dingilli V Tipi", "..."]
        }
      ]
    }
  ]
}
```
> Görsel eşleşmesi mevcut isimlendirmeyle birebir uyuyor: `silos-v-tipi-silobas.jpg`, `silos-damperli-silobas.jpg` vb.

### 4.2 `sablonlar.json` — otomatik dolan teknik özellikler
Her ürün için **varsayılan satırlar** (etiket → değer). Örnek PDF'teki yapı:

```jsonc
{
  "v-tipi-silobas": {
    "siparis_konusu": "1 ADET ; 3 Dingilli 35 metre küp V tipi silobas",
    "ozellikler": [
      { "etiket": "KAPASİTE",          "deger": "35 metre küp" },
      { "etiket": "MARKA",             "deger": "BARLAS TANKER" },
      { "etiket": "MODEL",             "deger": "2026" },
      { "etiket": "ÇALIŞMA SICAKLIĞI", "deger": "-40 C° / +80 C°" },
      { "etiket": "ŞASİ",              "deger": "Yüksek mukavemetli örme şasi ST52..." },
      { "etiket": "KAZAN",             "deger": "Yüksek mukavemetli ST52(6052)..." }
      // ... DİNGİL, FREN, KİNG PİN, LASTİK, JANT, BOŞALTMA, DOLUM, BOYA, AKSESUARLAR ...
    ],
    "fiyat": { "net": 1050000, "kdv_orani": 20, "para_birimi": "TL" },
    "odeme": "PEŞİN",
    "teslimat": "Ödeme alındıktan sonra 4 gün içinde teslim edilebilir."
  }
}
```

Bu şablonlar, mevcut `products.json` içindeki `specs` (etiket = `p`, değer = `li` satırları) ve `TUM_URUNLER_HIYERARSIK_KATALOG.docx`'tan otomatik türetilip elle rötuşlanır.

### 4.3 `teklif.json` — kaydedilen tekil teklif
Şablonun, kullanıcının düzenlemeleriyle birlikte donmuş hâli + müşteri/no/tarih. Geçmiş teklifi yeniden açmak bu dosyayı forma geri yüklemektir.

```jsonc
{
  "teklif_no": "TKL-2026-0001",
  "tarih": "2026-06-23",
  "musteri": { "ad": "Ahmet Yılmaz", "firma": "...", "telefon": "..." },
  "urun": { "kategori": "Silobas", "ad": "V Tipi Silobas", "gorsel": "..." },
  "siparis_konusu": "1 ADET ; 3 Dingilli 35 metre küp V tipi silobas",
  "ozellikler": [ { "etiket": "KAPASİTE", "deger": "35 metre küp" }, ... ],
  "fiyat": { "net": 1050000, "kdv_orani": 20, "kdv": 210000, "toplam": 1260000 },
  "odeme": "PEŞİN", "teslimat": "..."
}
```

---

## 5. Otomatik Doldurma & Düzenleme Mantığı

1. **Tarih otomatik:** Teklif, oluşturulduğu günün tarihini alır (sistem saatinden). PDF başlığına ve klasör adına yazılır.
2. **Ürün seçimi → otomatik dolum:** Kategori + ürün seçilince `sablonlar.json`'daki tüm özellik satırları, fiyat ve ödeme bilgileri forma otomatik yüklenir.
3. **Her alan düzenlenebilir:** Etiket ve değer kutuları serbestçe değiştirilebilir.
4. **Satır silme:** Her satırın yanında **✕** ile o özellik silinir.
5. **Satır ekleme:** "+ Özellik ekle" ile boş satır eklenir.
6. **Ürün görseli otomatik:** Ürün seçilince ilgili fotoğraf (`gorseller\`) teklife otomatik eklenir. İstersen görseli kaldırabilir, değiştirebilir veya kendi fotoğrafını yükleyebilirsin.
7. **Fiyat & KDV otomatik:** Net fiyat girilince KDV (varsayılan %20) ve KDV dâhil toplam otomatik hesaplanır (örn. 1.050.000 → +%20 → 1.260.000).
8. **Teklif no otomatik:** `sayac.json` üzerinden sıradaki numara (TKL-2026-0001, -0002 ...). İstenirse elle değiştirilebilir.

---

## 6. Kullanıcı Akışı (Adım Adım)

**Yeni teklif:**
1. `baslat.bat`'a çift tıkla → tarayıcı açılır.
2. "Yeni Teklif" → Kategori (Silobas) → Ürün (V Tipi Silobas) seç.
3. Özellikler otomatik dolar → istediğini düzenle / sil / ekle.
4. Müşteri adını, fiyatı gir (KDV otomatik). Tarih ve teklif no zaten hazır.
5. (Opsiyonel) Canlı önizlemeye bak.
6. "Kaydet ve PDF Oluştur" → `Teklifler\...\` klasörü oluşur, PDF + JSON yazılır.
7. "Klasörü Aç" bağlantısıyla teslim edilecek PDF'e ulaş.

**Eski teklifi düzenleme:**
1. "Geçmiş Teklifler" → listeden seç → "Aç".
2. Form, o teklifin verisiyle dolar → değişiklik yap → "Güncelle" → PDF yeniden üretilir.

---

## 7. PDF Tasarımı (Sade / Modern)

Örnekteki bilgiyi korur, görünümü modernleştirir. Kurumsal renk: **`#005BAA`** (koyu ton `#00467f`, açık vurgu `#0A6DC4`).

**Düzen:**
- **Üst bant:** Solda Barlas logosu, sağda "FİYAT TEKLİFİ", altında Teklif No + Tarih. İnce mavi vurgu çizgisi.
- **Müşteri kutusu:** Müşteri/firma adı, telefon (sade, açık gri).
- **Sipariş Konusu:** Mavi vurgulu tek satırlık kart.
- **Ürün görseli:** Seçili ürünün fotoğrafı teklife eklenir (Sipariş Konusu altında veya başlık yanında). Kaldırılabilir / değiştirilebilir; istenirse kendi fotoğrafın yüklenebilir.
- **Teknik Özellikler:** Çift sütunlu, **zebra (gri/beyaz)** satırlı temiz tablo — örnekteki kalın mavi kutucuklar yerine ince ayraçlar; etiket sütunu soft, değer sütunu koyu.
- **Fiyat ve Ödeme:** Özet kart — Net, KDV (%20), **KDV Dâhil Toplam** (kalın, mavi vurgu), ödeme şekli, teslimat.
- **Alt bilgi:** Firma adı, adres, telefon, web; hafif `www.barlastanker.com` filigranı.
- **Tipografi:** Temiz sans-serif, ferah satır aralığı.

> Not: Kesin logo ve renkler kurulum aşamasında `barlas-trailer/logo` ve `marka` klasörlerinden alınacak.

---

## 8. Geliştirme Yol Haritası (Fazlar)

| Faz | İçerik | Çıktı |
|---|---|---|
| **Faz 0** | Klasör iskeleti + `baslat.bat` + boş PHP sunucu + Dompdf kurulumu | Tarayıcıda boş ekran açılır |
| **Faz 1 — Deneme** | **Tek ürün (V Tipi Silobas)** şablonu + arayüz + otomatik dolum + düzenle/sil/ekle + fiyat/KDV | Örnek PDF'in modern eşdeğeri üretilir |
| **Faz 2** | Kaydetme: teklif klasörü + JSON + PDF; geçmiş listeleme + yeniden açma/düzenleme | Tam kayıt döngüsü çalışır |
| **Faz 3** | Tüm Silobas kategorisi (9 ürün) şablonları | Silobas ürünleri tam |
| **Faz 4** | Kalan 10 kategori şablonları (katalogdan toplu türetme) | ~76 ürün hazır |
| **Faz 5** | Cila: önizleme, arama, PDF ince ayarları, yedekleme | Tamamlanmış sistem |

> Sen "önce denemelik bir ürün" dediğin için **Faz 1**, doğrulama noktası olacak: orada PDF'i beğenince diğer ürünlere/kategorilere ölçeklenir.

---

## 9. Teknik Gereksinimler

- **PHP** (8.x) makinede kurulu olmalı — `barlas-trailer` PHP projesi olduğundan büyük ihtimalle var. Kurulumda kontrol edilir; yoksa tek seferlik kurulum adımı eklenir.
- **Modern tarayıcı** (Chrome / Edge) — arayüz için.
- İnternet **gerekmez**; sistem tamamen yerelde çalışır (görseller ve PDF kütüphanesi gömülü).

---

## 10. Onay Bekleyen / Senden Gerekenler

Uygulamaya geçince netleştirilecek küçük noktalar:

1. **Kurulum klasörüne erişim:** Sistem `barlas-trailer` dışında oluşacağı için, `Barlas-Teklif-Sistemi` klasörünü Masaüstünde oluşturabilmem adına o konuma erişim izni vermen gerekecek (şu an sadece `barlas-trailer`'a erişimim var).
2. **Firma bilgileri:** PDF alt bilgisinde görünecek resmî firma adı, adres, telefon, web, (varsa) IBAN ve vergi dairesi/no.
3. **KDV oranı:** Varsayılan %20 — onayın yeterli.
4. **Deneme ürünü:** V Tipi Silobas ile başlıyoruz (örnek PDF ile birebir karşılaştırmak için ideal).
5. **Logo tercihi:** `logo_horizontal.png` mi yoksa amblem mi kullanılsın?

---

## 11. Sonraki Adım

Onayın gelince sıralama:
**Faz 0 → Faz 1** ile başlayıp, V Tipi Silobas için çalışan bir teklif + modern PDF üretip sana göstereceğim. PDF tasarımını onayladıktan sonra kaydetme döngüsü (Faz 2) ve diğer ürünlere (Faz 3–4) geçeceğiz.

Bu plana eklemek/çıkarmak istediğin bir şey var mı? Varsa söyle, yoksa "başla" dediğinde Faz 0–1'e geçerim.
