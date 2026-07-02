/* =====================================================================
   tools.js — client-side logistics calculators (tools page)
   Constants come from the inline JSON injected by the view
   (app/Data/tools-constants.json). No network requests.
   ===================================================================== */
(function () {
    'use strict';

    var constEl = document.querySelector('[data-tools-constants]');
    var C;
    try {
        C = JSON.parse(constEl ? constEl.textContent : '{}');
    } catch (e) {
        C = {};
    }

    var densities = C.densities || {};
    var pallets   = C.pallets || { eur: { length_m: 1.2, width_m: 0.8 }, ind: { length_m: 1.2, width_m: 1 } };
    var presets   = C.presets || { tent: { inner_length_m: 13.62, inner_width_m: 2.48 }, frigo: { inner_length_m: 13.3, inner_width_m: 2.46 } };
    var limits    = C.limits || { tridem_kg: 24000, kingpin_kg: 12000 };
    var defaults  = C.defaults || {};

    var nf0 = new Intl.NumberFormat(document.documentElement.lang || 'tr', { maximumFractionDigits: 0 });
    var nf2 = new Intl.NumberFormat(document.documentElement.lang || 'tr', { maximumFractionDigits: 2 });

    function num(form, name) {
        var el = form.elements[name];
        if (!el) return NaN;
        var v = parseFloat(String(el.value).replace(',', '.'));
        return isNaN(v) ? NaN : v;
    }

    function show(card, values, rows) {
        var box = card.querySelector('[data-results]');
        if (!box) return;
        Object.keys(values).forEach(function (key) {
            var dd = box.querySelector('[data-r="' + key + '"]');
            if (dd) dd.textContent = values[key];
        });
        (rows || []).forEach(function (row) {
            var el = box.querySelector('[data-row="' + row.key + '"]');
            if (el) el.hidden = !row.visible;
        });
        box.hidden = false;
    }

    /* ---------- C1: fuel & freight cost ---------- */
    function initFuel(card) {
        var form = card.querySelector('[data-tool-form]');
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var distance = num(form, 'distance');
            if (!(distance > 0)) { form.elements.distance.focus(); return; }

            var consumption = num(form, 'consumption');
            var price       = num(form, 'fuel_price');
            var toll        = num(form, 'toll');
            var cargo       = num(form, 'cargo');
            if (!(consumption > 0)) consumption = defaults.consumption_l100 || 32;
            if (!(price > 0)) price = defaults.fuel_price_tl || 50;
            if (!(toll >= 0)) toll = 0;

            var factor   = form.elements.round_trip.checked ? 2 : 1;
            var km       = distance * factor;
            var liters   = km * consumption / 100;
            var fuelCost = liters * price;
            var total    = fuelCost + toll * factor; // geçiş ücreti her yönde ödenir

            show(card, {
                liters:    nf0.format(liters),
                fuel_cost: nf0.format(fuelCost),
                total:     nf0.format(total),
                per_km:    nf2.format(total / km),
                per_ton:   cargo > 0 ? nf0.format(total / cargo) : '–'
            }, [{ key: 'per_ton', visible: cargo > 0 }]);
        });
    }

    /* ---------- C2: pallet fit ---------- */
    function bestPalletLayout(L, W, p) {
        var long  = p.length_m; // 1.2
        var short = p.width_m;  // 0.8 / 1.0
        var acrossLong  = Math.floor(W / long);  // palet uzun kenarı enine
        var acrossShort = Math.floor(W / short); // palet kısa kenarı enine
        var best = { count: 0, text: '–' };

        // Karışık yerleşim: önce "uzun kenar enine" sıralar (derinlik=short),
        // kalan boyda "kısa kenar enine" sıralar (derinlik=long). Her iki uçtan
        // saf düzenler de bu döngünün k=0 / k=max uçlarıdır.
        var maxShortRows = Math.floor(L / short);
        for (var k = 0; k <= maxShortRows; k++) {
            var used = k * short;
            var longRows = Math.floor((L - used) / long);
            var count = k * acrossLong + longRows * acrossShort;
            if (count > best.count) {
                var parts = [];
                if (k > 0) parts.push(k + ' × ' + acrossLong);
                if (longRows > 0) parts.push(longRows + ' × ' + acrossShort);
                best = { count: count, text: parts.join(' + ') || '–' };
            }
        }
        return best;
    }

    function initPallet(card) {
        var form   = card.querySelector('[data-tool-form]');
        var preset = form.elements.preset;

        function applyPreset() {
            var p = presets[preset.value];
            var custom = !p;
            form.elements.length.readOnly = !custom;
            form.elements.width.readOnly  = !custom;
            if (p) {
                form.elements.length.value = p.inner_length_m;
                form.elements.width.value  = p.inner_width_m;
            }
        }
        preset.addEventListener('change', applyPreset);
        applyPreset();

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var L = num(form, 'length');
            var W = num(form, 'width');
            if (!(L > 0) || !(W > 0)) return;

            var best = bestPalletLayout(L, W, pallets[form.elements.pallet.value] || pallets.eur);
            show(card, { count: nf0.format(best.count), layout: best.text });
        });

        form.addEventListener('reset', function () {
            window.setTimeout(applyPreset, 0);
        });
    }

    /* ---------- C3: volume ↔ tonnage ---------- */
    function initVolume(card) {
        var form = card.querySelector('[data-tool-form]');

        function sync() {
            var v2w = form.elements.direction.value === 'v2w';
            card.querySelector('[data-field="volume"]').hidden = !v2w;
            card.querySelector('[data-field="weight"]').hidden = v2w;
            card.querySelector('[data-field="density"]').hidden = form.elements.product.value !== 'custom';
        }
        form.elements.direction.addEventListener('change', sync);
        form.elements.product.addEventListener('change', sync);
        sync();

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var product = form.elements.product.value;
            var density = product === 'custom' ? num(form, 'density') : densities[product];
            if (!(density > 0)) return;

            var v2w = form.elements.direction.value === 'v2w';
            if (v2w) {
                var vol = num(form, 'volume');
                if (!(vol > 0)) return;
                show(card, { weight: nf2.format(vol * density / 1000) },
                    [{ key: 'weight', visible: true }, { key: 'volume', visible: false }]);
            } else {
                var w = num(form, 'weight');
                if (!(w > 0)) return;
                show(card, { volume: nf2.format(w * 1000 / density) },
                    [{ key: 'weight', visible: false }, { key: 'volume', visible: true }]);
            }
        });

        form.addEventListener('reset', function () {
            window.setTimeout(sync, 0);
        });
    }

    /* ---------- C4: axle load (simple moment model) ---------- */
    function initAxle(card) {
        var form = card.querySelector('[data-tool-form]');
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var empty   = num(form, 'empty');
            var payload = num(form, 'payload');
            var L       = num(form, 'wheelbase');
            var x       = num(form, 'load_center');
            if (!(empty > 0)) empty = defaults.trailer_empty_kg || 6500;
            if (!(L > 0)) L = defaults.kingpin_to_axle_m || 7.7;
            if (!(payload >= 0)) { form.elements.payload.focus(); return; }
            x = Math.min(Math.max(x || 0, 0), L); // ağırlık merkezi fiziksel aralığa sıkıştırılır

            var share   = typeof defaults.empty_axle_share === 'number' ? defaults.empty_axle_share : 0.5;
            var axles   = empty * share + payload * (x / L);
            var kingpin = (empty + payload) - axles;

            show(card, { kingpin: nf0.format(kingpin), axles: nf0.format(axles) });

            var status = card.querySelector('[data-status]');
            if (status) {
                var msg = card.getAttribute('data-msg-ok');
                var bad = false;
                if (axles > (limits.tridem_kg || 24000)) { msg = card.getAttribute('data-msg-warn-axles'); bad = true; }
                else if (kingpin > (limits.kingpin_kg || 12000)) { msg = card.getAttribute('data-msg-warn-kingpin'); bad = true; }
                status.textContent = msg || '';
                status.classList.toggle('is-warn', bad);
                status.classList.toggle('is-ok', !bad);
            }
        });
    }

    var inits = { fuel: initFuel, pallet: initPallet, volume: initVolume, axle: initAxle };

    document.querySelectorAll('[data-tool]').forEach(function (card) {
        var init = inits[card.getAttribute('data-tool')];
        if (init) init(card);

        var form = card.querySelector('[data-tool-form]');
        if (form) {
            form.addEventListener('reset', function () {
                var box = card.querySelector('[data-results]');
                if (box) box.hidden = true;
            });
        }
    });
})();
