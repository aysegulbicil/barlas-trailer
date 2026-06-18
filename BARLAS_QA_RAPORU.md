# 🦁 BARLAS TRAILER — FİNAL QA RAPORU

**Proje:** Barlas Trailer — CodeIgniter 4 çoklu dil kurumsal web sitesi (tr / en / ru / ar / fr)
**Denetim tarihi:** 17 Haziran 2026
**Denetim türü:** Kod düzeyinde (statik) QA denetimi — 5 bağımsız uzman ajan
**Yöntem:** Canlı sunucu/Lighthouse kullanılmadı. Tüm bulgular gerçek kaynak kodundan, dosya boyutlarından ve yükleme stratejisinden doğrulandı. Varsayım yapılmadı; yalnızca doğrulanabilir bulgular raporlandı. **Hiçbir dosya değiştirilmedi (salt-okunur denetim).**

---

## 📋 YÖNETİCİ ÖZETİ

Site mimari olarak **sağlam ve büyük ölçüde profesyonelce kurulmuş**. Özellikle çeviri/çoklu dil altyapısı **mükemmel** (5 dilde %100 anahtar kapsama), rota tutarlılığı kusursuz, mail/WhatsApp/teklif akışı kod düzeyinde eksiksiz ve production güvenlik modu doğru. Ancak **canlıya çıkıştan önce mutlaka giderilmesi gereken bir grup kritik engel var** — bunların başında devasa sıkıştırılmamış 3D modeller (performans), `localhost` kalmış site adresi, eksik sosyal önizleme görseli, kapalı CSRF koruması ve ana sayfada çalışmayan mobil menü geliyor.

### Önem derecesine göre toplam bulgu dağılımı

| Ajan | 🔴 Kritik | 🟠 Orta | 🟡 Düşük | Genel Durum |
|---|---|---|---|---|
| **AJAN 1 — Mobil & RTL** | 3 | 4 | 3 | İyi temel, 2 sistemik kusur |
| **AJAN 2 — Çeviri & Çoklu Dil** | 0 | 0 | 3 | ⭐ Mükemmel |
| **AJAN 3 — UI/UX & İşlevsellik** | 1 | 6 | 5 | Sağlam, temizlik gerekiyor |
| **AJAN 4 — Performans** | 3 | 4 | 3 | ⚠️ En kritik alan |
| **AJAN 5 — Sistem & Teslimat** | 6 | 4 | 3 | Altyapı tam, deploy engelleri var |
| **TOPLAM (ham)** | **13** | **18** | **17** | — |

> **Not:** Bazı bulgular birden fazla ajan tarafından bağımsızca doğrulandı (örn. eksik `og-default.jpg` hem AJAN 3 hem AJAN 5'te; ölü `main.php`/`home/*` hem AJAN 3 hem AJAN 4'te). Çapraz doğrulanan bulgular yüksek güvenilirliktedir — aşağıda işaretlendi.

### Genel kanı

**Site, "fonksiyon tamam ama teslime hazır değil" durumunda.** İçerik, çeviri ve iş mantığı hazır; eksik olan tarafı performans optimizasyonu, production yapılandırması ve birkaç güvenlik/SEO düzeltmesi. Aşağıdaki **İlk 10 madde** giderildiğinde site yayına alınabilir durumda olur.

---

## ✅ TESLİM ÖNCESİ DÜZELTİLMESİ GEREKEN İLK 10 MADDE

Tüm ajanların bulguları, **canlıya çıkışı engelleyen / kullanıcıyı doğrudan etkileyen** etkiye göre önceliklendirildi.

| # | Öncelik | Bulgu | Kaynak | Neden kritik | Çözüm (özet) |
|---|---|---|---|---|---|
| **1** | 🔴 Deploy engeli | **`baseURL` hâlâ `http://localhost:8080/`** | S1 ✔doğrulandı | Canlıda TÜM canonical, hreflang, OG, mail ve WhatsApp linkleri `localhost`'u gösterir → SEO + önizleme + linkler tamamen bozulur | `.env`'e `app.baseURL = 'https://barlastrailer.com/'` ekle |
| **2** | 🔴 Performans | **3D modeller 187 MB, sıkıştırma yok** — tek model 16–43 MB | P1 ✔doğrulandı | Ana sayfa açılışta ~36 MB peşin + scroll'da +100 MB indiriyor; mobil veride felaket | `gltf-transform optimize --compress draco --texture-compress webp` → ~10–20 MB |
| **3** | 🔴 Sosyal/SEO | **`og-default.jpg` dosyası eksik** ama her sayfa referans veriyor | U1 + S2 ✔doğrulandı (çift) | WhatsApp/sosyal paylaşımda önizleme kartı kırık çıkar (üstelik teklif stratejisi buna dayanıyor) | 1200×630 marka görseli ekle: `public/assets/images/og-default.jpg` |
| **4** | 🔴 Güvenlik | **CSRF koruması global filtrelerde kapalı** | S3 ✔doğrulandı | Form `csrf_field()` bassa da doğrulama çalışmıyor → CSRF/spam saldırısına açık | `Filters.php` `globals.before`'da `'csrf'`'i aktif et |
| **5** | 🔴 Mobil UX | **Ana sayfa + iletişimde mobil hamburger menü açılmıyor** | M1 ✔doğrulandı | `yeni.php` `navigation.js`'i hiç yüklemiyor → en önemli 2 sayfada telefonda menü ölü | `yeni.php`'ye `inner.php`'deki gibi `initNavigation()` ekle |
| **6** | 🔴 Performans | **gzip/brotli ve tarayıcı önbelleği yok** | P3 ✔doğrulandı | 568 KB metin varlık sıkıştırılmadan gidiyor; her ziyarette 214 MB statik tekrar iniyor | `public/.htaccess`'e `mod_deflate` + `mod_expires` (1 yıl) ekle — **en yüksek kazanç/efor** |
| **7** | 🔴 Performans | **27 MB görsel; WebP/AVIF ve responsive yok** (en büyük tekil 2.66 MB PNG) | P2 ✔doğrulandı | LCP yavaş; mobil de masaüstü PNG indiriyor | WebP/AVIF + `<picture>`/`srcset` → 27 MB ≈ 4–6 MB |
| **8** | 🔴 Güvenlik | **Gerçek Gmail uygulama şifresi `.env`'de düz metin** | S5 ✔doğrulandı | Sır ifşa riski; sunucuya açık dağıtılacak | App-password'ü **iptal et + yeniden üret**; `.env`'in repoya girmediğini doğrula |
| **9** | 🔴 SEO | **`robots.txt` sitemap'i `localhost` gösteriyor + `sitemap.xml` yok** | S4 ✔doğrulandı | Arama motorları olmayan sitemap'i arar; indeksleme zayıflar | 5 dil + hreflang'li sitemap üret; robots.txt URL'sini gerçek alan adına çek |
| **10** | 🔴 RTL / Pazar | **`rtl.css` Arapça'da hiçbir sayfada yüklenmiyor** | M2 ✔doğrulandı | RTL düzeltmeleri ölü `main.css` üzerinden geliyor; 5 hedef pazardan biri (AR) kısmen aynalanmamış | `rtl.css`'i `is_rtl()` durumunda `yeni.php`/`inner.php`'ye doğrudan ekle |

### Onurlandırılan ek adaylar (11–13)
Aşağıdakiler ilk 10'a çok yakın; özellikle hukuki/pazar riski taşıyanlar lansman öncesi değerlendirilmeli:

- **KVKK / Gizlilik / Çerez sayfaları yok** (footer ve iletişim formundaki onay linkleri `href="#"`) — TR + AB pazarı için hukuki uyum riski (U2, U3, S11).
- **Footer "Ara" butonu yanlış numarayı arıyor** — beyan edilen telefon `0543 578 08 00`, link ise WhatsApp numarasına (`+905525002000`) gidiyor (U10, S7).
- **404 sayfası marka/dil dışı + ana sayfaya dönüş linki yok** — kullanıcı çıkmaza giriyor (S8).

---

## 🔗 ÇAPRAZ-DOĞRULANMIŞ KRİTİK BULGULAR (yüksek güvenilirlik)

Birden fazla bağımsız ajanın aynı sonuca varması, bu bulguların doğruluğunu pekiştirir:

