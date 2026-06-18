# Servis görselleri — çoklu görsel (slider) yapısı

Her servis sayfasının kendi **klasörü** vardır. O klasöre attığınız tüm
fotoğraflar, servis detay sayfasında otomatik olarak **kaydırmalı bir
galeri (slider)** halinde gösterilir. Kod düzenlemeye gerek yoktur —
dosyaları doğru klasöre bırakmanız yeterlidir.

## Nasıl eklenir?

1. Servisin slug'ı ile **aynı adda** bir klasör açın (bkz. liste).
2. Fotoğrafları içine atın.
3. Sıralamayı kontrol etmek için dosyaları numaralandırın:
   `01.jpg`, `02.jpg`, `03.jpg` … İlk sıradaki kapak / ilk slayttır.

Örnek:

```
services/
└── trailer-repair/
    ├── 01.jpg   ← ilk slayt (kapak)
    ├── 02.jpg
    └── 03.jpg
```

## Klasör adları (slug → sayfa)

| Klasör adı                  | Sayfa (URL)                                  |
|-----------------------------|----------------------------------------------|
| `trailer-repair`            | /tr/services/trailer-repair                  |
| `frigo-repair`              | /tr/services/frigo-repair                    |
| `silobas-repair`            | /tr/services/silobas-repair                  |
| `curtain-repair`            | /tr/services/curtain-repair                  |
| `adr-tanker-repair`         | /tr/services/adr-tanker-repair               |
| `lowbed-repair`             | /tr/services/lowbed-repair                   |
| `aluminum-silobas-repair`   | /tr/services/aluminum-silobas-repair         |
| `fuel-tank-repair-center`   | /tr/services/fuel-tank-repair-center         |
| `axle-repair`               | /tr/services/axle-repair                     |
| `suspension-repair`         | /tr/services/suspension-repair               |

> Not: Klasör adı her dilde aynıdır (URL'ler diller arasında değişmez),
> yani tek klasör tüm diller için geçerlidir.

## Kurallar ve öneriler

- **Desteklenen formatlar:** `.jpg`, `.jpeg`, `.png`, `.webp`
- **Oran:** yatay **4:3** önerilir (ör. 1200×900 veya daha büyük).
- **Adet:** istediğiniz kadar; 1 foto varsa oklar/noktalar gizlenir,
  birden fazlaysa slider devreye girer.
- **Sıralama:** dosya adına göre (numara verin: `01`, `02`, `03`).
- Klasör yoksa veya boşsa sayfa eski placeholder görselini gösterir.

## Örnek dosyalar

`trailer-repair/` klasöründeki `01.jpg`, `02.jpg`, `03.jpg` yalnızca
**örnektir** — yapının çalıştığını görmeniz için kondu. Kendi
fotoğraflarınızla değiştirin (veya silin).
