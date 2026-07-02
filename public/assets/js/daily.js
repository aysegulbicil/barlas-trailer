/* =====================================================================
   daily.js — Günün Sorusu (Bekleme Salonu §8)
   Soru, yerel tarihe göre bankadan deterministik seçilir (aynı gün
   herkes aynı soruyu görür). Cevap + gün serisi localStorage'da
   ('barlas-daily'); ertesi gün yeni soru açılır.
   ===================================================================== */
(function () {
    'use strict';

    var root = document.querySelector('[data-daily]');
    if (!root) return;

    var bank;
    try {
        bank = JSON.parse(root.querySelector('[data-daily-bank]').textContent);
    } catch (e) {
        return;
    }
    if (!Array.isArray(bank) || bank.length === 0) return;

    var elDate    = root.querySelector('[data-daily-date]');
    var elStreak  = root.querySelector('[data-daily-streak]');
    var elQ       = root.querySelector('[data-daily-q]');
    var elOpts    = root.querySelector('[data-daily-opts]');
    var elResult  = root.querySelector('[data-daily-result]');
    var elVerdict = root.querySelector('[data-daily-verdict]');
    var elWhy     = root.querySelector('[data-daily-why]');

    var KEY = 'barlas-daily';

    var now      = new Date();
    var todayKey = now.getFullYear() + '-'
        + String(now.getMonth() + 1).padStart(2, '0') + '-'
        + String(now.getDate()).padStart(2, '0');

    var dayNumber = Math.floor((now.getTime() - now.getTimezoneOffset() * 60000) / 86400000);
    var item      = bank[((dayNumber % bank.length) + bank.length) % bank.length];

    var saved;
    try {
        saved = JSON.parse(localStorage.getItem(KEY) || 'null');
    } catch (e) {
        saved = null;
    }
    if (!saved || typeof saved !== 'object') saved = { date: '', streak: 0, picked: -1, ok: false };

    elDate.textContent = new Intl.DateTimeFormat(document.documentElement.lang || 'tr', {
        day: 'numeric', month: 'long', year: 'numeric'
    }).format(now);

    elQ.textContent = item.q;

    function yesterdayKey() {
        var d = new Date(now.getTime() - 86400000);
        return d.getFullYear() + '-'
            + String(d.getMonth() + 1).padStart(2, '0') + '-'
            + String(d.getDate()).padStart(2, '0');
    }

    function renderStreak() {
        elStreak.textContent = String(saved.streak || 0);
    }

    function showResult(pickedIdx) {
        var correct = pickedIdx === item.c;
        elVerdict.textContent = root.getAttribute(correct ? 'data-str-correct' : 'data-str-wrong');
        elVerdict.classList.toggle('is-ok', correct);
        elVerdict.classList.toggle('is-no', !correct);
        elWhy.textContent = item.why || '';
        elResult.hidden = false;
    }

    function lockOptions(pickedIdx) {
        elOpts.querySelectorAll('button').forEach(function (btn, i) {
            btn.disabled = true;
            if (i === item.c) btn.classList.add('is-correct');
            if (i === pickedIdx && pickedIdx !== item.c) btn.classList.add('is-wrong');
        });
    }

    function answer(idx) {
        var correct = idx === item.c;

        // Seri: dün de oynandıysa devam eder; doğru cevap seriyi büyütür.
        var base = (saved.date === yesterdayKey()) ? (saved.streak || 0) : 0;
        saved = {
            date:   todayKey,
            streak: correct ? base + 1 : 0,
            picked: idx,
            ok:     correct
        };
        try {
            localStorage.setItem(KEY, JSON.stringify(saved));
        } catch (e) { /* gizli mod vb. — oyun yine oynanır */ }

        renderStreak();
        lockOptions(idx);
        showResult(idx);
    }

    item.o.forEach(function (label, i) {
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'daily__opt';
        btn.textContent = label;
        btn.addEventListener('click', function () { answer(i); });
        elOpts.appendChild(btn);
    });

    renderStreak();

    // Bugün zaten yanıtlandıysa: kilitli görünüm + sonuç.
    if (saved.date === todayKey && saved.picked >= 0) {
        var note = root.getAttribute('data-str-answered');
        lockOptions(saved.picked);
        showResult(saved.picked);
        if (note) elVerdict.textContent = note + ' — ' + elVerdict.textContent;
    }
})();
