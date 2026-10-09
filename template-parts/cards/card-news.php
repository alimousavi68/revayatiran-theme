<?php
/**
 * کارت استاندارد رصد اخبار (Card News)
 *
 * منبع اصلی حقیقت (Single Source of Truth) برای رندر کارت‌های خبری.
 * پشتیبانی کامل از هر دو حالت:
 *   ۱. ارسال آبجکت WP_Post در Loop استاندارد
 *   ۲. ارسال آرایه ساختاریافته در لایه قرارداد داده (Data Contract)
 *
 * طبق ADR-003:
 * اتصال کامل به Media Abstraction Layer و حذف فال‌بک لوگوی سایت.
 *
 * پارامترهای $args پشتیبانی‌شده:
 *   - 'post'          (WP_Post) — آبجکت پست (اختیاری)
 *   - 'title'         (string)  — عنوان خبر
 *   - 'url'           (string)  — پیوند خبر
 *   - 'thumbnail_url' (string)  — نشانی تصویر
 *   - 'category'      (string)  — برچسب دسته‌بندی
 *   - 'source_label'  (string)  — برچسب خبرگزاری یا منبع
 *
 * @package RevayatIran
 * @subpackage Template_Parts/Cards
 * @version 2.1.0 (Pass 2B — Media Abstraction Integration)
 */

if (!defined('ABSPATH')) {
    exit;
}

$card_post = $args['post'] ?? null;

if ($card_post instanceof WP_Post) {
    $card_title     = get_the_title($card_post);
    $card_url       = get_the_permalink($card_post);
    $card_thumbnail = revayat_get_post_image_url($card_post, 'revayat-card-thumb', 'news');
    $card_category  = $args['category'] ?? '';
    $card_source    = $args['source_label'] ?? '';

    if (empty($card_category)) {
        $primary_cat   = revayat_get_primary_category($card_post->ID);
        $card_category = $primary_cat ? $primary_cat->name : '';
    }
} else {
    $card_title     = $args['title'] ?? '';
    $card_url       = $args['url'] ?? home_url('/');
    $card_thumbnail = revayat_resolve_image_url($args['thumbnail_url'] ?? null, 'news');
    $card_category  = $args['category'] ?? '';
    $card_source    = $args['source_label'] ?? '';
}

if (empty($card_title)) {
    return;
}
?>
<article class="news-card group">
    <div class="news-card__media overflow-hidden">
        <img src="<?php echo esc_url($card_thumbnail); ?>" <?php echo revayat_responsive_image_attributes((int) ($args['thumbnail_id'] ?? ($card_post instanceof WP_Post ? get_post_thumbnail_id($card_post) : 0)), '(max-width: 767px) calc(100vw - 32px), 400px'); ?>
             alt="<?php echo esc_attr($card_title); ?>"
             class="news-card__img transition-transform duration-500 group-hover:scale-105"
             loading="lazy">
    </div>
    <div class="news-card__overlay">
        <div class="news-card__meta">
            <?php if (!empty($card_category)) : ?>
                <span class="chip"><?php echo esc_html($card_category); ?></span>
            <?php endif; ?>
            <?php if (!empty($card_source)) : ?>
                <span class="source-badge"><?php echo esc_html($card_source); ?></span>
            <?php endif; ?>
        </div>
        <h3 class="news-card__title">
            <a href="<?php echo esc_url($card_url); ?>" class="hover:text-primary transition-colors">
                <?php echo esc_html($card_title); ?>
            </a>
        </h3>
    </div>
</article>
