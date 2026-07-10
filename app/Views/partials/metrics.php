<?php

/**
 * Ölçüm beacon'ı — birinci taraf, çerezsiz (KVKK dostu; ayrıntı:
 * App\Controllers\Metrics). Sayfa başına tek POST; Panel > Analitik
 * bu akıştan beslenir. Ölçüm hiçbir koşulda sayfayı bozmamalıdır,
 * bu yüzden tamamı try/catch içindedir ve yanıt beklenmez.
 */
?>
<script>
(function () {
    try {
        var r = document.referrer || '';
        if (r.indexOf('//' + location.host) !== -1) { r = ''; }
        var url = '<?= base_url('metrics/hit') ?>';
        var body = JSON.stringify({ p: location.pathname, r: r });
        if (!(navigator.sendBeacon && navigator.sendBeacon(url, new Blob([body], { type: 'application/json' })))) {
            fetch(url, { method: 'POST', body: body, keepalive: true, headers: { 'Content-Type': 'application/json' } });
        }
    } catch (e) { /* ölçüm sessiz kalır */ }
})();
</script>
