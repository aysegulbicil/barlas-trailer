/**
 * cinema-intro.js — sinematik açılış perdesi sürücüsü (v3.0 §1)
 * =============================================================================
 * Sayfa önbelleği (cachePage) yüzünden partial HER ZAMAN gizli render edilir;
 * "oynasın mı?" kararı BURADA, %100 istemcide verilir:
 *   - window.BarlasCinema var  VE
 *   - tier 'off' değil          VE
 *   - 'cine_intro' oturum çerezi yok (= bu oturumda ilk ziyaret)
 * Aksi halde öğe DOM'dan sökülür ve sözleşme gereği 'cinema:intro:done'
 * olayı yine ateşlenir — diğer modüller intronun oynayıp oynamadığına
 * bakmaksızın TEK ve KESİN bir bitiş sinyaline güvenir.
 *
 * Oynatma: çerez ANINDA yazılır (yenileme tekrarı önlenir), perde açılır,
 * kaydırma kilitlenir (html.cine-intro-lock), .is-playing tüm CSS
 * koreografisini başlatır. Atla düğmesi + Escape + sekme gizlenmesi erken
 * bitirir. Doğal bitiş ~3.9 sn'lik zamanlayıcıdadır (görsel akış 3.8 sn'de
 * zaten sönmüştür). Her yolda: .cine-intro--leaving → 320 ms → öğe sökülür,
 * kilit açılır, 'cinema:intro:done' TAM BİR KEZ ateşlenir.
 *
 * Bağımlılık: cinema-core.js (önce yüklenir). Klasik IIFE + defer
 * (hero-static.js kalıbı). Kare kare JS animasyonu YOK — her şey CSS'te.
 */
(function () {
    'use strict';

    var COOKIE_NAME = 'cine_intro';   // oturum çerezi: '1' = intro bu oturumda görüldü
    var NATURAL_MS  = 3900;           // doğal bitiş emniyet zamanlayıcısı (CSS akışı 3.8 sn)
    var LEAVE_MS    = 320;            // .cine-intro--leaving solma payı (CSS: .3 sn)

    /* Bitiş olayı: kaç yoldan gelinirse gelinsin TAM BİR KEZ ateşlenir. */
    var doneFired = false;
    function fireDone() {
        if (doneFired) return;
        doneFired = true;
        var ev;
        try {
            ev = new CustomEvent('cinema:intro:done');
        } catch (e) {
            /* çok eski WebKit yolu */
            ev = document.createEvent('CustomEvent');
            ev.initCustomEvent('cinema:intro:done', false, false, null);
        }
        document.dispatchEvent(ev);
    }

    function boot() {
        var root = document.querySelector('[data-cine-intro]');

        /* Partial bu sayfada yoksa (ör. iç sayfa) sözleşme yine tutar. */
        if (!root) {
            fireDone();
            return;
        }

        var cine = window.BarlasCinema;
        var shouldPlay = !!(cine && cine.tier !== 'off' && !cine.getCookie(COOKIE_NAME));

        /* Oynamayacaksa: öğeyi sök, sinyali ver, çık. Perde hiç görünmez. */
        if (!shouldPlay) {
            if (root.parentNode) root.parentNode.removeChild(root);
            fireDone();
            return;
        }

        /* --- Oynatma kararı verildi --- */

        /* Çerez HEMEN yazılır: intro sırasında sayfa yenilenirse tekrar
           oynamaz. Gün parametresi YOK → oturum çerezi (tarayıcı kapanınca
           silinir, sonraki oturumda intro yeniden hak eder). */
        cine.setCookie(COOKIE_NAME, '1');

        var html    = document.documentElement;
        var skipBtn = root.querySelector('[data-cine-intro-skip]');
        var ended   = false;
        var timerId = 0;

        function onKeydown(ev) {
            if (ev.key === 'Escape' || ev.key === 'Esc') end();
        }

        /* Bitiş (atla / Escape / sekme gizlendi / doğal zamanlayıcı):
           --leaving solmasını başlat, sonra söküp kilidi aç ve sinyali ver.
           ended bayrağı çifte çalışmayı (ör. atla + zamanlayıcı) keser. */
        function end() {
            if (ended) return;
            ended = true;

            window.clearTimeout(timerId);
            document.removeEventListener('keydown', onKeydown);

            root.classList.add('cine-intro--leaving');
            window.setTimeout(function () {
                if (root.parentNode) root.parentNode.removeChild(root);
                html.classList.remove('cine-intro-lock');
                fireDone();
            }, LEAVE_MS);
        }

        /* Perdeyi aç: hidden/aria-hidden kalkar, kaydırma kilitlenir,
           reflow sonrası .is-playing tüm koreografiyi tetikler. */
        root.hidden = false;
        root.removeAttribute('aria-hidden');
        html.classList.add('cine-intro-lock');
        void root.offsetWidth; /* stil geçişinin kaydolması için zorunlu reflow */
        root.classList.add('is-playing');

        /* Erişilebilirlik: odak ilk kareden atla düğmesinde — Enter/Space
           anında çıkış demek. */
        if (skipBtn) {
            try { skipBtn.focus({ preventScroll: true }); } catch (e) { skipBtn.focus(); }
            skipBtn.addEventListener('click', end);
        }

        document.addEventListener('keydown', onKeydown);

        /* Sekme gizlenirse erken bitir: CSS animasyon saatleri ile JS
           zamanlayıcısının ayrışmasına (drift) izin verme. Not: onVisibility
           kayıtta bir kez hemen çağrılır — görünürse dokunma. */
        cine.onVisibility(function (visible) {
            if (!visible && !ended) end();
        });

        timerId = window.setTimeout(end, NATURAL_MS);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot, { once: true });
    } else {
        boot();
    }
})();
