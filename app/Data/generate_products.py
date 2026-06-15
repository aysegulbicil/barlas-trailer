#!/usr/bin/env python3
import json, re, unicodedata
from docx import Document
from docx.table import Table
from docx.text.paragraph import Paragraph
from docx.oxml.ns import qn

SRC = 'TUM_URUNLER_HIYERARSIK_KATALOG (3).docx'
OUT = 'app/Data/products.json'

CAT_SLUGS = {1:'tankers',2:'silos',3:'tippers',4:'curtain',5:'reefer',
             6:'dry-cargo',7:'containers',8:'lowbed',9:'platform',10:'trailers',11:'special'}

TR_MAP = str.maketrans('çÇğĞıİöÖşŞüÜ', 'cCgGiIoOsSuU')

BRAND_PATTERNS = [
    r'barlas\s+tanker',
    r'nursan\s+tra[iİı]ler', r'nursan\s+treyler', r'\bnursan\b',
    r'okt\s+tra[iİı]ler', r'\bokt\b',
    r't[öo]ke\s+mak[iİı]na', r't[öo]ke\s+tra[iİı]ler', r'\bt[öo]ke\b',
    r't[ıi]rsan\s+treyler', r'\bt[ıi]rsan\b',
    r'\bseymak\b', r'\bk[äa]ssbohrer\b', r'\bgüvenmak\b', r'\bguvenmak\b',
]
BRAND_RE = re.compile('|'.join(BRAND_PATTERNS), re.IGNORECASE | re.UNICODE)

def scrub(text):
    def repl(m):
        return 'BARLAS' if m.group(0).isupper() else 'Barlas'
    out = BRAND_RE.sub(repl, text)
    out = re.sub(r'\b(Barlas)(\s+Barlas)+\b', r'\1', out)
    out = re.sub(r'\b(BARLAS)(\s+BARLAS)+\b', r'\1', out)
    return out

# Cumle yeniden yazimlari (rewrites.json): kaynak metinle birebir ayni
# olmamak icin aciklama cumleleri anlam, sayi ve standartlar korunarak
# yeniden ifade edilmistir. Eslesme tam metin uzerinden yapilir;
# haritada olmayan satirlar (saf veri/olcu satirlari) aynen kalir.
import os as _os
_RW_PATH = _os.path.join(_os.path.dirname(_os.path.abspath(__file__)), 'rewrites.json')
try:
    REWRITES = json.load(open(_RW_PATH, encoding='utf-8'))
except FileNotFoundError:
    REWRITES = {}

def rewrite(text):
    return REWRITES.get(text, text)

def slugify(text):
    t = text.translate(TR_MAP)
    t = unicodedata.normalize('NFKD', t).encode('ascii','ignore').decode()
    return re.sub(r'[^a-zA-Z0-9]+','-',t).strip('-').lower()

def iter_blocks(doc):
    for child in doc.element.body.iterchildren():
        if child.tag == qn('w:p'): yield Paragraph(child, doc)
        elif child.tag == qn('w:tbl'): yield Table(child, doc)

doc = Document(SRC)
blocks = list(iter_blocks(doc))
def find_idx(pred):
    return next(i for i,b in enumerate(blocks) if isinstance(b,Paragraph) and pred(b.text.strip()))
sec2 = find_idx(lambda t: t.startswith('Bölüm II '))
sec3 = find_idx(lambda t: t.startswith('Bölüm III'))

H1RE = re.compile(r'^(\d+)\.\s+(.*)$')
H2RE = re.compile(r'^(\d+)\.(\d+)\s+(.*)$')

cats = {}; products = {}; cur_cat = cur_prod = None
for b in blocks[sec2:sec3]:
    if isinstance(b, Paragraph):
        t = b.text.strip()
        if not t: continue
        st = b.style.name
        if st == 'Heading 1':
            m = H1RE.match(t)
            if m:
                no = int(m.group(1))
                cur_cat = cats.setdefault(no, {'no':no,'name':m.group(2).strip(),
                                               'slug':CAT_SLUGS[no],'products':[]})
                cur_prod = None
        elif st == 'Heading 2':
            m = H2RE.match(t)
            if m and cur_cat:
                pno = int(m.group(2))
                cur_prod = {'no':f"{cur_cat['no']}.{pno}",'name':m.group(3).strip(),
                            'slug':slugify(m.group(3)),'aliases':[],'variants':[]}
                products[(cur_cat['no'],pno)] = cur_prod
                cur_cat['products'].append(cur_prod)
        elif st == 'Source Note' and cur_prod:
            if t.startswith('Diğer kullanılan adlar:'):
                cur_prod['aliases'] = [x.strip() for x in t[len('Diğer kullanılan adlar:'):].split(';') if x.strip()]

GROUPISH = re.compile(r'^[^\W\d]', re.UNICODE)
def classify(text, style):
    if style == 'Heading 4': return 'h', text
    if text.startswith('•'): return 'li', text.lstrip('•').strip()
    if (len(text)<=45 and ':' not in text and not re.search(r'\d',text)
            and not text.endswith(('.','!','?','*')) and GROUPISH.match(text)):
        return 'g', text
    return 'p', text

