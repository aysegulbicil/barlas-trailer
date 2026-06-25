/**
 * yeni-tanker.js — hero'daki etkileşimli 3D tanker (yeni arayüz)
 * CodeIgniter ana sayfası (layouts/yeni.php) tarafından yüklenir.
 *
 * Prosedürel olarak kurulan tanker dorsesi (lisans derdi yok), hero'nun
 * sağ sahnesinde cursor ile orbit eder (Spline hissi); sayfa hero'dan
 * çıkarken hafifçe yola döner ve söner (ScrollTrigger varsa).
 *
 * Aşamalı geliştirme: THREE yoksa, WebGL yoksa, ekran < 992px ise veya
 * reduced-motion açıksa hiç başlamaz; statik yedek görsel görünür kalır.
 *
 * YOL SAHNESİ (initRoad): artık gerçek GLB kamyonlardan bir KONVOY yükler
 * (window.__BARLAS_MODELS → assets/models/tanker-*.glb), art arda dizip
 * akan yolda sürdürür. GLTFLoader yoksa/yükleme başarısızsa prosedürel
 * çekici+tanker rig'ine düşer. HERO sahnesi (init) hâlâ prosedüreldir.
 */
(function () {
    'use strict';

    function onReady(fn) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fn, { once: true });
        } else {
            fn();
        }
    }

    function webglOk() {
        try {
            var c = document.createElement('canvas');
            return !!(window.WebGLRenderingContext &&
                (c.getContext('webgl') || c.getContext('experimental-webgl')));
        } catch (e) { return false; }
    }

    function showStaticFallback() {
        var hero = document.querySelector('.hero');
        if (hero) hero.classList.add('has-static-fallback');
    }

    onReady(function () {
        window.setTimeout(function () {
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
            if (window.innerWidth < 992) return;

            if (!window.THREE || !webglOk()) {
                showStaticFallback();
                return;
            }

            var stage = document.querySelector('[data-hero-stage]');
            if (stage) {
                try {
                    init(stage);
                } catch (err) {
                    showStaticFallback();
                    if (window.console && console.error) console.error('[barlas-3d]', err);
                }
            }

            var road = document.querySelector('[data-road]');
            if (road && window.gsap && window.ScrollTrigger) initRoad(road);
        }, 0);
    });

    /* ----------------------- doku yardımcıları ----------------------- */

    function makeStudioEnv(THREE) {
        var c = document.createElement('canvas');
        c.width = 1024; c.height = 512;
        var ctx = c.getContext('2d');
        var sky = ctx.createLinearGradient(0, 0, 0, 512);
        sky.addColorStop(0, '#36435c');
        sky.addColorStop(0.5, '#1b2436');
        sky.addColorStop(1, '#05080f');
        ctx.fillStyle = sky;
        ctx.fillRect(0, 0, 1024, 512);

        function softbox(x, y, w, h, color, a) {
            ctx.save();
            ctx.globalAlpha = a;
            ctx.filter = 'blur(14px)';
            ctx.fillStyle = color;
            ctx.fillRect(x, y, w, h);
            ctx.restore();
        }
        softbox(90, 60, 300, 70, '#ffffff', 0.95);
        softbox(560, 95, 360, 56, '#dfe9f6', 0.8);
        softbox(330, 205, 180, 36, '#9fc6ef', 0.55);
        softbox(0, 460, 1024, 52, '#1c2638', 0.9);

        var tex = new THREE.CanvasTexture(c);
        tex.mapping = THREE.EquirectangularReflectionMapping;
        return tex;
    }

    function makeDecal(THREE, flip) {
        var c = document.createElement('canvas');
        c.width = 256; c.height = 1024;
        var ctx = c.getContext('2d');
        ctx.clearRect(0, 0, 256, 1024);
        ctx.save();
        ctx.translate(128, 512);
        ctx.rotate(flip ? -Math.PI / 2 : Math.PI / 2);
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.font = '800 118px Sora, Inter, Arial, sans-serif';
        ctx.fillStyle = 'rgba(11, 18, 32, 0.92)';
        ctx.fillText('BARLAS', 0, -14);
        ctx.font = '600 30px Inter, Arial, sans-serif';
        ctx.fillStyle = 'rgba(0, 91, 170, 0.9)';
        ctx.fillText('T R A I L E R', 0, 64);
        ctx.restore();
        var tex = new THREE.CanvasTexture(c);
        tex.anisotropy = 4;
        return tex;
    }

    function makeShadow(THREE) {
        var c = document.createElement('canvas');
        c.width = 256; c.height = 128;
        var ctx = c.getContext('2d');
        var g = ctx.createRadialGradient(128, 64, 8, 128, 64, 120);
        g.addColorStop(0, 'rgba(3, 6, 12, 0.5)');
        g.addColorStop(0.55, 'rgba(3, 6, 12, 0.2)');
        g.addColorStop(1, 'rgba(3, 6, 12, 0)');
        ctx.fillStyle = g;
        ctx.fillRect(0, 0, 256, 128);
        return new THREE.CanvasTexture(c);
    }

    /* ----------------------- prosedürel tanker ----------------------- */

    function buildTanker(THREE) {
        var g = new THREE.Group();
        var R = 1.0, L = 5.4;

        var steel = new THREE.MeshStandardMaterial({ color: 0xd9dee5, metalness: 1, roughness: 0.24, envMapIntensity: 1.15 });
        var steelDark = new THREE.MeshStandardMaterial({ color: 0x9aa6b5, metalness: 1, roughness: 0.35, envMapIntensity: 0.9 });
        var chassis = new THREE.MeshStandardMaterial({ color: 0x1b2638, metalness: 0.55, roughness: 0.6 });
        var dark = new THREE.MeshStandardMaterial({ color: 0x10182a, metalness: 0.4, roughness: 0.7 });
        var tire = new THREE.MeshStandardMaterial({ color: 0x14181f, roughness: 0.95 });
        var rim = new THREE.MeshStandardMaterial({ color: 0xcfd6df, metalness: 1, roughness: 0.28 });
        var blue = new THREE.MeshStandardMaterial({ color: 0x005baa, metalness: 0.65, roughness: 0.38 });

        function add(geo, mat, x, y, z, rx, ry, rz) {
            var m = new THREE.Mesh(geo, mat);
            m.position.set(x || 0, y || 0, z || 0);
            if (rx) m.rotation.x = rx;
            if (ry) m.rotation.y = ry;
            if (rz) m.rotation.z = rz;
            g.add(m);
            return m;
        }

        var barrel = new THREE.CylinderGeometry(R, R, L, 56, 1, true);
        barrel.rotateZ(Math.PI / 2);
        add(barrel, steel);

        var cap = new THREE.SphereGeometry(R, 48, 24);
        add(cap, steel, -L / 2, 0, 0).scale.set(0.42, 1, 1);
        add(cap, steel, L / 2, 0, 0).scale.set(0.42, 1, 1);

        var ring = new THREE.TorusGeometry(R + 0.015, 0.022, 10, 72);
        ring.rotateY(Math.PI / 2);
        [-1.9, -0.7, 0.5, 1.7].forEach(function (x) { add(ring, steelDark, x, 0, 0); });

        var band = new THREE.CylinderGeometry(R + 0.006, R + 0.006, 0.10, 56, 1, true);
        band.rotateZ(Math.PI / 2);
        add(band, blue, -2.35, 0, 0);

        function decal(thetaStart, flip) {
            var dg = new THREE.CylinderGeometry(R + 0.012, R + 0.012, 3.4, 48, 1, true, thetaStart, 1.15);
            dg.rotateZ(Math.PI / 2);
            var m = new THREE.Mesh(dg, new THREE.MeshBasicMaterial({
                map: makeDecal(THREE, flip), transparent: true,
                polygonOffset: true, polygonOffsetFactor: -2
            }));
            m.position.x = 0.25;
            g.add(m);
        }
        decal(-0.575, false);
        decal(Math.PI - 0.575, true);

        add(new THREE.BoxGeometry(3.6, 0.04, 0.5), dark, 0.2, R + 0.04, 0);
        [-0.9, 0.2, 1.3].forEach(function (x) {
            add(new THREE.CylinderGeometry(0.17, 0.19, 0.12, 24), steelDark, x, R + 0.10, 0);
            add(new THREE.SphereGeometry(0.15, 24, 12, 0, Math.PI * 2, 0, Math.PI / 2), steel, x, R + 0.15, 0).scale.y = 0.55;
        });

        add(new THREE.BoxGeometry(4.8, 0.14, 0.12), chassis, 0.3, -R - 0.10, 0.52);
        add(new THREE.BoxGeometry(4.8, 0.14, 0.12), chassis, 0.3, -R - 0.10, -0.52);

        var wheels = [];
        var tireGeo = new THREE.TorusGeometry(0.32, 0.135, 14, 36);
        var rimGeo = new THREE.CylinderGeometry(0.20, 0.20, 0.24, 24);
        rimGeo.rotateX(Math.PI / 2);
        [1.05, 1.95, 2.85].forEach(function (x) {
            [-0.78, 0.78].forEach(function (z) {
                var w = new THREE.Group();
                w.add(new THREE.Mesh(tireGeo, tire));
                w.add(new THREE.Mesh(rimGeo, rim));
                w.position.set(x, -R - 0.42, z);
                g.add(w);
                wheels.push(w);
            });
            var ax = new THREE.CylinderGeometry(0.05, 0.05, 1.56, 10);
            ax.rotateX(Math.PI / 2);
            add(ax, dark, x, -R - 0.42, 0);
        });

        [-0.5, 0.5].forEach(function (z) {
            add(new THREE.BoxGeometry(0.09, 0.62, 0.09), steelDark, -1.85, -R - 0.36, z * 1.1);
            add(new THREE.BoxGeometry(0.22, 0.05, 0.16), dark, -1.85, -R - 0.66, z * 1.1);
        });
        add(new THREE.BoxGeometry(1.0, 0.08, 0.94), dark, -2.25, -R - 0.06, 0);
        add(new THREE.BoxGeometry(0.07, 0.42, 1.9), chassis, 3.06, -R - 0.32, 0);

        var shadow = new THREE.Mesh(
            new THREE.PlaneGeometry(8.6, 3.0),
            new THREE.MeshBasicMaterial({ map: makeShadow(THREE), transparent: true, depthWrite: false })
        );
        shadow.rotation.x = -Math.PI / 2;
        shadow.position.y = -R - 0.74;
        g.add(shadow);

        g.userData.wheels = wheels;
        return g;
    }

    /* ------------------- prosedürel çekici (kamyon kafası) ------------ */

    function buildTractor(THREE) {
        var g = new THREE.Group();

        var paint = new THREE.MeshStandardMaterial({ color: 0x005baa, metalness: 0.7, roughness: 0.32, envMapIntensity: 1.0 });
        var dark = new THREE.MeshStandardMaterial({ color: 0x10182a, metalness: 0.4, roughness: 0.7 });
        var glass = new THREE.MeshStandardMaterial({ color: 0x0a1422, metalness: 0.9, roughness: 0.12, envMapIntensity: 1.3 });
        var steelDark = new THREE.MeshStandardMaterial({ color: 0x9aa6b5, metalness: 1, roughness: 0.35 });
        var tire = new THREE.MeshStandardMaterial({ color: 0x14181f, roughness: 0.95 });
        var rim = new THREE.MeshStandardMaterial({ color: 0xcfd6df, metalness: 1, roughness: 0.28 });

        function add(geo, mat, x, y, z) {
            var m = new THREE.Mesh(geo, mat);
            m.position.set(x || 0, y || 0, z || 0);
            g.add(m);
            return m;
        }

        /* Şasi + beşinci teker tablası */
        add(new THREE.BoxGeometry(3.0, 0.16, 1.0), dark, 0.1, -1.05, 0);
        add(new THREE.CylinderGeometry(0.5, 0.5, 0.08, 24), steelDark, 0.9, -0.95, 0);

        /* Kabin gövdesi (stilize, hafif yuvarlatılmış hisse boyalı kütle) */
        add(new THREE.BoxGeometry(1.5, 1.75, 1.9), paint, -0.85, 0.05, 0);
        /* Tavan spoyleri */
        var spoiler = add(new THREE.BoxGeometry(1.0, 0.5, 1.7), paint, -0.62, 1.12, 0);
        spoiler.rotation.z = -0.35;
        /* Ön cam (hafif eğimli koyu cam) */
        var ws = add(new THREE.BoxGeometry(0.06, 0.78, 1.62), glass, -1.62, 0.42, 0);
        ws.rotation.z = -0.12;
        /* Yan camlar */
        add(new THREE.BoxGeometry(0.9, 0.5, 0.02), glass, -0.95, 0.45, 0.96);
        add(new THREE.BoxGeometry(0.9, 0.5, 0.02), glass, -0.95, 0.45, -0.96);
        /* Tampon + ızgara */
        add(new THREE.BoxGeometry(0.18, 0.5, 1.9), dark, -1.66, -0.78, 0);
        add(new THREE.BoxGeometry(0.05, 0.4, 1.3), steelDark, -1.72, -0.18, 0);
        /* Farlar */
        var lamp = new THREE.MeshStandardMaterial({ color: 0xbfd9f2, emissive: 0x9fc6ef, emissiveIntensity: 0.7, roughness: 0.3 });
        add(new THREE.BoxGeometry(0.06, 0.12, 0.3), lamp, -1.7, -0.55, 0.62);
        add(new THREE.BoxGeometry(0.06, 0.12, 0.3), lamp, -1.7, -0.55, -0.62);
        /* Egzoz + aynalar */
        add(new THREE.CylinderGeometry(0.05, 0.05, 1.5, 10), steelDark, 0.0, 0.1, 0.85);
        add(new THREE.BoxGeometry(0.04, 0.3, 0.16), dark, -1.5, 0.75, 1.05);
        add(new THREE.BoxGeometry(0.04, 0.3, 0.16), dark, -1.5, 0.75, -1.05);

        /* Tekerlekler: ön 1 aks, arka çift aks */
        var wheels = [];
        var tireGeo = new THREE.TorusGeometry(0.32, 0.135, 14, 36);
        var rimGeo = new THREE.CylinderGeometry(0.20, 0.20, 0.24, 24);
        rimGeo.rotateX(Math.PI / 2);
        [-1.15, 0.55, 1.35].forEach(function (x) {
            [-0.78, 0.78].forEach(function (z) {
                var w = new THREE.Group();
                w.add(new THREE.Mesh(tireGeo, tire));
                w.add(new THREE.Mesh(rimGeo, rim));
                w.position.set(x, -1.42, z);
                g.add(w);
                wheels.push(w);
            });
        });

        g.userData.wheels = wheels;
        return g;
    }

    /* --------------- prosedürel yedek rig (GLB yoksa) ---------------- */

    function buildProceduralRig(THREE) {
        var rig = new THREE.Group();
        var trailer = buildTanker(THREE);
        trailer.position.set(1.6, 0.32, 0);
        rig.add(trailer);
        /* Showroom kontakt gölgesi yolda kapatılır */
        trailer.children.forEach(function (child) {
            if (child.geometry && child.geometry.type === 'PlaneGeometry') child.visible = false;
        });
        var tractor = buildTractor(THREE);
        tractor.position.set(-2.55, 0.32, 0);
        rig.add(tractor);
        rig.scale.setScalar(0.92);
        return { group: rig, wheels: trailer.userData.wheels.concat(tractor.userData.wheels) };
    }

    /* ------------------- GLB normalize + konvoy yükleyici ------------- */

    /**
     * GLB sahnesini standartlaştır: XZ'de ortala, tabanı y=0'a indir,
     * uzunluğu (X) hedef birime ölçekle, gerekirse Y ekseninde döndür.
     * Bir "holder" grubu döner; konvoy bunları X boyunca dizer.
     */
    function normalizeModel(THREE, root, targetLen, yaw) {
        root.updateMatrixWorld(true);
        var box = new THREE.Box3().setFromObject(root);
        var size = box.getSize(new THREE.Vector3());
        var center = box.getCenter(new THREE.Vector3());
        root.position.x -= center.x;
        root.position.z -= center.z;
        root.position.y -= box.min.y;              /* taban -> y=0 */
        root.traverse(function (o) {
            if (o.isMesh && o.material) {
                o.frustumCulled = false;
                if (o.material.map) o.material.map.anisotropy = 8;
                if ('envMapIntensity' in o.material) o.material.envMapIntensity = 1.0;
            }
        });
        var holder = new THREE.Group();
        holder.add(root);
        /* Eşit boy: en uzun yatay eksene göre ölçekle (model X ya da Z uzun olabilir) */
        holder.scale.setScalar(targetLen / (Math.max(size.x, size.z) || 1));
        holder.rotation.y = yaw || 0;
        holder.userData.targetLen = targetLen;
        return holder;
    }

    /**
     * Hero sahnesi için: modeli her eksende ortala (taban değil, merkez),
     * en uzun yatay eksene göre hedef boya ölçekle. Hero, sahneyi y ekseninde
     * hafifçe aşağıda konumlar; bu yüzden konvoydan farklı olarak Y de ortalanır.
     */
    function centerModel(THREE, root, targetLen, yaw) {
        root.updateMatrixWorld(true);
        var box = new THREE.Box3().setFromObject(root);
        var size = box.getSize(new THREE.Vector3());
        var center = box.getCenter(new THREE.Vector3());
        root.position.x -= center.x;
        root.position.y -= center.y;
        root.position.z -= center.z;
        root.traverse(function (o) {
            if (o.isMesh && o.material) {
                o.frustumCulled = false;
                if (o.material.map) o.material.map.anisotropy = 8;
                if ('envMapIntensity' in o.material) o.material.envMapIntensity = 1.0;
            }
        });
        var holder = new THREE.Group();
        holder.add(root);
        holder.scale.setScalar(targetLen / (Math.max(size.x, size.z) || 1));
        holder.rotation.y = yaw || 0;
        return holder;
    }

    function loadConvoy(THREE, urls, opts, onTruck, onDone) {
        var loader = new window.GLTFLoader();
        var holders = new Array(urls.length);
        var done = 0;
        urls.forEach(function (url, i) {
            loader.load(url, function (gltf) {
                try {
                    holders[i] = normalizeModel(THREE, gltf.scene, opts.targetLen,
                        (opts.yaw && opts.yaw[i]) || 0);
                    if (onTruck) onTruck(holders[i], i);
                } catch (e) {
                    if (window.console) console.error('[barlas-3d] normalize', e);
                }
                if (++done === urls.length && onDone) onDone(holders);
            }, undefined, function (err) {
                if (window.console) console.error('[barlas-3d] glb yüklenemedi', url, err);
                if (++done === urls.length && onDone) onDone(holders);
            });
        });
    }

    /* ------------------------- yol dokuları --------------------------- */

    function makeRoadTexture(THREE) {
        var c = document.createElement('canvas');
        c.width = 1024; c.height = 512;        /* u = yol boyu (X), v = en */
        var ctx = c.getContext('2d');

        /* Asfalt + hafif gren */
        ctx.fillStyle = '#10141c';
        ctx.fillRect(0, 0, 1024, 512);
        for (var i = 0; i < 900; i++) {
            ctx.fillStyle = 'rgba(255,255,255,' + (Math.random() * 0.03) + ')';
            ctx.fillRect(Math.random() * 1024, Math.random() * 512, 2, 2);
        }
        /* Kenar çizgileri */
        ctx.fillStyle = 'rgba(226,236,248,0.35)';
        ctx.fillRect(0, 54, 1024, 6);
        ctx.fillRect(0, 452, 1024, 6);
        /* Orta kesik şerit */
        ctx.fillStyle = 'rgba(226,236,248,0.5)';
        for (var x = 0; x < 1024; x += 170) {
            ctx.fillRect(x, 252, 90, 8);
        }

        var tex = new THREE.CanvasTexture(c);
        tex.wrapS = THREE.RepeatWrapping;
        tex.wrapT = THREE.ClampToEdgeWrapping;
        tex.repeat.set(4, 1);
        tex.anisotropy = 8;
        return tex;
    }

    /* --------------------- yol sahnesi (pin + scrub) ------------------- */

    function initRoad(section) {
        var THREE = window.THREE;
        var gsap = window.gsap;
        var ScrollTrigger = window.ScrollTrigger;
        gsap.registerPlugin(ScrollTrigger);

        var stage = section.querySelector('[data-road-stage]');
        if (!stage) return;

        /* Perf: yol arka plan sahnesi — antialias kapalı + düşük pixelRatio
           (retina'da GPU dolum yükünü büyük ölçüde azaltır, fark belli olmaz). */
        var renderer = new THREE.WebGLRenderer({ alpha: true, antialias: false });
        renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 1.2));
        if (THREE.sRGBEncoding !== undefined) renderer.outputEncoding = THREE.sRGBEncoding;
        renderer.toneMapping = THREE.ACESFilmicToneMapping;
        renderer.toneMappingExposure = 1.05;
        stage.appendChild(renderer.domElement);
        renderer.domElement.style.opacity = '0';   /* araç hazır olunca yumuşakça açılır */

        var scene = new THREE.Scene();
        scene.fog = new THREE.Fog(0x070d17, 16, 44);

        var camera = new THREE.PerspectiveCamera(36, 1, 0.1, 80);

        var pmrem = new THREE.PMREMGenerator(renderer);
        scene.environment = pmrem.fromEquirectangular(makeStudioEnv(THREE)).texture;
        pmrem.dispose();

        scene.add(new THREE.HemisphereLight(0xbfd6ee, 0x0b1220, 0.5));
        var key = new THREE.DirectionalLight(0xffffff, 1.0);
        key.position.set(5, 8, 6);
        scene.add(key);
        var rimL = new THREE.DirectionalLight(0x4aa3e6, 0.5);
        rimL.position.set(-7, 4, -6);
        scene.add(rimL);

        /* ---- Konvoy: gerçek GLB kamyonlar, art arda (-X yönüne sürer) ----
           GLB modellerinde ayrı teker yok; hareket hissini yolun akışı ve
           refüj direkleri verir. GLTFLoader yoksa / yükleme başarısızsa
           eski prosedürel çekici+tanker rig'ine düşülür. */
        var vehicle = new THREE.Group();
        scene.add(vehicle);
        var wheels = [];
        var vehicleBaseY = -1.0;      /* asfalt ~ y=-1.02; kamyon tabanı oturur */
        var vehicleReady = false;
        var canvasFade = 0;

        var CONVOY = {
            targetLen: 3.6,           /* her aracın dünya-birimi uzunluğu (eşit boy) */
            lane: 1.9,                /* şerit ofseti (|z|): orta çizgiden uzaklık */
            gap: 5.0,                 /* ön–arka mesafe (X) */
            baseY: -1.0,
            /* 4 model sırası: tanker-1, tanker-2, tanker-4, tanker-5.
               Hepsinin kabini -X (ileri) → 0. Bir araç hâlâ ters görünürse
               ilgili indise Math.PI yaz (önizlemedeki "Yön çevir" ile netleştir). */
            yaw: [0, 0, 0, 0],
            /* Dizilim: 2x2 konvoy — 2 önde + 2 arkada, iki şeritte. Orta çizgide
               (z=0) kimse durmaz. fx: -1 ön / +1 arka (gidiş -X), ln: -1/+1 şerit. */
            slots: [
                { fx: -1, ln: -1 },   /* ön sol  (tanker-1) */
                { fx: -1, ln:  1 },   /* ön sağ  (tanker-2) */
                { fx:  1, ln: -1 },   /* arka sol (tanker-4) */
                { fx:  1, ln:  1 }    /* arka sağ (tanker-5) */
            ]
        };
        var MODEL_URLS = (window.__BARLAS_MODELS && window.__BARLAS_MODELS.length)
            ? window.__BARLAS_MODELS
            : ['assets/models/tanker-1.glb', 'assets/models/tanker-2.glb',
               'assets/models/tanker-4.glb', 'assets/models/tanker-5.glb'];

        function fitConvoy(holders) {
            holders.forEach(function (h, k) {
                if (!h) return;
                var s = CONVOY.slots[k] || { fx: 0, ln: 0 };
                h.position.x = s.fx * CONVOY.gap / 2;   /* ön: - / arka: + (gidiş -X) */
                h.position.z = s.ln * CONVOY.lane;       /* şerit (orta çizgi dışı) */
                vehicle.add(h);
            });
            vehicleBaseY = CONVOY.baseY;
            vehicleReady = true;
        }

        function proceduralFallback() {
            var rig = buildProceduralRig(THREE);
            vehicle.add(rig.group);
            for (var i = 0; i < rig.wheels.length; i++) wheels.push(rig.wheels[i]);
            vehicleBaseY = 0.41;
            vehicleReady = true;
        }

        function startConvoyLoad() {
            if (!window.GLTFLoader) { proceduralFallback(); return; }
            loadConvoy(THREE, MODEL_URLS, CONVOY, null, function (holders) {
                if (holders.some(Boolean)) fitConvoy(holders);
                else proceduralFallback();
            });
        }

        /* Tembel yükleme: bölüm görünüme ~1 ekran kala başlat (rootMargin) */
        var loadKicked = false;
        function kickLoad() { if (loadKicked) return; loadKicked = true; startConvoyLoad(); }
        if ('IntersectionObserver' in window) {
            var lio = new IntersectionObserver(function (es) {
                if (es[0].isIntersecting) { kickLoad(); lio.disconnect(); }
            }, { rootMargin: '100% 0px' });
            lio.observe(stage);
        } else {
            kickLoad();
        }

        /* Yol */
        var roadTex = makeRoadTexture(THREE);
        var road = new THREE.Mesh(
            new THREE.PlaneGeometry(90, 9),
            new THREE.MeshStandardMaterial({ map: roadTex, roughness: 0.95, metalness: 0 })
        );
        road.rotation.x = -Math.PI / 2;
        road.position.y = -1.02;
        scene.add(road);

        /* Yol dışı zemin */
        var ground = new THREE.Mesh(
            new THREE.PlaneGeometry(120, 70),
            new THREE.MeshStandardMaterial({ color: 0x0a1019, roughness: 1 })
        );
        ground.rotation.x = -Math.PI / 2;
        ground.position.y = -1.06;
        scene.add(ground);

        /* Refüj direkleri: iki sıra, akar ve sarmalanır */
        var posts = [];
        var postGeo = new THREE.BoxGeometry(0.08, 0.85, 0.08);
        var postMat = new THREE.MeshStandardMaterial({ color: 0x2a3852, roughness: 0.8 });
        var capMat = new THREE.MeshStandardMaterial({ color: 0x4aa3e6, emissive: 0x1c4a73, emissiveIntensity: 0.6 });
        for (var i = 0; i < 16; i++) {
            [-5.6, 5.6].forEach(function (z) {
                var p = new THREE.Group();
                p.add(new THREE.Mesh(postGeo, postMat));
                var cap = new THREE.Mesh(new THREE.BoxGeometry(0.1, 0.08, 0.1), capMat);
                cap.position.y = 0.46;
                p.add(cap);
                p.position.set(-44 + i * 5.5 + (z > 0 ? 2.7 : 0), -0.62, z);
                scene.add(p);
                posts.push(p);
            });
        }

        /* Kamera keyframe'leri: yandan-önden → arka 3/4 → hafif üstten */
        var camKeys = [
            { pos: new THREE.Vector3(-7.5, 1.5, 8.0), look: new THREE.Vector3(-0.6, 0.2, 0) },
            { pos: new THREE.Vector3(6.0, 2.3, 7.0),  look: new THREE.Vector3(-0.4, 0.1, 0) },
            { pos: new THREE.Vector3(-2.5, 8.0, 4.0), look: new THREE.Vector3(-0.5, 0.0, 0) }
        ];
        var camPos = camKeys[0].pos.clone();
        var camLook = camKeys[0].look.clone();

        function lerpKeys(p) {
            var seg = Math.min(1.9999, p * 2);          /* 0..2 */
            var i0 = Math.floor(seg), t = seg - i0;
            /* yumuşatma */
            t = t * t * (3 - 2 * t);
            camPos.lerpVectors(camKeys[i0].pos, camKeys[i0 + 1].pos, t);
            camLook.lerpVectors(camKeys[i0].look, camKeys[i0 + 1].look, t);
        }

        /* Mesajlar */
        var caps = Array.prototype.slice.call(section.querySelectorAll('[data-road-cap]'));
        function updateCaps(p) {
            caps.forEach(function (cap, i) {
                var a = i / caps.length, b = (i + 1) / caps.length;
                cap.classList.toggle('is-on', p >= a && p < (i === caps.length - 1 ? 1.01 : b));
            });
        }

        /* Pin + scrub */
        var progress = 0, lastProgress = 0, active = false;
        ScrollTrigger.create({
            trigger: section,
            start: 'top top',
            end: '+=110%',
            pin: true,
            anticipatePin: 1,
            scrub: 0.5,
            onUpdate: function (self) { progress = self.progress; }
        });
        /* Pin, sayfaya ~1.7 ekranlık boşluk ekler; daha önce kurulmuş
           tetikleyicilerin (galeri, süreç vb.) konumları bunu bilmiyor.
           Hemen yenile ki alt bölümlerin animasyonları erken oynamasın. */
        ScrollTrigger.refresh();
        /* Görünürlük: pin payından etkilenmemesi için IntersectionObserver.
           Sahne ekranda olduğu sürece yol akmaya devam eder (TIR durmaz). */
        if ('IntersectionObserver' in window) {
            var io = new IntersectionObserver(function (entries) {
                active = entries[0].isIntersecting;
            }, { threshold: 0 });
            io.observe(stage);
        } else {
            active = true;
        }

        section.classList.add('road--3d');
        document.documentElement.classList.add('has-road');

        var ROAD_BASE_FOV = 48;
        function size() {
            var r = stage.getBoundingClientRect();
            var w = Math.max(1, r.width), h = Math.max(1, r.height);
            renderer.setSize(w, h, false);
            camera.aspect = w / h;
            /* Portrait/dar ekranda perspektif yatayda daralıp konvoyu büyütür.
               Dikey FOV'u artırarak yatay çerçevelemeyi koru → mobilde "çok
               büyük" görünmesin (üst sınır 82° ile aşırı balıkgözü engellenir). */
            if (camera.aspect < 1) {
                var halfH = Math.tan(ROAD_BASE_FOV * Math.PI / 360);
                camera.fov = Math.min(82, 2 * Math.atan(halfH / camera.aspect) * 180 / Math.PI);
            } else {
                camera.fov = ROAD_BASE_FOV;
            }
            camera.updateProjectionMatrix();
        }
        size();
        var rT;
        window.addEventListener('resize', function () {
            window.clearTimeout(rT);
            rT = window.setTimeout(size, 120);
        });
        /* Sıkışma kök çözümü: stage boyutu HER değiştiğinde (pin, mobil adres
           çubuğu, yönelim, düzen) anında yeniden boyutlandır. resize olayı
           bunların hepsini yakalamaz; ResizeObserver yakalar. */
        if ('ResizeObserver' in window) {
            new ResizeObserver(function () { size(); }).observe(stage);
        }

        /* Döngü: yol akar, tekerlekler döner, kamera keyframe'ler arasında süzülür */
        var running = true;
        document.addEventListener('visibilitychange', function () {
            running = !document.hidden;
            if (running) loop();
        });

        var clock = new THREE.Clock();

        function loop() {
            if (!running) return;
            window.requestAnimationFrame(loop);
            if (!active) return;

            var t = clock.getElapsedTime();

            /* Hız: temel akış + scroll katkısı */
            var dp = progress - lastProgress;
            lastProgress = progress;
            var speed = 0.085 + Math.min(0.35, Math.abs(dp) * 26);

            /* Yol dokusunu akıt (+X yönünde → araç -X'e gidiyormuş gibi) */
            roadTex.offset.x -= speed * 0.055;

            /* Direkler akar, başa sarar */
            for (var i = 0; i < posts.length; i++) {
                posts[i].position.x += speed;
                if (posts[i].position.x > 46) posts[i].position.x -= 90;
            }

            /* Tekerlekler (prosedürel yedekte döner; GLB konvoyda liste boş) */
            for (var w = 0; w < wheels.length; w++) {
                wheels[w].rotation.z += speed / 0.45;
            }

            /* Süspansiyon esnemesi + hafif yalpa (yaşam belirtisi) */
            vehicle.position.y = vehicleBaseY + Math.sin(t * 9) * 0.012;
            vehicle.rotation.z = Math.sin(t * 6.3) * 0.0035;

            /* Araç yüklenince canvas'ı yumuşakça aç (altta statik görsel bekler) */
            if (vehicleReady && canvasFade < 1) {
                canvasFade = Math.min(1, canvasFade + 0.04);
                renderer.domElement.style.opacity = canvasFade.toFixed(3);
            }

            /* Kamera */
            lerpKeys(progress);
            camera.position.copy(camPos);
            camera.lookAt(camLook);

            updateCaps(progress);

            /* Perf: yol sahnesi ~30fps render — hareket her karede güncellenir
               (hız değişmez), yalnızca GPU çizimi her 2. karede yapılır. */
            roadFrame++;
            if (roadFrame % 2 === 0) renderer.render(scene, camera);
        }
        var roadFrame = 0;
        loop();
    }

    /* ------------------------------ sahne ----------------------------- */

    function init(stage) {
        var THREE = window.THREE;

        /* Perf: hero showcase — AA açık (merkez sahne crisp kalsın) ama
           pixelRatio biraz düşürüldü (retina'da fill yükü azalır). */
        var renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
        renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 1.4));
        if (THREE.sRGBEncoding !== undefined) renderer.outputEncoding = THREE.sRGBEncoding;
        renderer.toneMapping = THREE.ACESFilmicToneMapping;
        renderer.toneMappingExposure = 1.05;
        /* Canvas sahne kutusundan geniş taşar; kopya/CTA tıklamalarını engellemesin */
        renderer.domElement.style.pointerEvents = 'none';
        stage.appendChild(renderer.domElement);

        var scene = new THREE.Scene();
        var camera = new THREE.PerspectiveCamera(34, 1, 0.1, 60);
        camera.position.set(0, 0.65, 8.6);
        camera.lookAt(0, -0.2, 0);

        var pmrem = new THREE.PMREMGenerator(renderer);
        scene.environment = pmrem.fromEquirectangular(makeStudioEnv(THREE)).texture;
        pmrem.dispose();

        scene.add(new THREE.HemisphereLight(0xbfd6ee, 0x0b1220, 0.5));
        var key = new THREE.DirectionalLight(0xffffff, 1.0);
        key.position.set(4, 7, 6);
        scene.add(key);
        var rimL = new THREE.DirectionalLight(0x4aa3e6, 0.55);
        rimL.position.set(-6, 3, -5);
        scene.add(rimL);

        /* Hero aktörü: gerçek GLB (tanker-3.glb). GLTFLoader yoksa ya da
           yükleme başarısızsa prosedürel tankere düşülür. Model hazır olana
           kadar canvas saydam kalır; altta statik görsel görünür (crossfade). */
        var HERO_TARGET_LEN = 6.4;   /* dünya-birimi uzunluk (prosedürel ~6.1) */
        var HERO_YAW = 0;            /* kabin yanlış yöne bakarsa Math.PI yap */
        var tanker = new THREE.Group();
        scene.add(tanker);
        var modelReady = false;

        var hero = document.querySelector('.hero');

        function useProcedural() {
            var t = buildTanker(THREE);
            t.scale.setScalar(0.96);
            tanker.add(t);
            modelReady = true;
        }

        var HERO_URL = window.__BARLAS_HERO_MODEL || null;
        if (window.GLTFLoader && HERO_URL) {
            try {
                new window.GLTFLoader().load(HERO_URL, function (gltf) {
                    try {
                        tanker.add(centerModel(THREE, gltf.scene, HERO_TARGET_LEN, HERO_YAW));
                        modelReady = true;
                    } catch (e) {
                        if (window.console) console.error('[barlas-3d] hero normalize', e);
                        useProcedural();
                    }
                }, undefined, function (err) {
                    if (window.console) console.error('[barlas-3d] hero glb yüklenemedi', err);
                    useProcedural();
                });
            } catch (e) {
                useProcedural();
            }
        } else {
            useProcedural();
        }

        /* Boyutlandırma: sahne kutusundan biraz GENİŞ render et (yanlara nefes
           payı). Dikey FOV sabit olduğundan modelin yüksekliği/boyutu değişmez;
           sadece yatay görüş alanı genişler → cursor orbit'te yanlardan kırpılmaz.
           Canvas ortalanarak taşar; model sahne sütununun ortasında kalır. */
        var SIDE_SCALE = 1.5;
        function size() {
            var r = stage.getBoundingClientRect();
            var h = Math.max(1, r.height);
            var baseW = Math.max(1, r.width);
            /* Dar ekranda (mobil/tablet) yan-taşma yerine tam genişlik kullan;
               aksi halde model 1.5x ölçeklenip "çok büyük" görünür. */
            var scale = (baseW < 992) ? 1.0 : SIDE_SCALE;
            var w = Math.round(baseW * scale);
            renderer.setSize(w, h, false);
            var el = renderer.domElement;
            el.style.width = w + 'px';
            el.style.height = h + 'px';
            el.style.top = '0px';
            el.style.left = Math.round((baseW - w) / 2) + 'px';
            el.style.right = 'auto';
            camera.aspect = w / h;
            camera.updateProjectionMatrix();
        }
        size();
        var rT;
        window.addEventListener('resize', function () {
            window.clearTimeout(rT);
            rT = window.setTimeout(size, 120);
        });
        if ('ResizeObserver' in window) {
            new ResizeObserver(function () { size(); }).observe(stage);
        }

        /* Cursor orbit + scroll'da yola dönüş */
        var pointer = { x: 0, y: 0 };
        var orbit = { ry: 0, rx: 0 };
        window.addEventListener('pointermove', function (e) {
            pointer.x = e.clientX / window.innerWidth - 0.5;
            pointer.y = e.clientY / window.innerHeight - 0.5;
        }, { passive: true });

        var pose = { ry: -0.55, o: 0 };
        if (window.gsap && window.ScrollTrigger && hero) {
            window.gsap.registerPlugin(window.ScrollTrigger);
            window.gsap.to(pose, {
                ry: -1.4,
                o: -0.9,            /* hero'dan çıkarken söner (aşağıda 1 + o) */
                ease: 'none',
                immediateRender: false,
                scrollTrigger: {
                    trigger: hero,
                    start: 'top top',
                    end: 'bottom 35%',
                    scrub: 0.6
                }
            });
        }

        var clock = new THREE.Clock();
        var running = true;
        var fadeIn = 0;
        var heroSwapped = false;

        document.addEventListener('visibilitychange', function () {
            running = !document.hidden;
            if (running) loop();
        });

        function loop() {
            if (!running) return;
            window.requestAnimationFrame(loop);

            var t = clock.getElapsedTime();
            /* Model (GLB ya da prosedürel yedek) hazır olunca aç. */
            if (modelReady) fadeIn = Math.min(1, fadeIn + 0.04);

            /* Canvas yeterince açıldığında statik görseli gizle (crossfade). */
            if (!heroSwapped && fadeIn > 0.6 && hero) {
                hero.classList.add('has-3d');
                heroSwapped = true;
            }

            orbit.ry += ((pointer.x * 0.55) - orbit.ry) * 0.06;
            orbit.rx += ((pointer.y * 0.16) - orbit.rx) * 0.06;

            tanker.rotation.y = pose.ry + orbit.ry;
            tanker.rotation.x = 0.05 + orbit.rx;
            tanker.position.y = -0.15 + Math.sin(t * 0.85) * 0.05;

            var opacity = Math.max(0, Math.min(1, fadeIn * (1 + pose.o)));
            renderer.domElement.style.opacity = opacity.toFixed(3);
            if (opacity > 0.005) renderer.render(scene, camera);
        }
        loop();
    }
})();
