<?php
/**
 * مدیریت و صف‌بندی استایل‌ها، فونت‌ها و اسکریپت‌ها (Asset Pipeline)
 *
 * @package RevayatIran
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/css-bundles.php';

if (!function_exists('revayat_enqueue_assets')) {
    /**
     * ثبت و بارگذاری دارایی‌های فرانت‌اند
     */
    function revayat_enqueue_assets() {
        // تابع کمکی برای محاسبه نسخه فایل جهت Cache Busting دقیق
        $get_asset_version = function ($relative_path) {
            $full_path = REVAYAT_THEME_DIR . $relative_path;
            return file_exists($full_path) ? filemtime($full_path) : REVAYAT_THEME_VERSION;
        };

        // Generated subsets retain existing Phosphor classes and glyph metrics.
        $icons = require REVAYAT_THEME_DIR . '/assets/icons/dist/manifest.php';
        wp_enqueue_style('revayat-phosphor', REVAYAT_THEME_URI . $icons['file'], [], $icons['version']);
        foreach (['regular', 'bold', 'fill'] as $weight) {
            wp_register_style('revayat-phosphor-' . $weight, false, ['revayat-phosphor'], $icons['version']);
        }

        // Only carousel pages need the locally hosted Swiper bundle.
        $needs_slider = is_front_page() || is_home() || is_post_type_archive('situation_room');
        // ۲. اسلایدر Swiper (استایل)
        if ($needs_slider) {
            wp_enqueue_style(
                'swiper-bundle',
                REVAYAT_THEME_URI . '/assets/vendor/swiper-bundle.min.css',
                [],
                $get_asset_version('/assets/vendor/swiper-bundle.min.css')
            );
        }

        // Prebuilt, content-versioned CSS: shared foundation plus this view only.
        revayat_enqueue_css_bundle('core', 'revayat-main-style');
        revayat_enqueue_css_bundle(revayat_css_view(), 'revayat-main-custom', ['revayat-main-style']);

        // Keep public handles compatible with Customizer and extension dependencies.
        wp_register_style('revayat-theme-root', false, ['revayat-main-custom'], REVAYAT_THEME_VERSION);
        wp_enqueue_style('revayat-theme-root');

        if (is_singular(['special_dossier', 'post', 'analyst_post', 'multimedia', 'media_observatory', 'situation_room', 'person'])) {
            wp_enqueue_script('revayat-single-engagement', REVAYAT_THEME_URI . '/assets/js/single-engagement.js', [], $get_asset_version('/assets/js/single-engagement.js'), ['strategy' => 'defer', 'in_footer' => true]);
            if (comments_open() && get_option('thread_comments')) { wp_enqueue_script('comment-reply'); }
        }
        if (is_page_template('page-templates/template-dashboard.php')) {
            wp_enqueue_script('revayat-member-portal', REVAYAT_THEME_URI . '/assets/js/member-portal.js', [], $get_asset_version('/assets/js/member-portal.js'), ['strategy' => 'defer', 'in_footer' => true]);
            wp_localize_script('revayat-member-portal', 'revayatMember', ['endpoint' => admin_url('admin-ajax.php')]);
        }
        $analysts_experience_pages = is_singular(['analyst_post', 'person']) || is_post_type_archive('analyst_post') || is_tax('analyst_field') || is_page_template('page-templates/template-dashboard.php');
        if ($analysts_experience_pages) {
            wp_enqueue_script('revayat-analysts-experience', REVAYAT_THEME_URI . '/assets/js/analysts-experience.js', [], $get_asset_version('/assets/js/analysts-experience.js'), ['strategy' => 'defer', 'in_footer' => true]);
        }

        // ۵. اسلایدر Swiper (اسکریپت) با لود در فوتر و defer
        if ($needs_slider) {
            wp_enqueue_script(
                'swiper-bundle',
                REVAYAT_THEME_URI . '/assets/vendor/swiper-bundle.min.js',
                [],
                $get_asset_version('/assets/vendor/swiper-bundle.min.js'),
                [
                    'strategy'  => 'defer',
                    'in_footer' => true,
                ]
            );

        }

        // Home sections and carousel initialization are not needed on ordinary pages.
        if (is_front_page() || is_home()) {
            wp_enqueue_script('revayat-home-experience', REVAYAT_THEME_URI . '/assets/js/home-experience.js', [], $get_asset_version('/assets/js/home-experience.js'), ['strategy' => 'defer', 'in_footer' => true]);
        }
        if ($needs_slider) {
            wp_enqueue_script('revayat-carousel-experience', REVAYAT_THEME_URI . '/assets/js/carousel-experience.js', ['swiper-bundle', 'revayat-main-script'], $get_asset_version('/assets/js/carousel-experience.js'), ['strategy' => 'defer', 'in_footer' => true]);
        }

        // ۶. اسکریپت تعاملی اصلی پوسته (Vanilla JS)
        if (!is_user_logged_in()) {
            revayat_enqueue_css_bundle('auth-dialog', 'revayat-auth-dialog', ['revayat-main-custom']);
            wp_enqueue_script('revayat-auth-dialog', REVAYAT_THEME_URI . '/assets/js/auth-dialog.js', [], $get_asset_version('/assets/js/auth-dialog.js'), true);
            wp_localize_script('revayat-auth-dialog', 'revayatAuth', ['url' => home_url('/auth/'), 'endpoint' => admin_url('admin-ajax.php')]);
        }
        $main_js = '/assets/js/main.js';
        $main_dependencies = [];
        wp_enqueue_script(
            'revayat-main-script',
            REVAYAT_THEME_URI . $main_js,
            $main_dependencies,
            $get_asset_version($main_js),
            [
                'strategy'  => 'defer',
                'in_footer' => true,
            ]
        );

    }
}
add_action('wp_enqueue_scripts', 'revayat_enqueue_assets');

if (!function_exists('revayat_preload_primary_font')) {
    /** شروع زودهنگام دریافت فونت اصلی برای کاهش جابه‌جایی فونت در رندر نخست. */
    function revayat_preload_primary_font() {
        $font_path = '/assets/fonts/IRANYekanX(Pro)/Variable Font/webfont/IRANYekanXVF.woff2';
        if (!file_exists(REVAYAT_THEME_DIR . $font_path)) {
            return;
        }
        printf(
            '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
            esc_url(REVAYAT_THEME_URI . $font_path)
        );
    }
}
add_action('wp_head', 'revayat_preload_primary_font', 1);

if (!function_exists('revayat_script_loader_tag')) {
    /**
     * فال‌بک سازگاری: افزودن صریح صفت defer به اسکریپت‌های پوسته در نسخه‌های قدیمی‌تر وردپرس
     */
    function revayat_script_loader_tag($tag, $handle, $src) {
        $defer_handles = ['swiper-bundle', 'revayat-main-script', 'revayat-analysts-experience', 'revayat-home-experience', 'revayat-carousel-experience', 'revayat-single-engagement', 'revayat-member-portal'];
        if (in_array($handle, $defer_handles, true) && strpos($tag, 'defer') === false) {
            $tag = str_replace(' src=', ' defer src=', $tag);
        }
        return $tag;
    }
}
add_filter('script_loader_tag', 'revayat_script_loader_tag', 10, 3);
