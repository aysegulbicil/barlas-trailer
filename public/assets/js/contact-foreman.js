/**
 * contact-foreman.js — İletişim sayfası etkileşimi
 *
 * 1) Usta sahnesi (sol): çizgi film stili SVG usta maskotu (eklemli: kalça/baş/omuz/dirsek
 *    grupları) halata asıla asıla sağdaki iletişim formunu sahneye çeker.
 *    Form gizli başlar; her asılışta bir adım yaklaşır, yerine oturunca usta
 *    halatı bırakıp doğrulur ve nefes alma (idle) döngüsüne geçer. Form
 *    gönderilince usta kolunu kaldırıp selam verir, sahnede onay belirir.
 * 2) Halat: ustanın eli ([data-hand-anchor]) ile formun kulpu ([data-form-lug])
 *    arasında her karede yeniden çizilir (getBoundingClientRect tabanlı) —
 *    masaüstü/mobil/RTL düzenlerinde aynı kod çalışır; gerginlik state.sag ile
 *    tween'lenir (gergin = düz, gevşek = sarkık quadratic).
 * 3) Form (sağ): gerçek POST (Contact::submit) — fetch ile gönderilir.
 *
 * Aşamalı geliştirme: reduced-motion'da html'e contact-deliver eklenmez →
 * usta statik pozda durur, halat çizilmez, form sabit görünür. GSAP yoksa
 * güvenlik zamanlayıcısı + CSS (ctFormSafety) formu yine yerine koyar.
 * fetch yoksa form klasik POST ile çalışır (sunucu redirect + flashdata).
 */