| Bulgu | Ajanlar | Durum |
|---|---|---|
| `og-default.jpg` eksik → kırık sosyal/WhatsApp önizleme | AJAN 3 (U1) + AJAN 5 (S2) | ✔ ayrıca elle doğrulandı |
| Ölü `layouts/main.php` + `pages/home/*` 11 partial + 26 ölü dosya | AJAN 3 (U5, U6) + AJAN 4 (P5) | ✔ doğrulandı |
| Footer telefon/numara tutarsızlığı | AJAN 3 (U10) + AJAN 5 (S7) | ✔ doğrulandı |
| Footer yasal linkleri `href="#"` (KVKK) | AJAN 3 (U2) + AJAN 5 (S11) | ✔ doğrulandı |
| `rtl.css` yüklenmiyor ↔ `main.css` ölü (aynı kök neden) | AJAN 1 (M2) + AJAN 4 (P5) | ✔ doğrulandı |
| Production modu doğru (CI_ENVIRONMENT tanımsız → production) | AJAN 4 + AJAN 5 | ✔ doğrulandı (iyi haber) |

---

## 🚀 SİTE HIZLANDIRMA — DERİNLEMESİNE PLAN (özel talep)

> Bu bölüm kullanıcının özel isteği üzerine, AJAN 4'ün bulgularını genişleterek **ekstra çözümlerle** hazırlandı. Kazanımlar gerçek dosya boyutlarından hesaplandı.

### Mevcut ağırlık tablosu (ölçülmüş)

| Sayfa | İlk boya | Tam scroll | Asıl yük kaynağı |
|---|---|---|---|
| **Ana sayfa** | ~38 MB | **~140 MB** | 3D konvoy (.glb) |
| **İletişim** | ~20 MB | ~20 MB | three.js motoru + tanker-1.glb (gereksiz) |
| **Ürün/İç sayfalar** | 0.3–5 MB | — | görseller (sıkıştırılmamış) |

**Hedef:** Ana sayfa ilk-ziyaret toplam yükü **~140 MB → ~15–25 MB**; ikinci ziyaret **~0 MB** (önbellek); metin trafiği **~%75 azalma**.

### Aşama P0 — HEMEN (en yüksek etki)

**1. 3D modelleri sıkıştır** *(en kritik tek kazanım)*
`npm i -g @gltf-transform/cli` → her model için:
```
gltf-transform optimize giris.glb cikis.glb --compress draco --texture-compress webp --texture-size 2048
```
Layout'a `DRACOLoader` + WebP/KTX2 doku desteği ekle. **Kazanım: 187 MB → ~10–20 MB (%85–95).** Ana sayfa peşin yük 36 MB → ~3–5 MB.

