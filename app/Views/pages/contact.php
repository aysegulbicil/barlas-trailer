<?php

/**
 * İletişim sayfası — yeni arayüz (layouts/yeni.php).
 *
 * Sol: çizgi film stili SVG usta maskotu — halata asıla asıla sağdaki formu çeker
 * (form gizli başlar, her asılışta bir adım yaklaşır, oturunca usta halatı
 * bırakıp doğrulur). Form gönderilince usta kolunu kaldırıp selam verir,
 * sahnede onay belirir. Halat, ustanın eli ile formun kulpu arasında JS'te
 * her karede çizilir (contact-foreman.js) — mobil ve RTL'de de çalışır.
 * Sağ: cam panelli iletişim formu (gerçek POST → Contact::submit).
 * Altta: telefon / e-posta / adres kartları ve harita.
 *
 * Reduced-motion / JS yok → usta statik pozda durur, halat çizilmez, form
 * sabit görünür (contact-deliver eklenmez). 3D/WebGL bağımlılığı KALDIRILDI.
 * Tüm metinler dil dosyalarından (Contact.* / Common.*), bağlantılar locale_url().
 */
$this->extend('layouts/yeni');

/* Konu / ürün seçenekleri (değer = okunur etiket → e-postada anlamlı görünür) */
$subjects = ['subj_quote', 'subj_tanker', 'subj_silo', 'subj_tipper', 'subj_service', 'subj_parts', 'subj_other'];

/* Sunucu tarafı durum (no-JS / yeniden yükleme) */
$flashSuccess   = session('form_success');
$flashErrorsMsg = session('form_error');
$flashErrors    = session('errors') ?? [];
$fieldErr = static fn (string $k): string => isset($flashErrors[$k]) ? esc($flashErrors[$k]) : '';

/* İletişim verileri */
$phone     = lang('Common.phone');
$phoneRaw  = lang('Common.phone_raw');
$email     = lang('Common.email');
$address   = lang('Common.address');
$mapQuery  = trim(str_replace(["\r\n", "\n", "\r"], ', ', $address));
$mapEmbed  = 'https://www.google.com/maps?q=' . rawurlencode($mapQuery) . '&output=embed';
$mapLink   = 'https://maps.app.goo.gl/kC6t9j8MRdYTsojn7';

/* Sayfaya özel asset sürümleri (layout'taki $asset kapanışı burada yok) */
$cssVer = is_file(FCPATH . 'assets/css/contact.css') ? filemtime(FCPATH . 'assets/css/contact.css') : '1';
$jsVer  = is_file(FCPATH . 'assets/js/contact-foreman.js') ? filemtime(FCPATH . 'assets/js/contact-foreman.js') : '1';
?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/contact.css') ?>?v=<?= $cssVer ?>">
<!-- Girişte titreme olmasın: hareket açıksa anim + deliver sınıflarını
     boyamadan önce ekle. Usta sahnesi SVG+GSAP olduğundan WebGL/genişlik
     şartı yok — mobil dahil; yalnız reduced-motion'da kapalı (form sabit). -->
<script>
    (function () {
        try {
            var rm = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (!rm) {
                document.documentElement.classList.add('contact-anim');
                document.documentElement.classList.add('contact-deliver');
            }
        } catch (e) {}
    })();