(function () {
    'use strict';

    var convoyRevealed = false;

    function onReady(fn) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fn, { once: true });
        } else {
            fn();
        }
    }

    /* Güvenlik/yedek: formu animasyonsuz yerine koyar (gsap yoksa, sahne
       başlamazsa ya da süre dolarsa). is-pulled, CSS'teki gizli-başlangıç
       animasyonunu (ctFormSafety) kapatır; kalanı inline stil halleder. */
    function revealConvoy() {
        if (convoyRevealed) return;
        convoyRevealed = true;
        if (!document.documentElement.classList.contains('contact-deliver')) return; // form zaten yerinde
        var convoy = document.querySelector('[data-convoy]');
        var form = document.querySelector('[data-contact-form]');
        if (convoy) convoy.classList.add('is-pulled');
        if (form) { form.style.opacity = '1'; form.style.transform = 'none'; }
    }

    onReady(function () {
        var form = document.querySelector('[data-contact-form]');
        if (form) initForm(form);

        initHours();

        // Güvenlik ağı: çekiş sahnesi hiç başlamazsa formu yine de yerine koy.
        if (document.documentElement.classList.contains('contact-deliver')) {
            window.setTimeout(revealConvoy, 6000);
        }

        try {
            initCrewScene();
        } catch (err) {
            revealConvoy();
            if (window.console && console.error) console.error('[barlas-contact]', err);
        }
    });

    /* ===================================================================
       USTA SAHNESİ: halat çekişi → form teslimi → idle → selam
       =================================================================== */

    function initCrewScene() {
        if (!document.documentElement.classList.contains('contact-deliver')) return;

        var gsap = window.gsap;
        var convoyEl = document.querySelector('[data-convoy]');
        var stage = document.querySelector('[data-foreman-stage]');
        var foreman = document.querySelector('[data-foreman]');
        var rope = document.querySelector('[data-rope]');
        var form = document.querySelector('[data-contact-form]');
        var lug = document.querySelector('[data-form-lug]');
        if (!convoyEl || !stage || !foreman || !rope || !form || !lug) { revealConvoy(); return; }
        if (!gsap) { revealConvoy(); return; }

        var ropePath = rope.querySelector('path');
        var hand = foreman.querySelector('[data-hand-anchor]');
        var root = foreman.querySelector('[data-fm-root]');
        var hips = foreman.querySelector('[data-fm-hips]');
        var head = foreman.querySelector('[data-fm-head]');
        var armF = foreman.querySelector('[data-fm-arms]');
        var foreF = foreman.querySelector('[data-fm-fores]');
        var armB = foreman.querySelector('[data-fm-arm-b]');   // torsonun arkasındaki kol
        var foreB = foreman.querySelector('[data-fm-fore-b]');
        if (!ropePath || !hand || !root || !hips || !head || !armF || !foreF) { revealConvoy(); return; }

        /* Eklem pivotları — çizim koordinatlarıyla (360x440 uzayı) eşleşir.
           svgOrigin local user-space'te yorumlanır: üst grup dönünce alt pivot
           onunla birlikte taşınır (omuz gövdeye yapışık kalır). Ön/arka kol ayrı
           gruplardır (arka kol gövdenin altında çizilir); aynı tween ikisini
           kendi pivotu etrafında birlikte döndürür. */
        gsap.set(hips, { svgOrigin: '156 258' });
        gsap.set(head, { svgOrigin: '151 152' });
        gsap.set(armF, { svgOrigin: '140 184' });
        gsap.set(foreF, { svgOrigin: '177 207' });
        if (armB) gsap.set(armB, { svgOrigin: '137 180' });
        if (foreB) gsap.set(foreB, { svgOrigin: '174 202' });
        var arms = armB ? [armB, armF] : [armF];
        var fores = foreB ? [foreB, foreF] : [foreF];

        /* Form fiziksel sağdan çekilir; RTL'de düzen aynalanır (usta sağda,
           form soldan gelir) → ofset işareti ters çevrilir. Usta SVG'si CSS'te
           scaleX(-1) ile aynalanır (contact.css [dir=rtl] kuralı). */
        var isRTL = (document.documentElement.getAttribute('dir') === 'rtl');
        var vw = window.innerWidth;
        var base = vw < 992 ? Math.round(vw * 0.72)
                            : Math.max(380, Math.min(760, Math.round(vw * 0.55)));
        var OFF = isRTL ? -base : base;

        var state = { sag: 34 };   // halat sarkması (px); gergin ≈ 5, gevşek ≈ 46
        var ropeOn = false;

        function drawRope() {
            if (!ropeOn) return;
            var cr = convoyEl.getBoundingClientRect();
            if (!cr.width) return;
            rope.setAttribute('viewBox', '0 0 ' + Math.round(cr.width) + ' ' + Math.round(cr.height));
            var h = hand.getBoundingClientRect();
            var l = lug.getBoundingClientRect();
            var x1 = h.left + h.width / 2 - cr.left, y1 = h.top + h.height / 2 - cr.top;
            var x2 = l.left + l.width / 2 - cr.left, y2 = l.top + l.height / 2 - cr.top;
            var mx = (x1 + x2) / 2, my = (y1 + y2) / 2 + state.sag;
            ropePath.setAttribute('d',
                'M' + x1.toFixed(1) + ' ' + y1.toFixed(1) +
                ' Q' + mx.toFixed(1) + ' ' + my.toFixed(1) +
                ' ' + x2.toFixed(1) + ' ' + y2.toFixed(1));
        }

        var idleTweens = [];
        function startIdle() {
            idleTweens.push(gsap.to(hips, { rotation: -1.5, duration: 1.9, yoyo: true, repeat: -1, ease: 'sine.inOut' }));
            idleTweens.push(gsap.to(head, { rotation: 1.2, duration: 2.3, yoyo: true, repeat: -1, ease: 'sine.inOut', delay: 0.3 }));
        }

        var started = false;
        function startPull() {
            if (started) return;
            started = true;
            convoyRevealed = true;                   // güvenlik zamanlayıcısı devreye girmesin

            /* is-pulled: CSS'teki gizli-başlangıç + ctFormSafety animasyonunu kapatır
               (form artık GSAP'ın inline transform'uyla sürülür). is-driving: form
               transform'u sürerken cam blur'u kapatır (contact.css) → jank yok. */
            convoyEl.classList.add('is-pulled');
            convoyEl.classList.add('is-driving');
            gsap.set(form, { x: OFF, opacity: 1 });
            gsap.set(rope, { opacity: 0 });
            ropeOn = true;
            gsap.ticker.add(drawRope);

            var tl = gsap.timeline({ defaults: { ease: 'power2.inOut' } });

            /* Usta halatı omzuna alır, halat belirir */
            tl.to(rope, { opacity: 1, duration: 0.35 }, 0);

            /* Bir asılış: uzan (gövde dikleşir, kollar öne, halat gevşer) →
               çek (gövde geriye yatar, kollar kapanır, halat gerilir, form bir
               adım gelir; asılış biterken halat "kaçırır" → form minik geri kayar). */
            function heave(targetX, slip, last) {
                // uzan
                tl.to(hips,  { rotation: -3,  duration: 0.34 }, '>');
                tl.to(arms,  { rotation: -20, duration: 0.34 }, '<');
                tl.to(fores, { rotation: 16,  duration: 0.34 }, '<');
                tl.to(head,  { rotation: 3,   duration: 0.34 }, '<');
                tl.to(state, { sag: 46, duration: 0.34 }, '<');
                // asıl
                tl.to(hips,  { rotation: -16, duration: 0.5, ease: 'power2.in' }, '>');
                tl.to(arms,  { rotation: 11,  duration: 0.5, ease: 'power2.in' }, '<');
                tl.to(fores, { rotation: -9,  duration: 0.5, ease: 'power2.in' }, '<');
                tl.to(head,  { rotation: -5,  duration: 0.5 }, '<');
                tl.to(root,  { y: 3, duration: 0.5 }, '<');
                tl.to(state, { sag: 5, duration: 0.3 }, '<');
                // form adımı (çekişle hafif gecikmeli — halat önce gerilir)
                tl.to(form, {
                    x: targetX,
                    duration: last ? 0.7 : 0.55,
                    ease: last ? 'back.out(1.3)' : 'power3.out'
                }, '<0.14');
                if (!last) {
                    tl.to(root, { y: 0, duration: 0.3 }, '>-0.1');
                    if (slip) tl.to(form, { x: '+=' + slip, duration: 0.16, ease: 'power1.out' }, '>-0.05');
                }
            }

            heave(OFF * 0.56, OFF * 0.025, false);
            heave(OFF * 0.22, OFF * 0.02, false);
            heave(0, 0, true);

            /* Form oturdu: usta halatı bırakır (gevşer + kaybolur), doğrulur */
            tl.to(state, { sag: 44, duration: 0.3, ease: 'power1.in' }, '>');
            tl.to(rope, { opacity: 0, duration: 0.3 }, '<');
            tl.add(function () { ropeOn = false; gsap.ticker.remove(drawRope); });
            /* Dinlenme pozu: eller gövdenin önünde, uyluk hizasında — 46°'de
               kollar gövde konturuyla üst üste binip kayboluyordu, 30° açık kalır */
            tl.to(hips,  { rotation: 0,  duration: 0.65, ease: 'power3.out' }, '<0.1');
            tl.to(arms,  { rotation: 30, duration: 0.65, ease: 'power3.out' }, '<');
            tl.to(fores, { rotation: 14, duration: 0.65, ease: 'power3.out' }, '<');
            tl.to(head,  { rotation: 0,  duration: 0.65 }, '<');
            tl.to(root,  { y: 0, duration: 0.65 }, '<');
            tl.add(function () {
                convoyEl.classList.remove('is-driving');   // form durdu → cam blur geri
                startIdle();
            });
        }

        /* Gönderildi: usta kolunu kaldırıp selam verir, onay kartı belirir
           (kart görünürken usta CSS ile silüete düşer — contact.css is-done). */
        document.addEventListener('contact:sent', function () {
            idleTweens.forEach(function (t) { t.kill(); });
            var t2 = gsap.timeline();
            t2.to(arms,  { rotation: -68, duration: 0.5, ease: 'back.out(1.7)' }, 0);
            t2.to(fores, { rotation: -26, duration: 0.5, ease: 'back.out(1.7)' }, 0.05);
            t2.to(head,  { rotation: -7, duration: 0.4, ease: 'power2.out' }, 0.1);
            t2.to(hips,  { rotation: 3,  duration: 0.4, ease: 'power2.out' }, 0);
            gsap.delayedCall(0.85, function () { stage.classList.add('is-done'); });
        }, { once: true });

        /* Başlat: sahne görünür olunca (hero sayfa başında → pratikte hemen). */
        if ('IntersectionObserver' in window) {
            var io = new IntersectionObserver(function (entries) {
                if (entries[0].isIntersecting) { startPull(); io.disconnect(); }
            }, { threshold: 0.05 });
            io.observe(stage);
        } else {
            startPull();
        }
    }

    /* ===================================================================
       FORM: gerçek POST (fetch) + doğrulama + başarı durumu
       =================================================================== */

    function initForm(form) {
        var statusEl = form.querySelector('.contact-form__status');
        var endpoint = form.getAttribute('action') || window.location.href;
        var msgs = {
            name: form.dataset.msgName || 'Required',
            email: form.dataset.msgEmail || 'Invalid email',
            message: form.dataset.msgMessage || 'Required',
            generic: form.dataset.msgGeneric || 'Please check the form.'
        };
        var EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        var KEYS = ['name', 'email', 'phone', 'company', 'subject', 'message'];

        function el(name) { return form.elements && form.elements[name] ? form.elements[name] : null; }
        function val(name) { var e = el(name); return e ? ('' + e.value).trim() : ''; }
        function fieldWrap(key) { return form.querySelector('[data-field="' + key + '"]'); }
        function errBox(key) { return form.querySelector('[data-error-for="' + key + '"]'); }
        function setErr(key, msg) {
            var w = fieldWrap(key), b = errBox(key);
            if (b) b.textContent = msg || '';
            if (w) w.classList.toggle('is-invalid', !!msg);
        }
        function clearErrs() { KEYS.forEach(function (k) { setErr(k, ''); }); }

        function validate() {
            clearErrs();
            var firstBad = null;
            function bad(key, message) { setErr(key, message); if (!firstBad) firstBad = key; }

            if (val('name').length < 2) bad('name', msgs.name);
            if (!EMAIL_RE.test(val('email'))) bad('email', msgs.email);
            if (val('message').length < 10) bad('message', msgs.message);

            if (firstBad) {
                var f = el(firstBad);
                if (f && f.focus) { try { f.focus(); } catch (e) {} }
            }
            return !firstBad;
        }

        KEYS.forEach(function (k) {
            var e = el(k);
            if (!e) return;
            var ev = (e.type === 'checkbox' || e.tagName === 'SELECT') ? 'change' : 'input';
            e.addEventListener(ev, function () { setErr(k, ''); });
        });

        if (!window.fetch || !window.FormData) return; // klasik POST'a izin ver

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            if (form.classList.contains('is-loading')) return;
            if (statusEl) statusEl.textContent = '';

            if (!validate()) {
                if (statusEl) statusEl.textContent = msgs.generic;
                return;
            }

            form.classList.add('is-loading');

            fetch(endpoint, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: new FormData(form),
                credentials: 'same-origin'
            }).then(function (r) {
                return r.json().then(function (j) { return { status: r.status, body: j }; })
                    .catch(function () { return { status: r.status, body: null }; });
            }).then(function (res) {
                form.classList.remove('is-loading');
                var body = res.body || {};

                if (body.ok) {
                    if (body.csrf && body.csrf.name) {
                        var ci = form.querySelector('[name="' + body.csrf.name + '"]');
                        if (ci) ci.value = body.csrf.hash;
                    }
                    if (statusEl) statusEl.textContent = body.message || '';
                    // Mesaj gönderildi → usta selam verir (contact:sent) → başarı belirir
                    form.classList.add('is-sending');
                    document.dispatchEvent(new CustomEvent('contact:sent'));
                    window.setTimeout(function () {
                        form.classList.remove('is-sending');
                        form.classList.add('is-sent');
                        var title = form.querySelector('.contact-form__success-title');
                        if (title) { title.setAttribute('tabindex', '-1'); try { title.focus(); } catch (e) {} }
                    }, 430);
                    return;
                }

                if (res.status === 422 && body.errors) {
                    var first = null;
                    Object.keys(body.errors).forEach(function (k) {
                        setErr(k, body.errors[k]);
                        if (!first) first = k;
                    });
                    if (statusEl) statusEl.textContent = body.message || msgs.generic;
                    var fe = first && el(first);
                    if (fe && fe.focus) { try { fe.focus(); } catch (e) {} }
                    return;
                }

                if (statusEl) statusEl.textContent = (body && body.message) || msgs.generic;
            }).catch(function () {
                form.classList.remove('is-loading');
                if (statusEl) statusEl.textContent = msgs.generic;
            });
        });

        var resetBtn = form.querySelector('[data-reset]');
        if (resetBtn) {
            resetBtn.addEventListener('click', function () {
                form.classList.remove('is-sent');
                form.reset();
                clearErrs();
                if (statusEl) statusEl.textContent = '';
                var n = el('name');
                if (n && n.focus) { try { n.focus(); } catch (e) {} }
            });
        }
    }

    /* ===================================================================
       ÇALIŞMA SAATLERİ ROZETİ (artık sayfada yoksa sessizce atlar)
       =================================================================== */

    function initHours() {
        var badge = document.querySelector('[data-hours-badge]');
        if (!badge) return;
        var openLabel = badge.getAttribute('data-open');
        var closedLabel = badge.getAttribute('data-closed');
        if (!openLabel || !closedLabel) return;

        var now = new Date();
        var day = now.getDay();
        var mins = now.getHours() * 60 + now.getMinutes();
        var open = false;
        if (day >= 1 && day <= 5) open = mins >= 510 && mins < 1080;
        else if (day === 6) open = mins >= 540 && mins < 840;

        badge.textContent = open ? openLabel : closedLabel;
        badge.classList.add(open ? 'is-open' : 'is-closed');
        badge.hidden = false;
    }
})();
