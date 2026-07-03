<?php

/**
 * Tools page — four client-side logistics calculators.
 *
 * Field structure is fixed and mirrored by public/assets/js/tools.js;
 * shared constants come from app/Data/tools-constants.json (injected
 * below as JSON). All copy lives in app/Language/{locale}/Tools.php.
 */
$this->extend('layouts/inner');

$d = $constants['defaults'] ?? [];
?>

<?php $this->section('bodyClass') ?>tools<?php $this->endSection() ?>

<?php $this->section('content') ?>

<?= view('partials/page-hero', [
    'pageTitle'   => lang('Tools.page_title'),
    'pageLead'    => lang('Tools.page_lead'),
    'breadcrumbs' => [
        ['label' => lang('Tools.breadcrumb')],
    ],
]) ?>

<section class="tools-section">
    <div class="container">

        <nav class="tools-nav" aria-label="<?= esc(lang('Tools.page_title'), 'attr') ?>">
            <a class="tools-nav__link" href="#fuel"><?= esc(lang('Tools.c1_title')) ?></a>
            <a class="tools-nav__link" href="#pallet"><?= esc(lang('Tools.c2_title')) ?></a>
            <a class="tools-nav__link" href="#volume"><?= esc(lang('Tools.c3_title')) ?></a>
            <a class="tools-nav__link" href="#axle"><?= esc(lang('Tools.c4_title')) ?></a>
            <a class="tools-nav__link tools-nav__link--quiz" href="<?= esc(locale_url('quiz')) ?>"><?= esc(lang('Quiz.page_title')) ?> →</a>
        </nav>

        <div class="tools-grid">

            <!-- C1: Yakıt & Navlun -->
            <article class="tool-card glass-panel" id="fuel" data-tool="fuel">
                <header class="tool-card__head">
                    <h2 class="tool-card__title"><?= esc(lang('Tools.c1_title')) ?></h2>
                    <p class="tool-card__desc"><?= esc(lang('Tools.c1_desc')) ?></p>
                </header>

                <form class="tool-card__form" data-tool-form novalidate>
                    <label class="tool-field">
                        <span><?= esc(lang('Tools.c1_distance')) ?></span>
                        <input type="number" name="distance" min="1" max="50000" step="any" inputmode="decimal" required>
                    </label>
                    <label class="tool-field">
                        <span><?= esc(lang('Tools.c1_consumption')) ?></span>
                        <input type="number" name="consumption" min="5" max="100" step="any" inputmode="decimal"
                               value="<?= esc((string) ($d['consumption_l100'] ?? 32), 'attr') ?>">
                    </label>
                    <label class="tool-field">
                        <span><?= esc(lang('Tools.c1_fuel_price')) ?></span>
                        <input type="number" name="fuel_price" min="1" max="1000" step="any" inputmode="decimal"
                               value="<?= esc((string) ($d['fuel_price_tl'] ?? 50), 'attr') ?>">
                    </label>
                    <label class="tool-field">
                        <span><?= esc(lang('Tools.c1_toll')) ?></span>
                        <input type="number" name="toll" min="0" max="1000000" step="any" inputmode="decimal" value="0">
                    </label>
                    <label class="tool-field">
                        <span><?= esc(lang('Tools.c1_cargo')) ?></span>
                        <input type="number" name="cargo" min="0" max="60" step="any" inputmode="decimal">
                    </label>
                    <label class="tool-field tool-field--check">
                        <input type="checkbox" name="round_trip">
                        <span><?= esc(lang('Tools.c1_round_trip')) ?></span>
                    </label>

                    <div class="tool-card__actions">
                        <button type="submit" class="btn btn--primary"><?= esc(lang('Tools.calculate')) ?></button>
                        <button type="reset" class="btn btn--outline"><?= esc(lang('Tools.reset')) ?></button>
                    </div>
                </form>

                <div class="tool-card__results" data-results hidden>
                    <h3 class="tool-card__results-title"><?= esc(lang('Tools.results_title')) ?></h3>
                    <dl class="tool-results">
                        <div class="tool-results__row"><dt><?= esc(lang('Tools.c1_r_liters')) ?></dt><dd data-r="liters">–</dd></div>
                        <div class="tool-results__row"><dt><?= esc(lang('Tools.c1_r_fuel_cost')) ?></dt><dd data-r="fuel_cost">–</dd></div>
                        <div class="tool-results__row"><dt><?= esc(lang('Tools.c1_r_total')) ?></dt><dd data-r="total">–</dd></div>
                        <div class="tool-results__row"><dt><?= esc(lang('Tools.c1_r_per_km')) ?></dt><dd data-r="per_km">–</dd></div>
                        <div class="tool-results__row" data-row="per_ton" hidden><dt><?= esc(lang('Tools.c1_r_per_ton')) ?></dt><dd data-r="per_ton">–</dd></div>
                    </dl>
                </div>

                <p class="tool-card__note"><?= esc(lang('Tools.c1_note')) ?></p>
            </article>

            <!-- C2: Palet -->
            <article class="tool-card glass-panel" id="pallet" data-tool="pallet">
                <header class="tool-card__head">
                    <h2 class="tool-card__title"><?= esc(lang('Tools.c2_title')) ?></h2>
                    <p class="tool-card__desc"><?= esc(lang('Tools.c2_desc')) ?></p>
                </header>

                <form class="tool-card__form" data-tool-form novalidate>
                    <label class="tool-field">
                        <span><?= esc(lang('Tools.c2_preset')) ?></span>
                        <select name="preset">
                            <option value="tent"><?= esc(lang('Tools.c2_preset_tent')) ?></option>
                            <option value="frigo"><?= esc(lang('Tools.c2_preset_frigo')) ?></option>
                            <option value="custom"><?= esc(lang('Tools.c2_preset_custom')) ?></option>
                        </select>
                    </label>
                    <label class="tool-field">
                        <span><?= esc(lang('Tools.c2_length')) ?></span>
                        <input type="number" name="length" min="1" max="30" step="any" inputmode="decimal" required>
                    </label>
                    <label class="tool-field">
                        <span><?= esc(lang('Tools.c2_width')) ?></span>
                        <input type="number" name="width" min="1" max="4" step="any" inputmode="decimal" required>
                    </label>
                    <label class="tool-field">
                        <span><?= esc(lang('Tools.c2_pallet_type')) ?></span>
                        <select name="pallet">
                            <option value="eur"><?= esc(lang('Tools.c2_pallet_eur')) ?></option>
                            <option value="ind"><?= esc(lang('Tools.c2_pallet_ind')) ?></option>
                        </select>
                    </label>

                    <div class="tool-card__actions">
                        <button type="submit" class="btn btn--primary"><?= esc(lang('Tools.calculate')) ?></button>
                        <button type="reset" class="btn btn--outline"><?= esc(lang('Tools.reset')) ?></button>
                    </div>
                </form>

                <div class="tool-card__results" data-results hidden>
                    <h3 class="tool-card__results-title"><?= esc(lang('Tools.results_title')) ?></h3>
                    <dl class="tool-results">
                        <div class="tool-results__row"><dt><?= esc(lang('Tools.c2_r_count')) ?></dt><dd data-r="count">–</dd></div>
                        <div class="tool-results__row"><dt><?= esc(lang('Tools.c2_r_layout')) ?></dt><dd data-r="layout">–</dd></div>
                    </dl>
                </div>

                <p class="tool-card__note"><?= esc(lang('Tools.c2_note')) ?></p>
            </article>

            <!-- C3: Hacim–Tonaj -->
            <article class="tool-card glass-panel" id="volume" data-tool="volume">
                <header class="tool-card__head">
                    <h2 class="tool-card__title"><?= esc(lang('Tools.c3_title')) ?></h2>
                    <p class="tool-card__desc"><?= esc(lang('Tools.c3_desc')) ?></p>
                </header>

                <form class="tool-card__form" data-tool-form novalidate>
                    <label class="tool-field">
                        <span><?= esc(lang('Tools.c3_direction')) ?></span>
                        <select name="direction">
                            <option value="v2w"><?= esc(lang('Tools.c3_dir_v2w')) ?></option>
                            <option value="w2v"><?= esc(lang('Tools.c3_dir_w2v')) ?></option>
                        </select>
                    </label>
                    <label class="tool-field" data-field="volume">
                        <span><?= esc(lang('Tools.c3_volume')) ?></span>
                        <input type="number" name="volume" min="0.1" max="200" step="any" inputmode="decimal">
                    </label>
                    <label class="tool-field" data-field="weight" hidden>
                        <span><?= esc(lang('Tools.c3_weight')) ?></span>
                        <input type="number" name="weight" min="0.1" max="100" step="any" inputmode="decimal">
                    </label>
                    <label class="tool-field">
                        <span><?= esc(lang('Tools.c3_product')) ?></span>
                        <select name="product">
                            <option value="wheat"><?= esc(lang('Tools.c3_p_wheat')) ?></option>
                            <option value="flour"><?= esc(lang('Tools.c3_p_flour')) ?></option>
                            <option value="cement"><?= esc(lang('Tools.c3_p_cement')) ?></option>
                            <option value="water"><?= esc(lang('Tools.c3_p_water')) ?></option>
                            <option value="diesel"><?= esc(lang('Tools.c3_p_diesel')) ?></option>
                            <option value="gasoline"><?= esc(lang('Tools.c3_p_gasoline')) ?></option>
                            <option value="milk"><?= esc(lang('Tools.c3_p_milk')) ?></option>
                            <option value="veg_oil"><?= esc(lang('Tools.c3_p_veg_oil')) ?></option>
                            <option value="lpg"><?= esc(lang('Tools.c3_p_lpg')) ?></option>
                            <option value="custom"><?= esc(lang('Tools.c3_p_custom')) ?></option>
                        </select>
                    </label>
                    <label class="tool-field" data-field="density" hidden>
                        <span><?= esc(lang('Tools.c3_density')) ?></span>
                        <input type="number" name="density" min="1" max="5000" step="any" inputmode="decimal">
                    </label>

                    <div class="tool-card__actions">
                        <button type="submit" class="btn btn--primary"><?= esc(lang('Tools.calculate')) ?></button>
                        <button type="reset" class="btn btn--outline"><?= esc(lang('Tools.reset')) ?></button>
                    </div>
                </form>

                <div class="tool-card__results" data-results hidden>
                    <h3 class="tool-card__results-title"><?= esc(lang('Tools.results_title')) ?></h3>
                    <dl class="tool-results">
                        <div class="tool-results__row" data-row="weight"><dt><?= esc(lang('Tools.c3_r_weight')) ?></dt><dd data-r="weight">–</dd></div>
                        <div class="tool-results__row" data-row="volume" hidden><dt><?= esc(lang('Tools.c3_r_volume')) ?></dt><dd data-r="volume">–</dd></div>
                    </dl>
                </div>

                <p class="tool-card__note"><?= esc(lang('Tools.c3_note')) ?></p>
            </article>

            <!-- C4: Aks yükü -->
            <article class="tool-card glass-panel" id="axle" data-tool="axle"
                     data-msg-ok="<?= esc(lang('Tools.c4_ok'), 'attr') ?>"
                     data-msg-warn-axles="<?= esc(lang('Tools.c4_warn_axles'), 'attr') ?>"
                     data-msg-warn-kingpin="<?= esc(lang('Tools.c4_warn_kingpin'), 'attr') ?>">
                <header class="tool-card__head">
                    <h2 class="tool-card__title"><?= esc(lang('Tools.c4_title')) ?></h2>
                    <p class="tool-card__desc"><?= esc(lang('Tools.c4_desc')) ?></p>
                </header>

                <form class="tool-card__form" data-tool-form novalidate>
                    <label class="tool-field">
                        <span><?= esc(lang('Tools.c4_empty')) ?></span>
                        <input type="number" name="empty" min="1000" max="20000" step="any" inputmode="decimal"
                               value="<?= esc((string) ($d['trailer_empty_kg'] ?? 6500), 'attr') ?>">
                    </label>
                    <label class="tool-field">
                        <span><?= esc(lang('Tools.c4_payload')) ?></span>
                        <input type="number" name="payload" min="0" max="60000" step="any" inputmode="decimal" required>
                    </label>
                    <label class="tool-field">
                        <span><?= esc(lang('Tools.c4_wheelbase')) ?></span>
                        <input type="number" name="wheelbase" min="3" max="12" step="any" inputmode="decimal"
                               value="<?= esc((string) ($d['kingpin_to_axle_m'] ?? 7.7), 'attr') ?>">
                    </label>
                    <label class="tool-field">
                        <span><?= esc(lang('Tools.c4_load_center')) ?></span>
                        <input type="number" name="load_center" min="0" max="12" step="any" inputmode="decimal"
                               value="<?= esc((string) ($d['load_center_m'] ?? 3.85), 'attr') ?>">
                    </label>

                    <div class="tool-card__actions">
                        <button type="submit" class="btn btn--primary"><?= esc(lang('Tools.calculate')) ?></button>
                        <button type="reset" class="btn btn--outline"><?= esc(lang('Tools.reset')) ?></button>
                    </div>
                </form>

                <div class="tool-card__results" data-results hidden>
                    <h3 class="tool-card__results-title"><?= esc(lang('Tools.results_title')) ?></h3>
                    <dl class="tool-results">
                        <div class="tool-results__row"><dt><?= esc(lang('Tools.c4_r_kingpin')) ?></dt><dd data-r="kingpin">–</dd></div>
                        <div class="tool-results__row"><dt><?= esc(lang('Tools.c4_r_axles')) ?></dt><dd data-r="axles">–</dd></div>
                    </dl>
                    <p class="tool-card__status" data-status role="status"></p>
                </div>

                <p class="tool-card__note"><?= esc(lang('Tools.c4_note')) ?></p>
            </article>
        </div>

        <p class="tools-disclaimer"><?= esc(lang('Tools.disclaimer')) ?></p>
    </div>
