<?php
/**
 * دکمه اسکرول به بالای صفحه (Back to Top Button)
 *
 * @package RevayatIran
 * @subpackage Template_Parts/Footer
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<button id="back-to-top" class="back-to-top" aria-label="<?php esc_attr_e('بازگشت به بالای صفحه', 'revayatiran'); ?>" title="<?php esc_attr_e('بازگشت به بالا', 'revayatiran'); ?>">
    <svg class="back-to-top__progress" width="48" height="48" viewBox="0 0 48 48">
        <circle class="back-to-top__circle-bg" cx="24" cy="24" r="21" fill="none" stroke-width="2.5" />
        <circle class="back-to-top__circle-progress" cx="24" cy="24" r="21" fill="none" stroke-width="2.5" />
    </svg>
    <i class="ph ph-arrow-up back-to-top__icon"></i>
</button>
