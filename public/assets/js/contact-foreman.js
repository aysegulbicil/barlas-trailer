/**
 * contact-foreman.js — İletişim sayfası etkileşimi
 *
 * 1) Usta sahnesi (sol): çizgi film stili SVG usta maskotu (eklemli: kalça/baş/omuz/dirsek
 *    grupları) halata asıla asıla sağdaki iletişim formunu sahneye çeker.
 *    Form gizli başlar; her asılışta bir adım yaklaşır, yerine oturunca usta
 *    halatı bırakıp doğrulur ve nefes alma (idle) döngüsüne geçer. Form
 *    gönderilince usta kolunu kaldırıp selam verir, sahnede onay belirir.
 *    "Yeni mesaj"la form sıfırlanınca sahne başa sarar ve usta formu YENİDEN
 *    çeker (contact:reset dinlenir; teslimatta contact:delivered yayınlanır).
 * 2) Halat: ustanın eli ([data-hand-anchor]) ile formun kulpu ([data-form-lug])
 *    arasında her karede yeniden çizilir (getBoundingClientRect tabanlı) —
 *    LTR/RTL düzenlerinde aynı kod çalışır; gerginlik state.sag ile
 *    tween'lenir (gergin = düz, gevşek = sarkık quadratic).
 * 3) Form (sağ): gerçek POST (Contact::submit) — fetch ile gönderilir.
 *
 * Aşamalı geliştirme: sahne yalnız masaüstünde (≥992px) koşar — mobilde
 * contact-deliver eklenmez, usta CSS ile gizlidir, form doğrudan görünür.
 * Reduced-motion'da da contact-deliver eklenmez → usta (masaüstünde)
 * statik pozda durur, halat çizilmez, form sabit görünür. GSAP yoksa
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
       USTA SAHNESİ: beklenti → gerilim → asılışlar → teslim → canlı idle
       Tüm süre/easing/poz değerleri TUNE'dadır (magic number yok) — sahne
       oradan ayarlanır. Yalnız transform+opacity anime edilir (layout yok).
       =================================================================== */

    function initCrewScene() {
        if (!document.documentElement.classList.contains('contact-deliver')) return;
        /* Mobilde sahne yok: head script contact-deliver'ı yalnız ≥992px'te
           ekler, CSS sahneyi gizler. Bu kapı sıra dışı durumlara emniyettir. */
        if (window.matchMedia('(max-width: 991px)').matches) { revealConvoy(); return; }

        var gsap = window.gsap;
        var convoyEl = document.querySelector('[data-convoy]');
        var stage = document.querySelector('[data-foreman-stage]');
        var foreman = document.querySelector('[data-foreman]');
        var rope = document.querySelector('[data-rope]');
        var form = document.querySelector('[data-contact-form]');
        var lug = document.querySelector('[data-form-lug]');
        if (!convoyEl || !stage || !foreman || !rope || !form || !lug) { revealConvoy(); return; }
        if (!gsap) { revealConvoy(); return; }

        var ropeLayers = rope.querySelectorAll('path');
        var knot = rope.querySelector('[data-rope-knot]');
        var hand = foreman.querySelector('[data-hand-anchor]');
        var root = foreman.querySelector('[data-fm-root]');
        var hips = foreman.querySelector('[data-fm-hips]');
        var head = foreman.querySelector('[data-fm-head]');
        var eye = foreman.querySelector('[data-fm-eye]');
        var pupil = foreman.querySelector('[data-fm-pupil]');
        var armF = foreman.querySelector('[data-fm-arms]');
        var foreF = foreman.querySelector('[data-fm-fores]');
        var armB = foreman.querySelector('[data-fm-arm-b]');   // torsonun arkasındaki kol
        var foreB = foreman.querySelector('[data-fm-fore-b]');
        if (!ropeLayers.length || !hand || !root || !hips || !head || !armF || !foreF) { revealConvoy(); return; }

        /* ---------------- TUNE: sahnenin tek ayar tablosu ----------------
           t: süreler (sn) · ease: GSAP easing'leri · pose: eklem açıları (°)
           rope: halat fiziği (px). Sahne yalnız masaüstünde koşar (mobilde
           usta gizli, form doğrudan görünür). */
        var TUNE = {
            steps: [{ to: 0.56, slip: 0.025 }, { to: 0.22, slip: 0.02 }, { to: 0, slip: 0 }],
            rope: {
                slack: 46, taut: 4, rest: 34, release: 44,   // sarkma (px)
                preTension: 14,                              // gerilim anı sarkması
                coreW: 4.2, sheenW: 1.6, fiberW: 4.2,        // katman kalınlıkları (px)
                tenseScale: 0.78,                            // tam gerginlikte incelme (esneme hissi)
                knotR: 4.4
            },
            pose: {
                reach:  { hips: -3,  head: 3,  arm: -22, fore: 17 },   // uzanma
                brace:  { hips: -5.5, head: 4.5, arm: -25, fore: 19 }, // beklenti: geriye yaslan, omuz gerilir
                yank:   { hips: -16, head: -5, arm: 11,  fore: -9, dip: 3.5 },
                rest:   { hips: 0,  head: 0,  arm: 30,  fore: 14 },
                salute: { hips: 3,  head: -7, arm: -68, fore: -26 },
                glance: { head: 3.4, pupilX: 1.1, pupilY: 0.3 },
                breatheScale: 1.008, swayDeg: -1.5, bobDeg: 1.2,
                squint: 0.55, trembleDeg: 0.7
            },
            t: {
                ropeIn: 0.32, reach: 0.34, brace: 0.3, tension: 0.26, tremble: 0.08,
                yank: 0.5, yankFast: 0.42, formLag: 0.14, formStep: 0.55, formLast: 0.72,
                slip: 0.16, microBack: 0.12, microSettle: 0.24,
                release: 0.9, ropeOut: 0.35, settle: 0.65,
                breathe: 2.1, sway: 1.9, bob: 2.3,
                blinkClose: 0.07, blinkOpen: 0.11, blinkMin: 2.4, blinkMax: 5.4,
                glance: 0.45, saluteArm: 0.5, saluteBody: 0.4, done: 0.85
            },
            ease: {
                reach: 'power2.inOut', tension: 'power1.inOut',
                yank: 'power3.in', body: 'power2.inOut',
                formStep: 'power3.out', formLast: 'back.out(1.55)',
                micro: 'power2.out', release: 'elastic.out(1, 0.45)',
                settle: 'power3.out', idle: 'sine.inOut',
                glance: 'power2.out', salute: 'back.out(1.7)', blink: 'power2.in'
            },
            formTilt: 0.5    // uçuş sırasında momentum eğimi (derece)
        };

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
        if (eye) gsap.set(eye, { svgOrigin: '162 121' });
        var arms = armB ? [armB, armF] : [armF];
        var fores = foreB ? [foreB, foreF] : [foreF];

        var phase = 'boot';   // boot | pulling | idle | saluting

        /* Form fiziksel sağdan çekilir; RTL'de düzen aynalanır (usta sağda,
           form soldan gelir) → ofset işareti ters çevrilir. Usta SVG'si CSS'te
           scaleX(-1) ile aynalanır (contact.css [dir=rtl] kuralı). Her çekişte
           yeniden hesaplanır: tekrar oynatmada pencere boyutu değişmiş olabilir. */
        var isRTL = (document.documentElement.getAttribute('dir') === 'rtl');
        function offscreenX() {
            var base = Math.max(380, Math.min(760, Math.round(window.innerWidth * 0.55)));
            return isRTL ? -base : base;
        }

        /* ------------------------- HALAT ---------------------------------
           Üç katman aynı quadratic path'i paylaşır: koyu çekirdek + açık
           parlama + lif kesikleri (örgü dokusu). state.tension 0→1 arasında
           kalınlığı inceltir (halatın esneyip gerilme hissi); sag sarkmadır.
           Uçlar her karede elin ve kulpun gerçek rect'lerinden okunur → halat
           forma "gerçekten bağlı" (knot dairesi kulpun üstünde durur). */
        var state = { sag: TUNE.rope.rest, tension: 0 };
        var ropeOn = false;
        var baseW = [TUNE.rope.coreW, TUNE.rope.sheenW, TUNE.rope.fiberW];

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
            var d = 'M' + x1.toFixed(1) + ' ' + y1.toFixed(1) +
                    ' Q' + mx.toFixed(1) + ' ' + my.toFixed(1) +
                    ' ' + x2.toFixed(1) + ' ' + y2.toFixed(1);
            var thin = 1 - (1 - TUNE.rope.tenseScale) * state.tension;
            for (var i = 0; i < ropeLayers.length; i++) {
                ropeLayers[i].setAttribute('d', d);
                ropeLayers[i].style.strokeWidth = (baseW[i] * thin).toFixed(2) + 'px';
            }
            if (knot) {
                knot.setAttribute('cx', x2.toFixed(1));
                knot.setAttribute('cy', y2.toFixed(1));
                knot.setAttribute('r', (TUNE.rope.knotR * (0.9 + 0.1 * thin)).toFixed(2));
            }
        }

        /* ------------------- CANLILIK: idle / göz / bakış ----------------
           Karakter hiç donmaz: nefes (gövde ölçeği), salınım (kalça), kafa
           salınımı idle'da; göz kırpma HER fazda koşar (rastgele aralık).
           eyeBase: efor sırasında göz kısılır — kırpma o tabana geri açılır. */
        var idleTweens = [];
        var headIdle = null;
        var eyeBase = 1;

        function startHeadIdle() {
            headIdle = gsap.to(head, {
                rotation: TUNE.pose.bobDeg, duration: TUNE.t.bob,
                yoyo: true, repeat: -1, ease: TUNE.ease.idle, delay: 0.3
            });
            idleTweens.push(headIdle);
        }
        function startIdle() {
            phase = 'idle';
            idleTweens.push(gsap.to(hips, {
                rotation: TUNE.pose.swayDeg, duration: TUNE.t.sway,
                yoyo: true, repeat: -1, ease: TUNE.ease.idle
            }));
            idleTweens.push(gsap.to(hips, {
                scaleY: TUNE.pose.breatheScale, duration: TUNE.t.breathe,
                yoyo: true, repeat: -1, ease: TUNE.ease.idle
            }));
            startHeadIdle();
        }
        function stopIdle() {
            idleTweens.forEach(function (t) { t.kill(); });
            idleTweens.length = 0;
            headIdle = null;
            gsap.set(hips, { scaleY: 1 });
        }

        function squint(on) {
            if (!eye) return;
            eyeBase = on ? TUNE.pose.squint : 1;
            gsap.to(eye, { scaleY: eyeBase, duration: TUNE.t.blinkOpen, ease: TUNE.ease.micro, overwrite: 'auto' });
        }
        function blink() {
            if (eye) {
                var b = gsap.timeline({ onComplete: scheduleBlink });
                b.to(eye, { scaleY: 0.08, duration: TUNE.t.blinkClose, ease: TUNE.ease.blink });
                b.to(eye, { scaleY: eyeBase, duration: TUNE.t.blinkOpen, ease: TUNE.ease.micro });
            }
        }
        function scheduleBlink() {
            gsap.delayedCall(gsap.utils.random(TUNE.t.blinkMin, TUNE.t.blinkMax), blink);
        }
        scheduleBlink();

        /* Hover bakışı: imleç formdayken usta o yana süzülür (yalnız gerçek
           hover'lı cihazlarda ve yalnız idle'da — çekişe karışmaz). */
        if (window.matchMedia('(hover: hover)').matches) {
            form.addEventListener('mouseenter', function () {
                if (phase !== 'idle') return;
                if (headIdle) { headIdle.kill(); headIdle = null; }
                gsap.to(head, { rotation: TUNE.pose.glance.head, duration: TUNE.t.glance, ease: TUNE.ease.glance, overwrite: 'auto' });
                if (pupil) gsap.to(pupil, { x: TUNE.pose.glance.pupilX, y: TUNE.pose.glance.pupilY, duration: TUNE.t.glance, ease: TUNE.ease.glance });
            });
            form.addEventListener('mouseleave', function () {
                if (pupil) gsap.to(pupil, { x: 0, y: 0, duration: TUNE.t.glance, ease: TUNE.ease.glance });
                if (phase !== 'idle') return;
                gsap.to(head, {
                    rotation: 0, duration: TUNE.t.glance, ease: TUNE.ease.glance, overwrite: 'auto',
                    onComplete: function () { if (phase === 'idle' && !headIdle) startHeadIdle(); }
                });
            });
        }

        /* --------------------------- ÇEKİŞ -------------------------------
           Beklenti (yaslan + omuz gerilir + halat gerilir + titreme) →
           asılışlar (ağırlık aktarımı; form gecikmeli ivmelenir, asılış
           sonunda minik kaçırma) → teslim (overshoot + 2-3px geri oturma,
           halat elastik boşalır) → idle. */
        var pullTl = null;     // aktif çekiş timeline'ı; koşarken yeni çekiş başlatılmaz
        function startPull() {
            if (pullTl && pullTl.isActive()) return;
            convoyRevealed = true;                   // güvenlik zamanlayıcısı devreye girmesin
            phase = 'pulling';
            var OFF = offscreenX();
            var P = TUNE.pose, T = TUNE.t, E = TUNE.ease;

            /* is-pulled: CSS'teki gizli-başlangıç + ctFormSafety animasyonunu kapatır
               (form artık GSAP'ın inline transform'uyla sürülür). is-driving: form
               transform'u sürerken cam blur'u kapatır (contact.css) → jank yok. */
            convoyEl.classList.add('is-pulled');
            convoyEl.classList.add('is-driving');
            gsap.set(form, { x: OFF, opacity: 1, rotation: 0 });
            gsap.set([rope, lug], { opacity: 0 });
            state.sag = TUNE.rope.slack;
            state.tension = 0;
            ropeOn = true;
            gsap.ticker.add(drawRope);

            var tl = pullTl = gsap.timeline({ defaults: { ease: E.body } });

            /* BEKLENTİ: halat + form kulpu birlikte görünür (kulp yalnız halat
               sahnedeyken var), usta kavrayıp geriye yaslanır, omuzlar gerilir,
               halat toplanır (sarkma düşer) ve efor titremesi gelir. */
            tl.to([rope, lug], { opacity: 1, duration: T.ropeIn }, 0);
            tl.to(hips,  { rotation: P.brace.hips, duration: T.brace, ease: E.reach }, T.ropeIn * 0.4);
            tl.to(head,  { rotation: P.brace.head, duration: T.brace, ease: E.reach }, '<');
            tl.to(arms,  { rotation: P.brace.arm,  duration: T.brace, ease: E.reach }, '<');
            tl.to(fores, { rotation: P.brace.fore, duration: T.brace, ease: E.reach }, '<');
            tl.add(function () { squint(true); }, '>-0.05');
            tl.to(state, { sag: TUNE.rope.preTension, tension: 0.55, duration: T.tension, ease: E.tension }, '>');
            tl.to(hips,  { rotation: '+=' + P.trembleDeg, duration: T.tremble, yoyo: true, repeat: 3, ease: E.idle }, '<');

            /* Bir asılış: uzan (gövde toparlanır, halat gevşer) → asıl (gövde
               geriye devrilir, ağırlık merkezi düşer, halat gerilir; form
               gecikmeyle ivmelenir — kuvvet halattan geçiyor hissi). */
            function heave(stepIdx, targetX, slip, last) {
                if (stepIdx > 0) {   // ilk asılış beklenti pozundan başlar
                    tl.to(hips,  { rotation: P.reach.hips, duration: T.reach, ease: E.reach }, '>');
                    tl.to(arms,  { rotation: P.reach.arm,  duration: T.reach, ease: E.reach }, '<');
                    tl.to(fores, { rotation: P.reach.fore, duration: T.reach, ease: E.reach }, '<');
                    tl.to(head,  { rotation: P.reach.head, duration: T.reach, ease: E.reach }, '<');
                    tl.to(state, { sag: TUNE.rope.slack, tension: 0.2, duration: T.reach, ease: E.tension }, '<');
                }
                var yankDur = last ? T.yank : T.yankFast;
                tl.to(hips,  { rotation: P.yank.hips, duration: yankDur, ease: E.yank }, '>');
                tl.to(arms,  { rotation: P.yank.arm,  duration: yankDur, ease: E.yank }, '<');
                tl.to(fores, { rotation: P.yank.fore, duration: yankDur, ease: E.yank }, '<');
                tl.to(head,  { rotation: P.yank.head, duration: yankDur }, '<');
                tl.to(root,  { y: P.yank.dip, duration: yankDur }, '<');
                tl.to(state, { sag: TUNE.rope.taut, tension: 1, duration: yankDur * 0.6, ease: E.yank }, '<');
                // form: halat gerildikten SONRA harekete geçer; uçuşta minik
                // momentum eğimi alır, son adımda overshoot ile oturur
                tl.to(form, {
                    x: targetX,
                    rotation: last ? 0 : (isRTL ? TUNE.formTilt : -TUNE.formTilt),
                    duration: last ? T.formLast : T.formStep,
                    ease: last ? E.formLast : E.formStep
                }, '<' + T.formLag);
                if (!last) {
                    tl.to(root, { y: 0, duration: T.reach, ease: E.settle }, '>-0.1');
                    tl.to(state, { tension: 0.35, duration: T.reach, ease: E.tension }, '<');
                    if (slip) tl.to(form, { x: '+=' + slip, duration: T.slip, ease: 'power1.out' }, '>-0.05');
                }
            }

            TUNE.steps.forEach(function (s, i) {
                heave(i, OFF * s.to, OFF * s.slip, i === TUNE.steps.length - 1);
            });

            /* TESLİM: form 2-3px geri sekip oturur (fizik); usta halatı
               bırakır — halat elastik boşalır, sönerken de "boing" hissi. */
            var dir = isRTL ? -1 : 1;
            tl.to(form, { x: dir * 2.5, rotation: 0, duration: T.microBack, ease: 'power1.out' }, '>');
            tl.to(form, { x: 0, duration: T.microSettle, ease: E.micro }, '>');
            tl.to(state, { sag: TUNE.rope.release, tension: 0, duration: T.release, ease: E.release }, '<-0.1');
            tl.to([rope, lug], { opacity: 0, duration: T.ropeOut }, '<0.25');
            tl.add(function () { ropeOn = false; gsap.ticker.remove(drawRope); squint(false); });
            /* Dinlenme pozu: eller gövdenin önünde, uyluk hizasında — 46°'de
               kollar gövde konturuyla üst üste binip kayboluyordu, 30° açık kalır */
            tl.to(hips,  { rotation: P.rest.hips, duration: T.settle, ease: E.settle }, '<0.1');
            tl.to(arms,  { rotation: P.rest.arm,  duration: T.settle, ease: E.settle }, '<');
            tl.to(fores, { rotation: P.rest.fore, duration: T.settle, ease: E.settle }, '<');
            tl.to(head,  { rotation: P.rest.head, duration: T.settle }, '<');
            tl.to(root,  { y: 0, duration: T.settle }, '<');
            tl.add(function () {
                convoyEl.classList.remove('is-driving');   // form durdu → cam blur geri
                startIdle();
                // Form yerine oturdu (ilk geliş VE tekrarlar) — dinleyen varsa odaklanır
                document.dispatchEvent(new CustomEvent('contact:delivered'));
            });
        }

        /* Gönderildi: usta kolunu kaldırıp selam verir, onay kartı belirir
           (kart görünürken usta CSS ile silüete düşer — contact.css is-done).
           once DEĞİL: "yeni mesaj" akışında her gönderimde tekrar selam verir. */
        document.addEventListener('contact:sent', function () {
            phase = 'saluting';
            stopIdle();
            var P = TUNE.pose, T = TUNE.t, E = TUNE.ease;
            var t2 = gsap.timeline();
            t2.to(arms,  { rotation: P.salute.arm,  duration: T.saluteArm, ease: E.salute }, 0);
            t2.to(fores, { rotation: P.salute.fore, duration: T.saluteArm, ease: E.salute }, 0.05);
            t2.to(head,  { rotation: P.salute.head, duration: T.saluteBody, ease: E.glance }, 0.1);
            t2.to(hips,  { rotation: P.salute.hips, duration: T.saluteBody, ease: E.glance }, 0);
            gsap.delayedCall(T.done, function () { stage.classList.add('is-done'); });
        });

        /* "Yeni mesaj gönder": sahneyi başa sar, formu yeniden çektir.
           (Buton yalnız gönderim sonrası görünür → çekiş sırasında gelmez;
           yine de isActive() koruması çifte tetiği yutar.) */
        function resetScene() {
            stopIdle();
            if (pullTl) { pullTl.kill(); pullTl = null; }
            gsap.killTweensOf(arms.concat(fores, [hips, head, root, form, state, lug]));
            if (pupil) gsap.set(pupil, { x: 0, y: 0 });
            squint(false);
            ropeOn = false;
            gsap.ticker.remove(drawRope);
            gsap.set([rope, lug], { opacity: 0 });
            stage.classList.remove('is-done');
            gsap.set(arms.concat(fores, [hips, head]), { rotation: 0 });
            gsap.set(hips, { scaleY: 1 });
            gsap.set(root, { y: 0 });
            gsap.set(form, { rotation: 0 });
        }

        document.addEventListener('contact:reset', function () {
            resetScene();
            startPull();
        });

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
        var focusAfterDeliver = false;

        function focusName() {
            var n = el('name');
            if (!n || !n.focus) return;
            try { n.focus({ preventScroll: true }); } catch (e) { try { n.focus(); } catch (e2) {} }
        }

        /* Usta formu yeniden çektiyse imleç, form yerine OTURUNCA ada gider
           (uçuştaki gizli inputa odaklanmak kaydırma/titreme yapardı). */
        document.addEventListener('contact:delivered', function () {
            if (!focusAfterDeliver) return;
            focusAfterDeliver = false;
            focusName();
        });

        if (resetBtn) {
            resetBtn.addEventListener('click', function () {
                form.classList.remove('is-sent');
                form.reset();
                clearErrs();
                if (statusEl) statusEl.textContent = '';
                if (document.documentElement.classList.contains('contact-deliver')) {
                    /* Hareket açık: sahne başa sarar, usta formu yeniden çeker.
                       Sahne kurulamamışsa (dinleyici yok) teslimat olayı gelmez —
                       güvenlik zamanlayıcısı odağı yine de teslim eder. */
                    focusAfterDeliver = true;
                    document.dispatchEvent(new CustomEvent('contact:reset'));
                    window.setTimeout(function () {
                        if (!focusAfterDeliver) return;
                        focusAfterDeliver = false;
                        focusName();
                    }, 6500);
                } else {
                    focusName();
                }
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
