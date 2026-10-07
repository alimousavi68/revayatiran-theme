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

/** Decide whether multimedia introduction is already present in authored content. */
function revayat_media_intro_visibility(string $content, string $cover, string $excerpt): array {
    $normalize = static function ($text) {
        return trim(preg_replace('/\s+/u', ' ', html_entity_decode(wp_strip_all_tags($text), ENT_QUOTES, 'UTF-8')));
    };
    $photo_key = static function ($url) {
        $path = wp_parse_url(html_entity_decode($url, ENT_QUOTES, 'UTF-8'), PHP_URL_PATH) ?: '';
        $host = strtolower(wp_parse_url($url, PHP_URL_HOST) ?: wp_parse_url(home_url(), PHP_URL_HOST));
        return $host . preg_replace('/-\d+x\d+(?=\.[a-z0-9]+$)/i', '', $path);
    };
    $show_cover = (bool) $cover;
    if ($cover && preg_match_all('/<img\b[^>]*\bsrc\s*=\s*([\'\"])(.*?)\1/is', $content, $images)) {
        foreach ($images[2] as $src) {
            if ($photo_key($src) === $photo_key($cover)) { $show_cover = false; break; }
        }
    }
    $show_excerpt = (bool) $normalize($excerpt);
    if (preg_match('/<p\b[^>]*>(.*?)<\/p>/is', $content, $paragraph)) {
        if ($normalize($paragraph[1]) === $normalize($excerpt)) { $show_excerpt = false; }
    }
    return ['cover' => $show_cover, 'excerpt' => $show_excerpt];
}

/** Keep public reading-page forms compact while preserving native validation/cookies. */
add_filter('comment_form_default_fields', function ($fields) {
    if (is_singular(['post', 'analyst_post', 'multimedia', 'special_dossier'])) { unset($fields['url'], $fields['cookies']); }
    return $fields;
});
