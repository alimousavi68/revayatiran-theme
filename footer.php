<?php
/**
 * پابرگ اصلی پوسته روایت ایران (Site Footer Skeleton)
 *
 * @package RevayatIran
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
</main><!-- #primary -->

    <!-- فوتر اصلی سایت -->
    <?php get_template_part('template-parts/footer/main-footer'); ?>

    <!-- دکمه شناور شبکه‌های اجتماعی -->
    <?php get_template_part('template-parts/footer/social-fab'); ?>

    <!-- دکمه بازگشت به بالا -->
    <?php get_template_part('template-parts/footer/back-to-top'); ?>

<?php wp_footer(); ?>
</body>
</html>
