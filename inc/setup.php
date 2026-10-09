<?php
/**
 * پیکربندی بنیادین و اعلان پشتیبانی‌های پوسته (Theme Setup)
 *
 * @package RevayatIran
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('revayat_theme_setup')) {
    /**
     * ثبت ویژگی‌های استاندارد وردپرس در هوک after_setup_theme
     */
    function revayat_theme_setup() {
        // ۱. ترجمه‌پذیری پوسته
        load_theme_textdomain('revayatiran', REVAYAT_THEME_DIR . '/languages');

        // ۲. پشتیبانی از برچسب عنوان خودکار برای SEO
        add_theme_support('title-tag');

        // ۳. پشتیبانی از تصاویر شاخص (Post Thumbnails)
        add_theme_support('post-thumbnails');

        // ۴. خروجی معنایی HTML5 استاندارد
        add_theme_support('html5', [
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
            'navigation-widgets',
        ]);

        // ۵. پشتیبانی از لوگوی سفارشی (Custom Logo)
        add_theme_support('custom-logo', [
            'height'      => 80,
            'width'       => 240,
            'flex-width'  => true,
            'flex-height' => true,
            'unlink-homepage-logo' => false,
        ]);

        // ۶. پشتیبانی از حالت‌های عریض ادیتور و محتوای واکنش‌گرا
        add_theme_support('align-wide');
        add_theme_support('responsive-embeds');

        // ۷. پشتیبانی از استایل‌های پایه ادیتور گوتنبرگ
        add_theme_support('editor-styles');
        add_editor_style('assets/css/style.css');
    }
}
add_action('after_setup_theme', 'revayat_theme_setup');

/** Editable native widget area shared by public category and multimedia archives. */
function revayat_register_archive_sidebar() {
    register_sidebar( array(
        'name' => __( 'سایدبار آرشیوهای عمومی', 'revayatiran' ),
        'id' => 'public-archive-sidebar',
        'description' => __( 'ابزارک‌های روایت روز، رصد اخبار و رسانه‌نگار؛ در موبایل زیر مطالب نمایش داده می‌شوند.', 'revayatiran' ),
        'before_widget' => '<section id="%1$s" class="rv-archive-widget %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h2 class="rv-archive-widget__title">',
        'after_title' => '</h2>',
    ) );
}
add_action( 'widgets_init', 'revayat_register_archive_sidebar' );
