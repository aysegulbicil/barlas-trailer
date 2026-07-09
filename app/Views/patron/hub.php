<?php

/**
 * İç Araçlar hub'ı (rota sürümü) — üç kartlı seçim ekranı. Görsel dil,
 * apps/_shared/auth/public/index.php ile birebir aynıdır; yalnız kart/çıkış
 * bağlantıları CI4 rotalarına (site_url) uyarlanmıştır.
 *
 * @var string $user
 */
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Barlas · İç Araçlar</title>
<style>
  :root { color-scheme: dark; }
  * { box-sizing: border-box; margin: 0; padding: 0; }

  body {
    min-height: 100vh; display: flex; flex-direction: column;
    font: 15px/1.6 system-ui, -apple-system, "Segoe UI", sans-serif;
    background: radial-gradient(1200px 600px at 20% -10%, #1b2559 0%, #0b1026 55%, #070b1c 100%);
    color: #e6e9f5; overflow-x: hidden;
  }

  /* Süzülen ışık blob'ları + nokta dokusu */
  .bg { position: fixed; inset: 0; pointer-events: none; z-index: 0; }
  .bg::after {
    content: ""; position: absolute; inset: 0;
    background-image: radial-gradient(rgba(148, 163, 216, .10) 1px, transparent 1.4px);
    background-size: 28px 28px;
    mask-image: radial-gradient(ellipse 90% 70% at 50% 30%, #000 0%, transparent 75%);
  }
  .blob { position: absolute; border-radius: 50%; filter: blur(90px); opacity: .35; }
  .blob-1 { width: 480px; height: 480px; left: -140px; top: -120px; background: #2543a8; animation: drift-1 26s ease-in-out infinite alternate; }
  .blob-2 { width: 420px; height: 420px; right: -120px; top: 30%; background: #0e7490; animation: drift-2 32s ease-in-out infinite alternate; }
  .blob-3 { width: 380px; height: 380px; left: 35%; bottom: -180px; background: #6d28d9; opacity: .22; animation: drift-3 38s ease-in-out infinite alternate; }
  @keyframes drift-1 { to { transform: translate(90px, 60px) scale(1.12); } }
  @keyframes drift-2 { to { transform: translate(-70px, -80px) scale(1.08); } }
  @keyframes drift-3 { to { transform: translate(-60px, -40px) scale(1.15); } }

  header {
    position: relative; z-index: 2;
    display: flex; align-items: center; justify-content: space-between; gap: 16px;
    padding: 22px clamp(20px, 5vw, 48px);
    animation: fade-down .7s cubic-bezier(.22,1,.36,1) both;
  }
  .brand { display: flex; align-items: baseline; gap: 10px; }
  .brand b { font-size: 17px; letter-spacing: .14em; }
  .brand span { font-size: 13px; color: #94a3c8; }
  .who { display: flex; align-items: center; gap: 14px; font-size: 13px; color: #94a3c8; }
  .who a {
    color: #bfdbfe; text-decoration: none; padding: 7px 14px; border-radius: 10px;
    border: 1px solid rgba(120, 140, 220, .3); background: rgba(15, 20, 45, .6);
    transition: background .2s, border-color .2s;
  }
  .who a:hover { background: rgba(37, 99, 235, .25); border-color: rgba(96, 165, 250, .5); }
  .who .mail { max-width: 220px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

  main {
    position: relative; z-index: 1; flex: 1;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    padding: 24px clamp(20px, 5vw, 48px) 64px; perspective: 1400px;
  }
  .intro { text-align: center; margin-bottom: 44px; animation: fade-down .8s .08s cubic-bezier(.22,1,.36,1) both; }
  .intro h1 { font-size: clamp(24px, 3.4vw, 34px); font-weight: 700; letter-spacing: -.01em; }
  .intro h1 em { font-style: normal; background: linear-gradient(100deg, #7dd3fc, #818cf8 55%, #c084fc); -webkit-background-clip: text; background-clip: text; color: transparent; }
  .intro p { color: #94a3c8; margin-top: 8px; font-size: 15px; }

  .deck {
    display: grid; grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: clamp(16px, 2.6vw, 28px); width: 100%; max-width: 1060px;
  }
  @media (max-width: 860px) { .deck { grid-template-columns: 1fr; max-width: 420px; } }

  .card {
    --ac: #60a5fa; --mx: 50%; --my: 40%;
    position: relative; display: block; text-decoration: none; color: inherit;
    border-radius: 22px; outline: none;
    transform-style: preserve-3d; will-change: transform;
    transition: transform .18s ease-out;
    animation: card-in .85s cubic-bezier(.22,1,.36,1) both;
  }
  .card:nth-child(1) { animation-delay: .16s; }
  .card:nth-child(2) { animation-delay: .28s; }
  .card:nth-child(3) { animation-delay: .40s; }

  .card-inner {
    position: relative; overflow: hidden; border-radius: inherit;
    min-height: 280px; padding: 30px 28px 26px;
    display: flex; flex-direction: column; gap: 14px;
    background: linear-gradient(160deg, rgba(21, 28, 60, .92), rgba(11, 16, 38, .94));
    border: 1px solid rgba(120, 140, 220, .22);
    box-shadow: 0 18px 50px -18px rgba(3, 6, 20, .9);
    transition: border-color .25s, box-shadow .25s;
  }
  /* Pointer'ı izleyen spotlight */
  .card-inner::before {
    content: ""; position: absolute; inset: 0; opacity: 0; transition: opacity .3s;
    background: radial-gradient(420px circle at var(--mx) var(--my),
      color-mix(in srgb, var(--ac) 16%, transparent), transparent 65%);
  }
  /* Üst kenarda parıltı çizgisi */
  .card-inner::after {
    content: ""; position: absolute; inset: 0 0 auto 0; height: 1px;
    background: linear-gradient(90deg, transparent, color-mix(in srgb, var(--ac) 70%, transparent), transparent);
    opacity: .45; transition: opacity .3s;
  }
  .card:hover .card-inner, .card:focus-visible .card-inner {
    border-color: color-mix(in srgb, var(--ac) 55%, rgba(120, 140, 220, .25));
    box-shadow: 0 26px 70px -20px color-mix(in srgb, var(--ac) 32%, rgba(3, 6, 20, .95));
  }
  .card:hover .card-inner::before, .card:focus-visible .card-inner::before { opacity: 1; }
  .card:hover .card-inner::after { opacity: .9; }
  .card:focus-visible { box-shadow: 0 0 0 3px color-mix(in srgb, var(--ac) 70%, transparent); }

  .icon-wrap { position: relative; width: 62px; height: 62px; margin-bottom: 4px; transform: translateZ(40px); }
  .icon-halo {
    position: absolute; inset: -10px; border-radius: 20px;
    background: radial-gradient(circle, color-mix(in srgb, var(--ac) 35%, transparent), transparent 70%);
    animation: halo 3.6s ease-in-out infinite;
  }
  @keyframes halo { 50% { opacity: .45; transform: scale(1.18); } }
  .icon {
    position: relative; width: 62px; height: 62px; border-radius: 17px;
    display: grid; place-items: center;
    background: color-mix(in srgb, var(--ac) 14%, rgba(10, 15, 35, .8));
    border: 1px solid color-mix(in srgb, var(--ac) 40%, transparent);
    color: var(--ac); transition: transform .25s cubic-bezier(.34,1.56,.64,1);
    overflow: hidden;
  }
  .card:hover .icon { transform: scale(1.09) rotate(-2deg); }
  .icon svg { width: 32px; height: 32px; }
  /* QR kartında tarama çizgisi */
  .scanline {
    position: absolute; left: 10%; right: 10%; height: 2px; top: 18%;
    background: linear-gradient(90deg, transparent, var(--ac), transparent);
    opacity: 0; border-radius: 2px;
  }
  .card:hover .scanline { opacity: .9; animation: scan 1.4s ease-in-out infinite; }
  @keyframes scan { 0%,100% { top: 18%; } 50% { top: 78%; } }

  .card h2 { font-size: 20px; font-weight: 700; transform: translateZ(30px); }
  .card .desc { color: #9aa6cd; font-size: 14px; line-height: 1.55; flex: 1; transform: translateZ(20px); }
  .go {
    display: inline-flex; align-items: center; gap: 8px;
    font-weight: 600; font-size: 14px; color: var(--ac); transform: translateZ(25px);
  }
  .go svg { width: 17px; height: 17px; transition: transform .25s; }
  .card:hover .go svg { transform: translateX(5px); }

  /* Tıklanınca: seçilen parlar, kalanlar söner */
  .deck.leaving .card { pointer-events: none; transition: transform .3s ease, opacity .3s ease, filter .3s ease; }
  .deck.leaving .card:not(.picked) { opacity: 0; transform: scale(.94) translateY(8px); filter: blur(3px); }
  .deck.leaving .card.picked { transform: scale(1.05); }
  .deck.leaving .card.picked .card-inner { border-color: var(--ac); box-shadow: 0 0 90px -12px var(--ac); }

  @keyframes card-in { from { opacity: 0; transform: translateY(34px) scale(.96); filter: blur(6px); } to { opacity: 1; transform: none; filter: none; } }
  @keyframes fade-down { from { opacity: 0; transform: translateY(-14px); } to { opacity: 1; transform: none; } }

  @media (prefers-reduced-motion: reduce) {
    *, *::before, *::after { animation: none !important; transition: none !important; }
  }
</style>
</head>
<body>
<div class="bg"><i class="blob blob-1"></i><i class="blob blob-2"></i><i class="blob blob-3"></i></div>

<header>
  <div class="brand"><b>BARLAS</b><span>İç Araçlar</span></div>
  <div class="who">
    <span class="mail"><?= esc($user) ?></span>
    <a href="<?= site_url('patron/logout') ?>">Çıkış</a>
  </div>
</header>

<main>
  <div class="intro">
    <h1>Hangi sistemle <em>çalışıyoruz?</em></h1>
    <p>Tek girişle üç sistem. Kartı seç, gerisi hazır.</p>
  </div>

  <nav class="deck" id="deck">
    <a class="card" href="<?= site_url('patron/go-qr') ?>" style="--ac:#2dd4bf">
      <div class="card-inner">
        <div class="icon-wrap"><i class="icon-halo"></i>
          <span class="icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/>
              <rect x="3" y="14" width="7" height="7" rx="1.5"/>
              <path d="M14 14h3v3h-3zM20 14h1M14 20h1M18 18h3v3h-3z"/>
            </svg>
            <i class="scanline"></i>
          </span>
        </div>
        <h2>QR Personel</h2>
        <p class="desc">Yoklama, vardiya, görev ve bordro takibi. Kart seni yönetici oturumuyla otomatik içeri alır.</p>
        <span class="go">Sisteme geç
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </span>
      </div>
    </a>

    <a class="card" href="<?= site_url('teklif') ?>" style="--ac:#60a5fa">
      <div class="card-inner">
        <div class="icon-wrap"><i class="icon-halo"></i>
          <span class="icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/>
              <path d="M9 13h6M9 17h4"/>
            </svg>
          </span>
        </div>
        <h2>Teklif Sistemi</h2>
        <p class="desc">Müşteri tekliflerini hazırla, PDF üret, revizyonları ve arşivi tek yerden yönet.</p>
        <span class="go">Sisteme geç
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </span>
      </div>
    </a>

    <a class="card" href="<?= site_url('fatura') ?>" style="--ac:#fbbf24">
      <div class="card-inner">
        <div class="icon-wrap"><i class="icon-halo"></i>
          <span class="icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <path d="M5 3h14v18l-2.4-1.6L14.2 21l-2.2-1.6L9.8 21l-2.4-1.6L5 21z"/>
              <path d="M9 8h6M9 12h6M13 16h2"/>
            </svg>
          </span>
        </div>
        <h2>Fatura Takip</h2>
        <p class="desc">Gelen–giden faturalar, vade ve ödeme durumu. Kayıtlar tek panelde, arama bir tık uzağında.</p>
        <span class="go">Sisteme geç
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </span>
      </div>
    </a>
  </nav>
</main>

<script>
(function () {
  var deck = document.getElementById('deck');
  var cards = deck.querySelectorAll('.card');
  var fine = matchMedia('(hover: hover) and (pointer: fine)').matches;
  var calm = matchMedia('(prefers-reduced-motion: reduce)').matches;

  cards.forEach(function (card) {
    if (fine && !calm) {
      card.addEventListener('pointermove', function (e) {
        var r = card.getBoundingClientRect();
        var px = (e.clientX - r.left) / r.width;
        var py = (e.clientY - r.top) / r.height;
        card.style.transform = 'rotateY(' + ((px - .5) * 10).toFixed(2) + 'deg) rotateX(' + ((.5 - py) * 8).toFixed(2) + 'deg) translateY(-4px)';
        card.style.setProperty('--mx', (px * 100).toFixed(1) + '%');
        card.style.setProperty('--my', (py * 100).toFixed(1) + '%');
      });
      card.addEventListener('pointerleave', function () { card.style.transform = ''; });
    }

    // Tıklamada kısa uğurlama animasyonu, sonra yönlen (yeni sekme kısayollarına karışma)
    card.addEventListener('click', function (e) {
      if (calm || e.metaKey || e.ctrlKey || e.shiftKey || e.button === 1) return;
      e.preventDefault();
      deck.classList.add('leaving');
      card.classList.add('picked');
      setTimeout(function () { location.href = card.href; }, 280);
    });
  });
})();
</script>
</body>
</html>
