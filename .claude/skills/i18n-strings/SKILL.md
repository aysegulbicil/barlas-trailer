---
name: i18n-strings
description: >-
  Barlas Trailer sitesinde arayüz metni ekleme, değiştirme veya kontrol etme.
  5 dilin (tr, en, ru, ar, fr) dil dosyaları arasında parite sağlar. Kullanıcı
  yeni bir metin/etiket/buton yazısı eklemek, bir çeviriyi güncellemek veya
  "hangi anahtar eksik" diye sormak istediğinde kullan.
---

# i18n Metin Yönetimi

Bu sitede tüm arayüz metinleri CI4 dil dosyalarından gelir. Hardcode metin YASAK.

## Dosya düzeni
`app/Language/{locale}/{Grup}.php` — locale ∈ {tr, en, ru, ar, fr}.
Gruplar: Ai, Blog, Common, Contact, Home, Media, Navigation, Products, Quote, Services.
Her dosya `return [ 'key' => 'value', ... ];` döndürür. View'de `lang('Grup.key')` ile çağrılır.

## Yeni metin eklerken (zorunlu akış)
1. Doğru grubu seç (ör. iletişim formu → `Contact`, menü → `Navigation`).
2. Anahtarı **5 dilin hepsine** ekle — tr, en, ru, ar, fr. Eksik dil bırakma.
3. Anahtar adı İngilizce ve açıklayıcı olsun (`submitButton`, `heroSubtitle`).
4. Çeviriler: tr ve en'i doğru yaz; ru/ar/fr için doğru çeviri ver, emin değilsen
   metni bırakıp kullanıcıya "şu diller için çeviri onayı gerek" diye bildir.
5. Arapça değer RTL'dir; noktalama/yön sorunlarına dikkat et.

## Parite kontrolü (kullanıcı "eksik var mı?" derse)
Tüm grup dosyalarındaki anahtar kümelerini diller arası karşılaştır; bir dilde
olup diğerinde olmayanları raporla. Bilinen boşluk: `Validation.php` yalnızca
`en`'de mevcut.

Hızlı tarama örneği (Bash):
```
for g in Ai Blog Common Contact Home Media Navigation Products Quote Services; do
  echo "== $g =="; for l in tr en ru ar fr; do
    printf "%s: " "$l"; grep -c "=>" "app/Language/$l/$g.php" 2>/dev/null || echo "DOSYA YOK";
  done; done
```
Anahtar sayıları diller arası eşit değilse, hangi anahtarların kayıp olduğunu
`grep` ile isim isim çıkar ve eksik dile ekle.

## Yapma
- View/Controller içine düz metin gömme — hep `lang()` kullan.
- Tek bir dile ekleyip diğerlerini "sonra" diye bırakma.
