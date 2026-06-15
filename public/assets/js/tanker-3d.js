/**
 * tanker-3d.js — Scroll-driven 3D tanker actor (plain script, no module)
 *
 * Inspired by the "3D car as a persistent scene actor" pattern: a single
 * fixed WebGL canvas hosts a procedurally built tanker semi-trailer that
 * orbits with the cursor in the hero and travels between sections as the
 * visitor scrolls (GSAP ScrollTrigger drives a shared pose object).
 *
 * Progressive enhancement, in line with docs/DESIGN.md:
 *   - requires THREE + gsap + ScrollTrigger globals (CDN, deferred)
 *   - skips entirely on reduced-motion, narrow viewports or missing WebGL
 *   - pointer-events: none — never blocks interaction
 *   - transform/opacity only on the DOM side; WebGL is self-contained
 *
 * To swap the procedural model for a real .glb later: replace buildTanker()
 * with a GLTFLoader call that resolves to a THREE.Group of similar size
 * (length ~6 world units, wheels grouped under userData.wheels).
 */
(function () {
    'use strict';

    var MIN_WIDTH = 992;

    function onReady(fn) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fn, { once: true });
        } else {
            fn();
        }
    }

    function webglAvailable() {
        try {
            var c = document.createElement('canvas');
            return !!(window.WebGLRenderingContext &&
                (c.getContext('webgl') || c.getContext('experimental-webgl')));
        } catch (e) {
            return false;
        }
    }

    onReady(function () {
        // Libraries load deferred from CDN; wait one tick for safety.
        window.setTimeout(function () {
            if (!window.THREE || !window.gsap || !window.ScrollTrigger) return;
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
            if (window.innerWidth < MIN_WIDTH) return;
            if (!document.querySelector('.hero')) return;
            if (!webglAvailable()) return;

            // Build our ScrollTriggers AFTER flow.js has created its pins so
            // trigger positions account for pin spacing. Fall back gracefully
            // when flow.js is absent (reduced-motion handled above already).
            if (window.__flowReady) {
                init();
            } else {
                var started = false;
                var start = function () { if (!started) { started = true; init(); } };
                window.addEventListener('flow:ready', start, { once: true });
                window.setTimeout(start, 800);
            }
        }, 0);
    });

    /* =====================================================================
       Texture helpers (procedural — no external assets, no licenses)
       ===================================================================== */

    /** Fake studio HDRI: gradient sky + softboxes, used as PMREM env map. */
    function makeStudioEnvTexture(THREE) {
        var c = document.createElement('canvas');
        c.width = 1024; c.height = 512;
        var ctx = c.getContext('2d');

        var sky = ctx.createLinearGradient(0, 0, 0, 512);
        sky.addColorStop(0.0, '#3a4860');
        sky.addColorStop(0.45, '#202b3e');
        sky.addColorStop(0.62, '#11192a');
        sky.addColorStop(1.0, '#05080f');
        ctx.fillStyle = sky;
        ctx.fillRect(0, 0, 1024, 512);

        // Softboxes (bright rectangles read as long highlights on the tank)
        function softbox(x, y, w, h, color, alpha) {
            ctx.save();
            ctx.globalAlpha = alpha;
            ctx.filter = 'blur(14px)';
            ctx.fillStyle = color;
            ctx.fillRect(x, y, w, h);
            ctx.restore();
        }
        softbox(80, 60, 300, 70, '#ffffff', 0.95);   // key, upper left
        softbox(560, 90, 360, 56, '#dfe9f6', 0.8);   // fill, upper right
        softbox(330, 200, 180, 36, '#9fc6ef', 0.55); // cool kicker (brand blue cast)
        softbox(0, 460, 1024, 52, '#1c2638', 0.9);   // faint ground bounce

        var tex = new THREE.CanvasTexture(c);
        tex.mapping = THREE.EquirectangularReflectionMapping;
        return tex;
    }

    /** Brand decal ("BARLAS" + hairline rule) drawn for a curved side panel. */
    function makeDecalTexture(THREE, flip) {
        var c = document.createElement('canvas');
        c.width = 256; c.height = 1024;     // u = around arc, v = along tank
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
        var sub = 'T A N K E R   T R A I L E R S';
        ctx.fillText(sub, 0, 64);
        ctx.fillStyle = 'rgba(249, 115, 22, 0.95)';
        ctx.fillRect(-210, 38, 60, 5);      // the single warm accent, weld-spark sized
        ctx.restore();

        var tex = new THREE.CanvasTexture(c);
        tex.anisotropy = 4;
        return tex;
    }

    /** Soft elliptical contact shadow. */
    function makeShadowTexture(THREE) {
        var c = document.createElement('canvas');
        c.width = 256; c.height = 128;
        var ctx = c.getContext('2d');
        var g = ctx.createRadialGradient(128, 64, 8, 128, 64, 120);
        g.addColorStop(0, 'rgba(7, 11, 20, 0.42)');
        g.addColorStop(0.55, 'rgba(7, 11, 20, 0.18)');
        g.addColorStop(1, 'rgba(7, 11, 20, 0)');
        ctx.fillStyle = g;
        ctx.fillRect(0, 0, 256, 128);
        return new THREE.CanvasTexture(c);
    }

    /* =====================================================================
       Procedural tanker semi-trailer
       ===================================================================== */

    function buildTanker(THREE) {
        var group = new THREE.Group();
        var R = 1.0;       // tank radius
        var L = 5.4;       // cylindrical length (caps add ~1.0)

        var matSteel = new THREE.MeshStandardMaterial({
            color: 0xd9dee5, metalness: 1.0, roughness: 0.24, envMapIntensity: 1.15
        });
        var matSteelDark = new THREE.MeshStandardMaterial({
            color: 0x9aa6b5, metalness: 1.0, roughness: 0.35, envMapIntensity: 0.9
        });
        var matChassis = new THREE.MeshStandardMaterial({
            color: 0x1b2638, metalness: 0.55, roughness: 0.6
        });
        var matDark = new THREE.MeshStandardMaterial({
            color: 0x10182a, metalness: 0.4, roughness: 0.7
        });
        var matTire = new THREE.MeshStandardMaterial({
            color: 0x14181f, metalness: 0.0, roughness: 0.95
        });
        var matRim = new THREE.MeshStandardMaterial({
            color: 0xcfd6df, metalness: 1.0, roughness: 0.28, envMapIntensity: 1.0
        });
        var matBlue = new THREE.MeshStandardMaterial({
            color: 0x005baa, metalness: 0.65, roughness: 0.38
        });
        var matAccent = new THREE.MeshStandardMaterial({
            color: 0xf97316, metalness: 0.35, roughness: 0.5,
            emissive: 0x3a1703, emissiveIntensity: 0.5
        });

        function add(geo, mat, x, y, z, rx, ry, rz) {
            var m = new THREE.Mesh(geo, mat);
            m.position.set(x || 0, y || 0, z || 0);
            if (rx) m.rotation.x = rx;
            if (ry) m.rotation.y = ry;
            if (rz) m.rotation.z = rz;
            group.add(m);
            return m;
        }

        // --- Tank barrel (axis X) -------------------------------------
        var barrel = new THREE.CylinderGeometry(R, R, L, 56, 1, true);
        barrel.rotateZ(Math.PI / 2);
        add(barrel, matSteel);

        // Elliptical end caps
        var capGeo = new THREE.SphereGeometry(R, 48, 24);
        var capF = add(capGeo, matSteel, -L / 2, 0, 0);
        capF.scale.set(0.42, 1, 1);
        var capB = add(capGeo, matSteel, L / 2, 0, 0);
        capB.scale.set(0.42, 1, 1);

        // Ring stiffeners
        var ringGeo = new THREE.TorusGeometry(R + 0.015, 0.022, 10, 72);
        ringGeo.rotateY(Math.PI / 2);
        [-1.9, -0.7, 0.5, 1.7].forEach(function (x) {
            add(ringGeo, matSteelDark, x, 0, 0);
        });

        // Brand blue waistline (thin band, both = one quiet brand cue)
        var bandGeo = new THREE.CylinderGeometry(R + 0.006, R + 0.006, 0.10, 56, 1, true);
        bandGeo.rotateZ(Math.PI / 2);
        add(bandGeo, matBlue, -2.35, 0, 0);

        // --- Brand decals (curved panels hugging the barrel) ----------
        function decal(thetaStart, flip) {
            var g = new THREE.CylinderGeometry(R + 0.012, R + 0.012, 3.4, 48, 1, true,
                thetaStart, 1.15);
            g.rotateZ(Math.PI / 2);
            var m = new THREE.MeshBasicMaterial({
                map: makeDecalTexture(THREE, flip),
                transparent: true,
                polygonOffset: true,
                polygonOffsetFactor: -2
            });
            var mesh = new THREE.Mesh(g, m);
            mesh.position.x = 0.25;
            group.add(mesh);
            return mesh;
        }
        // Front (faces +Z / camera) and far side
        decal(-0.575, false);
        decal(Math.PI - 0.575, true);

        // --- Top fittings ----------------------------------------------
        add(new THREE.BoxGeometry(3.6, 0.04, 0.5), matDark, 0.2, R + 0.04, 0);
        [-0.9, 0.2, 1.3].forEach(function (x) {
            add(new THREE.CylinderGeometry(0.17, 0.19, 0.12, 24), matSteelDark, x, R + 0.10, 0);
            var dome = add(new THREE.SphereGeometry(0.15, 24, 12, 0, Math.PI * 2, 0, Math.PI / 2),
                matSteel, x, R + 0.15, 0);
            dome.scale.y = 0.55;
        });
        // Safety rail
        var railGeo = new THREE.CylinderGeometry(0.018, 0.018, 3.4, 8);
        railGeo.rotateZ(Math.PI / 2);
        add(railGeo, matSteelDark, 0.2, R + 0.34, 0.22);
        [-1.4, 0.2, 1.8].forEach(function (x) {
            add(new THREE.CylinderGeometry(0.014, 0.014, 0.3, 8), matSteelDark, x, R + 0.19, 0.22);
        });

        // --- Side pipe run ----------------------------------------------
        var pipeGeo = new THREE.CylinderGeometry(0.04, 0.04, 4.4, 12);
        pipeGeo.rotateZ(Math.PI / 2);
        add(pipeGeo, matSteelDark, 0, -R + 0.28, R * 0.86);

        // --- Chassis -----------------------------------------------------
        add(new THREE.BoxGeometry(4.8, 0.14, 0.12), matChassis, 0.3, -R - 0.10, 0.52);
        add(new THREE.BoxGeometry(4.8, 0.14, 0.12), matChassis, 0.3, -R - 0.10, -0.52);
        [-1.5, -0.3, 0.9, 2.1].forEach(function (x) {
            add(new THREE.BoxGeometry(0.10, 0.10, 1.04), matChassis, x, -R - 0.10, 0);
        });

        // --- Wheels (3 axles, rear) — grouped for rolling ---------------
        var wheels = [];
        var tireGeo = new THREE.TorusGeometry(0.32, 0.135, 14, 36);
        var rimGeo = new THREE.CylinderGeometry(0.20, 0.20, 0.24, 24);
        rimGeo.rotateX(Math.PI / 2);
        var hubGeo = new THREE.CylinderGeometry(0.07, 0.07, 0.27, 16);
        hubGeo.rotateX(Math.PI / 2);

        [1.05, 1.95, 2.85].forEach(function (x) {
            [-0.78, 0.78].forEach(function (z) {
                var w = new THREE.Group();
                w.add(new THREE.Mesh(tireGeo, matTire));
                w.add(new THREE.Mesh(rimGeo, matRim));
                w.add(new THREE.Mesh(hubGeo, matSteelDark));
                w.position.set(x, -R - 0.42, z);
                group.add(w);
                wheels.push(w);
            });
            // Axle + half-shell mudguard
            var axGeo = new THREE.CylinderGeometry(0.05, 0.05, 1.56, 10);
            axGeo.rotateX(Math.PI / 2);
            add(axGeo, matDark, x, -R - 0.42, 0);
            var mgGeo = new THREE.CylinderGeometry(0.52, 0.52, 0.34, 24, 1, true,
                -Math.PI / 2, Math.PI);
            mgGeo.rotateX(Math.PI / 2);
            var mgMat = matChassis.clone();
            mgMat.side = THREE.DoubleSide;
            add(mgGeo, mgMat, x, -R - 0.40, 0.86);
            add(mgGeo, mgMat, x, -R - 0.40, -0.86);
        });

        // --- Landing gear (front support legs) ---------------------------
        [-0.5, 0.5].forEach(function (z) {
            add(new THREE.BoxGeometry(0.09, 0.62, 0.09), matSteelDark, -1.85, -R - 0.36, z * 1.1);
            add(new THREE.BoxGeometry(0.22, 0.05, 0.16), matDark, -1.85, -R - 0.66, z * 1.1);
        });
        add(new THREE.BoxGeometry(0.05, 0.05, 1.1), matSteelDark, -1.85, -R - 0.30, 0);

        // Kingpin plate
        add(new THREE.BoxGeometry(1.0, 0.08, 0.94), matDark, -2.25, -R - 0.06, 0);
        add(new THREE.CylinderGeometry(0.06, 0.06, 0.16, 12), matSteelDark, -2.25, -R - 0.16, 0);

        // --- Rear: bumper, lights, discharge valve -----------------------
        add(new THREE.BoxGeometry(0.07, 0.42, 1.9), matChassis, 3.06, -R - 0.32, 0);
        var lightRed = new THREE.MeshStandardMaterial({
            color: 0x7a1020, emissive: 0xc11030, emissiveIntensity: 0.7, roughness: 0.4
        });
        add(new THREE.BoxGeometry(0.04, 0.09, 0.22), lightRed, 3.10, -R - 0.24, 0.72);
        add(new THREE.BoxGeometry(0.04, 0.09, 0.22), lightRed, 3.10, -R - 0.24, -0.72);
        // Discharge valve — the one orange accent on the machine
        var valveGeo = new THREE.CylinderGeometry(0.09, 0.09, 0.30, 16);
        valveGeo.rotateZ(Math.PI / 2);
        add(valveGeo, matAccent, 2.62, -R - 0.18, 0.35);

        // --- Rear ladder ---------------------------------------------------
        [-0.16, 0.16].forEach(function (dz) {
            var rail = add(new THREE.CylinderGeometry(0.02, 0.02, 1.9, 8), matSteelDark,
                2.95, -0.1, dz + 0.55);
            rail.rotation.z = 0.08;
        });
        for (var i = 0; i < 5; i++) {
            var rung = add(new THREE.CylinderGeometry(0.015, 0.015, 0.32, 8), matSteelDark,
                2.95 + (i - 2) * -0.013, -0.9 + i * 0.4, 0.55);
            rung.rotation.x = Math.PI / 2;
        }

        // --- Contact shadow -------------------------------------------------
        var shadow = new THREE.Mesh(
            new THREE.PlaneGeometry(8.6, 3.0),
            new THREE.MeshBasicMaterial({
                map: makeShadowTexture(THREE), transparent: true, depthWrite: false
            })
        );
        shadow.rotation.x = -Math.PI / 2;
        shadow.position.y = -R - 0.74;
        group.add(shadow);

        group.userData.wheels = wheels;
        return group;
    }

    /* =====================================================================
       Scene, choreography, render loop
       ===================================================================== */

    function init() {
        var THREE = window.THREE;
        var gsap = window.gsap;
        gsap.registerPlugin(window.ScrollTrigger);

        // --- Stage ------------------------------------------------------
        var stage = document.createElement('div');
        stage.className = 'tanker-stage';
        stage.setAttribute('aria-hidden', 'true');
        document.body.appendChild(stage);

        var renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
        renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 1.75));
        renderer.setSize(window.innerWidth, window.innerHeight);
        if (THREE.sRGBEncoding !== undefined) renderer.outputEncoding = THREE.sRGBEncoding;
        renderer.toneMapping = THREE.ACESFilmicToneMapping;
        renderer.toneMappingExposure = 1.05;
        stage.appendChild(renderer.domElement);

        var scene = new THREE.Scene();
        var camera = new THREE.PerspectiveCamera(
            32, window.innerWidth / window.innerHeight, 0.1, 60);
        camera.position.set(0, 0.55, 11.5);
        camera.lookAt(0, -0.1, 0);

        // Environment (fake studio HDRI -> PMREM)
        var pmrem = new THREE.PMREMGenerator(renderer);
        var envRT = pmrem.fromEquirectangular(makeStudioEnvTexture(THREE));
        scene.environment = envRT.texture;
        pmrem.dispose();

        // Lights (key + cool rim + ambient base)
        var hemi = new THREE.HemisphereLight(0xbfd6ee, 0x0b1220, 0.5);
        scene.add(hemi);
        var key = new THREE.DirectionalLight(0xffffff, 1.0);
        key.position.set(4, 7, 6);
        scene.add(key);
        var rim = new THREE.DirectionalLight(0x4aa3e6, 0.55);
        rim.position.set(-6, 3, -5);
        scene.add(rim);

        // Actor
        var tanker = buildTanker(THREE);
        scene.add(tanker);
        var wheels = tanker.userData.wheels;

        // --- Pose (single source of truth, tweened by ScrollTrigger) ----
        var pose = { x: 2.55, y: -0.35, ry: -0.55, rx: 0.05, s: 0.96, o: 0 };

        // Fade in once ready (Emil: enter via opacity, no scale-from-zero)
        gsap.to(pose, { o: 1, duration: 1.1, ease: 'power2.out', delay: 0.15 });

        /**
         * Choreography for the cinematic flow (see flow.js):
         *
         *   Act 1 — hero is PINNED for a short beat: the tanker swings from
         *           the showroom 3/4 view to a road-ready side profile.
         *   Act 2 — as the page leaves the hero it drives off stage left
         *           (wheels rolling), clearing the light editorial body.
         *   Act 3 — it returns in the dark CTA finale, arriving from the
         *           left and parking beside the closing message.
         */
        var heroEl2 = document.querySelector('.hero--cine') || document.querySelector('.hero');
        if (heroEl2) {
            // Act 1: during the hero pin (flow.js pins for +=55%)
            gsap.to(pose, {
                x: 1.9, y: -0.42, ry: -1.35, rx: 0.02, s: 0.92,
                ease: 'none',
                immediateRender: false,
                scrollTrigger: {
                    trigger: heroEl2,
                    start: 'top top',
                    end: '+=55%',
                    scrub: 0.5
                }
            });
        }

        var steps = [
            // Act 2: drive off stage left while the trust band / about arrive
            ['.marquee', { x: -2.6, y: -0.45, ry: -1.5,  rx: 0.0,  s: 0.88, o: 1 }, 'top 95%', 'top 35%'],
            ['.about',   { x: -8.0, y: -0.45, ry: -1.55, rx: 0.0,  s: 0.86, o: 0 }, 'top 90%', 'top 35%'],
            // Act 3: return for the finale — arrive and park stage right
            ['.cta-band--finale', { x: 2.4, y: -0.4, ry: 0.45, rx: 0.04, s: 0.95, o: 1 }, 'top 92%', 'top 30%']
        ];

        steps.forEach(function (step) {
            var el = document.querySelector(step[0]);
            if (!el) return;
            var to = step[1];
            gsap.to(pose, {
                x: to.x, y: to.y, ry: to.ry, rx: to.rx, s: to.s, o: to.o,
                ease: 'none',
                immediateRender: false,
                scrollTrigger: {
                    trigger: el,
                    start: step[2],
                    end: step[3],
                    scrub: 0.7
                }
            });
        });

        // --- Cursor orbit (hero-weighted, lerped — the Spline feel) ------
        var pointer = { x: 0, y: 0 };       // -0.5 .. 0.5
        var orbit = { ry: 0, rx: 0 };       // smoothed offsets
        window.addEventListener('pointermove', function (e) {
            pointer.x = e.clientX / window.innerWidth - 0.5;
            pointer.y = e.clientY / window.innerHeight - 0.5;
        }, { passive: true });

        var heroEl = document.querySelector('.hero');
        function heroFactor() {
            var h = heroEl ? heroEl.offsetHeight : window.innerHeight;
            var p = Math.min(1, Math.max(0, window.scrollY / (h * 0.85)));
            return 1 - p * 0.8;            // full orbit in hero, 20% afterwards
        }

        // --- Render loop ---------------------------------------------------
        var lastX = pose.x;
        var clock = new THREE.Clock();
        var running = true;

        document.addEventListener('visibilitychange', function () {
            running = !document.hidden;
            if (running) loop();
        });

        function loop() {
            if (!running) return;
            window.requestAnimationFrame(loop);

            var t = clock.getElapsedTime();
            var f = heroFactor();

            // Smooth cursor orbit
            orbit.ry += ((pointer.x * 0.55 * f) - orbit.ry) * 0.06;
            orbit.rx += ((pointer.y * 0.16 * f) - orbit.rx) * 0.06;

            tanker.position.x = pose.x;
            tanker.position.y = pose.y + Math.sin(t * 0.85) * 0.045; // idle float
            tanker.rotation.y = pose.ry + orbit.ry;
            tanker.rotation.x = pose.rx + orbit.rx;
            tanker.scale.setScalar(pose.s);

            // Wheels roll with horizontal travel
            var dx = pose.x - lastX;
            lastX = pose.x;
            if (Math.abs(dx) > 0.0001) {
                for (var i = 0; i < wheels.length; i++) {
                    wheels[i].rotation.z -= dx / 0.45;
                }
            }

            stage.style.opacity = pose.o.toFixed(3);
            if (pose.o > 0.005) renderer.render(scene, camera);
        }

        // --- Resize ----------------------------------------------------------
        var resizeT;
        window.addEventListener('resize', function () {
            window.clearTimeout(resizeT);
            resizeT = window.setTimeout(function () {
                camera.aspect = window.innerWidth / window.innerHeight;
                camera.updateProjectionMatrix();
                renderer.setSize(window.innerWidth, window.innerHeight);
            }, 120);
        });

        document.documentElement.classList.add('has-tanker-3d');
        loop();
    }
})();
