# Barlas Trailer — Canlıya Alma Rehberi

Sıfır VPS'ten çalışan HTTPS'li siteye. Her adım kopyala-yapıştır; toplam ~30 dk.
Bu rehberdeki tüm komutlar **sunucuda** çalıştırılır (aksi belirtilmedikçe).

## 0. Gereksinimler

- **VPS**: 2 vCPU / 4 GB RAM / 40 GB disk yeterli (öneri: Hetzner CX22 ~€4/ay
  veya DigitalOcean Basic). İşletim sistemi: Ubuntu 24.04 LTS.
- **Domain**: DNS yönetimine erişim (A kaydı ekleyebilmelisin).
- Yerelden SSH erişimi: `ssh root@SUNUCU-IP`

## 1. DNS

Domain sağlayıcının panelinden iki A kaydı ekle (TTL varsayılan kalsın):

| Tip | Ad  | Değer     |
|-----|-----|-----------|
| A   | @   | SUNUCU-IP |
| A   | www | SUNUCU-IP |

DNS yayılırken (5 dk–1 saat) sunucu kurulumuna geçebilirsin — Caddy,
kayıt yayılır yayılmaz sertifikayı kendiliğinden alır.

## 2. Sunucu hazırlığı

```bash
# Saat dilimi (cron zamanlamaları buna göre çalışır)
timedatectl set-timezone Europe/Istanbul

# Docker + git
curl -fsSL https://get.docker.com | sh
apt-get install -y git

# Güvenlik duvarı: yalnız SSH + HTTP + HTTPS
ufw allow OpenSSH && ufw allow 80/tcp && ufw allow 443/tcp && ufw --force enable
```

## 3. Projeyi kur

```bash
cd /opt
git clone https://github.com/aysegulbicil/barlas-trailer.git
cd barlas-trailer

# PHP bağımlılıkları (vendor/ git'te değildir) — tek seferlik composer koşusu
docker run --rm -v "$PWD":/app composer:2 install --no-dev --ignore-platform-reqs

# Üretim ayarları: domaini düzenle (BARLAS-DOMAIN.com → gerçek domain)
cp env.production.example .env
nano .env                    # app.baseURL satırındaki domaini yaz
nano deploy/Caddyfile        # 3 yerdeki domaini yaz

# Apache (www-data, uid 33) writable'a yazabilsin — bunu atlarsan
# SQLite "readonly database" hatası alırsın (yaşandı, bilinen tuzak)
chown -R 33:33 writable
```

## 4. Başlat

```bash
docker compose -f docker-compose.prod.yml up -d --build
```

DNS yayıldıysa 1–2 dk içinde `https://DOMAIN` sertifikalı şekilde açılır.
Kontrol: `docker compose -f docker-compose.prod.yml logs -f proxy`
("certificate obtained" satırını görmelisin).

## 5. İlk kurulum (tek seferlik)

Veritabanı git'te olmadığı için sunucuda sıfırdan oluşturulur:

```bash
# Shield + uygulama tabloları
docker exec -u www-data barlas-apache php /var/www/html/spark migrate --all

# Panel yöneticisi (parola en az 10 karakter — YENİ ve güçlü bir parola seç)
docker exec -u www-data barlas-apache php /var/www/html/spark panel:admin mehmet.quapo@icloud.com 'YENI-GUCLU-PAROLA'

# Kur verisini hemen çek (cron'u beklemeden konfigüratör çalışsın)
docker exec -u www-data barlas-apache php /var/www/html/spark agents:rates
```

Doğrula: `https://DOMAIN/panel` → giriş yap, dashboard açılmalı.

## 6. Cron (ajan ordusu + yedek)

```bash
crontab -e     # açılan dosyaya deploy/crontab içeriğini yapıştır, kaydet
chmod +x /opt/barlas-trailer/deploy/backup.sh
```

Ertesi sabah panel > Ajanlar ekranında koşu günlüklerini görmelisin.

## 7. Yedekler

`deploy/crontab` her gece 04:30'da `writable/`'ı `/var/backups/barlas/`'a
sıkıştırır (14 gün saklanır). **Bu klasördeki veri (lead'ler, teklifler,
SQLite) git'te YOKTUR — tek kopya bu yedeklerdir.** Ayda bir kontrol et:

```bash
ls -lh /var/backups/barlas/ | tail -3
```

İdeali: yedekleri sunucu dışına da kopyalamak (ör. yerel makineye
`scp` ya da bir object storage). Sunucu ölürse site kodu GitHub'dan
döner ama müşteri verisi ancak bu yedeklerden döner.

## 8. Güncelleme akışı (her deploy)

```bash
cd /opt/barlas-trailer
git pull
docker compose -f docker-compose.prod.yml restart web   # OPcache prod modda — şart
docker exec -u www-data barlas-apache php /var/www/html/spark cache:clear
```

composer.json değiştiyse pull'dan sonra bir de:
```bash
docker run --rm -v "$PWD":/app composer:2 install --no-dev --ignore-platform-reqs
```

## 9. Sorun giderme

| Belirti | Sebep / Çözüm |
|---|---|
| SQLite "readonly database" | `writable/` sahipliği bozulmuş → `chown -R 33:33 writable` |
| Kod değişikliği görünmüyor | Sayfa önbelleği + OPcache → adım 8'deki restart + cache:clear |
| Sertifika alınamıyor | DNS henüz yayılmamış → `dig +short DOMAIN` sunucu IP'sini dönmeli |
| Panel'e girilemiyor | Parola sıfırla: `spark panel:admin <email> <yeni-parola>` (aynı komut günceller) |
| domain-watch 9 dk sürüyor | Normal (NXDOMAIN zaman aşımı) — haftalık cron'da sorun değil |
