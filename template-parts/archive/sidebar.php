<?php
/** Native widgets only; no fallback content or archive queries. */
if ( ! defined( 'ABSPATH' ) || ! is_active_sidebar( 'public-archive-sidebar' ) ) { return; }
?>
<aside class="rv-archive-sidebar" aria-label="<?php esc_attr_e( 'ابزارهای آرشیو', 'revayatiran' ); ?>">
    <?php dynamic_sidebar( 'public-archive-sidebar' ); ?>
</aside>
