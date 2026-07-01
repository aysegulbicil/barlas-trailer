# Barlas Trailer — Claude Code Talimatları

Çok dilli kurumsal tanıtım sitesi. CodeIgniter 4 (appstarter 4.7, PHP 8.2+). İçerik **dosya tabanlı** — veritabanı / admin paneli **yok**.

## İletişim kuralı
- Kullanıcıyla **Türkçe** konuş. Kod, değişken, dosya adı ve commit mesajları **İngilizce**.

## Çok dillilik (i18n) — en kritik kural
- 5 dil: **tr (varsayılan), en, ru, ar, fr**. URL öneki ile yönlendirme: `/{locale}/...`.
- Locale çözümü: `app/Filters/LanguageFilter.php` (URI 1. segment) + `locale` çerezi. Bare `/` → `LanguageController::index`.
- UI metinleri **yalnızca** CI4 dil dosyalarından gelir: `app/Language/{locale}/*.php` (Ai, Blog, Common, Contact, Home, Media, Navigation, Products, Quote, Services).
- **Dosya paritesi zorunlu:** bir dile anahtar eklersen 5 dilin hepsine ekle. (Not: şu an `Validation.php` yalnızca `en`'de var — bu bilinen bir boşluk.)
- Arapça (ar) **RTL**; CSS'te fiziksel değil **mantıksal** özellikler kullan (`margin-inline-start` vb.).

## Çalıştırma
- Site Docker konteyneri `barlas-trailer` içinde elle başlatılmış `php spark serve` ile **:8080**'de sunuluyor.
- ⚠️ `docker restart barlas-trailer` **yapma** — konteyner içi elle başlatılan sunucuyu durdurur.
- Testler: `composer test` (PHPUnit) veya `./spark ...` komutları.

## İçerik modeli
- Ürünler: planlanan `app/Data/products/...` + varlıklar `public/assets/products/`. Blog: front-matter'lı Markdown (Phase 2).
- 14 ürün kategorisi; menü URL şeması `products/{cat-slug}` ve `products/{cat-slug}/{sub-slug}` (Phase 2'ye kadar bazıları 404 verir — normaldir).

## Dokunma / dikkat
- Kullanıcının Codex ile eklediği dosyalar korunacak: `public/assets/js/categories-carousel.js`, `ai-assistant.js` ve benzeri post-phase geliştirmeler — **değiştirme**.
- `docs/ROADMAP.md` ≈ `PROJECT_STRUCTURE.md` kopyası; gerçek yol haritası değil.
- Referans logo duvarı tema-duyarlı arka plan kullanır; karışık/opak logolar (4/5/6/15) tema başına bozulur — düzeltme = kaynak görselleri değiştirmek, CSS değil.

## Yapı özeti
- `app/Controllers/` — Home, Products, Services, Blog, Contact, Quote, Media, Ai, Language.
- `app/Views/` — layouts + partials (nav/mega-*). Stiller `public/assets/css/`, davranış `public/assets/js/` (GSAP/AOS/Swiper CDN, ES modülleri).
