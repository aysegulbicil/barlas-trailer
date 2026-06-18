<?php
/**
 * İletişim formu — kullanıcıya giden otomatik onay (auto-reply) e-postası.
 *
 * Beklenen değişkenler (Contact::autoReply() tarafından verilir):
 *   $locale, $siteName, $preheader, $greeting, $intro,
 *   $summaryTitle, $rows (label => value), $closing,
 *   $regards, $team, $autoNote
 *
 * Not: E-posta istemcileri için tablo tabanlı düzen + satır içi CSS kullanılır.
 */
$isRtl = ($locale ?? 'tr') === 'ar';
$dir   = $isRtl ? 'rtl' : 'ltr';
$align = $isRtl ? 'right' : 'left';
$accent = '#DC4814';
$ink    = '#0f172a';
$muted  = '#64748b';
$line   = '#e2e8f0';
$bg     = '#f1f5f9';
$headerBg   = '#005BAA'; // marka mavisi — üst başlık bandı
$headerLine = '#00467f'; // koyu mavi — üst ince çizgi
?>
<!DOCTYPE html>
<html lang="<?= esc($locale ?? 'tr', 'attr') ?>" dir="<?= $dir ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title><?= esc($siteName) ?></title>
</head>
<body style="margin:0; padding:0; background:<?= $bg ?>; -webkit-text-size-adjust:100%;">

    <!-- Önizleme metni (gelen kutusunda konu yanında görünür, gövdede gizli) -->
    <div style="display:none; max-height:0; overflow:hidden; opacity:0; mso-hide:all;">
        <?= esc($preheader) ?>
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:<?= $bg ?>; padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0"
                       style="width:600px; max-width:600px; background:#ffffff; border-radius:14px; overflow:hidden; border:1px solid <?= $line ?>; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">

                    <!-- Üst şerit / marka -->
                    <tr>
                        <td style="background:<?= $headerBg ?>; padding:22px 32px; border-top:4px solid <?= $headerLine ?>;" dir="<?= $dir ?>" align="<?= $align ?>">
                            <span style="color:#ffffff; font-size:20px; font-weight:700; letter-spacing:.2px;"><?= esc($siteName) ?></span>
                        </td>
                    </tr>

                    <!-- Gövde -->
                    <tr>
                        <td style="padding:32px 32px 8px 32px;" dir="<?= $dir ?>" align="<?= $align ?>">
                            <p style="margin:0 0 14px; color:<?= $ink ?>; font-size:16px; font-weight:600;"><?= esc($greeting) ?></p>
                            <p style="margin:0 0 22px; color:#334155; font-size:15px; line-height:1.6;"><?= esc($intro) ?></p>
                        </td>
                    </tr>

                    <!-- Özet kartı -->
                    <tr>
                        <td style="padding:0 32px 8px 32px;" dir="<?= $dir ?>" align="<?= $align ?>">
                            <p style="margin:0 0 10px; color:<?= $muted ?>; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.6px;"><?= esc($summaryTitle) ?></p>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                   style="background:<?= $bg ?>; border:1px solid <?= $line ?>; border-radius:10px;">
                                <?php foreach (($rows ?? []) as $label => $value): ?>
                                    <tr>
                                        <td style="padding:10px 16px; border-bottom:1px solid <?= $line ?>; color:<?= $muted ?>; font-size:13px; vertical-align:top; white-space:nowrap;" dir="<?= $dir ?>" align="<?= $align ?>">
                                            <?= esc($label) ?>
                                        </td>
                                        <td style="padding:10px 16px; border-bottom:1px solid <?= $line ?>; color:<?= $ink ?>; font-size:14px; line-height:1.55;" dir="<?= $dir ?>" align="<?= $align ?>">
                                            <?= nl2br(esc($value)) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </td>
                    </tr>

                    <!-- Kapanış -->
                    <tr>
                        <td style="padding:22px 32px 6px 32px;" dir="<?= $dir ?>" align="<?= $align ?>">
                            <p style="margin:0 0 22px; color:#334155; font-size:15px; line-height:1.6;"><?= esc($closing) ?></p>
                            <p style="margin:0; color:<?= $ink ?>; font-size:15px; line-height:1.5;">
                                <?= esc($regards) ?><br>
                                <strong><?= esc($team) ?></strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Alt bilgi -->
                    <tr>
                        <td style="padding:24px 32px 28px 32px;" dir="<?= $dir ?>" align="<?= $align ?>">
                            <hr style="border:none; border-top:1px solid <?= $line ?>; margin:0 0 14px;">
                            <p style="margin:0; color:#94a3b8; font-size:12px; line-height:1.5;"><?= esc($autoNote) ?></p>
                            <p style="margin:8px 0 0; color:#cbd5e1; font-size:12px;">© <?= date('Y') ?> <?= esc($siteName) ?></p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
