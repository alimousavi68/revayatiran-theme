<?php
/** Four independent native widget areas for public reading pages. */
if (!defined('ABSPATH')) { exit; }
function revayat_single_sidebar_id(): string {
    $type = get_post_type(get_queried_object_id());
    return in_array($type, ['post', 'analyst_post', 'multimedia', 'special_dossier'], true) ? 'single-' . $type . '-sidebar' : '';
}
add_action('widgets_init', function () {
    foreach (['post' => 'نوشته', 'analyst_post' => 'تحلیل', 'multimedia' => 'چندرسانه‌ای', 'special_dossier' => 'پرونده ویژه'] as $type => $label) {
        register_sidebar([
            'id' => 'single-' . $type . '-sidebar', 'name' => 'سایدبار صفحه تکی ' . $label,
            'description' => 'ابزارک‌های کنار محتوای ' . $label . '؛ در موبایل پس از محتوا نمایش داده می‌شوند.',
            'before_widget' => '<section id="%1$s" class="rv-reading-widget %2$s">', 'after_widget' => '</section>',
            'before_title' => '<h2 class="rv-reading-widget__title">', 'after_title' => '</h2>',
        ]);
    }
});
