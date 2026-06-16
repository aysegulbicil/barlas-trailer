<?php

/**
 * İletişim sayfası — yeni arayüz (layouts/yeni.php).
 *
 * Sol: prosedürel 3D TIR sahnesi (büyük) — çekici + tanker sağdan girer,
 * tekerlek döner, süspansiyonla yaylanarak park eder ve sağdaki formu
 * "getirir/teslim eder" (form baştan sabit değildir, TIR varınca belirir).
 * Form gönderilince TIR yükü alıp yola çıkar, sahnede "yola çıktı" onayı belirir.
 * Sağ: cam panelli iletişim formu (gerçek POST → Contact::submit).
 * Altta: telefon / e-posta / adres kartları ve harita.
 *
 * WebGL/JS yoksa veya reduced-motion'da SVG yedeği görünür, form sabit kalır.
 * Tüm metinler dil dosyalarından (Contact.* / Common.*), bağlantılar locale_url().
 */
$this->extend('layouts/yeni');

/* Konu / ürün seçenekleri (değer = okunur etiket → e-postada anlamlı görünür) */
$subjects = ['subj_quote', 'subj_tanker', 'subj_silo', 'subj_tipper', 'subj_service', 'subj_parts', 'subj_other'];

/* KVKK onayı: {link} yer tutucusunu güvenli bir bağlantıyla değiştir */
$consentHtml = esc(lang('Contact.consent'));
$consentHtml = str_replace(
    '{link}',
    '<a href="#" class="contact-form__link">' . esc(lang('Contact.consent_link')) . '</a>',
    $consentHtml
);

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
$jsVer  = is_file(FCPATH . 'assets/js/contact-tanker.js') ? filemtime(FCPATH . 'assets/js/contact-tanker.js') : '1';
?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/contact.css') ?>?v=<?= $cssVer ?>">
<!-- Girişte titreme olmasın: hareket açıksa anim sınıfını, 3D teslimat
     koşulları varsa (geniş ekran + WebGL) deliver sınıfını boyamadan önce ekle. -->
<script>
    (function () {
        try {
            var rm = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (!rm) document.documentElement.classList.add('contact-anim');
            function wgl() {
                try {
                    var c = document.createElement('canvas');
                    return !!(window.WebGLRenderingContext &&
                        (c.getContext('webgl') || c.getContext('experimental-webgl')));
                } catch (e) { return false; }
            }
            if (!rm && window.innerWidth >= 992 && wgl()) {
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

            <!-- Sol: büyük 3D TIR sahnesi (formu çekip getirir) -->
            <aside class="contact-hero__stage" data-contact-stage aria-hidden="true">
                <div class="contact-stage__mount" data-stage-mount></div>

                <!-- SVG yedeği: WebGL/JS yoksa ya da reduced-motion'da kalır -->
                <div class="contact-stage__fallback" data-stage-fallback>
                    <svg viewBox="0 0 560 280" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Barlas tanker">
                        <defs>
                            <linearGradient id="ctTank" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0" stop-color="#e8edf4"></stop>
                                <stop offset="0.5" stop-color="#cfd8e4"></stop>
                                <stop offset="1" stop-color="#9aa6b5"></stop>
                            </linearGradient>
                            <linearGradient id="ctCab" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0" stop-color="#2f8fe0"></stop>
                                <stop offset="1" stop-color="#005baa"></stop>
                            </linearGradient>
                        </defs>
                        <ellipse cx="280" cy="246" rx="240" ry="16" fill="rgba(3,6,12,0.45)"></ellipse>
                        <rect x="196" y="92" width="320" height="116" rx="58" fill="url(#ctTank)"></rect>
                        <ellipse cx="196" cy="150" rx="26" ry="58" fill="#b9c3d1"></ellipse>
                        <ellipse cx="516" cy="150" rx="20" ry="58" fill="#aeb9c8"></ellipse>
                        <rect x="300" y="120" width="150" height="26" rx="13" fill="#005baa" opacity="0.92"></rect>
                        <text x="375" y="139" text-anchor="middle" font-family="Sora, Inter, sans-serif" font-weight="800" font-size="20" fill="#ffffff" letter-spacing="2">BARLAS</text>
                        <circle cx="250" cy="84" r="6" fill="#9aa6b5"></circle>
                        <circle cx="330" cy="84" r="6" fill="#9aa6b5"></circle>
                        <circle cx="410" cy="84" r="6" fill="#9aa6b5"></circle>
                        <path d="M52 208 V120 q0-22 22-22 h70 q14 0 20 12 l20 40 V208 Z" fill="url(#ctCab)"></path>
                        <path d="M150 118 l16 34 h-44 V118 Z" fill="#0a1422" opacity="0.85"></path>
                        <rect x="44" y="150" width="14" height="40" rx="4" fill="#0a1422"></rect>
                        <rect x="120" y="200" width="404" height="12" rx="4" fill="#10182a"></rect>
                        <g fill="#14181f">
                            <circle cx="120" cy="214" r="30"></circle>
                            <circle cx="300" cy="214" r="30"></circle>
                            <circle cx="372" cy="214" r="30"></circle>
                            <circle cx="444" cy="214" r="30"></circle>
                        </g>
                        <g fill="#cfd6df">
                            <circle cx="120" cy="214" r="12"></circle>
                            <circle cx="300" cy="214" r="12"></circle>
                            <circle cx="372" cy="214" r="12"></circle>
                            <circle cx="444" cy="214" r="12"></circle>
                        </g>
                    </svg>
                </div>

                <!-- Gönderim sonrası: TIR yola çıkınca beliren onay -->
                <div class="contact-stage__done" data-stage-done aria-hidden="true">
                    <span class="contact-stage__done-ic">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
                    </span>
                    <span class="contact-stage__done-text"><?= esc(lang('Contact.success_title')) ?></span>
                </div>
            </aside>

            <!-- Çeki demiri: tankerin arkasını forma bağlar (konvoy hissi) -->
            <div class="contact-hitch" data-hitch aria-hidden="true">
                <span class="contact-hitch__ring contact-hitch__ring--truck"></span>
                <span class="contact-hitch__beam"></span>
                <span class="contact-hitch__ring contact-hitch__ring--form"></span>
            </div>

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
                      data-msg-consent="<?= esc(lang('Contact.err_consent'), 'attr') ?>"
                      data-msg-generic="<?= esc(lang('Contact.form_error'), 'attr') ?>"
                      novalidate>
                    <?= csrf_field() ?>

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

                        <label class="contact-consent" data-field="consent">
                            <input type="checkbox" name="consent" value="1" <?= old('consent') ? 'checked' : '' ?> required>
                            <span class="contact-consent__box" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l5 5 9-11"></path></svg>
                            </span>
                            <span class="contact-consent__text"><?= $consentHtml ?></span>
                        </label>
                        <p class="contact-field__error contact-field__error--consent" data-error-for="consent"><?= $fieldErr('consent') ?></p>

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
<script src="<?= base_url('assets/js/contact-tanker.js') ?>?v=<?= $jsVer ?>" defer></script>
<?= $this->endSection() ?>
