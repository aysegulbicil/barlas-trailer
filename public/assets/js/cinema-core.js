/**
 * cinema-core.js — sinematik katman çekirdeği (v3.0 addendum, §0 + §9.1)
 * =============================================================================
 * TÜM cinema-* modüllerinin ortak temeli. Tek başına hiçbir görsel efekt
 * çizmez; yalnızca "ne kadar hareket" sorusunu BİR KEZ cevaplar ve ortak
 * yardımcıları sunar. Diğer modüller window.BarlasCinema üzerinden konuşur.
 *
 * Katman kuralları (pazarlıksız):
 *   - prefers-reduced-motion  → tier 'off'  (tam statik geri dönüş)
 *   - mobil / kaba işaretçi / saveData / düşük RAM → tier 'lite'
 *   - aksi halde 'full'
 *   - Sekme gizlenince tüm animasyonlar durur (visibilitychange otobüsü).
 *   - Ağır efektler yalnızca görünüme girerken kurulur (lazy yardımcısı).
 *
 * <html> sınıfları: cine-off | cine-lite | cine-full — CSS bunlarla kapılanır.
 * Klasik IIFE + defer (hero-static.js / yeni-app.js ile aynı kalıp).
 */
(function () {
    'use strict';

    var html = document.documentElement;

    /* ---------------- hareket katmanı (tier) ---------------- */

    function mq(q) {
        try { return window.matchMedia(q).matches; } catch (e) { return false; }
    }

    var reduced = mq('(prefers-reduced-motion: reduce)');
    var coarse  = !mq('(hover: hover) and (pointer: fine)');
    var small   = mq('(max-width: 820px)');
    var conn    = navigator.connection || {};
    var lowData = conn.saveData === true;
    var lowMem  = typeof navigator.deviceMemory === 'number' && navigator.deviceMemory <= 4;

    var tier = reduced ? 'off' : ((coarse || small || lowData || lowMem) ? 'lite' : 'full');
    html.classList.add('cine-' + tier);

    /* ---------------- görünürlük otobüsü ---------------- */
    /* Şart §8.2: sekme pasifken shader/partikül SUSAR. Modüller subscribe
       eder; cb(visible) hem kayıtta hem her değişimde çağrılır. */

    var visSubs = [];
    function visible() { return document.visibilityState !== 'hidden'; }

    document.addEventListener('visibilitychange', function () {
        var v = visible();
        for (var i = 0; i < visSubs.length; i++) {
            try { visSubs[i](v); } catch (e) { /* tek abone tüm otobüsü devirmesin */ }
        }
    });

    /* ---------------- lazy kurulum yardımcısı ---------------- */
    /* Şart §8.3: fold altındaki ağır efekt, görünüme yaklaşınca kurulur.
       cb bir "teardown" fonksiyonu DÖNDÜREBİLİR; öğe tekrar ekrandan
       çıkarsa çağrılmaz (kurulum tek seferlik), ama görünürlük otobüsüyle
       birleşince duraklatma modülün kendi işidir. */

    function lazy(el, cb, margin) {
        if (!el) return;
        if (!('IntersectionObserver' in window)) { cb(el); return; }
        var io = new IntersectionObserver(function (entries) {
            for (var i = 0; i < entries.length; i++) {
                if (entries[i].isIntersecting) {
                    io.disconnect();
                    cb(el);
                    return;
                }
            }
        }, { rootMargin: margin || '25% 0px' });
        io.observe(el);
    }

    /* ---------------- rAF döngü yardımcısı ---------------- */
    /* Görünürlüğe otomatik bağlı animasyon döngüsü: sekme gizlenince durur,
       dönünce kaldığı yerden sürer. dur() ile kalıcı kapatılır. */

    function loop(step) {
        var running = false, rafId = 0, stopped = false;

        function frame(t) {
            if (!running) return;
            step(t);
            rafId = window.requestAnimationFrame(frame);
        }
        function start() {
            if (running || stopped) return;
            running = true;
            rafId = window.requestAnimationFrame(frame);
        }
        function pause() {
            running = false;
            if (rafId) window.cancelAnimationFrame(rafId);
        }
        visSubs.push(function (v) { if (v) { start(); } else { pause(); } });
        if (visible()) start();

        return {
            stop: function () { stopped = true; pause(); },
            pause: pause,
            resume: start
        };
    }

    /* ---------------- çerez yardımcıları ---------------- */
    /* İntro "ilk ziyaret" bayrağı vb. için — locale çerezi ile aynı yol. */

    function getCookie(name) {
        var m = document.cookie.match(new RegExp('(?:^|; )' + name + '=([^;]*)'));
        return m ? decodeURIComponent(m[1]) : null;
    }
    function setCookie(name, value, days) {
        var s = name + '=' + encodeURIComponent(value) + '; path=/; SameSite=Lax';
        if (days) s += '; max-age=' + (days * 86400);
        document.cookie = s;
    }

    /* ---------------- gündüz/gece değeri ---------------- */
    /* cinema-scroll.js yazar (0=gündüz, 1=gece); 3D sahne dahil herkes
       buradan okur. CSS tarafı --cine-night değişkeninden beslenir. */

    var night = 0;
    var nightSubs = [];
    function setNight(v) {
        v = v < 0 ? 0 : (v > 1 ? 1 : v);
        if (Math.abs(v - night) < 0.01) return;
        night = v;
        html.style.setProperty('--cine-night', night.toFixed(3));
        for (var i = 0; i < nightSubs.length; i++) {
            try { nightSubs[i](night); } catch (e) { /* yut */ }
        }
    }

    window.BarlasCinema = {
        tier: tier,
        reduced: reduced,
        motionOK: tier !== 'off',
        full: tier === 'full',
        visible: visible,
        onVisibility: function (cb) { visSubs.push(cb); cb(visible()); },
        lazy: lazy,
        loop: loop,
        getCookie: getCookie,
        setCookie: setCookie,
        setNight: setNight,
        getNight: function () { return night; },
        onNight: function (cb) { nightSubs.push(cb); cb(night); }
    };
})();
