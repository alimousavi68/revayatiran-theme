<?php
/**
 * توابع کمکی نمایش و رندرینگ تمپلیت‌ها (Template Tags)
 *
 * @package RevayatIran
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('revayat_posted_on')) {
    /**
     * رندر تاریخ انتشار به فرمت انسانی یا استاندارد
     */
    function revayat_posted_on($format = '') {
        $time_string = sprintf(
            '<time class="entry-date published updated" datetime="%1$s">%2$s</time>',
            esc_attr(get_the_date(DATE_W3C)),
            esc_html(get_the_date($format))
        );
        echo $time_string; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}

if (!function_exists('revayat_reading_time')) {
    /**
     * محاسبه تخمینی زمان مطالعه محتوا بر اساس کلمات
     */
    function revayat_reading_time($post_id = null) {
        $post = get_post($post_id);
        if (!$post) {
            return '۳ دقیقه';
        }

        $word_count = mb_strlen(strip_tags($post->post_content), 'UTF-8') / 5; // تخمین واژگان فارسی
        $reading_time = ceil($word_count / 200); // میانگین ۲۰۰ کلمه در دقیقه
        $reading_time = max(1, $reading_time);

        return sprintf(esc_html__('%s دقیقه مطالعه', 'revayatiran'), number_format_i18n($reading_time));
    }
}

if (!function_exists('revayat_get_primary_category')) {
    /**
     * دریافت دسته‌بندی اولیه نوشته
     */
    function revayat_get_primary_category($post_id = null) {
        $categories = get_the_category($post_id);
        if (!empty($categories) && !is_wp_error($categories)) {
            return $categories[0];
        }
        return null;
    }
}
