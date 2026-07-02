# Barlas Trailer — Claude Code Talimatları

Çok dilli kurumsal tanıtım sitesi. CodeIgniter 4 (appstarter 4.7, PHP 8.2+). **Hibrit model (2026-07-02 revizyonu):** kamusal içerik **dosya tabanlı** kalır (products.json, dil dosyaları, JSONL akışları); **kimlikli dünya** (Jarvis paneli, Shield auth) **SQLite** kullanır: `writable/db/barlas.sqlite` (.env `database.default.*`). Kamusal sayfalara DB sorgusu SOKMA.

## İletişim kuralı
- Kullanıcıyla **Türkçe** konuş. Kod, değişken, dosya adı ve commit mesajları **İngilizce**.

## Çok dillilik (i18n) — en kritik kural
- 10 dil: **tr (varsayılan), en, de, ru, ar, fr, es, it, nl, pl**. URL öneki ile yönlendirme: `/{locale}/...`. (de/nl/it/pl/es 2026-07-02'de eklendi — kullanıcının dil listesi, PDF'in 13'lü listesi DEĞİL; çeviriler Claude üretimi, ana-dil kontrolü bekliyor.)
- Locale çözümü: `app/Filters/LanguageFilter.php` (URI 1. segment) + `locale` çerezi. Bare `/` → `LanguageController::index`.
- UI metinleri **yalnızca** CI4 dil dosyalarından gelir: `app/Language/{locale}/*.php` (Ai, Blog, Common, Contact, Faq, Games, Home, Legal, Markets, Media, Navigation, News, Press, Products, Quiz, Quote, Services, Tools, Vehicle, Wiki).
- **Dosya paritesi zorunlu:** bir dile anahtar eklersen 10 dilin hepsine ekle. (Not: `Validation.php` yalnızca `en`'de — bilinen boşluk. Jarvis paneli görünümleri bilinçli tek dilli Türkçe'dir, parite kuralının kapsamı dışındadır. Wiki/news markdown içerikleri de/nl/it/pl/es'te henüz yok — MarkdownContent tr'ye düşer, sayfalar çalışır; çeviri dosyaları sonradan bırakılabilir.)
- Arapça (ar) **RTL**; CSS'te fiziksel değil **mantıksal** özellikler kullan (`margin-inline-start` vb.).

## Çalıştırma
- Site compose-yönetimli `barlas-apache` konteyneriyle (Apache + mod_php, gzip + cache) **:8080**'de sunuluyor; `docker compose up -d` güvenlidir. (Eski elle başlatılan `barlas-trailer` konteyneri emekli.)
- Spark komutlarını **www-data ile** çalıştır: `docker exec -u www-data barlas-apache php /var/www/html/spark ...` — root ile çalıştırılırsa oluşan dosyalara Apache yazamaz (SQLite "readonly database" hatası yaşandı).
- ⚠️ Konteynere elle kurulan araçlar (composer, unzip, cwebp) **yeniden oluşturmada silinir** — kalıcı gereken araç Dockerfile'a eklenmeli; vendor/ bind-mount'ta olduğu için paketler kalır.
- Sayfa önbelleği aktif (`cachePage`): view/controller değişikliği görünmüyorsa `spark cache:clear`.
- Ajanlar: `spark agents:{rates,health,digest,faq-candidates,reminders,content-factory,analyst,domain-watch}` — kill-switch bayrağı `writable/data/flags/` (panel > Ajanlar'dan da yönetilir); VPS'te cron'a bağlanacak (docblock'larda hazır). Not: domain-watch DNS taraması konteynerde ~9 dk sürer (NXDOMAIN zaman aşımı) — haftalık cron'da sorun değil.
- Jarvis paneli: `/panel` (Shield `session` filtresi + superadmin grubu). Kayıt/magic-link kapalı; kullanıcı yalnız `spark panel:admin <email> <parola>` ile açılır. Shield yüzünden `Security::$csrfProtection = 'session'` zorunlu; `auth`+`setting` helper'ları Autoload'da (filtreler controller'dan önce koşar).
- Testler: `composer test` (PHPUnit).

## İçerik modeli
- Ürünler: `app/Data/products.json` — **11 kategori / 76 ürün / 136 varyant** (tek doğruluk kaynağı; menü buradan üretilir, kırık link yoktur). Blog: front-matter'lı Markdown (Phase 2, henüz yok — metinler dil dosyalarında).
- Markdown içerik motoru (`app/Libraries/MarkdownContent.php`): `app/Data/content/{wiki,news}/{locale}/{slug}.md` — slug tüm dillerde aynı, çeviri yoksa tr'ye düşer. Ülke sayfaları kayıt defteri: `app/Data/markets.json` (adlar `Markets.php` dil dosyalarında).
- **İçerik Fabrikası (§7.6):** yeni araç kaydı → `agents:content-factory` taslak üretir (`writable/data/content/drafts/`) → Panel > İçerik'ten onay → `app/Data/content/news/tr/{slug}.md` yayınlanır (+`cache()->clean()`). v1 şablon tabanlı; AI API bağlanınca yalnız `buildDraft()` değişir.
- Konfigüratör/kur verisi: `writable/data/rates.json` (agents:rates yazar). Operasyon akışları JSONL: `writable/{leads,quotes,ai-logs}/` + `writable/data/{agents,alerts,briefings}/`.

## Dokunma / dikkat
- Kullanıcının Codex ile eklediği dosyalar korunacak: `public/assets/js/categories-carousel.js`, `ai-assistant.js` ve benzeri post-phase geliştirmeler — **değiştirme**.
- `docs/ROADMAP.md` ≈ `PROJECT_STRUCTURE.md` kopyası; gerçek yol haritası değil.
- Referans logo duvarı tema-duyarlı arka plan kullanır; karışık/opak logolar (4/5/6/15) tema başına bozulur — düzeltme = kaynak görselleri değiştirmek, CSS değil.

## Yapı özeti
- `app/Controllers/` — Home, Products, Services, Blog, Contact, Quote, Media, Ai, Language, Tools (hesaplayıcılar+quiz), Faq, Games, Legal, Wiki, Press, Markets (ülke sayfaları), News (üretim haberleri), Vehicle (QR kart + şasi doğrulama), Sitemap (sitemap.xml+robots.txt dinamik), Panel (Jarvis).
- `app/Commands/Agents/` — ajan ordusu (AgentCommand tabanı + rates/health/digest/faq-candidates/reminders/content-factory/analyst/domain-watch); `app/Commands/PanelAdmin.php` — panel kullanıcısı.
- `app/Views/` — layouts (yeni, inner, panel) + partials (nav/mega-*, assistant-widget). Stiller `public/assets/css/`, davranış `public/assets/js/` (GSAP/Lenis/THREE CDN, ES modülleri).
- Ana sayfa "çocuk testi" düzenindedir (hero → 4 seçenek → 3D konvoy → AI konsol → referanslar); eski bölüm partial'ları `pages/home/` altında yedek durur.