</script>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main id="main" class="contact">

    <!-- ============================ HERO ============================ -->
    <section class="contact-hero" data-contact-hero>
        <div class="contact-hero__bg" aria-hidden="true">
            <span class="contact-hero__grid"></span>
            <span class="contact-hero__glow"></span>
        </div>

        <div class="shell contact-hero__inner">
          <div class="contact-convoy" data-convoy>

            <!-- Sol: usta maskotu (halatla formu çeker). Eklemli gruplar
                 (data-fm-*) contact-foreman.js'teki GSAP rig'i tarafından döndürülür;
                 pivotlar JS'teki svgOrigin değerleriyle eşleşir — koordinat
                 değiştirirsen ikisini birlikte güncelle. Reduced-motion'da statik poz. -->
            <aside class="contact-hero__stage" data-foreman-stage aria-hidden="true">
                <!-- Barlas usta maskotu — dolgulu/cel-shading çizgi film stili.
                     viewBox karaktere kırpık (çizim koordinatları 360x440 uzayında
                     kalır); pivotlar JS'teki svgOrigin'lerle eşleşir: kalça (156,258),
                     boyun (151,152), omuz (140,184), dirsek (177,207). Kol/önkol hâlâ
                     tek polyline iskelet üzerine çok katmanlı stroke'tur (kontur +
                     kumaş/ten dolgusu) → rig aynı kalır; gövde/baş/bacaklar dolgulu
                     path'lerdir. stroke-width'ler elemanda (attribute) tutulur,
                     renkler contact.css'teki --fm-* token'larından gelir. -->
                <svg class="contact-foreman" data-foreman viewBox="40 78 300 348" focusable="false" aria-hidden="true">
                    <!-- zemin + gölge + blueprint süsleri -->
                    <ellipse class="fm-shadow" cx="160" cy="407" rx="106" ry="8.5"/>
                    <path class="fm-ground" d="M44 404 H336"/>
                    <path class="fm-deco" d="M312 92 v16 M304 100 h16"/>
                    <path class="fm-deco" d="M52 130 v12 M46 136 h12"/>
                    <path class="fm-deco fm-deco--arc" d="M258 374 A100 100 0 0 0 304 296"/>

                    <g data-fm-root>
                        <!-- BACAKLAR (statik; gövde kalçadan döner): tulum paçaları
                             kemik hattı (150,254→118,320→102,384) / (162,256→200,318→214,376)
                             etrafına dolgulu çizilir; üstleri torsonun altında kalır -->
                        <path class="fm-pant" d="M139 246 L164 248 Q149 288 133.5 317 Q124 349 116.5 379 L93.5 374.5 Q99.5 342 104.5 315 Q112 281 139 246 Z"/>
                        <path class="fm-shade" d="M139 246 Q112 281 104.5 315 Q99.5 342 93.5 374.5 L101.5 376 Q106.5 344 111.5 317 Q119 285 143.5 249 Z"/>
                        <path class="fm-crease" d="M124.5 314 Q130.5 317 136 314.5"/>
                        <path class="fm-cuffband" d="M92.5 371.5 L117.5 376.5 L116 385.5 L90.5 380.5 Z"/>
                        <path class="fm-boot" d="M118 379 L121.5 391.5 Q122.5 399 113.5 399 L84 399 Q76.5 398.5 78.5 391 Q81.5 382.5 93 377.5 Q106.5 372.5 118 379 Z"/>
                        <path class="fm-sole" d="M77.5 394 L122.2 394 L122.2 400.5 Q122.2 401.5 113.5 401.5 L84 401.5 Q77 401 77.5 394 Z"/>
                        <path class="fm-boot-line" d="M90.5 379.5 Q83 382.5 80.5 389.5 M108.5 381.5 L115 379.5 M110 386.5 L116.8 384.8"/>

                        <path class="fm-pant" d="M150.5 246.5 L176.5 249 Q196 285 209.5 313 Q219.5 344 225.5 371 L202.5 377 Q195.5 345 188 320 Q172 287 150.5 246.5 Z"/>
                        <path class="fm-seam-hi" d="M170.5 254 Q190 288 199 318 Q206.5 345 212 368"/>
                        <path class="fm-crease" d="M196 310 Q203 313 208.5 311 M199.5 318 Q205 320.5 210.5 318.5"/>
                        <path class="fm-cuffband" d="M201.5 374 L226.5 368.5 L228.5 377.5 L203.5 383 Z"/>
                        <path class="fm-boot" d="M200.5 375 L197.5 391 Q196.5 398.5 205.5 398.5 L236.5 398.5 Q244.5 398.5 242.5 390.5 Q240 381.5 229.5 377 Q213 370.5 200.5 375 Z"/>
                        <path class="fm-sole" d="M197 393.5 L243.3 393.5 Q243.6 401 236.5 401 L205.5 401 Q196.8 401 197 393.5 Z"/>
                        <path class="fm-boot-line" d="M231 379.5 Q239.5 383 241.5 390 M210 379.5 L203.5 378 M209 384.5 L202.5 383.5"/>

                        <!-- GÖVDE: önlüklü tulum + gömlek; kalça pivotu (156,258) -->
                        <g data-fm-hips>
                            <!-- kemerden sarkan anahtar (torsonun arkasında; gövdeyle salınır) -->
                            <path class="fm-lnk" stroke-width="9" d="M121.5 252 L117.5 283"/>
                            <path class="fm-tube-metal" stroke-width="5" d="M121.5 252 L117.5 283"/>
                            <path class="fm-lnk" stroke-width="7.5" d="M112 291 A 6.5 6.5 0 1 1 122.5 288"/>
                            <path class="fm-tube-metal" stroke-width="3.6" d="M112 291 A 6.5 6.5 0 1 1 122.5 288"/>

                            <!-- ARKA kol gövdeden ÖNCE (torsonun arkasında kalır);
                                 JS ön/arka kolu birlikte döndürür. Omuz (137,180), dirsek (174,202) -->
                            <g data-fm-arm-b>
                                <path class="fm-lnk fm-lnk--soft" stroke-width="14" d="M137 179 L173 201"/>
                                <path class="fm-tube-suit2" stroke-width="9.5" d="M137 179 L173 201"/>
                                <g data-fm-fore-b>
                                    <path class="fm-lnk fm-lnk--soft" stroke-width="13" d="M173 201 L214 219"/>
                                    <path class="fm-tube-skin2" stroke-width="8.5" d="M173 201 L214 219"/>
                                    <circle class="fm-glove-2 fm-glove--back" cx="218.5" cy="221.5" r="8.2"/>
                                    <circle class="fm-glove-2 fm-glove--back" cx="213.5" cy="226.5" r="4"/>
                                </g>
                            </g>

                            <!-- gömlek tabanı (tors silüeti) + önlük + askılar + kemer -->
                            <path class="fm-torso" d="M136 264 L122 198 Q116.5 172 132 165.5 L143.5 162.5 L145.5 148.5 Q153 144.5 161 146.5 L159.5 160.5 Q170 160 174 169 Q182.5 196 180.5 228 L179 254 Q179.5 263.5 170.5 264.5 L145 265.5 Q136.5 265.5 136 264 Z"/>
                            <path class="fm-bib" d="M124 200 Q152 190.5 180 196 L179 253.5 Q179.3 262 170.3 263 L145.4 264 Q138 264 137.5 262.5 Z"/>
                            <path class="fm-bib-seam" d="M124 200 Q152 190.5 180 196"/>
                            <path class="fm-lnk" stroke-width="10" d="M128.5 199 L132.5 169"/>
                            <path class="fm-tube-suit" stroke-width="6.5" d="M128.5 199 L132.5 169"/>
                            <path class="fm-lnk" stroke-width="10" d="M172.5 197 L159 163.5"/>
                            <path class="fm-tube-suit" stroke-width="6.5" d="M172.5 197 L159 163.5"/>
                            <circle class="fm-metal" cx="129.8" cy="197" r="3"/>
                            <circle class="fm-metal" cx="171.8" cy="194.5" r="3"/>
                            <!-- göğüs cebi + "B" yaka arması -->
                            <rect class="fm-patch" x="148" y="206" width="21" height="15" rx="3" transform="rotate(2 158.5 213.5)"/>
                            <path class="fm-patch-b" d="M155.5 209.5 L155.5 218 M155.5 209.5 Q160.6 209.5 160.6 211.6 Q160.6 213.5 155.5 213.5 M155.5 213.5 Q161.5 213.5 161.5 215.9 Q161.5 218 155.5 218"/>
                            <!-- alet kemeri + toka -->
                            <path class="fm-belt" d="M136.8 245 L179.2 243 L179.4 252.5 L137.9 254.5 Z"/>
                            <rect class="fm-metal" x="153" y="244.4" width="10" height="8.4" rx="1.5"/>
                            <!-- gövde gölgesi (sırt tarafı) -->
                            <path class="fm-shade" d="M136 264 L122 198 Q116.5 172 132 165.5 L137.5 164.2 Q126.5 176 129.5 200 L141.5 263 Q137 264.5 136 264 Z"/>

                            <!-- BAŞ: profil yüz + pala bıyık + turuncu baret; boyun pivotu (151,152).
                                 Katman sırası: bıyık ağzın üstünde, burun en önde (bıyığın
                                 kökünü örter), baret hepsinin üzerinde. -->
                            <g data-fm-head>
                                <path class="fm-skin" d="M146 141 L158 141 L158.5 157 L146.5 157 Z"/>
                                <path class="fm-hair" d="M130.5 112.5 Q127.5 118 129.5 123.5 Q132 120 133.5 114 Z"/>
                                <path class="fm-skin" d="M134 110 Q128 118 129 129 Q129 138 134 144 Q139 151 150 152.5 Q158 153 162 149 Q167 144 169 138 L171 131 Q175 122 172 113 Q166 106 155 105 Q141 105 134 110 Z"/>
                                <ellipse class="fm-skin" cx="139.5" cy="130.5" rx="4.2" ry="5.4"/>
                                <path class="fm-face-line" d="M138.3 128.4 Q140.8 130 139 133"/>
                                <path class="fm-hair" d="M143.5 115.5 L148 116.3 L146.2 127 Q143.6 126 143 122 Z"/>
                                <path class="fm-hair" d="M152.5 109.5 L168.5 112 Q171.5 112.8 170.8 116.8 L152 113.8 Q149.5 112.8 152.5 109.5 Z"/>
                                <ellipse class="fm-eye-w" cx="162" cy="120.5" rx="5" ry="5.6"/>
                                <circle class="fm-eye-p" cx="163.8" cy="121" r="2.3"/>
                                <circle class="fm-eye-g" cx="162.5" cy="118.8" r="1"/>
                                <path class="fm-mouth" d="M158.5 141.5 Q164.5 150.5 172 140.5 Q169.5 149.5 162.5 148.5 Q159 146.5 158.5 141.5 Z"/>
                                <path class="fm-teeth" d="M160.3 142.1 Q165 147 170.2 140.9 L169.5 139.4 Q164.8 144.4 161.5 140.6 Z"/>
                                <path class="fm-hair" d="M172.5 132.5 Q173.5 130 170.5 129.5 Q162.5 127.6 157 129.9 Q154 131.5 155.2 133.9 Q156.8 136.3 162 136 Q168.8 135.6 172.5 132.5 Z"/>
                                <path class="fm-skin" d="M169 120.5 Q177.5 121 177.8 127 Q177.8 131.5 171.8 131 Q168 130.6 168 126.5 Z"/>
                                <path class="fm-helmet" d="M129 114.5 Q125.5 97 142 90.5 Q158.5 84.5 169.5 92 Q177.5 97.5 178 104.5 Z"/>
                                <path class="fm-helmet-line" d="M143.5 91.5 Q155 86.5 166.5 92.5 M149.5 97.5 L148.5 104 M157 95.5 L156 102"/>
                                <path class="fm-hilite" stroke-width="3" d="M135 104.5 Q134.5 96 142.5 91.5"/>
                                <path class="fm-helmet-2" d="M128.5 113.5 L178 103.5 L178.6 107.2 L129.2 117.5 Z"/>
                                <path class="fm-helmet fm-helmet--brim" d="M173.5 103.5 L194 106.5 Q198.5 107.8 197.8 111 Q197.2 113.6 192.8 113.2 L172.8 108.6 Z"/>
                            </g>

                            <!-- ÖN KOL: omuz pivotu (140,184), dirsek pivotu (177,207).
                                 Sıvalı kol: üst kolda tulum kumaşı, dirsekte kıvrık manşet,
                                 önkolda ten; eldiven bilek+yumruk+başparmak katmanlıdır. -->
                            <g data-fm-arms>
                                <g data-fm-fores>
                                    <path class="fm-lnk" stroke-width="15" d="M178 210.5 L220 227.5"/>
                                    <path class="fm-tube-skin" stroke-width="10" d="M178 210.5 L220 227.5"/>
                                    <circle class="fm-tube-skin-bulge" cx="192" cy="215.5" r="6.3"/>
                                    <path class="fm-glove-2" d="M203.5 217 L209.5 232.5 L218 228.5 L210.5 214.5 Z"/>
                                    <circle class="fm-glove" cx="221.5" cy="228.5" r="9"/>
                                    <path class="fm-face-line" d="M224.5 220.8 Q229 226 226.5 232.8"/>
                                    <circle class="fm-glove" cx="215.8" cy="233" r="4.3"/>
                                    <!-- halatın el ucu (JS rect ile okur; görünmez) -->
                                    <circle data-hand-anchor cx="222" cy="228" r="1" fill="none" stroke="none"/>
                                </g>
                                <path class="fm-lnk" stroke-width="17" d="M141 187.5 L178 210"/>
                                <path class="fm-tube-suit" stroke-width="11.5" d="M141 187.5 L178 210"/>
                                <!-- kıvrılmış kol manşeti: dirsek eklemini örter (kola dik bant) -->
                                <rect class="fm-rollcuff" x="169.5" y="203.6" width="16" height="7.8" rx="3.8" transform="rotate(121.3 177.5 207.5)"/>
                                <!-- omuz: kolla aynı kumaş dolgusu (kontursuz) + üstte dikiş yayı -->
                                <circle class="fm-cap-fill" cx="141.5" cy="187" r="8.7"/>
                                <path class="fm-cap-seam" d="M135.2 180.9 A 8.7 8.7 0 0 1 147.8 180.9"/>
                            </g>
                        </g>
                    </g>
                </svg>

                <!-- Gönderim sonrası: usta selam verince beliren onay -->
                <div class="contact-stage__done" data-stage-done aria-hidden="true">
                    <span class="contact-stage__done-ic">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
                    </span>
                    <span class="contact-stage__done-text"><?= esc(lang('Contact.success_title')) ?></span>
                </div>
            </aside>

            <!-- Halat: ustanın eli ile formun kulpu arasında JS'te çizilir
                 (konvoyu kaplayan katman; viewBox her karede px'e eşitlenir) -->
            <svg class="contact-rope" data-rope aria-hidden="true" focusable="false" preserveAspectRatio="none" viewBox="0 0 100 100">
                <path d="M0 0" fill="none"/>
            </svg>

            <!-- Sağ: başlık + form (çekilen yük) -->
            <div class="contact-hero__main">
                <header class="contact-hero__intro">
                    <p class="contact-eyebrow">
                        <span class="contact-eyebrow__spark" aria-hidden="true"></span>
                        <?= esc(lang('Contact.eyebrow')) ?>
                    </p>
                    <h1 class="contact-hero__title"><?= esc(lang('Contact.title')) ?></h1>
                    <p class="contact-hero__lead"><?= esc(lang('Contact.lead')) ?></p>
                </header>

                <!-- ============================ FORM ============================ -->
                <form class="contact-form<?= $flashSuccess ? ' is-sent' : '' ?>"
                      method="post"
                      action="<?= esc(locale_url('contact'), 'attr') ?>"
                      data-contact-form
                      data-msg-name="<?= esc(lang('Contact.err_name'), 'attr') ?>"
                      data-msg-email="<?= esc(lang('Contact.err_email'), 'attr') ?>"
                      data-msg-message="<?= esc(lang('Contact.err_message'), 'attr') ?>"
                      data-msg-generic="<?= esc(lang('Contact.form_error'), 'attr') ?>"
                      novalidate>
                    <?= csrf_field() ?>

                    <!-- Çekme kulpu: halatın form ucu (JS rect ile okur) -->
                    <span class="contact-form__lug" data-form-lug aria-hidden="true"></span>

                    <div class="contact-form__inner">
                        <div class="contact-form__head">
                            <h2 class="contact-form__title"><?= esc(lang('Contact.form_title')) ?></h2>
                            <p class="contact-form__subtitle"><?= esc(lang('Contact.form_subtitle')) ?></p>
                        </div>

                        <?php if ($flashErrorsMsg): ?>
                            <div class="contact-form__banner" role="alert"><?= esc($flashErrorsMsg) ?></div>
                        <?php endif; ?>

                        <div class="contact-form__grid">
                            <div class="contact-field" data-field="name">
                                <label class="contact-field__label" for="cf-name">
                                    <?= esc(lang('Contact.f_name')) ?> <span class="contact-field__req" aria-hidden="true">*</span>
                                </label>
                                <input class="contact-field__input" id="cf-name" name="name" type="text"
                                       value="<?= esc(old('name'), 'attr') ?>"
                                       placeholder="<?= esc(lang('Contact.ph_name'), 'attr') ?>"
                                       autocomplete="name" required>
                                <p class="contact-field__error" data-error-for="name"><?= $fieldErr('name') ?></p>
                            </div>

                            <div class="contact-field" data-field="email">
                                <label class="contact-field__label" for="cf-email">
                                    <?= esc(lang('Contact.f_email')) ?> <span class="contact-field__req" aria-hidden="true">*</span>
                                </label>
                                <input class="contact-field__input" id="cf-email" name="email" type="email"
                                       value="<?= esc(old('email'), 'attr') ?>"
                                       placeholder="<?= esc(lang('Contact.ph_email'), 'attr') ?>"
                                       autocomplete="email" required>
                                <p class="contact-field__error" data-error-for="email"><?= $fieldErr('email') ?></p>
                            </div>

                            <div class="contact-field" data-field="phone">
                                <label class="contact-field__label" for="cf-phone"><?= esc(lang('Contact.f_phone')) ?></label>
                                <input class="contact-field__input" id="cf-phone" name="phone" type="tel"
                                       value="<?= esc(old('phone'), 'attr') ?>"
                                       placeholder="<?= esc(lang('Contact.ph_phone'), 'attr') ?>"
                                       autocomplete="tel">
                                <p class="contact-field__error" data-error-for="phone"></p>
                            </div>

                            <div class="contact-field" data-field="company">
                                <label class="contact-field__label" for="cf-company"><?= esc(lang('Contact.f_company')) ?></label>
                                <input class="contact-field__input" id="cf-company" name="company" type="text"
                                       value="<?= esc(old('company'), 'attr') ?>"
                                       placeholder="<?= esc(lang('Contact.ph_company'), 'attr') ?>"
                                       autocomplete="organization">
                                <p class="contact-field__error" data-error-for="company"></p>
                            </div>

                            <div class="contact-field contact-field--full" data-field="subject">
                                <label class="contact-field__label" for="cf-subject"><?= esc(lang('Contact.f_subject')) ?></label>
                                <div class="contact-field__select">
                                    <select class="contact-field__input" id="cf-subject" name="subject">
                                        <option value=""><?= esc(lang('Contact.subject_placeholder')) ?></option>
                                        <?php foreach ($subjects as $sk): $lbl = lang('Contact.' . $sk); ?>
                                            <option value="<?= esc($lbl, 'attr') ?>" <?= old('subject') === $lbl ? 'selected' : '' ?>><?= esc($lbl) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <svg class="contact-field__caret" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M6 9l6 6 6-6"></path>
                                    </svg>
                                </div>
                                <p class="contact-field__error" data-error-for="subject"></p>
                            </div>

                            <div class="contact-field contact-field--full" data-field="message">
                                <label class="contact-field__label" for="cf-message">
                                    <?= esc(lang('Contact.f_message')) ?> <span class="contact-field__req" aria-hidden="true">*</span>
                                </label>
                                <textarea class="contact-field__input contact-field__textarea" id="cf-message" name="message"
                                          rows="4" placeholder="<?= esc(lang('Contact.ph_message'), 'attr') ?>" required><?= esc(old('message')) ?></textarea>
                                <p class="contact-field__error" data-error-for="message"><?= $fieldErr('message') ?></p>
                            </div>
                        </div>

                        <!-- Bal kabı (görünmez; insanlar doldurmaz) -->
                        <div class="contact-hp" aria-hidden="true">
                            <label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                        </div>

                        <div class="contact-form__foot">
                            <p class="contact-form__hint"><?= esc(lang('Contact.required_hint')) ?></p>
                            <button class="btn btn--primary btn--lg contact-form__submit" type="submit" data-submit>
                                <span class="contact-form__submit-label"><?= esc(lang('Contact.submit')) ?></span>
                                <span class="contact-form__spinner" aria-hidden="true"></span>
                            </button>
                        </div>

                        <p class="contact-form__status" role="status" aria-live="polite"></p>
                    </div>

                    <!-- Başarı durumu (.is-sent ile görünür) -->
                    <div class="contact-form__success" data-success>
                        <span class="contact-form__success-ic" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 6 9 17l-5-5"></path>
                            </svg>
                        </span>
                        <h3 class="contact-form__success-title"><?= esc(lang('Contact.success_title')) ?></h3>
                        <p class="contact-form__success-text"><?= esc(lang('Contact.success_text')) ?></p>
                        <button class="btn btn--ghost" type="button" data-reset><?= esc(lang('Contact.success_again')) ?></button>
                    </div>
                </form>
            </div>
          </div>
        </div>
    </section>

    <!-- ===================== İLETİŞİM BİLGİLERİ ===================== -->
    <section class="contact-info">
        <div class="shell">
            <header class="contact-info__head" data-reveal>
                <h2 class="contact-info__title"><?= esc(lang('Contact.info_title')) ?></h2>
                <p class="contact-info__lead"><?= esc(lang('Contact.info_lead')) ?></p>
            </header>

            <div class="contact-info__grid" data-reveal-group>
                <a class="contact-card" href="tel:<?= esc($phoneRaw, 'attr') ?>">
                    <span class="contact-card__ic" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.3 1.8.6 2.7a2 2 0 0 1-.5 2.1L8.1 9.6a16 16 0 0 0 6 6l1.1-1.1a2 2 0 0 1 2.1-.5c.9.3 1.8.5 2.7.6a2 2 0 0 1 1.7 2z"></path>
                        </svg>
                    </span>
                    <span class="contact-card__label"><?= esc(lang('Contact.phone_label')) ?></span>
                    <span class="contact-card__value"><?= esc($phone) ?></span>
                </a>

                <a class="contact-card" href="mailto:<?= esc($email, 'attr') ?>">
                    <span class="contact-card__ic" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                            <path d="m3 7 9 6 9-6"></path>
                        </svg>
                    </span>
                    <span class="contact-card__label"><?= esc(lang('Contact.email_label')) ?></span>
                    <span class="contact-card__value"><?= esc($email) ?></span>
                </a>

                <div class="contact-card contact-card--address">
                    <span class="contact-card__ic" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 21s-7-5.6-7-11a7 7 0 0 1 14 0c0 5.4-7 11-7 11z"></path>
                            <circle cx="12" cy="10" r="2.6"></circle>
                        </svg>
                    </span>
                    <span class="contact-card__label"><?= esc(lang('Contact.address_label')) ?></span>
                    <span class="contact-card__value"><?= nl2br(esc($address)) ?></span>
                    <a class="contact-card__link" href="<?= esc($mapLink, 'attr') ?>" target="_blank" rel="noopener">
                        <?= esc(lang('Contact.cta_directions')) ?>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17 17 7M9 7h8v8"></path></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================ HARİTA ============================ -->
    <section class="contact-map" data-reveal>
        <div class="shell contact-map__head">
            <h2 class="contact-map__title"><?= esc(lang('Contact.map_title')) ?></h2>
            <p class="contact-map__note"><?= esc(lang('Contact.map_note')) ?></p>
        </div>
        <div class="contact-map__frame">
            <iframe
                src="<?= esc($mapEmbed, 'attr') ?>"
                title="<?= esc(lang('Contact.map_title'), 'attr') ?>"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                allowfullscreen></iframe>
        </div>
    </section>
</main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/contact-foreman.js') ?>?v=<?= $jsVer ?>" defer></script>
<?= $this->endSection() ?>
