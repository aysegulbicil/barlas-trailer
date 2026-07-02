/* =====================================================================
   quiz.js — "Which trailer suits me?" decision-tree wizard
   The localized tree is injected by the view as inline JSON; results
   link to the real category pages. No network requests.
   ===================================================================== */
(function () {
    'use strict';

    var root = document.querySelector('[data-quiz]');
    if (!root) return;

    var tree;
    try {
        tree = JSON.parse(root.querySelector('[data-quiz-tree]').textContent);
    } catch (e) {
        return;
    }

    var stepEl     = root.querySelector('[data-quiz-step]');
    var progressEl = root.querySelector('[data-quiz-progress]');
    var questionEl = root.querySelector('[data-quiz-question]');
    var optionsEl  = root.querySelector('[data-quiz-options]');
    var resultEl   = root.querySelector('[data-quiz-result]');
    var catNameEl  = root.querySelector('[data-quiz-cat-name]');
    var catLinkEl  = root.querySelector('[data-quiz-cat-link]');
    var reasonEl   = root.querySelector('[data-quiz-reason]');
    var altEl      = root.querySelector('[data-quiz-alt]');
    var altLinkEl  = root.querySelector('[data-quiz-alt-link]');
    var backBtn    = root.querySelector('[data-quiz-back]');
    var restartBtn = root.querySelector('[data-quiz-restart]');
    var shareBtn   = root.querySelector('[data-quiz-share]');

    var history = [];          // geride bırakılan düğüm id'leri
    var current = tree.start;  // ekrandaki (veya sonuca gelinen) soru

    /* Bu düğümden sonra en fazla kaç soru daha gelir? (ilerleme için) */
    function remainingDepth(id) {
        var node = tree.nodes[id];
        if (!node) return 0;
        var max = 0;
        node.options.forEach(function (opt) {
            if (opt.next) max = Math.max(max, 1 + remainingDepth(opt.next));
        });
        return max;
    }

    function renderQuestion(id) {
        var node = tree.nodes[id];
        if (!node) return;

        var step  = history.length + 1;
        var total = step + remainingDepth(id);
        var tpl   = root.getAttribute('data-progress-template') || '{0}/{1}';
        progressEl.textContent = tpl.replace('{0}', step).replace('{1}', total);

        questionEl.textContent = node.question;
        optionsEl.innerHTML = '';

        node.options.forEach(function (opt) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'quiz__option';
            btn.textContent = opt.label;
            btn.addEventListener('click', function () {
                if (opt.result) {
                    renderResult(opt.result);
                } else if (opt.next) {
                    history.push(id);
                    current = opt.next;
                    renderQuestion(current);
                }
            });
            optionsEl.appendChild(btn);
        });

        stepEl.hidden   = false;
        resultEl.hidden = true;
        backBtn.hidden    = history.length === 0;
        restartBtn.hidden = history.length === 0;
    }

    function renderResult(result) {
        catNameEl.textContent = result.name;
        reasonEl.textContent  = result.reason;
        catLinkEl.href        = result.url;

        if (result.altName && altEl && altLinkEl) {
            altLinkEl.textContent = result.altName;
            altLinkEl.href        = result.altUrl;
            altEl.hidden          = false;
        } else if (altEl) {
            altEl.hidden = true;
        }

        stepEl.hidden     = true;
        resultEl.hidden   = false;
        backBtn.hidden    = false;
        restartBtn.hidden = false;
    }

    backBtn.addEventListener('click', function () {
        if (!resultEl.hidden) {
            // Sonuçtan geri: sonuca gelinen soruya dön.
            renderQuestion(current);
            return;
        }
        if (history.length) {
            current = history.pop();
            renderQuestion(current);
        }
    });

    restartBtn.addEventListener('click', function () {
        history = [];
        current = tree.start;
        renderQuestion(current);
    });

    if (shareBtn) {
        shareBtn.addEventListener('click', function () {
            var done = function () {
                var old = shareBtn.textContent;
                shareBtn.textContent = root.getAttribute('data-share-copied') || old;
                window.setTimeout(function () { shareBtn.textContent = old; }, 2000);
            };
            if (navigator.share) {
                navigator.share({ title: document.title, url: window.location.href }).catch(function () {});
            } else if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(window.location.href).then(done, function () {});
            }
        });
    }

    renderQuestion(tree.start);
})();
