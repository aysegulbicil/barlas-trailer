# Performans — Yavaşlık Teşhisi ve Çözümleri

Tarih: 2026-06-30

## Özet

Sitenin "yavaşlığı" **frontend kodundan değil, sunucu katmanından** geliyordu.
Frontend (CSS/JS yükleme sırası, fontlar, 3D) zaten optimize durumdaydı.

## Kök neden

Site, Docker container'ında **`php spark serve`** ile sunuluyor; bu da PHP'nin
yerleşik **`php -S`** geliştirme sunucusunu başlatıyor. Bu sunucunun üç önemli kısıtı var:

1. **`.htaccess` tamamen yok sayılır.** `public/.htaccess` içindeki gzip/brotli
   sıkıştırma, tarayıcı önbelleği (Cache-Control/Expires) ve MIME kuralları
   **hiç çalışmıyordu.** Ölçülen sonuç:
   - `yeni.css` **64.6 KB sıkıştırılmadan** iniyordu (gzip ile 15.1 KB → %76 küçük).
   - Hiçbir asset'te `Cache-Control` yoktu → **her gezinmede** tüm CSS/JS **ve
     MB'larca `.glb` 3D modeli** (ana sayfa konvoyu ~8 MB) yeniden iniyordu.
2. **Tek thread'li.** Onlarca asset isteği sırayla sunuluyor; büyük bir model
   inerken diğer her şey bekliyor.
3. **OPcache fiilen kapalıydı.** `opcache.enable_cli=Off` olduğundan CLI tabanlı
   `php -S` her istekte tüm CodeIgniter'ı yeniden derliyordu (soğuk istek ~1.4 sn).

Ek olarak proje Windows diskinden (`C:\...`) container'a **bind-mount** edilmiş;
bu da dosya okumalarını yavaşlatıp yukarıdaki maliyetleri büyütüyor.

## Yapılan düzeltmeler

### 1. Apache + mod_php sunucu kurulumu (asıl çözüm) — `Dockerfile`, `docker-compose.yml`
Gerçek bir web sunucusu `.htaccess`'i okur; böylece tek hamlede:
- **gzip sıkıştırma** açılır (metin asset'lerinde ~%76 daha az transfer),
- **uzun süreli tarayıcı önbelleği** devreye girer (tekrar ziyarette CSS/JS/model
  yeniden inmez),
- **keep-alive + eşzamanlı** istek sunumu,
- **OPcache** (CLI olmayan SAPI'de açık) PHP'yi bir kez derleyip bellekte tutar.

Geçiş (eski container 8080'i tutuyorsa önce durdurun):
```bash
docker stop barlas-trailer
docker compose up -d --build
# http://localhost:8080
```
Doğrulandı (test :8085): `Content-Encoding: gzip`, `Cache-Control: max-age=31536000`,
8 asset için transfer 195 KB → 46 KB.

### 2. OPcache (geçici, mevcut container için)
Mevcut `php spark serve` sunucusunda OPcache'i etkinleştiren ayar dosyası
container'a yazıldı (`/etc/php/8.5/cli/conf.d/99-barlas-opcache.ini`,
`opcache.enable_cli=1`). **Etki için sunucu yeniden başlatılmalı.** Kalıcı çözüm
yukarıdaki Apache kurulumudur (container yeniden oluşturulunca bu ayar kaybolur).

## Sonraki adımlar (opsiyonel, daha fazla hız)

- **Projeyi WSL native diskine taşıyın** (`~/barlas` gibi). Windows→Docker
  bind-mount yavaştır; native dosya sistemi PHP I/O'sunu belirgin hızlandırır.
- **Sayfa önbelleği (CI4):** Home/Products/Services/Media gibi statik sayfalarda
  `$this->cachePage(...)` ile render maliyeti (~0.5 sn) sıfırlanabilir. İçerik
  dosya tabanlı olduğundan değişiklikte `php spark cache:clear` gerekir.
- **Kullanılmayan büyük dosyalar:** `public/assets/images/about.jpg` (2 MB) artık
  kullanılmıyor (CSS `about.webp`'i çağırıyor); `dark-logo.orig.png` gibi `.orig`
  dosyaları depoyu şişiriyor — silinebilir.
- Üretimde maksimum hız için imajda `opcache.validate_timestamps=0` (her dağıtımda
  yeniden başlatma şartıyla).