cur_cat_no = cur_prod_key = None; cur_var = None
for b in blocks[sec3:]:
    if not isinstance(b, Paragraph): continue
    t = b.text.strip()
    if not t: continue
    st = b.style.name
    if st == 'Heading 1':
        m = H1RE.match(t)
        cur_cat_no = int(m.group(1)) if m else None
        cur_prod_key = None; cur_var = None
    elif st == 'Heading 2' and cur_cat_no:
        m = H2RE.match(t)
        if m: cur_prod_key = (cur_cat_no, int(m.group(2))); cur_var = None
    elif st == 'Heading 3' and cur_prod_key in products:
        model = t.split(' — ',1)[1] if ' — ' in t else t
        # Kaynak defter kayitlari ve referans tip kodlari gosterilmez:
        #  - "[Kaynak varyanti N]" ekleri
        #  - parantez icindeki K.* tip kodlari (or. "(K.STB 39 / 5 - 11 / 24)")
        model = re.sub(r'\s*\[Kaynak varyant[ıi]\s*\d+\]', '', model)
        model = re.sub(r'\s*\(K\.[^)]*\)', '', model)
        model = scrub(model.strip())
        cur_var = {'model':model,'slug':slugify(model),'specs':[]}
        products[cur_prod_key]['variants'].append(cur_var)
    elif cur_var is not None:
        if st == 'Source Note':
            pass
        elif st in ('Source Text','Heading 4'):
            kind, text = classify(t, st)
            text = scrub(text)
            if kind in ('li', 'p'):
                text = rewrite(text)
            cur_var['specs'].append({'t':kind,'x':text})

NO_SPEC = re.compile(
    r'teknik (?:özellik(?:leri)?|özellik bilgisi|açıklama).*?'
    r'(?:belirtilmemiştir|verilmemiştir|paylaşılmamıştır|yer almamaktadır|bulunmamaktadır)',
    re.IGNORECASE,
)

def norm_line(t):
    return re.sub(r'[\s:•\-–]+', '', t).lower()

def to_sections(specs):
    secs = []; cur = {'title': '', 'items': []}
    for l in specs:
        if l['t'] in ('h', 'g'):
            if cur['title'] != '' or cur['items']: secs.append(cur)
            cur = {'title': l['x'], 'items': []}
            continue
        cur['items'].append(l)
    if cur['title'] != '' or cur['items']: secs.append(cur)
    return secs

def merge_variants(variants):
    """Ayni adli modeller tek foyde birlesir: bolumler basliga gore
    eslesir, birebir ayni satirlar bir kez yazilir, yalniz farkli
    satirlar eklenir (siralama korunur)."""
    base = variants[0]
    secs = to_sections(base['specs'])
    index = {s['title'].strip().casefold(): s for s in secs}
    for v in variants[1:]:
        for s in to_sections(v['specs']):
            key = s['title'].strip().casefold()
            if key in index:
                tgt = index[key]
                seen = {norm_line(i['x']) for i in tgt['items']}
                for item in s['items']:
                    if norm_line(item['x']) not in seen:
                        tgt['items'].append(item); seen.add(norm_line(item['x']))
            else:
                secs.append(s); index[key] = s
    flat = []
    for s in secs:
        if s['title'] != '': flat.append({'t': 'g', 'x': s['title']})
        flat.extend(s['items'])
    base['specs'] = flat
    return base

for p in products.values():
    p['name'] = scrub(p['name'])
    p['aliases'] = [scrub(a) for a in p['aliases']]
    for v in p['variants']:
        joined = ' '.join(s['x'] for s in v['specs'])
        v['has_specs'] = not (len(v['specs'])<=2 and NO_SPEC.search(joined))
    # 1) foysuz varyantlar hic eklenmez
    p['variants'] = [v for v in p['variants'] if v['has_specs']]
    # 2) ayni adli modeller tek foyde birlesir
    groups = {}; order = []
    for v in p['variants']:
        key = re.sub(r'[^a-z0-9]', '', v['model'].lower())
        if key not in groups: groups[key] = []; order.append(key)
        groups[key].append(v)
    p['variants'] = [merge_variants(groups[k]) for k in order]

# 3) hic foyu kalmayan urunler katalogdan cikar
for c in cats.values():
    c['products'] = [p for p in c['products'] if p['variants']]

for c in cats.values():
    seen = {}
    for p in c['products']:
        s = p['slug']
        if s in seen: p['slug'] = f"{s}-{p['no'].replace('.','-')}"
        seen[p['slug']] = True
    for p in c['products']:
        vs = {}
        for v in p['variants']:
            while v['slug'] in vs: v['slug'] += '-2'
            vs[v['slug']] = True

data = {'generated_from':SRC,'categories':[cats[k] for k in sorted(cats)]}
with open(OUT,'w',encoding='utf-8') as f:
    json.dump(data,f,ensure_ascii=False,indent=1)

ncat=len(data['categories'])
nprod=sum(len(c['products']) for c in data['categories'])
nvar=sum(len(p['variants']) for c in data['categories'] for p in c['products'])
nspec=sum(len(v['specs']) for c in data['categories'] for p in c['products'] for v in p['variants'])
print(f"categories={ncat} products={nprod} variants={nvar} spec_lines={nspec}")
