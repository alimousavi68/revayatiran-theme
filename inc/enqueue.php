<?php
/**
 * مدیریت و صف‌بندی استایل‌ها، فونت‌ها و اسکریپت‌ها (Asset Pipeline)
 *
 * @package RevayatIran
 */

if (!defined('ABSPATH')) {
    exit;
}

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

        // ۱. آیکون‌های محلی Phosphor (Regular, Bold, Duotone, Fill, Light, Thin)
        $phosphor_weights = ['regular', 'bold', 'duotone', 'fill', 'light', 'thin'];
        foreach ($phosphor_weights as $weight) {
            $rel_css = "/assets/vendor/phosphor/{$weight}/style.css";
            if (file_exists(REVAYAT_THEME_DIR . $rel_css)) {
                wp_enqueue_style(
                    "revayat-phosphor-{$weight}",
                    REVAYAT_THEME_URI . $rel_css,
                    [],
                    $get_asset_version($rel_css)
                );
            }
        }

        // Only carousel pages need Swiper; archive navigation must not wait for its CDN.
        $needs_slider = is_front_page() || is_home() || is_post_type_archive('situation_room');
        // ۲. اسلایدر Swiper (استایل)
        if ($needs_slider) {
            wp_enqueue_style(
                'swiper-bundle',
                'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
                [],
                '11.0.0'
            );
        }

        // ۳. استایل کامپایل‌شده اصلی پوسته (شامل متغیرها، فونت ایران‌یکان و Tailwind)
        $main_css = '/assets/css/style.css';
        wp_enqueue_style(
            'revayat-main-style',
            REVAYAT_THEME_URI . $main_css,
            [],
            $get_asset_version($main_css)
        );

        // ۳.۱ استایل ماژولار و سفارشی فرانت‌اند (شامل تمام ماژول‌های کامپوننت‌ها)
        $custom_css = '/assets/css/main-custom.css';
        if (file_exists(REVAYAT_THEME_DIR . $custom_css)) {
            wp_enqueue_style(
                'revayat-main-custom',
                REVAYAT_THEME_URI . $custom_css,
                ['revayat-main-style'],
                $get_asset_version($custom_css)
            );
        }

        // Shared editorial body for dossier and its selected articles.
        if (is_singular(['special_dossier', 'post', 'analyst_post', 'multimedia', 'media_observatory', 'situation_room', 'person'])) {
            $story_css = '/assets/css/modules/dossier-story.css';
            wp_enqueue_style('revayat-dossier-story', REVAYAT_THEME_URI . $story_css, ['revayat-main-custom'], $get_asset_version($story_css));
            $engagement_js = '/assets/js/single-engagement.js';
            wp_enqueue_script('revayat-single-engagement', REVAYAT_THEME_URI . $engagement_js, [], $get_asset_version($engagement_js), true);
            if (comments_open() && get_option('thread_comments')) { wp_enqueue_script('comment-reply'); }
        }

        // ۴. استایل سراسری متادیتای ریشه تم
        wp_enqueue_style(
            'revayat-theme-root',
            get_stylesheet_uri(),
            ['revayat-main-style', 'revayat-main-custom'],
            REVAYAT_THEME_VERSION
        );

        // ۴.۱ بارگذاری مشروط استایل ماژولار پرونده‌های ویژه (ADR-001 & Asset Mapping)
        if (is_front_page() || is_home() || is_singular('special_dossier') || is_post_type_archive('special_dossier')) {
            $dossier_css = '/assets/css/modules/special-dossiers.css';
            if (file_exists(REVAYAT_THEME_DIR . $dossier_css)) {
                wp_enqueue_style(
                    'revayat-module-special-dossiers',
                    REVAYAT_THEME_URI . $dossier_css,
                    ['revayat-main-custom'],
                    $get_asset_version($dossier_css)
                );
            }
        }

        // ۴.۲ بارگذاری مشروط استایل ماژولار اتاق وضعیت (ADR-001 & Asset Mapping)
        if (is_singular('situation_room') || is_post_type_archive('situation_room') || is_tax('security_level')) {
            $sr_css = '/assets/css/modules/situation-room.css';
            if (file_exists(REVAYAT_THEME_DIR . $sr_css)) {
                wp_enqueue_style(
                    'revayat-module-situation-room',
                    REVAYAT_THEME_URI . $sr_css,
                    ['revayat-main-style'],
                    $get_asset_version($sr_css)
                );
            }
        }

        // ۴.۳ بارگذاری مشروط استایل ماژولار دیده‌بان رسانه (ADR-001 & Asset Mapping)
        if (is_singular('media_observatory') || is_post_type_archive('media_observatory') || is_tax('observatory_badge') || is_front_page() || is_home()) {
            $obs_css = '/assets/css/modules/media-observatory.css';
            if (file_exists(REVAYAT_THEME_DIR . $obs_css)) {
                wp_enqueue_style(
                    'revayat-module-media-observatory',
                    REVAYAT_THEME_URI . $obs_css,
                    ['revayat-main-style'],
                    $get_asset_version($obs_css)
                );
            }
        }

        // ۴.۴ بارگذاری مشروط استایل ماژولار شبکه تحلیل‌گران (ADR-001 & Asset Mapping)
        if (is_singular('analyst_post') || is_post_type_archive('analyst_post') || is_tax('analyst_field') || is_front_page() || is_home()) {
            $an_css = '/assets/css/modules/analysts-network.css';
            if (file_exists(REVAYAT_THEME_DIR . $an_css)) {
                wp_enqueue_style(
                    'revayat-module-analysts-network',
                    REVAYAT_THEME_URI . $an_css,
                    ['revayat-main-style'],
                    $get_asset_version($an_css)
                );
            }
            $an_mob_css = '/assets/css/modules/analysts-mobile.css';
            if (file_exists(REVAYAT_THEME_DIR . $an_mob_css)) {
                wp_enqueue_style(
                    'revayat-module-analysts-mobile',
                    REVAYAT_THEME_URI . $an_mob_css,
                    ['revayat-module-analysts-network'],
                    $get_asset_version($an_mob_css)
                );
            }
        }

        // ۴.۵ برش‌های عمودی چندرسانه‌ای و پروفایل اشخاص
        if (is_singular(['multimedia', 'person']) || is_post_type_archive(['multimedia', 'person']) || is_tax('media_format')) {
            $vertical_css = '/assets/css/modules/vertical-content.css';
            if (file_exists(REVAYAT_THEME_DIR . $vertical_css)) {
                wp_enqueue_style(
                    'revayat-module-vertical-content',
                    REVAYAT_THEME_URI . $vertical_css,
                    ['revayat-main-style'],
                    $get_asset_version($vertical_css)
                );
            }
        }

        // ۴.۵.۱ استاندارد مشترک آرشیوهای راهبردی
        if (is_post_type_archive(['special_dossier', 'analyst_post', 'situation_room']) || is_tax(['dossier_topic', 'analyst_field', 'security_level'])) {
            $archive_css = '/assets/css/modules/archive-modernization.css';
            if (file_exists(REVAYAT_THEME_DIR . $archive_css)) {
                wp_enqueue_style('revayat-module-archive-modernization', REVAYAT_THEME_URI . $archive_css, ['revayat-main-style'], $get_asset_version($archive_css));
            }
        }

        // Public archives: native loops, media metadata and touch pagination.
        if (is_archive() && !is_post_type_archive('analyst_post') && !is_tax(['analyst_field', 'person_author'])) {
            $public_archive_css = '/assets/css/modules/public-archives.css';
            wp_enqueue_style('revayat-public-archives', REVAYAT_THEME_URI . $public_archive_css, ['revayat-main-custom'], $get_asset_version($public_archive_css));
        }

        // ۴.۶ درگاه احراز هویت و داشبورد فرانت‌اند
        if (is_page_template('page-templates/template-auth.php') || is_page_template('page-templates/template-dashboard.php')) {
            $portal_css = '/assets/css/modules/user-portal.css';
            if (file_exists(REVAYAT_THEME_DIR . $portal_css)) {
                wp_enqueue_style('revayat-module-user-portal', REVAYAT_THEME_URI . $portal_css, ['revayat-main-style'], $get_asset_version($portal_css));
				$otp_css = '/assets/css/modules/user-portal-otp.css';
				if (file_exists(REVAYAT_THEME_DIR . $otp_css)) {
					wp_enqueue_style('revayat-module-user-portal-otp', REVAYAT_THEME_URI . $otp_css, ['revayat-module-user-portal'], $get_asset_version($otp_css));
				}
				$profile_css = '/assets/css/modules/user-portal-profile.css';
				if (file_exists(REVAYAT_THEME_DIR . $profile_css)) {
					wp_enqueue_style('revayat-module-user-portal-profile', REVAYAT_THEME_URI . $profile_css, ['revayat-module-user-portal'], $get_asset_version($profile_css));
				}
				$workflow_css = '/assets/css/modules/user-portal-workflow.css';
				if (file_exists(REVAYAT_THEME_DIR . $workflow_css)) {
					wp_enqueue_style('revayat-module-user-portal-workflow', REVAYAT_THEME_URI . $workflow_css, ['revayat-module-user-portal'], $get_asset_version($workflow_css));
				}
            }
        }

        // خانواده صفحات بازطراحی‌شده شبکه تحلیلگران؛ بعد از استایل‌های عمومی و پرتال بارگذاری می‌شود.
        $analysts_experience_pages = is_singular(['analyst_post', 'person']) || is_post_type_archive('analyst_post') || is_tax('analyst_field') || is_page_template('page-templates/template-dashboard.php');
        if ($analysts_experience_pages) {
            $experience_css = '/assets/css/modules/analysts-experience.css';
            if (file_exists(REVAYAT_THEME_DIR . $experience_css)) {
                wp_enqueue_style(
                    'revayat-module-analysts-experience',
                    REVAYAT_THEME_URI . $experience_css,
                    ['revayat-theme-root'],
                    $get_asset_version($experience_css)
                );
            }
            $refinement_css = '/assets/css/modules/analysts-refinement.css';
            if (file_exists(REVAYAT_THEME_DIR . $refinement_css)) {
                wp_enqueue_style(
                    'revayat-module-analysts-refinement',
                    REVAYAT_THEME_URI . $refinement_css,
                    ['revayat-module-analysts-experience'],
                    $get_asset_version($refinement_css)
                );
            }
            $experience_js = '/assets/js/analysts-experience.js';
            if (file_exists(REVAYAT_THEME_DIR . $experience_js)) {
                wp_enqueue_script(
                    'revayat-analysts-experience',
                    REVAYAT_THEME_URI . $experience_js,
                    [],
                    $get_asset_version($experience_js),
                    [
                        'strategy'  => 'defer',
                        'in_footer' => true,
                    ]
                );
            }
        }

        // ۵. اسلایدر Swiper (اسکریپت) با لود در فوتر و defer
        if ($needs_slider) {
            wp_enqueue_script(
                'swiper-bundle',
                'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
                [],
                '11.0.0',
                [
                    'strategy'  => 'defer',
                    'in_footer' => true,
                ]
            );

        }

        // ۶. اسکریپت تعاملی اصلی پوسته (Vanilla JS)
        $main_js = '/assets/js/main.js';
        wp_enqueue_script(
            'revayat-main-script',
            REVAYAT_THEME_URI . $main_js,
            [],
            $get_asset_version($main_js),
            [
                'strategy'  => 'defer',
                'in_footer' => true,
            ]
        );

        // ۷. ارسال امن داده‌ها و متغیرهای رانتایم به جاوااسکریپت
        wp_localize_script('revayat-main-script', 'revayatData', [
            'ajaxUrl'     => admin_url('admin-ajax.php'),
            'restUrl'     => esc_url_raw(rest_url('revayat/v1/')),
            'nonce'       => wp_create_nonce('wp_rest'),
            'themeUri'    => REVAYAT_THEME_URI,
            'isLoggedIn'  => is_user_logged_in(),
            'currentUserId' => get_current_user_id(),
        ]);
    }
}
add_action('wp_enqueue_scripts', 'revayat_enqueue_assets');

if (!function_exists('revayat_script_loader_tag')) {
    /**
     * فال‌بک سازگاری: افزودن صریح صفت defer به اسکریپت‌های پوسته در نسخه‌های قدیمی‌تر وردپرس
     */
    function revayat_script_loader_tag($tag, $handle, $src) {
        $defer_handles = ['swiper-bundle', 'revayat-main-script', 'revayat-analysts-experience'];
        if (in_array($handle, $defer_handles, true) && strpos($tag, 'defer') === false) {
            $tag = str_replace(' src=', ' defer src=', $tag);
        }
        return $tag;
    }
}
add_filter('script_loader_tag', 'revayat_script_loader_tag', 10, 3);
