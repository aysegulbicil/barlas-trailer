# Barlas Trailer — Apache + mod_php üretim/geliştirme imajı
#
# NEDEN: Site şimdiye dek `php spark serve` (PHP yerleşik `php -S`) ile
# sunuluyordu. `php -S` tek thread'lidir ve `.htaccess`'i TAMAMEN yok sayar;
# bu yüzden public/.htaccess içindeki gzip/brotli sıkıştırma, tarayıcı
# önbelleği (Cache-Control/Expires) ve doğru MIME kuralları HİÇ çalışmıyordu.
# Sonuç: her gezinmede tüm CSS/JS ve MB'larca .glb modeli sıkıştırılmadan ve
# önbelleğe alınmadan yeniden iniyordu + tüm asset'ler sırayla sunuluyordu.
#
# Apache + mod_php bunların hepsini çözer: .htaccess kuralları devreye girer
# (sıkıştırma + uzun süreli önbellek), bağlantılar keep-alive olur, istekler
# eşzamanlı sunulur ve opcache (CLI olmayan SAPI'de zaten açık) PHP'yi bir
# kez derleyip belleğe alır.
FROM php:8.3-apache

# CI4 için gerekli eklentiler (intl = i18n zorunlu, mbstring) + opcache
RUN apt-get update && apt-get install -y --no-install-recommends \
        libicu-dev libonig-dev \
    && docker-php-ext-install -j"$(nproc)" intl mbstring opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# .htaccess kurallarının uygulanması için gereken Apache modülleri:
#   rewrite -> ön denetleyici yönlendirmesi (index.php)
#   deflate -> gzip sıkıştırma   |  expires/headers -> tarayıcı önbelleği
RUN a2enmod rewrite deflate expires headers

# DocumentRoot'u public/ klasörüne al ve orada .htaccess'e izin ver.
RUN sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
 && printf '<Directory /var/www/html/public>\n    AllowOverride All\n    Require all granted\n    Options FollowSymLinks\n</Directory>\n' \
      > /etc/apache2/conf-available/zz-barlas.conf \
 && a2enconf zz-barlas

# OPcache — geliştirici dostu: değişen PHP dosyaları en geç 2 sn'de algılanır,
# ama derlenmiş bytecode bellekte tutulur (her istekte yeniden derleme yok).
# Üretimde maksimum hız için validate_timestamps=0 yapıp imajı yeniden başlatın.
RUN { \
      echo 'opcache.enable=1'; \
      echo 'opcache.memory_consumption=192'; \
      echo 'opcache.interned_strings_buffer=16'; \
      echo 'opcache.max_accelerated_files=20000'; \
      echo 'opcache.validate_timestamps=1'; \
      echo 'opcache.revalidate_freq=2'; \
    } > /usr/local/etc/php/conf.d/zz-opcache.ini

WORKDIR /var/www/html
