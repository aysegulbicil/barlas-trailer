<?php

/**
 * Media (Medya) page — Fotoğraflar + Videolar sekmeleri
 *
 * İçerik dosya tabanlıdır (App\Controllers\Media):
 *   $photos                — public/assets/media/photos taranarak gelir;
 *                            klasör boşsa geçici örnek site görselleri.
 *   $videos                — public/assets/media/videos taranarak gelir;
 *                            klasör boşsa "Yakında" yer tutucu kartlar.
 *   $photosAreSamples      — fotoğraflar örnek mi (gerçek dosya yok)?
 *   $videosArePlaceholder  — videolar yer tutucu mu (gerçek video yok)?
 *
 * Her fotoğraf (ve yer tutucu video kartı) tıklanınca lightbox'ta büyür.
 * Galeri butonundan #fotograflar / #videolar ile doğrudan sekme açılabilir.
 */
$this->extend('layouts/inner');

$photos               = $photos ?? [];
$categories           = $categories ?? [];
$videos               = $videos ?? [];
$videosArePlaceholder = $videosArePlaceholder ?? false;
?>

<?php $this->section('bodyClass') ?>media<?php $this->endSection() ?>

<?php $this->section('content') ?>

<?= view('partials/page-hero', [
    'pageTitle'   => lang('Media.page_title'),
    'pageLead'    => lang('Media.page_lead'),
    'breadcrumbs' => [
        ['label' => lang('Media.breadcrumb')],
    ],
]) ?>

<section class="media-section">
    <div class="container">

        <!-- Sekmeler -->
        <div class="media-tabs" data-tabs>
            <div class="media-tabs__list" role="tablist" aria-label="<?= esc(lang('Media.page_title'), 'attr') ?>">
                <button class="media-tabs__tab is-active" type="button" role="tab"
                        id="tab-photos" aria-selected="true" aria-controls="panel-photos"
                        data-tab="photos">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="3" y="4" width="18" height="16" rx="2.5"></rect>
                        <circle cx="8.5" cy="9.5" r="1.6"></circle>
                        <path d="M21 16l-5-5L5 20"></path>
                    </svg>
                    <span><?= esc(lang('Media.tab_photos')) ?></span>
                    <?php if (! empty($photos)): ?>
                        <span class="media-tabs__count"><?= esc((string) count($photos)) ?></span>
                    <?php endif; ?>
                </button>

                <button class="media-tabs__tab" type="button" role="tab"
                        id="tab-videos" aria-selected="false" aria-controls="panel-videos"
                        data-tab="videos" tabindex="-1">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="3" y="5" width="18" height="14" rx="2.5"></rect>
                        <path d="M10 9.2v5.6l4.5-2.8z" fill="currentColor" stroke="none"></path>
                    </svg>
                    <span><?= esc(lang('Media.tab_videos')) ?></span>
                    <?php if (! empty($videos)): ?>
                        <span class="media-tabs__count"><?= esc((string) count($videos)) ?></span>
                    <?php endif; ?>
                </button>
            </div>
        </div>

        <!-- ============ FOTOĞRAFLAR ============ -->
        <div class="media-panel is-active" role="tabpanel" id="panel-photos"
             aria-labelledby="tab-photos" data-panel="photos" tabindex="0">

            <?php if (empty($photos) && empty($categories)): ?>
                <div class="media-empty">
                    <span class="media-empty__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="16" rx="2.5"></rect>
                            <circle cx="8.5" cy="9.5" r="1.7"></circle>
                            <path d="M21 16l-5-5L5 20"></path>
                        </svg>
                    </span>
                    <h2 class="media-empty__title"><?= esc(lang('Media.photos_empty_title')) ?></h2>
                    <p class="media-empty__text"><?= esc(lang('Media.photos_empty_text')) ?></p>
                </div>
            <?php else: ?>
                <!-- Kategori filtreleri -->
                <div class="media-filters" role="group"
                     aria-label="<?= esc(lang('Media.filter_aria'), 'attr') ?>" data-filters>
                    <button class="media-filter is-active" type="button" data-filter="all" aria-pressed="true">
                        <span><?= esc(lang('Media.filter_all')) ?></span>
                        <span class="media-filter__count"><?= esc((string) count($photos)) ?></span>
                    </button>
                    <?php foreach ($categories as $cat): ?>
                        <button class="media-filter" type="button"
                                data-filter="<?= esc($cat['slug'], 'attr') ?>" aria-pressed="false">
                            <span><?= esc($cat['name']) ?></span>
                            <?php if ($cat['count'] > 0): ?>
                                <span class="media-filter__count"><?= esc((string) $cat['count']) ?></span>
                            <?php endif; ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <ul class="media-grid" data-lightbox-group data-photo-grid>
                    <?php foreach ($photos as $photo): ?>
                        <li class="media-grid__item" data-cat="<?= esc($photo['cat'], 'attr') ?>">
                            <button class="media-thumb" type="button"
                                    data-lightbox data-src="<?= esc($photo['url'], 'attr') ?>"
                                    data-alt="<?= esc($photo['alt'], 'attr') ?>"
                                    aria-label="<?= esc($photo['alt'], 'attr') ?>">
                                <img src="<?= esc($photo['url'], 'attr') ?>" alt="<?= esc($photo['alt'], 'attr') ?>"
                                     loading="lazy" decoding="async">
                                <span class="media-thumb__zoom" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="11" cy="11" r="7"></circle>
                                        <path d="M21 21l-4.3-4.3M11 8v6M8 11h6"></path>
                                    </svg>
                                </span>
                            </button>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <!-- Seçili kategoride görsel yoksa (örn. Önce/Sonra henüz boş) -->
                <div class="media-empty" data-photo-empty hidden>
                    <span class="media-empty__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="16" rx="2.5"></rect>
                            <circle cx="8.5" cy="9.5" r="1.7"></circle>
                            <path d="M21 16l-5-5L5 20"></path>
                        </svg>
                    </span>
                    <h2 class="media-empty__title"><?= esc(lang('Media.before_after_empty_title')) ?></h2>
                    <p class="media-empty__text"><?= esc(lang('Media.before_after_empty')) ?></p>
                </div>
            <?php endif; ?>
        </div>

        <!-- ============ VİDEOLAR ============ -->
        <div class="media-panel" role="tabpanel" id="panel-videos"
             aria-labelledby="tab-videos" data-panel="videos" tabindex="0" hidden>


            <ul class="media-grid media-grid--video" data-lightbox-group>
                <?php foreach ($videos as $video): ?>
                    <li class="media-grid__item">
                        <?php if (! empty($video['placeholder'])): ?>
                            <button class="video-card video-card--placeholder" type="button"
                                    data-lightbox data-src="<?= esc($video['thumb'], 'attr') ?>"
                                    data-alt="<?= esc($video['alt'], 'attr') ?>"
                                    aria-label="<?= esc($video['title'], 'attr') ?>">
                                <span class="video-card__frame">
                                    <?php if ($video['thumb'] !== ''): ?>
                                        <img src="<?= esc($video['thumb'], 'attr') ?>" alt="<?= esc($video['alt'], 'attr') ?>" loading="lazy" decoding="async">
                                    <?php endif; ?>
                                    <span class="video-card__play" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M9 7.5v9l7-4.5z"></path></svg>
                                    </span>
                                    <span class="video-card__badge"><?= esc(lang('Media.video_badge')) ?></span>
                                </span>
                                <span class="video-card__title"><?= esc($video['title']) ?></span>
                            </button>
                        <?php else: ?>
                            <article class="video-card">
                                <div class="video-card__frame">
                                    <video controls preload="metadata" playsinline
                                           <?= $video['poster'] !== null ? 'poster="' . esc($video['poster'], 'attr') . '"' : '' ?>>
                                        <source src="<?= esc($video['url'], 'attr') ?>" type="<?= esc($video['mime'], 'attr') ?>">
                                    </video>
                                </div>
                                <h3 class="video-card__title"><?= esc($video['title']) ?></h3>
                            </article>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

    </div>
