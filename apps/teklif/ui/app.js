/* ===== Barlas Teklif Sistemi — App Logic ===== */
"use strict";

const API = "../server/api";
let PRODUCTS = {categories:[]}, TEMPLATES = {}, COMPANY = {}, COUNTER = {year:2026, seq:0};
let STYLE_CSS = "";
let itemSeq = 0;                 // unique id source for product blocks

const $ = id => document.getElementById(id);
const esc = s => String(s==null?"":s).replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;");
const today = () => { const d=new Date(); const p=n=>String(n).padStart(2,"0"); return `${p(d.getDate())}.${p(d.getMonth()+1)}.${d.getFullYear()}`; };
const formatTL = n => (Number(n)||0).toLocaleString("tr-TR") + " TL";

/* ---------- INIT ---------- */
async function init(){
  document.addEventListener("input", e=>{ if(e.target.closest(".form-pane")) render(); });
  $("theme").addEventListener("change", render);
  $("btnAddItem").addEventListener("click", addItemAndFocus);
  $("btnNew").addEventListener("click", newOffer);
  $("btnSave").addEventListener("click", save);
  $("btnHistory").addEventListener("click", showHistory);
  $("btnHistoryBack").addEventListener("click", showEditor);

  // Formu veri isteklerinden önce oluştur. Sunucu geç yanıt verse veya bir
  // istek hata verse bile ilk ürün kutusu ve arayüz kullanılabilir görünsün.
  newOffer();
  const provisionalOfferNo = $("offerNo").value;

  try{
    [PRODUCTS,TEMPLATES,COMPANY,COUNTER,STYLE_CSS] = await Promise.all([
      fetchJson("../data/products.json"),
      fetchJson("../data/templates.json"),
      fetchJson("../data/company.json"),
      fetchJson("../data/counter.json"),
      fetchText("style.css")
    ]);
  }catch(e){
    console.error("Teklif verileri yüklenemedi:", e);
    toast("Ürün verileri yüklenemedi. Sayfayı yenileyin veya yeniden giriş yapın.","err");
    return;
  }

  document.querySelectorAll("#items .item-card").forEach(fillCategoryOptions);
  if ($("offerNo").value === provisionalOfferNo) $("offerNo").value = nextNo();
  render();
}

async function fetchChecked(url){
  const response=await fetch(url,{credentials:"same-origin",cache:"no-store"});
  if(response.redirected && response.url.includes("/patron/login")){
    window.location.assign(response.url);
    throw new Error("Oturum doğrulanamadı");
  }
  if(!response.ok) throw new Error(`${url}: HTTP ${response.status}`);
  return response;
}

async function fetchJson(url){
  const response=await fetchChecked(url);
  const type=response.headers.get("content-type")||"";
  if(!type.includes("json")) throw new Error(`${url}: JSON yerine ${type||"bilinmeyen içerik"} döndü`);
  return response.json();
}

async function fetchText(url){
  return (await fetchChecked(url)).text();
}

/* ---------- NEW OFFER ---------- */
function newOffer(){
  showEditor();
  $("offerNo").value = nextNo();
  $("date").value = today();
  $("custName").value=""; $("custCompany").value=""; $("custPhone").value="";
  $("vat").value=20; $("payment").value=""; $("delivery").value=""; $("theme").value="light";
  $("items").innerHTML=""; itemSeq=0;
  addItem();                    // start with one empty product block
  renumberItems();
  render();
}

function nextNo(){
  const pre = COMPANY.offer_no_prefix || "TKL";
  return `${pre}-${COUNTER.year}-${String((COUNTER.seq||0)+1).padStart(4,"0")}`;
}

