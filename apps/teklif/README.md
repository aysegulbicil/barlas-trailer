# Barlas Teklif Sistemi

`barlas-trailer`'dan **bağımsız** teklif oluşturma paneli. Ürün seçersin → teknik özellikler, başlık, görsel ve tarih otomatik dolar → tek tıkla **PDF** üretip kendi klasörüne kaydeder.

> **Durum:** Çalışır halde. 76 ürünün tamamı otomatik şablonlu. Kod/klasör adları İngilizce, arayüz ve veri Türkçe.

---

## Açmak

**XAMPP ile (önerilen):** Tarayıcıda
```
localhost/Barlas-Teklif-Sistemi/
```
(otomatik panele yönlenir; doğrudan `.../ui/` de açılır.)

**XAMPP olmadan:** `start.bat`'a çift tıkla → `http://127.0.0.1:8765` açılır. (`stop.bat` durdurur.)

## Önce görmek istersen
`SAMPLE-offer.html`'e çift tıkla → V Tipi Silobas için örnek teklifin birebir görünümü (Ctrl+P ile PDF'e basabilirsin).

---

## Kullanım
1. **Müşteri** bilgilerini gir. Tarih ve teklif no hazır gelir.
2. Her ürün için **Kategori** + **Ürün** seç → teknik özellikler, görsel, başlık ve net fiyat otomatik dolar. Satırları düzenle/sil, "＋ Özellik Ekle" ile satır ekle.
3. **"＋ Ürün Ekle"** ile aynı teklife farklı kategori/modelden ürün ekle. Her ürünün kendi net fiyatı olur; tek ürün varsa belge sade görünür.
4. **KDV (%)**, **Ödeme**, **Teslimat** gir → altta ara toplam + KDV + genel toplam otomatik hesaplanır. Sağda **canlı önizleme**.
5. **"Kaydet & PDF Oluştur"** → `offers/<tarih_müşteri_no>/` klasörü oluşur (`offer.pdf` + `offer.json` + `offer.html`) **ve PDF tarayıcının İndirilenler klasörüne otomatik iner**.
6. **Geçmiş Teklifler** → ayrı bir **sayfada** listelenir; teklifi aç / düzenle / PDF indir / sil.

---

## Klasör yapısı

```
Barlas-Teklif-Sistemi/
├── index.php             → kökü panele yönlendirir
├── start.bat / stop.bat  → XAMPP'siz çalıştır / durdur
├── SAMPLE-offer.html     → örnek çıktı
├── ui/                   → panel (index.html, app.js, style.css, assets/)
├── server/api/           → PHP: save, list, open, delete, _common
├── data/                 → products.json, templates.json, company.json, counter.json
│   └── source/           → catalog.docx, catalog.json (kaynak; çalışmada kullanılmaz)
├── images/               → 86 ürün görseli
└── offers/               → oluşturulan teklifler (her biri kendi klasöründe)
```

## Sonraki adımlar
- **Firma bilgileri:** `data/company.json` içindeki `[...]` alanlarını (ad, adres, telefon, IBAN, vergi no) doldur → PDF alt bilgisinde görünür.
- İstenirse: aynı ürünün farklı **modelleri/varyantları** için seçici eklenebilir.

> Değişiklikten sonra tarayıcıda **Ctrl+F5** (önbellek).
