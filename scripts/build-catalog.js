// Barlas Trailer product catalog generator
// Reads app/Data/products.json + product images, emits catalog.html (A4 print-ready).
//
// Usage:
//   node scripts/build-catalog.js [outDir]     (outDir defaults to os tmpdir)
// Then print to PDF with headless Chrome:
//   chrome --headless=new --disable-gpu --no-pdf-header-footer --virtual-time-budget=30000 \
//     --print-to-pdf="<outDir>/catalog.pdf" "file:///<outDir>/catalog.html"
// Published copy lives at public/downloads/barlas-urun-katalogu-2026.pdf
const fs = require('fs');
const path = require('path');
const os = require('os');

const ROOT = path.resolve(__dirname, '..').replace(/\\/g, '/');
const IMG = ROOT + '/public/assets/images/products';
const LOGO = 'file:///' + ROOT + '/public/assets/images/logo.png';
const OUT = path.join(process.argv[2] || os.tmpdir(), 'catalog.html');

const data = JSON.parse(fs.readFileSync(ROOT + '/app/Data/products.json', 'utf8'));

const CAT_DESC = {
  tankers: 'Akaryakıttan gıdaya, kimyasaldan LPG’ye: alüminyum, çelik ve paslanmaz gövdeli, ADR uyumlu tanker ailesi.',
  silos: 'Toz ve granül yükler için damperli, V ve W tipi, vakumlu silobas çözümleri.',
  tippers: 'Hafriyattan kaya tipine; inşaat, madencilik ve tarım için yüksek mukavemetli damper gövdeleri.',
  curtain: 'Hızlı yükleme-boşaltma için perdeli, tenteli ve swap body treyler ailesi.',
  reefer: 'Soğuk zincirin her halkası için ısı yalıtımlı frigorifik kasalar.',
  'dry-cargo': 'Genel kargo ve sanayi yükleri için sac kasa, kapaklı ve rulo taşıyıcı çözümler.',
  containers: 'Sabit, teleskopik, damperli ve liman içi konteyner şasileri.',
  lowbed: 'İş makineleri ve proje yükleri için sabit, uzar ve hidrolik dümenlenir low-bed’ler.',
  platform: 'Uzun ve geniş yükler için rulo taşıyıcı ve standart flatbed platformlar.',
  trailers: 'Tarım ve sanayi taşımacılığı için akslı ve dingilli römork çözümleri.',
  special: 'Arazözden oto taşıyıcıya: göreve özel mühendislik isteyen üstyapılar.',
};

const esc = s => String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
const norm = s => String(s).toLocaleLowerCase('tr').replace(/[^a-zçğıöşü0-9]+/g, '');

// numeric sort of "11.2" style product numbers
const sortNo = (a, b) => {
  const [a1, a2] = a.no.split('.').map(Number);
  const [b1, b2] = b.no.split('.').map(Number);
  return a1 - b1 || a2 - b2;
};

const imgUrl = (cat, p) => 'file:///' + IMG + '/' + cat.slug + '-' + p.slug + '.jpg';

// ---- spec rendering -------------------------------------------------------
// spec item types: g = group heading, p = paragraph (mostly plain text,
// sometimes a "Label:" line), li = bullet
function renderSpecs(product, variant) {
  const pn = norm(product.name), vn = norm(variant.model || '');
  const groups = [];
  let cur = null;
  let lastText = '';
  for (const s of variant.specs || []) {
    const x = String(s.x || '').trim();
    if (!x) continue;
    const nx = norm(x);
    if (nx === lastText) continue; // consecutive duplicate
    lastText = nx;
    if (s.t === 'g') {
      // skip headings that just repeat the product/variant name
      if (nx === pn || nx === vn) continue;
      if (cur && cur.items.length === 0) { cur.title = x; continue; } // merge empty group
      cur = { title: x, items: [] };
      groups.push(cur);
      continue;
    }
    if (!cur) { cur = { title: '', items: [] }; groups.push(cur); }
    if (s.t === 'p' && x.endsWith(':')) cur.items.push({ k: 'label', x });
    else cur.items.push({ k: 'li', x });
  }
  const gs = groups.filter(g => g.items.length);
  if (!gs.length) return '';
  return '<div class="specs">' + gs.map(g => {
    const small = g.items.length <= 8 ? ' sg--keep' : '';
    return '<div class="sg' + small + '">'
      + (g.title ? '<h4>' + esc(g.title) + '</h4>' : '')
      + '<ul>' + g.items.map(i =>
          i.k === 'label'
            ? '<li class="lbl">' + esc(i.x.replace(/:$/, '')) + '</li>'
            : '<li>' + esc(i.x) + '</li>'
        ).join('') + '</ul>'
      + '</div>';
  }).join('') + '</div>';
}