/* ---------- PRODUCT BLOCKS ---------- */
function itemTemplate(){
  return `
  <div class="item-head">
    <h3 class="item-title">Ürün</h3>
    <button type="button" class="item-del btn-mini" title="Bu ürünü kaldır">✕ Kaldır</button>
  </div>
  <div class="grid2">
    <div class="field"><label>Kategori</label><select class="it-category"></select></div>
    <div class="field"><label>Ürün</label><select class="it-product" disabled></select></div>
  </div>
  <div class="field"><label>Başlık (Sipariş Konusu)</label><input class="it-subject" type="text" placeholder="1 ADET ; ..."></div>
  <div class="field">
    <label>Ürün Görseli</label>
    <div class="image-box">
      <img class="it-image-preview" src="" alt="" hidden>
      <div class="it-image-empty image-empty">Görsel yok</div>
    </div>
    <div class="image-tools">
      <button type="button" class="it-image-remove btn-mini">Kaldır</button>
      <label class="btn btn-mini">Değiştir<input type="file" class="it-image-upload" accept="image/*" hidden></label>
    </div>
  </div>
  <div class="card-title-row">
    <h4 class="item-sub">Teknik Özellikler</h4>
    <button type="button" class="it-add-spec btn-mini">＋ Özellik Ekle</button>
  </div>
  <div class="it-specs specs"></div>
  <div class="field item-price"><label>Net Fiyat (TL)</label><input class="it-net" type="number" min="0" step="1000"></div>`;
}

function addItem(data){
  const block=document.createElement("div");
  block.className="card item-card";
  block.dataset.item=String(++itemSeq);
  block._image="";
  block.innerHTML=itemTemplate();

  const cat=block.querySelector(".it-category");
  fillCategoryOptions(block);

  cat.addEventListener("change", ()=>{ onItemCategory(block); render(); });
  block.querySelector(".it-product").addEventListener("change", ()=>onItemProduct(block));
  block.querySelector(".it-add-spec").addEventListener("click", ()=>{ itemSpecRow(block,"",""); render(); });
  block.querySelector(".item-del").addEventListener("click", ()=>removeItem(block));
  block.querySelector(".it-image-remove").addEventListener("click", ()=>{ setItemImage(block,""); render(); });
  block.querySelector(".it-image-upload").addEventListener("change", e=>onItemUploadImage(block,e));

  $("items").appendChild(block);
  if(data) fillItem(block,data);
  return block;
}

function fillCategoryOptions(block){
  const cat=block.querySelector(".it-category");
  const selected=cat.value;
  cat.innerHTML=`<option value="">— Kategori seçin —</option>`+
    PRODUCTS.categories.map(c=>`<option value="${c.slug}">${esc(c.name)}</option>`).join("");
  if(selected) cat.value=selected;
}

function removeItem(block){
  const blocks=[...document.querySelectorAll("#items .item-card")];
  if(blocks.length<=1){ clearItem(block); render(); return; }   // keep at least one
  block.remove(); renumberItems(); render();
}

function clearItem(block){
  block.querySelector(".it-category").value="";
  const u=block.querySelector(".it-product"); u.innerHTML=""; u.disabled=true;
  block.querySelector(".it-subject").value="";
  setItemImage(block,"");
  block.querySelector(".it-specs").innerHTML="";
  block.querySelector(".it-net").value="";
}

function renumberItems(){
  const blocks=[...document.querySelectorAll("#items .item-card")];
  blocks.forEach((b,i)=>{
    b.querySelector(".item-title").textContent=`Ürün ${i+1}`;
    b.querySelector(".item-del").style.display = blocks.length>1 ? "" : "none";
  });
}

/* add a product, then scroll to it + focus + brief flash so the user notices it */
function addItemAndFocus(){
  const block=addItem();
  renumberItems(); render();
  block.scrollIntoView({behavior:"smooth", block:"center"});
  const sel=block.querySelector(".it-category");
  if(sel) setTimeout(()=>sel.focus({preventScroll:true}), 350);
  block.classList.add("item-flash");
  setTimeout(()=>block.classList.remove("item-flash"), 1300);
}

function onItemCategory(block){
  const c=PRODUCTS.categories.find(k=>k.slug===block.querySelector(".it-category").value);
  const u=block.querySelector(".it-product");
  if(!c){ u.innerHTML=""; u.disabled=true; return; }
  u.disabled=false;
  u.innerHTML=`<option value="">— Ürün seçin —</option>`+
    c.products.map(p=>`<option value="${p.slug}">${esc(p.name)}</option>`).join("");
}

