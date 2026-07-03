<?php
/**
 * cinema-intro.php — sinematik açılış perdesi (v3.0 §1)
 * =============================================================================
 * 3–4 saniyelik açılış sekansı: gece otoyolu → uzaktan yaklaşan farlar →
 * kameranın önünden süpürülüp geçen dorse silüeti → şasi çizgisi üzerinde
 * kaynak kıvılcımıyla tutuşan logo → perde açılır, hero görünür.
 *
 * ⚠️ SAYFA ÖNBELLEĞİ KISITI (cachePage): Sunucu çıktıyı çereze göre
 * ÇEŞİTLEYEMEZ — aynı HTML herkese gider. Bu yüzden partial HER ZAMAN
 * render edilir ve varsayılan olarak gizlidir ([hidden] + aria-hidden).
 * "Gösterilsin mi?" kararı %100 İSTEMCİ tarafındadır: cinema-intro.js,
 * BarlasCinema katmanına (tier) ve 'cine_intro' oturum çerezine bakarak
 * ya perdeyi açar ya da öğeyi DOM'dan söküp atar. Sunucuda koşul YOK.
 *
 * Sahne tamamen CSS/SVG'dir (görsel yalnızca mevcut logo). Atla düğmesi ilk
 * kareden itibaren görünürdür; yerleşimi RTL için mantıksal özelliklerle
 * yapılır (inset-inline-end). Ses yok.
 *
 * Bağımlılıklar: cinema-core.js (window.BarlasCinema), cinema-intro.css,
 * cinema-intro.js. Dil anahtarları: Common.site_name, Common.cine_intro_skip.
 */
?>
<div class="cine-intro" data-cine-intro hidden aria-hidden="true">
    <div class="cine-intro__scene">
        <!-- Ufuk çizgisi ışıması: gecenin içinde belli belirsiz şehir aydınlığı -->
        <div class="cine-intro__horizon"></div>

        <!-- Perspektif yol: rotateX'li zemin düzlemi — kenar çizgileri doğal
             biçimde ufka doğru daralır; kesik orta şerit transform ile akar -->
        <div class="cine-intro__ground">
            <div class="cine-intro__asphalt">
                <span class="cine-intro__dashes"></span>
            </div>
        </div>

        <!-- Uzaktan yaklaşan iki far: büyüyüp parlayan ışık noktaları -->
        <span class="cine-intro__headlight cine-intro__headlight--left"></span>
        <span class="cine-intro__headlight cine-intro__headlight--right"></span>

        <!-- Far patlaması: dorse geçerken kareyi kısaca yıkayan ışık -->
        <div class="cine-intro__flare"></div>

        <!-- Dorse silüeti: kameranın hemen önünden süpürülüp geçer.
             Arkasındaki çizgiler hareket bulanıklığı yanılsaması verir. -->
        <div class="cine-intro__truck">
            <span class="cine-intro__streak cine-intro__streak--a"></span>
            <span class="cine-intro__streak cine-intro__streak--b"></span>
            <svg class="cine-intro__truck-svg" viewBox="0 0 760 210" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                <!-- Dorse kutusu -->
                <rect class="cine-intro__truck-body" x="16" y="30" width="444" height="120" rx="10"/>
                <!-- Çekici: kabin + eğimli ön cam + kaput + tampon -->
                <path class="cine-intro__truck-body" d="M478 150 L478 60 Q478 50 488 50 L586 50 L622 88 L688 88 Q700 88 703 100 L707 132 L717 136 L717 150 Z"/>
                <!-- Dorse-çekici arasını alttan bağlayan şasi köprüsü -->
                <rect class="cine-intro__truck-body" x="440" y="140" width="60" height="12"/>
                <!-- Kabin camı: silüet içinde hafif ışık -->
                <path class="cine-intro__truck-glass" d="M592 58 L616 84 L588 84 L588 58 Z"/>
                <!-- Tekerlekler: dorse tandem + çekici tandem + ön aks -->
                <circle class="cine-intro__truck-wheel" cx="84"  cy="172" r="26"/>
                <circle class="cine-intro__truck-wheel" cx="146" cy="172" r="26"/>
                <circle class="cine-intro__truck-wheel" cx="498" cy="172" r="26"/>
                <circle class="cine-intro__truck-wheel" cx="556" cy="172" r="26"/>
                <circle class="cine-intro__truck-wheel" cx="668" cy="172" r="26"/>
            </svg>
        </div>

        <!-- Final: kendini çizen şasi konturu + gezen kaynak noktası +
             kıvılcım patlaması + tutuşan logo -->
        <div class="cine-intro__finale">
            <div class="cine-intro__logo-wrap">
                <!-- Koyu zemin için beyaz yazılı logo (header'ın on-dark varyantı) -->
                <img class="cine-intro__logo" src="<?= base_url('assets/logo.png') ?>" alt="<?= esc(lang('Common.site_name'), 'attr') ?>" width="318" height="75" decoding="async">
                <span class="cine-intro__logo-flash"></span>
            </div>
            <div class="cine-intro__chassis-wrap">
                <svg class="cine-intro__chassis" viewBox="0 0 360 120" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                    <!-- Ana şasi rayı + traversler (tek path: dashoffset ile çizilir) -->
                    <path class="cine-intro__chassis-line" pathLength="1" d="M6 44 H354 L354 58 H6 Z M70 44 V58 M130 44 V58 M190 44 V58 M250 44 V58 M310 44 V58"/>
                    <!-- King-pim + dayama ayakları -->
                    <path class="cine-intro__chassis-line cine-intro__chassis-line--late" pathLength="1" d="M28 58 V72 M96 58 V94 M106 58 V94 M90 94 H112"/>
                    <!-- Akslar -->
                    <circle class="cine-intro__chassis-line cine-intro__chassis-line--late" pathLength="1" cx="252" cy="80" r="17"/>
                    <circle class="cine-intro__chassis-line cine-intro__chassis-line--late" pathLength="1" cx="310" cy="80" r="17"/>
                </svg>
                <!-- Kaynak noktası: ray boyunca soldan sağa gezer (track %100 genişlik) -->
                <span class="cine-intro__spark-track"><span class="cine-intro__spark"></span></span>
                <!-- Kıvılcım patlaması: logo tutuşurken savrulan parçacıklar -->
                <span class="cine-intro__burst" style="--bx:-72px; --by:-46px"></span>
                <span class="cine-intro__burst" style="--bx:64px;  --by:-58px"></span>
                <span class="cine-intro__burst" style="--bx:-38px; --by:34px"></span>
                <span class="cine-intro__burst" style="--bx:86px;  --by:22px"></span>
                <span class="cine-intro__burst" style="--bx:-92px; --by:-8px"></span>
                <span class="cine-intro__burst" style="--bx:30px;  --by:-84px"></span>
            </div>
        </div>
    </div>

    <!-- Atla: ilk kareden itibaren görünür; Escape de aynı işi yapar (JS) -->
    <button type="button" class="cine-intro__skip" data-cine-intro-skip><?= esc(lang('Common.cine_intro_skip')) ?></button>
</div>
