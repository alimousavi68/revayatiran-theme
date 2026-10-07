<?php
/** Native operator-managed sidebar; empty areas render nothing. */
if (!defined('ABSPATH')) { exit; }
$id = revayat_single_sidebar_id();
if (!$id || !is_active_sidebar($id)) { return; }
$embedded = !empty($args['embedded']);
$tag = $embedded ? 'div' : 'aside';
?>
<<?php echo $tag; ?> class="rv-reading-sidebar<?php echo $embedded ? ' rv-reading-sidebar--embedded' : ''; ?>" aria-label="<?php esc_attr_e('مطالب و ابزارهای مرتبط', 'revayatiran'); ?>">
    <?php dynamic_sidebar($id); ?>
</<?php echo $tag; ?>>
