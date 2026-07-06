# Barlas Trailer — Claude Code Talimatları

Çok dilli kurumsal tanıtım sitesi. CodeIgniter 4 (appstarter 4.7, PHP 8.2+). **Hibrit model (2026-07-02 revizyonu):** kamusal içerik **dosya tabanlı** kalır (products.json, dil dosyaları, JSONL akışları); **kimlikli dünya** (Jarvis paneli, Shield auth) **SQLite** kullanır: `writable/db/barlas.sqlite` (.env `database.default.*`). Kamusal sayfalara DB sorgusu SOKMA.

## İletişim kuralı
- Kullanıcıyla **Türkçe** konuş. Kod, değişken, dosya adı ve commit mesajları **İngilizce**.

## Çok dillilik (i18n) — en kritik kural
- 15 dil: **tr (varsayılan), en, de, ru, ar, fr, es, it, nl, pl, ro, bg, el, he, zh**. URL öneki ile yönlendirme: `/{locale}/...`. (PDF'in 13 dili + nl/it; RTL = ar **ve he** — `is_rtl()` helper'ı yönetir. Tüm çeviriler Claude üretimi, ana-dil kontrolü bekliyor.)
- Locale çözümü: `app/Filters/LanguageFilter.php` (URI 1. segment) + `locale` çerezi. Bare `/` → `LanguageController::index`.
- UI metinleri **yalnızca** CI4 dil dosyalarından gelir: `app/Language/{locale}/*.php` (Ai, Blog, Common, Contact, Faq, Games, Home, Legal, Markets, Media, Navigation, News, Press, Products, Quiz, Quote, Services, Tools, Vehicle, Wiki).
- **Dosya paritesi zorunlu:** bir dile anahtar eklersen 15 dilin hepsine ekle. (Not: `Validation.php` yalnızca `en`'de — bilinen boşluk. Jarvis paneli görünümleri bilinçli tek dilli Türkçe'dir, parite kuralının kapsamı dışındadır. Wiki/news markdown içerikleri yalnız tr/en/ru/ar/fr'de var — diğer 10 dilde MarkdownContent tr'ye düşer, sayfalar çalışır; çeviri dosyaları sonradan bırakılabilir.)
- Arapça (ar) **RTL**; CSS'te fiziksel değil **mantıksal** özellikler kullan (`margin-inline-start` vb.).

