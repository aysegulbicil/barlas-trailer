/**
 * hero-cinematic.js — tam ekran sinematik 3D hero (yeni arayüz)
 * =============================================================================
 * Barlas ana sayfasının giriş deneyimi. Mevcut yapı BOZULMADAN eklendi:
 * eski "tshow" showcase hero'su (yeni-tanker.js → initShowcase) ve .tshow CSS
 * yerinde/çalışır durur; bu modül yalnızca [data-hero-cine] varsa devreye girer.
 * Beğenilmezse home.php'de tek satır include geri alınır.
 *
 * Deneyim:
 *   - Hero tamamen canlı bir 3D canvas (tanker-3.glb, hero ile AYNI model).
 *   - Kamera nefes alır (mouse parallax + idle); model mikro hareket eder.
 *     (Modeller tek-mesh olduğu için teker/süspansiyon bağımsız DÖNMEZ; canlılık
 *      tüm-model salınım + ışık/yansıma ile verilir — bkz. proje notu.)
 *   - Scroll'da hero PIN'lenir; ortada ince ışık çizgisi doğar, sahne ORTADAN
 *     İKİYE ayrılır (fiziksel kapı yok — ekranın kendisi yarılır), kamera öne
 *     dalar, açılan boşluktan arkadaki "giriş" bölümü görünür → pin kalkar.
 *
 * Teknik:
 *   - Masaüstü (güçlü): sahne bir WebGLRenderTarget'a çizilir; tam-ekran quad +
 *     "split" shader iki yarıyı ayırır, ortadaki boşluğu ŞEFFAF bırakır → canvas
 *     alpha'sı sayesinde arkadaki DOM (giriş bölümü) sızar. Tek ekstra geçiş.
 *   - Mobil / kaba işaretçi / düşük güç: render-target YOK. Sahne doğrudan
 *     çizilir; "yarılma" CSS ile taklit edilir (dolly + fade + ışık çizgisi).
 *   - reduced-motion veya WebGL yok: hiç 3D yok; hero statik okunur kalır.
 *
 * Kütüphaneler layout'ta zaten yüklü: THREE 0.149 (ESM→window.THREE),
 * window.GLTFLoader (DRACO'lu), GSAP + ScrollTrigger. Yeni bağımlılık yok.
 */