// ---- product pages --------------------------------------------------------
function productSection(cat, catIdx, p) {
  const variants = p.variants || [];
  const primary = variants[0] || { specs: [] };
  const multi = variants.length > 1;
  const chips = multi
    ? '<div class="models"><span class="models__t">Modeller (' + variants.length + ')</span>'
      + variants.map(v => '<span class="chip">' + esc(v.model) + '</span>').join('')
      + '</div>'
      + '<p class="models__note">Aşağıdaki teknik özellikler <strong>' + esc(primary.model) + '</strong> modeline aittir; diğer modeller için teklif isteyiniz.</p>'
    : '';
  return `
<section class="product">
  <div class="run">BARLAS TRAILER — ÜRÜN KATALOĞU</div>
  <header class="p-head">
    <div>
      <div class="eyebrow">${String(catIdx).padStart(2, '0')} · ${esc(cat.name)}</div>
      <h2>${esc(p.name)}</h2>
    </div>
    <div class="p-no">${esc(p.no)}</div>
  </header>
  <figure class="p-img"><img src="${imgUrl(cat, p)}" alt=""></figure>
  ${chips}
  ${renderSpecs(p, primary)}
</section>`;
}

// ---- divider pages --------------------------------------------------------
function dividerSection(cat, idx) {
  const prods = [...cat.products].sort(sortNo);
  const three = prods.slice(0, 3);
  return `
<section class="sheet divider">
  <div class="divider__head">
    <div class="divider__no">${String(idx).padStart(2, '0')}</div>
    <div>
      <h2>${esc(cat.name)}</h2>
      <p class="divider__desc">${esc(CAT_DESC[cat.slug] || '')}</p>
      <p class="divider__meta">${prods.length} ürün · ${prods.reduce((n, p) => n + (p.variants || []).length, 0)} model</p>
    </div>
  </div>
  <div class="divider__imgs">
    ${three.map(p => '<img src="' + imgUrl(cat, p) + '" alt="">').join('')}
  </div>
  <ul class="divider__list">
    ${prods.map(p => '<li><span>' + esc(p.no) + '</span>' + esc(p.name) + '</li>').join('')}
  </ul>
</section>`;
}

// ---- assemble -------------------------------------------------------------
const cats = data.categories;
const totP = cats.reduce((n, c) => n + c.products.length, 0);
const totV = cats.reduce((n, c) => n + c.products.reduce((m, p) => m + (p.variants || []).length, 0), 0);

const toc = `
<section class="toc">
  <div class="toc__head"><div class="eyebrow">BARLAS TRAILER · 2026</div><h2>İçindekiler</h2></div>
  <div class="toc__grid">
    ${cats.map((c, i) => `
    <div class="toc__cat">
      <h3><b>${String(i + 1).padStart(2, '0')}</b> ${esc(c.name)}</h3>
      <ul>${[...c.products].sort(sortNo).map(p => '<li><span>' + esc(p.no) + '</span>' + esc(p.name) + '</li>').join('')}</ul>
    </div>`).join('')}
  </div>
</section>`;

const cover = `
<section class="sheet cover">
  <div class="cover__top">
    <div class="logochip"><img src="${LOGO}" alt="Barlas Trailer"></div>
    <div class="cover__year">2026</div>
  </div>
  <div class="cover__title">
    <div class="eyebrow eyebrow--light">Treyler · Tanker · Özel Üstyapı</div>
    <h1>ÜRÜN<br>KATALOĞU</h1>
    <p>ADR uyumlu tankerlerden silobaslara, damperlerden low-bed ve özel üstyapılara — mühendislik odaklı üretimin tam envanteri.</p>
  </div>
  <div class="cover__img"><img src="file:///${IMG}/tankers-akaryakit-yakit-tankeri.jpg" alt=""></div>
  <div class="cover__stats">
    <div><b>${cats.length}</b><span>Kategori</span></div>
    <div><b>${totP}</b><span>Ürün</span></div>
    <div><b>${totV}</b><span>Model</span></div>
    <div><b>40+</b><span>İhracat Ülkesi</span></div>
  </div>
</section>`;