**2. `.htaccess`'e sıkıştırma + önbellek** *(en iyi kazanç/efor)*
`mod_deflate` (css/js/html/svg/json/glb) + `mod_expires` (statik varlıklar "1 yıl", `?v=` hash'i sayesinde güvenli). **Kazanım: 568 KB metin → ~140 KB; ikinci ziyaret 214 MB statik → 0 byte.** ~30 dk iş, sıfıra yakın risk.

**3. Görselleri WebP/AVIF + responsive yap**
`cwebp -q 80` / `avifenc` + `<picture>`/`srcset`. Öncelik: `hero-2.png` (2.66 MB), `4bb9…png` (2.06 MB), `about.jpg` (1.72 MB). **Kazanım: 27 MB → ~4–6 MB.**

### Aşama P1 — KISA VADE

**4. three.js'i koşullu/dinamik yükle** — `await import('three')` yalnızca ≥992px + WebGL + reduced-motion kapalıyken. İletişim için yalın layout. **Kazanım: mobilde ~1.47 MB JS hiç inmez.**

**5. CSS/JS minify + birleştir** — `esbuild`/`lightningcss` ile bundle. 6 CSS isteği → 1; 104 KB → ~70 KB (+gzip ~16 KB).

### Aşama P2 — TEMİZLİK / İNCE AYAR

**6.** 26 ölü dosyayı sil (55 KB JS + 125 KB CSS + `main.php`). **7.** Logoyu SVG/küçük WebP yap (36 KB → ~6 KB). **8.** Hero `<img>`'lerine `width/height` ekle (CLS). **9.** İmleç rAF'ını idle'da durdur; `lenis`'i değerlendir.

### 🎯 EKSTRA HIZLANDIRMA ÖNERİLERİ (denetim kapsamı dışı, stratejik)

Bunlar koddaki tekil bulguların ötesinde, mimari düzeyde ekstra hız kazandırır:

1. **Önüne bir CDN koy (Cloudflare ücretsiz katman).** Tek hamlede brotli sıkıştırma + edge önbellek + HTTP/3 + otomatik görsel optimizasyonu (Polish/Mirage) sağlar. P0-2 ve P0-3'ün çoğunu sunucuya dokunmadan halleder; 187 MB modeller edge'den dağıtılır. **En yüksek kaldıraç.**

2. **3D'yi "tıkla-yükle" veya video/poster fallback'e çevir.** Ziyaretçilerin çoğu için WebGL konvoyu yerine yüksek kaliteli bir poster görseli + (isteğe bağlı) kısa MP4 döngüsü göster; "3D'yi başlat" butonuna basan kullanıcıya gerçek modeli yükle. WebGL motoru + 100+ MB model çoğu oturumda hiç inmez.

3. **Model geometrisini sadeleştir (decimate).** Meshy AI çıktıları aşırı yüksek poligonlu olur. `gltf-transform simplify` / `weld` / `dedup` ile (Draco'dan önce) poligon ve vertex sayısını düşür — hem dosya hem GPU/CPU maliyeti azalır.

4. **Dokuları KTX2/Basis Universal'a çevir.** WebP doku zaten büyük kazanç; KTX2 GPU'da sıkıştırılmış kalır (VRAM ve upload süresi düşer) — düşük güçlü cihazlarda 3D'yi akıcılaştırır.

5. **Fontları self-host + subset.** Google Fonts yerine `woff2` self-host + yalnızca kullanılan glyph alt kümesi (Latin + Kiril + Arapça ayrı dosyalar, `unicode-range` ile). Üçüncü taraf bağlantı turunu (DNS+TLS) eler, kritik fontu `preload` et.

6. **HTTP/2 veya HTTP/3 + multiplexing.** 6 ayrı CSS + 5 JS isteği HTTP/2 altında paralel iner; sunucuda etkinleştir (CDN zaten verir).

7. **PHP OPcache + CI4 önbelleği aç.** `Optimize.php`'de `configCacheEnabled`/`locatorCacheEnabled = true`; sunucuda OPcache aktif. Statik/yarı-statik sayfalar için CI4 Page Cache (örn. ana sayfa 60 sn) ilk byte süresini (TTFB) düşürür.

8. **Performans bütçesi koy + ölç.** Lansman öncesi gerçek Lighthouse/WebPageTest ölç (özellikle mobil 4G). Hedef bütçe: ilk yük < 2 MB, LCP < 2.5 sn, TBT < 200 ms. CI'a bir Lighthouse bütçe kontrolü ekle ki gelecekte 187 MB'lik bir varlık fark edilmeden geri sızmasın.

9. **`fetchpriority` + `preload` ince ayarı.** Hero LCP görseli zaten `fetchpriority=high` (iyi); aynı disiplini WebP hero'ya taşı, gereksiz preload'lardan kaçın.

10. **Lazy bölüm hidrasyonu.** Ana sayfadaki ağır bölümleri (AI konsolu, galeri, konvoy) yalnızca görünüme girince başlat (IntersectionObserver konvoyda zaten var — aynı deseni tüm pahalı widget'lara yay).

---
---

# 📑 EKLER — AJANLARIN TAM RAPORLARI

Aşağıda 5 ajanın kendi alanlarında hazırladığı bağımsız, tam raporlar yer alır.

---

## AJAN 1 — Mobil Uyumluluk & RTL Denetim Raporu

**Özet:** 21 CSS dosyası, 24 JS dosyası ve 13+ view/layout/partial salt-okunur olarak incelendi (toplam ~8.900 satır CSS, ~4.170 satır JS). Genel durum: Mobil grid/yığılma davranışı ve logical-property disiplini **çok iyi**; ancak iki sistemik kusur var — (1) **ana sayfa ve iletişim sayfasında hamburger menü JS'i hiç yüklenmiyor**, yani mobilde menü açılamıyor; (2) **rtl.css gerçek sayfaların hiçbirinde yüklenmiyor** ve hedeflediği seçiciler ölü. Formlarda iOS auto-zoom riski mevcut.

### 🔴 Kritik

**M1 — [Dosya: app/Views/layouts/yeni.php + public/assets/js/yeni-app.js + public/assets/js/navigation.js] Ana sayfa ve iletişim sayfasında mobil hamburger menü ÇALIŞMIYOR.**
`layouts/yeni.php` (ana sayfa `home.php` ve `contact.php` bunu `extend` eder) `navigation.css` (satır 51) ile mobil çekmece stilini ve `yeni-app.js`'i (satır 71) yükler, ancak `navigation.js`'i hiç yüklemez ve `initNavigation()`'ı çağırmaz. `header.php` çekmeceyi süren tek modül `navigation.js`'tir (`initMobileMenu`, satır 47-48 → `[data-nav-toggle]` + `[data-nav-menu]`). `yeni-app.js`'in kendi `initMenu()` fonksiyonu (satır 154-156) ise `[data-nav]` arar — fakat `header.php`'deki gerçek seçici `[data-nav-menu]`'dür (satır 71), `[data-nav]` markup'ta yoktur. Sonuç: `nav` değişkeni `null` olur, fonksiyon erken `return` eder; hamburger butonuna (`max-width:991px`'de `display:flex` ile görünür) **hiçbir tıklama dinleyicisi bağlanmaz**. Karşılaştırma: `layouts/inner.php` (satır 75-76) `initNavigation()`'ı doğru çağırır, bu yüzden iç sayfalarda (ürünler, hizmetler, blog, teklif, medya) menü çalışır. — **Çözüm:** `layouts/yeni.php`'ye `inner.php`'deki gibi `<script type="module">import { initNavigation } from '.../navigation.js'; initNavigation();</script>` ekleyin; VEYA `yeni-app.js` satır 156'daki `[data-nav]` seçicisini `[data-nav-menu]` ile düzeltip header'a uygun aç/kapa + scroll-lock mantığını tamamlayın. (Tek başına seçici düzeltmesi yetmez: `yeni-app.js`'in `initMenu`'sü scroll-lock, Escape, mega-akordeon ve `--header-offset` güncellemesini içermez.)

**M2 — [Dosya: public/assets/css/rtl.css + public/assets/css/main.css + layouts/*] rtl.css hiçbir canlı sayfada yüklenmiyor ve hedef seçicileri ölü.**
`rtl.css` yalnızca `main.css` içinden `@import url('rtl.css')` ile gelir (main.css satır 19). `main.css` ise SADECE `layouts/main.php`'de linklenir — ve hiçbir sayfa bu layout'u `extend` etmez (tüm sayfalar `layouts/yeni` veya `layouts/inner` kullanıyor; doğrulandı: home/contact→yeni, blog/products/services/quote/media→inner). Dolayısıyla Arapça'da `rtl.css` devreye girmez. Üstelik `rtl.css`'in hedeflediği seçiciler (`.service-item`, `.category-card`, `.hero__glow`, `.category-card__more`) yüklenen stil sayfalarında **yok** — eski tasarıma (sections.css/redesign.css) ait olup onlar da yalnızca kullanılmayan main.css üzerinden gelir. — **Çözüm:** rtl.css'i `is_rtl()` durumunda `inner.php` ve `yeni.php`'ye doğrudan `<link>` ile ekleyin VEYA RTL düzeltmelerini canlı stil sayfalarındaki güncel seçicilere göre yeniden yazın (özellikle `yeni.css` için — aşağıdaki M3).

**M3 — [Dosya: public/assets/css/yeni.css] Ana sayfa stil sayfasında (1481 satır) HİÇ `[dir="rtl"]` kuralı yok.**
`yeni.css` ana sayfanın tüm görünümünü kontrol eder ama `[dir=rtl]` override sayısı = 0 (doğrulandı). İçinde yönlü ok/hover animasyonları var: `.console__chip::after` hover `translateX` (satır 1009-1011), hero CTA okları vb. Arapça'da `dir="rtl"` doğru set edilse de (layout'larda `locale_direction()` çalışıyor — bu kısım doğru), bu yönlü hareketler aynalanmaz; oklar yanlış yöne kayar. Logical property kullanımı genelde mükemmel olduğundan ana yerleşim aynalanır, sorun yalnızca dekoratif yönlü transform'lardadır. — **Çözüm:** `yeni.css`'e ok/SVG hover animasyonları için `[dir="rtl"] ... { transform: scaleX(-1) ... }` override'ları ekleyin (yeni-pages.css satır 325/467 ve products.css satır 202'deki kalıbı izleyin).

### 🟠 Orta

**M4 — [Dosya: public/assets/css/contact.css satır 189-192] İletişim formu input'ları iOS'ta otomatik yakınlaştırmayı tetikler.**
`.contact-field__input { font-size: 0.95rem }` (≈15.2px) — iOS Safari, 16px altındaki font-size'lı bir input'a odaklanınca sayfayı otomatik yakınlaştırır. Bu, ad/e-posta/telefon/firma/konu(select)/mesaj(textarea) alanlarının tümünü etkiler (markup: contact.php satır 187-244). — **Çözüm:** Mobil breakpoint'te (`max-width:560px` veya genel) `.contact-field__input { font-size: 16px }` yapın.

**M5 — [Dosya: app/Views/pages/quote/index.php satır 42] Teklif arama input'u iOS'ta otomatik yakınlaştırmayı tetikler.**
`.quote-pick__search { ... font-size: var(--fs-300) }` ve `--fs-300 = 0.875rem = 14px` (variables.css satır 66) — 16px'in altında, iOS zoom tetiklenir. — **Çözüm:** Input'a `font-size: max(16px, var(--fs-300))` veya mobilde 16px verin.

**M6 — [Dosya: public/assets/css/yeni-pages.css satır 702] `.media-grid--video` 320px'lik ekranlarda yatay taşma riski.**
`.media-grid--video { grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)) }` — bu ızgarayı saran bir media query yok. Çok dar ekranlarda (örn. 320px cihaz: 320 − ~2×18px iç boşluk ≈ 284px kullanılabilir < 300px) tek sütun 300px minimumu aşar ve sayfada yatay kaydırma oluşabilir. Markup `media/index.php` satır 147'de kullanılıyor. Fotoğraf ızgarası (satır 699, `minmax(240px,1fr)`) bu riski taşımaz. — **Çözüm:** `minmax(min(100%, 300px), 1fr)` kullanın veya küçük ekran için `minmax(220px, 1fr)`'e düşürün.

**M7 — [Dosya: yeni.css / yeni-pages.css / contact.css — yüklenen stil sayfaları] Body/html düzeyinde global `overflow-x: hidden` güvenlik ağı YOK.**
`base.css` satır 28'de `body { overflow-x: hidden }` mevcut ancak base.css yalnızca (kullanılmayan) main.css üzerinden gelir. Canlı sayfaların kullandığı `yeni.css` (kendi reset'i satır 59-69) ve diğer yüklenen stil sayfalarında html/body üzerinde `overflow-x: hidden` tanımı yoktur (doğrulandı; yalnızca contact.css'te tekil bir elemana `overflow-x: clip` var). Marquee/ticker/refs bantları kendi container'larında `overflow: hidden` ile güvende olduğundan akut bir taşma görünmüyor; ancak M6 gibi bir tetikleyici oluştuğunda tüm sayfada yatay kaydırmayı durduracak ağ yok. — **Çözüm:** `yeni.css` ve `yeni-pages.css` reset bölümüne `html, body { overflow-x: hidden }` (veya daha güvenli `clip`) ekleyin.

### 🟡 Düşük

**M8 — [Dosya: public/assets/css/yeni.css satır 176-211] Ölü/yetim mobil menü stilleri (`.nav` / `.nav-toggle` @860px) — bakım karmaşası.**
yeni.css'te `.nav` (fixed çekmece, `max-width:860px`) ve `.nav-toggle` (@860px `display:flex`) için tam bir mobil menü stil bloğu var, fakat `header.php`'de `.nav`/`.nav-toggle`'ı bu şekilde kullanan markup yok (gerçek seçiciler `.nav-menu`/`.site-header .nav-toggle`, breakpoint 991px). Bu blok, eski bir markup sürümünden kalma ölü koddur. Cascade analizi: 861–991px arasında `yeni-header.css` satır 209'daki `.site-header .nav-toggle{display:flex}` (özgüllük 0,2,0, en son yüklenir) kazanır, yani hamburger doğru görünür — **görsel bir bug oluşturmaz**, ancak M1'i araştırmayı zorlaştıran kafa karıştırıcı bir tutarsızlıktır (iki ayrı breakpoint: 860 vs 991). — **Çözüm:** yeni.css'teki yetim `.nav`/`.nav-toggle` bloklarını kaldırın; tek nav sistemi (navigation.css + yeni-header.css, 991px) kalsın.

**M9 — [Dosya: public/assets/css/navigation.css satır 11 + 634] Ana sayfada mobil çekmece konumu yanlış `--header-offset` (112px) kullanır.**
Çekmece `inset-block-start: var(--header-offset)` ile konumlanır; bu değişken yalnızca `navigation.js`'in `initHeaderOffset()`'i (satır 40) tarafından gerçek header yüksekliğine güncellenir. M1 nedeniyle ana sayfada navigation.js çalışmadığından değer sabit `112px`'te kalır; topbar küçük ekranda sarıp header yükseldiğinde çekmece yanlış konumlanır. M1 düzeltilince bu da otomatik çözülür; ayrı bir not olarak bırakılmıştır. — **Çözüm:** M1 ile birlikte navigation.js'i yükleyin.

**M10 — [Dosya: public/assets/css/yeni.css satır 597-613] `.spot__card` (230px sabit) ekran kenarındaki hotspot'ta taşabilir.**
Hotspot tooltip'i `width: 230px` + `inset-inline-start: 50%; translate: -50% 0` ile noktaya göre ortalanır; kenara yakın bir hotspot'ta dar telefonda kart viewport dışına taşabilir (kenar-clamp mantığı yok). Canlı ana sayfa bu `.spot`/`.showcase` sistemini KULLANMIYOR (road bölümü `road__cap` ile yığılmış statik metin kullanır; `$spotPos` tanımlı ama markup'ta `.spot` üretilmiyor — doğrulandı). Bu nedenle düşük önemde latent bir risktir. — **Çözüm:** Kullanılmıyorsa `.spot*`/`.showcase*` ölü CSS'i temizleyin; kullanılacaksa karta `max-width: min(230px, 90vw)` ve kenar-farkında konumlandırma ekleyin.

### ✅ Sorun Bulunmayan Alanlar

- **Viewport meta** — `partials/meta.php` satır 19: `<meta name="viewport" content="width=device-width, initial-scale=1.0">` doğru; `maximum-scale`/`user-scalable=no` yok (erişilebilirlik açısından isabetli). Üç layout da bu partial'ı include eder.
- **`dir="rtl"` kurulumu** — Üç layout da `<html ... dir="<?= locale_direction($locale) ?>">` kullanır; `locale_helper.php` `is_rtl()` `'ar'`'ı doğru içerir.
- **Logical property disiplini** — Yüklenen stil sayfalarında fiziksel `margin/padding-left/right`, `text-align:left/right`, `float` kullanımı = **0** (doğrulandı). `inset-inline-*`, `margin-inline`, `padding-block` her yerde; yerleşim RTL'de otomatik aynalanır.
- **service-gallery.css RTL** — prev/next okları, sayaç rozeti ve SVG için her fiziksel `left/right`'ın `[dir="rtl"]` karşılığı verilmiş.
- **Mega menü mobil davranışı** — navigation.css mega panelleri mobilde iki seviyeli akordeona çevirir; 48px dokunma hedefi; tek-açık akordeon, Escape/dış-tık/klavye desteği (iç sayfalarda).
- **Mega menü RTL** — ok/chevron `scaleX(-1)` ile aynalanır (canlı sayfalarda yüklenir).
- **Mobil grid yığılması** — Tüm ana sayfa bölümleri doğru çöker (`stats`, `process`, `why`, `gallery`, `console`); hepsi `minmax(0,1fr)` (taşma yapmaz).
- **Sekme şeritleri** — `prd-tabs`, `media-tabs`, `media-filters` `overflow-x:auto` + gizli scrollbar ile mobilde düzgün.
- **Marquee/ticker/refs bantları** — Container'larda `overflow: hidden`; şeritler sayfayı yatayda genişletmez.
- **Form mobil yığılma** — contact/quote formları 1 sütuna çöker; dokunma hedefleri yeterli (`btn--lg`, 48px).
- **Reduced-motion** — Tüm büyük stil sayfalarında `prefers-reduced-motion: reduce` blokları mevcut.
- **Genel breakpoint kapsamı** — Breakpoint'ler dağınık (520–992 arası çeşitli) ama kapsam yeterli; kritik aralık boşluğu yok. (İyileştirme: ortak breakpoint token seti — işlevsel bug değil.)

<!-- /AJAN-1 -->

---

## AJAN 2 — Çeviri & Çoklu Dil Denetim Raporu

**Kapsam:** 5 dil (tr kaynak, en, ru, ar, fr) × 10 dil dosyası = 50 dosya + locale altyapısı (App.php, LanguageController, LanguageFilter, locale_helper, language-switcher, Routes, Filters) + 43 view dosyası. Anahtar kıyası Python ile programatik yapıldı; değerler iki ayrı parser ile çapraz doğrulandı.

**Özet:** Çeviri katmanı olağanüstü sağlıklı. **Dillerarası anahtar kapsama %100** — 10 modülün tamamında 5 dilin anahtar setleri birebir aynı. Tek gerçek tutarsızlık: `Media.sample_note` anahtarının kaynak (tr) dosyasında bulunmaması (diğer 4 dilde var; ayrıca ölü/kullanılmayan anahtar). Boş string, placeholder, dil kontaminasyonu **yok**. View'larda hardcode kullanıcı metni **yok**. Locale altyapısı 5 dili de kapsıyor, geçersiz locale ele alınıyor, RTL (ar) doğru kuruluyor.

### Dillerarası Anahtar Kapsama Tablosu (eksik anahtar sayısı, kaynak = tr)

| Dosya | tr (anahtar) | en | ru | ar | fr |
|---|---|---|---|---|---|
| Ai.php | 17 | 0 | 0 | 0 | 0 |
| Blog.php | 39 | 0 | 0 | 0 | 0 |
| Common.php | 21 | 0 | 0 | 0 | 0 |
| Contact.php | 72 | 0 | 0 | 0 | 0 |
| Home.php | 154 | 0 | 0 | 0 | 0 |
| Media.php | 21 | 0 (+1 fazla) | 0 (+1 fazla) | 0 (+1 fazla) | 0 (+1 fazla) |
| Navigation.php | 138 | 0 | 0 | 0 | 0 |
| Products.php | 33 | 0 | 0 | 0 | 0 |
| Quote.php | 10 | 0 | 0 | 0 | 0 |
| Services.php | 34 | 0 | 0 | 0 | 0 |
| **TOPLAM eksik** | — | **0** | **0** | **0** | **0** |

Not: en'de ek olarak `Validation.php` var (CI4 framework çevirisi, diğer dillerde gerekmez — sorun değil). "Fazla" = sadece `Media.sample_note` (aşağıda Ç1).

### 🔴 Kritik
Kritik bulgu **yok**. Eksik çeviri, bozuk anahtar render'ı, kullanıcıya görünen çevrilmemiş metin veya çalışmayan dil değiştirici tespit edilmedi.

### 🟠 Orta
Orta öncelikli bulgu **yok**. İncelenen ve temiz çıkan riskli alanlar: dil değiştirici 5 dili de listeliyor; geçersiz locale hem `LanguageController::switch()` hem `LanguageFilter` hem CI4 route placeholder düzeyinde `supportedLocales`'a göre doğrulanıp default'a (`tr`) düşürülüyor; cookie 1 yıl saklanıyor; referrer yolu locale değişiminde doğru yeniden yazılıyor.

### 🟡 Düşük

**Ç1. [tr/Media.php — `sample_note` anahtarı]** — Anahtar tr dosyasında **yok**, ama en/ru/ar/fr Media.php'de mevcut. Kaynak dile göre asimetri yaratıyor. Ancak `sample_note` ne view'da ne controller'da çağrılıyor — **kullanılmayan (ölü) anahtar**, hiçbir kullanıcı bozuk anahtar görmüyor. **Çözüm:** Ya 4 dilden de kaldırın, ya da tr/Media.php'ye ekleyip set'i simetrik yapın.

**Ç2. [Common.php — `address` değeri, tüm diller]** — Adres metninde `No: 4s` yazıyor. Sondaki "s" muhtemel yazım hatası ("No: 4" olmalı). 5 dilde de aynı → **kaynak veri hatası**, çeviri hatası değil. Ayrıca tr varyantında satır kırılımı diğer dillerden farklı konumda — kozmetik. **Çözüm:** "4s" ifadesini doğru kapı numarasıyla düzeltin ve `\n` konumunu hizalayın.

**Ç3. [tr/Contact.php — `ph_phone` placeholder]** — Değer `+90 5xx xxx xx xx` (5 dilde aynı). "xx" deseni taramada işaretlendi; ancak bu **bilinçli bir telefon formatı placeholder'ı** (input için), gerçek bir TODO değil. Bilgi amaçlı — aksiyon gerekmez.

### ✅ Sorun Bulunmayan Alanlar

- **Anahtar tutarsızlığı:** 10 modül × 5 dil tam simetrik (Media'daki tek ölü anahtar hariç). İç içe diziler (`Home.showcase_bars`, `Media.video_placeholders`, Contact nested) dahil eksik/fazla anahtar yok.
- **Çevrilmemiş metin / dil kontaminasyonu:** ru'da Kiril dışı düz metin yok; ar'de Arapça dışı blok yok; fr/en'de yabancı script yok. İşaretlenenler meşru: `Barlas Trailer`, `WhatsApp` (ar: `واتساب`), e-posta/telefon, ADR/LPG gibi marka/teknik terimler.
- **Yanlış/anlamsız çeviri:** Boş string yok, TODO/FIXME/XXX/LOREM yok, anahtarın değer olarak bırakıldığı durum yok. Çeviriler doğal ve bağlama uygun (ar akıcı RTL, ru deyimsel, fr tipografik). `{product}/{category}/{url}/{0}` interpolasyon placeholder'ları tüm dillerde korunmuş.
- **View hardcode metin:** 43 view dosyasında `lang()` dışı kullanıcı metni **0 satır**. Türkçe karakter içeren tüm satırlar HTML/PHP yorumu. E-posta şablonu tamamen değişken-tabanlı, RTL'i doğru uyguluyor.
- **Locale altyapısı:** `$supportedLocales = ['tr','en','ru','ar','fr']`, `$defaultLocale='tr'`, `$negotiateLocale=true`. `LanguageFilter` global before filtresi; geçersiz locale default'a düşüyor. `language-switcher.php` 5 dili listeliyor, aktif dili `aria-current` ile işaretliyor, `hreflang` veriyor, aynı sayfada kalıyor. RTL: 3 layout da `dir` kuruyor; Quote sayfasında `[dir="rtl"]` özel CSS bile var.

<!-- /AJAN-2 -->

---

## AJAN 3 — UI/UX & İşlevsellik Denetim Raporu

**Kapsam:** `app/Views/` altındaki tüm view/partial/layout dosyaları, `app/Config/Routes.php`, ilgili controller'lar ve `public/assets/` dosya varlığı kod düzeyinde denetlendi. Bulgular `grep` + dosya-varlık (`ls`/`test -f`) ile doğrulandı.

**Genel değerlendirme:** Site mimari olarak sağlam. Neredeyse tüm bağlantılar `locale_url()` / `switch_locale_url()` üzerinden üretiliyor ve tanımlı rotalara işaret ediyor; tanımsız rotaya giden tek bir link bile yok. Görseller çoğunlukla "blueprint placeholder" sistemiyle zarifçe bozulmadan çalışıyor. Asıl sorunlar: birkaç ölü `href="#"` bağlantı, site genelinde eksik bir OG görseli ve ciddi miktarda ölü/kullanılmayan dosya.

### 🔴 Kritik

**U1 — [app/Views/partials/meta.php:14] Site genelinde eksik varsayılan OG görseli.**
`$metaImage = $metaImage ?? base_url('assets/images/og-default.jpg');` — bu dosya dosya sisteminde **YOK**. `$metaImage` override etmeyen TÜM sayfalar (ana sayfa, blog listesi, hizmetler, ürün listesi/kategori, medya, teklif, iletişim) `og:image` ve `twitter:image` olarak kırık bir URL yayınlıyor. Sonuç: WhatsApp/sosyal medyada paylaşıldığında önizleme kartında görsel boş/bozuk çıkar.
**Çözüm:** `public/assets/images/og-default.jpg` (önerilen 1200×630) ekle. Hızlı geçici çözüm: mevcut bir görseli bu adla kopyala.

### 🟠 Orta

**U2 — [app/Views/partials/footer.php:170-172] Yasal bağlantılar ölü (`href="#"`).**
KVKK, Gizlilik ve Çerez bağlantılarının üçü de `<a href="#">`. Dosya başındaki yorumda yer tutucu oldukları belirtilmiş. Footer her sayfada göründüğü için bu üç ölü link site genelinde tıklanabilir ama hiçbir yere gitmiyor. **Çözüm:** Gerçek KVKK/gizlilik/çerez sayfaları oluşturulup bağlanmalı; içerik yoksa `<li>`'ler geçici gizlenmeli.

**U3 — [app/Views/pages/contact.php:25] KVKK onay (consent) bağlantısı ölü (`href="#"`).**
İletişim formunda zorunlu onay kutusunun metnindeki `{link}` yer tutucusu `<a href="#">` ile değiştiriliyor. Kullanıcı "açık rıza metni"ni okumak için tıkladığında hiçbir yere gitmiyor — zorunlu bir onay alanında hem UX hem hukuki açıdan sorunlu. **Çözüm:** U2'deki KVKK sayfası hazır olunca gerçek URL bağlanmalı (yeni sekmede).

**U4 — [app/Views/pages/home.php:262, 277] "Referanslar" bandındaki tüm partner logoları sahte (Barlas logosunun tekrarı).**
İki ayrı bantta (8'er adet) gerçek partner logoları yerine her birinde `assets/images/logo.png` (Barlas'ın kendi logosu) gösteriliyor. 16 kutunun hepsi aynı logo. Ziyaretçiye yanıltıcı/yer-tutucu görünür. **Çözüm:** Gerçek partner logoları eklenip bağlanmalı; yoksa bölüm kaldırılmalı.

**U5 — [app/Views/layouts/main.php] Hiçbir view tarafından kullanılmayan ölü layout.**
Tüm view'lar `layouts/yeni` veya `layouts/inner` extend ediyor (doğrulandı). `main.php` hiçbir yerden extend EDİLMİYOR. Üstelik bu ölü layout Bootstrap 5.3, AOS, Swiper gibi ağır CDN bağımlılıklarını yüklüyor. **Çözüm:** Silinmeli.

**U6 — [app/Views/pages/home/*.php] 11 adet kullanılmayan (ölü) bölüm partial'ı.**
`about, categories, cta, hero, marquee, process, references, services, showcase, stats, why`. `home.php` docblock'unda "artık kullanılmıyor" deniyor; hiçbirinin include edilmediği doğrulandı. Ana sayfa tüm içeriğini `home.php` içine gömülü üretiyor. **Çözüm:** Bu 11 dosya silinmeli (git geçmişi referansı tutar).

**U7 — [app/Views/welcome_message.php] Kullanılmayan CodeIgniter varsayılan şablonu.**
CI4 boilerplate; hiçbir route döndürmüyor. İçinde `<a href="#">Home</a>` gibi ölü bağlantılar var. **Çözüm:** Silinmeli.

### 🟡 Düşük

**U8 — [public/assets/media/photos/] Kullanılmayan boş klasör + yanıltıcı dökümantasyon.**
Yalnızca `.gitkeep` + `README.md`. Media controller docblock'u "photos taranıyor" diyor ama controller galeriyi `assets/images/products/*` + `media/before-after/` üzerinden kuruyor — `photos/` hiç taranmıyor. **Çözüm:** Klasör kaldırılmalı, docblock'lar güncellenmeli.

**U9 — [public/assets/media/videos/] Boş — canlı Medya sayfasında "Yakında" yer tutucu kartlar.**
Video yoksa controller `placeholderVideos()` ile "Yakında" rozetli kartlar gösteriyor (bilinçli). Bozuk değil ama Medya > Videolar sekmesi tamamen "Yakında" açılıyor. **Çözüm:** Gerçek videolar eklenmeli; lansmana kadar kalması bilinçli karar olmalı.

**U10 — [app/Views/partials/footer.php:204-208 yorum] Yanlış/bayat telefon numarası yorumu.**
Yorumda "0543 578 08 00 → tel:+905525002000" yazıyor; gerçekte tüm `tel:`/wa.me bağlantıları `Common.phone_raw` = `05525002000` numarasına gidiyor. Kod tutarlı; yorum kafa karıştırıcı. **Çözüm:** Yorum düzeltilmeli ya da ayrı hat varsa ayrı anahtar tanımlanmalı.

**U11 — [public/assets/images/] Artık/yetim dosyalar.**
Referans verilmeyen çöp dosyalar: `logo.png.png`, `hero-1.jpg` (`hero-1.png` varken), UUID adlı dosyalar; kök dizinde `*-preview.html` dev artıkları (hiçbiri `app/` içinden referanslanmıyor). **Çözüm:** Temizlenmeli (depo hijyeni; canlı işlevi etkilemiyor).

**U12 — [app/Views/pages/blog/detail.php:96] Var olmayan görsele işaret eden JSON-LD `image`.**
Schema.org `BlogPosting.image` her zaman `assets/images/blog/{image}` yazıyor, ama blog görselleri henüz yok (sayfa `data-img` ile zarifçe çalışıyor, yapısal veride kırık URL kalıyor). **Çözüm:** `is_file()` kontrolüyle koşullu hale getirilmeli.

### ✅ Sorun Bulunmayan Alanlar

- **Rota tutarlılığı (TEMİZ):** Tüm 17 tanımlı rota controller'larda gerçek view döndürüyor/yönlendiriyor. View'larda tanımsız rotaya işaret eden tek bir çağrı YOK. Mega menü ve teklif akışındaki slug'lar controller kayıtlarıyla birebir eşleşiyor.
- **Header:** Tüm nav/CTA bağlantıları gerçek hedeflere; brand logosu mevcut; tema/menü butonları `aria-label`/`aria-expanded`/`aria-controls` ile doğru.
- **Mega menüler:** Tüm kategori (11) ve hizmet (10) bağlantıları gerçek rotalara; foto fallback zinciri devrede (bozuk `<img>` yok).
- **Teklif akışı:** Her ürün gerçek `quote.start` rotasına bağlı; arama, boş-sonuç, alternatif iletişim çalışıyor; WhatsApp numarası tek kaynaktan.
- **İletişim formu:** Etiket-input ilişkileri eksiksiz, honeypot + CSRF token + AJAX/no-JS yolu, erişilebilir hata/başarı (`role="alert"`, `aria-live`). Telefon/e-posta/adres GERÇEK (yer tutucu yok).
- **Erişilebilirlik:** Lightbox ve galeri odak yönetimi, klavye navigasyonu, `role=tablist/tabpanel`, `aria-selected` doğru. Anlamlı `<img>`'lerde `alt`, dekoratiflerde bilinçli `alt=""`.
- **Görsel fallback:** Ürün/kategori/hizmet/blog görselleri `data-img` placeholder ile yükleniyor; dosya yoksa "blueprint" çiziliyor — eksik foto bozuk görsel değil (tek istisna U1).
- **Çift dil:** `dir` + `hreflang` alternatifleriyle doğru kuruluyor; bağlantılar dil değişiminde yeri koruyor.

<!-- /AJAN-3 -->

---

## AJAN 4 — Performans & Optimizasyon Denetim Raporu

**Denetim kapsamı:** Tüm bulgular kaynak kodundan, gerçek dosya boyutlarından (`du`/`wc -c`/`ls -la`) ve yükleme stratejisinden doğrulandı. Canlı ölçüm (Lighthouse) yapılmadı; ağ payload tahminleri dosya boyutları + yükleme mantığından hesaplandı.

### Genel tablo — varlık envanteri (ölçülmüş)

| Klasör | Boyut | Adet | Not |
|---|---|---|---|
| `public/assets/models/` (.glb) | **187 MB** | 10 | En büyük performans riski. Sıkıştırma yok (Draco/meshopt/KTX2 yok) |
| `public/assets/images/` | **27 MB** | 97 (85 jpg, 9 png) | webp/avif **sıfır**. En büyük tekil: `hero-2.png` 2.66 MB |
| `public/assets/css/` | 352 KB | 21 | Tamamı **ham/minify edilmemiş** |
| `public/assets/js/` | 216 KB | 24 | Tamamı **ham/minify edilmemiş** |
| `package.json` | **YOK** | — | Build/minify pipeline **yok** (doğrulandı) |

### Tahmini ilk yük ağırlığı (masaüstü ≥992px, ilk ziyaret)

GLB boyutları (ölçülmüş): tanker-1=17.4MB, tanker-2=27.0MB, tanker-3=36.2MB, tanker-4=24.0MB, tanker-5=33.0MB.

| Sayfa | Layout | Yerel CSS | Yerel JS | CDN JS (raw) | 3D (.glb) | **İlk yük TOPLAM (yaklaşık)** |
|---|---|---|---|---|---|---|
| **Ana sayfa** | yeni | 104 KB | 66 KB | ~1.47 MB | tanker-3 36 MB peşin → scroll'da +101 MB konvoy | **~38 MB ilk boya, ~140 MB tam scroll** |
| **İletişim** | yeni | 124 KB | 95 KB | ~1.47 MB | tanker-1 17.4 MB | **~20 MB** (three motoru boşa) |
| **Ürünler** | inner | 163 KB | +8 KB | yok | yok | **~0.5–2 MB** |
| **Hizmet/Blog/Medya/Teklif** | inner | 139–144 KB | küçük | yok | yok | **~0.3–1 MB** |

### 🔴 Kritik

**P1 — `public/assets/models/` = 187 MB, sıkıştırma sıfır; ana sayfada peşin 36 MB + scroll'da 101 MB.**
`yeni-tanker.js` hero `init()` sayfa açılır açılmaz (≥992px) **tanker-3.glb'yi (36.2 MB) PEŞİN** indiriyor. Scroll yol bölümüne yaklaşınca konvoy **tanker-1+2+4+5 = 101.4 MB** daha iniyor. GLB'lerde **Draco/meshopt/KTX2 yok**. Mobil veride felaket; masaüstünde tam scroll'da ~140 MB. **Çözüm:** Tüm .glb'leri `gltf-transform optimize --compress draco --texture-compress webp` ile sıkıştır (187 MB ≈ **10–20 MB**); hero modelini de IntersectionObserver'a al; layout'a `DRACOLoader`/`KTX2Loader` ekle.

**P2 — `hero-2.png` 2.66 MB + `4bb9…png` 2.06 MB + `about.jpg` 1.72 MB + `hero-11.png` 0.91 MB: dev PNG/JPG, webp/avif yok.**
27 MB görsel; format dökümü **85 jpg + 9 png, 0 webp, 0 avif**. `hero-2.png` (2.66 MB) iki kez `<img>` ile kullanılıyor. Hiçbir görselde `srcset`/`<picture>` yok; mobil de masaüstü PNG indiriyor. **Çözüm:** WebP/AVIF'e çevir (`cwebp -q 80`) + `<picture>`/`srcset`. 27 MB → **~4–6 MB**; `hero-2.png` → ~200 KB.

**P3 — Sıkıştırma (gzip/brotli) ve tarayıcı önbelleği YOK — `public/.htaccess` stok CI4 dosyası.**
`mod_deflate`/`mod_brotli` yok → 352 KB CSS + 216 KB JS sıkıştırılmadan gidiyor. `Cache-Control`/`Expires` yok → `?v=filemtime` cache-busting var ama "uzun sakla" denmediği için her ziyarette 187 MB model + 27 MB görsel tekrar iniyor. `Optimize.php`: framework cache de kapalı. **Çözüm:** `.htaccess`'e `mod_deflate` + `mod_expires` (statik için 1 yıl, hash'li güvenli). Sıfır risk, en yüksek kazanç/efor.

### 🟠 Orta

**P4 — İletişim sayfası ana sayfanın TÜM 3D motorunu boşuna yüklüyor.**
contact'ta `.hero`/`[data-road]` yok ama three (~1.26 MB) + gsap + lenis + yeni-tanker.js boşuna iniyor; üstüne contact kendi `contact-tanker.js` + tanker-1.glb yüklüyor. **Çözüm:** Contact için yalın layout (three+gltf+contact-tanker yeterli); gsap/lenis/yeni-tanker/yeni-app kaldır.

**P5 — Ölü JS (55 KB) ve ölü CSS (125 KB) repoda — hiçbir aktif layout yüklemiyor.**
Ölü JS (15 dosya): `ai-assistant, animations, aos-init, categories-carousel, counter, flow, hero-slider, home, mouse-follower, process-timeline, progress-bars, showcase-hotspots, swiper-init, tanker-3d, __synctest`. Ölü CSS (11 dosya): `main.css` + yalnız onun `@import` ettiği `base/blog/components/flow/layout/redesign/rtl/sections/tanker-3d/typography`. **Çözüm:** 26 dosyayı + `layouts/main.php`'yi sil.

**P6 — CSS/JS minify edilmemiş + birleştirilmemiş; ana sayfa 6 ayrı CSS + 5 ayrı script isteği.**
Build pipeline yok; her dosya ayrı HTTP isteği + ham boyut. **Çözüm:** `esbuild`/`lightningcss` ile birleştir+küçült (104 KB → ~70 KB; +gzip ~16 KB).

**P7 — Üçüncü taraf 3D/animasyon CDN paketi ana sayfada ~1.47 MB ham.**
three.module.js (~1.26 MB), GLTFLoader (~95 KB), gsap (~70 KB), ScrollTrigger (~42 KB), lenis (~7 KB) peşin iniyor (mobilde de). **Çözüm:** three/GLTFLoader'ı dinamik `import()` ile koşullu yükle; lenis'i kaldırmayı değerlendir.

### 🟡 Düşük

**P8 — Marka logosu `assets/logo.png` 36 KB ve 1830×432 px, ekranda 200×47 (~4× büyük).** `emblem.svg` (1.4 KB) mevcut ama kullanılmıyor. **Çözüm:** SVG yap veya 400×94 webp (~6 KB).

**P9 — Ana sayfa hero `<img>`'lerinde width/height yok (CLS riski).** Diğer sayfalarda iyi. **Çözüm:** intrinsic `width`/`height` veya `aspect-ratio` ekle.

**P10 — İmleç takip rAF döngüsü sürekli çalışıyor (masaüstü).** `hover:none`/`reduced-motion`'da doğru kapatılıyor; maliyet düşük ama sürekli. **Çözüm:** idle'da `cancelAnimationFrame`.

### ✅ Sorun Bulunmayan / İyi Durumdaki Alanlar

- **Konvoy 3D tembel yükleme:** 101 MB konvoy IntersectionObserver ile tembel; peşin değil (yine de sıkıştırma şart).
- **Aşamalı geliştirme:** ≥992px değilse / WebGL yoksa / reduced-motion açıksa 3D hiç başlamaz; statik `<img>` fallback. Mobil 3D motorunu çalıştırmıyor.
- **Cache-busting:** Tüm yerel CSS/JS `?v=filemtime()` ile sürümleniyor.
- **Font:** Google Fonts `preconnect` + `display=swap` doğru; FOIT yok.
- **CDN preconnect:** `preconnect https://cdn.jsdelivr.net` mevcut.
- **Render-blocking JS:** Tüm script'ler `defer` veya `type=module` — head'de bloklayan script yok.
- **Tema FOUC önleme:** inline script ile `data-theme` ilk boyadan önce ayarlanıyor.
- **Lazy görseller (inner):** media/blog/contact `loading="lazy"` + `decoding="async"`.
- **Hero LCP:** `hero-1.png` `loading="eager" fetchpriority="high"` — doğru işaretli.
- **Ortam:** `CI_ENVIRONMENT` tanımsız → CI4 varsayılanı **production**; DebugToolbar render edilmiyor, `display_errors=0`.

### 🚀 ÖNCELİK SIRALI HIZLANDIRMA PLANI (AJAN 4)

**P0 — HEMEN:** (1) GLB modelleri Draco+WebP doku ile sıkıştır → 187 MB→~10–20 MB; (2) `.htaccess` gzip + 1 yıl Expires → metin %75 küçülür, ikinci ziyaret 0 byte; (3) Görselleri WebP/AVIF + responsive → 27 MB→~4–6 MB.
**P1 — KISA VADE:** (4) three.js dinamik/koşullu import → mobilde ~1.47 MB inmez; (5) CSS/JS minify+bundle.
**P2 — TEMİZLİK:** (6) 26 ölü dosyayı sil; (7) logoyu SVG/webp; (8) hero width/height; (9) imleç rAF idle, lenis değerlendir.

**Özet beklenen kazanım:** Ana sayfa tam scroll **~140 MB → ~15–25 MB**; ikinci ziyaret **~0 MB**; metin trafiği **~%75 azalır**; mobil **~1.5 MB JS** kurtulur.

<!-- /AJAN-4 -->

---

## AJAN 5 — Sistem & Teslimat Denetim Raporu

**Özet (teslime hazırlık durumu):** Sistem mimarisi sağlam ve çoğu altyapı doğru kurulmuş: mail entegrasyonu kod düzeyinde tam (bildirim + autoreply, hata yönetimi, lead kaydı), WhatsApp/teklif akışı çalışır ve tutarlı tek numaraya gidiyor, SEO meta partial'ı her sayfa için benzersiz başlık/açıklama + canonical + hreflang (x-default dahil) üretiyor, production hata gizleme doğru. **ANCAK canlıya çıkış için kritik engeller var:** (1) `og:image` eksik, (2) `baseURL` hâlâ `localhost`, (3) CSRF kapalı, (4) robots.txt sitemap'i `localhost` + sitemap.xml yok, (5) Gmail app-password düz metin. Bunlar düzeltilmeden canlıya çıkış riskli.

### 🔴 Kritik

**S1 — [App.php:19] `baseURL` production değeri `localhost`.** `public string $baseURL = 'http://localhost:8080/';` ve `.env`'de override **YOK** (`.env` yalnızca `email.*` içeriyor). Canlıda tüm SEO/canonical/hreflang/OG URL'leri + mail + teklif WhatsApp mesajındaki ürün linki `localhost:8080`'i gösterir. **Çözüm:** `.env`'e `app.baseURL = 'https://barlastrailer.com/'` ekle. HTTPS için `forceGlobalSecureRequests = true` değerlendir.

**S2 — [meta.php:14] Varsayılan `og:image` dosyası eksik.** `test -f public/assets/images/og-default.jpg` → **YOK**. Her sayfa `og:image`/`twitter:image` olarak 404 veren URL yayıyor — TEKLIF_AL_PLAN'ın tüm WhatsApp önizleme stratejisinin dayandığı dosya. Ürün detay ürüne özel görseli kontrol ediyor (81 görsel mevcut), ama eşleşmeyenler + diğer tüm sayfalar kırık varsayılana düşüyor. **Çözüm:** `og-default.jpg` (1200×630) ekle.

**S3 — [Filters.php:78-84] CSRF ve Honeypot global filtreleri KAPALI.** `globals.before` içinde `'csrf'` ve `'honeypot'` yorumlanmış. POST rotalarına (`contact`, `ai/ask`) filtre eklenmemiş. `Security.php` `csrfProtection='cookie'` ve form `csrf_field()` bassa da **hiçbir POST'ta CSRF doğrulaması çalışmıyor** — token kozmetik. **Çözüm:** `'csrf'`'i aktif et (AJAX `X-CSRF-TOKEN` zaten tanımlı; Contact.php token tazeliyor). `tokenName`/`cookieName` CI4 varsayılanları — özelleştir.

**S4 — [robots.txt:5 + sitemap yok] Sitemap referansı `localhost`, dosya mevcut değil.** `Sitemap: http://localhost:8080/sitemap.xml`; `find -iname "sitemap*"` → **YOK**. **Çözüm:** 5 dil × tüm sayfalar (hreflang ile) sitemap üret; URL'yi gerçek alan adına güncelle. Üretilene kadar bu satırı kaldırmak yanlış `localhost`'tan iyidir.

**S5 — [.env:31] Gerçek Gmail uygulama şifresi düz metin olarak repo dosyasında.** `email.SMTPPass` (gerçek 16 hane) + `SMTPUser`/`fromEmail` gerçek Gmail. `.gitignore` `.env`'i listeliyor (iyi) ama sır dosya sisteminde açık + sunucuya dağıtılacak. **Çözüm:** App-password'ü **iptal et + yeniden üret** (ifşa kabul et). Canlıda kurumsal posta kullan. `.env`'in repoya girmediğini doğrula.

**S6 — [Email.php vs .env] Config'de SMTP boş; sadece `.env` dolu — `.env` yoksa mail sessizce çöker.** `Email.php` varsayılanları: `protocol='mail'`, `SMTPHost=''`, `fromEmail=''`. Tüm gerçek değerler `.env`'de. Sunucuya `.env` kopyalanmazsa gönderim sessizce başarısız olur; Contact.php hata yönetimi sağlam (lead her zaman `writable/leads`'e yazılıyor) ama **mail GİTMEZ ve fark edilmez.** **Çözüm:** Deploy kontrol listesine ".env mevcut/dolu mu?" ekle; canlıda test başvurusuyla bildirim+autoreply doğrula.

### 🟠 Orta

**S7 — [footer.php:204-208] Telefon FAB butonu yanlış/tutarsız numarayı arıyor.** Yorum "Telefon: 0543 578 08 00" diyor ama `$callHref = 'tel:+905525002000'` (WhatsApp numarası). Kullanıcı "Ara"ya basınca beyan edilen yerine WhatsApp numarasını arar. `$waHref` de numarayı `whatsapp_url()` yerine elle gömüyor (DRY ihlali). **Çözüm:** Doğru numarayı netleştir; footer'ı `whatsapp_url()` helper'ına bağla.

**S8 — [errors/html/error_404.php] 404 sayfası CI4 varsayılanı; marka/dil tutarsız.** `<html lang="en">`, generic kutu — sitenin markası/RTL'i yok. Lokalize metin var ama **ana sayfaya dönüş bağlantısı yok**, kullanıcı çıkmaza giriyor. Geçersiz slug doğru biçimde 404'e düşüyor (doğrulandı). **Çözüm:** Hata sayfalarını site layout'una uydur; `<html lang>`'i `current_locale()` ver; dönüş butonu ekle.

**S9 — [SEO / JSON-LD] Organization ve Product şeması yok.** `ld+json` yalnızca `blog/detail.php`'de `BlogPosting`. Ana sayfada **Organization yok** (logo/sosyal/iletişim — knowledge panel için kritik); ürün detayda **Product yok** (81 görsel + teknik veri mevcutken). **Çözüm:** Layout'a Organization, ürün detaya Product JSON-LD ekle.

**S10 — [docs/TEKLIF_AL_PLAN.md:3] Dokümantasyon kodla çelişiyor (eski plan).** Doküman "PLAN (uygulanmadı)" diyor ama Quote controller/rotalar/picker/helper **tamamen uygulanmış**. Fonksiyonel hata değil; yanıltıcı doküman. **Çözüm:** "uygulandı" olarak güncelle veya arşivle.

### 🟡 Düşük

**S11 — [footer.php:170-172] Yasal/legal bağlantılar boş (`href="#"`).** KVKK/Gizlilik/Çerez placeholder. TR+AB pazarı KVKK/GDPR uyumu için gerçek sayfalar gerekli (form zaten consent topluyor ama bağlanacak politika yok). **Çözüm:** Politika sayfalarını ekle+bağla.

**S12 — [footer.php:147] İletişim kartı `tel:` linki yerel formatta.** `tel:05525002000` (E.164 `+90...` değil). Uluslararası ziyaretçide (RU/AR/FR) `+90` daha güvenilir. **Çözüm:** `tel:+90...`'a geçir.

**S13 — [App.php:136] `appTimezone = 'UTC'`.** Lead/teklif zaman damgaları UTC; satış ekibi TR saati bekliyorsa kafa karışıklığı. **Çözüm:** `Europe/Istanbul` değerlendir (veya raporlamada dönüştür).

### ✅ Sorun Bulunmayan Alanlar

- **Mail gönderim mantığı:** Admin bildirimi + kullanıcı autoreply uygulanmış; `try/catch` + `log_message`; gönderim başarısız olsa bile lead **her zaman** `writable/leads/YYYY-MM.jsonl`'e yazılıyor (mesaj kaybı yok). Autoreply ziyaretçinin diline göre; geçersiz e-posta engelleniyor.
- **Autoreply şablonu:** Tablo tabanlı, satır-içi CSS'li e-posta uyumlu HTML; RTL desteği; `esc()` ile XSS güvenli; düz metin alternatifi; preheader.
- **WhatsApp helper:** Numara tek kaynaktan; `0`→`90` ülke kodu dönüşümü doğru; mesaj `rawurlencode`; boş değere fallback.
- **Teklif akışı:** Ürün/kategori `ProductCatalog` ile doğrulanıyor, geçersizde 404; lokalize WhatsApp mesajı; niyet `writable/quotes`'a loglanıyor; wa.me'ye 302; picker'da `/contact` fallback.
- **SEO meta benzersizliği:** Her sayfa kendi `metaTitle`/`metaDescription`'ı; `<title>`, description, canonical, OG (title/desc/url/image/type/site_name/locale), Twitter `summary_large_image` tam.
- **Hreflang:** 5 dil + `x-default`; `switch_locale_url()` doğru URL'ler; self-referencing dahil; `<head>`'de tek kez (duplikasyon yok).
- **Production güvenliği:** `CI_ENVIRONMENT` tanımsız → CI4 varsayılanı **production** (doğrulandı); `display_errors=0`, `CI_DEBUG=false`; `production.php` generic + `noindex`; detaylı hata görünümü yalnızca non-production → **üretimde iz sızıntısı yok**; debug toolbar kapalı.
- **Honeypot (manuel):** Contact ve Ai gizli `website` alanını kontrol ediyor; erişilebilir biçimde gizli (`clip-path`, `tabindex=-1`, `aria-hidden`). (S3 ile birlikte değerlendirilmeli.)
- **Routing locale güvenliği:** `{locale}` CI4 özel placeholder'ı; bilinmeyen segment default'a düşüyor; `autoRoute=false` — saldırı yüzeyi dar.

**Canlıya çıkış önceliği:** S1 → S2 → S4 → S5/S6 → S3.

<!-- /AJAN-5 -->

---

## 📌 DOĞRULAMA & YÖNTEM NOTU

Bu denetim **5 bağımsız ajan** tarafından, yalnızca gerçek kaynak kodu okunarak ve dosya boyutları/yükleme stratejisi ölçülerek yapıldı. Sentez aşamasında, rapora başlık olan **en yüksek riskli 5 bulgu ayrıca elle yeniden doğrulandı:**

- ✔ **M1** — `yeni.php` `navigation.css`'i yüklüyor ama `initNavigation()`'ı çağırmıyor; `inner.php` çağırıyor → ana sayfa/iletişimde mobil menü ölü.
- ✔ **S1** — `App.php:19` `baseURL = 'http://localhost:8080/'`; `.env`'de override yok.
- ✔ **S3** — `Filters.php` `globals.before`'da `'csrf'` ve `'honeypot'` yorum satırı.
- ✔ **U1/S2** — `public/assets/images/og-default.jpg` dosyası yok ama `meta.php:14` referans veriyor.
- ✔ **P1** — `public/assets/models/` 187 MB / 10 dosya, her model 16–43 MB, Draco/meshopt/KTX2 referansı yok.

Hiçbir dosya değiştirilmedi. Tüm öneriler uygulama için hazır ancak **bilinçli olarak uygulanmadı** (talep: yalnızca rapor + çözüm önerisi).

---

*Rapor sonu — Barlas Trailer Final QA Raporu, 17 Haziran 2026.*