</section>

<script type="application/json" data-tools-constants><?= json_encode($constants, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>

<?php $this->endSection() ?>

<?php $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/tools.css') ?>?v=<?= is_file(FCPATH . 'assets/css/tools.css') ? filemtime(FCPATH . 'assets/css/tools.css') : '1' ?>">
<?php /* Sinematik kaplama: cam panel reçetesi (cinema.css) + hesaplayıcıya özel
         katman (tools-cinema.css). Sıra önemli: tools.css'ten SONRA gelirler ki
         .glass-panel kart zeminini, tools-cinema da sonuç panelini ezebilsin. */ ?>
<link rel="stylesheet" href="<?= base_url('assets/css/cinema.css') ?>?v=<?= is_file(FCPATH . 'assets/css/cinema.css') ? filemtime(FCPATH . 'assets/css/cinema.css') : '1' ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/tools-cinema.css') ?>?v=<?= is_file(FCPATH . 'assets/css/tools-cinema.css') ? filemtime(FCPATH . 'assets/css/tools-cinema.css') : '1' ?>">
<?php $this->endSection() ?>

<?php $this->section('scripts') ?>
<script src="<?= base_url('assets/js/tools.js') ?>?v=<?= is_file(FCPATH . 'assets/js/tools.js') ? filemtime(FCPATH . 'assets/js/tools.js') : '1' ?>" defer></script>
<?php /* Canlı sayaç katmanı — yalnız sunum; tools.js'ten SONRA yüklenir. */ ?>
<script src="<?= base_url('assets/js/tools-cinema.js') ?>?v=<?= is_file(FCPATH . 'assets/js/tools-cinema.js') ? filemtime(FCPATH . 'assets/js/tools-cinema.js') : '1' ?>" defer></script>
<?php $this->endSection() ?>
