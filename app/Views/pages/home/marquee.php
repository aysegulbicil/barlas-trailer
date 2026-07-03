<?php

/**
 * Home / Güven şeridi (marquee) — editoryal akan bant
 * =============================================================================
 * Home.marquee dizisindeki kısa iddiaları (sertifika, ihracat, miras) tek
 * satırlık sonsuz bantta akıtır: ITEM ✦ ITEM ✦ ... Parça iki kez basılır
 * (ikinci kopya aria-hidden) → CSS translateX(-50%) döngüsü dikişsizdir;
 * RTL'de yön cinema-editorial.css'teki ayna keyframe ile döner.
 *
 * Saf CSS animasyon (~40s linear): reduced-motion / cine-off'ta durur,
 * cine-lite'ta yavaşlar, hover'da duraklar. Koyu bant YOK — şerit sayfa
 * zemininde ince çizgiler arasında oturur. Stil: cinema-editorial.css.
 * Dizi boş/geçersizse bant HİÇ basılmaz (varlıklar yine yüklenir —
 * numaratör + kontur kelime banttan bağımsız çalışır).
 */
$claims = lang('Home.marquee');
$claims = is_array($claims) ? array_values(array_filter($claims, 'is_string')) : [];
?>
<?php if ($claims !== []): ?>
<section class="marquee" aria-label="<?= esc(lang('Home.references_eyebrow'), 'attr') ?>">
    <div class="marquee__track" data-marquee>
        <?php for ($pass = 0; $pass < 2; $pass++): ?>
            <ul class="marquee__group"<?= $pass === 1 ? ' aria-hidden="true"' : '' ?>>
                <?php foreach ($claims as $i => $claim): ?>
                    <li class="marquee__item<?= $i % 2 === 1 ? ' marquee__item--glow' : '' ?>">
                        <span class="marquee__star" aria-hidden="true">&#10022;</span>
                        <span class="marquee__text"><?= esc($claim) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endfor; ?>
    </div>
</section>
<?php endif; ?>

<?php /* Editoryal katman varlıkları — layout'a DOKUNMADAN styles/scripts
         section'larıyla yüklenir (numbers.php ile aynı kalıp). Layout ileride
         merkezî yüklerse aynı URL tarayıcı önbelleğinden gelir; JS kendini
         çift kuruluma karşı korur (data-cine-editorial bayrağı). */ ?>
<?php $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/cinema-editorial.css') ?>?v=<?= is_file(FCPATH . 'assets/css/cinema-editorial.css') ? filemtime(FCPATH . 'assets/css/cinema-editorial.css') : '1' ?>">
<?php $this->endSection() ?>

<?php $this->section('scripts') ?>
<script src="<?= base_url('assets/js/cinema-editorial.js') ?>?v=<?= is_file(FCPATH . 'assets/js/cinema-editorial.js') ? filemtime(FCPATH . 'assets/js/cinema-editorial.js') : '1' ?>" defer></script>
<?php $this->endSection() ?>