const intro = `
<section class="sheet intro">
  <div class="eyebrow">Hakkımızda</div>
  <h2>Endüstriyel taşımanın her alanında,<br>mühendislik odaklı üretim.</h2>
  <p class="intro__lead">Modern üretim tesisimizde ADR ve uluslararası güvenlik standartlarında tanker, silobas, lowbed ve özel üstyapılar tasarlıyor ve imal ediyoruz. Her araç, mühendislik hassasiyeti ve uzun ömür için üretilir.</p>
  <div class="intro__badges">
    <span>ADR Sertifikalı Üretim</span><span>ISO 9001 Kalite</span><span>TSE Belgeli</span><span>1988’den Beri Üretim</span><span>Tam Garanti &amp; Servis</span>
  </div>
  <div class="intro__how">
    <h3>Bu katalog nasıl okunur?</h3>
    <ul>
      <li>Ürünler <b>${cats.length} kategori</b> altında, <b>1.1’den ${cats.length}.x’e</b> numaralandırılmıştır; içindekiler bölümünden numarayla ulaşabilirsiniz.</li>
      <li>Her ürün sayfasında görsel, model seçenekleri ve temel modelin teknik özellikleri yer alır.</li>
      <li>Birden fazla modeli olan ürünlerde özellikler <b>temel model</b> üzerinden verilmiştir; diğer modellerin detayları için satış ekibimizle görüşünüz.</li>
      <li>Tüm üretim, sipariş konfigürasyonuna göre uyarlanır — ölçü ve donanımlar ihtiyacınıza göre revize edilebilir.</li>
    </ul>
  </div>
  <div class="intro__contact">
    <div><b>Telefon</b>0 552 500 20 00</div>
    <div><b>E-posta</b>info@barlastrailer.com</div>
    <div><b>Adres</b>Fevziçakmak Mah. Şehit Hamdi Karagöz Cad. No: 4s Karatay / Konya</div>
  </div>
</section>`;

const back = `
<section class="sheet back">
  <div class="logochip logochip--lg"><img src="${LOGO}" alt="Barlas Trailer"></div>
  <h2>Yük ne olursa olsun,<br>taşıyacak aracı üretiriz.</h2>
  <div class="back__grid">
    <div><b>Telefon</b><span>0 552 500 20 00</span></div>
    <div><b>E-posta</b><span>info@barlastrailer.com</span></div>
    <div><b>Web</b><span>www.barlastrailer.com</span></div>
    <div><b>Üretim Tesisi &amp; Showroom</b><span>Fevziçakmak Mah. Şehit Hamdi Karagöz Cad. No: 4s Karatay / Konya</span></div>
  </div>
  <p class="back__note">Bu katalogdaki tüm teknik bilgiler bilgilendirme amaçlıdır; üretim, sipariş anındaki konfigürasyona göre yapılır. © 2026 Barlas Trailer</p>
</section>`;

let body = cover + intro + toc;
cats.forEach((c, i) => {
  body += dividerSection(c, i + 1);
  for (const p of [...c.products].sort(sortNo)) body += productSection(c, i + 1, p);
});
body += back;

