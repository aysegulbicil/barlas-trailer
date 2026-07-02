/* =====================================================================
   tetris.js — Treyler Tetris (Bekleme Salonu §8)
   Bağımlılıksız canvas oyunu: klavye + dokunmatik ped. Rekor yalnız
   localStorage'da ('barlas-tetris-best'). Sekme gizlenince duraklar.
   ===================================================================== */
(function () {
    'use strict';

    var root = document.querySelector('[data-tetris]');
    if (!root) return;

    var canvas = root.querySelector('[data-t-canvas]');
    var ctx    = canvas.getContext('2d');
    if (!ctx) return;

    var COLS = 10;
    var ROWS = 16;

    /* HiDPI: çizim tamponunu büyüt, CSS boyutu sabit kalsın. */
    var dpr  = Math.min(window.devicePixelRatio || 1, 2);
    var CELL = 30;
    canvas.width  = COLS * CELL * dpr;
    canvas.height = ROWS * CELL * dpr;
    ctx.scale(dpr, dpr);

    var COLORS = ['#4AA3E6', '#005BAA', '#57ec88', '#f2b84b', '#e06666', '#9b7fe6', '#3ec9c9'];

    var SHAPES = [
        [[1, 1, 1, 1]],                 // I
        [[1, 1], [1, 1]],               // O
        [[0, 1, 0], [1, 1, 1]],         // T
        [[0, 1, 1], [1, 1, 0]],         // S
        [[1, 1, 0], [0, 1, 1]],         // Z
        [[1, 0, 0], [1, 1, 1]],         // J
        [[0, 0, 1], [1, 1, 1]]          // L
    ];

    var elScore = root.querySelector('[data-t-score]');
    var elBest  = root.querySelector('[data-t-best]');
    var elLevel = root.querySelector('[data-t-level]');
    var elLines = root.querySelector('[data-t-lines]');
    var overlay = root.querySelector('[data-t-overlay]');
    var ovText  = root.querySelector('[data-t-overlay-text]');
    var btnGo   = root.querySelector('[data-t-start]');
    var btnPause   = root.querySelector('[data-t-pause]');
    var btnRestart = root.querySelector('[data-t-restart]');

    var BEST_KEY = 'barlas-tetris-best';
    var best = parseInt(localStorage.getItem(BEST_KEY) || '0', 10) || 0;
    elBest.textContent = best;

    var board, piece, score, lines, level, playing, paused, dropTimer, lastDrop;

    function newBoard() {
        var b = [];
        for (var r = 0; r < ROWS; r++) b.push(new Array(COLS).fill(0));
        return b;
    }

    function newPiece() {
        var i = Math.floor(Math.random() * SHAPES.length);
        var shape = SHAPES[i].map(function (row) { return row.slice(); });
        return {
            shape: shape,
            color: i + 1,
            x: Math.floor((COLS - shape[0].length) / 2),
            y: 0
        };
    }

    function collides(shape, x, y) {
        for (var r = 0; r < shape.length; r++) {
            for (var c = 0; c < shape[r].length; c++) {
                if (!shape[r][c]) continue;
                var nx = x + c;
                var ny = y + r;
                if (nx < 0 || nx >= COLS || ny >= ROWS) return true;
                if (ny >= 0 && board[ny][nx]) return true;
            }
        }
        return false;
    }

    function rotate(shape) {
        var rows = shape.length;
        var cols = shape[0].length;
        var out  = [];
        for (var c = 0; c < cols; c++) {
            var row = [];
            for (var r = rows - 1; r >= 0; r--) row.push(shape[r][c]);
            out.push(row);
        }
        return out;
    }

    function merge() {
        piece.shape.forEach(function (row, r) {
            row.forEach(function (v, c) {
                if (v && piece.y + r >= 0) board[piece.y + r][piece.x + c] = piece.color;
            });
        });
    }

    function clearLines() {
        var cleared = 0;
        for (var r = ROWS - 1; r >= 0; r--) {
            if (board[r].every(function (v) { return v !== 0; })) {
                board.splice(r, 1);
                board.unshift(new Array(COLS).fill(0));
                cleared++;
                r++; // aynı satırı yeniden kontrol et
            }
        }
        if (cleared > 0) {
            score += [0, 100, 300, 500, 800][cleared] * level;
            lines += cleared;
            level = Math.floor(lines / 10) + 1;
            syncStats();
        }
    }

    function syncStats() {
        elScore.textContent = score;
        elLevel.textContent = level;
        elLines.textContent = lines;
    }

    function dropInterval() {
        return Math.max(120, 800 - (level - 1) * 70);
    }

    /* ---------- çizim ---------- */
    function draw() {
        ctx.clearRect(0, 0, COLS * CELL, ROWS * CELL);

        // Izgara (dorse tabanı hissi)
        ctx.strokeStyle = 'rgba(127, 152, 190, 0.12)';
        ctx.lineWidth = 1;
        for (var c = 1; c < COLS; c++) {
            ctx.beginPath(); ctx.moveTo(c * CELL, 0); ctx.lineTo(c * CELL, ROWS * CELL); ctx.stroke();
        }
        for (var r = 1; r < ROWS; r++) {
            ctx.beginPath(); ctx.moveTo(0, r * CELL); ctx.lineTo(COLS * CELL, r * CELL); ctx.stroke();
        }

        for (var y = 0; y < ROWS; y++) {
            for (var x = 0; x < COLS; x++) {
                if (board[y][x]) drawCell(x, y, board[y][x]);
            }
        }

        if (piece) {
            piece.shape.forEach(function (row, ry) {
                row.forEach(function (v, rx) {
                    if (v && piece.y + ry >= 0) drawCell(piece.x + rx, piece.y + ry, piece.color);
                });
            });
        }
    }

    function drawCell(x, y, colorIdx) {
        var px = x * CELL;
        var py = y * CELL;
        ctx.fillStyle = COLORS[(colorIdx - 1) % COLORS.length];
        ctx.fillRect(px + 1, py + 1, CELL - 2, CELL - 2);
        ctx.fillStyle = 'rgba(255,255,255,0.18)';
        ctx.fillRect(px + 1, py + 1, CELL - 2, 4); // üst parlaklık: "kasa" hissi
    }

    /* ---------- oyun akışı ---------- */
    function step(now) {
        if (!playing) return;
        if (!paused) {
            if (now - lastDrop >= dropInterval()) {
                softDrop();
                lastDrop = now;
            }
            draw();
        }
        dropTimer = window.requestAnimationFrame(step);
    }

    function softDrop() {
        if (!collides(piece.shape, piece.x, piece.y + 1)) {
            piece.y++;
            return;
        }
        merge();
        clearLines();
        piece = newPiece();
        if (collides(piece.shape, piece.x, piece.y)) gameOver();
    }

    function hardDrop() {
        while (!collides(piece.shape, piece.x, piece.y + 1)) piece.y++;
        softDrop();
        draw();
    }

    function move(dx) {
        if (!collides(piece.shape, piece.x + dx, piece.y)) {
            piece.x += dx;
            draw();
        }
    }

    function tryRotate() {
        var rotated = rotate(piece.shape);
        var kicks = [0, -1, 1, -2, 2];
        for (var i = 0; i < kicks.length; i++) {
            if (!collides(rotated, piece.x + kicks[i], piece.y)) {
                piece.shape = rotated;
                piece.x += kicks[i];
                draw();
                return;
            }
        }
    }

    function start() {
        board   = newBoard();
        piece   = newPiece();
        score   = 0;
        lines   = 0;
        level   = 1;
        playing = true;
        paused  = false;
        lastDrop = performance.now();
        syncStats();
        overlay.hidden    = true;
        btnPause.hidden   = false;
        btnRestart.hidden = false;
        btnPause.textContent = btnPause.getAttribute('data-label-pause');
        window.cancelAnimationFrame(dropTimer);
        dropTimer = window.requestAnimationFrame(step);
    }

    function setPaused(p) {
        if (!playing) return;
        paused = p;
        btnPause.textContent = p
            ? btnPause.getAttribute('data-label-resume')
            : btnPause.getAttribute('data-label-pause');
    }

    function gameOver() {
        playing = false;
        window.cancelAnimationFrame(dropTimer);

        var msg = root.getAttribute('data-msg-gameover') || 'Game over';
        if (score > best) {
            best = score;
            localStorage.setItem(BEST_KEY, String(best));
            elBest.textContent = best;
            msg = (root.getAttribute('data-msg-newbest') || '') + ' — ' + msg;
        }

        ovText.textContent = msg + ' · ' + score;
        btnGo.textContent  = root.getAttribute('data-msg-start') || btnGo.textContent;
        overlay.hidden = false;
    }

    /* ---------- girişler ---------- */
    btnGo.addEventListener('click', start);
    btnPause.addEventListener('click', function () { setPaused(!paused); });
    btnRestart.addEventListener('click', start);

    document.addEventListener('keydown', function (e) {
        if (!playing || paused) return;
        // Oyun odaktayken sayfa kaymasın.
        if (['ArrowLeft', 'ArrowRight', 'ArrowDown', 'ArrowUp', ' '].indexOf(e.key) !== -1) {
            e.preventDefault();
        }
        if (e.key === 'ArrowLeft') move(-1);
        else if (e.key === 'ArrowRight') move(1);
        else if (e.key === 'ArrowDown') { softDrop(); draw(); }
        else if (e.key === 'ArrowUp') tryRotate();
        else if (e.key === ' ') hardDrop();
        else if (e.key === 'p' || e.key === 'P') setPaused(!paused);
    });

    var actions = {
        left:   function () { move(-1); },
        right:  function () { move(1); },
        rotate: tryRotate,
        down:   function () { softDrop(); draw(); },
        drop:   hardDrop
    };

    root.querySelectorAll('[data-t-btn]').forEach(function (btn) {
        var key = btn.getAttribute('data-t-btn');
        var repeat = null;

        btn.addEventListener('pointerdown', function (e) {
            e.preventDefault();
            if (!playing || paused) return;
            actions[key]();
            if (key === 'left' || key === 'right' || key === 'down') {
                repeat = window.setInterval(function () {
                    if (playing && !paused) actions[key]();
                }, 140);
            }
        });

        ['pointerup', 'pointerleave', 'pointercancel'].forEach(function (ev) {
            btn.addEventListener(ev, function () {
                if (repeat) { window.clearInterval(repeat); repeat = null; }
            });
        });
    });

    document.addEventListener('visibilitychange', function () {
        if (document.hidden) setPaused(true);
    });

    draw();
})();