## Çalıştırma
- Site compose-yönetimli `barlas-apache` konteyneriyle (Apache + mod_php, gzip + cache) **:8080**'de sunuluyor; `docker compose up -d` güvenlidir. (Eski elle başlatılan `barlas-trailer` konteyneri emekli.)
- **Perf düzeni (2026-07-06, Windows bind-mount stat vergisine karşı; sayfa üretimi ~3 sn → ~0.2 sn — bozma):** `vendor/` named volume'da (`vendor-data`, bind'ın üzerine biner) — **composer install/update'ten sonra `docker cp vendor/. barlas-apache:/var/www/html/vendor/` şart**, yoksa konteyner eski vendor'la koşar. OPcache `revalidate_freq=60` (Dockerfile): PHP değişikliği en geç 60 sn'de görünür; anında görmek için `docker exec barlas-apache apachectl -k graceful`. CSS/JS/görsel statiktir, bundan etkilenmez.
- Spark komutlarını **www-data ile** çalıştır: `docker exec -u www-data barlas-apache php /var/www/html/spark ...` — root ile çalıştırılırsa oluşan dosyalara Apache yazamaz (SQLite "readonly database" hatası yaşandı).
- ⚠️ Konteynere elle kurulan araçlar (composer, unzip, cwebp) **yeniden oluşturmada silinir** — kalıcı gereken araç Dockerfile'a eklenmeli. (vendor/ artık named volume'da; rebuild'de kalır ama `docker compose down -v` volume'u siler → tekrar `docker cp` gerekir.)
- Sayfa önbelleği aktif (`cachePage`): view/controller değişikliği görünmüyorsa `spark cache:clear`.
- Ajanlar: `spark agents:{rates,health,digest,faq-candidates,reminders,content-factory,analyst,domain-watch}` — kill-switch bayrağı `writable/data/flags/` (panel > Ajanlar'dan da yönetilir); VPS'te cron'a bağlanacak (docblock'larda hazır). Not: domain-watch DNS taraması konteynerde ~9 dk sürer (NXDOMAIN zaman aşımı) — haftalık cron'da sorun değil.
- Jarvis paneli: `/panel` (Shield `session` filtresi + superadmin grubu). Kayıt/magic-link kapalı; kullanıcı yalnız `spark panel:admin <email> <parola>` ile açılır. Shield yüzünden `Security::$csrfProtection = 'session'` zorunlu; `auth`+`setting` helper'ları Autoload'da (filtreler controller'dan önce koşar).
- Testler: `composer test` (PHPUnit).

## Gömülü uygulamalar (apps/) — 2026-07-05
- Üç bağımsız iç araç repoya gömülü, aynı Apache'den Alias ile sunulur (`deploy/apache-apps.conf` → compose mount; istekler ana CI4'e hiç girmez): **`/qr`** = `apps/qr` (ikinci bir CI4 4.7 uygulaması, personel/QR yoklama, **kendi girişi var**, MySQLi), **`/fatura`** = `apps/fatura` (saf PHP fatura takip, PDO), **`/teklif`** = `apps/teklif` (saf PHP+JS teklif üretici, DB'siz — JSON + `offers/` klasörleri).
- **Ortak giriş kapısı:** fatura+teklif `apps/_shared/auth/guard.php` (Apache `auto_prepend_file`) ile korunur; giriş `/apps-auth/login.php` — kimlik kaynağı **panelle aynı Shield kullanıcısı** (barlas.sqlite, düz PDO+password_verify). Teklif'in statik dosyaları (offers/ müşteri verisi!) `apps/teklif/gate.php` üzerinden servis edilir. `/qr/q/{kod}` bilinçli girişsizdir (QR okutma).
- **Veritabanı:** `barlas-db` (mariadb:11) konteyneri, DB'ler `qr_sistemi` + `fatura_takip`, kullanıcı `apps` (parola compose `${APPS_DB_PASSWORD:-apps_pass}`; ilk kurulum `deploy/db-init/01-init.sh`). Ana site SQLite'ta kalır; kamusal sayfalara MariaDB sorgusu da SOKMA.
- **Çerez izolasyonu:** ana site `ci_session`, qr `qr_session` (path `/qr`, `apps/qr/.env`), kapı `BARLASAPPS` — üçü aynı hostta çakışmadan yaşar. qr'ın `.env`'i git'te YOK; şablon `apps/qr/env.embedded.example`.
- qr spark: `docker exec -u www-data barlas-apache php /var/www/html/apps/qr/spark ...`; qr composer ayrı proje (`docker run --rm -v "$PWD/apps/qr":/app composer:2 install`). Teklif PDF'i konteynerdeki Google Chrome ile üretilir (`_common.php` Linux dalı; Debian'ın chromium paketi WSL2/Docker'da çöküyor, Chrome .deb kullanıldı) — Windows/XAMPP dalı korunmuştur.
- Bu uygulamaların arayüzleri bilinçli **tek dilli Türkçe** (Jarvis paneli gibi) — 15 dil parite kuralının kapsamı dışındadır.

## İçerik modeli
- Ürünler: `app/Data/products.json` — **11 kategori / 76 ürün / 136 varyant** (tek doğruluk kaynağı; menü buradan üretilir, kırık link yoktur). **Katalog i18n (2026-07-06):** `products.{locale}.json` overlay'leri slug bazında ad/model (+varsa spec) çevirisi bindirir (`ProductCatalog::localize()`); eksik her parça tr'ye düşer. en overlay'i spec'li (ana varyantlar), diğer 13 dil yalnız ad katmanı. Slug'lar tüm dillerde AYNI kalır — asla çevirme. Blog: front-matter'lı Markdown (Phase 2, henüz yok — metinler dil dosyalarında).
- Markdown içerik motoru (`app/Libraries/MarkdownContent.php`): `app/Data/content/{wiki,news}/{locale}/{slug}.md` — slug tüm dillerde aynı, çeviri yoksa tr'ye düşer. Ülke sayfaları kayıt defteri: `app/Data/markets.json` (adlar `Markets.php` dil dosyalarında).
- **İçerik Fabrikası (§7.6):** yeni araç kaydı → `agents:content-factory` taslak üretir (`writable/data/content/drafts/`) → Panel > İçerik'ten onay → `app/Data/content/news/tr/{slug}.md` yayınlanır (+`cache()->clean()`). v1 şablon tabanlı; AI API bağlanınca yalnız `buildDraft()` değişir.
- Konfigüratör/kur verisi: `writable/data/rates.json` (agents:rates yazar). Operasyon akışları JSONL: `writable/{leads,quotes,ai-logs}/` + `writable/data/{agents,alerts,briefings}/`.

## Dokunma / dikkat
- Kullanıcının Codex ile eklediği dosyalar korunacak: `public/assets/js/categories-carousel.js`, `ai-assistant.js` ve benzeri post-phase geliştirmeler — **değiştirme**.
- `docs/ROADMAP.md` ≈ `PROJECT_STRUCTURE.md` kopyası; gerçek yol haritası değil.
- Referans logo duvarı (2026-07-06): bant tema duyarlı — koyu temada `#181849` (`yeni.css .refs__belt`), açık temada beyaz (`theme-light.css` ezmesi). Chip/kutu YOK — logolar doğrudan bant üzerinde (kullanıcı tercihi; eski tek tip chip sistemi ve `-dark` konvansiyonu kaldırıldı). Opak beyaz kutulu kaynak logolar kendi zeminiyle görünür; nihai cila = kaynak görselleri şeffaflaştırmak.

## Yapı özeti
- `app/Controllers/` — Home, Products, Services, Blog, Contact, Quote, Media, Ai, Language, Tools (hesaplayıcılar+quiz), Faq, Games, Legal, Wiki, Press, Markets (ülke sayfaları), News (üretim haberleri), Vehicle (QR kart + şasi doğrulama), Sitemap (sitemap.xml+robots.txt dinamik), Panel (Jarvis).
- `app/Commands/Agents/` — ajan ordusu (AgentCommand tabanı + rates/health/digest/faq-candidates/reminders/content-factory/analyst/domain-watch); `app/Commands/PanelAdmin.php` — panel kullanıcısı.
- `app/Views/` — layouts (yeni, inner, panel) + partials (nav/mega-*, assistant-widget). Stiller `public/assets/css/`, davranış `public/assets/js/` (GSAP/Lenis/THREE CDN, ES modülleri).
- Ana sayfa "çocuk testi" düzenindedir (hero → 4 seçenek → 3D konvoy → AI konsol → referanslar); eski bölüm partial'ları `pages/home/` altında yedek durur.