(function () {
    'use strict';

    /* ----------------------------- yardımcılar ----------------------------- */

    function prefersReduced() {
        try { return window.matchMedia('(prefers-reduced-motion: reduce)').matches; }
        catch (e) { return false; }
    }

    function webglOk() {
        try {
            var c = document.createElement('canvas');
            return !!(window.WebGLRenderingContext &&
                (c.getContext('webgl') || c.getContext('experimental-webgl')));
        } catch (e) { return false; }
    }

    /* Düşük-güç yolu (shader yok): mobil, dokunmatik ya da dar ekran. */
    function isLowPower() {
        try {
            return window.matchMedia('(max-width: 860px)').matches ||
                   window.matchMedia('(pointer: coarse)').matches;
        } catch (e) { return false; }
    }

    function clamp01(v) { return v < 0 ? 0 : (v > 1 ? 1 : v); }
    function smooth(a, b, x) { var t = clamp01((x - a) / (b - a)); return t * t * (3 - 2 * t); }
    function lerp(a, b, t) { return a + (b - a) * t; }

    /* Parlak stüdyo env — PBR yansımaları besler (yeni-tanker.js reçetesinden,
       biraz zenginleştirildi). */
    function makeStudioEnv(THREE) {
        var c = document.createElement('canvas');
        c.width = 1024; c.height = 512;
        var ctx = c.getContext('2d');
        var sky = ctx.createLinearGradient(0, 0, 0, 512);
        sky.addColorStop(0, '#3a4a66');
        sky.addColorStop(0.5, '#1b2436');
        sky.addColorStop(1, '#05080f');
        ctx.fillStyle = sky;
        ctx.fillRect(0, 0, 1024, 512);
        function softbox(x, y, w, h, color, a) {
            ctx.save();
            ctx.globalAlpha = a;
            ctx.filter = 'blur(16px)';
            ctx.fillStyle = color;
            ctx.fillRect(x, y, w, h);
            ctx.restore();
        }
        softbox(60, 40, 380, 96, '#ffffff', 1.0);
        softbox(540, 60, 440, 80, '#eaf2ff', 0.95);
        softbox(300, 175, 260, 48, '#bcd6f5', 0.7);
        softbox(120, 300, 160, 34, '#4aa3e6', 0.4);
        softbox(0, 456, 1024, 58, '#223049', 0.9);
        var tex = new THREE.CanvasTexture(c);
        tex.mapping = THREE.EquirectangularReflectionMapping;
        return tex;
    }

    /* Yumuşak kontakt gölge (radyal gradient sprite) — modeli zemine oturtur. */
    function makeContactShadow(THREE) {
        var c = document.createElement('canvas');
        c.width = 256; c.height = 256;
        var ctx = c.getContext('2d');
        var g = ctx.createRadialGradient(128, 128, 8, 128, 128, 128);
        g.addColorStop(0, 'rgba(0,0,0,0.55)');
        g.addColorStop(0.55, 'rgba(0,0,0,0.28)');
        g.addColorStop(1, 'rgba(0,0,0,0)');
        ctx.fillStyle = g;
        ctx.fillRect(0, 0, 256, 256);
        var tex = new THREE.CanvasTexture(c);
        var mat = new THREE.MeshBasicMaterial({ map: tex, transparent: true, depthWrite: false });
        var mesh = new THREE.Mesh(new THREE.PlaneGeometry(1, 1), mat);
        mesh.rotation.x = -Math.PI / 2;
        return mesh;
    }

    /* Modeli her eksende ortala + en uzun yatay eksene göre ölçekle
       (yeni-tanker.js centerModel ile aynı, kanıtlanmış matematik). */
    function centerModel(THREE, root, targetLen) {
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
                if ('envMapIntensity' in o.material) o.material.envMapIntensity = 1.15;
            }
        });
        var holder = new THREE.Group();
        holder.add(root);
        var s = targetLen / (Math.max(size.x, size.z) || 1);
        holder.scale.setScalar(s);
        holder.userData.footprint = { x: size.x * s, z: size.z * s };
        holder.userData.halfHeight = (size.y * s) / 2;   // yere oturtmak için taban
        return holder;
    }

    /* --------------------------- split shader --------------------------- */

    var SPLIT_VERT = [
        'varying vec2 vUv;',
        'void main(){ vUv = uv; gl_Position = vec4(position.xy, 0.0, 1.0); }'
    ].join('\n');

    /* Sahne dokusunu ortadan ikiye böler; iki yarı dışa kayar, ortadaki boşluk
       tamamen ŞEFFAF olur (alpha 0) → arkadaki DOM görünür. Kesim kenarlarında
       ince ışık çizgisi (seam). Çıktı premultiplied (renderer premultipliedAlpha). */
    var SPLIT_FRAG = [
        'precision highp float;',
        'uniform sampler2D tDiffuse;',
        'uniform float uSplit;',      // 0..1 açıklık oranı
        'uniform float uSeam;',       // 0..1 ışık çizgisi yoğunluğu
        'uniform float uAspect;',     // en/boy → çizgi kalınlığını düzeltmek için
        'uniform vec3  uSeamColor;',
        'varying vec2 vUv;',
        'void main(){',
        '  float halfx = 0.5;',
        '  float shift = uSplit * 0.5;',
        '  vec2 uv = vUv;',
        '  float mask = 1.0;',       // 1 = panel, 0 = boşluk
        '  vec2 src = uv;',
        '  if(uv.x < halfx){',
        '    src.x = uv.x + shift;',
        '    if(src.x > halfx) mask = 0.0;',
        '  } else {',
        '    src.x = uv.x - shift;',
        '    if(src.x < halfx) mask = 0.0;',
        '  }',
        '  vec4 col = texture2D(tDiffuse, src);',
        '  vec3 rgb = col.rgb * mask;',
        '  float a = mask;',
        '  float dl = abs(uv.x - (halfx - shift));',
        '  float dr = abs(uv.x - (halfx + shift));',
        '  float d = min(dl, dr) * uAspect;',
        '  float core = smoothstep(0.0035, 0.0, d);',
        '  float halo = smoothstep(0.06, 0.0, d) * 0.45;',
        '  float seam = (core + halo) * uSeam;',
        '  rgb += uSeamColor * seam;',
        '  a = clamp(a + seam, 0.0, 1.0);',
        '  gl_FragColor = vec4(rgb, a);',
        '}'
    ].join('\n');

    /* =============================== ANA =============================== */

    function boot() {
        var section = document.querySelector('[data-hero-cine]');
        if (!section) return;
        var stage = section.querySelector('[data-hc-stage]');
        if (!stage) return;

        var THREE = window.THREE;

        /* Statik yol: 3D yoksa ya da hareket istenmiyorsa — hero okunur kalır,
           giriş bölümü normal akışta altta durur. */
        if (prefersReduced() || !THREE || !webglOk()) {
            section.classList.add('hc--static');
            return;
        }

        try {
            initScene(section, stage, THREE);
        } catch (err) {
            section.classList.add('hc--static');
            if (window.console && console.error) console.error('[hero-cine]', err);
        }
    }

    function initScene(section, stage, THREE) {
        var lowPower = isLowPower();
        var useShader = !lowPower;

        /* --- renderer (yeni-tanker.js reçetesi) --- */
        var renderer = new THREE.WebGLRenderer({ alpha: true, antialias: !lowPower });
        var dprCap = lowPower ? 1.25 : 1.6;
        renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, dprCap));
        if (THREE.sRGBEncoding !== undefined) renderer.outputEncoding = THREE.sRGBEncoding;
        renderer.toneMapping = THREE.ACESFilmicToneMapping;
        renderer.toneMappingExposure = 1.1;
        renderer.setClearColor(0x000000, 0);
        renderer.domElement.style.pointerEvents = 'none';
        stage.appendChild(renderer.domElement);

        /* --- sahne: SADE, düz koyu zemin. Ekstra backdrop/dekor/sis YOK —
           görünen tek şey 3D araç. env yalnızca metal yansımaları içindir
           (arka plan değil; scene.background'a atanmaz). --bg-0 ile aynı renk. */
        var scene = new THREE.Scene();
        scene.background = new THREE.Color(0x05070d);
        scene.fog = new THREE.Fog(0x05070d, 16, 46);   // showroom vignette (kenarlar koyulaşır)

        var pmrem = new THREE.PMREMGenerator(renderer);
        scene.environment = pmrem.fromEquirectangular(makeStudioEnv(THREE)).texture;
        pmrem.dispose();

        /* --- ışık rig'i: KOYU SHOWROOM — güçlü key + iki mavi rim --- */
        scene.add(new THREE.HemisphereLight(0x9fc0e8, 0x05070d, 0.32));
        var key = new THREE.DirectionalLight(0xffffff, 1.3);
        key.position.set(5, 9, 6);
        scene.add(key);
        var rimL = new THREE.DirectionalLight(0x3aa0ff, 1.1);   // mavi rim (sol/arka)
        rimL.position.set(-7, 4, -5);
        scene.add(rimL);
        var rimR = new THREE.DirectionalLight(0x2f7bff, 0.55);  // mavi rim (sağ/arka)
        rimR.position.set(7, 3.5, -4);
        scene.add(rimR);
        var fill = new THREE.DirectionalLight(0xdfe9f6, 0.22);
        fill.position.set(2, -1, 8);
        scene.add(fill);

        /* --- kamera --- */
        var camera = new THREE.PerspectiveCamera(36, 1, 0.1, 120);
        var CAM_START = { z: 8.9, y: 0.85 };     // referans kadrajı: yakın, hafif tepeden
        var CAM_END   = { z: 5.0, y: 0.62 };     // scroll'da öne dalış
        var LOOK_Y = -0.15;                       // hafif aşağı bak → araç merkez, altta zemin+neon görünür
        var FIXED_YAW = -0.42;                    // sabit 3/4 hero açısı (dönmez)
        /* Yatay pan: kamerayı sola kaydırıp aynı noktaya bakar → araç kadrajda SAĞA
           kayar (metne sol yarıda yer açılır). Ekran-merkezli dikey ışık dikişi
           (shader) bundan etkilenmez → referanstaki gibi araç dikişin sağında durur. */
        var CAM_X = -1.85;
        camera.position.set(CAM_X, CAM_START.y, CAM_START.z);
        camera.lookAt(CAM_X, LOOK_Y, 0);

        /* --- model (hero ile AYNI: tanker-3.glb) + kontakt gölge --- */
        var TARGET_LEN = 8.8;   // referanstaki gibi kadrajı dolduracak büyük araç — "slide = araç"
        var tanker = new THREE.Group();
        scene.add(tanker);
        var shadow = makeContactShadow(THREE);
        shadow.position.y = -1.35;
        shadow.visible = false;
        scene.add(shadow);
        var modelReady = false;

        /* --- Showroom zemini: parlak yansıtıcı taban (env'i yansıtır → ıslak
           metal hissi). Araç bunun üzerinde durur; scroll'da bu zemin de mavi
           neon dikiş boyunca ikiye ayrılır. --- */
        var floorY = -1.35;
        var floor = new THREE.Mesh(
            new THREE.PlaneGeometry(90, 90),
            new THREE.MeshStandardMaterial({ color: 0x04060c, metalness: 0.9, roughness: 0.18, envMapIntensity: 1.6 })
        );
        floor.rotation.x = -Math.PI / 2;
        floor.position.y = floorY;
        scene.add(floor);

        /* --- Mavi neon zemin çizgileri (additive glow) --- */
        function neonStrip(w, d, color, opacity) {
            var mesh = new THREE.Mesh(
                new THREE.PlaneGeometry(w, d),
                new THREE.MeshBasicMaterial({ color: color, transparent: true, opacity: opacity, blending: THREE.AdditiveBlending, depthWrite: false })
            );
            mesh.rotation.x = -Math.PI / 2;
            return mesh;
        }
        var neonFront     = neonStrip(36, 0.09, 0x6ec3ff, 1.0);    // araç önünde enine parlak çizgi
        var neonFrontGlow = neonStrip(36, 0.85, 0x2f86e6, 0.34);   // çizginin yumuşak halesi
        var neonL         = neonStrip(0.14, 42, 0x5ab4ff, 0.95);   // sol perspektif çizgisi
        var neonLGlow     = neonStrip(0.6,  42, 0x2f86e6, 0.16);   // sol çizginin halesi
        var neonR         = neonStrip(0.14, 42, 0x5ab4ff, 0.95);   // sağ perspektif çizgisi
        var neonRGlow     = neonStrip(0.6,  42, 0x2f86e6, 0.16);   // sağ çizginin halesi
        /* Perspektif çizgileri ekran-merkezine (dikey ışık dikişi = CAM_X ekseni)
           göre simetrik → referanstaki gibi ufukta birleşen V; araç bunların
           arasında, dikişin sağında durur. */
        neonFront.position.set(CAM_X, floorY + 0.012, 4.6);
        neonFrontGlow.position.set(CAM_X, floorY + 0.009, 4.6);
        neonL.position.set(CAM_X - 6.5, floorY + 0.012, -2);
        neonLGlow.position.set(CAM_X - 6.5, floorY + 0.008, -2);
        neonR.position.set(CAM_X + 6.5, floorY + 0.012, -2);
        neonRGlow.position.set(CAM_X + 6.5, floorY + 0.008, -2);
        scene.add(neonFront, neonFrontGlow, neonL, neonLGlow, neonR, neonRGlow);

        function fitShadow(holder) {
            var fp = (holder.userData && holder.userData.footprint) || { x: 6, z: 2.4 };
            var hh = (holder.userData && holder.userData.halfHeight) || 1.3;
            floorY = -hh;
            floor.position.y = floorY;
            neonFront.position.y = floorY + 0.012;
            neonFrontGlow.position.y = floorY + 0.009;
            neonL.position.y = floorY + 0.012;
            neonLGlow.position.y = floorY + 0.008;
            neonR.position.y = floorY + 0.012;
            neonRGlow.position.y = floorY + 0.008;
            shadow.scale.set(fp.x * 1.5, fp.z * 2.5, 1);
            shadow.position.y = floorY + 0.015;   // zeminin hemen üstünde → yere basıyor
            shadow.visible = true;
        }

        var URL = window.__BARLAS_HERO_MODEL || null;
        if (window.GLTFLoader && URL) {
            try {
                new window.GLTFLoader().load(URL, function (gltf) {
                    try {
                        var holder = centerModel(THREE, gltf.scene, TARGET_LEN);
                        tanker.add(holder);
                        fitShadow(holder);
                        modelReady = true;
                    } catch (e) {
                        if (window.console) console.error('[hero-cine] normalize', e);
                    }
                }, undefined, function (err) {
                    if (window.console) console.error('[hero-cine] glb yüklenemedi', err);
                });
            } catch (e) { /* sessiz: model gelmezse boş sahne, statik değil */ }
        }

        /* --- render target + fullscreen quad (yalnızca shader yolu) --- */
        var rt = null, quadScene = null, quadCam = null, quadMat = null;
        if (useShader) {
            rt = new THREE.WebGLRenderTarget(2, 2, {
                minFilter: THREE.LinearFilter,
                magFilter: THREE.LinearFilter,
                format: THREE.RGBAFormat
            });
            quadScene = new THREE.Scene();
            quadCam = new THREE.Camera();
            quadMat = new THREE.ShaderMaterial({
                uniforms: {
                    tDiffuse: { value: rt.texture },
                    uSplit: { value: 0 },
                    uSeam: { value: 0 },
                    uAspect: { value: 1 },
                    uSeamColor: { value: new THREE.Color(0x5ab4ff) }
                },
                vertexShader: SPLIT_VERT,
                fragmentShader: SPLIT_FRAG,
                transparent: true,
                depthTest: false,
                depthWrite: false
            });
            var quad = new THREE.Mesh(new THREE.PlaneGeometry(2, 2), quadMat);
            quad.frustumCulled = false;
            quadScene.add(quad);
        }

        /* --- boyutlandırma (viewport dolu) --- */
        function size() {
            var w = Math.max(1, stage.clientWidth || window.innerWidth);
            var h = Math.max(1, stage.clientHeight || window.innerHeight);
            renderer.setSize(w, h, false);
            camera.aspect = w / h;
            camera.updateProjectionMatrix();
            if (rt) {
                var pr = renderer.getPixelRatio();
                rt.setSize(Math.floor(w * pr), Math.floor(h * pr));
                quadMat.uniforms.uAspect.value = w / h;
            }
        }
        size();
        var rTimer;
        window.addEventListener('resize', function () {
            window.clearTimeout(rTimer);
            rTimer = window.setTimeout(size, 120);
        });
        if ('ResizeObserver' in window) {
            new ResizeObserver(function () { size(); }).observe(stage);
        }

        /* Not: imleç parallax'ı yok — araç idle'da tamamen sabit (kullanıcı isteği:
           daha sade + daha performanslı; hareket yalnızca scroll yarılmasında). */

        /* --- görünürlük gate --- */
        var running = true, onScreen = true;
        if ('IntersectionObserver' in window) {
            new IntersectionObserver(function (es) {
                onScreen = es[0].isIntersecting;
            }, { threshold: 0 }).observe(section);
        }
        document.addEventListener('visibilitychange', function () {
            running = !document.hidden;
            if (running) loop();
        });

        /* --- DOM katmanları --- */
        var overlay = section.querySelector('[data-hc-overlay]');
        var reveal = section.querySelector('[data-hc-reveal]');
        var seamEl = section.querySelector('[data-hc-seam]');
        var pinEl = section.querySelector('[data-hc-pin]') || section;

        /* --- scroll: ScrollTrigger pin + progress --- */
        var targetP = 0, scrollP = 0;
        var gsap = window.gsap, ST = window.ScrollTrigger;
        var pinned = false;
        if (gsap && ST) {
            gsap.registerPlugin(ST);
            ST.create({
                trigger: section,
                start: 'top top',
                end: lowPower ? '+=120%' : '+=165%',
                pin: pinEl,
                anticipatePin: 1,
                scrub: true,
                onUpdate: function (self) { targetP = self.progress; },
                onEnter: function () { section.classList.add('hc--pinned'); },
                onLeaveBack: function () { section.classList.remove('hc--pinned'); }
            });
            pinned = true;
            /* Pin sayfaya boşluk ekler; alt tetikleyiciler yeniden hizalansın. */
            ST.refresh();
            window.addEventListener('load', function () { ST.refresh(); });
        }

        section.classList.add('hc--ready');

        /* --- animasyon döngüsü (ihtiyaç anında render) --- */
        var fadeIn = 0;      // sahne/ışık belirir — modelden BAĞIMSIZ
        var modelFade = 0;   // model gelince yumuşak büyür (pop yok)
        var lastP = -1;      // en son çizilen ilerleme (idle'da yeniden çizme yok)

        function applyDom(p) {
            /* p: 0..1 sinematik ilerleme */
            if (useShader) {
                /* Shader yolu: yarılma canvas'ta; overlay söner, giriş içeriği
                   boşluktan görünür + hafifçe belirir. */
                if (overlay) {
                    var oOut = smooth(0.04, 0.24, p);
                    overlay.style.opacity = (1 - oOut).toFixed(3);
                    overlay.style.transform = 'translateY(' + (-oOut * 40).toFixed(1) + 'px)';
                }
                if (reveal) {
                    var rIn = smooth(0.16, 0.62, p);
                    reveal.style.opacity = rIn.toFixed(3);
                    reveal.style.transform = 'scale(' + lerp(1.08, 1, rIn).toFixed(4) + ')';
                }
            } else {
                /* Düşük-güç yolu: yarılma CSS ile taklit — hero söner/büyür,
                   giriş belirir, ortada ışık çizgisi genişler. */
                if (stage) {
                    var hOut = smooth(0.12, 0.72, p);
                    stage.style.opacity = (1 - hOut).toFixed(3);
                    stage.style.transform = 'scale(' + lerp(1, 1.08, hOut).toFixed(4) + ')';
                }
                if (overlay) {
                    var ov = smooth(0.04, 0.34, p);
                    overlay.style.opacity = (1 - ov).toFixed(3);
                    overlay.style.transform = 'translateY(' + (-ov * 40).toFixed(1) + 'px)';
                }
                if (reveal) {
                    var rv = smooth(0.2, 0.72, p);
                    reveal.style.opacity = rv.toFixed(3);
                    reveal.style.transform = 'scale(' + lerp(1.06, 1, rv).toFixed(4) + ')';
                }
                if (seamEl) {
                    var s1 = smooth(0.04, 0.2, p);
                    var s2 = smooth(0.28, 0.6, p);
                    seamEl.style.opacity = (s1 * (1 - s2)).toFixed(3);
                    seamEl.style.transform = 'scaleY(' + lerp(0.2, 1, s1).toFixed(3) + ') scaleX(' + lerp(1, 3, s2).toFixed(3) + ')';
                }
            }
        }

        function loop() {
            if (!running) return;
            window.requestAnimationFrame(loop);
            if (!onScreen) return;

            /* fade-in sürerken sürekli çiz; sonra yalnızca ilerleme değişince. */
            var fading = (fadeIn < 1) || (modelReady && modelFade < 1);
            if (fadeIn < 1) fadeIn = Math.min(1, fadeIn + 0.03);
            if (modelReady && modelFade < 1) modelFade = Math.min(1, modelFade + 0.045);

            /* ilerlemeyi yumuşat; dinlenince tam otur */
            var d = targetP - scrollP;
            if (Math.abs(d) < 0.0005) scrollP = targetP; else scrollP += d * 0.14;

            /* İHTİYAÇ ANINDA RENDER: sabit sahne idle'da yeniden çizilmez (0 fps →
               daha performanslı). Yalnızca fade sürerken ya da scroll ilerlerken çiz. */
            if (!fading && Math.abs(scrollP - lastP) < 0.0004) return;
            lastP = scrollP;
            var p = scrollP;

            /* Kamera: idle'da TAMAMEN SABİT; yalnızca scroll'da öne dalar. */
            var dolly = smooth(0.1, 0.86, p);
            camera.position.set(CAM_X, lerp(CAM_START.y, CAM_END.y, dolly), lerp(CAM_START.z, CAM_END.z, dolly));
            camera.lookAt(CAM_X, LOOK_Y, 0);

            /* Araç sabit ve yere basar — süzülme/nefes/dönüş/parallax yok. */
            tanker.rotation.set(0, FIXED_YAW, 0);
            tanker.position.y = 0;
            tanker.scale.setScalar(lerp(0.9, 1, smooth(0, 1, modelFade)));
            if (shadow.visible) shadow.material.opacity = modelFade;

            renderer.domElement.style.opacity = fadeIn.toFixed(3);
            applyDom(p);

            if (fadeIn <= 0.005) return;

            if (useShader) {
                quadMat.uniforms.uSplit.value = smooth(0.12, 0.72, p);
                /* Dikey mavi neon dikiş: idle'da GÖRÜNÜR (yarılma kılavuzu),
                   açılırken iki parlayan kenara ayrılır, en sonda söner. */
                quadMat.uniforms.uSeam.value = 0.85 * (1 - smooth(0.78, 1.0, p));
                renderer.setRenderTarget(rt);
                renderer.clear();
                renderer.render(scene, camera);
                renderer.setRenderTarget(null);
                renderer.clear();
                renderer.render(quadScene, quadCam);
            } else {
                renderer.render(scene, camera);
            }
        }
        loop();

        /* İçerik hazırsa (sekme geri geldiğinde) yeniden boyutla. */
        window.addEventListener('load', size);
    }

    /* Layout, gsap/ScrollTrigger (defer) + window.THREE (module) hazır olduktan
       sonra bu dosyayı enjekte eder; yine de DOM garanti olsun diye onReady. */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot, { once: true });
    } else {
        boot();
    }
})();
