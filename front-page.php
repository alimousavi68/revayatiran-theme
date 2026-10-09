<?php
/**
 * تمپلیت ویترین صفحه اصلی (Front Page)
 *
 * ساختار ثابت (Static Composition) این تمپلیت در Pass 2A تکمیل شد.
 * محتوا فقط از Provider Contract افزونه تامین می‌شود؛ داده خالی با empty state نمایش داده می‌شود.
 *
 * ترتیب sections مطابق با طراحی تایید‌شده در pass-0.5-html-mapping.md:
 * 1. Hero         → ویترین اصلی
 * 2. Daily Narrative → روایت روزانه
 * 3. Situation Room   → اتاق وضعیت (Gated)
 * 4. News Monitoring  → رصد اخبار
 * 5. Analysts Network → شبکه تحلیل‌گران
 * 6. Special Dossiers → پرونده‌های ویژه
 * 7. Media Observatory→ دیده‌بان رسانه
 * 8. Multimedia       → چندرسانه‌ای
 *
 * @package RevayatIran
 * @version 2.0.0  (Pass 2A — static composition complete)
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<div class="homepage-stage" id="homepage-content">

    <?php foreach ( revayat_home_sections() as $section => $definition ) : ?>
        <?php if ( revayat_theme_setting( $section . '_enabled' ) ) : ?>
            <?php get_template_part( 'template-parts/sections/' . $section ); ?>
        <?php endif; ?>
    <?php endforeach; ?>

</div><!-- /#homepage-content -->

<?php
get_footer();
