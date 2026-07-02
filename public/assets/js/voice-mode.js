/* =====================================================================
   voice-mode.js — Sesli Site Modu (tasarım anayasası: şoförler için
   sesli gezinme). Web Speech API destekliyse header'daki gizli düğmeyi
   açar; sayfanın ana içeriğini sayfa dilinde sesli okur.
   ===================================================================== */
(function () {
    'use strict';

    var btn = document.querySelector('[data-voice-toggle]');
    if (!btn || !('speechSynthesis' in window) || typeof SpeechSynthesisUtterance === 'undefined') return;

    btn.hidden = false;

    var speaking   = false;
    var labelStart = btn.getAttribute('data-label-start') || '';
    var labelStop  = btn.getAttribute('data-label-stop') || '';

    function setState(on) {
        speaking = on;
        btn.setAttribute('aria-pressed', on ? 'true' : 'false');
        btn.classList.toggle('is-on', on);
        var label = on ? labelStop : labelStart;
        if (label) {
            btn.setAttribute('aria-label', label);
            btn.title = label;
        }
    }

    function stop() {
        window.speechSynthesis.cancel();
        setState(false);
    }

    /* Ana içerikteki okunur metni topla (dekoratif/gizli öğeler atlanır). */
    function collectText() {
        var scope = document.getElementById('main-content')
            || document.querySelector('main')
            || document.body;
        var parts = [];

        scope.querySelectorAll('h1, h2, h3, p, li, dt, dd').forEach(function (el) {
            if (el.closest('[aria-hidden="true"], script, style, noscript, [hidden]')) return;
            if (el.getClientRects().length === 0) return; // görünmeyeni okuma
            var t = (el.textContent || '').replace(/\s+/g, ' ').trim();
            if (t) parts.push(t);
        });

        // Tarayıcı sentez sınırlarına takılmamak için makul uzunlukta kes.
        return parts.join('. ').slice(0, 6000);
    }

    btn.addEventListener('click', function () {
        if (speaking) { stop(); return; }

        var text = collectText();
        if (!text) return;

        var u  = new SpeechSynthesisUtterance(text);
        u.lang = document.documentElement.lang || 'tr';
        u.onend   = function () { setState(false); };
        u.onerror = function () { setState(false); };

        window.speechSynthesis.cancel();
        window.speechSynthesis.speak(u);
        setState(true);
    });

    window.addEventListener('pagehide', stop);
})();