async function onItemProduct(block){
  const cs=block.querySelector(".it-category").value, ps=block.querySelector(".it-product").value;
  const cat=PRODUCTS.categories.find(k=>k.slug===cs);
  const prod=cat && cat.products.find(p=>p.slug===ps);
  if(!prod) return;

  const t=TEMPLATES[ps] || TEMPLATES._default || {specs:[],price:{net:0,vat_rate:20}};
  block.querySelector(".it-subject").value = t.subject || `1 ADET ; ${prod.name}`;
  block.querySelector(".it-specs").innerHTML="";
  (t.specs||[]).forEach(o=>itemSpecRow(block,o.label,o.value));
  block.querySelector(".it-net").value = (t.price&&t.price.net)||"";

  /* fill global payment/delivery from template only if still empty (first product) */
  if(t.payment && !$("payment").value) $("payment").value=t.payment;
  if(t.delivery && !$("delivery").value) $("delivery").value=t.delivery;

  if(prod.image){ await setItemImageFromUrl(block,"../"+prod.image); } else { setItemImage(block,""); }
  render();
}

/* ---------- SPEC ROWS (per block) ---------- */
function itemSpecRow(block,label,value){
  const wrap=block.querySelector(".it-specs");
  const row=document.createElement("div"); row.className="spec-row";
  const li=document.createElement("input"); li.type="text"; li.value=label; li.placeholder="ETİKET"; li.className="spec-label";
  const va=document.createElement("textarea"); va.rows=2; va.value=value; va.placeholder="değer"; va.className="spec-value";
  const del=document.createElement("button"); del.type="button"; del.className="spec-del"; del.textContent="✕"; del.title="Sil";
  del.addEventListener("click",()=>{ row.remove(); render(); });
  row.append(li,va,del); wrap.appendChild(row);
}
function collectItemSpecs(block){
  return [...block.querySelectorAll(".it-specs .spec-row")].map(r=>({
    label:r.querySelector(".spec-label").value, value:r.querySelector(".spec-value").value
  })).filter(o=>o.label.trim()||o.value.trim());
}

/* ---------- IMAGE (per block) ---------- */
function setItemImage(block,dataUrl){
  block._image=dataUrl||"";
  const img=block.querySelector(".it-image-preview"), none=block.querySelector(".it-image-empty");
  if(block._image){ img.src=block._image; img.hidden=false; none.hidden=true; }
  else{ img.src=""; img.hidden=true; none.hidden=false; }
}
async function setItemImageFromUrl(block,url){
  try{ const r=await fetch(url); const b=await r.blob();
    const d=await new Promise(res=>{const fr=new FileReader();fr.onload=()=>res(fr.result);fr.readAsDataURL(b);});
    setItemImage(block,d);
  }catch(e){ setItemImage(block,""); }
}
function onItemUploadImage(block,e){
  const f=e.target.files[0]; if(!f) return;
  const fr=new FileReader(); fr.onload=()=>{ setItemImage(block,fr.result); render(); }; fr.readAsDataURL(f);
}

/* ---------- FILL ONE BLOCK (loading an offer) ---------- */
function fillItem(block,it){
  const prod=it.product||{};
  let cat=null;
  if(prod.slug) cat=PRODUCTS.categories.find(k=>k.products.some(p=>p.slug===prod.slug));
  if(!cat && prod.category) cat=PRODUCTS.categories.find(k=>k.name===prod.category);
  if(cat){
    block.querySelector(".it-category").value=cat.slug;
    onItemCategory(block);
    if(prod.slug) block.querySelector(".it-product").value=prod.slug;
  }
  block.querySelector(".it-subject").value=it.subject||"";
  block.querySelector(".it-specs").innerHTML="";
  (it.specs||[]).forEach(s=>itemSpecRow(block,s.label,s.value));
  const net = (it.price && typeof it.price==="object") ? it.price.net : it.price;
  block.querySelector(".it-net").value = net || "";
  setItemImage(block,it.image||"");
}

/* ---------- COLLECT OFFER ---------- */
function collectItems(){
  return [...document.querySelectorAll("#items .item-card")].map(block=>{
    const cs=block.querySelector(".it-category").value, ps=block.querySelector(".it-product").value;
    const cat=PRODUCTS.categories.find(k=>k.slug===cs);
    const prod=cat && cat.products.find(p=>p.slug===ps);
    return {
      product:{category:cat?cat.name:"", name:prod?prod.name:"", slug:prod?prod.slug:""},
      subject:block.querySelector(".it-subject").value.trim(),
      image:block._image||"",
      specs:collectItemSpecs(block),
      price:{net:Number(block.querySelector(".it-net").value)||0}
    };
  });
}