const css = `
:root{
  --navy:#0B1322; --navy2:#101A2E; --band:#181849;
  --blue:#005BAA; --blue-b:#4AA3E6; --sky:#8FD0FF;
  --ink:#1B2430; --mut:#5A6675; --line:#DDE4EC; --wash:#F2F6FA;
}
*{margin:0;padding:0;box-sizing:border-box;-webkit-print-color-adjust:exact;print-color-adjust:exact}
html{font-size:10pt}
body{font-family:'Segoe UI',Arial,sans-serif;color:var(--ink)}
h1,h2,h3,h4,.p-no,.divider__no,.cover__year{font-family:Bahnschrift,'Segoe UI',Arial,sans-serif}

@page{size:A4;margin:14mm 12mm 15mm}
@page full{size:A4;margin:0}
.sheet{page:full;width:210mm;height:297mm;overflow:hidden;break-after:page;position:relative}

.eyebrow{font-size:8pt;letter-spacing:.22em;text-transform:uppercase;color:var(--blue);font-weight:600}
.eyebrow--light{color:var(--sky)}

/* ---------- cover ---------- */
.cover{background:linear-gradient(160deg,#0B1322 0%,#101A2E 45%,#181849 100%);color:#F0F5FC;padding:16mm 16mm 0;display:flex;flex-direction:column}
.cover__top{display:flex;justify-content:space-between;align-items:center}
.logochip{background:#fff;border-radius:3mm;padding:3mm 5mm;display:inline-block}
.logochip img{height:11mm;display:block}
.cover__year{font-size:16pt;color:var(--sky);letter-spacing:.18em}
.cover__title{margin-top:16mm}
.cover__title h1{font-size:46pt;line-height:1.02;letter-spacing:.02em;margin:4mm 0}
.cover__title p{max-width:120mm;color:rgba(226,236,248,.75);font-size:10.5pt;line-height:1.55}
.cover__img{margin-top:10mm;border-radius:4mm;overflow:hidden;border:.6mm solid rgba(143,208,255,.25)}
.cover__img img{width:100%;height:118mm;object-fit:cover;display:block}
.cover__stats{margin-top:auto;display:flex;border-top:.3mm solid rgba(143,208,255,.25);padding:7mm 0 9mm}
.cover__stats div{flex:1;text-align:center}
.cover__stats b{display:block;font-family:Bahnschrift;font-size:20pt;color:var(--sky)}
.cover__stats span{font-size:8pt;letter-spacing:.14em;text-transform:uppercase;color:rgba(226,236,248,.6)}

/* ---------- intro ---------- */
.intro{padding:18mm 18mm;background:#fff}
.intro h2{font-size:22pt;line-height:1.15;margin:4mm 0 6mm}
.intro__lead{font-size:11pt;color:var(--mut);line-height:1.6;max-width:150mm}
.intro__badges{display:flex;flex-wrap:wrap;gap:2.5mm;margin:8mm 0}
.intro__badges span{border:.3mm solid var(--blue);color:var(--blue);border-radius:10mm;padding:1.6mm 4mm;font-size:8.5pt;font-weight:600}
.intro__how{background:var(--wash);border-radius:4mm;padding:7mm 8mm;margin-top:4mm}
.intro__how h3{font-size:12pt;margin-bottom:3mm;color:var(--navy)}
.intro__how ul{list-style:none}
.intro__how li{position:relative;padding-left:6mm;margin:2.2mm 0;line-height:1.5;color:#37414d}
.intro__how li::before{content:'';position:absolute;left:0;top:2.2mm;width:2.6mm;height:2.6mm;border-radius:50%;background:var(--blue-b)}
.intro__contact{display:flex;gap:6mm;margin-top:10mm;border-top:.3mm solid var(--line);padding-top:6mm}
.intro__contact div{flex:1;font-size:9pt;color:var(--mut);line-height:1.5}
.intro__contact b{display:block;color:var(--navy);font-size:8pt;letter-spacing:.14em;text-transform:uppercase;margin-bottom:1mm}

/* ---------- toc (flows across pages) ---------- */
.toc{break-before:page;break-after:page;background:#fff}
.toc__head h2{font-size:24pt;margin:2mm 0 8mm}
.toc__grid{column-count:2;column-gap:10mm}
.toc__cat{break-inside:avoid;margin-bottom:6mm}
.toc__cat h3{font-size:11pt;color:var(--navy);border-bottom:.4mm solid var(--blue);padding-bottom:1.4mm;margin-bottom:2mm}
.toc__cat h3 b{color:var(--blue);margin-right:2mm}
.toc__cat ul{list-style:none}
.toc__cat li{display:flex;gap:3mm;font-size:8.6pt;padding:.9mm 0;color:#37414d;border-bottom:.2mm dotted var(--line)}
.toc__cat li span{color:var(--blue);font-weight:600;min-width:8mm}

/* ---------- divider ---------- */
.divider{background:linear-gradient(150deg,#0B1322,#181849);color:#F0F5FC;padding:16mm 16mm}
.divider__head{display:flex;gap:8mm;align-items:flex-start}
.divider__no{font-size:52pt;color:transparent;-webkit-text-stroke:.5mm var(--sky);line-height:.9}
.divider__head h2{font-size:26pt;margin-bottom:2.5mm}
.divider__desc{color:rgba(226,236,248,.75);max-width:120mm;line-height:1.55;font-size:10.5pt}
.divider__meta{margin-top:2.5mm;color:var(--sky);font-size:8.5pt;letter-spacing:.16em;text-transform:uppercase}
.divider__imgs{display:flex;gap:4mm;margin:10mm 0}
.divider__imgs img{flex:1;min-width:0;height:44mm;object-fit:cover;border-radius:3mm;border:.4mm solid rgba(143,208,255,.25)}
.divider__list{list-style:none;column-count:2;column-gap:8mm;margin-top:2mm}
.divider__list li{break-inside:avoid;display:flex;gap:3mm;padding:2mm 0;border-bottom:.2mm solid rgba(143,208,255,.16);font-size:9.5pt;color:rgba(226,236,248,.88)}
.divider__list li span{color:var(--sky);font-weight:600;min-width:9mm}

/* ---------- product ---------- */
.product{break-before:page}
.run{font-size:6.8pt;letter-spacing:.24em;color:#9AA7B5;text-transform:uppercase;margin-bottom:3mm}
.p-head{display:flex;justify-content:space-between;align-items:flex-start;gap:6mm;border-bottom:.5mm solid var(--navy);padding-bottom:3mm}
.p-head h2{font-size:17pt;color:var(--navy);margin-top:1mm}
.p-no{background:var(--blue);color:#fff;border-radius:2.2mm;padding:1.8mm 3.4mm;font-size:12pt;font-weight:700}
.p-img{margin:4mm 0}
.p-img img{width:100%;height:64mm;object-fit:cover;border-radius:3mm;display:block}
.models{display:flex;flex-wrap:wrap;gap:2mm;align-items:center;margin:1mm 0 1.5mm}
.models__t{font-size:8pt;letter-spacing:.16em;text-transform:uppercase;color:var(--blue);font-weight:700;margin-right:1mm}
.chip{border:.3mm solid var(--line);background:var(--wash);border-radius:8mm;padding:1.2mm 3.2mm;font-size:8.2pt;color:#37414d}
.models__note{font-size:8.2pt;color:var(--mut);margin-bottom:2mm}
.specs{column-count:2;column-gap:8mm;margin-top:2.5mm}
.sg{margin-bottom:4mm}
.sg--keep{break-inside:avoid}
.sg h4{font-size:9.5pt;color:var(--blue);text-transform:uppercase;letter-spacing:.08em;border-bottom:.25mm solid var(--line);padding-bottom:1mm;margin-bottom:1.6mm;break-after:avoid}
.sg ul{list-style:none}
.sg li{position:relative;padding-left:4mm;font-size:8.4pt;line-height:1.45;color:#37414d;margin:1.1mm 0;break-inside:avoid}
.sg li::before{content:'';position:absolute;left:0;top:1.55mm;width:1.7mm;height:1.7mm;background:var(--blue-b);border-radius:.4mm}
.sg li.lbl{padding-left:0;font-weight:700;color:var(--navy);margin-top:2mm}
.sg li.lbl::before{display:none}

/* ---------- back ---------- */
.back{background:linear-gradient(160deg,#0B1322,#181849);color:#F0F5FC;padding:20mm;display:flex;flex-direction:column}
.logochip--lg{align-self:flex-start}
.logochip--lg img{height:13mm}
.back h2{font-size:26pt;line-height:1.15;margin:14mm 0 12mm}
.back__grid{display:grid;grid-template-columns:1fr 1fr;gap:7mm;max-width:160mm}
.back__grid div{border:.3mm solid rgba(143,208,255,.3);border-radius:3mm;padding:5mm 6mm}
.back__grid b{display:block;font-size:8pt;letter-spacing:.16em;text-transform:uppercase;color:var(--sky);margin-bottom:1.6mm}
.back__grid span{font-size:11pt}
.back__note{margin-top:auto;font-size:8pt;color:rgba(226,236,248,.55);line-height:1.5}
`;

const html = `<!doctype html>
<html lang="tr"><head><meta charset="utf-8"><title>Barlas Trailer — Ürün Kataloğu 2026</title>
<style>${css}</style></head><body>${body}</body></html>`;

fs.writeFileSync(OUT, html, 'utf8');
console.log('written', OUT, Math.round(html.length / 1024) + 'KB');
