<?php
/** Counts use the same filtered relations as the public resource list. */
if (!defined('ABSPATH')) { exit; }
$id = (int) ($args['id'] ?? 0);
if (!$id || !class_exists('Revayat_Companion_Dossier_Links')) { return; }
$links = Revayat_Companion_Dossier_Links::get($id);
$counts = $links['counts'];
if (!$counts['documents'] && !$counts['content']) { return; }
$is_hero = 'hero' === ($args['variant'] ?? '');
?>
<div class="rv-dossier-counts<?php echo $is_hero ? ' rv-dossier-counts--hero' : ''; ?>" aria-label="<?php esc_attr_e('موارد متصل به پرونده', 'revayatiran'); ?>">
    <?php if ($is_hero) : ?>
    <span class="rv-dossier-stat">
        <i class="ph ph-file-text" aria-hidden="true"></i>
        <strong><?php echo esc_html(revayat_en2fa($counts['documents'])); ?></strong>
        <span class="rv-dossier-stat__label"><?php esc_html_e('سند', 'revayatiran'); ?></span>
    </span>
    <span class="rv-dossier-stat">
        <i class="ph ph-article" aria-hidden="true"></i>
        <strong><?php echo esc_html(revayat_en2fa($counts['content'])); ?></strong>
        <span class="rv-dossier-stat__label"><?php esc_html_e('مطلب مرتبط', 'revayatiran'); ?></span>
    </span>
    <?php else : ?>
    <span><?php echo esc_html($counts['documents']); ?> <?php esc_html_e('سند', 'revayatiran'); ?></span>
    <span><?php echo esc_html($counts['content']); ?> <?php esc_html_e('مطلب مرتبط', 'revayatiran'); ?></span>
    <?php endif; ?>
</div>