</section>

<!-- Lightbox (fotoğraf / yer tutucu büyütme) -->
<div class="lightbox" data-lightbox-modal hidden>
    <div class="lightbox__backdrop" data-lightbox-close></div>
    <button class="lightbox__btn lightbox__close" type="button" data-lightbox-close
            aria-label="<?= esc(lang('Media.lightbox_close'), 'attr') ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"></path></svg>
    </button>
    <button class="lightbox__btn lightbox__nav lightbox__nav--prev" type="button" data-lightbox-prev
            aria-label="<?= esc(lang('Media.lightbox_prev'), 'attr') ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 6l-6 6 6 6"></path></svg>
    </button>
    <figure class="lightbox__figure">
        <img class="lightbox__img" data-lightbox-img src="" alt="">
    </figure>
    <button class="lightbox__btn lightbox__nav lightbox__nav--next" type="button" data-lightbox-next
            aria-label="<?= esc(lang('Media.lightbox_next'), 'attr') ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 6l6 6-6 6"></path></svg>
    </button>
</div>

<?php $this->endSection() ?>

<?php $this->section('scripts') ?>
<script>
(function () {
    'use strict';

    /* ---------------- Sekmeler ---------------- */
    var root = document.querySelector('[data-tabs]');
    if (root) {
        var tabs   = Array.prototype.slice.call(root.querySelectorAll('[data-tab]'));
        var panels = Array.prototype.slice.call(document.querySelectorAll('[data-panel]'));

        function activate(name, focus) {
            tabs.forEach(function (t) {
                var on = t.getAttribute('data-tab') === name;
                t.classList.toggle('is-active', on);
                t.setAttribute('aria-selected', on ? 'true' : 'false');
                t.tabIndex = on ? 0 : -1;
                if (on && focus) { t.focus(); }
            });
            panels.forEach(function (p) {
                var on = p.getAttribute('data-panel') === name;
                p.classList.toggle('is-active', on);
                if (on) { p.removeAttribute('hidden'); } else { p.setAttribute('hidden', ''); }
            });
            if (history.replaceState) {
                history.replaceState(null, '', '#' + (name === 'videos' ? 'videolar' : 'fotograflar'));
            }
        }

        tabs.forEach(function (tab, i) {
            tab.addEventListener('click', function () { activate(tab.getAttribute('data-tab')); });
            tab.addEventListener('keydown', function (e) {
                var dir = e.key === 'ArrowRight' ? 1 : e.key === 'ArrowLeft' ? -1 : 0;
                if (!dir) { return; }
                e.preventDefault();
                var next = tabs[(i + dir + tabs.length) % tabs.length];
                activate(next.getAttribute('data-tab'), true);
            });
        });

        // Derin bağlantı: #videolar / #videos → Videolar sekmesi
        var h = (location.hash || '').toLowerCase();
        if (h.indexOf('video') !== -1) { activate('videos'); }
    }

    /* ---------------- Kategori filtreleri (Fotoğraflar) ---------------- */
    var filterBar  = document.querySelector('[data-filters]');
    var photoGrid  = document.querySelector('[data-photo-grid]');
    var photoEmpty = document.querySelector('[data-photo-empty]');
    if (filterBar && photoGrid) {
        var filterBtns = Array.prototype.slice.call(filterBar.querySelectorAll('[data-filter]'));
        var photoItems = Array.prototype.slice.call(photoGrid.querySelectorAll('.media-grid__item'));

        function applyFilter(cat) {
            var visible = 0;
            photoItems.forEach(function (li) {
                var on = cat === 'all' || li.getAttribute('data-cat') === cat;
                li.classList.toggle('is-hidden', !on);
                if (on) { visible++; }
            });
            filterBtns.forEach(function (b) {
                var active = b.getAttribute('data-filter') === cat;
                b.classList.toggle('is-active', active);
                b.setAttribute('aria-pressed', active ? 'true' : 'false');
            });
            photoGrid.style.display = visible === 0 ? 'none' : '';
            if (photoEmpty) {
                if (visible === 0) { photoEmpty.removeAttribute('hidden'); }
                else { photoEmpty.setAttribute('hidden', ''); }
            }
        }

        filterBtns.forEach(function (b) {
            b.addEventListener('click', function () { applyFilter(b.getAttribute('data-filter')); });
        });
    }

    /* ---------------- Lightbox (sekmeye göre gruplanır) ---------------- */
    var modal = document.querySelector('[data-lightbox-modal]');
    if (modal) {
        var imgEl     = modal.querySelector('[data-lightbox-img]');
        var prevBtn   = modal.querySelector('[data-lightbox-prev]');
        var nextBtn   = modal.querySelector('[data-lightbox-next]');
        var group     = [];
        var index     = 0;
        var lastFocus = null;

        function show(i) {
            index = (i + group.length) % group.length;
            var t = group[index];
            imgEl.src = t.getAttribute('data-src');
            imgEl.alt = t.getAttribute('data-alt') || '';
        }
        function openFrom(trigger) {
            var scope = trigger.closest('[data-lightbox-group]') || document;
            // Yalnızca o an görünür (filtrelenmemiş) görseller gezilir.
            group = Array.prototype.slice.call(scope.querySelectorAll('[data-lightbox]')).filter(function (el) {
                var li = el.closest('.media-grid__item');
                return !li || !li.classList.contains('is-hidden');
            });
            var solo = group.length < 2;
            if (prevBtn) { prevBtn.style.display = solo ? 'none' : ''; }
            if (nextBtn) { nextBtn.style.display = solo ? 'none' : ''; }
            lastFocus = document.activeElement;
            show(group.indexOf(trigger));
            modal.removeAttribute('hidden');
            modal.classList.add('is-open');
            document.body.style.overflow = 'hidden';
            var closeBtn = modal.querySelector('[data-lightbox-close]');
            if (closeBtn) { closeBtn.focus(); }
        }
        function close() {
            modal.classList.remove('is-open');
            modal.setAttribute('hidden', '');
            document.body.style.overflow = '';
            imgEl.src = '';
            if (lastFocus && lastFocus.focus) { lastFocus.focus(); }
        }

        Array.prototype.slice.call(document.querySelectorAll('[data-lightbox]')).forEach(function (t) {
            t.addEventListener('click', function () { openFrom(t); });
        });
        modal.querySelectorAll('[data-lightbox-close]').forEach(function (b) {
            b.addEventListener('click', close);
        });
        if (prevBtn) { prevBtn.addEventListener('click', function () { show(index - 1); }); }
        if (nextBtn) { nextBtn.addEventListener('click', function () { show(index + 1); }); }

        document.addEventListener('keydown', function (e) {
            if (modal.hasAttribute('hidden')) { return; }
            if (e.key === 'Escape') { close(); }
            else if (e.key === 'ArrowRight') { show(index + 1); }
            else if (e.key === 'ArrowLeft') { show(index - 1); }
        });
    }
})();
</script>
<?php $this->endSection() ?>
