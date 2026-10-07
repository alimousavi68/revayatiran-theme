<?php
/** Shared return path for a dossier's selected articles. */
if (!defined('ABSPATH') || post_password_required()) { exit; }
$parents = class_exists('Revayat_Companion_Dossier_Links') ? Revayat_Companion_Dossier_Links::parents(get_the_ID()) : [];
if (!$parents) { return; }
?>
<nav class="rv-dossier-return" aria-label="<?php esc_attr_e('بازگشت به پرونده‌های مرتبط', 'revayatiran'); ?>">
    <p><i class="ph ph-folder-open" aria-hidden="true"></i> <?php esc_html_e('پرونده‌های مرتبط با این مطلب', 'revayatiran'); ?></p>
    <?php foreach ($parents as $parent) : ?>
        <a href="<?php echo esc_url($parent['url']); ?>">
            <span class="rv-dossier-return__title"><?php echo esc_html($parent['title']); ?></span>
            <span class="rv-dossier-return__action"><?php esc_html_e('بازگشت به پرونده', 'revayatiran'); ?> <i class="ph ph-arrow-left" aria-hidden="true"></i></span>
        </a>
    <?php endforeach; ?>
</nav>
