/* =====================================================================
   assistant-widget.js — site geneli asistan paneli
   Ana sayfa konsoluyla aynı POST {locale}/ai/ask ucunu kullanır.
   XSS-güvenli: tüm metinler textContent ile basılır; linkler yalnız
   kendi API'mizin döndürdüğü URL'lerdir.
   ===================================================================== */
(function () {
    'use strict';

    var root = document.querySelector('[data-aw]');
    if (!root) return;

    var endpoint = root.getAttribute('data-endpoint') || '';
    var errorMsg = root.getAttribute('data-error') || '';
    var toggle   = root.querySelector('[data-aw-toggle]');
    var panel    = root.querySelector('[data-aw-panel]');
    var closeBtn = root.querySelector('[data-aw-close]');
    var messages = root.querySelector('[data-aw-messages]');
    var form     = root.querySelector('[data-aw-form]');
    var input    = root.querySelector('[data-aw-input]');
    var busy     = false;

    function setOpen(open) {
        if (open) removeBubble(true);
        panel.hidden = !open;
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        if (open && input) input.focus();
    }

    /* ------------------- Karşılama balonu -------------------
       Panel varsayılan KAPALI başlar (hero'yu örtmez). Sayfa açılışından
       birkaç saniye sonra FAB'ın yanında küçük bir karşılama balonu çıkar;
       tıklanınca panel açılır. Oturum başına bir kez gösterilir. Metin,
       paneldeki mevcut karşılama mesajından alınır (ek çeviri gerekmez). */
    var bubble = null;

    function removeBubble(instant) {
        if (!bubble) return;
        var el = bubble;
        bubble = null;
        if (instant) {
            el.remove();
            return;
        }
        el.classList.add('is-leaving');
        window.setTimeout(function () { el.remove(); }, 260);
    }

    function showBubble() {
        if (!panel.hidden || bubble) return;
        var first = messages ? messages.querySelector('.aw__msg--bot') : null;
        var text = first ? first.textContent.trim() : '';
        if (!text) return;
        bubble = document.createElement('button');
        bubble.type = 'button';
        bubble.className = 'aw__bubble';
        /* Metin iç span'de: satır sınırı (line-clamp) padding'e taşmadan uygulanır */
        var span = document.createElement('span');
        span.className = 'aw__bubble-text';
        span.textContent = text;
        bubble.appendChild(span);
        root.insertBefore(bubble, toggle);
        bubble.addEventListener('click', function () { setOpen(true); });
        window.setTimeout(function () { removeBubble(false); }, 12000);
    }

    try {
        if (!sessionStorage.getItem('barlas-aw-hello')) {
            sessionStorage.setItem('barlas-aw-hello', '1');
            window.setTimeout(showBubble, 3500);
        }
    } catch (e) { /* gizli mod: balonu atla */ }

    toggle.addEventListener('click', function () { setOpen(panel.hidden); });
    closeBtn.addEventListener('click', function () { setOpen(false); toggle.focus(); });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !panel.hidden) { setOpen(false); toggle.focus(); }
    });

    function addMsg(text, who) {
        var p = document.createElement('p');
        p.className = 'aw__msg aw__msg--' + who;
        p.textContent = text;
        messages.appendChild(p);
        messages.scrollTop = messages.scrollHeight;
        return p;
    }

    function addLinks(items) {
        if (!items || !items.length) return;
        var box = document.createElement('div');
        box.className = 'aw__links';
        items.slice(0, 3).forEach(function (item) {
            if (!item || !item.url || !item.name) return;
            var a = document.createElement('a');
            a.className = 'aw__link';
            a.href = item.url;
            a.textContent = item.name;
            box.appendChild(a);
        });
        if (box.children.length) {
            messages.appendChild(box);
            messages.scrollTop = messages.scrollHeight;
        }
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var text = (input.value || '').trim();
        if (!text || busy || !endpoint) return;

        busy = true;
        addMsg(text, 'user');
        input.value = '';

        var typing = addMsg('…', 'bot');
        typing.classList.add('aw__msg--typing');

        var hp = form.querySelector('.aw__hp');

        fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ q: text, website: hp ? hp.value : '' })
        })
            .then(function (r) { return r.json().catch(function () { return null; }); })
            .then(function (data) {
                typing.remove();
                if (data && typeof data.answer === 'string' && data.answer !== '') {
                    addMsg(data.answer, 'bot');
                    addLinks(data.products);
                    addLinks(data.services);
                } else if (!(data && data.ok)) {
                    addMsg(errorMsg, 'bot');
                }
            })
            .catch(function () {
                typing.remove();
                addMsg(errorMsg, 'bot');
            })
            .then(function () {
                busy = false;
                input.focus();
            });
    });
})();