function collectOffer(){
  const items=collectItems();
  const vatRate=Number($("vat").value)||0;
  const subtotal=items.reduce((s,it)=>s+(Number(it.price.net)||0),0);
  const vat=Math.round(subtotal*vatRate/100), total=subtotal+vat;
  return {
    offer_no:$("offerNo").value.trim(), date:$("date").value.trim(),
    customer:{name:$("custName").value.trim(),company:$("custCompany").value.trim(),phone:$("custPhone").value.trim()},
    items,
    price:{vat_rate:vatRate, subtotal, vat, total, currency:"TL"},
    payment:$("payment").value.trim(), delivery:$("delivery").value.trim(),
    theme:$("theme").value||"light"
  };
}

/* ---------- DOCUMENT HTML ---------- */
function docHTML(o){
  const c=o.customer||{};
  const customerBlock = (c.name||c.company||c.phone) ? `
    <div class="sec-label">MÜŞTERİ</div>
    <div class="customer">
      ${c.name?`<b>Sayın ${esc(c.name)}</b>`:""}
      ${c.company?` &nbsp;·&nbsp; ${esc(c.company)}`:""}
      ${c.phone?`<br>Tel: ${esc(c.phone)}`:""}
    </div>` : "";

  const items=o.items||[];
  const multi=items.length>1;
  const itemsHTML=items.map((it,idx)=>{
    const image=it.image?`<div class="doc-image"><img src="${it.image}" alt=""></div>`:"";
    const rows=(it.specs||[]).map(s=>`<tr><th>${esc(s.label)}</th><td>${esc(s.value)}</td></tr>`).join("");
    const heading=multi?`<div class="item-heading">${idx+1}. ${esc(it.product&&it.product.name?it.product.name:("Ürün "+(idx+1)))}</div>`:"";
    return `
    ${heading}
    <div class="sec-label">SİPARİŞ KONUSU</div>
    <div class="subject-card">${esc(it.subject||"—")}</div>
    ${image}
    <div class="sec-label">TEKNİK ÖZELLİKLER</div>
    <table class="spec">${rows||`<tr><td>—</td></tr>`}</table>`;
  }).join(`<div class="item-divider"></div>`);

  const p=o.price||{};
  let priceRows="";
  if(multi){
    priceRows=items.map((it,idx)=>{
      const nm=it.product&&it.product.name?it.product.name:("Ürün "+(idx+1));
      return `<div class="row"><span>${idx+1}. ${esc(nm)}</span><span>${formatTL(it.price&&it.price.net)}</span></div>`;
    }).join("");
    priceRows+=`<div class="row subtotal"><span>Ara Toplam</span><span>${formatTL(p.subtotal)}</span></div>`;
  }else{
    priceRows=`<div class="row"><span>Net Fiyat</span><span>${formatTL(p.subtotal)} + KDV</span></div>`;
  }

  const foot=[COMPANY.name, COMPANY.address, COMPANY.phone, COMPANY.web].filter(x=>x&&!/^\[/.test(x)).map(esc).join(" · ");
  const foot2=[COMPANY.iban?`IBAN: ${COMPANY.iban}`:"", COMPANY.tax_no?`Vergi No: ${COMPANY.tax_no}`:""].filter(x=>x&&!/\[/.test(x)).map(esc).join(" · ");
  const logo=(window.BARLAS_LOGO||"assets/logo.png");
  return `
  <div class="doc-head">
    <img class="doc-logo" src="${logo}" alt="Barlas">
    <div class="doc-head-right">
      <div class="doc-title">FİYAT TEKLİFİ</div>
      <div class="doc-meta">
        ${o.offer_no?`<b>Teklif No:</b> ${esc(o.offer_no)}<br>`:""}
        <b>Tarih:</b> ${esc(o.date||today())}
      </div>
    </div>
  </div>
  <div class="accent-rule"></div>
  ${customerBlock}
  ${itemsHTML}
  <div class="sec-label">FİYAT VE ÖDEME</div>
  <div class="price-card">
    ${priceRows}
    <div class="row"><span>KDV (%${p.vat_rate||0})</span><span>${formatTL(p.vat)}</span></div>
    <div class="row total"><span>KDV Dahil Toplam</span><span>${formatTL(p.total)}</span></div>
    ${o.payment?`<div class="row"><span>Ödeme</span><span>${esc(o.payment)}</span></div>`:""}
    ${o.delivery?`<div class="row"><span>Teslimat</span><span>${esc(o.delivery)}</span></div>`:""}
  </div>
  <div class="doc-foot">
    <div>${foot||"BARLAS TANKER · www.barlastanker.com"}</div>
    <div>${foot2}</div>
  </div>
  <div class="watermark">${esc((COMPANY.web||"www.barlastanker.com").replace(/^https?:\/\//,""))}</div>`;
}

/* ---------- PREVIEW ---------- */
function render(){
  const o=collectOffer();
  $("subtotalAmount").textContent=formatTL(o.price.subtotal);
  $("vatAmount").textContent=formatTL(o.price.vat);
  $("total").textContent=formatTL(o.price.total);
  $("offerOutput").className = "doc" + (o.theme==="dark" ? " theme-dark" : "");
  $("offerOutput").innerHTML=docHTML(o);
}

/* ---------- SAVE & PDF ---------- */
function standaloneHTML(o){
  return `<!DOCTYPE html><html lang="tr"><head><meta charset="UTF-8">
<title>Teklif ${esc(o.offer_no)}</title><style>${STYLE_CSS}</style></head>
<body class="standalone"><div class="doc${o.theme==="dark"?" theme-dark":""}">${docHTML(o)}</div></body></html>`;
}

async function save(){
  const o=collectOffer();
  const hasProduct=o.items.some(it=>it.product.name || it.subject);
  if(!hasProduct){ toast("Önce bir ürün seçin.","err"); return; }
  $("btnSave").disabled=true; toast("Kaydediliyor…");
  try{
    const r=await fetch(`${API}/save.php`,{
      method:"POST",headers:{"Content-Type":"application/json"},
      body:JSON.stringify({offer:o, html:standaloneHTML(o)})
    });
    const j=await r.json();
    if(!j.ok) throw new Error(j.message||"Hata");
    if(j.seq!=null) COUNTER.seq=j.seq;
    if(j.pdf_created && j.folder){
      toast("✓ Teklif kaydedildi, PDF indiriliyor…","ok");
      await downloadPdf(j.folder, o.offer_no);
    }else{
      toast("✓ Klasör oluşturuldu. PDF için yazdır penceresi açılıyor…","ok");
      printOffer(o);
    }
  }catch(e){ toast("Kaydedilemedi: "+e.message,"err"); }
  finally{ $("btnSave").disabled=false; }
}

/* fetch the server-generated PDF and push it to the browser's Downloads */
async function downloadPdf(folder, offerNo){
  const url=`../offers/${encodeURIComponent(folder)}/offer.pdf`;
  try{
    const r=await fetch(url);
    if(!r.ok) throw new Error();
    const blob=await r.blob();
    const objUrl=URL.createObjectURL(blob);
    const a=document.createElement("a");
    a.href=objUrl; a.download=(offerNo||"teklif")+".pdf";
    document.body.appendChild(a); a.click(); a.remove();
    setTimeout(()=>URL.revokeObjectURL(objUrl), 5000);
  }catch(e){
    window.open(url,"_blank");   // fallback: open so the user can save manually
  }
}

function printOffer(o){
  $("offerOutput").innerHTML=docHTML(o);
  setTimeout(()=>window.print(),300);
}

/* ---------- VIEW SWITCH ---------- */
function showEditor(){ $("historyPage").hidden=true; $("editorView").hidden=false; }

/* offers hidden from the list — UI only; folders/files stay on disk */
let hiddenMem=null;
function hiddenOffers(){
  if(hiddenMem) return hiddenMem;
  try{ hiddenMem=JSON.parse(localStorage.getItem("barlas_hidden_offers")||"[]"); }catch(e){ hiddenMem=[]; }
  if(!Array.isArray(hiddenMem)) hiddenMem=[];
  return hiddenMem;
}
function saveHiddenOffers(a){
  hiddenMem=a;
  try{ localStorage.setItem("barlas_hidden_offers", JSON.stringify(a)); }catch(e){}
}

async function showHistory(){
  $("editorView").hidden=true; $("historyPage").hidden=false;
  const list=$("historyList"); list.innerHTML=`<div class="history-empty">Yükleniyor…</div>`;
  try{
    const all=await fetch(`${API}/list.php`).then(r=>r.json());
    const hidden=hiddenOffers();
    const visible=all.filter(x=>!hidden.includes(x.folder));
    const hiddenCount=all.length-visible.length;
    let html = hiddenCount>0
      ? `<div class="history-hidden-bar">${hiddenCount} teklif listeden gizli · <button id="btnRestoreHidden" class="linklike" type="button">geri getir</button></div>`
      : "";
    if(!visible.length){
      html += `<div class="history-empty">${all.length?"Gösterilecek teklif yok.":"Henüz teklif yok."}</div>`;
    }else{
      html += visible.map(x=>`
      <div class="history-item">
        <div class="hi-info">
          <b>${esc(x.offer_no||x.folder)}</b>
          <span>${esc(x.date||"")} · ${esc(x.customer||"—")} · ${esc(x.product||"—")}</span>
        </div>
        <div class="hi-tools">
          ${x.pdf?`<a class="btn btn-mini" href="../offers/${encodeURIComponent(x.folder)}/offer.pdf" target="_blank">PDF</a>`:""}
          <button class="btn btn-mini" data-open="${esc(x.folder)}">Aç / Düzenle</button>
          <button class="btn btn-mini btn-danger" data-del="${esc(x.folder)}">Sil</button>
        </div>
      </div>`).join("");
    }
    list.innerHTML=html;
    const rb=$("btnRestoreHidden");
    if(rb) rb.addEventListener("click",()=>{ saveHiddenOffers([]); showHistory(); toast("Gizlenen teklifler geri getirildi.","ok"); });
    list.querySelectorAll("[data-open]").forEach(b=>b.addEventListener("click",()=>openOffer(b.dataset.open)));
    list.querySelectorAll("[data-del]").forEach(b=>b.addEventListener("click",()=>deleteOffer(b.dataset.del)));
  }catch(e){ list.innerHTML=`<div class="history-empty">Liste alınamadı.</div>`; }
}

async function openOffer(folder){
  try{
    const o=await fetch(`${API}/open.php?folder=${encodeURIComponent(folder)}`).then(r=>r.json());
    if(!o || !o.offer_no) throw new Error();
    showEditor();
    $("offerNo").value=o.offer_no; $("date").value=o.date||today();
    $("custName").value=o.customer?.name||""; $("custCompany").value=o.customer?.company||""; $("custPhone").value=o.customer?.phone||"";
    $("vat").value=o.price?.vat_rate??20;
    $("payment").value=o.payment||""; $("delivery").value=o.delivery||"";
    $("theme").value=o.theme||"light";

    /* normalize to items[] — backward compatible with old single-product offers */
    let items=Array.isArray(o.items)?o.items:null;
    if(!items){
      items=[{product:o.product||{}, subject:o.subject||"", image:o.image||"", specs:o.specs||[], price:{net:o.price?.net||0}}];
    }
    $("items").innerHTML=""; itemSeq=0;
    (items.length?items:[{}]).forEach(it=>addItem(it));
    renumberItems();
    render();
    toast("Teklif yüklendi — düzenleyip tekrar kaydedebilirsiniz.","ok");
  }catch(e){ toast("Teklif açılamadı.","err"); }
}

/* "Sil" = teklifi yalnızca listeden gizle. Klasör ve dosyalar diskte KORUNUR
   (delete.php çağrılmaz). Yanlışlıkla gizlenirse "geri getir" ile dönülür. */
function deleteOffer(folder){
  const hidden=hiddenOffers();
  if(!hidden.includes(folder)){ hidden.push(folder); saveHiddenOffers(hidden); }
  showHistory();
  toast("Listeden gizlendi — dosyalar diskte korundu.","ok");
}

/* ---------- TOAST ---------- */
let toastT;
function toast(msg,type){
  const el=$("toast"); el.textContent=msg; el.className="toast no-print"+(type?" "+type:""); el.hidden=false;
  clearTimeout(toastT); toastT=setTimeout(()=>el.hidden=true, type==="err"?5000:3500);
}

document.addEventListener("DOMContentLoaded", init);
/* v2: çoklu ürün + oto PDF indirme + geçmiş sayfası */
