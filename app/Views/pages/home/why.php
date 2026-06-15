<?php

/**
 * Home / AI trailer assistant
 *
 * The interface works as a lightweight demo today. The same form can be
 * connected to the recommendation API later without changing the layout.
 */
$examples = lang('Home.ai_examples');
$examples = is_array($examples) ? $examples : [];
?>
<section class="ai-assistant" id="ai-assistant" data-ai-assistant data-demo-response="<?= esc(lang('Home.ai_demo_response'), 'attr') ?>">
    <span class="ai-assistant__glow ai-assistant__glow--one" aria-hidden="true"></span>
    <span class="ai-assistant__glow ai-assistant__glow--two" aria-hidden="true"></span>
    <span class="ai-assistant__grid" aria-hidden="true"></span>

    <div class="container ai-assistant__inner">
        <header class="ai-assistant__header" data-aos="fade-up">
            <span class="ai-assistant__eyebrow">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M12 3v3M12 18v3M3 12h3M18 12h3M5.6 5.6l2.1 2.1M16.3 16.3l2.1 2.1M18.4 5.6l-2.1 2.1M7.7 16.3l-2.1 2.1"></path>
                    <circle cx="12" cy="12" r="4"></circle>
                </svg>
                <?= esc(lang('Home.ai_eyebrow')) ?>
            </span>
            <h2 class="ai-assistant__title"><?= esc(lang('Home.ai_title')) ?></h2>
            <p class="ai-assistant__lead"><?= esc(lang('Home.ai_lead')) ?></p>
        </header>

        <div class="ai-console" data-aos="fade-up" data-aos-delay="100">
            <aside class="ai-console__examples">
                <span class="ai-console__examples-title"><?= esc(lang('Home.ai_examples_title')) ?></span>
                <div class="ai-console__example-list">
                    <?php foreach ($examples as $example): ?>
                        <button class="ai-console__example" type="button" data-ai-example="<?= esc($example, 'attr') ?>">
                            <span aria-hidden="true">&#8594;</span>
                            <?= esc($example) ?>
                        </button>
                    <?php endforeach; ?>
                </div>
                <div class="ai-console__note">
                    <span class="ai-console__note-icon" aria-hidden="true">i</span>
                    <span><?= esc(lang('Home.ai_note')) ?></span>
                </div>
            </aside>

            <div class="ai-console__chat">
                <div class="ai-console__messages" data-ai-messages aria-live="polite">
                    <div class="ai-message ai-message--assistant">
                        <span class="ai-message__avatar" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <rect x="5" y="7" width="14" height="11" rx="4"></rect>
                                <path d="M9 11h.01M15 11h.01M9 15h6M12 4v3"></path>
                            </svg>
                        </span>
                        <p><?= esc(lang('Home.ai_welcome')) ?></p>
                    </div>
                </div>

                <form class="ai-console__form" data-ai-form>
                    <label class="visually-hidden" for="ai-assistant-input"><?= esc(lang('Home.ai_input_label')) ?></label>
                    <span class="ai-console__input-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="12" cy="8" r="3"></circle>
                            <path d="M6 20c0-3.3 2.7-6 6-6s6 2.7 6 6"></path>
                        </svg>
                    </span>
                    <input id="ai-assistant-input" type="text" data-ai-input placeholder="<?= esc(lang('Home.ai_placeholder'), 'attr') ?>" autocomplete="off">
                    <button class="ai-console__send" type="submit" aria-label="<?= esc(lang('Home.ai_send'), 'attr') ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M22 2 11 13"></path>
                            <path d="m22 2-7 20-4-9-9-4z"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        <p class="ai-assistant__disclaimer"><?= esc(lang('Home.ai_disclaimer')) ?></p>
    </div>
</section>
