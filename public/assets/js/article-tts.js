/* =====================================================================
   article-tts.js — Sesli makale okuma (§7.7 "sesli blog"). Web Speech
   API ile istemci tarafında, sıfır işletme maliyeti. voice-mode.js'in
   sayfa-geneli okumasından farkı: yalnız data-listen-target ile
   gösterilen makale gövdesini okur; blog, ansiklopedi ve haber
   sayfalarındaki "Bu yazıyı dinle" düğmesini besler.
   ===================================================================== */
(function () {
    'use strict';

    var buttons = document.querySelectorAll('[data-listen-btn]');
    if (!buttons.length || !('speechSynthesis' in window) || typeof SpeechSynthesisUtterance === 'undefined') return;

    var active = null; // aynı anda tek okuma

    function setState(btn, on) {
        btn.setAttribute('aria-pressed', on ? 'true' : 'false');
        btn.classList.toggle('is-on', on);
        var label = btn.getAttribute(on ? 'data-label-stop' : 'data-label-start') || '';
        var span  = btn.querySelector('[data-listen-label]');
        if (span && label) span.textContent = label;
    }

    function stop() {
        window.speechSynthesis.cancel();
        if (active) setState(active, false);
        active = null;
    }

    buttons.forEach(function (btn) {
        btn.hidden = false;

        btn.addEventListener('click', function () {
            if (active === btn) { stop(); return; }
            stop();

            var scope = document.querySelector(btn.getAttribute('data-listen-target') || '');
            if (!scope) return;

            var parts = [];
            scope.querySelectorAll('h1, h2, h3, p, li, dt, dd').forEach(function (el) {
                if (el.closest('[aria-hidden="true"], script, style, noscript, [hidden]')) return;
                var t = (el.textContent || '').replace(/\s+/g, ' ').trim();
                if (t) parts.push(t);
            });

            // Tarayıcı sentez sınırlarına takılmamak için makul uzunlukta kes.
            var text = parts.join('. ').slice(0, 6000);
            if (!text) return;

            var u  = new SpeechSynthesisUtterance(text);
            u.lang = document.documentElement.lang || 'tr';
            u.onend   = stop;
            u.onerror = stop;

            window.speechSynthesis.speak(u);
            active = btn;
            setState(btn, true);
        });
    });

    window.addEventListener('pagehide', stop);
})();
