/**
 * contact-tanker.js — İletişim sayfası etkileşimi
 *
 * 1) 3D sahne (sol, büyük): prosedürel çekici + tanker (yeni-tanker.js'teki
 *    yapıcıların bağımsız kopyaları — ana sayfa dokunulmasın diye) sağdan
 *    girer, tekerlekler döner, fren yapıp süspansiyonla yaylanarak park eder.
 *    Varınca sağdaki formu "getirir" (form gizli başlar, TIR varınca kayar).
 *    Form gönderilince TIR yükü alıp yola çıkar; sahnede "yola çıktı" onayı belirir.
 * 2) Form (sağ): gerçek POST (Contact::submit) — fetch ile gönderilir.
 *
 * Aşamalı geliştirme: THREE/WebGL yoksa, ekran < 992px ise veya reduced-motion
 * açıksa 3D hiç başlamaz; SVG yedeği görünür, form sabit görünür kalır.
 * fetch yoksa form klasik POST ile çalışır (sunucu redirect + flashdata).
 */
(function () {
    'use strict';

    var MIN_WIDTH = 992;
    var convoyRevealed = false;

    function onReady(fn) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fn, { once: true });
        } else {
            fn();
        }
    }

    function reducedMotion() {
        return window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    }

    function webglOk() {
        try {
            var c = document.createElement('canvas');
            return !!(window.WebGLRenderingContext &&
                (c.getContext('webgl') || c.getContext('experimental-webgl')));
        } catch (e) { return false; }
    }

    /* Güvenlik/yedek: konvoyu animasyonsuz yerine koyar (gsap yoksa,
       sahne başlamazsa ya da süre dolarsa). */
    function revealConvoy() {
        if (convoyRevealed) return;
        convoyRevealed = true;
        var convoy = document.querySelector('[data-convoy]');
        if (!convoy) return;
        if (!document.documentElement.classList.contains('contact-deliver')) return; // zaten yerinde
        convoy.classList.add('is-delivered');
        convoy.style.transform = '';
    }

    onReady(function () {
        var form = document.querySelector('[data-contact-form]');
        if (form) initForm(form);

        initHours();

        // Güvenlik ağı: 3D teslimat hiç başlamazsa konvoyu yine de yerine koy.
        // Drive-in artık modeli bekliyor (en geç 4 sn'de prosedürel TIR'la başlar
        // ve convoyRevealed'i kilitler), bu yüzden bu ağ ondan SONRA (6 sn) devreye
        // girsin; aksi halde formu erken yerine koyup drive-in ile çakışırdı.
        if (document.documentElement.classList.contains('contact-deliver')) {
            window.setTimeout(revealConvoy, 6000);
        }

        // 3D TIR sahnesi geri açıldı: aşağıdaki initScene konvoyu sürer ve
        // süspansiyon yaylanmasıyla formu getirir. reduced-motion / WebGL yoksa
        // aşağıdaki kontroller sahneyi başlatmaz; SVG yedeği görünür kalır.
        var stage = document.querySelector('[data-contact-stage]');
        if (!stage) return;

        window.setTimeout(function () {
            // Mobilde 3D YOK (kullanıcı isteği): dar ekranda sahne hiç başlatılmaz —
            // SVG yedeği + statik form gösterilir, WebGL bağlamı hiç açılmaz.
            if (window.innerWidth < MIN_WIDTH) return;
            if (reducedMotion()) return;
            if (!window.THREE || !webglOk()) return;
            var mount = stage.querySelector('[data-stage-mount]');
            if (!mount) return;
            try {
                initScene(stage, mount);
            } catch (err) {
                revealConvoy();
                if (window.console && console.error) console.error('[barlas-contact]', err);
            }
        }, 0);
    });

    /* ===================================================================
       DOKU YARDIMCILARI (yeni-tanker.js ile aynı görünüm)
       =================================================================== */

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

    /* ===================================================================
       PROSEDÜREL TANKER DORSE
       =================================================================== */

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

        g.userData.wheels = wheels;
        return g;
    }

    /* ===================================================================
       PROSEDÜREL ÇEKİCİ (KAMYON KAFASI)
       =================================================================== */

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

        add(new THREE.BoxGeometry(3.0, 0.16, 1.0), dark, 0.1, -1.05, 0);
        add(new THREE.CylinderGeometry(0.5, 0.5, 0.08, 24), steelDark, 0.9, -0.95, 0);

        add(new THREE.BoxGeometry(1.5, 1.75, 1.9), paint, -0.85, 0.05, 0);
        var spoiler = add(new THREE.BoxGeometry(1.0, 0.5, 1.7), paint, -0.62, 1.12, 0);
        spoiler.rotation.z = -0.35;
        var ws = add(new THREE.BoxGeometry(0.06, 0.78, 1.62), glass, -1.62, 0.42, 0);
        ws.rotation.z = -0.12;
        add(new THREE.BoxGeometry(0.9, 0.5, 0.02), glass, -0.95, 0.45, 0.96);
        add(new THREE.BoxGeometry(0.9, 0.5, 0.02), glass, -0.95, 0.45, -0.96);
        add(new THREE.BoxGeometry(0.18, 0.5, 1.9), dark, -1.66, -0.78, 0);
        add(new THREE.BoxGeometry(0.05, 0.4, 1.3), steelDark, -1.72, -0.18, 0);
        var lamp = new THREE.MeshStandardMaterial({ color: 0xbfd9f2, emissive: 0x9fc6ef, emissiveIntensity: 0.7, roughness: 0.3 });
        add(new THREE.BoxGeometry(0.06, 0.12, 0.3), lamp, -1.7, -0.55, 0.62);
        add(new THREE.BoxGeometry(0.06, 0.12, 0.3), lamp, -1.7, -0.55, -0.62);
        add(new THREE.CylinderGeometry(0.05, 0.05, 1.5, 10), steelDark, 0.0, 0.1, 0.85);
        add(new THREE.BoxGeometry(0.04, 0.3, 0.16), dark, -1.5, 0.75, 1.05);
        add(new THREE.BoxGeometry(0.04, 0.3, 0.16), dark, -1.5, 0.75, -1.05);

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

    /* ===================================================================
       GLB NORMALİZE: modeli XZ'de ortala, tabanı gölge düzlemine indir,
       en uzun yatay eksene göre hedef boya ölçekle, gerekirse Y'de döndür.
       Bir "holder" döner; rig bunu içerir, yaylanma rig'e uygulanır.
       =================================================================== */

    function normalizeContactModel(THREE, root, targetLen, yaw) {
        root.updateMatrixWorld(true);
        var box = new THREE.Box3().setFromObject(root);
        var size = box.getSize(new THREE.Vector3());
        var center = box.getCenter(new THREE.Vector3());
        root.position.x -= center.x;
        root.position.z -= center.z;
        root.position.y -= box.min.y;            /* taban -> y=0 */
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
        holder.position.y = -1.12;               /* taban, gölge düzlemine otursun */
        return holder;
    }

    /* ===================================================================
       SAHNE: TIR sağdan girer → frenler → yaylanır → park → formu getirir
       =================================================================== */

    function initScene(stage, mount) {
        var THREE = window.THREE;
        var gsap = window.gsap;
        var stageDone = stage.querySelector('[data-stage-done]');

        var renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
        renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 1.75));
        if (THREE.sRGBEncoding !== undefined) renderer.outputEncoding = THREE.sRGBEncoding;
        renderer.toneMapping = THREE.ACESFilmicToneMapping;
        renderer.toneMappingExposure = 1.06;
        renderer.domElement.style.pointerEvents = 'none';
        /* Canvas baştan gizli: üstte SVG yedeği görünür; 3D hazır olunca (startDriveIn)
           canvas fade-in + SVG fade-out ile crossfade yapılır. Böylece başta yalnızca
           SVG araç görünür, boş kutu/gölge sızıntısı olmaz. */
        renderer.domElement.style.opacity = '0';
        mount.appendChild(renderer.domElement);

        var scene = new THREE.Scene();
        var camera = new THREE.PerspectiveCamera(36, 1, 0.1, 80);

        var pmrem = new THREE.PMREMGenerator(renderer);
        scene.environment = pmrem.fromEquirectangular(makeStudioEnv(THREE)).texture;
        pmrem.dispose();

        scene.add(new THREE.HemisphereLight(0xbfd6ee, 0x0b1220, 0.55));
        var key = new THREE.DirectionalLight(0xffffff, 1.05);
        key.position.set(-4, 7, 7);
        scene.add(key);
        var rimL = new THREE.DirectionalLight(0x4aa3e6, 0.55);
        rimL.position.set(7, 3, -5);
        scene.add(rimL);

        /* Aktör: gerçek GLB kamyon (tanker-1.glb). Yaylanma, park ve çıkış
           zaman çizelgesinin tamamı 'rig' grubuna uygulanır; bu yüzden ister
           GLB ister prosedürel yedek olsun aynı şekilde çalışır. GLB'de ayrı
           teker yoktur (giriş kayışıyla dönmez) — yaylanma korunur. */
        var rig = new THREE.Group();
        var wheels = [];
        var WHEEL_R = 0.32;

        var shadow = new THREE.Mesh(
            new THREE.PlaneGeometry(12, 3.4),
            new THREE.MeshBasicMaterial({ map: makeShadow(THREE), transparent: true, depthWrite: false, opacity: 0.9 })
        );
        shadow.rotation.x = -Math.PI / 2;
        shadow.position.set(0.1, -1.12, 0);
        rig.add(shadow);

        var BASE_Y = 0.5;
        var REST_X = 0.0;
        rig.position.set(REST_X, BASE_Y, 0);   // TIR dünyada sabit; "sürüş" konvoyu DOM olarak kayar
        scene.add(rig);

        /* Araç + form BERABER gelsin: drive-in animasyonu, aracın (GLB ya da
           prosedürel yedek) sahnede HAZIR olmasını bekler. modelLoaded olunca
           ve sahne görünürken maybeStart() konvoyu içeri sürer. */
        var modelLoaded = false, rigBuilt = false, started = false, inView = false;
        function markReady() {
            if (modelLoaded) return;
            modelLoaded = true;
            maybeStart();
        }

        /* GLTFLoader yoksa / yükleme başarısızsa eski prosedürel çekici+tanker */
        function buildProceduralRig() {
            if (rigBuilt) return;
            rigBuilt = true;
            var trailer = buildTanker(THREE);
            trailer.position.set(1.6, 0.32, 0);
            rig.add(trailer);
            var tractor = buildTractor(THREE);
            tractor.position.set(-2.55, 0.32, 0);
            rig.add(tractor);
            rig.scale.setScalar(1.16);
            wheels = trailer.userData.wheels.concat(tractor.userData.wheels);
            markReady();
        }

        var CONTACT_URL = window.__BARLAS_CONTACT_MODEL || null;
        var CONTACT_YAW = 0;   /* kabin yanlış yöne bakarsa Math.PI yap */
        if (window.GLTFLoader && CONTACT_URL) {
            try {
                new window.GLTFLoader().load(CONTACT_URL, function (gltf) {
                    if (rigBuilt) return;   /* 4 sn güvenliği prosedürele düştüyse atla */
                    try {
                        rigBuilt = true;
                        rig.add(normalizeContactModel(THREE, gltf.scene, 9.6, CONTACT_YAW));
                        markReady();
                    } catch (e) {
                        if (window.console) console.error('[barlas-contact] normalize', e);
                        rigBuilt = false;
                        buildProceduralRig();
                    }
                }, undefined, function (err) {
                    if (window.console) console.error('[barlas-contact] glb yüklenemedi', err);
                    buildProceduralRig();
                });
            } catch (e) {
                buildProceduralRig();
            }
        } else {
            buildProceduralRig();
        }

        /* Güvenlik: model 4 sn'de gelmezse prosedürel TIR'la başla — boş sahneyle
           drive-in yapma (form yine araçla beraber gelsin). */
        window.setTimeout(function () { buildProceduralRig(); }, 4000);

        /* Konvoy (sahne + çeki demiri + form) tek parça olarak sağdan içeri sürülür.
           Tanker sabit kalır; tekerlekler bu kaymayla senkron döner → "çekiyor" hissi. */
        var convoyEl = document.querySelector('[data-convoy]');
        var deliver = !!convoyEl && document.documentElement.classList.contains('contact-deliver');
        var OFF = Math.max(560, Math.round(window.innerWidth * 0.62));
        var conv = { x: 0 };
        var prevConvX = 0;
        function applyConvoy() { if (convoyEl) convoyEl.style.transform = 'translateX(' + conv.x + 'px)'; }

        var camTarget = new THREE.Vector3(-0.5, 0.65, 0);
        function frame() {
            var r = mount.getBoundingClientRect();
            var w = Math.max(1, r.width), h = Math.max(1, r.height);
            renderer.setSize(w, h, false);
            var aspect = w / h;
            camera.aspect = aspect;
            var dist = aspect < 1.5 ? 11.5 * (1.5 / aspect) : 11.5;
            dist = Math.min(dist, 24);
            camera.position.set(-6.2, 2.1, dist);
            camera.lookAt(camTarget);
            camera.updateProjectionMatrix();
        }
        frame();
        var rT;
        window.addEventListener('resize', function () {
            window.clearTimeout(rT);
            rT = window.setTimeout(frame, 120);
        });

        /* Canvas başta gizli (opacity 0); startDriveIn'de 3D render'a hazır olunca
           yumuşakça belirir. (SVG placeholder kaldırıldığı için crossfade gerekmez.) */
        var prevX = REST_X;
        var arrived = false;
        var leaving = false;
        var wheelAngle = 0;

        function startDriveIn() {
            var hitchEl = convoyEl ? convoyEl.querySelector('[data-hitch]') : null;

            if (!gsap || !deliver) {
                if (deliver) revealConvoy();   // gsap yoksa: yerinde göster
                arrived = true;
                return;
            }

            /* ARAÇ + FORM BERABER GELİR: TIR gizliydi (canvas opacity 0); burada
               canvas belirir, TIR görünür şekilde sağdan içeri sürer (sadece yaylanma
               değil), süspansiyonla oturur ve AYNI ANDA form sağdan kayarak "teslim"
               edilir (CSS: .contact-convoy.is-delivered .contact-form). Form transform'u
               sürerken cam blur'u kapatılır (CSS: .is-driving .contact-form) → jank yok;
               form oturunca (~1.05 sn) blur geri gelir. Çeki demiri ikisini bağlar. */
            var canvas = renderer.domElement;
            convoyRevealed = true;                  // güvenlik zamanlayıcısı devreye girmesin
            convoyEl.classList.add('is-driving');    // form girişi boyunca cam blur kapalı
            convoyEl.classList.add('is-delivered');  // formu sağdan kaydırarak getirir

            var tl = gsap.timeline({ onComplete: function () { arrived = true; } });
            /* Cam blur'u form oturur oturmaz (~1.05 sn) geri getir. TIR'ın elastik
               yaylanması ~2.5 sn sürer ama form çoktan yerindedir; blur'u o kadar
               bekletirsek geç ve fark edilir bir "blur snap" olur. */
            gsap.delayedCall(1.05, function () { convoyEl.classList.remove('is-driving'); });

            /* TIR canvas'ı yumuşakça belirir */
            tl.to(canvas, { opacity: 1, duration: 0.7, ease: 'power2.out' }, 0);

            /* TIR görünür şekilde sağdan içeri sürer — tekerlekler döner */
            gsap.set(rig.position, { x: REST_X + 4.2 });
            tl.to(rig.position, { x: REST_X, duration: 1.25, ease: 'power3.out' }, 0);

            /* Durunca: süspansiyon yaylanması */
            tl.to(rig.rotation, { z: 0.06, duration: 0.2, ease: 'power2.out' }, 1.1);
            tl.to(rig.position, { y: BASE_Y - 0.08, duration: 0.2, ease: 'power2.out' }, 1.1);
            tl.to(rig.rotation, { z: 0, duration: 1.2, ease: 'elastic.out(1, 0.3)' }, 1.3);
            tl.to(rig.position, { y: BASE_Y, duration: 1.2, ease: 'elastic.out(1, 0.28)' }, 1.3);

            /* Çeki demiri gerilir (görünür → elastik gerilme), form gelişiyle senkron */
            if (hitchEl) {
                gsap.set(hitchEl, { transformOrigin: 'right center' });
                tl.fromTo(hitchEl, { scaleX: 0.2 }, {
                    scaleX: 1, duration: 1.0, ease: 'elastic.out(1, 0.5)',
                    onComplete: function () { gsap.set(hitchEl, { clearProps: 'transform' }); }
                }, 0.55);
            }
        }

        /* Gönderildi: TIR yükü alıp yola çıkar, sahnede onay belirir */
        document.addEventListener('contact:sent', function () {
            if (leaving) return;
            leaving = true;
            arrived = false;
            stage.classList.remove('is-arrived');
            if (!gsap) {
                rig.position.x = -30;
                renderer.domElement.style.opacity = '0';
                if (stageDone) stage.classList.add('is-done');
                return;
            }
            var tl = gsap.timeline();
            tl.to(rig.position, { x: REST_X + 0.6, duration: 0.22, ease: 'power2.out' })   // hafif geri yaslan
              .to(rig.rotation, { z: -0.05, duration: 0.22, ease: 'power2.out' }, '<')      // burun kalkar (kalkış)
              .to(rig.position, { x: -30, duration: 1.5, ease: 'power2.in' })               // hızlanarak yola çık
              .to(rig.rotation, { z: 0, duration: 0.7, ease: 'power2.out' }, '<')
              .to(renderer.domElement, { opacity: 0, duration: 1.0, ease: 'power1.in' }, '<0.35');
            gsap.delayedCall(0.85, function () { if (stageDone) stage.classList.add('is-done'); });
        }, { once: true });

        /* Başlatma koşulu: sahne görünür VE araç hazır (beraber gelsinler).
           maybeStart iki koşul da sağlanınca drive-in'i bir kez tetikler. */
        function maybeStart() {
            if (started || !inView || !modelLoaded) return;
            started = true;
            /* Başlangıç takılması: yeni eklenen TIR materyallerinin shader DERLEMESİ +
               geometri/doku GPU UPLOAD'u ilk render'da pahalıdır; drive-in'in ilk
               karesiyle çakışınca takılır. Canvas'ı önce GİZLE (opacity 0 → belirme
               flaş'ı yok), sonra derle + warm-up render → ağır iş görünmez yapılır.
               İki kare sonra startDriveIn canvas'ı yumuşakça 0→1 açar → akıcı giriş. */
            renderer.domElement.style.opacity = '0';
            try { renderer.compile(scene, camera); } catch (e) {}
            renderer.render(scene, camera);
            window.requestAnimationFrame(function () {
                window.requestAnimationFrame(startDriveIn);
            });
        }
        function begin() { inView = true; maybeStart(); }
        if ('IntersectionObserver' in window) {
            var io = new IntersectionObserver(function (entries) {
                if (entries[0].isIntersecting) { begin(); io.disconnect(); }
            }, { threshold: 0.15 });
            io.observe(stage);
        } else {
            begin();
        }

        var running = true;
        var clock = new THREE.Clock();
        document.addEventListener('visibilitychange', function () {
            running = !document.hidden;
            if (running) loop();
        });

        function loop() {
            if (!running) return;
            window.requestAnimationFrame(loop);

            var t = clock.getElapsedTime();

            var dxConv = conv.x - prevConvX;
            prevConvX = conv.x;
            var dxWorld = rig.position.x - prevX;
            prevX = rig.position.x;
            wheelAngle -= dxConv * 0.013;       // konvoy DOM kayışı (giriş)
            wheelAngle -= dxWorld / WHEEL_R;    // dünya sürüşü (çıkış)
            for (var i = 0; i < wheels.length; i++) wheels[i].rotation.z = wheelAngle;

            if (arrived && !leaving) {
                rig.position.y = BASE_Y + Math.sin(t * 1.6) * 0.012;
                rig.rotation.z = Math.sin(t * 1.2) * 0.004;
            }

            renderer.render(scene, camera);
        }
        loop();
    }

    /* ===================================================================
       FORM: gerçek POST (fetch) + doğrulama + başarı durumu
       =================================================================== */

    function initForm(form) {
        var statusEl = form.querySelector('.contact-form__status');
        var endpoint = form.getAttribute('action') || window.location.href;
        var msgs = {
            name: form.dataset.msgName || 'Required',
            email: form.dataset.msgEmail || 'Invalid email',
            message: form.dataset.msgMessage || 'Required',
            generic: form.dataset.msgGeneric || 'Please check the form.'
        };
        var EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        var KEYS = ['name', 'email', 'phone', 'company', 'subject', 'message'];

        function el(name) { return form.elements && form.elements[name] ? form.elements[name] : null; }
        function val(name) { var e = el(name); return e ? ('' + e.value).trim() : ''; }
        function fieldWrap(key) { return form.querySelector('[data-field="' + key + '"]'); }
        function errBox(key) { return form.querySelector('[data-error-for="' + key + '"]'); }
        function setErr(key, msg) {
            var w = fieldWrap(key), b = errBox(key);
            if (b) b.textContent = msg || '';
            if (w) w.classList.toggle('is-invalid', !!msg);
        }
        function clearErrs() { KEYS.forEach(function (k) { setErr(k, ''); }); }

        function validate() {
            clearErrs();
            var firstBad = null;
            function bad(key, message) { setErr(key, message); if (!firstBad) firstBad = key; }

            if (val('name').length < 2) bad('name', msgs.name);
            if (!EMAIL_RE.test(val('email'))) bad('email', msgs.email);
            if (val('message').length < 10) bad('message', msgs.message);

            if (firstBad) {
                var f = el(firstBad);
                if (f && f.focus) { try { f.focus(); } catch (e) {} }
            }
            return !firstBad;
        }

        KEYS.forEach(function (k) {
            var e = el(k);
            if (!e) return;
            var ev = (e.type === 'checkbox' || e.tagName === 'SELECT') ? 'change' : 'input';
            e.addEventListener(ev, function () { setErr(k, ''); });
        });

        if (!window.fetch || !window.FormData) return; // klasik POST'a izin ver

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            if (form.classList.contains('is-loading')) return;
            if (statusEl) statusEl.textContent = '';

            if (!validate()) {
                if (statusEl) statusEl.textContent = msgs.generic;
                return;
            }

            form.classList.add('is-loading');

            fetch(endpoint, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: new FormData(form),
                credentials: 'same-origin'
            }).then(function (r) {
                return r.json().then(function (j) { return { status: r.status, body: j }; })
                    .catch(function () { return { status: r.status, body: null }; });
            }).then(function (res) {
                form.classList.remove('is-loading');
                var body = res.body || {};

                if (body.ok) {
                    if (body.csrf && body.csrf.name) {
                        var ci = form.querySelector('[name="' + body.csrf.name + '"]');
                        if (ci) ci.value = body.csrf.hash;
                    }
                    if (statusEl) statusEl.textContent = body.message || '';
                    // Mesaj TIR'a "yüklenir" → TIR yola çıkar → başarı belirir
                    form.classList.add('is-sending');
                    document.dispatchEvent(new CustomEvent('contact:sent'));
                    window.setTimeout(function () {
                        form.classList.remove('is-sending');
                        form.classList.add('is-sent');
                        var title = form.querySelector('.contact-form__success-title');
                        if (title) { title.setAttribute('tabindex', '-1'); try { title.focus(); } catch (e) {} }
                    }, 430);
                    return;
                }

                if (res.status === 422 && body.errors) {
                    var first = null;
                    Object.keys(body.errors).forEach(function (k) {
                        setErr(k, body.errors[k]);
                        if (!first) first = k;
                    });
                    if (statusEl) statusEl.textContent = body.message || msgs.generic;
                    var fe = first && el(first);
                    if (fe && fe.focus) { try { fe.focus(); } catch (e) {} }
                    return;
                }

                if (statusEl) statusEl.textContent = (body && body.message) || msgs.generic;
            }).catch(function () {
                form.classList.remove('is-loading');
                if (statusEl) statusEl.textContent = msgs.generic;
            });
        });

        var resetBtn = form.querySelector('[data-reset]');
        if (resetBtn) {
            resetBtn.addEventListener('click', function () {
                form.classList.remove('is-sent');
                form.reset();
                clearErrs();
                if (statusEl) statusEl.textContent = '';
                var n = el('name');
                if (n && n.focus) { try { n.focus(); } catch (e) {} }
            });
        }
    }

    /* ===================================================================
       ÇALIŞMA SAATLERİ ROZETİ (artık sayfada yoksa sessizce atlar)
       =================================================================== */

    function initHours() {
        var badge = document.querySelector('[data-hours-badge]');
        if (!badge) return;
        var openLabel = badge.getAttribute('data-open');
        var closedLabel = badge.getAttribute('data-closed');
        if (!openLabel || !closedLabel) return;

        var now = new Date();
        var day = now.getDay();
        var mins = now.getHours() * 60 + now.getMinutes();
        var open = false;
        if (day >= 1 && day <= 5) open = mins >= 510 && mins < 1080;
        else if (day === 6) open = mins >= 540 && mins < 840;

        badge.textContent = open ? openLabel : closedLabel;
        badge.classList.add(open ? 'is-open' : 'is-closed');
        badge.hidden = false;
    }
})();
