<?php
/**
 * لایه انتزاعی رسانه و تصاویر (Media Abstraction Layer)
 *
 * مسئولیت‌ها:
 * ۱. ثبت اندازه‌های استاندارد تصاویر پوسته (Custom Image Sizes).
 * ۲. ارائه توابع هلپر متمرکز برای دریافت تصویر شاخص، آواتار و پلیس‌هولدرها.
 * ۳. جداسازی کامل Template Parts از مسیرهای فیزیکی فایل‌ها و منطق Fallback (طبق ADR-003).
 *
 * @package RevayatIran
 * @subpackage Inc
 * @version 2.0.0 (Pass 2B)
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('revayat_register_image_sizes')) {
    /**
     * ثبت اندازه‌های سفارشی تصاویر پوسته
     */
    function revayat_register_image_sizes(): void {
        add_image_size('revayat-hero-large', 1200, 600, true);
        add_image_size('revayat-card-thumb', 600, 400, true);
        add_image_size('revayat-story-thumb', 400, 600, true);
        add_image_size('revayat-avatar', 160, 160, true);
        foreach ([320, 480, 640, 960, 1280, 1600] as $width) {
            add_image_size('revayat-web-' . $width, $width, 0, false);
        }
    }
}
add_action('after_setup_theme', 'revayat_register_image_sizes');

if (!function_exists('revayat_get_placeholder_url')) {
    /**
     * دریافت نشانی پلیس‌هولدر استاندارد بر اساس نوع رسانه
     *
     * @param string $type نوع رسانه: 'news' | 'avatar' | 'media'
     * @return string نشانی معتبر فایل پلیس‌هولدر
     */
    function revayat_get_placeholder_url(string $type = 'news'): string {
        $base_url = REVAYAT_THEME_URI . '/assets/images/placeholders/';

        switch ($type) {
            case 'avatar':
            case 'author':
            case 'analyst':
                return esc_url($base_url . 'placeholder-avatar.svg');

            case 'news':
            case 'post':
            case 'hero':
            case 'media':
            default:
                return esc_url($base_url . 'placeholder-news.svg');
        }
    }
}

if (!function_exists('revayat_get_post_image_url')) {
    /**
     * دریافت ایمن نشانی تصویر شاخص پست به همراه فال‌بک متمرکز
     *
     * @param WP_Post|int|null $post آبجکت یا شناسه پست
     * @param string $size اندازه تصویر رجیسترشده در وردپرس
     * @param string $fallback_type نوع پلیس‌هولدر در صورت نبود تصویر
     * @return string نشانی تصویر
     */
    function revayat_get_post_image_url($post = null, string $size = 'revayat-card-thumb', string $fallback_type = 'news'): string {
        $post_obj = get_post($post);

        if ($post_obj instanceof WP_Post && has_post_thumbnail($post_obj)) {
            $thumb_url = get_the_post_thumbnail_url($post_obj, $size);
            if (!empty($thumb_url)) {
                return esc_url($thumb_url);
            }
        }

        return revayat_get_placeholder_url($fallback_type);
    }
}

if (!function_exists('revayat_get_avatar_url')) {
    /**
     * دریافت ایمن نشانی آواتار کاربر با فال‌بک مدرن
     *
     * @param mixed $user_or_id شناسه کاربر، ایمیل یا آبجکت کاربر
     * @param int $size ابعاد آواتار
     * @return string نشانی آواتار
     */
    function revayat_get_avatar_url($user_or_id, int $size = 96): string {
        $avatar_url = get_avatar_url($user_or_id, ['size' => $size]);

        if (!empty($avatar_url)) {
            return esc_url($avatar_url);
        }

        return revayat_get_placeholder_url('avatar');
    }
}

if (!function_exists('revayat_resolve_image_url')) {
    /**
     * بررسی و اعتبارسنجی نشانی تصویر ورودی یا بازگرداندن فال‌بک متمرکز
     *
     * @param string|null $url نشانی احتمالی تصویر
     * @param string $fallback_type نوع فال‌بک
     * @return string نشانی تمیز و معتبر
     */
    function revayat_resolve_image_url(?string $url, string $fallback_type = 'news'): string {
        if (!empty($url) && is_string($url)) {
            return esc_url($url);
        }

        return revayat_get_placeholder_url($fallback_type);
    }
}

if (!function_exists('revayat_get_situation_fallback_media')) {
    /**
     * دریافت کاور موضوعی اتاق وضعیت برای بولتن‌های فاقد تصویر شاخص.
     *
     * تصویر واقعی مطلب همیشه در Template اولویت دارد و این تابع فقط fallback است.
     *
     * @param array $item داده نرمال‌شده بولتن.
     * @return array{url:string,alt:string,key:string}
     */
    function revayat_get_situation_fallback_media(array $item): array {
        $meta   = isset($item['meta']) && is_array($item['meta']) ? $item['meta'] : [];
        $title  = (string) ($item['title'] ?? '');
        $type   = (string) ($meta['bulletin_type'] ?? ($item['category'] ?? ''));
        $search = $title . ' ' . $type;
        $key    = 'signals-intelligence';

        $matches = static function (string $haystack, array $needles): bool {
            foreach ($needles as $needle) {
				if (false !== strpos($haystack, $needle)) {
                    return true;
                }
            }
            return false;
        };

        if ($matches($search, ['دریا', 'دریایی', 'شناور', 'خلیج', 'کشتیرانی'])) {
            $key = 'maritime-surveillance';
        } elseif ($matches($search, ['ماهواره', 'رصد', 'پیرامونی', 'سوخت‌رسانی'])) {
            $key = 'satellite-observation';
        } elseif ($matches($search, ['پدافند', 'رادار', 'هوایی', 'سامانه'])) {
            $key = 'air-defense-radar';
        }

        return [
            'url' => REVAYAT_THEME_URI . '/assets/images/situation-room/' . $key . '.webp',
            'alt' => sprintf(__('کاور موضوعی بولتن «%s»', 'revayatiran'), $title),
            'key' => $key,
        ];
    }
}

/** Responsive candidates from an existing attachment ID; no URL-to-ID query. */
function revayat_responsive_image_attributes(int $attachment_id, string $sizes, string $size = 'large'): string {
    if (!$attachment_id) { return ''; }
    $srcset = wp_get_attachment_image_srcset($attachment_id, $size);
    return $srcset ? 'srcset="' . esc_attr($srcset) . '" sizes="' . esc_attr($sizes) . '"' : '';
}
